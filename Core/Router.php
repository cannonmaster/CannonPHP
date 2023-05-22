<?php

namespace Core;

class Router
{
    protected $routes = [];
    protected $params = [];
    protected $registry;

    /**
     * Router constructor.
     *
     * @param mixed $registry The registry object.
     */
    public function __construct($registry)
    {
        $this->registry = $registry;
    }

    /**
     * Attempts to match the given URL against the defined routes.
     *
     * @param string $url The URL to match.
     * @return bool True if a match is found, false otherwise.
     */
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

    /**
     * Converts the controller name to camelCase.
     *
     * @param string $controller The controller name.
     * @return string The camelCase version of the controller name.
     */
    private function controllerNameCamel($controller)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $controller)));
    }

    /**
     * Converts the method name to camelCase.
     *
     * @param string $methodName The method name.
     * @return string The camelCase version of the method name.
     */
    private function methodNameCamel($methodName)
    {
        return lcfirst($this->controllerNameCamel($methodName));
    }

    /**
     * Dispatches the request to the appropriate controller and method based on the matched route.
     *
     * @param string $url The URL to dispatch.
     * @throws \Exception If the controller or method is not found.
     */
    public function dispatch($url)
    {
        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->controllerNameCamel($controller);
            $controller = $this->getNamespace() . $controller;
            if (class_exists($controller)) {
                $this->params['action'] = isset($this->params['action']) ? $this->params['action'] : 'index';
                $controller_object = new $controller($this->params, $this->registry);
                $this->registry->set('currentRoute', $controller_object);
                $method = $this->params['action'];
                $method = $this->methodNameCamel($method);
                if (preg_match('/action$/i', $method) == 0) {
                    $controller_object->$method();
                } else {
                    throw new \Exception("Method $method in controller $controller cannot be called directly");
                }
            } else {
                throw new \Exception("Controller $controller not found");
            }
        } else {
            throw new \Exception('Router not found', 404);
        }
    }

    /**
     * Retrieves the namespace for the controllers.
     *
     * @return string The namespace for the controllers.
     */
    protected function getNamespace()
    {
        $namespace = 'App\\Controller\\';
        if (isset($this->params['namespace'])) {
            return $namespace . $this->params['namespace'] . '\\';
        }
        return $namespace;
    }

    /**
     * Removes the query string variables from the URL.
     *
     * @param string $url The URL to remove the query string variables from
     * @return string The processed URL.
     */
    protected function removeQueryStringVariables($url)
    {
        if ($url === '') {
            return $url;
        }
        return parse_url($url, PHP_URL_PATH) ?: '';
    }

    /**
     * Adds a new route to the router.
     *
     * @param string $route The route pattern.
     * @param array $params The route parameters.
     */
    public function add($route, $params = [])
    {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        $route = '/^' . $route . '$/i';
        $this->routes[$route] = $params;
    }

    /**
     * Retrieves the route parameters.
     *
     * @return array The route parameters.
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Retrieves the defined routes.
     *
     * @return array The defined routes.
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Loads the routes from the Routes.php file.
     */
    public function load()
    {
        $router = $this;
        require(dirname(__DIR__) . '/App/Routes.php');
    }
}
