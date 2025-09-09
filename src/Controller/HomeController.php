<?php
namespace App\Controller;

class HomeController
{
    public function render(): void
    {
        $page = 'home';
        include __DIR__ . '/../View/page.phtml';
    }

    public function create(): void
    {
        echo "Aqui vamos cadastrar no banco";
    }

}