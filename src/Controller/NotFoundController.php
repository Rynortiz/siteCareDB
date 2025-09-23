<?php
namespace App\Controller;

class NotFoundController
{
    function render() : void 
    {
        $page = 'erro';
        include __DIR__ . '/../View/page.phtml';   
    }
}