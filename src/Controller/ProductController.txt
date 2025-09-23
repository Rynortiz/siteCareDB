<?php
namespace App\Controller;

use App\Model\Product;
use App\Model\Produto;

class ProductController
{
    public function render(): void
    {
        $page = 'product';
        $products = Product::findAll();

        include __DIR__ . '/../View/page.phtml';

    }

    public function create(): void
    {
        if(!$_POST) return;

        $product = new Product(
            nome: $_POST['nome'] ?? '',
            descricao: $_POST['descricao'] ?? '',
            preco: (float) ($_POST['preco'] ?? 0),
            categoria: $_POST['categoria'] ?? ''
        );

        $product->save();

    }
}