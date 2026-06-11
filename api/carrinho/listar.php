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

$itens = isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : [];

$totalGeral = 0;
foreach ($itens as $item) {
    $totalGeral += $item['preco'] * $item['quantidade'];
}

respondeJson([
    'sucesso' => true,
    'itens' => $itens,
    'total_geral' => number_format($totalGeral, 2, ',', '.'),
    'total_geral_num' => $totalGeral,
]);
