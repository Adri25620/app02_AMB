<?php

namespace Controllers;

use Model\ActiveRecord;
use MVC\Router;

class ClienteController extends ActiveRecord {

    public function index(Router $router){
        $router->render('clientes/index', []);
    }
}