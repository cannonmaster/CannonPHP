<?php

/**
 * Adds a custom hook to be executed before or after the "apples/index" action.
 *
 * @param string $hookName The name of the hook.
 * @param callable $callback The callback function to be executed.
 * @param array $options Additional options for the hook.
 *                        Available options:
 *                        - runwhen: Specifies when the hook should run ("before" or "after").
 * @return void
 */
$hook::addCustomHook('apples/index', function () {
    // echo 'apples index abc';
    $file = dirname(__DIR__) . '/../log/2023-06-11.txt';
    error_log('action hook1', 3, $file);
}, array('runwhen' => 'before'));

/**
 * Adds a custom hook to be executed before or after the "apples/index" action.
 *
 * @param string $hookName The name of the hook.
 * @param callable $callback The callback function to be executed.
 * @param array $options Additional options for the hook.
 *                        Available options:
 *                        - runwhen: Specifies when the hook should run ("before" or "after").
 * @return void
 */
$hook::addCustomHook('apples/index', function () {
    // echo 'apples index 123';
    $file = dirname(__DIR__) . '/../log/2023-06-11.txt';
    error_log('action hook2', 3, $file);
}, array('runwhen' => 'before'));

/**
 * Adds a custom hook to be executed before or after the "apples/index" action.
 *
 * @param string $hookName The name of the hook.
 * @param callable $callback The callback function to be executed.
 * @param array $options Additional options for the hook.
 *                        Available options:
 *                        - runwhen: Specifies when the hook should run ("before" or "after").
 * @return void
 */
$hook::addCustomHook('apples/index', function () {
    // echo 'apples index 321';
    $file = dirname(__DIR__) . '/../log/2023-06-11.txt';
    error_log('action hook3', 3, $file);
}, array('runwhen' => 'after'));
