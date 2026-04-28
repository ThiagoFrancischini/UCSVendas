<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../../config.php');
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'FORNECEDOR') {
    header('Location: ' . BASE_URL . '/views/store/index.php');
    exit;
}

include_once(__DIR__ . '/../../../dao/postgres/PostgresDaoFactory.php');

$factory = new PostgresDaoFactory();
$fornecedorDao = $factory->getFornecedorDao();
$produtoDao = $factory->getProdutoDao();

$fornecedor = $fornecedorDao->buscaPorUsuarioId($_SESSION['usuario_id']);
$produtos = $produtoDao->buscaPorFornecedorId($fornecedor->getId());
?>
<?php include_once '../../layouts/header.php'; ?>

<main>
    <div class="page-header">
        <h1>Meus Produtos</h1>
        <a href="adicionar.php" class="btn-primary">+ Adicionar Produto</a>
    </div>
    
    <?php if (empty($produtos)): ?>
        <div class="empty-state">
            <p>Voc� ainda n�o tem produtos cadastrados.</p>
            <a href="adicionar.php" class="btn-link">Clique aqui para adicionar seu primeiro produto</a>
        </div>
    <?php else: ?>
        <div id="grid-produtos">
            <?php foreach ($produtos as $produto): ?>
                <div class="produto-card">
                    <div class="produto-info">
                        <h3><?php echo htmlspecialchars($produto->getNome()); ?></h3>
                        <p><?php echo htmlspecialchars($produto->getDescricao()); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include_once '../../layouts/footer.php'; ?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.btn-primary {
    background-color: #1abc9c;
    color: white;
    padding: 12px 24px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
}
.btn-primary:hover {
    background-color: #16a085;
    color: white;
}
.empty-state {
    text-align: center;
    padding: 50px;
    background-color: #f9fbfc;
    border-radius: 8px;
}
.btn-link {
    color: #1abc9c;
    text-decoration: underline;
}
.produto-card {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.produto-card h3 {
    margin-bottom: 10px;
    font-size: 18px;
}
.produto-card p {
    color: #7f8c8d;
    font-size: 14px;
}
</style>