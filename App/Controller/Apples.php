<?php

namespace App\Controller;

use Core\BaseController;
use Core\View;

class Apples extends BaseController
{
    public function indexAction()
    {
        // echo 'apple index method';
        $name = 'hi';

        // $output = View::renderTemplate('Home/index.html', ['name' => $name]);
        // $this->response->setoutput($output);
        return View::renderTemplate('Home/index.html', ['name' => $name]);
    }
    public function makeJuiceAction()
    {
        echo htmlspecialchars(print_r($this->route_params, true));
        echo 'made a juice';
    }

    public function testAction()
    {
        echo '123';
    }
}
