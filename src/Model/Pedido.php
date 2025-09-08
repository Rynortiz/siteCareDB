<?php

namespace App\Model;

use DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use App\Core\Database;

#[Entity]

class Pedido
{
    #[Column, Id, GeneratedValue]
    private int $id;

    #[ManyToOne(targetEntity: User::class)]
    private User $usuario;

    #[Column]
    private \DateTime $data;

    #[Column]
    private string $status;

    public function __construct(User $usuario, string $status = "PENDENTE")
    {
        $this->usuario = $usuario;
        $this->data = new \DateTime();
        $this->status = $status;
    }

    public function getId(): int { return $this->id; }
    public function getUsuario(): User { return $this->usuario; }
    public function getData(): \DateTime { return $this->data; }
    public function getStatus(): string { return $this->status; }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function save(): void
    {
        $em = Database::getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public static function findAll(): array
    {
        $em = Database::getEntityManager();
        $repository = $em->getRepository(Pedido::class);
        return $repository->findAll();
    }
}
