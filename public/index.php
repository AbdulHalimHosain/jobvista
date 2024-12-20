<?php

require __DIR__ .'/../vendor/autoload.php';


use Framework\Router;
use Framework\Session;

Session::start();


require '../helpers.php';


//instantiate router
$router = new Router();
//get routes
$routes = require basePath('routes.php');
//get uri and http method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); 

//route the request
$router->route($uri);
?>