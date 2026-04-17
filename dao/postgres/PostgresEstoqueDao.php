<?php
include_once(__DIR__ . '/../EstoqueDao.php');
include_once(__DIR__ . '/../DAO.php');
// include_once(__DIR__ . '/../../models/Estoque.php'); // Modelo não existe

class PostgresEstoqueDao extends DAO implements EstoqueDao {

    private $table_name = 'estoque';

    public function __construct($conn) {
        parent::__construct($conn);
    }

    public function insere($estoque) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (quantidade, preco, produto_id) 
                  VALUES (:quantidade, :preco, :produto_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":quantidade", $estoque->getQuantidade());
        $stmt->bindValue(":preco", $estoque->getPreco());
        $stmt->bindValue(":produto_id", $estoque->getProdutoId());

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return -1;
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
                  SET quantidade = :quantidade, preco = :preco 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":quantidade", $estoque->getQuantidade());
        $stmt->bindValue(":preco", $estoque->getPreco());
        $stmt->bindValue(':id', $estoque->getId());

        if($stmt->execute()) {
            return true;
        }    
        return false;
    }

    public function buscaPorId($id) {
        $estoque = null;
        $query = "SELECT id, quantidade, preco, produto_id 
                  FROM " . $this->table_name . " WHERE id = ? LIMIT 1 OFFSET 0";
     
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $id);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $estoque = new Estoque($row['id'], $row['quantidade'], $row['preco'], $row['produto_id']);
        } 
        return $estoque;
    }

    public function buscaPorProdutoId($produto_id) {
        $estoque = null;
        $query = "SELECT id, quantidade, preco, produto_id 
                  FROM " . $this->table_name . " WHERE produto_id = ? LIMIT 1 OFFSET 0";
     
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $produto_id);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $estoque = new Estoque($row['id'], $row['quantidade'], $row['preco'], $row['produto_id']);
        } 
        return $estoque;
    }

    public function buscaTodos() {
        $estoques = array();
        $query = "SELECT id, quantidade, preco, produto_id 
                  FROM " . $this->table_name . " ORDER BY id ASC";
     
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
     
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $estoque = new Estoque($row['id'], $row['quantidade'], $row['preco'], $row['produto_id']);
            array_push($estoques, $estoque);
        }
        return $estoques;
    }
}
?>