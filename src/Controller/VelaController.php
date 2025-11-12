<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\Vela;
use App\Model\ItemCarrinho;
use App\Model\VelaStatus;
use App\Model\Usuario;

class VelaController
{
    public function listar()
    {
        session_start();
        $idUsuario = $_SESSION['id_usuario'] ?? null;
        $nomeUsuario = $_SESSION['nome_usuario'] ?? 'Seja bem-vindo!';

        $em = Database::getEntityManager();

        $velas = $em->getRepository(Vela::class)->findBy(['status' => VelaStatus::DISPONIVEL]);

        $itens = [];

        if ($idUsuario) {
            $carrinhoRepo = $em->getRepository(ItemCarrinho::class);
            $usuario = $em->find(Usuario::class, $idUsuario);
            $carrinhoUsuario = $carrinhoRepo->findBy(['usuario' => $usuario]);
            foreach ($carrinhoUsuario as $car) {
                $itens[] = $car->getVela()->getId();
            }
        }

        $page = 'galeria';
        require __DIR__ . '/../View/page.phtml';
    }
}
