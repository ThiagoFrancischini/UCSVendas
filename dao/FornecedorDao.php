<?php
interface FornecedorDao {
    public function insere($fornecedor);
    public function remove($fornecedor);
    public function altera($fornecedor);
    public function buscaPorId($id);
    public function buscaPorUsuarioId($usuario_id);
    public function buscaTodos();
}
?>