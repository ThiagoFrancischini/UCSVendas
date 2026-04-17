<?php
include_once(__DIR__ . '/../dao/FornecedorDao.php');
include_once(__DIR__ . '/../DAO.php');
include_once(__DIR__ . '/../models/Fornecedor.php');

class PostgresFornecedorDao extends DAO implements FornecedorDao {

    private $table_name = 'fornecedor';

    public function insere($fornecedor) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome, descricao, telefone, usuario_id, endereco_id) 
                  VALUES (:nome, :descricao, :telefone, :usuario_id, :endereco_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":nome", $fornecedor->getNome());
        $stmt->bindValue(":descricao", $fornecedor->getDescricao());
        $stmt->bindValue(":telefone", $fornecedor->getTelefone());
        $stmt->bindValue(":usuario_id", $fornecedor->getUsuarioId()); // FK do Login
        $stmt->bindValue(":endereco_id", $fornecedor->getEnderecoId()); // FK do Endereco

        if($stmt->execute()) {
            return $this->conn->lastInsertId(); 
        }
        return -1;
    }

    public function remove($fornecedor) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $fornecedor->getId());

        if($stmt->execute()) {
            return true;
        }    
        return false;
    }

    public function altera($fornecedor) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nome = :nome, descricao = :descricao, telefone = :telefone 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":nome", $fornecedor->getNome());
        $stmt->bindValue(":descricao", $fornecedor->getDescricao());
        $stmt->bindValue(":telefone", $fornecedor->getTelefone());
        $stmt->bindValue(':id', $fornecedor->getId());

        if($stmt->execute()) {
            return true;
        }    
        return false;
    }

    public function buscaPorId($id) {
        $fornecedor = null;
        $query = "SELECT id, nome, descricao, telefone, usuario_id, endereco_id 
                  FROM " . $this->table_name . " WHERE id = ? LIMIT 1 OFFSET 0";
     
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $id);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $fornecedor = new Fornecedor($row['id'], $row['nome'], $row['descricao'], 
                                         $row['telefone'], $row['usuario_id'], $row['endereco_id']);
        } 
        return $fornecedor;
    }

    public function buscaPorUsuarioId($usuario_id) {
        $fornecedor = null;
        $query = "SELECT id, nome, descricao, telefone, usuario_id, endereco_id 
                  FROM " . $this->table_name . " WHERE usuario_id = ? LIMIT 1 OFFSET 0";
     
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $usuario_id);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $fornecedor = new Fornecedor($row['id'], $row['nome'], $row['descricao'], 
                                         $row['telefone'], $row['usuario_id'], $row['endereco_id']);
        } 
        return $fornecedor;
    }

    public function buscaTodos() {
        $fornecedores = array();
        $query = "SELECT id, nome, descricao, telefone, usuario_id, endereco_id 
                  FROM " . $this->table_name . " ORDER BY id ASC";
     
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
     
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $fornecedor = new Fornecedor($row['id'], $row['nome'], $row['descricao'], 
                                         $row['telefone'], $row['usuario_id'], $row['endereco_id']);
            array_push($fornecedores, $fornecedor);
        }
        return $fornecedores;
    }
}
?>