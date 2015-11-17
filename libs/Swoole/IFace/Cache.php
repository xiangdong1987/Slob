<?php
namespace Swoole\IFace;

interface Cache
{
    /**
     * 设置缓存
     * @param $key
     * @param $value
     * @param $expire
     * @return bool
     */
    function set($key,$value,$expire=0);
    /**
     * 获取缓存值
     * @param $key
     * @return mixed
     */
    function get($key);
    /**
     * 删除缓存值
     * @param $key
     * @return bool
     */
    function delete($key);
}