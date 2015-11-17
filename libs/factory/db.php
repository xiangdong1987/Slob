<?php
global $php;
if (empty($php->config['db'][$php->factory_key]))
{
    throw new Swoole\Exception\Factory("db->{$php->factory_key} is not found.");
}
$db = new Swoole\Database($php->config['db'][$php->factory_key]);
$db->connect();
return $db;
