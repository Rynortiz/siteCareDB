<?php
namespace App\Model;

use App\Core\Database;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "itens_carrinho")]
class ItemCarrinho
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Usuario $usuario;

    #[ORM\ManyToOne(targetEntity: Vela::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Vela $vela;

    public function __construct(Usuario $usuario, Vela $vela)
    {
        $this->usuario = $usuario;
        $this->vela = $vela;
    }

    public function getUsuario(): Usuario { return $this->usuario; }
    public function getVela(): Vela { return $this->vela; }

    public function save(): void
    {
        $em = Database::getEntityManager();
        $em->persist($this);
        $em->flush();
    }

    public static function getClass() {
        
        $em = Database::getEntityManager();
        $repository = $em->getRepository(ItemCarrinho::class);

        return $repository->getClass();

    }

    public static function findAll(): array
    {
        $em = Database::getEntityManager();
        $repository = $em->getRepository(ItemCarrinho::class);
        return $repository->findAll();
    }

}
