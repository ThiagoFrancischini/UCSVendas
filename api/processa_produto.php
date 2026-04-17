<?php
header('Content-Type: application/json');
include_once(__DIR__ . '/../controllers/ProdutoController.php');

if (session_status() == PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['usuario_tipo'] === 'FORNECEDOR') {
    
    $dadosProduto = [
        'nome' => $_POST['nome'] ?? '',
        'descricao' => $_POST['descricao'] ?? '',
        'foto' => $_POST['foto'] ?? '',
        'fornecedor_id' => $_SESSION['usuario_id'] 
    ];

    $dadosEstoque = [
        'preco' => $_POST['preco'] ?? 0,
        'quantidade' => $_POST['quantidade'] ?? 0
    ];

    $controller = new ProdutoController();
    $sucesso = $controller->cadastrarProduto($dadosProduto, $dadosEstoque);

    if ($sucesso) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar o produto e o estoque.']);
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
}
?>