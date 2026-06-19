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
            // ADMIN não tem registro em tabelas filha

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
            } elseif ($usuario->getPerfil() === 'CLIENTE') {
                $clienteDao = $factory->getClienteDao();
                $cliente = $clienteDao->buscaPorUsuarioId($usuario->getId());
                if ($cliente) {
                    $nome = $cliente->getNome();
                }
            }
            // ADMIN: usa email como nome
        }

        return [
            'usuario_id' => $usuario->getId(),
            'perfil' => $usuario->getPerfil(),
            'nome' => $nome
        ];
    }

    public function editarFornecedor($usuarioId, $dadosFornecedor, $dadosEndereco) {
        $factory = new PostgresDaoFactory();
        $fornecedorDao = $factory->getFornecedorDao();
        $enderecoDao = $factory->getEnderecoDao();
        $conn = $factory->getConnection();

        $conn->beginTransaction();

        try {
            $fornecedor = $fornecedorDao->buscaPorUsuarioId($usuarioId);
            if (!$fornecedor) {
                throw new Exception("Fornecedor não encontrado");
            }

            $endereco = $enderecoDao->buscaPorId($fornecedor->getEnderecoId());
            if ($endereco) {
                $endereco->setRua($dadosEndereco['rua']);
                $endereco->setNumero($dadosEndereco['numero']);
                $endereco->setComplemento($dadosEndereco['complemento']);
                $endereco->setBairro($dadosEndereco['bairro']);
                $endereco->setCidade($dadosEndereco['cidade']);
                $endereco->setEstado($dadosEndereco['estado']);
                $endereco->setCep($dadosEndereco['cep']);

                if (!$enderecoDao->altera($endereco)) {
                    throw new Exception("Erro ao atualizar endereço");
                }
            }

            $fornecedor->setNome($dadosFornecedor['nome']);
            $fornecedor->setTelefone($dadosFornecedor['telefone']);
            $fornecedor->setCnpj($dadosFornecedor['cnpj']);

            if (!$fornecedorDao->altera($fornecedor)) {
                throw new Exception("Erro ao atualizar fornecedor");
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}
?>