<?php
declare(strict_types=1);

require_once __DIR__ .'/../vendor/autoload.php';

use App\Core\Database;
use App\Model\Usuario;
use App\Model\Vela;
use App\Model\TipoUsuario;
use App\Model\VelaStatus;

$em = Database::getEntityManager();

$usuario = new Usuario("Teste", "teste@email.com", password_hash("123456", PASSWORD_DEFAULT), TipoUsuario::CLIENTE);
$em->persist($usuario);

$usuario = new Usuario("Admin", "admin@email.com", password_hash("123456", PASSWORD_DEFAULT), TipoUsuario::ADMIN);
$em->persist($usuario);

$vela1 = new Vela("Vela de Lavanda", "Lavanda", 29.90, 20,"images/careVela2.jpeg", VelaStatus::DISPONIVEL);
$vela2 = new Vela("Vela de Canela", "Canela", 34.90, 20,"images/careVela4.jpg", VelaStatus::DISPONIVEL);
$vela3 = new Vela("Vela de Hortelã", "Hortelã", 24.50, 20,"images/careVela.jpg", VelaStatus::INDISPONIVEL);
$vela4 = new Vela("Kit 3 Velas", "Mix de aromas", 89.90, 20, "images/kit1.jpg", VelaStatus::DISPONIVEL);

$em->persist($vela1);
$em->persist($vela2);
$em->persist($vela3);
$em->persist($vela4);

$em->flush();

echo "Usuário e velas criados com sucesso!\n";