<?php
require '../Core/Router.php';
$router = new Router();

// $router->add('posts/new', ['controller' => 'Posts', 'action' => 'new']);

// echo '<pre>';
// var_dump($router->getRoutes());
// echo '</pre>';
// echo $_SRVER['QUERY_STRING'];
$url = $_SERVER['QUERY_STRING'];
// $router->add('admin/{controller}/{action}');
// $router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
if ($router->match($url)) {
  var_dump($router->getParams());
} else {
  echo 'No route found for url';
}
