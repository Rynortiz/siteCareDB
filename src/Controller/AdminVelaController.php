<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\Vela;
use App\Model\VelaStatus;

class AdminVelaController
{
    private function checkAdmin(): void
    {
        session_start();

        if (empty($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'ADMIN') {
            header("Location: /login");
            exit;
        }
    }

    public function listar(): void
    {
        $this->checkAdmin();
        $idUsuario = $_SESSION['id_usuario'];
        $nomeUsuario = $_SESSION['nome_usuario'];
        $em = Database::getEntityManager();
        $velas = $em->getRepository(Vela::class)->findAll();
        $page = 'admin_velas';
        require __DIR__ . '/../View/page.phtml';
    }

    public function criar(): void
    {
        $this->checkAdmin();
        $nome = $_POST['nome'] ?? '';
        $aroma = $_POST['aroma'] ?? '';
        $preco = $_POST['preco'] ?? '';
        $imagem = $_POST['imagem'] ?? '';
        $status = $_POST['status'] ?? 'DISPONIVEL';

        if ($nome && $aroma && $preco && $imagem) {
            $vela = new Vela($nome, $aroma, (float)$preco, $imagem, VelaStatus::from($status));
            $vela->save();

            header("Location: /admin");
            exit;
        }

    }

    public function editar(): void
    {
        $this->checkAdmin();
        $id = $_POST['id'] ?? null;
        if (!$id) {
            header("Location: /admin");
            exit;
        }

        $em = Database::getEntityManager();
        $vela = $em->find(Vela::class, $id);

        if ($vela) {
            $vela->setNome($_POST['nome'] ?? $vela->getNome());
            $vela->setAroma($_POST['aroma'] ?? $vela->getAroma());
            $vela->setPreco((float)($_POST['preco'] ?? $vela->getPreco()));
            $vela->setImagem($_POST['imagem'] ?? $vela->getImagem());
            $vela->setStatus(VelaStatus::from($_POST['status'] ?? $vela->getStatus()->value));

            $em->flush();
            header("Location: /admin");
            exit;
        }

    }

    public function remover(): void
    {
        $this->checkAdmin();
        $id = $_POST['id'] ?? null;
        if ($id) {
            $em = Database::getEntityManager();
            $vela = $em->find(Vela::class, $id);
            if ($vela) {
                $em->remove($vela);
                $em->flush();
            }
            
            header("Location: /admin");
            exit;
        }

    }
}
