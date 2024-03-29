<?php

namespace Core;

abstract class BaseController
{
    protected array $route_params = [];
    protected array $beforeHooks = [];
    protected array $afterHooks = [];
    protected \Engine\Di $registry;

    /**
     * Magic method triggered when invoking inaccessible methods in the controller.
     *
     * @param string $method The method name.
     * @param array  $args   The method arguments.
     *
     * @throws \Exception If the method is not found in the controller.
     */
    public function __call(string $method, array $args)
    {
        if (method_exists($this, $method . 'Action')) {
            if ($this->beforeController() !== false) {
                if ($this->beforeAction() !== false) {
                }
            }
            $output = call_user_func_array([$this, $method . 'Action'], $args);
            $this->registry->get('response')->setOutput($output);
        } else if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], $args);
        } else if (method_exists(self::class, $method)) {
            call_user_func_array([self::class, $method], $args);
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    /**
     * Executed before the controller action.
     */
    protected function beforeController(): bool
    {
        $hook =  $this->registry->get('hook');
        if (null !== $hook->getHook('beforeController')) {
            $hook->execute('beforeController');
            return true;
        }

        return false;
    }

    /**
     * Executed after the controller action.
     */
    protected function afterController(): bool
    {
        if (null !== $this->registry->get('hook')->getHook('afterController')) {
            $this->registry->get('hook')->execute('afterController');
            return true;
        }
        return false;
    }

    /**
     * Executed before the specific controller action.
     */
    protected function beforeAction(): bool
    {
        $controller = $this->route_params['controller'];
        $action = $this->route_params['action'];
        $hook = "before:$controller/$action";
        if (null !== $this->registry->get('hook')->getHook($hook)) {
            $this->registry->get('hook')->execute($hook);
            return true;
        }
        return false;
    }

    /**
     * Executed after the specific controller action.
     */
    protected function afterAction(): bool
    {
        $controller = $this->route_params['controller'];
        $action = $this->route_params['action'];
        $hook = "after:$controller/$action";
        if (null !== $this->registry->get('hook')->getHook($hook)) {
            $this->registry->get('hook')->execute($hook);
            return true;
        }
        return false;
    }

    /**
     * BaseController constructor.
     *
     * @param array $params   The route parameters.
     * @param \Engine\Di $registry The registry object.
     */
    public function __construct(array $params, \Engine\Di $registry)
    {
        $this->route_params = $params;
        $this->registry = $registry;
    }

    /**
     * Magic method triggered when accessing inaccessible properties in the controller.
     *
     * @param string $key The property name.
     *
     * @return mixed|null The value of the property or null if it doesn't exist.
     */
    public function __get(string $key)
    {
        if ($this->registry->has($key)) {
            return $this->registry->get($key);
        }
        return null;
    }

    /**
     * Magic method triggered when setting a value to an inaccessible property in the controller.
     *
     * @param string $key   The property name.
     * @param mixed  $data  The value to be set.
     */
    public function __set(string $key, $data): void
    {
        $this->registry->set($key, $data);
    }
}
