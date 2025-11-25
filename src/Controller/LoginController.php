<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\Usuario;

class LoginController
{
    public function render(): void
    {
        $page = 'login';
        require __DIR__ . '/../View/page.phtml';
    }

    public function autenticar(): void
    {

        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $em = Database::getEntityManager();
        $usuarioRepo = $em->getRepository(Usuario::class);

        $usuario = $usuarioRepo->findOneBy(['email' => $email]);

        if ($usuario && password_verify($senha, $usuario->getSenha())) {
            $_SESSION['id_usuario'] = $usuario->getId();
            $_SESSION['nome_usuario'] = $usuario->getNome();
            $_SESSION['tipo_usuario'] = $usuario->getTipo()->name;
            $em->getConnection()->executeQuery("SET @usuario_id := :uid", ['uid' => $usuario->getId()]);

            header("Location: /home");
            exit;
        } else {
            $_SESSION['erro_login'] = "E-mail ou senha inválidos.";
            header("Location: /login");
            exit;
        }
    }

    public function logout(): void
    {
        session_start();
        session_destroy();
        header("Location: /login");
        exit;
    }
}
