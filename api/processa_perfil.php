<?php
header('Content-Type: application/json');
include_once(__DIR__ . '/../controllers/UsuarioController.php');

if (session_status() == PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['usuario_id'])) {

    $acao = $_GET['acao'] ?? 'editar';

    if ($acao === 'editar' && $_SESSION['perfil'] === 'FORNECEDOR') {
        $dadosFornecedor = [
            'nome' => $_POST['nome'] ?? '',
            'telefone' => $_POST['telefone'] ?? '',
            'cnpj' => $_POST['cnpj'] ?? ''
        ];

        $dadosEndereco = [
            'rua' => $_POST['rua'] ?? '',
            'numero' => $_POST['numero'] ?? '',
            'complemento' => $_POST['complemento'] ?? '',
            'bairro' => $_POST['bairro'] ?? '',
            'cidade' => $_POST['cidade'] ?? '',
            'estado' => $_POST['estado'] ?? '',
            'cep' => $_POST['cep'] ?? ''
        ];

        $controller = new UsuarioController();
        try {
            $sucesso = $controller->editarFornecedor($_SESSION['usuario_id'], $dadosFornecedor, $dadosEndereco);
            if ($sucesso) {
                echo json_encode(['sucesso' => true]);
            } else {
                echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar o perfil.']);
            }
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'mensagem' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ação não permitida.']);
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
}
?>