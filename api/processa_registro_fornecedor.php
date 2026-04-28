<?php
header('Content-Type: application/json');

include_once(__DIR__ . '/../controllers/FornecedorController.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $dadosUsuario = [
        'email' => $_POST['email'] ?? '',
        'senha' => $_POST['senha'] ?? ''
    ];

    $dadosFornecedor = [
        'nome' => $_POST['nome'] ?? '',
        'descricao' => $_POST['descricao'] ?? '',
        'telefone' => $_POST['telefone'] ?? '',
        'cnpj' => $_POST['cnpj'] ?? ''
    ];

    $dadosEndereco = [
        'rua' => $_POST['rua'] ?? '',
        'numero' => $_POST['numero'] ?? '',
        'complemento' => $_POST['complemento'] ?? '',
        'bairro' => $_POST['bairro'] ?? '',
        'cep' => $_POST['cep'] ?? '',
        'cidade' => $_POST['cidade'] ?? '',
        'estado' => $_POST['estado'] ?? ''
    ];

    try {
        $controller = new FornecedorController();
        $sucesso = $controller->cadastrar($dadosUsuario, $dadosEndereco, $dadosFornecedor);

        if ($sucesso) {
            echo json_encode(['sucesso' => true]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao realizar o cadastro da empresa.']);
        }
    } catch (Exception $e) {
        $mensagem = $e->getMessage();
        $campo = null;

        if (strpos($mensagem, 'duplicate key') !== false || strpos($mensagem, 'unique') !== false) {
            if (strpos($mensagem, 'email') !== false) {
                $mensagem = 'E-mail já cadastrado.';
                $campo = 'email';
            } elseif (strpos($mensagem, 'cnpj') !== false) {
                $mensagem = 'CNPJ já cadastrado.';
                $campo = 'cnpj';
            } else {
                $mensagem = 'Dados já cadastrados.';
            }
        } elseif (strpos($mensagem, 'usuario') !== false) {
            $campo = 'email';
        } elseif (strpos($mensagem, 'endereco') !== false) {
            $campo = 'cep';
        } elseif (strpos($mensagem, 'fornecedor') !== false) {
            $campo = 'nome';
        }

        echo json_encode(['sucesso' => false, 'mensagem' => $mensagem, 'campo' => $campo]);
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido.']);
}
?>