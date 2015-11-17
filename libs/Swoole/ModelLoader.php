<?php
namespace Swoole;
/**
 * 模型加载器
 * 产生一个模型的接口对象
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage MVC
 */
class ModelLoader
{
	private $swoole = null;
	public $_models = array();

	function __construct($swoole)
	{
		$this->swoole = $swoole;
	}

    function __get($model_name)
    {
        if (isset($this->_models[$model_name]))
        {
            return $this->_models[$model_name];
        }
        else
        {
            return $this->load($model_name);
        }
    }

    function load($model_name)
    {
        $model_file = \Swoole::$app_path . '/models/' . $model_name . '.php';
        if (!is_file($model_file))
        {
            throw new Error("The model [<b>$model_name</b>] does not exist.");
        }
        $model_class = '\\App\\Model\\' . $model_name;
        require_once $model_file;
        $this->_models[$model_name] = new $model_class($this->swoole);
        return $this->_models[$model_name];
    }
}
