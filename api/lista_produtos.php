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

include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');

$factory = new PostgresDaoFactory();
$produtoDao = $factory->getProdutoDao();
$estoqueDao = $factory->getEstoqueDao();

try {
    $produtos = $produtoDao->buscaTodos();
    $resultado = [];

    foreach ($produtos as $produto) {
        $estoques = $estoqueDao->buscaPorProdutoId($produto->getId());
        $precoMaximo = null;
        $quantidadeTotal = 0;

        foreach ($estoques as $estoque) {
            if ($precoMaximo === null || $estoque->getPreco() > $precoMaximo) {
                $precoMaximo = $estoque->getPreco();
            }
            $quantidadeTotal += $estoque->getQuantidade();
        }

        $resultado[] = [
            'id' => $produto->getId(),
            'nome' => $produto->getNome(),
            'descricao' => $produto->getDescricao(),
            'foto' => $produto->getFoto(),
            'fornecedor_id' => $produto->getFornecedorId(),
            'preco' => $precoMaximo !== null ? number_format($precoMaximo, 2, ',', '.') : null,
            'quantidade_total' => $quantidadeTotal,
        ];
    }

    $json = json_encode(['sucesso' => true, 'produtos' => $resultado], JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        throw new Exception('JSON encode falhou: ' . json_last_error_msg());
    }
    respondeJson(json_decode($json, true));
} catch (Exception $e) {
    respondeJson(['sucesso' => false, 'mensagem' => $e->getMessage()]);
}
