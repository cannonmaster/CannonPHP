<?php
// require '../Core/Router.php';
// require '../App/Controller/Apples.php';

require_once '../vendor/autoload.php';
// spl_autoload_register(function ($class) {
//   $root = dirname(__DIR__);
//   $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
//   if (is_readable($file)) {
//     require $root . '/' . str_replace('\\', '/', $class) . '.php';
//   }
// });
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

$router = new Core\Router();

// $router->add('posts/new', ['controller' => 'Posts', 'action' => 'new']);

// echo '<pre>';
// var_dump($router->getRoutes());
// echo '</pre>';
// echo $_SRVER['QUERY_STRING'];
$url = $_SERVER['QUERY_STRING'];
// $router->add('admin/{controller}/{action}');
$router->add('{controller}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);
$router->add('{controller}/{id:\d+}/{action}');
$router->dispatch($url);
