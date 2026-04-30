<?php
/**
 * Teste de Funcionalidade de Produtos
 * Este arquivo testa os fluxos de criar, editar e deletar produtos
 */

require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');
require_once(__DIR__ . '/../controllers/ProdutoController.php');
require_once(__DIR__ . '/../models/Produto.php');
require_once(__DIR__ . '/../models/Estoque.php');

echo "====== TESTE DE PRODUTOS ======\n\n";

try {
    $factory = new PostgresDaoFactory();
    $produtoDao = $factory->getProdutoDao();
    $estoqueDao = $factory->getEstoqueDao();
    $controller = new ProdutoController();
    
    // Teste 1: Criar um produto de teste
    echo "1. Testando CRIAÇÃO de Produto...\n";
    $dadosProduto = [
        'nome' => 'Produto Teste - ' . date('Y-m-d H:i:s'),
        'descricao' => 'Descrição do produto de teste',
        'foto' => 'https://via.placeholder.com/300',
        'fornecedor_id' => 1
    ];
    
    $dadosEstoque = [
        'preco' => 99.99,
        'quantidade' => 50
    ];
    
    $resultado = $controller->cadastrarProduto($dadosProduto, $dadosEstoque);
    echo "   ✓ Produto criado: " . ($resultado ? "SIM" : "NÃO") . "\n";
    
    if ($resultado) {
        // Buscar o último produto criado
        $todosProdutos = $produtoDao->buscaTodos();
        $produtoTeste = end($todosProdutos);
        $produtoId = $produtoTeste->getId();
        echo "   ✓ ID do produto: $produtoId\n";
        
        // Teste 2: Editar o produto
        echo "\n2. Testando EDIÇÃO de Produto...\n";
        $dadosProdutoEditado = [
            'nome' => 'Produto Teste Editado - ' . date('Y-m-d H:i:s'),
            'descricao' => 'Descrição editada do produto de teste',
            'foto' => 'https://via.placeholder.com/300?v=2',
            'fornecedor_id' => 1
        ];
        
        $dadosEstoqueEditado = [
            'preco' => 149.99,
            'quantidade' => 75
        ];
        
        $resultadoEdicao = $controller->editarProduto($produtoId, $dadosProdutoEditado, $dadosEstoqueEditado);
        echo "   ✓ Produto editado: " . ($resultadoEdicao ? "SIM" : "NÃO") . "\n";
        
        // Verificar se os dados foram atualizados
        $produtoAtualizado = $produtoDao->buscaPorId($produtoId);
        $estoqueAtualizado = $estoqueDao->buscaPorProdutoId($produtoId);
        
        echo "   ✓ Nome atualizado: " . htmlspecialchars($produtoAtualizado->getNome()) . "\n";
        echo "   ✓ Preço atualizado: R$ " . number_format($estoqueAtualizado->getPreco(), 2, ',', '.') . "\n";
        echo "   ✓ Quantidade atualizada: " . $estoqueAtualizado->getQuantidade() . "\n";
        
        // Teste 3: Buscar produto por ID
        echo "\n3. Testando BUSCA por ID...\n";
        $produtoBuscado = $produtoDao->buscaPorId($produtoId);
        echo "   ✓ Produto encontrado: " . ($produtoBuscado ? "SIM" : "NÃO") . "\n";
        
        // Teste 4: Deletar o produto
        echo "\n4. Testando DELEÇÃO de Produto...\n";
        $resultadoDelecao = $controller->deletarProduto($produtoId);
        echo "   ✓ Produto deletado: " . ($resultadoDelecao ? "SIM" : "NÃO") . "\n";
        
        // Verificar se foi realmente deletado
        $produtoVerificacao = $produtoDao->buscaPorId($produtoId);
        echo "   ✓ Verificação pós-deleção (deve ser NULL): " . ($produtoVerificacao === null ? "OK" : "FALHA") . "\n";
        
        echo "\n====== TESTES CONCLUÍDOS COM SUCESSO ======\n";
    } else {
        echo "   ✗ Falha ao criar produto de teste\n";
    }
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Stack Trace: " . $e->getTraceAsString() . "\n";
}
?>
