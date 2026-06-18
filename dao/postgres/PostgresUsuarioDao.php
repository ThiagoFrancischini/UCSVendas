<?php
include_once(__DIR__ . '/../UsuarioDao.php');
include_once(__DIR__ . '/../DAO.php');
include_once(__DIR__ . '/../../models/Usuario.php');

class PostgresUsuarioDao extends DAO implements UsuarioDao {

    private $table_name = 'usuario';

    public function __construct($conn) {
        parent::__construct($conn);
    }

    public function insere($usuario) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (email, senha, perfil) VALUES (:email, :senha, :perfil)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":email", $usuario->getEmail());
        $stmt->bindValue(":senha", $usuario->getSenha());
        $stmt->bindValue(":perfil", $usuario->getPerfil());

        if($stmt->execute()) {
            return $this->conn->lastInsertId(); 
        } else {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Erro ao inserir usuário: " . $errorInfo[2]);
        }
    }

    public function remove($usuario) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $usuario->getId());

        if($stmt->execute()) {
            return true;
        }    
        return false;
    }

    public function altera($usuario) {
        $query = "UPDATE " . $this->table_name . " 
                  SET email = :email, senha = :senha, perfil = :perfil 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindValue(":email", $usuario->getEmail());
        $stmt->bindValue(":senha", $usuario->getSenha());
        $stmt->bindValue(":perfil", $usuario->getPerfil());
        $stmt->bindValue(':id', $usuario->getId());

        if($stmt->execute()) {
            return true;
        }    
        return false;
    }

    public function buscaPorId($id) {
        $usuario = null;
        $query = "SELECT id, email, senha, perfil FROM " . $this->table_name . " WHERE id = ? LIMIT 1 OFFSET 0";
     
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $id);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $usuario = new Usuario($row['id'], $row['email'], $row['senha'], $row['perfil']);
        } 
        return $usuario;
    }

    public function buscaPorEmail($email) {
        $usuario = null;
        $query = "SELECT id, email, senha, perfil FROM " . $this->table_name . " WHERE email = ? LIMIT 1 OFFSET 0";
     
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $email);
        $stmt->execute();
     
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $usuario = new Usuario($row['id'], $row['email'], $row['senha'], $row['perfil']);
        } 
        return $usuario;
    }

    public function buscaTodos() {
        $usuarios = array();
        $query = "SELECT id, email, senha, perfil FROM " . $this->table_name . " ORDER BY id ASC";
     
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
     
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $usuario = new Usuario($row['id'], $row['email'], $row['senha'], $row['perfil']);
            array_push($usuarios, $usuario);
        }
        return $usuarios;
    }

    public function buscaPaginado($busca, $pagina, $porPagina) {
        $usuarios = array();
        $offset = ($pagina - 1) * $porPagina;
        $like = '%' . $busca . '%';
        $query = "SELECT id, email, senha, perfil FROM " . $this->table_name .
                 " WHERE email ILIKE :busca ORDER BY id ASC LIMIT :limite OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':busca', $like);
        $stmt->bindValue(':limite', (int)$porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario($row['id'], $row['email'], $row['senha'], $row['perfil']);
        }
        return $usuarios;
    }

    public function contar($busca) {
        $like = '%' . $busca . '%';
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE email ILIKE :busca";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':busca', $like);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function existePorEmail($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(1, $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
?>