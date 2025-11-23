<?php
namespace App\Model;

use App\Core\Database;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "vendas")]
class Venda
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: "usuario_id", nullable: false)]
    private Usuario $usuario;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private float $total;

    #[ORM\Column(type: "datetime", nullable: false)]
    private \DateTime $criadoEm;

    public function __construct(Usuario $usuario, float $total)
    {
        $this->usuario = $usuario;
        $this->total = $total;
        $this->criadoEm = new \DateTime();
    }

    public function getId(): int { return $this->id; }
    public function getUsuario(): Usuario { return $this->usuario; }
    public function getTotal(): float { return $this->total; }
    public function getCriadoEm(): \DateTime { return $this->criadoEm; }
}
