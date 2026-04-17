<?php
include_once(__DIR__ . '/../EnderecoDao.php');
include_once(__DIR__ . '/../DAO.php');
include_once(__DIR__ . '/../../models/Endereco.php');

class PostgresEnderecoDao extends DAO implements EnderecoDao {

    private $table_name = 'endereco';

    public function __construct($conn) {
        parent::__construct($conn);
    }

    public function insere($endereco) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (rua, numero, complemento, bairro, cep, cidade, estado) 
                  VALUES (:rua, :numero, :complemento, :bairro, :cep, :cidade, :estado)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":rua", $endereco->getRua());
        $stmt->bindValue(":numero", $endereco->getNumero());
        $stmt->bindValue(":complemento", $endereco->getComplemento());
        $stmt->bindValue(":bairro", $endereco->getBairro());
        $stmt->bindValue(":cep", $endereco->getCep());
        $stmt->bindValue(":cidade", $endereco->getCidade());
        $stmt->bindValue(":estado", $endereco->getEstado());

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        } else {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Erro ao inserir endereço: " . $errorInfo[2]);
        }
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":rua", $endereco->getRua());
        $stmt->bindValue(":numero", $endereco->getNumero());
        $stmt->bindValue(":complemento", $endereco->getComplemento());
        $stmt->bindValue(":bairro", $endereco->getBairro());
        $stmt->bindValue(":cep", $endereco->getCep());
        $stmt->bindValue(":cidade", $endereco->getCidade());
        $stmt->bindValue(":estado", $endereco->getEstado());
        $stmt->bindValue(':id', $endereco->getId());

        if($stmt->execute()) {
            return true;
        }    
        return false;
    }

    public function buscaPorId($id) {
        $endereco = null;
        $query = "SELECT id, rua, numero, complemento, bairro, cep, cidade, estado 
                  FROM " . $this->table_name . " WHERE id = ? LIMIT 1 OFFSET 0";
     
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $id);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $endereco = new Endereco($row['id'], $row['rua'], $row['numero'], $row['complemento'], 
                                     $row['bairro'], $row['cep'], $row['cidade'], $row['estado']);
        } 
        return $endereco;
    }

    public function buscaTodos() {
        $enderecos = array();
        $query = "SELECT id, rua, numero, complemento, bairro, cep, cidade, estado 
                  FROM " . $this->table_name . " ORDER BY id ASC";
     
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
     
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $endereco = new Endereco($row['id'], $row['rua'], $row['numero'], $row['complemento'], 
                                     $row['bairro'], $row['cep'], $row['cidade'], $row['estado']);
            array_push($enderecos, $endereco);
        }
        return $enderecos;
    }
}
?>