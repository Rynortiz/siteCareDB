<?php
namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "carrinho_itens")]
class CarrinhoItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Carrinho::class)]
    #[ORM\JoinColumn(nullable:false)]
    private Carrinho $carrinho;

    #[ORM\ManyToOne(targetEntity: Vela::class)]
    #[ORM\JoinColumn(nullable:false)]
    private Vela $vela;

    #[ORM\Column(type:"integer")]
    private int $quantidade;

    public function __construct(Carrinho $carrinho, Vela $vela, int $quantidade = 1)
    {
        $this->carrinho = $carrinho;
        $this->vela = $vela;
        $this->quantidade = $quantidade;
    }

    // GETTERS
    public function getId(): int { return $this->id; }
    public function getCarrinho(): Carrinho { return $this->carrinho; }
    public function getVela(): Vela { return $this->vela; }

    public function getQuantidade(): int { return $this->quantidade; }
    public function setQuantidade(int $q): void { $this->quantidade = $q; }
}
