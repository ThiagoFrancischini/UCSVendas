<?php
header('Content-Type: application/json');

include_once(__DIR__ . '/../controllers/FornecedorController.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $dadosUsuario = [
        'email' => $_POST['email'] ?? '',
        'senha' => $_POST['senha'] ?? ''
    ];

    $dadosFornecedor = [
        'nome' => $_POST['nome'] ?? '',
        'descricao' => $_POST['descricao'] ?? '',
        'telefone' => $_POST['telefone'] ?? '',
        'cnpj' => $_POST['cnpj'] ?? ''
    ];

    $dadosEndereco = [
        'rua' => $_POST['rua'] ?? '',
        'numero' => $_POST['numero'] ?? '',
        'complemento' => $_POST['complemento'] ?? '',
        'bairro' => $_POST['bairro'] ?? '',
        'cep' => $_POST['cep'] ?? '',
        'cidade' => $_POST['cidade'] ?? '',
        'estado' => $_POST['estado'] ?? ''
    ];

    try {
        $controller = new FornecedorController();
        $sucesso = $controller->cadastrar($dadosUsuario, $dadosEndereco, $dadosFornecedor);

        if ($sucesso) {
            echo json_encode(['sucesso' => true]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao realizar o cadastro da empresa.']);
        }
    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido.']);
}
?>