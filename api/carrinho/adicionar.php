<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

ob_start();

if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}

function respondeJson($data) {
    if (ob_get_length() > 0) {
        ob_clean();
    }
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondeJson(['sucesso' => false, 'mensagem' => 'Método não permitido.']);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['produto_id']) || !isset($input['quantidade'])) {
    respondeJson(['sucesso' => false, 'mensagem' => 'Dados incompletos.']);
}

$produtoId = intval($input['produto_id']);
$quantidade = intval($input['quantidade']);
$nome = $input['nome'] ?? '';
$foto = $input['foto'] ?? '';

if ($quantidade < 1) {
    respondeJson(['sucesso' => false, 'mensagem' => 'Quantidade inválida.']);
}

include_once(__DIR__ . '/../../dao/postgres/PostgresDaoFactory.php');
$factory = new PostgresDaoFactory();
$estoqueDao = $factory->getEstoqueDao();
$estoques = $estoqueDao->buscaPorProdutoId($produtoId); // ordenado por preco DESC
$estoqueTotal = 0;
$preco = 0.0;
foreach ($estoques as $e) {
    $estoqueTotal += (int)$e->getQuantidade();
    $ePreco = (float)$e->getPreco();
    if ($ePreco > $preco) $preco = $ePreco;
}

$qtdNoCarrinho = 0;
if (isset($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $item) {
        if ($item['produto_id'] === $produtoId) {
            $qtdNoCarrinho = $item['quantidade'];
            break;
        }
    }
}

$qtdFinal = $qtdNoCarrinho + $quantidade;
if ($qtdFinal > $estoqueTotal) {
    $restante = $estoqueTotal - $qtdNoCarrinho;
    if ($restante <= 0) {
        respondeJson(['sucesso' => false, 'mensagem' => 'Produto sem estoque disponível. Você já possui ' . $qtdNoCarrinho . ' unidade(s) no carrinho.']);
    }
    respondeJson(['sucesso' => false, 'mensagem' => 'Quantidade solicitada (' . $quantidade . ') excede o estoque. Você já tem ' . $qtdNoCarrinho . ' no carrinho, restam apenas ' . $restante . '.']);
}

if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

$encontrou = false;
foreach ($_SESSION['carrinho'] as $i => $item) {
    if ($item['produto_id'] === $produtoId) {
        $_SESSION['carrinho'][$i]['quantidade'] = $qtdFinal;
        $encontrou = true;
        break;
    }
}

if (!$encontrou) {
    $_SESSION['carrinho'][] = [
        'produto_id' => $produtoId,
        'nome' => $nome,
        'foto' => $foto,
        'preco' => $preco,
        'quantidade' => $quantidade,
    ];
}

$totalItens = array_reduce($_SESSION['carrinho'], function($sum, $item) {
    return $sum + $item['quantidade'];
}, 0);

respondeJson(['sucesso' => true, 'mensagem' => 'Produto adicionado ao carrinho.', 'total_itens' => $totalItens]);
