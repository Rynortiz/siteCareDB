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
        session_start();
        if (empty($_SESSION['id_usuario'])) {
            header("Location: /login");
            exit;
        }
    }

    public function listar()
    {
        $this->requireLogin();
        $idUsuario = $_SESSION['id_usuario'];
        $nomeUsuario = $_SESSION['nome_usuario'];

        $em = Database::getEntityManager();
        $usuario = $em->find(Usuario::class, $_SESSION['id_usuario']);

        $itens = $em->getRepository(CarrinhoItem::class)->findBy(['usuario' => $usuario]);

        $page = 'carrinho';
        require __DIR__ . '/../View/page.phtml';
    }

    public function add()
    {
        $this->requireLogin();
        session_start();
        

        $idVela = (int)($_POST['id_vela'] ?? 0);
        if (!$idVela) {
            $_SESSION['erro'] = "Produto inválido.";
            header("Location: /produto");
            exit;
        }

        $em = Database::getEntityManager();
        $vela = $em->find(Vela::class, $idVela);

        // checagens: existe, status e estoque
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

        if ($item) {
            // se já existe, incrementa verificando estoque
            $novaQtd = $item->getQuantidade() + 1;
            if ($novaQtd > $vela->getEstoque()) {
                $_SESSION['erro'] = "Quantidade solicitada maior que o estoque.";
            } else {
                $item->setQuantidade($novaQtd);
                $em->flush();
            }
        } else {
            $novo = new CarrinhoItem($usuario, $vela, 1);
            $em->persist($novo);
            $em->flush();
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // endpoint AJAX para atualizar quantidade (+/-)
    public function atualizarQuantidade()
    {
        $this->requireLogin();
        session_start();

        // espera JSON com { id_item, acao: 'mais'|'menos' }
        $input = json_decode(file_get_contents('php://input'), true);
        $idItem = (int)($input['id_item'] ?? 0);
        $acao = $input['acao'] ?? '';

        header('Content-Type: application/json');

        if (!$idItem || !in_array($acao, ['mais','menos'])) {
            echo json_encode(['ok'=>false,'msg'=>'Requisição inválida']);
            exit;
        }

        $em = Database::getEntityManager();
        $item = $em->find(CarrinhoItem::class, $idItem);

        if (!$item) {
            echo json_encode(['ok'=>false,'msg'=>'Item não encontrado']);
            exit;
        }

        $vela = $item->getVela();

        if ($vela->getStatus()->value !== VelaStatus::DISPONIVEL->value) {
            echo json_encode(['ok'=>false,'msg'=>'Produto indisponível']);
            exit;
        }

        if ($acao === 'mais') {
            if ($item->getQuantidade() + 1 > $vela->getEstoque()) {
                echo json_encode(['ok'=>false,'msg'=>'Estoque insuficiente']);
                exit;
            }
            $item->setQuantidade($item->getQuantidade() + 1);
        } else {
            // menos
            $q = $item->getQuantidade() - 1;
            if ($q <= 0) {
                // remover item
                $em->remove($item);
                $em->flush();
                echo json_encode(['ok'=>true,'msg'=>'Item removido','removido'=>true]);
                exit;
            }
            $item->setQuantidade($q);
        }

        $em->flush();
        echo json_encode(['ok'=>true]);
        exit;
    }

    public function remover()
    {
        $this->requireLogin();
        session_start();

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
        session_start();

        $em = Database::getEntityManager();
        $conn = $em->getConnection(); // DBAL connection
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
