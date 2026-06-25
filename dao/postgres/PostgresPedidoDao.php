<?php
include_once(__DIR__ . '/../PedidoDao.php');
include_once(__DIR__ . '/../DAO.php');
include_once(__DIR__ . '/../../models/Pedido.php');
include_once(__DIR__ . '/../../models/ItemPedido.php');

class PostgresPedidoDao extends DAO implements PedidoDao {

    public function __construct($conn) {
        parent::__construct($conn);
    }

    public function inserePedido($pedido) {
        $query = "INSERT INTO pedido (cliente_id, status, valor_total)
                  VALUES (:cliente_id, :status, :valor_total) RETURNING id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cliente_id', $pedido->getClienteId());
        $stmt->bindValue(':status', $pedido->getStatus());
        $stmt->bindValue(':valor_total', $pedido->getValorTotal());
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['id'];
    }

    public function insereItemPedido($item) {
        $query = "INSERT INTO item_pedido (pedido_id, estoque_id, quantidade, valor_unitario)
                  VALUES (:pedido_id, :estoque_id, :quantidade, :valor_unitario) RETURNING id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pedido_id', $item->getPedidoId());
        $stmt->bindValue(':estoque_id', $item->getEstoqueId());
        $stmt->bindValue(':quantidade', $item->getQuantidade());
        $stmt->bindValue(':valor_unitario', $item->getValorUnitario());
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['id'];
    }

    public function buscaPorId($id) {
        $query = "SELECT p.*, c.nome AS cliente_nome
                  FROM pedido p
                  JOIN cliente c ON c.id = p.cliente_id
                  WHERE p.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        return $this->rowToPedido($row);
    }

    public function buscaPorNumero($numero) {
        return $this->buscaPorId($numero);
    }

