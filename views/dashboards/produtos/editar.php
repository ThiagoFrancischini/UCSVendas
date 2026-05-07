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

$estoques = $estoqueDao->buscaPorProdutoId($produtoId);
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
            <label>Estoques:</label>
            <div id="estoques-container">
                <?php foreach ($estoques as $index => $estoque): ?>
                <div class="estoque-item" data-index="<?php echo $index; ?>">
                    <h4>Estoque <?php echo $index + 1; ?></h4>
                    <div class="form-group">
                        <label for="preco_<?php echo $index; ?>">Preço de Venda (R$):</label>
                        <input type="number" id="preco_<?php echo $index; ?>" name="estoques[<?php echo $index; ?>][preco]" step="0.01" min="0.01" required value="<?php echo htmlspecialchars($estoque->getPreco()); ?>">
                    </div>
                    <div class="form-group">
                        <label for="quantidade_<?php echo $index; ?>">Quantidade:</label>
                        <input type="number" id="quantidade_<?php echo $index; ?>" name="estoques[<?php echo $index; ?>][quantidade]" min="0" required value="<?php echo htmlspecialchars($estoque->getQuantidade()); ?>">
                    </div>
                    <div class="form-group">
                        <label for="preco_custo_<?php echo $index; ?>">Preço de Custo (R$):</label>
                        <input type="number" id="preco_custo_<?php echo $index; ?>" name="estoques[<?php echo $index; ?>][preco_custo]" step="0.01" min="0" value="<?php echo htmlspecialchars($estoque->getPrecoCusto() ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="lote_<?php echo $index; ?>">Lote:</label>
                        <input type="text" id="lote_<?php echo $index; ?>" name="estoques[<?php echo $index; ?>][lote]" maxlength="50" value="<?php echo htmlspecialchars($estoque->getLote() ?? ''); ?>">
                    </div>
                    <?php if ($index > 0): ?>
                    <button type="button" class="remove-estoque btn-danger">Remover Estoque</button>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add-estoque" class="btn-secondary">Adicionar Estoque</button>
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

<script>
$(document).ready(function() {
    let estoqueIndex = <?php echo count($estoques); ?>;
    
    function getNextEstoqueIndex() {
        const existingIndexes = [];
        $('#estoques-container .estoque-item').each(function() {
            const index = parseInt($(this).data('index'));
            existingIndexes.push(index);
        });
        // Encontra o menor index não usado começando do 0
        let nextIndex = 0;
        while (existingIndexes.includes(nextIndex)) {
            nextIndex++;
        }
        return nextIndex;
    }
    
    function updateEstoqueTitles() {
        $('#estoques-container .estoque-item').each(function(index) {
            $(this).find('h4').text('Estoque ' + (index + 1));
        });
    }
    
    $('#add-estoque').on('click', function() {
        const container = $('#estoques-container');
        const nextIndex = getNextEstoqueIndex();
        const newEstoque = `
            <div class="estoque-item" data-index="${nextIndex}">
                <h4>Estoque ${$('#estoques-container .estoque-item').length + 1}</h4>
                <div class="form-group">
                    <label for="preco_${nextIndex}">Preço de Venda (R$):</label>
                    <input type="number" id="preco_${nextIndex}" name="estoques[${nextIndex}][preco]" step="0.01" min="0.01" required>
                </div>
                <div class="form-group">
                    <label for="quantidade_${nextIndex}">Quantidade:</label>
                    <input type="number" id="quantidade_${nextIndex}" name="estoques[${nextIndex}][quantidade]" min="0" required>
                </div>
                <div class="form-group">
                    <label for="preco_custo_${nextIndex}">Preço de Custo (R$):</label>
                    <input type="number" id="preco_custo_${nextIndex}" name="estoques[${nextIndex}][preco_custo]" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label for="lote_${nextIndex}">Lote:</label>
                    <input type="text" id="lote_${nextIndex}" name="estoques[${nextIndex}][lote]" maxlength="50">
                </div>
                <button type="button" class="remove-estoque btn-danger">Remover Estoque</button>
            </div>
        `;
        container.append(newEstoque);
        updateEstoqueTitles();
    });
    
    $(document).on('click', '.remove-estoque', function() {
        $(this).closest('.estoque-item').remove();
        updateEstoqueTitles();
    });
});
</script>

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

.estoque-item {
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.estoque-item h4 {
    margin-top: 0;
    color: #333;
}

.btn-secondary {
    background-color: #3498db;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
}

.btn-secondary:hover {
    background-color: #2980b9;
}

.btn-danger {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    margin-top: 10px;
}

.btn-danger:hover {
    background-color: #c0392b;
}
</style>
