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

        $em = Database::getEntityManager();

        // Buscar velas disponíveis
        $velas = $em->getRepository(Vela::class)->findBy(['status' => VelaStatus::DISPONIVEL]);

        // Buscar favoritos do usuário
        $favoritos = [];

        if ($idUsuario) {
            $favoritoRepo = $em->getRepository(Favorito::class);
            $usuario = $em->find(Usuario::class, $idUsuario);
            $favoritosUsuario = $favoritoRepo->findBy(['usuario' => $usuario]);
            foreach ($favoritosUsuario as $fav) {
                $favoritos[] = $fav->getVela()->getId();
            }
        }

        // Define qual página será injetada em page.phtml
        $page = 'galeria';

        // Inclui o layout principal que carrega a view certa
        require __DIR__ . '/../View/page.phtml';
    }
}
