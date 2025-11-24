<?php
namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "carrinhos")]
class Carrinho
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity:Usuario::class)]
    #[ORM\JoinColumn(nullable:false)]
    private Usuario $usuario;

    #[ORM\OneToMany(
        mappedBy: "carrinho",
        targetEntity: CarrinhoItem::class,
        cascade: ["persist", "remove"]
    )]
    private $itens;

    #[ORM\Column(type:"datetime")]
    private \DateTime $criadoEm;

    public function __construct(Usuario $usuario)
    {
        $this->usuario = $usuario;
        $this->criadoEm = new \DateTime();
        $this->itens = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function addItem(CarrinhoItem $item)
    {
        $this->itens->add($item);
    }

    public function getItens() { return $this->itens; }
    public function getUsuario() { return $this->usuario; }
}
