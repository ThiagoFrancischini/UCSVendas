<?php
include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');
include_once(__DIR__ . '/../models/Usuario.php');
include_once(__DIR__ . '/../models/Endereco.php');
include_once(__DIR__ . '/../models/Cliente.php');

class ClienteController {
    private $factory;

    public function __construct() {
        $this->factory = new PostgresDaoFactory();
    }

    public function cadastrar($dadosUsuario, $dadosEndereco, $dadosCliente) {
        $usuarioDao = $this->factory->getUsuarioDao();
        $enderecoDao = $this->factory->getEnderecoDao();
        $clienteDao = $this->factory->getClienteDao();
        $conn = $this->factory->getConnection();

        if ($usuarioDao->existePorEmail($dadosUsuario['email'])) {
            throw new Exception("E-mail já cadastrado no sistema.");
        }

        $conn->beginTransaction();

        try {
            $senhaHash = password_hash($dadosUsuario['senha'], PASSWORD_DEFAULT);
            $usuario = new Usuario(null, $dadosUsuario['email'], $senhaHash, 'CLIENTE');
            $usuarioId = $usuarioDao->insere($usuario);

            if ($usuarioId <= 0) {
                throw new Exception("Erro ao inserir usuário");
            }

            $endereco = new Endereco(null, $dadosEndereco['rua'], $dadosEndereco['numero'], $dadosEndereco['complemento'], $dadosEndereco['bairro'], $dadosEndereco['cep'], $dadosEndereco['cidade'], $dadosEndereco['estado']);
            $enderecoId = $enderecoDao->insere($endereco);

            if ($enderecoId <= 0) {
                throw new Exception("Erro ao inserir endereço");
            }

            $cliente = new Cliente(null, $dadosCliente['nome'], $dadosCliente['telefone'], $dadosCliente['cartao_credito'], $usuarioId, $enderecoId);
            $clienteId = $clienteDao->insere($cliente);

            if ($clienteId <= 0) {
                throw new Exception("Erro ao inserir cliente");
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