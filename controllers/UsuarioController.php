<?php
include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');
include_once(__DIR__ . '/../models/Usuario.php');

class UsuarioController {
    private $usuarioDao;

    public function __construct() {
        $factory = new PostgresDaoFactory();
        $this->usuarioDao = $factory->getUsuarioDao();
    }

    public function login($email, $senha) {
        $usuario = $this->usuarioDao->buscaPorEmail($email);
        
        if ($usuario && password_verify($senha, $usuario->getSenha())) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['usuario_id'] = $usuario->getId();
            $_SESSION['perfil'] = $usuario->getPerfil();
            return true;
        }
        return false;
    }

    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }
}
?>