<?php
/**
 * 这里没办法保证原子性，请线上服务使用redis，httpsqs或系统的ipcs消息队列
 */
class CacheQueue implements IQueue
{
	private $swoole;
	private $cache;
	private $start_id = 1;
	private $end_id = 1;
	private $compress = false;
	private $compress_level = 9;

	public $name = 'swoole';
	public $prefix = 'queue_';
	private $cache_prefix;
	static $cache_lifetime = 0;
	static $mutex_loop = 100;

	function __construct($config)
	{
		global $php;
		if(!empty($config['name'])) $this->name = $config['name'];
		if(!empty($config['prefix'])) $this->prefix = $config['prefix'];
		if(empty($config['server_url'])) $this->cache = $php->cache;
		else
		{
			$this->cache = new Cache($config['server_url']);
		}
		$this->init();
	}
	private function init()
	{
		$this->cache_prefix = $this->prefix.$this->name.'_';
		//队列起始ID
		$start_id = $this->cache->get($this->cache_prefix.'start');
		if($start_id!==false) $this->start_id = $start_id;
		//队列结束ID
		$end_id = $this->cache->get($this->cache_prefix.'end');
        if($end_id!==false) $this->end_id = $end_id;
	}
	function push($data)
	{
	    $c_id = $this->end_id;
	    $this->cache->increment($this->cache_prefix.'end');
		$this->cache->set($this->cache_prefix.$c_id,$data,self::$cache_lifetime);
		$this->cache->save();
		return true;
	}
	function pop()
	{
		$c_id = $this->start_id;
	    $data = $this->cache->get($this->cache_prefix.$c_id);
		if($data===false) return false;
		else
		{
		    $this->cache->increment($this->cache_prefix.'start');
			$this->cache->delete($this->cache_prefix.$c_id);
			$this->cache->save();
            return $data;
		}
	}
}