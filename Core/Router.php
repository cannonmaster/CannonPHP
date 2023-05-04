<?php
class Router
{
    protected $routes = [];
    protected $params = [];

    public function match($url)
    {

        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                // var_dump($matches);
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
    public function add($route, $params = [])
    {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        $route = '/^' . $route . '$/i';
        // var_dump($route);
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
}
