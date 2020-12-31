<?php
use Swoole\Coroutine as co;

co::create(function ()
{
    $cli = new co\Http2\Client('127.0.0.1', 9518);
    $cli->set([ 'timeout' => 1]);
    $cli->connect();

    $req = new co\Http2\Request;
    $req->path = "/index.html";
    $req->headers = [
        'host' => "localhost",
        "user-agent" => 'Chrome/49.0.2587.3',
        'accept' => 'text/html,application/xhtml+xml,application/xml',
        'accept-encoding' => 'gzip',
    ];
    $req->cookies = ['name' => 'rango', 'email' => '1234@qq.com'];
    var_dump($cli->send($req));
    $resp = $cli->recv();
    var_dump($resp);

});
