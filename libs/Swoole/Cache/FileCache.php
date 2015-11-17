<?php
namespace Swoole\Cache;
/**
 * 文件缓存类，提供类似memcache的接口
 * 警告：此类仅用于测试，不作为生产环境的代码，请使用Key-Value缓存系列！
 * @author Tianfeng.Han
 * @package Swoole
 * @subpackage cache
 */
class FileCache implements \Swoole\IFace\Cache
{
    protected $config;
	function __construct($config)
	{
        if (!isset($config['cache_dir']))
        {
            throw new \Exception(__CLASS__.": require cache_dir");
        }
        if (!is_dir($config['cache_dir']))
        {
            mkdir($config['cache_dir'], 0755, true);
        }
        $this->config = $config;
    }

    protected function getFileName($key)
    {
        $file = $this->config['cache_dir'] . '/' . trim(str_replace('_', '/', $key), '/');
        $dir = dirname($file);
        if(!is_dir($dir))
        {
            mkdir($dir, 0755, true);
        }
        return $file;
    }

    function set($key, $value, $timeout=0)
	{
        $file = $this->getFileName($key);
        $data["value"] = $value;
        $data["timeout"] = $timeout;
        $data["mktime"] = time();
        return file_put_contents($file, serialize($data));
    }

	function get($key)
	{
        $file = $this->getFileName($key);
        if(!is_file($file)) return false;
        $data = unserialize(file_get_contents($file));
        if (empty($data) or !isset($data['timeout']) or !isset($data["value"]))
        {
            return false;
        }
        //已过期
        if ($data["timeout"] != 0 and ($data["mktime"] + $data["timeout"]) < time())
        {
            $this->delete($key);
            return false;
        }
        return $data['value'];
	}

	function delete($key)
	{
        $file = $this->getFileName($key);
        return unlink($file);
	}
}