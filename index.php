<?php
session_start();

// Definir o fuso horário para Brasília
date_default_timezone_set('America/Sao_Paulo');

// Inclusão manual dos arquivos necessários
require_once __DIR__ . '/app/Engine/Database.php';
require_once __DIR__ . '/app/Router.php';
require_once __DIR__ . '/app/Controllers/Controller.php';
require_once __DIR__ . '/app/Controllers/UserController.php';
require_once __DIR__ . '/app/Models/UsersModel.php';

use Core\Router;
// Inicializa o roteador
$router = new Router();

// Definição de rotas para a aplicação
$router->get('/', 'UserController@listarUsuarios');
$router->get('/usuarios', 'UserController@listarUsuarios');
$router->get('/cadastro', 'UserController@cadastrar');
$router->get('/login', 'UserController@login');
$router->post('/login', 'UserController@login');
$router->get('/logout', 'UserController@logout');
$router->post('/cadastro', 'UserController@cadastrar');
$router->get('/update/(\d+)', 'UserController@atualizar');
$router->post('/update/(\d+)', 'UserController@atualizar');
$router->post('/cadastro', 'UserController@cadastrar');

// Executa o roteador
$router->run();