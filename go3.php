<?php
go(function () {
    \Co::sleep(1);
    echo "hello go1 \n";
});

echo "hello main \n";

go(function () {
    \Co::sleep(1);
    echo "hello go2 \n";
});
