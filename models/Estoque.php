<?php
class Estoque {
    private $id;
    private $quantidade;
    private $preco;
    private $produto_id;

    public function __construct($id = null, $quantidade = 0, $preco = 0.00, $produto_id = null) {
        $this->id = $id;
        $this->quantidade = $quantidade;
        $this->preco = $preco;
        $this->produto_id = $produto_id;
    }

    public function getId() { return $this->id; }
    public function getQuantidade() { return $this->quantidade; }
    public function getPreco() { return $this->preco; }
    public function getProdutoId() { return $this->produto_id; }

    public function setId($id) { $this->id = $id; }
    public function setQuantidade($quantidade) { $this->quantidade = $quantidade; }
    public function setPreco($preco) { $this->preco = $preco; }
    public function setProdutoId($produto_id) { $this->produto_id = $produto_id; }
}