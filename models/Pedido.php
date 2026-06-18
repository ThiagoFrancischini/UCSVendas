<?php
class Pedido {
    private $id;
    private $cliente_id;
    private $data_pedido;
    private $status;
    private $data_envio;
    private $data_cancelamento;
    private $valor_total;

    public function __construct($id = null, $cliente_id = null, $data_pedido = null, $status = 'PENDENTE', $data_envio = null, $data_cancelamento = null, $valor_total = 0) {
        $this->id = $id;
        $this->cliente_id = $cliente_id;
        $this->data_pedido = $data_pedido;
        $this->status = $status;
        $this->data_envio = $data_envio;
        $this->data_cancelamento = $data_cancelamento;
        $this->valor_total = $valor_total;
    }

    public function getId() { return $this->id; }
    public function getClienteId() { return $this->cliente_id; }
    public function getDataPedido() { return $this->data_pedido; }
    public function getStatus() { return $this->status; }
    public function getDataEnvio() { return $this->data_envio; }
    public function getDataCancelamento() { return $this->data_cancelamento; }
    public function getValorTotal() { return $this->valor_total; }

    public function setId($id) { $this->id = $id; }
    public function setClienteId($cliente_id) { $this->cliente_id = $cliente_id; }
    public function setDataPedido($data_pedido) { $this->data_pedido = $data_pedido; }
    public function setStatus($status) { $this->status = $status; }
    public function setDataEnvio($data_envio) { $this->data_envio = $data_envio; }
    public function setDataCancelamento($data_cancelamento) { $this->data_cancelamento = $data_cancelamento; }
    public function setValorTotal($valor_total) { $this->valor_total = $valor_total; }
}
