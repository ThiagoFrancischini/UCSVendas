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
    SELECT c.id, c.nome, c.telefone, c.cartao_credito,
           u.email, u.id AS usuario_id,
           e.rua, e.numero, e.complemento, e.bairro, e.cep, e.cidade, e.estado
    FROM cliente c
    JOIN usuario u ON u.id = c.usuario_id
    JOIN endereco e ON e.id = c.endereco_id
    WHERE c.id = ?
");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Cliente não encontrado.']);
    exit;
}

$totalPedidos = $conn->prepare("SELECT COUNT(*) FROM pedido WHERE cliente_id = ?");
$totalPedidos->execute([$id]);
$numPedidos = (int)$totalPedidos->fetchColumn();

echo json_encode([
    'sucesso'  => true,
    'cliente'  => [
        'id'             => $row['id'],
        'nome'           => $row['nome'],
        'telefone'       => $row['telefone'],
        'email'          => $row['email'],
        'usuario_id'     => $row['usuario_id'],
        'cartao_credito' => $row['cartao_credito'],
        'endereco'       => trim($row['rua'] . ', ' . $row['numero'] . ($row['complemento'] ? ' ' . $row['complemento'] : '')),
        'bairro'         => $row['bairro'],
        'cidade'         => $row['cidade'],
        'estado'         => $row['estado'],
        'cep'            => $row['cep'],
        'num_pedidos'    => $numPedidos,
    ]
]);
