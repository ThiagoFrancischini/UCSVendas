<?php
include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');
include_once(__DIR__ . '/../models/Pedido.php');
include_once(__DIR__ . '/../models/ItemPedido.php');

class PedidoController {
    private $factory;

    public function __construct() {
        $this->factory = new PostgresDaoFactory();
    }

    /**
     * Finaliza o carrinho da sessão como um pedido.
     * Valida estoque, deduz quantidades, cria pedido e itens dentro de uma transação.
     * Retorna o id do pedido criado.
     */
    public function finalizarPedido($cliente_id, $carrinho) {
        if (empty($carrinho)) {
            throw new Exception("Carrinho vazio.");
        }

        $estoqueDao = $this->factory->getEstoqueDao();
        $pedidoDao  = $this->factory->getPedidoDao();
        $conn       = $this->factory->getConnection();

        // Pré-validação de estoque (fora da transação para mensagem clara)
        foreach ($carrinho as $item) {
            $estoques = $estoqueDao->buscaPorProdutoId($item['produto_id']);
            $disponivel = array_sum(array_map(fn($e) => $e->getQuantidade(), $estoques));
            if ($item['quantidade'] > $disponivel) {
                throw new Exception("Estoque insuficiente para '{$item['nome']}'. Disponível: $disponivel.");
            }
        }

        $conn->beginTransaction();
        try {
            $valorTotal = 0;
            foreach ($carrinho as $item) {
                $valorTotal += $item['preco'] * $item['quantidade'];
            }

            $pedido = new Pedido(null, $cliente_id, null, 'PENDENTE', null, null, $valorTotal);
            $pedidoId = $pedidoDao->inserePedido($pedido);

            foreach ($carrinho as $item) {
                // Busca o primeiro estoque com quantidade disponível
                $estoques = $estoqueDao->buscaPorProdutoId($item['produto_id']);
                $qtdRestante = $item['quantidade'];

                foreach ($estoques as $estoque) {
                    if ($qtdRestante <= 0) break;
                    $deduzir = min($qtdRestante, $estoque->getQuantidade());

                    // Insere item do pedido referenciando este lote de estoque
                    $itemPedido = new ItemPedido(null, $pedidoId, $estoque->getId(), $deduzir, $item['preco']);
                    $pedidoDao->insereItemPedido($itemPedido);

                    // Deduz estoque
                    $estoque->setQuantidade($estoque->getQuantidade() - $deduzir);
                    $estoqueDao->altera($estoque);

                    $qtdRestante -= $deduzir;
                }
            }

            $conn->commit();
            return $pedidoId;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function buscarPedidoPorId($id) {
        return $this->factory->getPedidoDao()->buscaPorId($id);
    }

    public function listarTodos($pagina = 1, $porPagina = 10) {
        return $this->factory->getPedidoDao()->listarTodos($pagina, $porPagina);
    }

    public function contarTodos() {
        return $this->factory->getPedidoDao()->contarTodos();
    }

    public function buscarPorNomeCliente($nome, $pagina = 1, $porPagina = 10) {
        return $this->factory->getPedidoDao()->buscaPorNomeCliente($nome, $pagina, $porPagina);
    }

    public function contarPorNomeCliente($nome) {
        return $this->factory->getPedidoDao()->contarPorNomeCliente($nome);
    }

    public function buscarItensPedido($pedidoId, $pagina = 1, $porPagina = 10) {
        return $this->factory->getPedidoDao()->buscaItensPorPedidoId($pedidoId, $pagina, $porPagina);
    }

    public function contarItensPedido($pedidoId) {
        return $this->factory->getPedidoDao()->contarItensPorPedidoId($pedidoId);
    }

    public function buscarPedidosCliente($cliente_id) {
        return $this->factory->getPedidoDao()->buscaPedidosPorClienteId($cliente_id);
    }

    public function alterarStatus($pedidoId, $novoStatus) {
        $pedidoDao = $this->factory->getPedidoDao();
        $pedido = $pedidoDao->buscaPorId($pedidoId);
        if (!$pedido) {
            throw new Exception("Pedido não encontrado.");
        }

        $pedido->setStatus($novoStatus);

        if ($novoStatus === 'ENVIADO') {
            $pedido->setDataEnvio(date('Y-m-d H:i:s'));
        } elseif ($novoStatus === 'CANCELADO') {
            $pedido->setDataCancelamento(date('Y-m-d H:i:s'));
        }

        return $pedidoDao->alteraStatus($pedido);
    }
}
