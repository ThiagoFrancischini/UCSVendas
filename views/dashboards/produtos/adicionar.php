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
?>
<?php include_once '../../layouts/header.php'; ?>

<main>
    <h1>Cadastrar Produto</h1>
    
    <div id="mensagem-produto"></div>
    
    <form id="form-produto">
        <div class="form-group">
            <label for="nome">Nome do Produto:</label>
            <input type="text" id="nome" name="nome" maxlength="150" required>
        </div>
        
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" rows="4" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="preco">Preço (R$):</label>
            <input type="number" id="preco" name="preco" step="0.01" min="0.01" required>
        </div>
        
        <div class="form-group">
            <label for="quantidade">Quantidade em Estoque:</label>
            <input type="number" id="quantidade" name="quantidade" min="0" required>
        </div>
        
        <div class="form-group">
            <label for="foto">URL da Foto:</label>
            <input type="url" id="foto" name="foto">
        </div>
        
        <div class="form-actions">
            <button type="submit">Publicar Produto</button>
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
}
.btn-cancel:hover {
    background-color: #7f8c8d;
    color: white;
}
</style>