<?php

namespace Engine;

class Di
{
    /**
     * @var array Data container for storing dependencies
     */
    private $data = [];

    /**
     * Magic method to get a dependency by key.
     *
     * @param string $key The key of the dependency
     * @return mixed|null The dependency value if found, null otherwise
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * Magic method to set a dependency by key.
     *
     * @param string $key The key of the dependency
     * @param object $value The value of the dependency
     */
    public function __set(string $key, object $value): void
    {
        $this->set($key, $value);
    }

    /**
     * Set a dependency by key.
     *
     * @param string $key The key of the dependency
     * @param object $value The value of the dependency
     */
    public function set(string $key, object $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * Get a dependency by key.
     *
     * @param string $key The key of the dependency
     * @return mixed|null The dependency value if found, null otherwise
     */
    public function get(string $key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Check if a dependency exists by key.
     *
     * @param string $key The key of the dependency
     * @return bool True if the dependency exists, false otherwise
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Remove a dependency by key.
     *
     * @param string $key The key of the dependency
     */
    public function remove(string $key): void
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }
    }
}
