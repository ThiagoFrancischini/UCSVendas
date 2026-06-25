<?php
header('Content-Type: application/json');
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'ADMIN') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id   = (int)($data['fornecedor_id'] ?? 0);
if (!$id) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido.']);
    exit;
}

include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');
$factory = new PostgresDaoFactory();
$dao     = $factory->getFornecedorDao();

$fornecedor = $dao->buscaPorId($id);
if (!$fornecedor) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Fornecedor não encontrado.']);
    exit;
}

$conn = $factory->getConnection();

// Verifica se algum produto do fornecedor aparece em pedidos
$stmtCheck = $conn->prepare("
    SELECT COUNT(*) FROM item_pedido ip
    INNER JOIN estoque e ON e.id = ip.estoque_id
    INNER JOIN produto p ON p.id = e.produto_id
    WHERE p.fornecedor_id = ?
");
$stmtCheck->execute([$id]);
if ((int)$stmtCheck->fetchColumn() > 0) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Não é possível excluir: este fornecedor possui produtos que constam em pedidos registrados.']);
    exit;
}

try {
    $dao->remove($fornecedor);
    echo json_encode(['sucesso' => true]);
} catch (Throwable $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Não é possível excluir: este fornecedor possui produtos que constam em pedidos registrados.']);
}
