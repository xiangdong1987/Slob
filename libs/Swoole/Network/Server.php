<?php
namespace Swoole\Network;
use Swoole;

/**
 * Class Server
 * @package Swoole\Network
 */
class Server extends Swoole\Server implements Swoole\Server\Driver
{
    static $sw_mode = SWOOLE_PROCESS;
    static $pidFile;
    /**
     * @var \swoole_server
     */
    protected $sw;
    protected $pid_file;

    /**
     * 设置PID文件
     * @param $pidFile
     */
    static function setPidFile($pidFile)
    {
        self::$pidFile = $pidFile;
    }

    /**
     * 显示命令行指令
     */
    static function start($startFunction)
    {
        if (empty(self::$pidFile))
        {
            throw new \Exception("require pidFile.");
        }
        $pid_file = self::$pidFile;
        if (is_file($pid_file))
        {
            $server_pid = file_get_contents($pid_file);
        }
        else
        {
            $server_pid = 0;
        }
        global $argv;
        if (empty($argv[1]))
        {
            goto usage;
        }
        elseif ($argv[1] == 'reload')
        {
            if (empty($server_pid))
            {
                exit("Server is not running");
            }
            posix_kill($server_pid, SIGUSR1);
            exit;
        }
        elseif ($argv[1] == 'stop')
        {
            if (empty($server_pid))
            {
                exit("Server is not running\n");
            }
            posix_kill($server_pid, SIGTERM);
            exit;
        }
        elseif ($argv[1] == 'start')
        {
            //已存在ServerPID，并且进程存在
            if (!empty($server_pid) and posix_kill($server_pid, 0))
            {
                exit("Server is already running.\n");
            }
        }
        else
        {
            usage:
            exit("Usage: php {$argv[0]} start|stop|reload\n");
        }
        $startFunction();
    }

    /**
     * 自动推断扩展支持
     * 默认使用swoole扩展,其次是libevent,最后是select(支持windows)
     * @param      $host
     * @param      $port
     * @param bool $ssl
     * @return Server
     */
    static function autoCreate($host, $port, $ssl = false)
    {
        if (class_exists('\\swoole_server', false))
        {
            return new self($host, $port, $ssl);
        }
        elseif (function_exists('event_base_new'))
        {
            return new EventTCP($host, $port, $ssl);
        }
        else
        {
            return new SelectTCP($host, $port, $ssl);
        }
    }

    function __construct($host, $port, $ssl = false)
    {
        $flag = $ssl ? (SWOOLE_SOCK_TCP | SWOOLE_SSL) : SWOOLE_SOCK_TCP;
        $this->sw = new \swoole_server($host, $port, self::$sw_mode, $flag);
        $this->host = $host;
        $this->port = $port;
        Swoole\Error::$stop = false;
        Swoole\JS::$return = true;
        $this->runtimeSetting = array(
            //'reactor_num' => 4,      //reactor thread num
            //'worker_num' => 4,       //worker process num
            'backlog' => 128,        //listen backlog
            //'open_cpu_affinity' => 1,
            //'open_tcp_nodelay' => 1,
            //'log_file' => '/tmp/swoole.log',
        );
    }

    function daemonize()
    {
        $this->runtimeSetting['daemonize'] = 1;
    }

    function connection_info($fd)
    {
        return $this->sw->connection_info($fd);
    }

    function onMasterStart($serv)
    {
        global $argv;
        Swoole\Console::setProcessName('php ' . $argv[0] . ': master -host=' . $this->host . ' -port=' . $this->port);
        
        if (!empty($this->runtimeSetting['pid_file']))
        {
            file_put_contents(self::$pidFile, $serv->master_pid);
        }
    }


    function onMasterStop($serv)
    {
        if (!empty($this->runtimeSetting['pid_file']))
        {
            unlink(self::$pidFile);
        }
    }

    function onManagerStop()
    {

    }

    function onWorkerStart($serv, $worker_id)
    {
        global $argv;
        if ($worker_id >= $serv->setting['worker_num'])
        {
            Swoole\Console::setProcessName('php ' . $argv[0] . ': task');
        }
        else
        {
            Swoole\Console::setProcessName('php ' . $argv[0] . ': worker');
        }
        if (method_exists($this->protocol, 'onStart'))
        {
            $this->protocol->onStart($serv, $worker_id);
        }
    }

    function run($setting = array())
    {
        $this->runtimeSetting = array_merge($this->runtimeSetting, $setting);
        if (self::$pidFile)
        {
            $this->runtimeSetting['pid_file'] = self::$pidFile;
        }
        $this->sw->set($this->runtimeSetting);
        $version = explode('.', SWOOLE_VERSION);
        //1.7.0
        if ($version[1] >= 7)
        {
            $this->sw->on('ManagerStart', function ($serv)
            {
                global $argv;
                Swoole\Console::setProcessName('php ' . $argv[0] . ': manager');
            });
        }
        $this->sw->on('Start', array($this, 'onMasterStart'));
        $this->sw->on('Shutdown', array($this, 'onMasterStop'));
        $this->sw->on('ManagerStop', array($this, 'onManagerStop'));
        $this->sw->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->sw->on('Connect', array($this->protocol, 'onConnect'));
        $this->sw->on('Receive', array($this->protocol, 'onReceive'));
        $this->sw->on('Close', array($this->protocol, 'onClose'));
        $this->sw->on('WorkerStop', array($this->protocol, 'onShutdown'));
        if (is_callable(array($this->protocol, 'onTimer')))
        {
            $this->sw->on('Timer', array($this->protocol, 'onTimer'));
        }
        if (is_callable(array($this->protocol, 'onTask')))
        {
            $this->sw->on('Task', array($this->protocol, 'onTask'));
            $this->sw->on('Finish', array($this->protocol, 'onFinish'));
        }
        $this->sw->start();
    }

    function shutdown()
    {
        return $this->sw->shutdown();
    }

    function close($client_id)
    {
        return $this->sw->close($client_id);
    }

    function addListener($host, $port, $type)
    {
        return $this->sw->addlistener($host, $port, $type);
    }

    function send($client_id, $data)
    {
        return $this->sw->send($client_id, $data);
    }
}
