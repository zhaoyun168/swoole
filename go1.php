<?php

//sleep(100);

go(function () {
    echo "hello go1 \n";
});

echo "hello main \n";

go(function () {
    echo "hello go2 \n";
});
