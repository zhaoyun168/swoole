<?php
$serv = new Swoole\Http\Server("127.0.0.1", 9501, SWOOLE_BASE);

$serv->on('request', function ($req, $resp) {
    $chan = new Swoole\Coroutine\Channel(2);
    go(function () use ($chan) {
        $cli = new Swoole\Coroutine\Http\Client('www.qq.com', 80);
        $cli->set(['timeout' => 10]);
        $cli->setHeaders([
            'Host' => "www.qq.com",
            'User-Agent' => 'Chrome/49.0.2587.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml',
            'Accept-Encoding' => 'gzip',
        ]);
        $ret = $cli->get('/');
        $chan->push(['www.qq.com' => $cli->body]);
    });

    go(function () use ($chan) {
        $cli = new Swoole\Coroutine\Http\Client('www.baidu.com', 80);
        $cli->set(['timeout' => 10]);
        $cli->setHeaders([
            'Host' => "www.baidu.com",
            'User-Agent' => 'Chrome/49.0.2587.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml',
            'Accept-Encoding' => 'gzip',
        ]);
        $ret = $cli->get('/');
        $chan->push(['www.baidu.com' => $cli->body]);
    });

    $result = [];
    for ($i = 0; $i < 2; $i++)
    {
        // 当通道为空时，会自动挂起当前协程，等待生产者推送数据后，重新调度进来
        $result += $chan->pop();
    }
    $resp->end(json_encode($result));
});
$serv->start();
