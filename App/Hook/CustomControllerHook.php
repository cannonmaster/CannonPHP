<?php
$hook::addCustomHook('apples/index', function () {
    // echo 'apples index abc';
    $file = dirname(__DIR__) . '/../log/2023-05-21.txt';
    error_log('action hook1', 3, $file);
}, array('runwhen' => 'before'));

$hook::addCustomHook('apples/index', function () {
    // echo 'apples index 123';
    $file = dirname(__DIR__) . '/../log/2023-05-21.txt';
    error_log('action hook2', 3, $file);
}, array('runwhen' => 'before'));

$hook::addCustomHook('apples/index', function () {
    // echo 'apples index 321';
    $file = dirname(__DIR__) . '/../log/2023-05-21.txt';
    error_log('action hook3', 3, $file);
}, array('runwhen' => 'after'));
