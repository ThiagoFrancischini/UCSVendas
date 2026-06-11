<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

ob_start();

if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../config.php');
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

set_exception_handler(function ($ex) {
    respondeJson(['sucesso' => false, 'mensagem' => $ex->getMessage()]);
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null) {
        respondeJson(['sucesso' => false, 'mensagem' => 'Erro fatal: ' . $error['message']]);
    }
});

if (!isset($_GET['id']) || empty($_GET['id'])) {
    respondeJson(['sucesso' => false, 'mensagem' => 'ID do produto não informado.']);
}

$id = intval($_GET['id']);

include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');

$factory = new PostgresDaoFactory();
$produtoDao = $factory->getProdutoDao();
$estoqueDao = $factory->getEstoqueDao();
$fornecedorDao = $factory->getFornecedorDao();

try {
    $produto = $produtoDao->buscaPorId($id);

    if (!$produto) {
        respondeJson(['sucesso' => false, 'mensagem' => 'Produto não encontrado.']);
    }

    $estoques = $estoqueDao->buscaPorProdutoId($produto->getId());
    $menorPreco = null;
    $quantidadeTotal = 0;

    foreach ($estoques as $estoque) {
        if ($menorPreco === null || $estoque->getPreco() < $menorPreco) {
            $menorPreco = $estoque->getPreco();
        }
        $quantidadeTotal += $estoque->getQuantidade();
    }

    $fornecedor = $fornecedorDao->buscaPorId($produto->getFornecedorId());

    $resultado = [
        'id' => $produto->getId(),
        'nome' => $produto->getNome(),
        'descricao' => $produto->getDescricao(),
        'foto' => $produto->getFoto(),
        'fornecedor_id' => $produto->getFornecedorId(),
        'fornecedor_nome' => $fornecedor ? $fornecedor->getNome() : 'Desconhecido',
        'preco' => $menorPreco !== null ? number_format($menorPreco, 2, ',', '.') : null,
        'preco_num' => $menorPreco,
        'quantidade_total' => $quantidadeTotal,
    ];

    respondeJson(['sucesso' => true, 'produto' => $resultado]);
} catch (Exception $e) {
    respondeJson(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}
