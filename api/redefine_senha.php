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

$data     = json_decode(file_get_contents('php://input'), true);
$id       = (int)($data['usuario_id'] ?? 0);
$novaSenha = trim($data['nova_senha'] ?? '');

if (!$id || strlen($novaSenha) < 6) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido ou senha muito curta (mínimo 6 caracteres).']);
    exit;
}

include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');
$factory = new PostgresDaoFactory();
$conn    = $factory->getConnection();

$stmt = $conn->prepare("SELECT id FROM usuario WHERE id = ?");
$stmt->execute([$id]);
if (!$stmt->fetch()) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não encontrado.']);
    exit;
}

$hash = password_hash($novaSenha, PASSWORD_DEFAULT);
$upd  = $conn->prepare("UPDATE usuario SET senha = ? WHERE id = ?");
$upd->execute([$hash, $id]);

echo json_encode(['sucesso' => true, 'mensagem' => 'Senha redefinida com sucesso.']);
