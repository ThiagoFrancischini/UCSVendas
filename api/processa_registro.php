<?php
header('Content-Type: application/json');

include_once(__DIR__ . '/../controllers/ClienteController.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dadosUsuario = [
        'email' => $_POST['email'] ?? '',
        'senha' => $_POST['senha'] ?? ''
    ];

    $dadosCliente = [
        'nome' => $_POST['nome'] ?? '',
        'telefone' => $_POST['telefone'] ?? '',
        'cartao_credito' => $_POST['cartao'] ?? ''
    ];

    $dadosEndereco = [
        'rua' => $_POST['rua'] ?? '',
        'numero' => $_POST['numero'] ?? '',
        'bairro' => $_POST['bairro'] ?? '',
        'cidade' => $_POST['cidade'] ?? '',
        'estado' => $_POST['estado'] ?? '',
        'cep' => $_POST['cep'] ?? '',
        'complemento' => $_POST['complemento'] ?? ''
    ];

    $controller = new ClienteController();
    $sucesso = $controller->cadastrar($dadosUsuario, $dadosEndereco, $dadosCliente);

    if ($sucesso) {
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao realizar o cadastro.']);
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido.']);
}
?>