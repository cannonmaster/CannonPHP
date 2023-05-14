<?php
// spl_autoload_register(function ($class) {
//   $root = dirname(__DIR__);
//   $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
//   if (is_readable($file)) {
//     require $root . '/' . str_replace('\\', '/', $class) . '.php';
//   }
// });
// phpinfo();
// exit;

// composer autolaod
require_once '../vendor/autoload.php';

use App\Config;
use Core\Router;
use Engine\Di;
use Engine\Error;
use Engine\Hook;
use Engine\Request;
use Engine\Db;
use Engine\Session;
use Engine\Cache;
use Engine\Language;
use Engine\Response;

// error handler
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

// di
$di = new Engine\Di();
// global config file
$config = new App\Config();
$di->set('config', $config);

// routing system
$router = new Core\Router($di);
// routes registers
$router->load();
$di->set('router', $router);


// global controller hook and custom action before / after hook system
// Todo: add remove hook and clear hook for a specific hook
$hook = new Engine\Hook();
// glob to load all the hook under the app/hook directory
$hook->load();
$di->set('hook', $hook);

// sanitize the global super variable such as $_GET, $_POST, $_FILES , etc ...
$request = new Engine\Request();
$di->set('request', $request);

// setup database

// connection tested but not the query, todo: test query etc ...
if (App\Config::db_autostart) {
  $db = new Engine\Db($di, App\Config::db_engine);
  $di->set('db', $db);
}


// enable session if user config the serssion auto start "true"
if (App\Config::session_autostart) {
  $session = new Engine\Session(App\Config::session_engine, $di);
  $di->set('session', $session);

  if (isset($request->cookie[App\Config::session_name])) {
    $session_id = $request->cookie[App\Config::session_name];
  } else {
    $session_id = '';
  }

  // start the session will retrive the data from the session engine, the session engine could be redis or db
  // todo: add file as session data storage
  $session->start($session_id);
  $option = [
    'expires' => 0,
    'path' => App\Config::session_path,
    'domain' => App\Config::session_domain,
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => App\Config::session_http_only,
    'Samesite' => App\Config::session_samesite
  ];

  setcookie(App\Config::session_name, $session->getId(), $option);
}

// cache system (need to be test)
$cache = new Engine\Cache(App\Config::cache_engine, $di);
$di->set('cache', $cache);

// language system (need to be test)
$language = new Engine\Language(App\Config::language, dirname(__DIR__) . '/App/Language/');
$di->set('language', $language);

// reponse 
$response = new Engine\Response($di);
// register response header defined in app/service
foreach (App\Config::header as $header) {
  $response->addHeader($header);
}
// setup compression level 0 - 9
$response->setCompressionLevel(App\Config::compressLevel);
$di->set('response', $response);

// custom service provider register to di container
foreach (glob(dirname(__DIR__) . '/App/Service/*.php') as $service) {
  $classname = basename($service, '.php');
  $class = "App\\Service\\" . $classname;
  if (class_exists($class)) {
    $di->set($classname, new $class($di));
  }
}

// dispatch route from the query_string which from the current visiting url
$router->dispatch($_SERVER['QUERY_STRING']);

// output to client
$response->output();
