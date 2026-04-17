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

        $produto = new Produto(null, $dadosProduto['nome'], $dadosProduto['descricao'], $dadosProduto['foto'], $dadosProduto['fornecedor_id']);
        $produtoId = $produtoDao->insere($produto);

        if ($produtoId > 0) {
            $estoque = new Estoque(null, $dadosEstoque['quantidade'], $dadosEstoque['preco'], $produtoId);
            return $estoqueDao->insere($estoque) > 0;
        }
        return false;
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
}
?>