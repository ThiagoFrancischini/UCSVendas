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

$factory     = new PostgresDaoFactory();
$fornecedorDao = $factory->getFornecedorDao();
$produtoDao  = $factory->getProdutoDao();

$fornecedor  = $fornecedorDao->buscaPorUsuarioId($_SESSION['usuario_id']);
if (!$fornecedor) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Fornecedor não encontrado.']);
    exit;
}

$fid     = $fornecedor->getId();
$total   = $produtoDao->contarPorFornecedorId($fid, $busca);
$paginas = max(1, (int)ceil($total / $porPag));
$pagina  = min($pagina, $paginas);
$produtos = $produtoDao->buscaPorFornecedorIdPaginado($fid, $busca, $pagina, $porPag);

$lista = [];
foreach ($produtos as $p) {
    $lista[] = [
        'id'        => $p->getId(),
        'nome'      => $p->getNome(),
        'descricao' => $p->getDescricao(),
    ];
}

echo json_encode(['sucesso' => true, 'produtos' => $lista, 'total' => $total, 'paginas' => $paginas, 'pagina' => $pagina]);
