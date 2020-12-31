<?php
Co\run(function () {
    $wg = new \Swoole\Coroutine\WaitGroup();
    $outChan = new Swoole\Coroutine\Channel(1);

    $requestUrl = "https://cn.bing.com/search?q=urldecode";
    $urlArr = parse_url($requestUrl);
    $host = $urlArr['host'];
    $port = isset($urlArr['port']) ? $urlArr['port'] : 80;
    $uri = sprintf("%s?%s", $urlArr['path'], $urlArr['query']);

    for ($i = 1; $i <= 10;$i++) {
        $wg->add();
        go(function () use ($outChan, $i, $host, $port, $uri) {
            $cli = new Swoole\Coroutine\Http\Client($host, $port);
            $cli->set(
                array(
                    'timeout' => 3
                )
            );

            $cli->setHeaders(
                array(
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36',
                )
            );

            $uri = sprintf("%s&page=%d", $uri, $i);
                $cli->get($uri);
                $html = $cli->statusCode;
                $cli->close();

                if(strlen($html) > 0){
                    $outChan->push(array($i, $html)) ;
                }

                //出让协程执行
                co::sleep(0.05);
            });
            $wg->done();
        }

    $wg->add();
    go(function () use ($wg, $outChan) {
        while (true) {
            Co::sleep(1);
            var_dump($outChan->pop());
        }
        $wg->done();
    });

    $wg->wait();
});
