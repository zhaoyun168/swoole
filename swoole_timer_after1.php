<?php
swoole_timer_after(1000, function(){
    echo "timeout\n";
});
