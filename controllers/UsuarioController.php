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

            $factory = new PostgresDaoFactory();
            if ($usuario->getPerfil() === 'FORNECEDOR') {
                $fornecedorDao = $factory->getFornecedorDao();
                $fornecedor = $fornecedorDao->buscaPorUsuarioId($usuario->getId());
                if ($fornecedor) {
                    $_SESSION['fornecedor_id'] = $fornecedor->getId();
                }
            } elseif ($usuario->getPerfil() === 'CLIENTE') {
                $clienteDao = $factory->getClienteDao();
                $cliente = $clienteDao->buscaPorUsuarioId($usuario->getId());
                if ($cliente) {
                    $_SESSION['cliente_id'] = $cliente->getId();
                }
            }

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

    public function buscarDadosLogin($email) {
        $usuario = $this->usuarioDao->buscaPorEmail($email);
        $nome = $email;

        if ($usuario) {
            $factory = new PostgresDaoFactory();
            if ($usuario->getPerfil() === 'FORNECEDOR') {
                $fornecedorDao = $factory->getFornecedorDao();
                $fornecedor = $fornecedorDao->buscaPorUsuarioId($usuario->getId());
                if ($fornecedor) {
                    $nome = $fornecedor->getNome();
                }
            } else {
                $clienteDao = $factory->getClienteDao();
                $cliente = $clienteDao->buscaPorUsuarioId($usuario->getId());
                if ($cliente) {
                    $nome = $cliente->getNome();
                }
            }
        }

        return [
            'usuario_id' => $usuario->getId(),
            'perfil' => $usuario->getPerfil(),
            'nome' => $nome
        ];
    }
}
?>