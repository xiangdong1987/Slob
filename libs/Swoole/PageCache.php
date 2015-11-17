<?php
namespace Swoole;
/**
 * 页面缓存类
 * Class PageCache
 * @package Swoole
 */
class PageCache
{
	public $cache_dir;
	public $expire;
	
	function __construct($expire=3600,$cache_dir='')
	{
		$this->expire = $expire;
		if($cache_dir==='') $this->cache_dir = WEBPATH.'/cache/pages_c';
		else $this->cache_dir = $cache_dir;		
	}
		
	//建立缓存
	function create($content)
	{
		file_put_contents($this->cache_dir.'/'.base64_encode($_SERVER['REQUEST_URI']).'.html',$content);
	}
	
	//加载缓存
	function load()
	{
		include ($this->cache_dir.'/'.base64_encode($_SERVER['REQUEST_URI']).'.html');
	}
	
	//检查是否存在有效缓存
	function isCached()
	{
		$file=$this->cache_dir.'/'.base64_encode($_SERVER['REQUEST_URI']).'.html';
		if(!file_exists($file)) return false;
		elseif(filemtime ($file)+$this->expire<time()) return false;
		else return true;
	}
}
