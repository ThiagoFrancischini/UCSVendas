<?php
ini_set('display_errors', 0);
ob_start();

if (!defined('BASE_URL')) require_once(__DIR__ . '/../config.php');

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
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondeJson(['sucesso' => false, 'mensagem' => 'Método inválido.']);
}

$input     = json_decode(file_get_contents('php://input'), true);
$pedidoIds = array_filter(array_map('intval', (array)($input['pedido_ids'] ?? [])));

if (empty($pedidoIds)) {
    respondeJson(['sucesso' => false, 'mensagem' => 'Nenhum pedido informado.']);
}

include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');
$factory = new PostgresDaoFactory();
$conn    = $factory->getConnection();

$clienteId = (int)$_SESSION['cliente_id'];
foreach ($pedidoIds as $pedidoId) {
    $stmt = $conn->prepare("
        SELECT id FROM pedido
        WHERE id = ? AND cliente_id = ? AND status = 'AGUARDANDO_PAGAMENTO'
    ");
    $stmt->execute([$pedidoId, $clienteId]);
    if (!$stmt->fetch()) {
        respondeJson(['sucesso' => false, 'mensagem' => "Pedido #$pedidoId não encontrado ou já processado."]);
    }
}

$placeholders = implode(',', array_fill(0, count($pedidoIds), '?'));
$upd = $conn->prepare("UPDATE pedido SET status = 'PENDENTE' WHERE id IN ($placeholders)");
$upd->execute(array_values($pedidoIds));

respondeJson(['sucesso' => true]);
