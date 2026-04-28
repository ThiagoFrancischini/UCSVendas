<?php
class Cliente {
    private $id;
    private $nome;
    private $telefone;
    private $cartaoCredito;
    private $usuario_id;
    private $endereco_id;

    public function __construct($id = null, $nome = "", $telefone = "", $cartaoCredito = "", $usuario_id = null, $endereco_id = null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->telefone = $telefone;
        $this->cartaoCredito = $cartaoCredito;
        $this->usuario_id = $usuario_id;
        $this->endereco_id = $endereco_id;
    }

    public function getId() { return $this->id; }
    public function getNome() { return $this->nome; }
    public function getTelefone() { return $this->telefone; }
    public function getCartaoCredito() { return $this->cartaoCredito; }
    public function getUsuarioId() { return $this->usuario_id; }
    public function getEnderecoId() { return $this->endereco_id; }

    public function setId($id) { $this->id = $id; }
    public function setNome($nome) { $this->nome = $nome; }
    public function setTelefone($telefone) { $this->telefone = $telefone; }
    public function setCartaoCredito($cartaoCredito) { $this->cartaoCredito = $cartaoCredito; }
    public function setUsuarioId($usuario_id) { $this->usuario_id = $usuario_id; }
    public function setEnderecoId($endereco_id) { $this->endereco_id = $endereco_id; }
}
?>