    public function buscaPorNomeCliente($nome, $pagina = 1, $porPagina = 10) {
        $offset = ($pagina - 1) * $porPagina;
        $query = "SELECT p.*, c.nome AS cliente_nome
                  FROM pedido p
                  JOIN cliente c ON c.id = p.cliente_id
                  WHERE LOWER(c.nome) LIKE LOWER(:nome)
                  ORDER BY p.id DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $pedidos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = $this->rowToPedido($row);
        }
        return $pedidos;
    }

    public function contarPorNomeCliente($nome) {
        $query = "SELECT COUNT(*) FROM pedido p JOIN cliente c ON c.id = p.cliente_id WHERE LOWER(c.nome) LIKE LOWER(:nome)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function listarTodos($pagina = 1, $porPagina = 10) {
        $offset = ($pagina - 1) * $porPagina;
        $query = "SELECT p.*, c.nome AS cliente_nome
                  FROM pedido p
                  JOIN cliente c ON c.id = p.cliente_id
                  ORDER BY p.id DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $pedidos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = $this->rowToPedido($row);
        }
        return $pedidos;
    }

    public function contarTodos() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM pedido");
        return (int)$stmt->fetchColumn();
    }

    public function listarPorFornecedor($fornecedor_id, $pagina = 1, $porPagina = 10) {
        $offset = ($pagina - 1) * $porPagina;
        $query = "SELECT DISTINCT p.*, c.nome AS cliente_nome
                  FROM pedido p
                  JOIN cliente c ON c.id = p.cliente_id
                  JOIN item_pedido ip ON ip.pedido_id = p.id
                  JOIN estoque e ON e.id = ip.estoque_id
                  JOIN produto pr ON pr.id = e.produto_id
                  WHERE pr.fornecedor_id = :fornecedor_id
                  ORDER BY p.id DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':fornecedor_id', $fornecedor_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $pedidos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = $this->rowToPedido($row);
        }
        return $pedidos;
    }

    public function contarPorFornecedor($fornecedor_id) {
        $query = "SELECT COUNT(DISTINCT p.id) FROM pedido p
                  JOIN item_pedido ip ON ip.pedido_id = p.id
                  JOIN estoque e ON e.id = ip.estoque_id
                  JOIN produto pr ON pr.id = e.produto_id
                  WHERE pr.fornecedor_id = :fornecedor_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':fornecedor_id', $fornecedor_id, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function buscaPorNomeClienteEFornecedor($nome, $fornecedor_id, $pagina = 1, $porPagina = 10) {
        $offset = ($pagina - 1) * $porPagina;
        $query = "SELECT DISTINCT p.*, c.nome AS cliente_nome
                  FROM pedido p
                  JOIN cliente c ON c.id = p.cliente_id
                  JOIN item_pedido ip ON ip.pedido_id = p.id
                  JOIN estoque e ON e.id = ip.estoque_id
                  JOIN produto pr ON pr.id = e.produto_id
                  WHERE pr.fornecedor_id = :fornecedor_id AND LOWER(c.nome) LIKE LOWER(:nome)
                  ORDER BY p.id DESC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':fornecedor_id', $fornecedor_id, PDO::PARAM_INT);
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $pedidos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = $this->rowToPedido($row);
        }
        return $pedidos;
    }

    public function contarPorNomeClienteEFornecedor($nome, $fornecedor_id) {
        $query = "SELECT COUNT(DISTINCT p.id) FROM pedido p
                  JOIN cliente c ON c.id = p.cliente_id
                  JOIN item_pedido ip ON ip.pedido_id = p.id
                  JOIN estoque e ON e.id = ip.estoque_id
                  JOIN produto pr ON pr.id = e.produto_id
                  WHERE pr.fornecedor_id = :fornecedor_id AND LOWER(c.nome) LIKE LOWER(:nome)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':fornecedor_id', $fornecedor_id, PDO::PARAM_INT);
        $stmt->bindValue(':nome', '%' . $nome . '%');
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function buscaItensPorPedidoIdEFornecedor($pedido_id, $fornecedor_id) {
        $query = "SELECT ip.*, e.produto_id, p.nome AS produto_nome, p.descricao AS produto_descricao, p.foto AS produto_foto, f.nome AS fornecedor_nome
                  FROM item_pedido ip
                  JOIN estoque e ON e.id = ip.estoque_id
                  JOIN produto p ON p.id = e.produto_id
                  JOIN fornecedor f ON f.id = p.fornecedor_id
                  WHERE ip.pedido_id = :pedido_id AND p.fornecedor_id = :fornecedor_id
                  ORDER BY ip.id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pedido_id', $pedido_id, PDO::PARAM_INT);
        $stmt->bindValue(':fornecedor_id', $fornecedor_id, PDO::PARAM_INT);
        $stmt->execute();
        $itens = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $itens[] = [
                'id'                => $row['id'],
                'pedido_id'         => $row['pedido_id'],
                'estoque_id'        => $row['estoque_id'],
                'produto_id'        => $row['produto_id'],
                'quantidade'        => $row['quantidade'],
                'valor_unitario'    => $row['valor_unitario'],
                'valor_total_item'  => $row['quantidade'] * $row['valor_unitario'],
                'produto_nome'      => $row['produto_nome'],
                'produto_descricao' => $row['produto_descricao'],
                'foto'              => $row['produto_foto'] ?: null,
                'fornecedor_nome'   => $row['fornecedor_nome'],
            ];
        }
        return $itens;
    }

    public function buscaItensPorPedidoId($pedido_id, $pagina = 1, $porPagina = 10) {
        $offset = ($pagina - 1) * $porPagina;
        $query = "SELECT ip.*, e.produto_id, p.nome AS produto_nome, p.descricao AS produto_descricao, p.foto AS produto_foto, f.nome AS fornecedor_nome
                  FROM item_pedido ip
                  JOIN estoque e ON e.id = ip.estoque_id
                  JOIN produto p ON p.id = e.produto_id
                  JOIN fornecedor f ON f.id = p.fornecedor_id
                  WHERE ip.pedido_id = :pedido_id
                  ORDER BY ip.id ASC
                  LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pedido_id', $pedido_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $itens = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $itens[] = [
                'id'               => $row['id'],
                'pedido_id'        => $row['pedido_id'],
                'estoque_id'       => $row['estoque_id'],
                'produto_id'       => $row['produto_id'],
                'quantidade'       => $row['quantidade'],
                'valor_unitario'   => $row['valor_unitario'],
                'valor_total_item' => $row['quantidade'] * $row['valor_unitario'],
                'produto_nome'     => $row['produto_nome'],
                'produto_descricao'=> $row['produto_descricao'],
                'foto'             => $row['produto_foto'] ?: null,
                'fornecedor_nome'  => $row['fornecedor_nome'],
            ];
        }
        return $itens;
    }

    public function contarItensPorPedidoId($pedido_id) {
        $query = "SELECT COUNT(*) FROM item_pedido WHERE pedido_id = :pedido_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':pedido_id', $pedido_id, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function buscaPedidosPorClienteId($cliente_id) {
        $query = "SELECT p.*, c.nome AS cliente_nome
                  FROM pedido p
                  JOIN cliente c ON c.id = p.cliente_id
                  WHERE p.cliente_id = :cliente_id
                  ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':cliente_id', $cliente_id, PDO::PARAM_INT);
        $stmt->execute();
        $pedidos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedidos[] = $this->rowToPedido($row);
        }
        return $pedidos;
    }

    public function alteraStatus($pedido) {
        $query = "UPDATE pedido SET status = :status, data_envio = :data_envio, data_cancelamento = :data_cancelamento WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':status', $pedido->getStatus());
        $stmt->bindValue(':data_envio', $pedido->getDataEnvio());
        $stmt->bindValue(':data_cancelamento', $pedido->getDataCancelamento());
        $stmt->bindValue(':id', $pedido->getId(), PDO::PARAM_INT);
        return $stmt->execute();
    }

    private function rowToPedido($row) {
        $pedido = new Pedido(
            $row['id'],
            $row['cliente_id'],
            $row['data_pedido'],
            $row['status'],
            $row['data_envio'] ?? null,
            $row['data_cancelamento'] ?? null,
            $row['valor_total']
        );
        // attach extra info
        $pedido->cliente_nome = $row['cliente_nome'] ?? null;
        return $pedido;
    }
}
