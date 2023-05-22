<?php

namespace Engine;

class Hook
{
    /** @var array Array to store the registered hooks */
    static array $hooks = [];

    /** @var \Engine\Di Dependency injection container */
    private \Engine\Di $di;

    /**
     * Hook constructor.
     *
     * @param \Engine\Di $di The dependency injection container
     */
    public function __construct(\Engine\Di $di)
    {
        $this->di = $di;
    }

    /**
     * Add a custom hook with the specified key, hook function, and options.
     *
     * @param string $key The key to identify the hook
     * @param callable $hook The hook function to execute
     * @param array $options Additional options for the hook
     */
    public static function addCustomHook(string $key, callable $hook, array $options = [])
    {
        self::addHook($key, $hook, $options);
    }

    /**
     * Add a hook with the specified key, hook function, and options.
     *
     * @param string $key The key to identify the hook
     * @param callable $hook The hook function to execute
     * @param array $options Additional options for the hook
     */
    public static function addHook(string $key, callable $hook, array $options = ["priority" => 0])
    {
        // Modify the key if 'runwhen' option is provided
        if (!empty($options['runwhen'] ?? false)) {
            $key = $options['runwhen'] . ':' . $key;
        }

        // Initialize the hooks array for the key if it doesn't exist
        if (!isset(self::$hooks[$key])) {
            self::$hooks[$key] = [];
        }

        // Add the hook to the hooks array with priority
        self::$hooks[$key][] = [
            'hook' => $hook,
            'priority' => isset($options["priority"]) ? $options['priority'] : 0
        ];

        // Sort the hooks based on priority
        $hooks = &self::$hooks[$key];
        array_multisort(array_column($hooks, 'priority'), SORT_DESC, array_keys($hooks), SORT_ASC, $hooks);
    }

    /**
     * Get the hooks registered for the specified key.
     *
     * @param string $key The key identifying the hooks
     * @return array|null Array of hooks or null if no hooks found
     */
    public static function getHook(string $key): ?array
    {
        return self::hasHook($key) ?  self::$hooks[$key] : null;
    }

    /**
     * Check if hooks are registered for the specified key.
     *
     * @param string $key The key identifying the hooks
     * @return bool True if hooks are registered, false otherwise
     */
    public static function hasHook(string $key): bool
    {
        return isset(self::$hooks[$key]);
    }

    /**
     * Load the hook files from the specified directory.
     */
    public function load(): void
    {
        $hook = $this;

        // Load hook files from the directory
        foreach (glob(dirname(__DIR__) . '/App/Hook/*.php') as $filename) {
            require_once $filename;
        }
        // Example:
        // require(dirname(__DIR__) . '/App/Hook/BeforeControllerHook.php');
        // require(dirname(__DIR__) . '/App/Hook/AfterControllerHook.php');
    }

    /**
     * Execute the hooks registered for the specified key.
     *
     * @param string $key The key identifying the hooks to execute
     */
    public function execute(string $key): void
    {
        if (isset(self::$hooks[$key])) {
            foreach (self::$hooks[$key] as $hook) {
                $hook['hook']($this->di);
            }
        }
    }
}
