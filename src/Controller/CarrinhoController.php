<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\CarrinhoItem;
use App\Model\Carrinho;
use App\Model\Vela;
use App\Model\Usuario;
use App\Model\Venda;
use App\Model\VendaItem;
use App\Model\VelaStatus;
use App\Model\VendaStatus;

class CarrinhoController
{
    private function requireLogin()
    {
        if (empty($_SESSION['id_usuario'])) {
            header("Location: /login");
            exit;
        }
    }

    public function listar()
    {
        $this->requireLogin();

        $em = Database::getEntityManager();
        $usuario = $em->find(Usuario::class, $_SESSION['id_usuario']);

        // Buscar ou criar carrinho do usuário
        $carrinho = $em->getRepository(Carrinho::class)
            ->findOneBy(['usuario' => $usuario]);

        // Se não houver carrinho, cria
        if (!$carrinho) {
            $carrinho = new Carrinho($usuario);
            $em->persist($carrinho);
            $em->flush();
        }

        // Itens do carrinho
        $itensCarrinho = $carrinho->getItens();

        $page = 'carrinho';
        require __DIR__ . '/../View/page.phtml';
    }


    public function getOrCreateCarrinho(Usuario $usuario)
    {
        $em = Database::getEntityManager();
        $repo = $em->getRepository(Carrinho::class);

        // verifica se já existe carrinho ativo
        $carrinho = $repo->findOneBy(['usuario' => $usuario]);

        if ($carrinho) {
            return $carrinho;
        }

        // cria novo carrinho
        $carrinho = new Carrinho($usuario);
        $em->persist($carrinho);
        $em->flush();

        return $carrinho;
    }

    public function add()
    {
        $this->requireLogin();
        $em = Database::getEntityManager();

        $usuario = $em->find(Usuario::class, $_SESSION['id_usuario']);
        $velaId = $_POST['id'] ?? null;

        if (!$velaId) {
            $_SESSION['erro'] = "ID inválido.";
            header("Location: /produto");
            exit;
        }

        $vela = $em->find(Vela::class, $velaId);

        if (!$vela) {
            $_SESSION['erro'] = "Vela não encontrada.";
            header("Location: /produto");
            exit;
        }

        // pega ou cria o carrinho
        $carrinho = $this->getOrCreateCarrinho($usuario);

        // busca item dentro do carrinho
        $item = $em->getRepository(CarrinhoItem::class)->findOneBy([
            'carrinho' => $carrinho,
            'vela' => $vela
        ]);

        if ($item) {
            // verifica estoque antes de aumentar
            if ($item->getQuantidade() + 1 > $vela->getEstoque()) {
                $_SESSION['erro'] = "Quantidade maior que o estoque disponível.";
                header("Location: /carrinho");
                exit;
            }

            $item->setQuantidade($item->getQuantidade() + 1);
            $em->flush();
        } else {
            // novo item
            if ($vela->getEstoque() < 1) {
                $_SESSION['erro'] = "Produto sem estoque.";
                header("Location: /produto");
                exit;
            }

            $novo = new CarrinhoItem($carrinho, $vela, 1);
            $em->persist($novo);
            $em->flush();
        }

        $_SESSION['msg'] = "Item adicionado ao carrinho.";
        header("Location: /carrinho");
    }


    public function acrescentarItem()
    {
        $this->requireLogin();

        $idItem = (int)($_POST['id_item'] ?? 0);
        if (!$idItem) {
            header("Location: /carrinho");
            exit;
        }

        $em = Database::getEntityManager();
        $item = $em->find(CarrinhoItem::class, $idItem);

        if (!$item) {
            $_SESSION['erro'] = "Item não encontrado.";
            header("Location: /carrinho");
            exit;
        }

        $vela = $item->getVela();
        $qtdAtual = $item->getQuantidade();

        if ($qtdAtual >= $vela->getEstoque()) {
            $_SESSION['erro'] = "Estoque máximo atingido.";
            header("Location: /carrinho");
            exit;
        }

        $item->setQuantidade($qtdAtual + 1);
        $em->flush();

        header("Location: /carrinho");
        exit;
    }

