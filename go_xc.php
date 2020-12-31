<?php
$start_time = time();
for ($i = 0; $i <= 500; $i++) {
    go(function ()use($i,$start_time){
        $cli = new Swoole\Coroutine\Http\Client('www.baidu.com');
        $cli->setHeaders([
            'Host' => "www.baidu.com",
            "User-Agent" => 'Chrome/49.0.2587.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml',
            'Accept-Encoding' => 'gzip',
        ]);
        $cli->set([ 'timeout' => 0.11]);
        $cli->get('/');
        $cli->close();
        echo  "协程{$i}已完成,耗时".(time()-$start_time).PHP_EOL;
    });
}

/*
$start_time = time();
for ($i = 0; $i <= 500; $i++) {
    $url     = 'https://www.baidu.com/';
    $content = file_get_contents($url);
    echo "普通{$i}已完成\n";
}
*/

echo "非携程完成时间:" . (time() - $start_time);
