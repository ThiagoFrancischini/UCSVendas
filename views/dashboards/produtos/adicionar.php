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
            <label>Estoques:</label>
            <div id="estoques-container">
                <div class="estoque-item" data-index="0">
                    <h4>Estoque 1</h4>
                    <div class="form-group">
                        <label for="preco_0">Preço de Venda (R$):</label>
                        <input type="number" id="preco_0" name="estoques[0][preco]" step="0.01" min="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="quantidade_0">Quantidade:</label>
                        <input type="number" id="quantidade_0" name="estoques[0][quantidade]" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="preco_custo_0">Preço de Custo (R$):</label>
                        <input type="number" id="preco_custo_0" name="estoques[0][preco_custo]" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label for="lote_0">Lote:</label>
                        <input type="text" id="lote_0" name="estoques[0][lote]" maxlength="50">
                    </div>
                </div>
            </div>
            <button type="button" id="add-estoque" class="btn-secondary">Adicionar Estoque</button>
        </div>
        
        <div class="form-group">
            <label for="foto">URL da Foto:</label>
            <input type="url" id="foto" name="foto" placeholder="https://exemplo.com/imagem.jpg">
            <div id="foto-preview-wrap" style="display:none;margin-top:10px">
                <img id="foto-preview" src="" alt="Prévia" style="max-width:200px;max-height:200px;border-radius:8px;border:1px solid #ddd;object-fit:cover;">
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit">Publicar Produto</button>
            <a href="index.php" class="btn-cancel">Cancelar</a>
        </div>
    </form>
</main>

<?php include_once '../../layouts/footer.php'; ?>

<script src="<?php echo BASE_URL; ?>/assets/js/dashboard/produto.js"></script>

<script>
document.getElementById('foto').addEventListener('input', function() {
    const url = this.value.trim();
    const wrap = document.getElementById('foto-preview-wrap');
    const img  = document.getElementById('foto-preview');
    if (url) {
        img.src = url;
        wrap.style.display = 'block';
        img.onerror = function() { wrap.style.display = 'none'; };
        img.onload  = function() { wrap.style.display = 'block'; };
    } else {
        wrap.style.display = 'none';
    }
});
</script>

<script>
$(document).ready(function() {
    let estoqueIndex = 1;
    
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