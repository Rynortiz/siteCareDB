<?php
namespace App\Model;

use App\Core\Database;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "velas")]
class Vela
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 120)]
    private string $nome;

    #[ORM\Column(type: "string", length: 120)]
    private string $aroma;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private float $preco;

    #[ORM\Column(type: "integer")]
    private int $estoque = 0;

    #[ORM\Column(type: "string", length: 255)]
    private string $imagem;

    #[ORM\Column(type: "string", enumType: VelaStatus::class)]
    private VelaStatus $status;

    public function __construct(string $nome, string $aroma, float $preco, int $estoque, string $imagem, VelaStatus $status)
    {
        $this->nome = $nome;
        $this->aroma = $aroma;
        $this->preco = $preco;
        $this->estoque = $estoque;
        $this->imagem = $imagem;
        $this->status = $status;
    }

    // Getters e Setters
    public function getId(): int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function setNome(string $nome): void { $this->nome = $nome; }

    public function getAroma(): string { return $this->aroma; }
    public function setAroma(string $aroma): void { $this->aroma = $aroma; }

    public function getPreco(): float { return $this->preco; }
    public function setPreco(float $preco): void { $this->preco = $preco; }

    public function getImagem(): string { return $this->imagem; }
    public function setImagem(string $imagem): void { $this->imagem = $imagem; }

    public function getEstoque(): int { return $this->estoque; }
    public function setEstoque(int $estoque): void { $this->estoque = $estoque; }

    public function getStatus(): VelaStatus { return $this->status; }
    public function setStatus(VelaStatus $status): void { $this->status = $status; }

    public function save(): void
    {
        $em = Database::getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public static function findAll(): array
    {
        $em = Database::getEntityManager();
        $repository = $em->getRepository(Vela::class);
        return $repository->findAll();
    }
    
}

