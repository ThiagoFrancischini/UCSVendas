<?php
interface ClienteDao {
    public function insere($cliente);
    public function remove($cliente);
    public function altera($cliente);
    public function buscaPorId($id);
    public function buscaPorUsuarioId($usuario_id);
    public function buscaTodos();
}
?>