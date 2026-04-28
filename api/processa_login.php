<?php
header('Content-Type: application/json');

include_once(__DIR__ . '/../controllers/UsuarioController.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha todos os campos.']);
        exit;
    }

    $controller = new UsuarioController();
    $logado = $controller->login($email, $senha);

    if ($logado) {
        $dados = $controller->buscarDadosLogin($email);
        echo json_encode([
            'sucesso' => true,
            'perfil' => $dados['perfil'],
            'nome' => $dados['nome'],
            'usuario_id' => $dados['usuario_id']
        ]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'E-mail ou senha incorretos.']);
    }
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido.']);
}
?>