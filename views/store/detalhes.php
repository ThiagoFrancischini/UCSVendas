<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}
include_once '../layouts/header.php';
?>

<main class="detalhes-produto">
    <div class="detalhes-loading" id="detalhes-loading">Carregando produto...</div>
    <div class="detalhes-content" id="detalhes-content" style="display:none">
        <div class="detalhes-voltar">
            <a href="<?php echo BASE_URL; ?>/views/store/index.php"><i class="fas fa-arrow-left"></i> Voltar para vitrine</a>
        </div>
        <div class="detalhes-grid">
            <div class="detalhes-imagem-wrapper">
                <div class="detalhes-imagem" id="detalhes-imagem"></div>
            </div>
            <div class="detalhes-info">
                <h1 id="detalhes-nome"></h1>
                <div class="detalhes-preco" id="detalhes-preco"></div>
                <div class="detalhes-estoque" id="detalhes-estoque"></div>
                <div class="detalhes-fornecedor">
                    <span class="label">Vendido por:</span>
                    <span id="detalhes-fornecedor-nome"></span>
                </div>
                <div class="detalhes-descricao">
                    <h3>Descrição</h3>
                    <p id="detalhes-descricao"></p>
                </div>
                <div class="detalhes-compra">
                    <div class="detalhes-qtd">
                        <label for="qtd-input">Quantidade:</label>
                        <input type="number" id="qtd-input" value="1" min="1" max="999">
                    </div>
                    <button id="btn-adicionar-carrinho" class="btn-comprar"><i class="fas fa-cart-plus"></i> Adicionar ao Carrinho</button>
                    <div class="detalhes-msg" id="detalhes-msg"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="detalhes-erro" id="detalhes-erro" style="display:none"></div>
</main>

<script>
    window.BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>/assets/js/store/detalhes.js"></script>

<style>
.detalhes-produto {
    max-width: none;
    margin: 0;
    padding: 32px 40px;
    background: transparent;
    box-shadow: none;
    border: none;
}

.detalhes-loading {
    text-align: center;
    padding: 60px 0;
    font-size: 18px;
    color: #666;
}

.detalhes-erro {
    text-align: center;
    padding: 60px 0;
    font-size: 18px;
    color: #e74c3c;
}

.detalhes-voltar {
    margin-bottom: 24px;
}

.detalhes-voltar a {
    color: #1abc9c;
    text-decoration: none;
    font-weight: 600;
}

.detalhes-voltar a:hover {
    text-decoration: underline;
}

.detalhes-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.04);
}

.detalhes-imagem-wrapper {
    display: flex;
    align-items: flex-start;
    justify-content: center;
}

.detalhes-imagem {
    width: 100%;
    min-height: 400px;
    background-size: contain;
    background-position: center;
    background-repeat: no-repeat;
    background-color: #f9f9f9;
    border-radius: 12px;
}

.detalhes-info h1 {
    font-size: 28px;
    margin: 0 0 16px;
    color: #222;
}

.detalhes-preco {
    font-size: 36px;
    font-weight: 700;
    color: #1abc9c;
    margin-bottom: 12px;
}

.detalhes-estoque {
    font-size: 14px;
    color: #27ae60;
    margin-bottom: 16px;
}

.detalhes-estoque.sem-estoque {
    color: #e74c3c;
}

.detalhes-fornecedor {
    font-size: 14px;
    color: #555;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f0f0f0;
}

.detalhes-fornecedor .label {
    color: #888;
}

.detalhes-descricao {
    margin-bottom: 24px;
}

.detalhes-descricao h3 {
    font-size: 16px;
    color: #333;
    margin-bottom: 8px;
}

.detalhes-descricao p {
    font-size: 15px;
    color: #555;
    line-height: 1.7;
}

.detalhes-compra {
    padding-top: 24px;
    border-top: 1px solid #f0f0f0;
}

.detalhes-qtd {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}

.detalhes-qtd label {
    margin: 0;
    font-weight: 600;
    font-size: 14px;
    color: #333;
}

#qtd-input {
    width: 80px;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 16px;
    text-align: center;
}

.btn-comprar {
    width: 100%;
    padding: 16px;
    font-size: 18px;
    font-weight: 700;
    border: none;
    border-radius: 12px;
    background: #1abc9c;
    color: #fff;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-comprar:hover {
    background: #16a085;
}

.btn-comprar:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.detalhes-msg {
    margin-top: 12px;
    font-size: 14px;
    font-weight: 600;
}

.detalhes-msg.sucesso {
    color: #27ae60;
}

.detalhes-msg.erro {
    color: #e74c3c;
}

@media (max-width: 768px) {
    .detalhes-grid {
        grid-template-columns: 1fr;
        gap: 24px;
        padding: 20px;
    }
    .detalhes-imagem {
        min-height: 260px;
    }
    .detalhes-info h1 {
        font-size: 22px;
    }
    .detalhes-preco {
        font-size: 28px;
    }
}
</style>

<?php include_once '../layouts/footer.php'; ?>
