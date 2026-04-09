<?php
class Fornecedor {
    private $id;
    private $nome;
    private $descricao;
    private $telefone;
    private $usuario_id;
    private $endereco_id;

    public function __construct($id = null, $nome = "", $descricao = "", $telefone = "", $usuario_id = null, $endereco_id = null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->telefone = $telefone;
        $this->usuario_id = $usuario_id;
        $this->endereco_id = $endereco_id;
    }    
}
?>