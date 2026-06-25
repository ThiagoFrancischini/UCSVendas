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
     * Finaliza o carrinho criando um pedido separado por fornecedor.
     * Retorna array de pedido_ids criados.
     */
    public function finalizarPedido($cliente_id, $carrinho) {
        if (empty($carrinho)) {
            throw new Exception("Carrinho vazio.");
        }

        $estoqueDao = $this->factory->getEstoqueDao();
        $produtoDao = $this->factory->getProdutoDao();
        $pedidoDao  = $this->factory->getPedidoDao();
        $conn       = $this->factory->getConnection();

        // Pré-validação de estoque
        foreach ($carrinho as $item) {
            $estoques = $estoqueDao->buscaPorProdutoId($item['produto_id']);
            $disponivel = array_sum(array_map(fn($e) => $e->getQuantidade(), $estoques));
            if ($item['quantidade'] > $disponivel) {
                throw new Exception("Estoque insuficiente para '{$item['nome']}'. Disponível: $disponivel.");
            }
        }

        // Agrupa itens do carrinho por fornecedor_id
        $grupos = [];
        foreach ($carrinho as $item) {
            $produto = $produtoDao->buscaPorId($item['produto_id']);
            $fid = $produto->getFornecedorId();
            $grupos[$fid][] = $item;
        }

        $conn->beginTransaction();
        try {
            $pedidoIds = [];

            foreach ($grupos as $fornecedorId => $itens) {
                // Calcula valor total deste grupo com preços reais dos lotes
                $valorTotal = 0;
                foreach ($itens as $item) {
                    $estoques = $estoqueDao->buscaPorProdutoId($item['produto_id']);
                    $qtdRestante = $item['quantidade'];
                    foreach ($estoques as $estoque) {
                        if ($qtdRestante <= 0) break;
                        $deduzir = min($qtdRestante, $estoque->getQuantidade());
                        $valorTotal += $deduzir * $estoque->getPreco();
                        $qtdRestante -= $deduzir;
                    }
                }

                $pedido = new Pedido(null, $cliente_id, null, 'AGUARDANDO_PAGAMENTO', null, null, $valorTotal);
                $pedidoId = $pedidoDao->inserePedido($pedido);
                $pedidoIds[] = $pedidoId;

                foreach ($itens as $item) {
                    $estoques = $estoqueDao->buscaPorProdutoId($item['produto_id']);
                    $qtdRestante = $item['quantidade'];
                    foreach ($estoques as $estoque) {
                        if ($qtdRestante <= 0) break;
                        $deduzir = min($qtdRestante, $estoque->getQuantidade());
                        $itemPedido = new ItemPedido(null, $pedidoId, $estoque->getId(), $deduzir, $estoque->getPreco());
                        $pedidoDao->insereItemPedido($itemPedido);
                        $estoque->setQuantidade($estoque->getQuantidade() - $deduzir);
                        $estoqueDao->altera($estoque);
                        $qtdRestante -= $deduzir;
                    }
                }
            }

            $conn->commit();
            return $pedidoIds;
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

    public function listarPorFornecedor($fornecedor_id, $pagina = 1, $porPagina = 10) {
        return $this->factory->getPedidoDao()->listarPorFornecedor($fornecedor_id, $pagina, $porPagina);
    }

    public function contarPorFornecedor($fornecedor_id) {
        return $this->factory->getPedidoDao()->contarPorFornecedor($fornecedor_id);
    }

    public function buscarPorNomeClienteEFornecedor($nome, $fornecedor_id, $pagina = 1, $porPagina = 10) {
        return $this->factory->getPedidoDao()->buscaPorNomeClienteEFornecedor($nome, $fornecedor_id, $pagina, $porPagina);
    }

    public function contarPorNomeClienteEFornecedor($nome, $fornecedor_id) {
        return $this->factory->getPedidoDao()->contarPorNomeClienteEFornecedor($nome, $fornecedor_id);
    }

    public function buscarItensPedido($pedidoId, $pagina = 1, $porPagina = 10) {
        return $this->factory->getPedidoDao()->buscaItensPorPedidoId($pedidoId, $pagina, $porPagina);
    }

    public function buscarItensPedidoPorFornecedor($pedidoId, $fornecedorId) {
        return $this->factory->getPedidoDao()->buscaItensPorPedidoIdEFornecedor($pedidoId, $fornecedorId);
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
