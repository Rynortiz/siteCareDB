<?php

use App\Controller\FavoritoController;
use App\Controller\GaleriaController;
use App\Controller\HomeController;
use App\Controller\LoginController;
use App\Controller\NotFoundController;
use App\Controller\CadastroController;
use App\Controller\VelaController;
use App\Controller\AdminVelaController;

require_once __DIR__ . '/../vendor/autoload.php';

$uri = $_SERVER['REQUEST_URI'];

$routes = [
    '/home' => [new HomeController, 'render'],
    '/galeria' => [new VelaController, 'listar'],
    '/login' => [new LoginController, 'render'],
    '/login/autenticar' => [new LoginController, 'autenticar'],
    '/logout' => [new LoginController, 'logout'],
    '/cadastro' =>[new CadastroController, 'render'],
    '/cadastro/cadastrar' =>[new CadastroController, 'cadastrar'],
    '/favorito' =>[new FavoritoController, 'listar'],
    '/favorito/toggle' =>[new FavoritoController, 'toggle'],
    '/admin' => [new AdminVelaController, 'listar'],
    '/admin/criar' => [new AdminVelaController, 'criar'],
    '/admin/editar' => [new AdminVelaController, 'editar'],
    '/admin/remover' => [new AdminVelaController, 'remover'],
    //'/product' => [new ProductController, 'render'],
    //'/product/create' => [new ProductController, 'create']
];

$controller = $routes[$uri] ?? [new NotFoundController, 'render'];

call_user_func($controller);