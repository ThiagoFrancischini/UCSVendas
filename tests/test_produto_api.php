<?php
/**
 * Teste da API de Produtos
 * Simula requisições AJAX para testar os endpoints
 */

session_start();

// Simular uma sessão autenticada como FORNECEDOR
$_SESSION['perfil'] = 'FORNECEDOR';
$_SESSION['usuario_id'] = 1;
$_SESSION['fornecedor_id'] = 1;

require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../controllers/ProdutoController.php');
require_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');

echo "====== TESTE DE API DE PRODUTOS ======\n\n";

// Teste 1: Criar Produto (simulando POST sem acao)
echo "1. Testando CRIAÇÃO de Produto via API...\n";

$_SERVER['REQUEST_METHOD'] = 'POST';
$_GET['acao'] = 'criar';
$_POST = [
    'nome' => 'Produto API Teste - ' . date('Y-m-d H:i:s'),
    'descricao' => 'Descrição do teste via API',
    'foto' => 'https://via.placeholder.com/300',
    'preco' => 89.99,
    'quantidade' => 30
];

ob_start();
include(__DIR__ . '/../api/processa_produto.php');
$resposta = ob_get_clean();

echo "   Response: " . $resposta . "\n";
$resposta_decoded = json_decode($resposta, true);
echo "   ✓ Status: " . ($resposta_decoded['sucesso'] ? "SUCESSO" : "FALHA") . "\n";

if ($resposta_decoded['sucesso']) {
    echo "   ✓ Mensagem: Produto criado com sucesso!\n";
    
    // Buscar o produto criado para testar edição
    $factory = new PostgresDaoFactory();
    $produtoDao = $factory->getProdutoDao();
    $todosProdutos = $produtoDao->buscaTodos();
    $produtoTeste = end($todosProdutos);
    $produtoId = $produtoTeste->getId();
    
    // Teste 2: Editar Produto
    echo "\n2. Testando EDIÇÃO de Produto via API...\n";
    
    $_GET['acao'] = 'editar';
    $_POST = [
        'produto_id' => $produtoId,
        'nome' => 'Produto API Teste EDITADO - ' . date('Y-m-d H:i:s'),
        'descricao' => 'Descrição editada via API',
        'foto' => 'https://via.placeholder.com/300?v=2',
        'preco' => 129.99,
        'quantidade' => 50
    ];
    
    ob_start();
    include(__DIR__ . '/../api/processa_produto.php');
    $resposta_edicao = ob_get_clean();
    
    echo "   Response: " . $resposta_edicao . "\n";
    $resposta_edicao_decoded = json_decode($resposta_edicao, true);
    echo "   ✓ Status: " . ($resposta_edicao_decoded['sucesso'] ? "SUCESSO" : "FALHA") . "\n";
    
    // Teste 3: Deletar Produto
    echo "\n3. Testando DELEÇÃO de Produto via API...\n";
    
    $_GET['acao'] = 'deletar';
    $_POST = [
        'produto_id' => $produtoId
    ];
    
    ob_start();
    include(__DIR__ . '/../api/processa_produto.php');
    $resposta_delecao = ob_get_clean();
    
    echo "   Response: " . $resposta_delecao . "\n";
    $resposta_delecao_decoded = json_decode($resposta_delecao, true);
    echo "   ✓ Status: " . ($resposta_delecao_decoded['sucesso'] ? "SUCESSO" : "FALHA") . "\n";
    
} else {
    echo "   ✗ Erro: " . ($resposta_decoded['mensagem'] ?? 'Erro desconhecido') . "\n";
}

echo "\n====== TESTES DE API CONCLUÍDOS ======\n";
?>
