<?php
$process = new swoole_process('callback_function', true);
//子进程执行的逻辑
function callback_function(swoole_process $worker)
{
    echo '子进程创建成功';
}
$ret = $process->start();
echo '子进程进程号为'.$process->pid . "\r\n";
echo '管道的文件描述符为'.$process->pipe . "\r\n";
