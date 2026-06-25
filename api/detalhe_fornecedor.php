<?php
header('Content-Type: application/json');
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'ADMIN') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido.']);
    exit;
}

include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');
$factory = new PostgresDaoFactory();
$conn    = $factory->getConnection();

$stmt = $conn->prepare("
    SELECT f.id, f.nome, f.descricao, f.telefone, f.cnpj,
           u.email, u.id AS usuario_id,
           e.rua, e.numero, e.complemento, e.bairro, e.cep, e.cidade, e.estado
    FROM fornecedor f
    JOIN usuario u ON u.id = f.usuario_id
    JOIN endereco e ON e.id = f.endereco_id
    WHERE f.id = ?
");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Fornecedor não encontrado.']);
    exit;
}

$totalProdutos = $conn->prepare("SELECT COUNT(*) FROM produto WHERE fornecedor_id = ?");
$totalProdutos->execute([$id]);
$numProdutos = (int)$totalProdutos->fetchColumn();

echo json_encode([
    'sucesso'    => true,
    'fornecedor' => [
        'id'          => $row['id'],
        'nome'        => $row['nome'],
        'descricao'   => $row['descricao'],
        'telefone'    => $row['telefone'],
        'cnpj'        => $row['cnpj'],
        'email'       => $row['email'],
        'usuario_id'  => $row['usuario_id'],
        'endereco'    => trim($row['rua'] . ', ' . $row['numero'] . ($row['complemento'] ? ' ' . $row['complemento'] : '')),
        'bairro'      => $row['bairro'],
        'cidade'      => $row['cidade'],
        'estado'      => $row['estado'],
        'cep'         => $row['cep'],
        'num_produtos' => $numProdutos,
    ]
]);
