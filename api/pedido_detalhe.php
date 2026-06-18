<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ob_start();

if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../config.php');
}

function respondeJson($data) {
    if (ob_get_length() > 0) ob_clean();
    if (!headers_sent()) header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['perfil'])) {
    respondeJson(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
}

$pedidoId  = intval($_GET['pedido_id'] ?? 0);
$pagina    = max(1, intval($_GET['pagina'] ?? 1));
$porPagina = intval($_GET['por_pagina'] ?? 10);

if ($pedidoId <= 0) {
    respondeJson(['sucesso' => false, 'mensagem' => 'ID do pedido inválido.']);
}

include_once(__DIR__ . '/../controllers/PedidoController.php');
$controller = new PedidoController();

// Verifica permissão: fornecedor vê tudo, cliente só vê os próprios
$pedido = $controller->buscarPedidoPorId($pedidoId);
if (!$pedido) {
    respondeJson(['sucesso' => false, 'mensagem' => 'Pedido não encontrado.']);
}

if ($_SESSION['perfil'] === 'CLIENTE' && $pedido->getClienteId() != $_SESSION['cliente_id']) {
    respondeJson(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
}

$itens  = $controller->buscarItensPedido($pedidoId, $pagina, $porPagina);
$total  = $controller->contarItensPedido($pedidoId);
$paginas = ceil($total / $porPagina);

respondeJson([
    'sucesso' => true,
    'itens'   => $itens,
    'total'   => $total,
    'paginas' => $paginas,
    'pagina'  => $pagina,
]);
