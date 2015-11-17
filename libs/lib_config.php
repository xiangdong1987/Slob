<?php
/**
 * 基本函数，全局对象$php的构造
 * @package SwooleSystem
 * @author 韩天峰
 */
define("LIBPATH", __DIR__);
if (PHP_OS == 'WINNT')
{
    define("NL", "\r\n");
}
else
{
    define("NL", "\n");
}
define("BL", "<br />" . NL);

require_once __DIR__ . '/Swoole/Swoole.php';
require_once __DIR__ . '/Swoole/Loader.php';
/**
 * 注册顶层命名空间到自动载入器
 */
Swoole\Loader::addNameSpace('Swoole', __DIR__.'/Swoole');
spl_autoload_register('\\Swoole\\Loader::autoload');

/**
 * 产生类库的全局变量
 */
global $php;
$php = Swoole::getInstance();

function createModel($model_name)
{
    return model($model_name);
}

/**
 * 生产一个model接口，模型在注册树上为单例
 * @param $model_name
 * @return Swoole\Model
 */
function model($model_name)
{
    global $php;
    return $php->model->$model_name;
}

/**
 * 传入一个数据库表，返回一个封装此表的Model接口
 * @param $table_name
 * @return Swoole\Model
 */
function table($table_name, $db_id = 'master')
{
    global $php;
    if (isset($php->model->_models[$table_name]))
    {
        return $php->model->$table_name;
    }
    else
    {
        $model = new Swoole\Model($php, $db_id);
        $model->table = $table_name;
        $php->model->_models[$table_name] = $model;
        return $model;
    }
}

/**
 * 开启会话
 */
function session($readonly = false)
{
    Swoole::getInstance()->session->start($readonly);
}

/**
 * 调试数据，终止程序的运行
 */
function debug()
{
    $vars = func_get_args();
    foreach ($vars as $var)
    {
        if (php_sapi_name() == 'cli')
        {
            var_export($var);
        }
        else
        {
            highlight_string("<?php\n" . var_export($var, true));
            echo '<hr />';
        }
    }
    exit;
}
/**
 * 引发一个错误
 * @param $error_id
 * @param $stop
 */
function error($error_id, $stop = true)
{
    global $php;
    $error = new \Swoole\Error($error_id);
    if (isset($php->error_call[$error_id]))
    {
        call_user_func($php->error_call[$error_id], $error);
    }
    elseif ($stop)
    {
        exit($error);
    }
    else
    {
        echo $error;
    }
}

/**
 * 错误信息输出处理
 */
function swoole_error_handler($errno, $errstr, $errfile, $errline)
{
    $info = '';
    switch ($errno)
    {
        case E_USER_ERROR:
            $level = 'User Error';
            break;
        case E_USER_WARNING:
            $level = 'Warnning';
            break;
        case E_USER_NOTICE:
            $level = 'Notice';
            break;
        default:
            $level = 'Unknow';
            break;
    }

    $title = 'Swoole '.$level;
    $info .= '<b>File:</b> '.$errfile."<br />\n";
    $info .= '<b>Line:</b> '.$errline."<br />\n";
    $info .= '<b>Info:</b> '.$errstr."<br />\n";
    $info .= '<b>Code:</b> '.$errno."<br />\n";
    echo Swoole\Error::info($title, $info);
}
