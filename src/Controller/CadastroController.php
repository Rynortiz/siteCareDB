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
        session_start();

        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        if (empty($nome) || empty($email) || empty($senha)) {
            $_SESSION['erro_login'] = "Preencha todos os campos!";
            header("Location: /cadastro");
            exit;
        }

        $em = Database::getEntityManager();

        // Verificar se já existe email
        $repo = $em->getRepository(Usuario::class);
        $usuarioExistente = $repo->findOneBy(['email' => $email]);

        if ($usuarioExistente) {
            $_SESSION['erro_login'] = "E-mail já cadastrado!";
            header("Location: /cadastro");
            exit;
        }

        // Criar usuário

        $usuario = new Usuario(
            $nome,
            $email,
            password_hash($senha, PASSWORD_BCRYPT),
            TipoUsuario::CLIENTE
        );
        
        $usuario->save();

        // Loga automaticamente após cadastro
        $_SESSION['id_usuario'] = $usuario->getId();
        $_SESSION['nome_usuario'] = $usuario->getNome();
        $_SESSION['tipo_usuario'] = $usuario->getTipo()->name;

        header("Location: /home");
        exit;
    }
}
