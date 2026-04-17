<?php
interface ProdutoDao {
    public function insere($produto);
    public function remove($produto);
    public function altera($produto);
    public function buscaPorId($id);
    public function buscaPorFornecedorId($fornecedor_id);
    public function buscaTodos();
}
?>