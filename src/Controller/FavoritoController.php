<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\Favorito;
use App\Model\Vela;
use App\Model\Usuario;

class FavoritoController
{
    public function toggle()
    {
        session_start();
        $idUsuario = $_SESSION['id_usuario'];
        $nomeUsuario = $_SESSION['nome_usuario'];
        $idVela = $_POST['id_vela'];

        $em = Database::getEntityManager();
        $usuario = $em->find(Usuario::class, $idUsuario);
        $vela = $em->find(Vela::class, $idVela);

        $favoritoRepo = $em->getRepository(Favorito::class);
        $favoritoExistente = $favoritoRepo->findOneBy(['usuario' => $usuario, 'vela' => $vela]);

        if ($favoritoExistente) {
            $em->remove($favoritoExistente); // remover
        } else {
            $favorito = new Favorito($usuario, $vela);
            $em->persist($favorito); // adicionar
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

        // Buscar favoritos do usuário
        $favoritoRepo = $em->getRepository(Favorito::class);
        // buscar itens
        $usuario = $em->find(Usuario::class, $idUsuario);
        $favoritosUsuario = $favoritoRepo->findBy(['usuario' => $usuario]);

        $velasFavoritas = [];
        foreach ($favoritosUsuario as $fav) {
            $velasFavoritas[] = $fav->getVela();

        }
        $page = 'favorito';
        require __DIR__ . '/../View/page.phtml';
    }
}