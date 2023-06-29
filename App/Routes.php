<?php

// customize route table
$router->add('', ['controller' => 'home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);
$router->add('{controller}/{id:\d+}/{action}');
