<?php include_once '../layouts/header.php'; ?>

<main>
    <?php if ($_SESSION['usuario_tipo'] === 'FORNECEDOR'): ?>
        <h1>Painel do Fornecedor</h1>
        <section id="cadastro-produto">
            <h3>Cadastrar Novo Produto</h3>
            <div id="mensagem-produto"></div>
            
            <form id="form-produto">
                <div class="form-group">
                    <label>Nome do Produto:</label>
                    <input type="text" name="nome" required>
                </div>
                <div class="form-group">
                    <label>Descrição:</label>
                    <textarea name="descricao" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Preço (R$):</label>
                    <input type="number" name="preco" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Quantidade em Estoque:</label>
                    <input type="number" name="quantidade" required>
                </div>
                <div class="form-group">
                    <label>URL da Foto:</label>
                    <input type="text" name="foto">
                </div>
                <button type="submit">Publicar Produto</button>
            </form>
        </section>
    <?php else: ?>
        <h1>Meu Painel</h1>
        <p>Bem-vindo, <?php echo $_SESSION['usuario_email']; ?>! Aqui você verá seus pedidos em breve.</p>
    <?php endif; ?>
</main>

<?php include_once '../layouts/footer.php'; ?>
<script src="../../assets/js/dashboard/index.js"></script>