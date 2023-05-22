<?php

$hook::addHook('afterController', function () {
    // echo 'after 1';
    $file = dirname(__DIR__) . '/../log/2023-05-21.txt';
    error_log('after controller 1', 3, $file);
}, array('priority' => 0));

$hook::addHook('afterController', function () {
    $file = dirname(__DIR__) . '/../log/2023-05-21.txt';
    error_log('after controller 2', 3, $file);
});
