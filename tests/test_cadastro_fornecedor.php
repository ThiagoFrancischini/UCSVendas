<?php
include_once(__DIR__ . '/../controllers/FornecedorController.php');

$dadosUsuario = [
    'email' => 'fornecedor_teste@example.com',
    'senha' => '123456'
];

$dadosFornecedor = [
    'nome' => 'Empresa Teste LTDA',
    'descricao' => 'Empresa de testes',
    'telefone' => '1133334444',
    'cnpj' => '12345678000123'
];

$dadosEndereco = [
    'rua' => 'Rua Fornecedor Teste',
    'numero' => '789',
    'complemento' => '',
    'bairro' => 'Centro',
    'cep' => '12345678',
    'cidade' => 'São Paulo',
    'estado' => 'SP'
];

try {
    $controller = new FornecedorController();
    $sucesso = $controller->cadastrar($dadosUsuario, $dadosEndereco, $dadosFornecedor);
    if ($sucesso) {
        echo "Cadastro de fornecedor realizado com sucesso!\n";
    } else {
        echo "Erro no cadastro.\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}