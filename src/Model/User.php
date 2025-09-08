<?php

namespace App\Model;

use App\Core\Database;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

#[Entity]

class User
{
    #[Column, Id, GeneratedValue]
    private int $id;

    #[Column]
    private string $name;

    #[Column]
    private string $email;

    #[Column]
    private string $password;

    #[Column]
    private string $role;

    public function __construct(string $name, string $email, string $password, string $role = "CLIENTE")
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = hash('sha256', $password);
        $this->role = $role;
    }

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getRole(): string { return $this->role; }

    public function validatePassword(string $password): bool
    {
        return $this->password == hash('sha256', $password);
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
        $repository = $em->getRepository(User::class);
        return $repository->findAll();
    }
}
