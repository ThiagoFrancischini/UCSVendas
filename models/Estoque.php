<?php
class Estoque {
    private $id;
    private $quantidade;
    private $preco;
    private $preco_custo;
    private $lote;
    private $produto_id;

    public function __construct($id = null, $quantidade = 0, $preco = 0.00, $preco_custo = null, $lote = null, $produto_id = null) {
        $this->id = $id;
        $this->quantidade = $quantidade;
        $this->preco = $preco;
        $this->preco_custo = $preco_custo;
        $this->lote = $lote;
        $this->produto_id = $produto_id;
    }

    public function getId() { return $this->id; }
    public function getQuantidade() { return $this->quantidade; }
    public function getPreco() { return $this->preco; }
    public function getPrecoCusto() { return $this->preco_custo; }
    public function getLote() { return $this->lote; }
    public function getProdutoId() { return $this->produto_id; }

    public function setId($id) { $this->id = $id; }
    public function setQuantidade($quantidade) { $this->quantidade = $quantidade; }
    public function setPreco($preco) { $this->preco = $preco; }
    public function setPrecoCusto($preco_custo) { $this->preco_custo = $preco_custo; }
    public function setLote($lote) { $this->lote = $lote; }
    public function setProdutoId($produto_id) { $this->produto_id = $produto_id; }
}