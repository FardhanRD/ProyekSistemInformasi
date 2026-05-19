<?php
$t = microtime(true);
$mysqli = @mysqli_connect('127.0.0.1', 'root', '', 'db_apk_main', 3306);
if (!$mysqli) {
    echo 'fail: ' . mysqli_connect_error();
} else {
    echo 'ok';
    mysqli_close($mysqli);
}
echo ' time=' . round(microtime(true) - $t, 2) . PHP_EOL;
