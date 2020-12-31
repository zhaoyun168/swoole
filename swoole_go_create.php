<?php
go(function () {
    $db = new Co\MySQL();
    $server = array(
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => '123456zy',
        'database' => 'test',
    );

    $db->connect($server);

    $result = $db->query('SELECT * FROM yb_users WHERE id = 3');
    var_dump($result);
});