    public function diminuirItem()
    {
        $this->requireLogin();

        $idItem = (int)($_POST['id_item'] ?? 0);
        if (!$idItem) {
            header("Location: /carrinho");
            exit;
        }

        $em = Database::getEntityManager();
        $item = $em->find(CarrinhoItem::class, $idItem);

        if (!$item) {
            header("Location: /carrinho");
            exit;
        }

        $qtdAtual = $item->getQuantidade();

        if ($qtdAtual <= 1) {
            // Remover item quando chegar a 0
            $em->remove($item);
            $em->flush();
            header("Location: /carrinho");
            exit;
        }

        $item->setQuantidade($qtdAtual - 1);
        $em->flush();

        header("Location: /carrinho");
        exit;
    }

    public function remover()
    {
        $this->requireLogin();

        $id = (int)($_POST['id_item'] ?? 0);
        if (!$id) {
            header("Location: /carrinho");
            exit;
        }

        $em = Database::getEntityManager();
        $item = $em->find(CarrinhoItem::class, $id);

        if ($item) {
            $em->remove($item);
            $em->flush();
        }

        header("Location: /carrinho");
        exit;
    }

    public function finalizar()
    {
        $this->requireLogin();

        $em = Database::getEntityManager();
        $conn = $em->getConnection();
        $usuario = $em->find(Usuario::class, $_SESSION['id_usuario']);

        // procura o carrinho
        $carrinho = $em->getRepository(Carrinho::class)->findOneBy([
            'usuario' => $usuario
        ]);

        if (!$carrinho) {
            $_SESSION['erro'] = "Carrinho não encontrado.";
            header("Location: /carrinho");
            exit;
        }

        // pega os itens
        $itens = $carrinho->getItens();

        if ($itens->isEmpty()) {
            $_SESSION['erro'] = "Seu carrinho está vazio.";
            header("Location: /carrinho");
            exit;
        }

        try {
            $conn->beginTransaction();

            // valida estoque
            foreach ($itens as $item) {
                $vela = $item->getVela();

                if ($vela->getStatus() !== VelaStatus::DISPONIVEL) {
                    throw new \Exception("Produto {$vela->getNome()} está indisponível.");
                }

                if ($item->getQuantidade() > $vela->getEstoque()) {
                    throw new \Exception("Estoque insuficiente para {$vela->getNome()}.");
                }
            }

            // calcula total da venda
            $total = 0.0;
            foreach ($itens as $item) {
                $total += $item->getQuantidade() * $item->getVela()->getPreco();
            }

            // criar venda
            $venda = new Venda($usuario, $total, VendaStatus::PROCESSANDO);
            $em->persist($venda);
            $em->flush(); // vende_id fica disponível

            // cria itens da venda e atualiza estoque
            foreach ($itens as $item) {
                $vela = $item->getVela();
                $qtd = $item->getQuantidade();
                $preco = $vela->getPreco();

                $vItem = new VendaItem($venda, $vela, $qtd, $preco);
                $em->persist($vItem);
                $conn->executeQuery("CALL processar_venda(?)", [$venda->getId()]);
                $status = $conn->fetchOne("SELECT status FROM vendas WHERE id = ?", [$venda->getId()]);




                // atualizar estoque
                if ($status == "finalizado") {
                    $vela->setEstoque($vela->getEstoque() - $qtd);
                    $em->persist($vela);
                } else {
                    $conn->rollBack();
                    $_SESSION['erro'] = "Erro ao finalizar venda ";
                    header("Location: /carrinho");
                }

                // remover o item do carrinho
                $em->remove($item);
            }

            // remover o carrinho vazio
            $em->remove($carrinho);


            $em->flush();
            $conn->commit();

            $_SESSION['msg'] = "Compra finalizada e sendo processada!";
            header("Location: /carrinho");
            exit;
        } catch (\Throwable $e) {
            if ($conn->isTransactionActive()) {
                $conn->rollBack();
            }

            $_SESSION['erro'] = "Erro ao finalizar venda: " . $e->getMessage();
            header("Location: /carrinho");
            exit;
        }
    }
}
