<?php

/**
 * Adds a hook to be executed after the controller.
 *
 * @param string $hookName The name of the hook.
 * @param callable $callback The callback function to be executed.
 * @param array $options Additional options for the hook.
 *                        Available options:
 *                        - priority: The priority of the hook (default: 10).
 * @return void
 */
$hook::addHook('afterController', function () {
    // echo 'after 1';
    $file = dirname(__DIR__) . '/../log/2023-06-11.txt';
    error_log('after controller 1', 3, $file);
}, array('priority' => 0));

/**
 * Adds a hook to be executed after the controller.
 *
 * @param string $hookName The name of the hook.
 * @param callable $callback The callback function to be executed.
 * @param array $options Additional options for the hook.
 *                        Available options:
 *                        - priority: The priority of the hook (default: 10).
 * @return void
 */
$hook::addHook('afterController', function () {
    $file = dirname(__DIR__) . '/../log/2023-06-11.txt';
    error_log('after controller 2', 3, $file);
});
