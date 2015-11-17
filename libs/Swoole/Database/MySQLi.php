<?php
namespace Swoole\Database;
use Swoole;
/**
 * MySQL数据库封装类
 *
 * @package SwooleExtend
 * @author  Tianfeng.Han
 *
 */
class MySQLi extends \mysqli implements Swoole\IDatabase
{
    const DEFAULT_PORT = 3306;

    public $debug = false;
    public $conn = null;
    public $config;
    public $display_error = true;

    function __construct($db_config)
    {
        if (empty($db_config['port']))
        {
            $db_config['port'] = self::DEFAULT_PORT;
        }
        $this->config = $db_config;
    }

    function lastInsertId()
    {
        return $this->insert_id;
    }

    function connect($host = null, $user = null, $password = null, $database = null, $port = null, $socket = null)
    {
        $db_config = & $this->config;
        if (!empty($db_config['persistent']))
        {
            $db_config['host'] = 'p:' . $db_config['host'];
        }
        if (isset($db_config['passwd']))
        {
            $db_config['password'] = $db_config['passwd'];
        }
        if (isset($db_config['dbname']))
        {
            $db_config['name'] = $db_config['dbname'];
        }
        parent::connect(
            $db_config['host'],
            $db_config['user'],
            $db_config['password'],
            $db_config['name'],
            $db_config['port']
        );
        if (mysqli_connect_errno())
        {
            trigger_error("Mysqli connect failed: " . mysqli_connect_error());
            return false;
        }
        if (!empty($db_config['charset']))
        {
            $this->set_charset($db_config['charset']);
        }
        return true;
    }

    /**
     * 过滤特殊字符
     *
     * @param $value
     *
     * @return string
     */
    function quote($value)
    {
        return $this->escape_string($value);
    }

    /**
     * SQL错误信息
     * @param $sql
     * @return string
     */
    protected function errorMessage($sql)
    {
        $msg = $this->error . "<hr />$sql<hr />";
        $msg .= "Server: {$this->config['host']}:{$this->config['port']}. <br/>";
        $msg .= "Message: {$this->error} <br/>";
        $msg .= "Errno: {$this->errno}";
        return $msg;
    }

    /**
     * 执行一个SQL语句
     *
     * @param string $sql 执行的SQL语句
     *
     * @return MySQLiRecord | false
     */
    function query($sql)
    {
        $result = false;
        for ($i = 0; $i < 2; $i++)
        {
            $result = parent::query($sql);
            if ($result === false)
            {
                if ($this->errno == 2013 or $this->errno == 2006)
                {
                    $r = $this->checkConnection();
                    if ($r === true)
                    {
                        continue;
                    }
                }
                else
                {
                    if ($this->display_error)
                    {
                        trigger_error(__CLASS__ . " SQL Error: " . $this->errorMessage($sql), E_USER_WARNING);
                        echo Swoole\Error::info("SQL Error", $this->errorMessage($sql));
                    }
                    return false;
                }
            }
            break;
        }
        if (!$result)
        {
            echo \Swoole\Error::info("SQL Error", $this->errorMessage($sql));
            return false;
        }
        return new MySQLiRecord($result);
    }

    /**
     * 检查数据库连接,是否有效，无效则重新建立
     */
    protected function checkConnection()
    {
        if (!@$this->ping())
        {
            $this->close();
            return $this->connect();
        }
        return true;
    }

    /**
     * 获取错误码
     * @return int
     */
    function errno()
    {
        return $this->errno;
    }

    /**
     * 获取受影响的行数
     * @return int
     */
    function getAffectedRows()
    {
        return $this->affected_rows;
    }

    /**
     * 返回上一个Insert语句的自增主键ID
     * @return int
     */
    function Insert_ID()
    {
        return $this->insert_id;
    }
}

class MySQLiRecord implements Swoole\IDbRecord
{
    /**
     * @var \mysqli_result
     */
    public $result;

    function __construct($result)
    {
        $this->result = $result;
    }

    function fetch()
    {
        return $this->result->fetch_assoc();
    }

    function fetchall()
    {
        $data = array();
        while ($record = $this->result->fetch_assoc())
        {
            $data[] = $record;
        }
        return $data;
    }

    function free()
    {
        $this->result->free_result();
    }
}
