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

try {
    $ok = $dao->remove($fornecedor);
    echo json_encode(['sucesso' => $ok]);
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}
