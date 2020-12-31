<?php
$http = new swoole_http_server("127.0.0.1", 9501);
$http->on('request', function ($request, $response) {
    $html = "<h1>Hello Swoole.</h1>";
    $response->end($html);
});
$http->start();
