<?php
namespace App\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "auditoria_precos")]
class AuditoriaPreco
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $vela_id;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private float $preco_antigo;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private float $preco_novo;

    #[ORM\Column(type: "datetime")]
    private \DateTime $alterado_em;

    #[ORM\Column(type: "integer")]
    private int $usuario_id;

    public function __construct(int $vela_id, float $preco_antigo, float $preco_novo, int $usuario_id) 
    {
        $this->vela_id = $vela_id;
        $this->preco_antigo = $preco_antigo;
        $this->preco_novo = $preco_novo;
        $this->usuario_id = $usuario_id;
        $this->alterado_em = new \DateTime();
    }

    // GETTERS
    public function getId(): int { return $this->id; }
    public function getVelaId(): int { return $this->vela_id; }
    public function getPrecoAntigo(): float { return $this->preco_antigo; }
    public function getPrecoNovo(): float { return $this->preco_novo; }
    public function getAlteradoEm(): \DateTime { return $this->alterado_em; }
    public function getUsuarioId(): int { return $this->usuario_id; }
}
