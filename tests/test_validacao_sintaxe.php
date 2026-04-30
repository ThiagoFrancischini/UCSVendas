<?php
/**
 * Teste de Validação da API de Produtos
 * Verifica a sintaxe e estrutura dos arquivos
 */

echo "====== VALIDAÇÃO DE SINTAXE PHP ======\n\n";

$arquivos = [
    __DIR__ . '/../api/processa_produto.php',
    __DIR__ . '/../controllers/ProdutoController.php',
    __DIR__ . '/../assets/js/dashboard/produto.js' // Será lido como texto
];

foreach ($arquivos as $arquivo) {
    if (file_exists($arquivo)) {
        echo "✓ Arquivo existe: " . basename($arquivo) . "\n";
        
        if (strpos($arquivo, '.php') !== false) {
            // Validar sintaxe PHP
            $saida = [];
            $retorno = 0;
            exec('php -l "' . $arquivo . '"', $saida, $retorno);
            
            if ($retorno === 0) {
                echo "  ✓ Sintaxe PHP válida\n";
            } else {
                echo "  ✗ Erro de sintaxe:\n";
                foreach ($saida as $linha) {
                    echo "    " . $linha . "\n";
                }
            }
        }
    } else {
        echo "✗ Arquivo NÃO encontrado: " . basename($arquivo) . "\n";
    }
    echo "\n";
}

echo "====== TESTE DE CONFIGURAÇÃO ======\n\n";

// Verificar se config.php existe
if (file_exists(__DIR__ . '/../config.php')) {
    echo "✓ config.php encontrado\n";
    require_once(__DIR__ . '/../config.php');
    echo "✓ BASE_URL definido como: " . BASE_URL . "\n";
} else {
    echo "✗ config.php não encontrado\n";
}

echo "\n====== FIM DA VALIDAÇÃO ======\n";
?>
