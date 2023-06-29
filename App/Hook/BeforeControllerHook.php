<?php

/**
 * Adds a hook to be executed before the controller.
 *
 * @param string $hookName The name of the hook.
 * @param callable $callback The callback function to be executed.
 * @param array $options Additional options for the hook.
 *                        Available options:
 *                        - priority: The priority of the hook (default: 10).
 * @return void
 */
$hook::addHook('beforeController', function () {
    // echo 'before controller hook1 /';
    // $file = dirname(__DIR__) . '/../log/2023-06-11.txt';
    // error_log('0', 3, $file);
});

/**
 * Adds a hook to be executed before the controller.
 *
 * @param string $hookName The name of the hook.
 * @param callable $callback The callback function to be executed.
 * @param array $options Additional options for the hook.
 *                        Available options:
 *                        - priority: The priority of the hook (default: 10).
 * @return void
 */
$hook::addHook('beforeController', function () {
    // echo 'before controller hook2 /';
    // $file = dirname(__DIR__) . '/../log/2023-06-11.txt';
    // error_log('1', 3, $file);
}, array('priority' => 0));
