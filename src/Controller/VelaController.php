<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\Vela;
use App\Model\Favorito;
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

        $favoritos = [];

        if ($idUsuario) {
            $favoritoRepo = $em->getRepository(Favorito::class);
            $usuario = $em->find(Usuario::class, $idUsuario);
            $favoritosUsuario = $favoritoRepo->findBy(['usuario' => $usuario]);
            foreach ($favoritosUsuario as $fav) {
                $favoritos[] = $fav->getVela()->getId();
            }
        }

        $page = 'galeria';
        require __DIR__ . '/../View/page.phtml';
    }
}
