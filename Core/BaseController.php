<?php

namespace Core;

abstract class BaseController
{
    protected $route_params = [];
    public function __call($method, $args)
    {
        $method = $method . 'Action';
        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new \Exception("method $method not found in controller " . get_class($this));
        }
        static $twig;
        if (!$twig) {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/View');
            $twig = new \Twig\Environment($loader, [
                'cache' => '../Cache',
            ]);
        }
    }
    protected function before()
    {
        echo "(before) ";
    }
    protected function after()
    {
        echo ' (after)';
    }
    public function __construct($parames)
    {
        $this->route_params = $parames;
    }
}
