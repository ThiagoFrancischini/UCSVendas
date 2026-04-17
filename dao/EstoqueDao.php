<?php
interface EstoqueDao {
    public function insere($estoque);
    public function remove($estoque);
    public function altera($estoque);
    public function buscaPorId($id);
    public function buscaPorProdutoId($produto_id);
    public function buscaTodos();
}
?>