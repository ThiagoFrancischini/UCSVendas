<?php
class Produto {
    private $id;
    private $nome;
    private $descricao;
    private $foto;
    private $fornecedor_id;

    public function __construct($id = null, $nome = "", $descricao = "", $foto = null, $fornecedor_id = null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->foto = $foto;
        $this->fornecedor_id = $fornecedor_id;
    }
}
?>