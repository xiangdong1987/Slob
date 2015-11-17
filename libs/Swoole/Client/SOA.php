<?php
namespace Swoole\Client;
use Swoole\Protocol\SOAServer;

class SOA
{
    protected $servers = array();
    protected $env = array();

    protected $wait_list = array();
    protected $timeout = 0.5;
    protected $packet_maxlen = 2097152;   //最大不超过2M的数据包

    /**
     * 启用长连接
     * @var bool
     */
    protected $keep_connection = false;

    const OK = 0;
    const TYPE_ASYNC        = 1;
    const TYPE_SYNC         = 2;
    public $re_connect      = true;    //重新connect

    protected static $_instance;

    function __construct()
    {
        if (self::$_instance)
        {
            throw new \Exception("cannot to create two soa client.");
        }
        self::$_instance = $this;
    }

    /**
     * 获取SOA服务实例
     * @return SOA
     */
    static function getInstance()
    {
        if (!self::$_instance)
        {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    static function getRequestId()
    {
        list($us) = explode(' ', microtime());
        return intval(strval($us * 1000 * 1000) . rand(100000, 999999));
    }

    /**
     * 发送请求
     * @param $type
     * @param $send
     * @param SOA_result $retObj
     * @return bool
     */
    protected function request($type, $send, $retObj)
    {
        $socket = new \Swoole\Client\TCP;
        $retObj->socket = $socket;
        $retObj->type = $type;
        $retObj->send = $send;

        $svr = $this->getServer();
        //异步connect
        //TODO 如果连接失败，需要上报机器存活状态
        $ret = $socket->connect($svr['host'], $svr['port'], $this->timeout);
        //使用SOCKET的编号作为ID
        $retObj->id = (int)$socket->get_socket();
        if ($ret === false)
        {
            $retObj->code = SOA_Result::ERR_CONNECT;
            unset($retObj->socket);
            return false;
        }
        //请求串号
        $retObj->requestId = self::getRequestId();
        //发送失败了
        if ($retObj->socket->send(SOAServer::encode($retObj->send, SOAServer::DECODE_PHP, 0, $retObj->requestId)) === false)
        {
            $retObj->code = SOA_Result::ERR_SEND;
            unset($retObj->socket);
            return false;
        }
        //加入wait_list
        if ($type != self::TYPE_ASYNC)
        {
            $this->wait_list[$retObj->id] = $retObj;
        }
        return true;
    }

    /**
     * 设置环境变量
     * @return array
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * 获取环境变量
     * @param array $env
     */
    public function setEnv($env)
    {
        $this->env = $env;
    }

    /**
     * 设置一项环境变量
     * @param $k
     * @param $v
     */
    public function putEnv($k, $v)
    {
        $this->env[$k] = $v;
    }

    /**
     * 完成请求
     * @param $retData
     * @param $retObj
     */
    protected function finish($retData, $retObj)
    {
        //解包失败了
        if ($retData === false)
        {
            $retObj->code = SOA_Result::ERR_UNPACK;
        }
        //调用成功
        elseif ($retData['errno'] === self::OK)
        {
            $retObj->code = self::OK;
            $retObj->data = $retData['data'];
        }
        //服务器返回失败
        else
        {
            $retObj->code = $retData['errno'];
            $retObj->data = null;
        }
        if ($retObj->type != self::TYPE_ASYNC)
        {
            unset($this->wait_list[$retObj->id]);
        }
        if ($retObj->callback)
        {
            call_user_func($retObj->callback, $retObj);
        }
    }

    /**
     * 添加服务器
     * @param array $servers
     */
    function addServers(array $servers)
    {
        if (isset($servers['host']))
        {
            $this->servers[] = $servers;
        }
        else
        {
            $this->servers = array_merge($this->servers, $servers);
        }
    }

    /**
     * 从配置中取出一个服务器配置
     * @return array
     * @throws \Exception
     */
    function getServer()
    {
        if (empty($this->servers))
        {
            throw new \Exception("servers config empty.");
        }
        $_svr = $this->servers[array_rand($this->servers)];
        $svr = array('host' => '', 'port' => 0);
        list($svr['host'], $svr['port']) = explode(':', $_svr, 2);
        return $svr;
    }

    /**
     * RPC调用
     *
     * @param $function
     * @param $params
     * @param $callback
     * @return SOA_Result
     */
    function task($function, $params = array(), $callback = null)
    {
        $retObj = new SOA_Result();
        $send = array('call' => $function, 'params' => $params);
        if (count($this->env) > 0)
        {
            //调用端环境变量
            $send['env'] = $this->env;
        }
        $this->request(self::TYPE_SYNC, $send, $retObj);
        $retObj->callback = $callback;
        return $retObj;
    }

    /**
     * 异步任务
     * @param $function
     * @param $params
     * @return SOA_Result
     */
    function async($function, $params)
    {
        $retObj = new SOA_Result();
        $send = array('call' => $function, 'params' => $params);
        $this->request(self::TYPE_ASYNC, $send, $retObj);
        if ($retObj->socket != null)
        {
            $recv = $retObj->socket->recv();
            if ($recv == false)
            {
                $retObj->code = SOA_Result::ERR_TIMEOUT;
                return $retObj;
            }
            $this->finish(SOAServer::decode($recv), $retObj);
        }
        return $retObj;
    }

    /**
     * 并发请求
     * @param float $timeout
     * @return int
     */
    function wait($timeout = 0.5)
    {
        $st = microtime(true);
        $t_sec = (int)$timeout;
        $t_usec = (int)(($timeout - $t_sec) * 1000 * 1000);
        $buffer = $header = array();
        $success_num = 0;

        while (true)
        {
            $write = $error = $read = array();
            if(empty($this->wait_list))
            {
                break;
            }
            foreach($this->wait_list as $obj)
            {
                if($obj->socket !== null)
                {
                    $read[] = $obj->socket->get_socket();
                }
            }
            if (empty($read))
            {
                break;
            }
            $n = socket_select($read, $write, $error, $t_sec, $t_usec);
            if($n > 0)
            {
                //可读
                foreach($read as $sock)
                {
                    $id = (int)$sock;

                    /**
                     * @var $retObj SOA_Result
                     */
                    $retObj = $this->wait_list[$id];
                    $data = $retObj->socket->recv();
                    //socket被关闭了
                    if (empty($data))
                    {
                        $retObj->code = SOA_Result::ERR_CLOSED;
                        unset($this->wait_list[$id], $retObj->socket);
                        continue;
                    }
                    //一个新的请求，缓存区中没有数据
                    if (!isset($buffer[$id]))
                    {
                        //这里仅使用了length和type，uid,serid未使用
                        $header[$id] = unpack(SOAServer::HEADER_STRUCT, substr($data, 0, SOAServer::HEADER_SIZE));
                        //错误的包头
                        if ($header[$id] === false or $header[$id]['length'] <= 0)
                        {
                            $retObj->code = SOA_Result::ERR_HEADER;
                            unset($this->wait_list[$id]);
                            continue;
                        }
                        //错误的长度值
                        elseif ($header[$id]['length'] > $this->packet_maxlen)
                        {
                            $retObj->code = SOA_Result::ERR_TOOBIG;
                            unset($this->wait_list[$id]);
                            continue;
                        }
                        $buffer[$id] = substr($data, SOAServer::HEADER_SIZE);
                    }
                    else
                    {
                        $buffer[$id] .= $data;
                    }
                    //达到规定的长度
                    if (strlen($buffer[$id]) == $header[$id]['length'])
                    {
                        $retObj->responseId = $header[$id]['serid'];
                        //成功处理
                        $this->finish(SOAServer::decode($buffer[$id], $header[$id]['type']), $retObj);
                        $success_num++;
                    }
                    //继续等待数据
                }
            }
            //发生超时
            if ((microtime(true) - $st) > $timeout)
            {
                foreach($this->wait_list as $obj)
                {
                    //TODO 如果请求超时了，需要上报服务器负载
                    $obj->code = ($obj->socket->connected) ? SOA_Result::ERR_TIMEOUT : SOA_Result::ERR_CONNECT;
                }
                //清空当前列表
                $this->wait_list = array();
                return $success_num;
            }
        }
        //未发生任何超时
        $this->wait_list = array();
        return $success_num;
    }

}

class SOA_Result
{
    public $id;
    public $code = self::ERR_NO_READY;
    public $msg;
    public $data = null;
    public $send;  //要发送的数据
    public $type;

    /**
     * 请求串号
     */
    public $requestId;

    /**
     * 响应串号
     */
    public $responseId;

    /**
     * 回调函数
     * @var mixed
     */
    public $callback;

    /**
     * @var \Swoole\Client\TCP
     */
    public $socket = null;

    const ERR_NO_READY   = 8001; //未就绪
    const ERR_CONNECT    = 8002; //连接服务器失败
    const ERR_TIMEOUT    = 8003; //服务器端超时
    const ERR_SEND       = 8004; //发送失败
    const ERR_SERVER     = 8005; //server返回了错误码
    const ERR_UNPACK     = 8006; //解包失败了

    const ERR_HEADER     = 8007; //错误的协议头
    const ERR_TOOBIG     = 8008; //超过最大允许的长度
    const ERR_CLOSED     = 8009; //连接被关闭

    function getResult($timeout = 0.5)
    {
        if ($this->code == self::ERR_NO_READY)
        {
            $soaclient = SOA::getInstance();
            $soaclient->wait($timeout);
        }
        return $this->data;
    }
}