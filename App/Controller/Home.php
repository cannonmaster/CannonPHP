<?php

namespace App\Controller;

use Core\BaseController;

class Home extends  BaseController
{
    public function indexAction()
    {
        return 'home/index';
    }
}
