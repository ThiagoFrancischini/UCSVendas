<?php
header('Content-Type: application/json');
include_once(__DIR__ . '/../controllers/ProdutoController.php');
include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');

if (session_status() == PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['perfil'] === 'FORNECEDOR') {
    
    $acao = $_GET['acao'] ?? 'criar';
    
    if ($acao === 'editar') {
        $produtoId = $_POST['produto_id'] ?? null;
        
        if (!$produtoId) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID do produto não informado.']);
            exit;
        }

        $factory = new PostgresDaoFactory();
        $produtoDao = $factory->getProdutoDao();
        $fornecedorDao = $factory->getFornecedorDao();

        $produto = $produtoDao->buscaPorId($produtoId);
        if (!$produto) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Produto não encontrado.']);
            exit;
        }

        $fornecedor = $fornecedorDao->buscaPorUsuarioId($_SESSION['usuario_id']);
        if ($produto->getFornecedorId() != $fornecedor->getId()) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Você não tem permissão para editar este produto.']);
            exit;
        }

        $dadosProduto = [
            'nome' => $_POST['nome'] ?? '',
            'descricao' => $_POST['descricao'] ?? '',
            'foto' => $_POST['foto'] ?? '',
            'fornecedor_id' => $fornecedor->getId()
        ];

        // Processar múltiplos estoques
        $dadosEstoques = [];
        if (isset($_POST['estoques']) && is_array($_POST['estoques'])) {
            foreach ($_POST['estoques'] as $estoqueData) {
                $dadosEstoques[] = [
                    'quantidade' => $estoqueData['quantidade'] ?? 0,
                    'preco' => $estoqueData['preco'] ?? 0,
                    'preco_custo' => $estoqueData['preco_custo'] ?? null,
                    'lote' => $estoqueData['lote'] ?? null
                ];
            }
        }

        $controller = new ProdutoController();
        try {
            $sucesso = $controller->editarProduto($produtoId, $dadosProduto, $dadosEstoques);
            if ($sucesso) {
                echo json_encode(['sucesso' => true]);
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar o produto e o estoque.']);
            }
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    } elseif ($acao === 'deletar') {
        $produtoId = $_POST['produto_id'] ?? null;
        
        if (!$produtoId) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'ID do produto não informado.']);
            exit;
        }

        $factory = new PostgresDaoFactory();
        $produtoDao = $factory->getProdutoDao();
        $fornecedorDao = $factory->getFornecedorDao();

        $produto = $produtoDao->buscaPorId($produtoId);
        if (!$produto) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Produto não encontrado.']);
            exit;
        }

        $fornecedor = $fornecedorDao->buscaPorUsuarioId($_SESSION['usuario_id']);
        if ($produto->getFornecedorId() != $fornecedor->getId()) {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Você não tem permissão para deletar este produto.']);
            exit;
        }

        $controller = new ProdutoController();
        try {
            $sucesso = $controller->deletarProduto($produtoId);
            if ($sucesso) {
                echo json_encode(['sucesso' => true]);
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao deletar o produto.']);
            }
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    } else {
        $dadosProduto = [
            'nome' => $_POST['nome'] ?? '',
            'descricao' => $_POST['descricao'] ?? '',
            'foto' => $_POST['foto'] ?? '',
            'fornecedor_id' => $_SESSION['fornecedor_id']
        ];

        // Processar múltiplos estoques
        $dadosEstoques = [];
        if (isset($_POST['estoques']) && is_array($_POST['estoques'])) {
            foreach ($_POST['estoques'] as $estoqueData) {
                $dadosEstoques[] = [
                    'quantidade' => $estoqueData['quantidade'] ?? 0,
                    'preco' => $estoqueData['preco'] ?? 0,
                    'preco_custo' => $estoqueData['preco_custo'] ?? null,
                    'lote' => $estoqueData['lote'] ?? null
                ];
            }
        }

        $controller = new ProdutoController();
        try {
            $sucesso = $controller->cadastrarProduto($dadosProduto, $dadosEstoques);

            if ($sucesso) {
                echo json_encode(['sucesso' => true]);
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar o produto e o estoque.']);
            }
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
}
?>