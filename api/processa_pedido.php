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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondeJson(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
}

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'CLIENTE') {
    respondeJson(['sucesso' => false, 'mensagem' => 'Você precisa estar logado como cliente para finalizar o pedido.', 'requer_login' => true]);
}

if (empty($_SESSION['carrinho'])) {
    respondeJson(['sucesso' => false, 'mensagem' => 'Seu carrinho está vazio.']);
}

include_once(__DIR__ . '/../controllers/PedidoController.php');

try {
    $controller = new PedidoController();
    $pedidoIds = $controller->finalizarPedido($_SESSION['cliente_id'], $_SESSION['carrinho']);
    $_SESSION['carrinho'] = [];
    $idsParam = implode(',', $pedidoIds);
    respondeJson(['sucesso' => true, 'pedido_ids' => $pedidoIds, 'redirecionar' => BASE_URL . '/views/store/pagamento.php?pedidos=' . $idsParam]);
} catch (Exception $e) {
    respondeJson(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}
