<?php
$process = new swoole_process('callback_function');
//子进程执行的逻辑
function callback_function(swoole_process $worker)
{
    swoole_timer_tick(2000,function(){
        echo time();
    });
}
$pid = $process->start();
//父进程执行的逻辑
//监听子进程退出信号
swoole_process::signal(SIGCHLD, function($sig) {
  //必须为false，非阻塞模式
  while($ret =  swoole_process::wait(false)) {
      echo 'process end';
      //执行回收后的处理逻辑，比如拉起一个新的进程
  }
});
