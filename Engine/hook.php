<?php

namespace Engine;

class Hook
{
    static array $hooks = [];

    public static function addCustomHook($key, $hook, $options = array())
    {
        self::addHook($key, $hook, $options);
    }

    public static function addHook($key, $hook, $option = array("priority" => 0))
    {
        if (!empty($option['runwhen']) ?? false) {
            $key = $option['runwhen'] . ':' . $key;
        }
        if (!isset(self::$hooks[$key])) {
            self::$hooks[$key] = [];
        }
        self::$hooks[$key][] = [
            'hook' => $hook,
            'priority' => isset($option["priority"]) ? $option['priority'] : 0
        ];


        $hooks = &self::$hooks[$key];
        array_multisort(array_column($hooks, 'priority'), SORT_DESC, array_keys($hooks), SORT_ASC, $hooks);
    }

    public static function getHook($key)
    {
        return self::hasHook($key) ?  self::$hooks[$key] : null;
    }

    public static function hasHook($key)
    {
        return isset(self::$hooks[$key]);
    }

    public function load()
    {
        $hook = $this;
        foreach (glob(dirname(__DIR__) . '/App/Hook/*.php') as $filename) {
            require_once $filename;
        }

        // require(dirname(__DIR__) . '/App/Hook/BeforeControllerHook.php');
        // require(dirname(__DIR__) . '/App/Hook/AfterControllerHook.php');
    }

    public function execute($key)
    {
        if (isset(self::$hooks[$key])) {
            foreach (self::$hooks[$key] as $key => $hook) {
                $hook['hook']();
            }
        }
    }
}
