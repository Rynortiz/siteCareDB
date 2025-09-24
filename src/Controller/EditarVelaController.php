<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\Vela;
use App\Model\VelaStatus;
use App\Model\Usuario;
use App\Model\TipoUsuario;

class EditarVelaController
{
    private function checkAdmin()
    {
        session_start();
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /login");
            exit;
        }

        $em = Database::getEntityManager();
        $usuario = $em->find(Usuario::class, $_SESSION['id_usuario']);

        if (!$usuario || $usuario->getTipo() !== TipoUsuario::ADMIN) {
            http_response_code(403);
            echo "Acesso negado. Somente administradores.";
            exit;
        }
    }

    public function render()
    {
        $this->checkAdmin();

        $em = Database::getEntityManager();
        $velas = $em->getRepository(Vela::class)->findAll();

        $page = 'admin';
        require __DIR__ . '/../View/page.phtml';
    }

    public function adicionar(): void
    {

        $this->checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $em = Database::getEntityManager();

            $vela = new Vela(
                $_POST['nome'],
                $_POST['aroma'],
                (float) $_POST['preco'],
                $_POST['imagem'],
                VelaStatus::from($_POST['status'])
            );

            $vela->save();

            header("Location: /admin/velas");
            exit;
        }

        require __DIR__ . '/../../views/adicionarVelas.phtml';
    }

    public function editar(int $id): void
    {

        $this->checkAdmin();

        $em = Database::getEntityManager();
        $vela = $em->find(Vela::class, $id);

        if (!$vela) {
            die("Vela não encontrada");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $vela->setNome($_POST['nome']);
            $vela->setAroma($_POST['aroma']);
            $vela->setPreco((float) $_POST['preco']);
            $vela->setImagem($_POST['imagem']);
            $vela->setStatus(VelaStatus::from($_POST['status']));

            $em->flush();

            header("Location: /admin");
            exit;
        }

        require __DIR__ . '/../../views/editarVelas.phtml';
    }

    public function remover(int $id): void
    {
        $this->checkAdmin();

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
