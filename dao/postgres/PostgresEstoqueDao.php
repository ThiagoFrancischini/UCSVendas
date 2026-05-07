<?php
include_once(__DIR__ . '/../EstoqueDao.php');
include_once(__DIR__ . '/../DAO.php');
include_once(__DIR__ . '/../../models/Estoque.php');

class PostgresEstoqueDao extends DAO implements EstoqueDao {

    private $table_name = 'estoque';

    public function __construct($conn) {
        parent::__construct($conn);
    }

    public function insere($estoque) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (quantidade, preco, preco_custo, lote, produto_id) 
                  VALUES (:quantidade, :preco, :preco_custo, :lote, :produto_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":quantidade", $estoque->getQuantidade());
        $stmt->bindValue(":preco", $estoque->getPreco());
        $stmt->bindValue(":preco_custo", $estoque->getPrecoCusto());
        $stmt->bindValue(":lote", $estoque->getLote());
        $stmt->bindValue(":produto_id", $estoque->getProdutoId());

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        } else {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Erro ao inserir estoque: " . $errorInfo[2]);
        }
    }

    public function remove($estoque) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $estoque->getId());

        if($stmt->execute()) {
            return true;
        }    
        return false;
    }

    public function altera($estoque) {
        $query = "UPDATE " . $this->table_name . " 
                  SET quantidade = :quantidade, preco = :preco, preco_custo = :preco_custo, lote = :lote 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":quantidade", $estoque->getQuantidade());
        $stmt->bindValue(":preco", $estoque->getPreco());
        $stmt->bindValue(":preco_custo", $estoque->getPrecoCusto());
        $stmt->bindValue(":lote", $estoque->getLote());
        $stmt->bindValue(':id', $estoque->getId());

        if($stmt->execute()) {
            return true;
        }    
        return false;
    }

    public function buscaPorId($id) {
        $estoque = null;
        $query = "SELECT id, quantidade, preco, preco_custo, lote, produto_id 
                  FROM " . $this->table_name . " WHERE id = ? LIMIT 1 OFFSET 0";
     
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $id);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $estoque = new Estoque($row['id'], $row['quantidade'], $row['preco'], $row['preco_custo'], $row['lote'], $row['produto_id']);
        } 
        return $estoque;
    }

    public function buscaPorProdutoId($produto_id) {
        $estoques = array();
        $query = "SELECT id, quantidade, preco, preco_custo, lote, produto_id 
                  FROM " . $this->table_name . " WHERE produto_id = ? ORDER BY id ASC";
     
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $produto_id);
        $stmt->execute();
     
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $estoque = new Estoque($row['id'], $row['quantidade'], $row['preco'], $row['preco_custo'], $row['lote'], $row['produto_id']);
            array_push($estoques, $estoque);
        }
        return $estoques;
    }

    public function buscaTodos() {
        $estoques = array();
        $query = "SELECT id, quantidade, preco, preco_custo, lote, produto_id 
                  FROM " . $this->table_name . " ORDER BY id ASC";
     
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
     
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $estoque = new Estoque($row['id'], $row['quantidade'], $row['preco'], $row['preco_custo'], $row['lote'], $row['produto_id']);
            array_push($estoques, $estoque);
        }
        return $estoques;
    }
}
?>