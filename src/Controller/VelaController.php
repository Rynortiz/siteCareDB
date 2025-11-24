<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\Vela;
use App\Model\Favorito;
use App\Model\VelaStatus;
use App\Model\Usuario;
use App\Model\Carrinho;

class VelaController
{
    public function listar()
    {
        $idUsuario = $_SESSION['id_usuario'] ?? null;
        $em = Database::getEntityManager();

        // Buscar velas disponíveis
        $velas = $em->getRepository(Vela::class)->findBy([
            'status' => VelaStatus::DISPONIVEL
        ]);

        // Filtrar estoque > 0
        $velas = array_filter($velas, fn($v) => $v->getEstoque() > 0);

        $favoritos = [];
        $itensCarrinho = [];

        if ($idUsuario) {

            // Buscar usuário
            $usuario = $em->find(Usuario::class, $idUsuario);

            // Buscar ou criar carrinho
            $carrinho = $em->getRepository(Carrinho::class)
                           ->findOneBy(['usuario' => $usuario]);

            if ($carrinho) {
                // extrair ids das velas do carrinho
                foreach ($carrinho->getItens() as $item) {
                    $itensCarrinho[] = $item->getVela()->getId();
                }
            }

            // buscar favoritos
            $repoFavorito = $em->getRepository(Favorito::class);
            foreach ($repoFavorito->findBy(['usuario' => $usuario]) as $fav) {
                $favoritos[] = $fav->getVela()->getId();
            }
        }

        $page = 'produto';
        require __DIR__ . '/../View/page.phtml';
    }
}
