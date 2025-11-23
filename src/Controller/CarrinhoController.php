<?php

namespace App\Controller;

use App\Core\Database;
use App\Model\CarrinhoItem;
use App\Model\Vela;
use App\Model\Usuario;
use App\Model\Venda;
use App\Model\VendaItem;
use App\Model\VelaStatus;

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

        $itensCarrinho = [];

        $itens = $em->getRepository(CarrinhoItem::class)->findBy(['usuario' => $usuario]);

        $page = 'carrinho';
        require __DIR__ . '/../View/page.phtml';
    }

    public function add()
    {
        $this->requireLogin();


        $idVela = (int)($_POST['id_vela'] ?? 0);
        if (!$idVela) {
            $_SESSION['erro'] = "Produto inválido.";
            header("Location: /produto");
            exit;
        }

        $em = Database::getEntityManager();
        $vela = $em->find(Vela::class, $idVela);

        // verificação existe, status e estoque
        if (!$vela || $vela->getStatus()->value !== VelaStatus::DISPONIVEL->value) {
            $_SESSION['erro'] = "Produto indisponível.";
            header("Location: /produto");
            exit;
        }

        if ($vela->getEstoque() <= 0) {
            $_SESSION['erro'] = "Produto sem estoque.";
            header("Location: /produto");
            exit;
        }

        $usuario = $em->find(Usuario::class, $_SESSION['id_usuario']);

        // busca item do carrinho se existir
        $repo = $em->getRepository(CarrinhoItem::class);
        $item = $repo->findOneBy(['usuario' => $usuario, 'vela' => $vela]);

        if (!$item) {
            // se não existe, cria e adiciona no carrinho
            $novo = new CarrinhoItem($usuario, $vela, 1);
            $em->persist($novo);
            $em->flush();
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
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




    // endpoint AJAX para atualizar quantidade (+/-)

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

    // finalizar: usa transaction do DBAL (Doctrine connection)
    public function finalizar()
    {
        $this->requireLogin();

        $em = Database::getEntityManager();
        $conn = $em->getConnection();
        $usuario = $em->find(Usuario::class, $_SESSION['id_usuario']);

        $itens = $em->getRepository(CarrinhoItem::class)->findBy(['usuario' => $usuario]);

        if (empty($itens)) {
            $_SESSION['erro'] = "Seu carrinho está vazio.";
            header("Location: /carrinho");
            exit;
        }

        try {
            $conn->beginTransaction();

            // validações: estoque e status
            foreach ($itens as $item) {
                $vela = $item->getVela();

                if ($vela->getStatus()->value !== VelaStatus::DISPONIVEL->value) {
                    throw new \Exception("Produto {$vela->getNome()} está indisponível.");
                }

                if ($item->getQuantidade() > $vela->getEstoque()) {
                    throw new \Exception("Estoque insuficiente para {$vela->getNome()}.");
                }
            }

            // calcula total
            $total = 0.0;
            foreach ($itens as $item) {
                $total += $item->getQuantidade() * $item->getVela()->getPreco();
            }

            // cria entidade Venda (persist mas não commit ainda)
            $venda = new Venda($usuario, $total);
            $em->persist($venda);
            $em->flush(); // importante para ter id da venda antes de criar itens (mas a transação ainda não está commitada)

            // cria venda_itens e atualiza estoque
            foreach ($itens as $item) {
                $vela = $item->getVela();
                $quant = $item->getQuantidade();
                $precoUnit = $vela->getPreco();

                $vItem = new VendaItem($venda, $vela, $quant, $precoUnit);
                $em->persist($vItem);

                // desconta estoque
                $vela->setEstoque($vela->getEstoque() - $quant);
                $em->persist($vela);

                // remove item do carrinho
                $em->remove($item);
            }

            $em->flush();

            // commit da transação: só aqui a venda aparecerá no banco definitivamente
            $conn->commit();

            $_SESSION['msg'] = "Venda finalizada com sucesso!";
            header("Location: /carrinho");
            exit;
        } catch (\Throwable $e) {
            // rollback e mensagem de erro
            if ($conn->isTransactionActive()) {
                $conn->rollBack();
            }
            $_SESSION['erro'] = "Erro ao finalizar venda: " . $e->getMessage();
            header("Location: /carrinho");
            exit;
        }
    }
}
