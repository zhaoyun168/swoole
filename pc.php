<?php

$s_time = time();

echo '开始时间:'.date('H:i:s',$s_time).PHP_EOL;

//进程数

$work_number=6;

  

//

$worker=[];

  

//模拟地址

$curl=[

    'https://blog.csdn.net/feiwutudou',

    'https://wiki.swoole.com/wiki/page/215.html',

    'http://fanyi.baidu.com/?aldtype=16047#en/zh/manager',

    'http://wanguo.net/Salecar/index.html',

    'http://o.ngking.com/themes/mskin/login/login.jsp',

    'https://blog.csdn.net/marksinoberg/article/details/77816991'

];

  

//单线程模式

// foreach ($curl as $v) {

// echo curldeta($v);

// }

  

//创建进程

for ($i=0; $i < $work_number; $i++) {

    //创建多线程

    $pro=new swoole_process(function(swoole_process $work) use($i,$curl){

        //获取html文件

        $content=curldeta($curl[$i]);

        //写入管道

        $work->write($content.PHP_EOL);

    },true);

    $pro_id=$pro->start();

    $worker[$pro_id]=$pro;

}

//读取管道内容

foreach ($worker as $v) {

    echo $v->read().PHP_EOL;

}

  

//模拟爬虫

function curldeta($curl_arr)

{//file_get_contents

    echo $curl_arr.PHP_EOL;

    file_get_contents($curl_arr);

}

  

//进程回收

swoole_process::wait();

  

$e_time = time();

echo '结束时间:'.date('H:i:s',$e_time).PHP_EOL;

  

echo '所用时间:'.($e_time-$s_time).'秒'.PHP_EOL;

?>
