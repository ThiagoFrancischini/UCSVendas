<?php
/**
 * Teste da Edição de Perfil do Fornecedor
 * Simula a edição de dados do fornecedor
 */

session_start();

// Simular uma sessão autenticada como FORNECEDOR
$_SESSION['perfil'] = 'FORNECEDOR';
$_SESSION['usuario_id'] = 1;

require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../controllers/UsuarioController.php');
require_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');

echo "====== TESTE DE EDIÇÃO DE PERFIL ======\n\n";

// Teste: Editar fornecedor
echo "Testando edição de perfil do fornecedor...\n";

$_SERVER['REQUEST_METHOD'] = 'POST';
$_GET['acao'] = 'editar';
$_POST = [
    'nome' => 'Empresa Teste Editada',
    'telefone' => '(11) 99999-8888',
    'cnpj' => '12.345.678/0001-99',
    'rua' => 'Rua Editada',
    'numero' => '123',
    'complemento' => 'Sala 456',
    'bairro' => 'Centro Editado',
    'cidade' => 'São Paulo',
    'estado' => 'SP',
    'cep' => '01234-567'
];

ob_start();
include(__DIR__ . '/../api/processa_perfil.php');
$resposta = ob_get_clean();

echo "Response: " . $resposta . "\n";
$resposta_decoded = json_decode($resposta, true);
echo "Status: " . ($resposta_decoded['sucesso'] ? "SUCESSO" : "FALHA") . "\n";

if (!$resposta_decoded['sucesso']) {
    echo "Erro: " . ($resposta_decoded['mensagem'] ?? 'Erro desconhecido') . "\n";
}

echo "\n====== FIM DO TESTE ======\n";
?>