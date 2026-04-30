<?php
include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');
include_once(__DIR__ . '/../models/Produto.php');
include_once(__DIR__ . '/../models/Estoque.php');

class ProdutoController {
    private $factory;

    public function __construct() {
        $this->factory = new PostgresDaoFactory();
    }

    public function cadastrarProduto($dadosProduto, $dadosEstoque) {
        $produtoDao = $this->factory->getProdutoDao();
        $estoqueDao = $this->factory->getEstoqueDao();
        $conn = $this->factory->getConnection();

        $conn->beginTransaction();

        try {
            $produto = new Produto(null, $dadosProduto['nome'], $dadosProduto['descricao'], $dadosProduto['foto'], $dadosProduto['fornecedor_id']);
            $produtoId = $produtoDao->insere($produto);

            if ($produtoId <= 0) {
                throw new Exception("Erro ao inserir produto");
            }

            $estoque = new Estoque(null, $dadosEstoque['quantidade'], $dadosEstoque['preco'], $produtoId);
            $estoqueId = $estoqueDao->insere($estoque);

            if ($estoqueId <= 0) {
                throw new Exception("Erro ao inserir estoque");
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function listarProdutosPorFornecedor($fornecedorId) {
        $produtoDao = $this->factory->getProdutoDao();
        return $produtoDao->buscaPorFornecedorId($fornecedorId);
    }

    public function listarTodosProdutos() {
        $produtoDao = $this->factory->getProdutoDao();
        return $produtoDao->buscaTodos();
    }

    public function buscarEstoquePorProduto($produtoId) {
        $estoqueDao = $this->factory->getEstoqueDao();
        return $estoqueDao->buscaPorProdutoId($produtoId);
    }

    public function editarProduto($produtoId, $dadosProduto, $dadosEstoque) {
        $produtoDao = $this->factory->getProdutoDao();
        $estoqueDao = $this->factory->getEstoqueDao();
        $conn = $this->factory->getConnection();

        $conn->beginTransaction();

        try {
            $produto = new Produto($produtoId, $dadosProduto['nome'], $dadosProduto['descricao'], $dadosProduto['foto'], $dadosProduto['fornecedor_id']);
            if (!$produtoDao->altera($produto)) {
                throw new Exception("Erro ao atualizar produto");
            }

            $estoque = $estoqueDao->buscaPorProdutoId($produtoId);
            if ($estoque) {
                $estoque->setQuantidade($dadosEstoque['quantidade']);
                $estoque->setPreco($dadosEstoque['preco']);
                if (!$estoqueDao->altera($estoque)) {
                    throw new Exception("Erro ao atualizar estoque");
                }
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }

    public function deletarProduto($produtoId) {
        $produtoDao = $this->factory->getProdutoDao();
        $estoqueDao = $this->factory->getEstoqueDao();
        $conn = $this->factory->getConnection();

        $conn->beginTransaction();

        try {
            $estoque = $estoqueDao->buscaPorProdutoId($produtoId);
            if ($estoque) {
                if (!$estoqueDao->remove($estoque)) {
                    throw new Exception("Erro ao deletar estoque");
                }
            }

            $produto = $produtoDao->buscaPorId($produtoId);
            if (!$produto) {
                throw new Exception("Produto não encontrado");
            }

            if (!$produtoDao->remove($produto)) {
                throw new Exception("Erro ao deletar produto");
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}