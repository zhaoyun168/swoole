<?php
$swoole_mysql = new Swoole\Coroutine\MySQL();

go(function() use($swoole_mysql) {
	$swoole_mysql->connect([
	    'host' => '127.0.0.1',
	    'port' => '3306',
	    'user' => 'root',
	    'password' => '123456zy',
	    'database' => 'test',
	    'charset' => 'utf8'
	]);
	$res = $swoole_mysql->query('select * from yb_users');

	print_r($res);
});
