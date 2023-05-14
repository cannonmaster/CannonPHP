<?php

namespace Core;

abstract class BaseController
{
    protected $route_params = [];
    protected $beforeHooks = [];
    protected $afterHooks = [];
    protected $registry;
    public function __call($method, $args)
    {
        // $method = $method . 'Action';
        if (method_exists($this, $method . 'Action')) {
            if ($this->before() !== false) {
                if ($this->beforeAction() !== false) {
                    $output = call_user_func_array([$this, $method . 'Action'], $args);
                    $this->registry->get('response')->setoutput($output);
                    // $this->afterAction();
                }
                // $this->after();
            }
        } else if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], $args);
        } else {
            throw new \Exception("method $method not found in controller " . get_class($this));
        }
        // static $twig;
        // if (!$twig) {
        //     $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/View');
        //     $twig = new \Twig\Environment($loader, [
        //         'cache' => '../Cache',
        //     ]);
        // }
    }
    protected function before()
    {
        // echo "(before) ";
        if (null !== $this->registry->get('hook')->getHook('beforeController')) {
            $this->registry->get('hook')->execute('beforeController');
        }
    }
    protected function after()
    {
        // echo ' (after)';
        if (null !== $this->registry->get('hook')->getHook('afterController')) {
            $this->registry->get('hook')->execute('afterController');
        }
    }
    protected function beforeAction()
    {
        $controller = $this->route_params['controller'];
        $action = $this->route_params['action'];
        $hook = "before:$controller/$action";
        if (null !== $this->registry->get('hook')->getHook($hook)) {
            $this->registry->get('hook')->execute($hook);
        }
    }
    protected function afterAction()
    {
        $controller = $this->route_params['controller'];
        $action = $this->route_params['action'];
        $hook = "after:$controller/$action";
        if (null !== $this->registry->get('hook')->getHook($hook)) {
            $this->registry->get('hook')->execute($hook);
        }
    }
    public function __construct($parames, $registry)
    {
        $this->route_params = $parames;
        $this->registry = $registry;
    }
    public function __get(string $key)
    {
        if ($this->registry->has($key)) {
            return $this->registry->get($key);
        }
        return null;
    }

    public function __set(string $key, $data): void
    {
        $this->registry->set($key, $data);
    }
}
