<?php
$process = new swoole_process('callback_function', true);
//子进程执行的逻辑
function callback_function(swoole_process $worker)
{
    $worker->name('child process');
    swoole_timer_tick(2000,function(){
        echo time();
    });
}
$pid = $process->start();
swoole_set_process_name('parent process');
swoole_timer_tick(2000,function(){
   echo time();
});
