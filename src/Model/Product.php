<?php

namespace App\Model;

use App\Core\Database;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

#[Entity]

class Produto
{
    #[Column, Id, GeneratedValue]
    private int $id;

    #[Column]
    private string $nome;

    #[Column]
    private string $descricao;

    #[Column]
    private float $preco;

    #[Column]
    private string $categoria;

    public function __construct(string $nome, string $descricao, float $preco, string $categoria)
    {
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->preco = $preco;
        $this->categoria = $categoria;
    }

    public function getId(): int { return $this->id; }
    public function getNome(): string { return $this->nome; }
    public function getDescricao(): string { return $this->descricao; }
    public function getPreco(): float { return $this->preco; }
    public function getCategoria(): string { return $this->categoria; }

    public function save(): void
    {
        $em = Database::getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public static function findAll(): array
    {
        $em = Database::getEntityManager();
        $repository = $em->getRepository(Produto::class);
        return $repository->findAll();
    }
}
