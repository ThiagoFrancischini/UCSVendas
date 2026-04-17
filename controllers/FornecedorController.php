<?php
include_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');
include_once(__DIR__ . '/../models/Usuario.php');
include_once(__DIR__ . '/../models/Endereco.php');
include_once(__DIR__ . '/../models/Fornecedor.php');

class FornecedorController {
    private $factory;

    public function __construct() {
        $this->factory = new PostgresDaoFactory();
    }

    public function cadastrar($dadosUsuario, $dadosEndereco, $dadosFornecedor) {
        $usuarioDao = $this->factory->getUsuarioDao();
        $enderecoDao = $this->factory->getEnderecoDao();
        $fornecedorDao = $this->factory->getFornecedorDao();
        $conn = $this->factory->getConnection();

        $conn->beginTransaction();

        try {
            $senhaHash = password_hash($dadosUsuario['senha'], PASSWORD_DEFAULT);
            $usuario = new Usuario(null, $dadosUsuario['email'], $senhaHash, 'FORNECEDOR');
            $usuarioId = $usuarioDao->insere($usuario);

            if ($usuarioId <= 0) {
                throw new Exception("Erro ao inserir usuário");
            }

            $endereco = new Endereco(null, $dadosEndereco['rua'], $dadosEndereco['numero'], $dadosEndereco['complemento'], $dadosEndereco['bairro'], $dadosEndereco['cep'], $dadosEndereco['cidade'], $dadosEndereco['estado']);
            $enderecoId = $enderecoDao->insere($endereco);

            if ($enderecoId <= 0) {
                throw new Exception("Erro ao inserir endereço");
            }

            $fornecedor = new Fornecedor(null, $dadosFornecedor['nome'], $dadosFornecedor['descricao'], $dadosFornecedor['telefone'], $dadosFornecedor['cnpj'], $usuarioId, $enderecoId);
            $fornecedorId = $fornecedorDao->insere($fornecedor);

            if ($fornecedorId <= 0) {
                throw new Exception("Erro ao inserir fornecedor");
            }

            $conn->commit();
            return true;
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e; // Re-throw para o try-catch no processa_registro_fornecedor.php
        }
    }
}
?>