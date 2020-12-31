<?php
$client    = new \swoole_client(SWOOLE_SOCK_TCP);
$num=rand(111111,999999);
$rts=$client->connect('127.0.0.1', 9508, 10) or die("连接失败");//链接mysql客户端
$sql =("select  * from yb_users");
$client->send($sql);  
$resdata = $client->recv();   

$resda=json_decode($resdata,true);
$client->close();
echo json_encode($resda);
