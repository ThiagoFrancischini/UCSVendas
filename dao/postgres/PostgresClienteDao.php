<?php
include_once(__DIR__ . '/../ClienteDao.php');
include_once(__DIR__ . '/../DAO.php');
include_once(__DIR__ . '/../../models/Cliente.php');

class PostgresClienteDao extends DAO implements ClienteDao {

    private $table_name = 'cliente';

    public function __construct($conn) {
        parent::__construct($conn);
    }

    public function insere($cliente) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome, telefone, cartao_credito, usuario_id, endereco_id) 
                  VALUES (:nome, :telefone, :cartao_credito, :usuario_id, :endereco_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":nome", $cliente->getNome());
        $stmt->bindValue(":telefone", $cliente->getTelefone());
        $stmt->bindValue(":cartao_credito", $cliente->getCartaoCredito());
        $stmt->bindValue(":usuario_id", $cliente->getUsuarioId());
        $stmt->bindValue(":endereco_id", $cliente->getEnderecoId());

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return -1;
    }

    public function remove($cliente) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $cliente->getId());

        if($stmt->execute()) {
            return true;
        }    
        return false;
    }

    public function altera($cliente) {
        $query = "UPDATE " . $this->table_name . " 
                  SET nome = :nome, telefone = :telefone, cartao_credito = :cartao_credito 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":nome", $cliente->getNome());
        $stmt->bindValue(":telefone", $cliente->getTelefone());
        $stmt->bindValue(":cartao_credito", $cliente->getCartaoCredito());
        $stmt->bindValue(':id', $cliente->getId());

        if($stmt->execute()) {
            return true;
        }    
        return false;
    }

    public function buscaPorId($id) {
        $cliente = null;
        $query = "SELECT id, nome, telefone, cartao_credito, usuario_id, endereco_id 
                  FROM " . $this->table_name . " WHERE id = ? LIMIT 1 OFFSET 0";
     
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $id);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $cliente = new Cliente($row['id'], $row['nome'], $row['telefone'], 
                                   $row['cartao_credito'], $row['usuario_id'], $row['endereco_id']);
        } 
        return $cliente;
    }

    public function buscaPorUsuarioId($usuario_id) {
        $cliente = null;
        $query = "SELECT id, nome, telefone, cartao_credito, usuario_id, endereco_id 
                  FROM " . $this->table_name . " WHERE usuario_id = ? LIMIT 1 OFFSET 0";
     
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $usuario_id);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $cliente = new Cliente($row['id'], $row['nome'], $row['telefone'], 
                                   $row['cartao_credito'], $row['usuario_id'], $row['endereco_id']);
        } 
        return $cliente;
    }

    public function buscaTodos() {
        $clientes = array();
        $query = "SELECT id, nome, telefone, cartao_credito, usuario_id, endereco_id 
                  FROM " . $this->table_name . " ORDER BY id ASC";
     
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
     
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $cliente = new Cliente($row['id'], $row['nome'], $row['telefone'], 
                                   $row['cartao_credito'], $row['usuario_id'], $row['endereco_id']);
            array_push($clientes, $cliente);
        }
        return $clientes;
    }
}
?>