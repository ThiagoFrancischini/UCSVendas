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
}
?>