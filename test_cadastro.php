<?php
include_once(__DIR__ . '/controllers/FornecedorController.php');

$dadosUsuario = [
    'email' => 'teste@example.com',
    'senha' => '123456'
];

$dadosFornecedor = [
    'nome' => 'Empresa Teste',
    'descricao' => 'Descrição',
    'telefone' => '123456789',
    'cnpj' => '12345678000123'
];

$dadosEndereco = [
    'rua' => 'Rua Teste',
    'numero' => '123',
    'complemento' => '',
    'bairro' => 'Bairro',
    'cep' => '12345678',
    'cidade' => 'Cidade',
    'estado' => 'SP'
];

try {
    $controller = new FornecedorController();
    $sucesso = $controller->cadastrar($dadosUsuario, $dadosEndereco, $dadosFornecedor);
    if ($sucesso) {
        echo "Cadastro realizado com sucesso!\n";
    } else {
        echo "Erro no cadastro.\n";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>