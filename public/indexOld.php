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
    '/user' => new UserController,
    '/sample' => new SampleController
];

$controller = $pages[$uri] ?? new NotFoundController;

$controller->render();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Velas Care</title>

    <base href="http://<?= $base ?>">


    <link rel="shortcut icon" href="images/careMLogo.png">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poiret+One&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/aos.css">
    <link rel="stylesheet" href="css/styles.css">

</head>

<?php
include 'templates/header.php';
?>

<main>
    <?php
    
    $pagina = $_GET["param"] ?? "home";
    $pagina = "paginas/{$pagina}.php";

    if (file_exists($pagina)) {
        include $pagina;
    } else {
        include "paginas/erro.php";
    }


    ?>



</main>


<?php include './templates/footer.php'; ?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="script.js"></script>
<script src="js/jquery-1.11.3.min.js"></script>
<script src="js/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
    crossorigin="anonymous"></script>