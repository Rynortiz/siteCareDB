<?php

use App\Controller\NotFoundController;
use App\Controller\SampleController;
use App\Controller\UserController;
use App\Model\Pedido;
use App\Model\Post;
use App\Model\Product;
use App\Model\User;

require_once __DIR__ . '/../vendor/autoload.php';

$uri = $_SERVER['REQUEST_URI'];

$pages = [
    '/home' => new SiteController,
    '/sample' => new SampleController
];

$controller = $pages[$uri] ?? new NotFoundController;

$controller->render();

?>