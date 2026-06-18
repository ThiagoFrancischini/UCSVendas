<?php
interface PedidoDao {
    public function inserePedido($pedido);
    public function insereItemPedido($item);
    public function buscaPorId($id);
    public function buscaPorNumero($numero);
    public function buscaPorNomeCliente($nome, $pagina, $porPagina);
    public function contarPorNomeCliente($nome);
    public function listarTodos($pagina, $porPagina);
    public function contarTodos();
    public function buscaItensPorPedidoId($pedido_id, $pagina, $porPagina);
    public function contarItensPorPedidoId($pedido_id);
    public function buscaPedidosPorClienteId($cliente_id);
    public function alteraStatus($pedido);
}
