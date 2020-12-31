<?php
swoole_timer_tick(1000, function(){
    echo "timeout\n";
});
