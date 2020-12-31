<?php

//获取多个网页信息

$urls = [
    'https://www.baidu.com',
    'http://www.52fhy.com',
    'http://www.52fhy.com/1',
    'https://www.52fhy.com',
];

$process = new swoole_process(function(swoole_process $worker) use($urls) {
    foreach ($urls as $url) {
        $code = getHttpCode($url);
        $worker->push($url.': '.$code);
    }
    $worker->push('exit');
}, false, false); //不创建管道
$process->useQueue(1, 2); //使用消息队列。消息队列通信方式与管道不可共用。消息队列不支持EventLoop，使用消息队列后只能使用同步阻塞模式非阻塞
$process->start();

while(1){
    $ret = $process->pop();
    if($ret == 'exit') break;
    echo sprintf("%s\n", $ret);
}

echo "ok.\n";

while($ret = swoole_process::wait()){
    echo PHP_EOL."Worker Exit, PID=" . $ret['pid'] . PHP_EOL;
}

/**
 * 获取网页http code
 */
function getHttpCode($url){
   //省略
}
