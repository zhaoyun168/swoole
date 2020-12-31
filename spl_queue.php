<?php
$queue = new SplQueue;

$data = ['name' => 'tianyu', 'age' => 21];
//入队
$queue->push($data);
//出队
$data = $queue->shift();
//查询队列中的排队数量
echo $n = count($queue);
