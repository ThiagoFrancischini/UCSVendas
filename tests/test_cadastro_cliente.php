<?php
include_once(__DIR__ . '/../controllers/ClienteController.php');

$dadosUsuario = [
    'email' => 'cliente_teste@example.com',
    'senha' => '123456'
];

$dadosCliente = [
    'nome' => 'Cliente Teste',
    'telefone' => '11999998888',
    'cartao_credito' => '1234567890123456'
];

$dadosEndereco = [
    'rua' => 'Rua Cliente Teste',
    'numero' => '456',
    'complemento' => 'Apto 101',
    'bairro' => 'Bairro Teste',
    'cep' => '12345678',
    'cidade' => 'São Paulo',
    'estado' => 'SP'
];

try {
    $controller = new ClienteController();
    $sucesso = $controller->cadastrar($dadosUsuario, $dadosEndereco, $dadosCliente);
    if ($sucesso) {
        echo "Cadastro de cliente realizado com sucesso!\n";
    } else {
        echo "Erro no cadastro.\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}