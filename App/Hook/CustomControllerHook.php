<?php
$hook::addCustomHook('apples/index', function () {
    // echo 'apples index abc';
}, array('runwhen' => 'before'));

$hook::addCustomHook('apples/index', function () {
    // echo 'apples index 123';
}, array('runwhen' => 'before'));

$hook::addCustomHook('apples/index', function () {
    // echo 'apples index 321';
}, array('runwhen' => 'after'));
