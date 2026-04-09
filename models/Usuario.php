<?php
class Usuario {
    private $id;
    private $email;
    private $senha;
    private $perfil;

    public function __construct($id = null, $email = "", $senha = "", $perfil = "") {
        $this->id = $id;
        $this->email = $email;
        $this->senha = $senha;
        $this->perfil = $perfil;
    }

    public function getId() { return $this->id; }
    public function getEmail() { return $this->email; }
    public function getSenha() { return $this->senha; }
    public function getPerfil() { return $this->perfil; }

    public function setId($id) { $this->id = $id; }
    public function setEmail($email) { $this->email = $email; }
    public function setSenha($senha) { $this->senha = $senha; }
    public function setPerfil($perfil) { $this->perfil = $perfil; }
}
?>