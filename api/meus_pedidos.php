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

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'CLIENTE') {
    respondeJson(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
}

include_once(__DIR__ . '/../controllers/PedidoController.php');
$controller = new PedidoController();
$pedidos = $controller->buscarPedidosCliente($_SESSION['cliente_id']);

$resultado = array_map(function($p) {
    return [
        'id'                => $p->getId(),
        'data_pedido'       => $p->getDataPedido(),
        'status'            => $p->getStatus(),
        'data_envio'        => $p->getDataEnvio(),
        'data_cancelamento' => $p->getDataCancelamento(),
        'valor_total'       => number_format($p->getValorTotal(), 2, ',', '.'),
    ];
}, $pedidos);

respondeJson(['sucesso' => true, 'pedidos' => $resultado]);
