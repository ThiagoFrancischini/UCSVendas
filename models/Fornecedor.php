<?php
class Fornecedor {
    private $id;
    private $nome;
    private $descricao;
    private $telefone;
    private $cnpj;
    private $usuario_id;
    private $endereco_id;

    public function __construct($id = null, $nome = "", $descricao = "", $telefone = "", $cnpj = "", $usuario_id = null, $endereco_id = null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->telefone = $telefone;
        $this->cnpj = $cnpj;
        $this->usuario_id = $usuario_id;
        $this->endereco_id = $endereco_id;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function getTelefone() {
        return $this->telefone;
    }

    public function getCnpj() {
        return $this->cnpj;
    }

    public function getUsuarioId() {
        return $this->usuario_id;
    }

    public function getEnderecoId() {
        return $this->endereco_id;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    public function setCnpj($cnpj) {
        $this->cnpj = $cnpj;
    }

    public function setUsuarioId($usuario_id) {
        $this->usuario_id = $usuario_id;
    }

    public function setEnderecoId($endereco_id) {
        $this->endereco_id = $endereco_id;
    }
}
?>