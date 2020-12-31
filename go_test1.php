<?php
Co\run(function () {
    $wg = new \Swoole\Coroutine\WaitGroup();
    $result = [];

    // 增加第一个计数
    $wg->add();
    // 启动第一个协程
    go(function () use ($wg, &$result) {
        //启动一个协程客户端client，请求淘宝首页
        $cli = new \Swoole\Coroutine\Http\Client('www.taobao.com', 80);
        $cli->setHeaders([
            'Host' => 'www.taobao.com',
            'User-Agent' => 'Chrome/49.0.2587.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml',
            'Accept-Encoding' => 'gzip',
        ]);
        $cli->set(['timeout' => 1]);
        $cli->get('/index.php');

        $result['taobao'] = $cli->body;
        $cli->close();

        // 标记任务完成
        $wg->done();
    });

    // 增加第二个计数
    $wg->add();
    // 启动第二个协程
    go(function () use ($wg, &$result) {
        //启动一个协程客户端client，请求百度首页
        $cli = new \Swoole\Coroutine\Http\Client('www.baidu.com', 80);
        $cli->setHeaders([
            'Host' => 'www.baidu.com',
            'User-Agent' => 'Chrome/49.0.2587.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml',
            'Accept-Encoding' => 'gzip',
        ]);
        $cli->set(['timeout' => 1]);
        $cli->get('/index.php');

        $result['baidu'] = $cli->body;
        $cli->close();

        // 标记任务完成
        $wg->done();
    });

    // 挂起当前协程，等待所有任务完成后恢复当前协程的执行
    $wg->wait();
    //这里 $result 包含了 2 个任务执行结果
    var_dump($result);
});
