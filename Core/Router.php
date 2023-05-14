<?php

namespace Core;



class Router
{
    protected $routes = [];
    protected $params = [];
    protected $registry;

    public function __construct($registry)
    {
        $this->registry = $registry;
    }
    public function match($url)
    {
        $url = $this->removeQueryStringVariables($url);
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }
    private function controllerNameCamel($controller)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $controller)));
    }
    private function methodNameCamel($methodName)
    {
        return lcfirst($this->controllerNameCamel($methodName));
    }
    public function dispatch($url)
    {
        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->controllerNameCamel($controller);
            $controller = $this->getNamespace() . $controller;
            if (class_exists($controller)) {
                $controller_object = new $controller($this->params, $this->registry);
                $this->registry->set('currentRoute', $controller_object);
                $method = $this->params['action'];
                $method = $this->methodNameCamel($method);
                if (preg_match('/action$/i', $method) == 0) {
                    $controller_object->$method();
                } else {
                    throw new \Exception('Method $action in controller $controller cannon be called directly');
                }
            } else {
                throw new \Exception("controller $controller not found");
            }
        } else {
            throw new \Exception('router not found', 404);
        }
    }
    protected function getNamespace()
    {
        $namespace = 'App\\Controller\\';
        if (isset($this->params['namespace']))
            return $namespace . $this->params['namespace'] . '\\';
        return $namespace;
    }
    protected function removeQueryStringVariables($url)
    {
        if ($url === '') return $url;
        $matches = explode('&', $url, 2);
        if (false === strpos($matches[0], '=')) return $matches[0];
        return '';
    }
    public function add($route, $params = [])
    {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        $route = '/^' . $route . '$/i';
        $this->routes[$route] = $params;
    }
    public function getParams()
    {
        return $this->params;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function load()
    {
        $router = $this;
        require(dirname(__DIR__) . '/App/Routes.php');
    }
}
