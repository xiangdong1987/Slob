<?php
namespace Swoole;

/**
 * 所有Swoole应用类的基类
 * Class Ojbect
 *
 * @package Swoole
 * @property Database            $db
 * @property IFace\Cache         $cache
 * @property Upload              $upload
 * @property Event               $event
 * @property Session             $session
 * @property Template            $tpl
 * @property \redis              $redis
 * @property \MongoClient        $mongo
 * @property Config              $config
 * @property Http\PWS            $http
 * @property Log                 $log
 * @property Auth                $user
 * @property URL                 $url
 * @property Limit               $limit
 *          @method Database     db
 *          @method \MongoClient mongo
 *          @method \redis       redis
 *          @method IFace\Cache  cache
 *          @method URL          url
 */
class Object
{
    /**
     * @var \Swoole
     */
    public $swoole;

    function __get($key)
    {
        return $this->swoole->$key;
    }

    function __call($func, $param)
    {
        return call_user_func_array(array($this->swoole, $func), $param);
    }
}