<?php
include_once(__DIR__ . '/../ProdutoDao.php');
include_once(__DIR__ . '/../DAO.php');
include_once(__DIR__ . '/../../models/Produto.php');

class PostgresProdutoDao extends DAO implements ProdutoDao {

    private $table_name = 'produto';

    public function __construct($conn) {
        parent::__construct($conn);
    }

    public function insere($produto) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome, descricao, foto, fornecedor_id) 
                  VALUES (:nome, :descricao, :foto, :fornecedor_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":nome", $produto->getNome());
        $stmt->bindValue(":descricao", $produto->getDescricao());
        $stmt->bindValue(":foto", $produto->getFoto());
        $stmt->bindValue(":fornecedor_id", $produto->getFornecedorId());

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return -1;
    }

    public function remove($produto) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $produto->getId());

        if($stmt->execute()) {
            return true;
        }    
        return false;
    }

    public function altera($produto) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nome = :nome, descricao = :descricao, foto = :foto 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":nome", $produto->getNome());
        $stmt->bindValue(":descricao", $produto->getDescricao());
        $stmt->bindValue(":foto", $produto->getFoto());
        $stmt->bindValue(':id', $produto->getId());

        if($stmt->execute()) {
            return true;
        }    
        return false;
    }

    public function buscaPorId($id) {
        $produto = null;
        $query = "SELECT id, nome, descricao, foto, fornecedor_id 
                  FROM " . $this->table_name . " WHERE id = ? LIMIT 1 OFFSET 0";
     
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $id);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $produto = new Produto($row['id'], $row['nome'], $row['descricao'], 
                                   $row['foto'], $row['fornecedor_id']);
        } 
        return $produto;
    }

    public function buscaPorFornecedorId($fornecedor_id) {
        $produtos = array();
        $query = "SELECT id, nome, descricao, foto, fornecedor_id 
                  FROM " . $this->table_name . " WHERE fornecedor_id = ? ORDER BY id ASC";
     
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $fornecedor_id);
        $stmt->execute();
     
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $produto = new Produto($row['id'], $row['nome'], $row['descricao'], 
                                   $row['foto'], $row['fornecedor_id']);
            array_push($produtos, $produto);
        }
        return $produtos;
    }

    public function buscaTodos() {
        $produtos = array();
        $query = "SELECT id, nome, descricao, foto, fornecedor_id 
                  FROM " . $this->table_name . " ORDER BY id ASC";
     
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
     
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $produto = new Produto($row['id'], $row['nome'], $row['descricao'], 
                                   $row['foto'], $row['fornecedor_id']);
            array_push($produtos, $produto);
        }
        return $produtos;
    }
}
?>