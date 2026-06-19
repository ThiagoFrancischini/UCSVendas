<?php
header('Content-Type: application/json');
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['perfil']) || !in_array($_SESSION['perfil'], ['FORNECEDOR', 'ADMIN'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
    exit;
}
include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');

$busca   = trim($_GET['busca'] ?? '');
$pagina  = max(1, (int)($_GET['pagina'] ?? 1));
$porPag  = 10;

$factory = new PostgresDaoFactory();
$dao     = $factory->getFornecedorDao();

$total      = $dao->contar($busca);
$paginas    = max(1, (int)ceil($total / $porPag));
$pagina     = min($pagina, $paginas);
$fornecedores = $dao->buscaPaginado($busca, $pagina, $porPag);

$lista = [];
foreach ($fornecedores as $f) {
    $lista[] = [
        'id'        => $f->getId(),
        'nome'      => $f->getNome(),
        'cnpj'      => $f->getCnpj(),
        'telefone'  => $f->getTelefone(),
        'descricao' => $f->getDescricao(),
    ];
}

echo json_encode(['sucesso' => true, 'fornecedores' => $lista, 'total' => $total, 'paginas' => $paginas, 'pagina' => $pagina]);
