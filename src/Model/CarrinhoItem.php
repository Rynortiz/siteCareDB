<?php
namespace App\Model;

use App\Core\Database;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "carrinho_itens")]
class CarrinhoItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity:Usuario::class)]
    #[ORM\JoinColumn(nullable:false)]
    private Usuario $usuario;

    #[ORM\ManyToOne(targetEntity:Vela::class)]
    #[ORM\JoinColumn(nullable:false)]
    private Vela $vela;

    #[ORM\Column(type:"integer")]
    private int $quantidade;

    public function __construct(Usuario $usuario, Vela $vela, int $quantidade = 1)
    {
        $this->usuario = $usuario;
        $this->vela = $vela;
        $this->quantidade = $quantidade;
    }

    public function getId() { return $this->id; }
    public function getUsuario() { return $this->usuario; }
    public function getVela() { return $this->vela; }
    public function getQuantidade() { return $this->quantidade; }
    public function setQuantidade(int $q) { $this->quantidade = $q; }

    public function save() {
        $em = Database::getEntityManager();
        $em->persist($this);
        $em->flush();
    }
}
