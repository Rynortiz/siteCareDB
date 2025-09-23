<?php
namespace App\Controller;

class GaleriaController
{
    public function render(): void
    {
        $page = 'galeria';
        include __DIR__ . '/../View/page.phtml';
    }

}