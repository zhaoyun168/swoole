<?php
// 同步版, redis使用时会有 IO 阻塞
$cnt = 2000;

for ($i = 0; $i < $cnt; $i++) {
    $redis = new \Redis();
    $redis->connect('127.0.0.1',6379);
    $redis->auth('ab123456');
    echo $key = $redis->get('name');
}

/*
// 单协程版: 只有一个协程, 并没有使用到协程调度减少 IO 阻塞
go(function () use ($cnt) {
    for ($i = 0; $i < $cnt; $i++) {
        $redis = new Co\Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->auth('ab123456');
        echo $redis->get('name');
    }
});
*/

/*
// 多协程版, 真正使用到协程调度带来的 IO 阻塞时的调度
for ($i = 0; $i < $cnt; $i++) {
    go(function () {
        $redis = new \Co\Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->auth('ab123456');
        echo $redis->get('name');
    });
}
*/
