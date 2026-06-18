<?php
header('Content-Type: application/json');
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'FORNECEDOR') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id   = (int)($data['usuario_id'] ?? 0);
if (!$id) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido.']);
    exit;
}

// Não deixar excluir o próprio usuário logado
if ($id === (int)$_SESSION['usuario_id']) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Você não pode excluir sua própria conta.']);
    exit;
}

include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');
$factory = new PostgresDaoFactory();
$dao     = $factory->getUsuarioDao();

$usuario = $dao->buscaPorId($id);
if (!$usuario) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não encontrado.']);
    exit;
}

try {
    $ok = $dao->remove($usuario);
    echo json_encode(['sucesso' => $ok]);
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}
