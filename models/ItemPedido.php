<?php
class ItemPedido {
    private $id;
    private $pedido_id;
    private $estoque_id;
    private $quantidade;
    private $valor_unitario;

    public function __construct($id = null, $pedido_id = null, $estoque_id = null, $quantidade = 0, $valor_unitario = 0) {
        $this->id = $id;
        $this->pedido_id = $pedido_id;
        $this->estoque_id = $estoque_id;
        $this->quantidade = $quantidade;
        $this->valor_unitario = $valor_unitario;
    }

    public function getId() { return $this->id; }
    public function getPedidoId() { return $this->pedido_id; }
    public function getEstoqueId() { return $this->estoque_id; }
    public function getQuantidade() { return $this->quantidade; }
    public function getValorUnitario() { return $this->valor_unitario; }

    public function setId($id) { $this->id = $id; }
    public function setPedidoId($pedido_id) { $this->pedido_id = $pedido_id; }
    public function setEstoqueId($estoque_id) { $this->estoque_id = $estoque_id; }
    public function setQuantidade($quantidade) { $this->quantidade = $quantidade; }
    public function setValorUnitario($valor_unitario) { $this->valor_unitario = $valor_unitario; }
}
