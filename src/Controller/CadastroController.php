<?php
namespace App\Controller;

use App\Core\Database;
use App\Model\Usuario;
use App\Model\TipoUsuario;

class CadastroController
{

    public function render(): void
    {
        $page = 'cadastro';
        require __DIR__ . '/../View/page.phtml';
    }

    public function cadastrar()
    {

        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        if (empty($nome) || empty($email) || empty($senha)) {
            $_SESSION['erro_login'] = "Preencha todos os campos!";
            
            header("Location: /cadastro");
            exit;
        }

        $em = Database::getEntityManager();

        // verificação de email
        $repo = $em->getRepository(Usuario::class);
        $usuarioExistente = $repo->findOneBy(['email' => $email]);

        if ($usuarioExistente) {
            $_SESSION['erro_login'] = "E-mail já cadastrado!";
            header("Location: /cadastro");
            exit;
        }

        // cria usuario

        $usuario = new Usuario(
            $nome,
            $email,
            password_hash($senha, CRYPT_SHA512),
            TipoUsuario::CLIENTE
        );
        
        $usuario->save();

        // loga dps de criar conta
        $_SESSION['id_usuario'] = $usuario->getId();
        $_SESSION['nome_usuario'] = $usuario->getNome();
        $_SESSION['tipo_usuario'] = $usuario->getTipo()->name;

        header("Location: /home");
        exit;
    }
}
