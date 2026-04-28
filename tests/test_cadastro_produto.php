<?php
include_once(__DIR__ . '/../controllers/ProdutoController.php');
include_once(__DIR__ . '/../controllers/FornecedorController.php');
include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');

$factory = new PostgresDaoFactory();
$usuarioDao = $factory->getUsuarioDao();
$fornecedorDao = $factory->getFornecedorDao();

$usuario = $usuarioDao->buscaPorEmail('fornecedor_teste@example.com');

if (!$usuario) {
    echo "Fornecedor de teste nao encontrado. Criando...\n";
    
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

    $controller = new FornecedorController();
    $sucesso = $controller->cadastrar($dadosUsuario, $dadosEndereco, $dadosFornecedor);
    
    if (!$sucesso) {
        echo "Falha ao criar fornecedor de teste.\n";
        exit(1);
    }
    
    $usuario = $usuarioDao->buscaPorEmail('fornecedor_teste@example.com');
}

$fornecedor = $fornecedorDao->buscaPorUsuarioId($usuario->getId());
$fornecedorId = $fornecedor->getId();
echo "Fornecedor ID: $fornecedorId\n";

$dadosProduto = [
    'nome' => 'Produto Teste ' . date('Y-m-d H:i:s'),
    'descricao' => 'Produto criado por teste unitario',
    'foto' => 'https://exemplo.com/foto.jpg',
    'fornecedor_id' => $fornecedorId
];

$dadosEstoque = [
    'preco' => 99.90,
    'quantidade' => 10
];

try {
    $controller = new ProdutoController();
    $sucesso = $controller->cadastrarProduto($dadosProduto, $dadosEstoque);
    
    if ($sucesso) {
        echo "Produto cadastrado com sucesso!\n";
        exit(0);
    } else {
        echo "Falha ao cadastrar produto.\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}