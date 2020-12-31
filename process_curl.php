<?php

//获取多个网页信息

$urls = [
    'https://www.baidu.com',
    'http://www.52fhy.com',
    'http://www.52fhy.com/1',
    'https://www.52fhy.com',
];

foreach ($urls as $key => $url) {
    $process = new swoole_process(function(swoole_process $worker) use ($url){
        $code = getHttpCode($url);
        $worker->write($code);
    }, true);
    $process->start();

    swoole_event_add($process->pipe, function($pipe) use($process, $url) {
        echo sprintf("%s code: %s\n", $url, $process->read());
        swoole_event_del($pipe);
    });
}

echo "ok.\n";

while($ret = swoole_process::wait()){
    // echo PHP_EOL."Worker Exit, PID=" . $ret['pid'] . PHP_EOL;
}

/**
 * 获取网页http code
 */
function getHttpCode($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "HEAD");
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
    curl_setopt ($ch, CURLOPT_TIMEOUT_MS, 1000);//超时时间
    curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    return (string)$info['http_code'];
}
