<?php
$timerid = swoole_timer_tick(1000,function(){
		echo time()."\n";
});
swoole_timer_tick(1000,function() use($timerid) {
		swoole_timer_clear($timerid);
});
