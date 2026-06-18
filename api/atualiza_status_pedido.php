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

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'FORNECEDOR') {
    respondeJson(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
}

$input = json_decode(file_get_contents('php://input'), true);
$pedidoId   = intval($input['pedido_id'] ?? 0);
$novoStatus = strtoupper(trim($input['status'] ?? ''));

$statusPermitidos = ['PENDENTE', 'CONFIRMADO', 'ENVIADO', 'ENTREGUE', 'CANCELADO'];
if ($pedidoId <= 0 || !in_array($novoStatus, $statusPermitidos)) {
    respondeJson(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
}

include_once(__DIR__ . '/../controllers/PedidoController.php');
$controller = new PedidoController();

try {
    $controller->alterarStatus($pedidoId, $novoStatus);
    respondeJson(['sucesso' => true]);
} catch (Exception $e) {
    respondeJson(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}
