<?php

namespace App\Controller;

use Core\BaseController;
use Core\View;

class Home extends  BaseController
{
    public function indexAction()
    {
        $name = 666;


        return View::renderTemplate('Home/index.html', ['name' => $name]);
    }

    public function updateAction($params)
    {
        var_dump($this->session->adapter->read($this->session->getId()));
        return $params['id'];
    }

    public function __destruct()
    {
        echo '123';
    }
}
