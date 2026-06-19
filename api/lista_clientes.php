<?php
header('Content-Type: application/json');
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'ADMIN') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
    exit;
}
include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');

$busca  = trim($_GET['busca'] ?? '');
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$porPag = 10;

$factory = new PostgresDaoFactory();
$conn    = $factory->getConnection();

$like   = '%' . $busca . '%';
$offset = ($pagina - 1) * $porPag;

$stmtTotal = $conn->prepare(
    "SELECT COUNT(*) FROM cliente c JOIN usuario u ON u.id = c.usuario_id
     WHERE c.nome ILIKE :busca OR u.email ILIKE :busca2"
);
$stmtTotal->bindValue(':busca', $like);
$stmtTotal->bindValue(':busca2', $like);
$stmtTotal->execute();
$total   = (int)$stmtTotal->fetchColumn();
$paginas = max(1, (int)ceil($total / $porPag));
$pagina  = min($pagina, $paginas);
$offset  = ($pagina - 1) * $porPag;

$stmt = $conn->prepare(
    "SELECT c.id, c.nome, c.telefone, u.email, c.usuario_id
     FROM cliente c JOIN usuario u ON u.id = c.usuario_id
     WHERE c.nome ILIKE :busca OR u.email ILIKE :busca2
     ORDER BY c.id ASC LIMIT :limite OFFSET :offset"
);
$stmt->bindValue(':busca', $like);
$stmt->bindValue(':busca2', $like);
$stmt->bindValue(':limite', $porPag, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$lista = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $lista[] = [
        'id'         => $row['id'],
        'nome'       => $row['nome'],
        'telefone'   => $row['telefone'],
        'email'      => $row['email'],
        'usuario_id' => $row['usuario_id'],
    ];
}

echo json_encode(['sucesso' => true, 'clientes' => $lista, 'total' => $total, 'paginas' => $paginas, 'pagina' => $pagina]);
