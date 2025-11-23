<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\Vela;
use App\Model\Favorito;
use App\Model\VelaStatus;
use App\Model\Usuario;
use App\Model\CarrinhoItem;

class VelaController
{
    public function listar()
    {
        $idUsuario = $_SESSION['id_usuario'] ?? null;

        $em = Database::getEntityManager();

        $velas = $em->getRepository(Vela::class)->findBy(['status' => VelaStatus::DISPONIVEL]);
        $velas = array_filter($velas, fn($vela) => $vela->getEstoque() > 0);

        $favoritos = [];

        $itensCarrinho = [];

        if ($idUsuario) {
            // verificação de itens no carrinho
            $repoCarrinho = $em->getRepository(CarrinhoItem::class);
            $usuario = $em->find(Usuario::class, $idUsuario);

            foreach ($repoCarrinho->findBy(['usuario' => $usuario]) as $item) {
                $itensCarrinho[] = $item->getVela()->getId();
            }

            //verificação de itens nos favoritos
            $favoritoRepo = $em->getRepository(Favorito::class);
            $usuario = $em->find(Usuario::class, $idUsuario);
            
            foreach ($favoritoRepo->findBy(['usuario' => $usuario]) as $fav) {
                $favoritos[] = $fav->getVela()->getId();
            }
        }

        $page = 'produto';
        require __DIR__ . '/../View/page.phtml';
    }
}
