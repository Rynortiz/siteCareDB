<?php
namespace App\Model;

use App\Core\Database;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "venda_itens")]
class VendaItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Venda::class)]
    #[ORM\JoinColumn(name:"venda_id", nullable:false)]
    private Venda $venda;

    #[ORM\ManyToOne(targetEntity: Vela::class)]
    #[ORM\JoinColumn(name:"vela_id", nullable:false)]
    private Vela $vela;

    #[ORM\Column(type:"integer")]
    private int $quantidade;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
    private float $precoUnitario;

    public function __construct(Venda $venda, Vela $vela, int $quantidade, float $precoUnitario)
    {
        $this->venda = $venda;
        $this->vela = $vela;
        $this->quantidade = $quantidade;
        $this->precoUnitario = $precoUnitario;
    }

    public function getId(): int { return $this->id; }
    public function getVenda(): Venda { return $this->venda; }
    public function getVela(): Vela { return $this->vela; }
    public function getQuantidade(): int { return $this->quantidade; }
    public function getPrecoUnitario(): float { return $this->precoUnitario; }
}
