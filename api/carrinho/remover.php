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

if (!$input || !isset($input['produto_id'])) {
    respondeJson(['sucesso' => false, 'mensagem' => 'Dados incompletos.']);
}

$produtoId = intval($input['produto_id']);

if (!isset($_SESSION['carrinho'])) {
    respondeJson(['sucesso' => false, 'mensagem' => 'Carrinho vazio.']);
}

$encontrou = false;
foreach ($_SESSION['carrinho'] as $i => $item) {
    if ($item['produto_id'] === $produtoId) {
        array_splice($_SESSION['carrinho'], $i, 1);
        $encontrou = true;
        break;
    }
}

if (!$encontrou) {
    respondeJson(['sucesso' => false, 'mensagem' => 'Produto não encontrado no carrinho.']);
}

respondeJson(['sucesso' => true, 'mensagem' => 'Produto removido do carrinho.']);
