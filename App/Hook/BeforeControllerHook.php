<?php

$hook::addHook('beforeController', function () {
    // echo 'before 1';
    $file = dirname(__DIR__) . '/../log/2023-05-21.txt';
    error_log('0', 3, $file);
});
$hook::addHook('beforeController', function () {
    // echo 'before 2';
    $file = dirname(__DIR__) . '/../log/2023-05-21.txt';
    error_log('1', 3, $file);
}, array('priority' => 0));
