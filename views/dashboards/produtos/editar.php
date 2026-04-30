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

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

include_once(__DIR__ . '/../../../dao/postgres/PostgresDaoFactory.php');

$factory = new PostgresDaoFactory();
$produtoDao = $factory->getProdutoDao();
$estoqueDao = $factory->getEstoqueDao();
$fornecedorDao = $factory->getFornecedorDao();

$produtoId = $_GET['id'];
$produto = $produtoDao->buscaPorId($produtoId);

// Verificar se o produto existe e pertence ao fornecedor
if (!$produto) {
    header('Location: index.php');
    exit;
}

$fornecedor = $fornecedorDao->buscaPorUsuarioId($_SESSION['usuario_id']);
if ($produto->getFornecedorId() != $fornecedor->getId()) {
    header('Location: index.php');
    exit;
}

$estoque = $estoqueDao->buscaPorProdutoId($produtoId);
?>
<?php include_once '../../layouts/header.php'; ?>

<main>
    <h1>Editar Produto</h1>
    
    <div id="mensagem-produto"></div>
    
    <form id="form-produto">
        <input type="hidden" id="produto_id" name="produto_id" value="<?php echo $produto->getId(); ?>">
        
        <div class="form-group">
            <label for="nome">Nome do Produto:</label>
            <input type="text" id="nome" name="nome" maxlength="150" required value="<?php echo htmlspecialchars($produto->getNome()); ?>">
        </div>
        
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" rows="4" required><?php echo htmlspecialchars($produto->getDescricao()); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="preco">Preço (R$):</label>
            <input type="number" id="preco" name="preco" step="0.01" min="0.01" required value="<?php echo htmlspecialchars($estoque->getPreco()); ?>">
        </div>
        
        <div class="form-group">
            <label for="quantidade">Quantidade em Estoque:</label>
            <input type="number" id="quantidade" name="quantidade" min="0" required value="<?php echo htmlspecialchars($estoque->getQuantidade()); ?>">
        </div>
        
        <div class="form-group">
            <label for="foto">URL da Foto:</label>
            <input type="url" id="foto" name="foto" value="<?php echo htmlspecialchars(is_string($produto->getFoto()) ? $produto->getFoto() : ''); ?>">
        </div>
        
        <div class="form-actions">
            <button type="submit">Salvar Alterações</button>
            <a href="index.php" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</main>

<?php include_once '../../layouts/footer.php'; ?>

<script src="<?php echo BASE_URL; ?>/assets/js/dashboard/produto.js"></script>

<style>
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}
.form-actions button {
    flex: 1;
}
.btn-cancel {
    display: inline-block;
    padding: 12px 20px;
    background-color: #95a5a6;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    text-align: center;
    font-weight: 600;
    flex: 1;
}
.btn-cancel:hover {
    background-color: #7f8c8d;
    color: white;
}
</style>
