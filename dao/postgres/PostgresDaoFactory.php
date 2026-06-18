<?php
include_once(__DIR__ . '/../DaoFactory.php');
include_once(__DIR__ . '/PostgresUsuarioDao.php');
include_once(__DIR__ . '/PostgresEnderecoDao.php');
include_once(__DIR__ . '/PostgresClienteDao.php');
include_once(__DIR__ . '/PostgresFornecedorDao.php');
include_once(__DIR__ . '/PostgresProdutoDao.php');
include_once(__DIR__ . '/PostgresEstoqueDao.php');
include_once(__DIR__ . '/PostgresPedidoDao.php');

class PostgresDaoFactory extends DaoFactory {

    private $host = "localhost";
    private $db_name = "UCSVendas";
    private $port = "5432";
    private $username = "postgres";
    private $password = "0501";
    public $conn;

    public function getConnection(){
        if ($this->conn === null) {
            try{
                $this->conn = new PDO("pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch(PDOException $exception){
                throw new Exception("Erro ao conectar ao banco de dados: " . $exception->getMessage());
            }
        }
        return $this->conn;
    }

    public function getUsuarioDao() {
        return new PostgresUsuarioDao($this->getConnection());
    }
    
    public function getEnderecoDao() {
        return new PostgresEnderecoDao($this->getConnection());
    }

    public function getClienteDao() {
        return new PostgresClienteDao($this->getConnection());
    }

    public function getFornecedorDao() {
        return new PostgresFornecedorDao($this->getConnection());
    }

    public function getProdutoDao() {
        return new PostgresProdutoDao($this->getConnection());
    }

    public function getEstoqueDao() {
        return new PostgresEstoqueDao($this->getConnection());
    }

    public function getPedidoDao() {
        return new PostgresPedidoDao($this->getConnection());
    }
}
?>