<?php
$str = "Say ";
$timer_id = swoole_timer_tick( 1000 , function($timer_id , $params) use ($str) {
    echo $timer_id . $str . $params;  // 输出“Say Hello”
    
} , "Hello" );
