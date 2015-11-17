<?php
namespace Swoole;
class Config extends \ArrayObject
{
    protected $config_path;
    public $dir_num = 0;
    static $debug = false;
    static $active = false;

    function setPath($dir)
    {
        $this->config_path[] = $dir;
        self::$active = true;
    }

	function offsetGet($index)
	{
		if(!isset($this->config[$index]))
		{
			$this->load($index);
		}
		return isset($this->config[$index])?$this->config[$index]:false;
	}

	function load($index)
	{
        foreach ($this->config_path as $path)
        {
            $filename = $path . '/' . $index . '.php';
            if (is_file($filename))
            {
                $retData = include $filename;
                if (empty($retData) and self::$debug)
                {
                    trigger_error(__CLASS__ . ": $filename no return data");
                }
                else
                {
                    $this->config[$index] = $retData;
                }
            }
            elseif (self::$debug)
            {
                trigger_error(__CLASS__ . ": $filename not exists");
            }
        }
	}

	function offsetSet($index, $newval)
	{
        $this->config[$index] = $newval;
	}

	function offsetUnset($index)
	{
        unset($this->config[$index]);
	}

	function offsetExists($index)
	{
		return isset($this->config[$index]);
	}
}