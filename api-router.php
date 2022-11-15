<?php
require_once './libs/Router.php';
require_once './app/controller/disco-api.controller.php';
require_once './app/controller/auth-api.controller.php';

// crea el router
$router = new Router();

// defina la tabla de ruteo
$router->addRoute('discos', 'GET', 'discoApiController', 'getAll');
$router->addRoute('discos/:ID', 'GET', 'discoApiController', 'getDisco');
$router->addRoute('discos/:ID', 'DELETE', 'discoApiController', 'deleteDisco');
$router->addRoute('discos', 'POST', 'discoApiController', 'insertDisco');
$router->addRoute('discos/:ID', 'PUT', 'discoApiController', 'updateDisco'); 

$router->addRoute('auth/token', 'GET', 'AuthApiController', 'getToken');

// ejecuta la ruta (sea cual sea)
$router->route($_GET["resource"], $_SERVER['REQUEST_METHOD']);