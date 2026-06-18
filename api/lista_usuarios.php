<?php
header('Content-Type: application/json');
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'FORNECEDOR') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
    exit;
}
include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');

$busca  = trim($_GET['busca'] ?? '');
$pagina = max(1, (int)($_GET['pagina'] ?? 1));
$porPag = 10;

$factory  = new PostgresDaoFactory();
$dao      = $factory->getUsuarioDao();

$total   = $dao->contar($busca);
$paginas = max(1, (int)ceil($total / $porPag));
$pagina  = min($pagina, $paginas);
$usuarios = $dao->buscaPaginado($busca, $pagina, $porPag);

$lista = [];
foreach ($usuarios as $u) {
    $lista[] = [
        'id'     => $u->getId(),
        'email'  => $u->getEmail(),
        'perfil' => $u->getPerfil(),
    ];
}

echo json_encode(['sucesso' => true, 'usuarios' => $lista, 'total' => $total, 'paginas' => $paginas, 'pagina' => $pagina]);
