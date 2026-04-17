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

        $senhaHash = password_hash($dadosUsuario['senha'], PASSWORD_DEFAULT);
        $usuario = new Usuario(null, $dadosUsuario['email'], $senhaHash, 'FORNECEDOR');
        $usuarioId = $usuarioDao->insere($usuario);

        if ($usuarioId > 0) {
            $endereco = new Endereco(null, $dadosEndereco['rua'], $dadosEndereco['numero'], $dadosEndereco['complemento'], $dadosEndereco['bairro'], $dadosEndereco['cep'], $dadosEndereco['cidade'], $dadosEndereco['estado']);
            $enderecoId = $enderecoDao->insere($endereco);

            if ($enderecoId > 0) {
                $fornecedor = new Fornecedor(null, $dadosFornecedor['nome'], $dadosFornecedor['descricao'], $dadosFornecedor['telefone'], $usuarioId, $enderecoId);
                return $fornecedorDao->insere($fornecedor) > 0;
            }
        }
        return false;
    }
}
?>