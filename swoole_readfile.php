<?php
swoole_async_readfile( __DIR__."/Test.txt", function($filename, $content) {
    echo "$filename: $content";
});
