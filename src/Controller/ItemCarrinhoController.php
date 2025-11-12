<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\ItemCarrinho;
use App\Model\Vela;
use App\Model\Usuario;

class ItemCarrinhoController
{
    public function add()
    {
        session_start();
        $idUsuario = $_SESSION['id_usuario'];
        $nomeUsuario = $_SESSION['nome_usuario'];
        $idVela = $_POST['id_vela'];

        $em = Database::getEntityManager();
        $usuario = $em->find(Usuario::class, $idUsuario);
        $vela = $em->find(Vela::class, $idVela);

        $itemRepo = $em->getRepository(ItemCarrinho::class);
        $itemNoCarrinho = $itemRepo->findOneBy(['usuario' => $usuario, 'vela' => $vela]);

        if ($itemNoCarrinho) {
            $em->remove($itemNoCarrinho); // remove do carrinho
        } else {
            $carrinho = new ItemCarrinho($usuario, $vela);
            $em->persist($carrinho); // coloca no carrinho
        }

        $em->flush();
        header("Location: " . $_SERVER['HTTP_REFERER']); // volta pra pagina anterior
        exit;
    }

    public function listar()
    {
        session_start();
        $idUsuario = $_SESSION['id_usuario'];
        $nomeUsuario = $_SESSION['nome_usuario'];

        $em = Database::getEntityManager();

        // buscar itens
        $itemRepo = $em->getRepository(ItemCarrinho::class);
        $usuario = $em->find(Usuario::class, $idUsuario);
        $itensDoUsuario = $itemRepo->findBy(['usuario' => $usuario]);

        $itensCarrinho = [];
        foreach ($itensDoUsuario as $item) {
            $itensCarrinho[] = $item->getVela();
        }

        $page = 'carrinho';
        require __DIR__ . '/../View/page.phtml';
    }
}
