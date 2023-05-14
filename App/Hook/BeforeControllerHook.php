<?php

$hook::addHook('beforeController', function () {
    // echo 'before 1';
    // $file = dirname(__DIR__) . '/../log/2023-05-13.txt';
    // error_log('0000', 3, $file);
});
$hook::addHook('beforeController', function () {
    // echo 'before 2';
}, array('priority' => 0));
