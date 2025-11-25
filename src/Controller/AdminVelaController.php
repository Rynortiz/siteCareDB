<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\Vela;
use App\Model\VelaStatus;
use App\Model\Usuario;

class AdminVelaController
{
    private function checkAdmin(): void
    {

        if (empty($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'ADMIN') {
            header("Location: /login");
            exit;
        }
    }

    public function listar(): void
    {
        $this->checkAdmin();
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
        $estoque = $_POST['estoque'] ?? 0;
        $imagem = $_POST['imagem'] ?? '';
        $status = $_POST['status'] ?? 'DISPONIVEL';

        if ($nome && $aroma && $preco && $estoque && $imagem) {
            $vela = new Vela($nome, $aroma, (float)$preco, (int)$estoque, $imagem, VelaStatus::from($status));
            $vela->save();

        }
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
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
            $vela->setEstoque((int)($_POST['estoque'] ?? $vela->getEstoque()));
            if ($vela->getImagem() !== $_POST['imagem'] && !empty($_POST['imagem'])) {

                $vela->setImagem($_POST['imagem']);

            }

            $vela->setStatus(VelaStatus::from($_POST['status'] ?? $vela->getStatus()->value));
            $usuario = $_SESSION['id_usuario'];
            $em->getConnection()->executeQuery("SET @usuario_id := :uid", ['uid' => $usuario->getId()]);
            $em->flush();
        }
        else 
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
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

        }
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
