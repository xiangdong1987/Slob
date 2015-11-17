<?php
namespace Swoole\Cache;
/**
 * 数据库缓存
 * @author Tianfeng.Han
 * @package Swoole
 * @subpackage cache
 */
class DBCache implements \Swoole\IFace\Cache
{
    public $swoole;
    public $shard_id = 0;

    function __construct($table)
    {
        //用URL配置缓存
        if(is_array($table)) $table = $table['params']['table'];
        global $php;
        $this->model = new \Swoole\Model($php);
        $this->model->table = $table;
        $this->model->create_sql = "CREATE TABLE `{$table}` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `ckey` VARCHAR( 128 ) NOT NULL ,
            `cvalue` TEXT NOT NULL ,
            `sid` INT NOT NULL ,
            `expire` INT NOT NULL ,
            INDEX ( `ckey` )
            ) ENGINE = MYISAM ;";
    }

    function create_table()
    {
        $this->model->create_table();
    }

    function shard($id)
    {
        $this->shard_id= $id;
    }

    function gets($key_like)
    {
        $gets['sid'] = $this->shard_id;
        $gets['order'] = '';
        $gets['select'] = 'id,ckey,cvalue,expire';
        $gets['like'] = array('ckey',$key_like.'%');
        $list = $this->model->gets($gets);
        foreach($list as $li)
        {
            $return[$li['ckey']] = $this->_filter_expire($li);
        }
        return $return;
    }

    function getm()
    {
        $params = func_get_args();
        $gets['sid'] = $this->shard_id;
        $gets['order'] = '';
        $gets['select'] = 'id,ckey,cvalue,expire';
        $gets['in'] = array('ckey','"'.implode('","',$params).'"');
        $list = $this->model->gets($gets);
        foreach($list as $li)
        {
            $return[$li['ckey']] = $this->_filter_expire($li);
        }
        return $return;
    }

    function get($key)
    {
        $gets['sid'] = $this->shard_id;
        $gets['limit'] = 1;
        $gets['order'] = '';
        $gets['select'] = 'id,cvalue,expire';
        $gets['ckey'] = $key;
        $rs = $this->model->gets($gets);
        if(empty($rs)) return false;
        return $this->_filter_expire($rs[0]);
    }
    private function _filter_expire($rs)
    {
        if($rs['expire']!=0 and $rs['expire']<time())
        {
            $this->model->del($rs['id']);
            return false;
        }
        else return $rs['cvalue'];
    }
    function set($key,$value,$expire=0)
    {
        $in['ckey'] = $key;
        if(is_array($value)) $value = serialize($value);
        $in['cvalue'] = $value;
        if($expire==0) $in['expire'] = $expire;
        else $in['expire'] = time() + $expire;
        $in['sid'] = $this->shard_id;
        $this->model->put($in);
    }
    function delete($key)
    {
        $gets['sid'] = $this->shard_id;
        $gets['limit'] = 1;
        $gets['ckey'] = $key;
        $this->model->dels($gets);
    }
}