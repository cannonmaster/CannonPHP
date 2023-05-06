<?php

namespace App\Controller;

use Core\BaseController;
use Core\View;

class Apples extends BaseController
{
    public function indexAction()
    {
        echo 'apple index method';
        $name = 'hi';
        // return View::render('Home/index.php', ['name' => $name]);
        return View::renderTemplate('Home/index.html', ['name' => $name]);
    }
    public function makeJuiceAction()
    {
        echo htmlspecialchars(print_r($this->route_params, true));
        echo 'made a juice';
    }
}
