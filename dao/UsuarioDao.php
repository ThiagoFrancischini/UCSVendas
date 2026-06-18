<?php
interface UsuarioDao {
    public function insere($usuario);
    public function remove($usuario);
    public function altera($usuario);
    public function buscaPorId($id);
    public function buscaPorEmail($email);
    public function buscaTodos();
    public function existePorEmail($email);
    public function buscaPaginado($busca, $pagina, $porPagina);
    public function contar($busca);
}
?>