<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/views/auth/login.php');
    exit;
}

$perfil = $_SESSION['perfil'];
$usuarioId = $_SESSION['usuario_id'];

include_once(__DIR__ . '/../../dao/postgres/PostgresDaoFactory.php');

$factory = new PostgresDaoFactory();
$usuarioDao = $factory->getUsuarioDao();
$enderecoDao = $factory->getEnderecoDao();

$usuario = $usuarioDao->buscaPorId($usuarioId);
$dados = [
    'email' => $usuario->getEmail(),
    'nome' => '',
    'telefone' => '',
    'cnpj' => '',
    'cartao' => '',
    'endereco' => null
];

if ($perfil === 'FORNECEDOR') {
    $fornecedorDao = $factory->getFornecedorDao();
    $fornecedor = $fornecedorDao->buscaPorUsuarioId($usuarioId);
    if ($fornecedor) {
        $dados['nome'] = $fornecedor->getNome();
        $dados['telefone'] = $fornecedor->getTelefone();
        $dados['cnpj'] = $fornecedor->getCnpj();
        $endereco = $enderecoDao->buscaPorId($fornecedor->getEnderecoId());
        $dados['endereco'] = $endereco;
    }
} else {
    $clienteDao = $factory->getClienteDao();
    $cliente = $clienteDao->buscaPorUsuarioId($usuarioId);
    if ($cliente) {
        $dados['nome'] = $cliente->getNome();
        $dados['telefone'] = $cliente->getTelefone();
        $dados['cartao'] = $cliente->getCartaoCredito();
        $endereco = $enderecoDao->buscaPorId($cliente->getEnderecoId());
        $dados['endereco'] = $endereco;
    }
}
?>
<?php include_once '../layouts/header.php'; ?>

<main>
    <h1>Meu Perfil</h1>
    
    <section class="perfil-dados">
        <h3><?php echo $perfil === 'FORNECEDOR' ? 'Dados da Empresa' : 'Dados Pessoais'; ?></h3>
        
        <div class="info-group">
            <label><?php echo $perfil === 'FORNECEDOR' ? 'Nome da Empresa' : 'Nome'; ?>:</label>
            <p><?php echo htmlspecialchars($dados['nome']); ?></p>
        </div>
        
        <div class="info-group">
            <label>E-mail:</label>
            <p><?php echo htmlspecialchars($dados['email']); ?></p>
        </div>
        
        <div class="info-group">
            <label>Telefone:</label>
            <p><?php echo htmlspecialchars($dados['telefone']); ?></p>
        </div>
        
        <?php if ($perfil === 'FORNECEDOR'): ?>
        <div class="info-group">
            <label>CNPJ:</label>
            <p><?php echo htmlspecialchars($dados['cnpj']); ?></p>
        </div>
        <?php else: ?>
        <div class="info-group">
            <label>Cartão de Crédito:</label>
            <p><?php echo htmlspecialchars($dados['cartao']); ?></p>
        </div>
        <?php endif; ?>
    </section>
    
    <?php if ($dados['endereco']): ?>
    <section class="perfil-endereco">
        <h3>Endereço</h3>
        
        <div class="info-group">
            <label>Rua:</label>
            <p><?php echo htmlspecialchars($dados['endereco']->getRua()); ?>, <?php echo htmlspecialchars($dados['endereco']->getNumero()); ?></p>
        </div>
        
        <?php if ($dados['endereco']->getComplemento()): ?>
        <div class="info-group">
            <label>Complemento:</label>
            <p><?php echo htmlspecialchars($dados['endereco']->getComplemento()); ?></p>
        </div>
        <?php endif; ?>
        
        <div class="info-group">
            <label>Bairro:</label>
            <p><?php echo htmlspecialchars($dados['endereco']->getBairro()); ?></p>
        </div>
        
        <div class="info-group">
            <label>Cidade / Estado:</label>
            <p><?php echo htmlspecialchars($dados['endereco']->getCidade()); ?> / <?php echo htmlspecialchars($dados['endereco']->getEstado()); ?></p>
        </div>
        
        <div class="info-group">
            <label>CEP:</label>
            <p><?php echo htmlspecialchars($dados['endereco']->getCep()); ?></p>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php include_once '../layouts/footer.php'; ?>