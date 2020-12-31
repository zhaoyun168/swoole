<?php
echo PHP_EOL . time() ;
$worker_num =3;//创建的进程数
for($i=0;$i<$worker_num ; $i++){
    $process = new swoole_process('callback_function_we_write');
    $pid = $process->start();
}

function callback_function_we_write(swoole_process $worker){
    for($i=0;$i<100000000;$i++){}
    echo PHP_EOL . time() ; 
}
