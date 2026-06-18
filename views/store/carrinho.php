<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}
include_once '../layouts/header.php';
?>

<main class="carrinho-page">
    <div class="carrinho-header">
        <h1>Carrinho de Compras</h1>
    </div>

    <div class="carrinho-loading" id="carrinho-loading">Carregando carrinho...</div>
    <div class="carrinho-vazio" id="carrinho-vazio" style="display:none">
        <p>Seu carrinho está vazio.</p>
        <a href="<?php echo BASE_URL; ?>/views/store/index.php" class="btn-continuar">Continuar comprando</a>
    </div>

    <div class="carrinho-content" id="carrinho-content" style="display:none">
        <div class="carrinho-tabela-wrapper">
            <table class="carrinho-tabela">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="carrinho-itens"></tbody>
            </table>
        </div>

        <div class="carrinho-resumo">
            <div class="carrinho-total">
                <span>Total:</span>
                <strong id="carrinho-total-geral">R$ 0,00</strong>
            </div>
            <div class="carrinho-acoes">
                <a href="<?php echo BASE_URL; ?>/views/store/index.php" class="btn-continuar">Continuar comprando</a>
                <a href="<?php echo BASE_URL; ?>/views/store/checkout.php" class="btn-finalizar-carrinho">Finalizar Pedido</a>
            </div>
        </div>
    </div>
</main>

<script>
    window.BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>/assets/js/store/carrinho.js"></script>

<style>
.carrinho-page {
    max-width: 960px;
    margin: 40px auto;
    padding: 0 20px;
    background: transparent;
    box-shadow: none;
}

.carrinho-header h1 {
    font-size: 28px;
    margin-bottom: 24px;
}

.carrinho-loading {
    text-align: center;
    padding: 60px 0;
    font-size: 18px;
    color: #666;
}

.carrinho-vazio {
    text-align: center;
    padding: 60px 0;
}

.carrinho-vazio p {
    font-size: 18px;
    color: #555;
    margin-bottom: 20px;
}

.carrinho-tabela-wrapper {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,0.04);
    margin-bottom: 24px;
}

.carrinho-tabela {
    width: 100%;
    border-collapse: collapse;
}

.carrinho-tabela th {
    background: #f8f9fa;
    padding: 16px 20px;
    text-align: left;
    font-size: 13px;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #ececec;
}

.carrinho-tabela td {
    padding: 20px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

.carrinho-tabela tr:last-child td {
    border-bottom: none;
}

.carrinho-item-info {
    display: flex;
    align-items: center;
    gap: 16px;
}

.carrinho-item-foto {
    width: 64px;
    height: 64px;
    border-radius: 8px;
    background-size: cover;
    background-position: center;
    background-color: #f4f4f4;
    flex-shrink: 0;
}

.carrinho-item-nome {
    font-weight: 600;
    color: #222;
}

.carrinho-item-preco {
    color: #555;
    font-weight: 600;
}

.carrinho-qtd-input {
    width: 60px;
    padding: 8px 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    text-align: center;
}

.carrinho-item-subtotal {
    font-weight: 700;
    color: #222;
}

.btn-remover {
    background: none;
    border: 1.5px solid #fee2e2;
    color: #e74c3c;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    padding: 7px 12px;
    width: auto;
    border-radius: 6px;
    transition: background 0.2s, border-color 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.btn-remover:hover {
    background: #fef2f2;
    border-color: #fca5a5;
    box-shadow: none;
}

.carrinho-resumo {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 24px 32px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.04);
}

.carrinho-total {
    font-size: 18px;
    color: #333;
}

.carrinho-total strong {
    font-size: 28px;
    color: #1abc9c;
    margin-left: 12px;
}

.btn-continuar {
    display: inline-block;
    background: #1abc9c;
    color: #fff;
    text-decoration: none;
    padding: 12px 28px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 15px;
    transition: background 0.2s;
}

.btn-continuar:hover {
    background: #16a085;
    color: #fff;
}

.carrinho-acoes {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: flex-end;
    align-items: center;
}

.btn-finalizar-carrinho {
    display: inline-block;
    background: #2ecc71;
    color: #fff;
    text-decoration: none;
    padding: 12px 28px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 15px;
    transition: background 0.2s;
}

.btn-finalizar-carrinho:hover {
    background: #27ae60;
    color: #fff;
}

@media (max-width: 640px) {
    .carrinho-tabela thead {
        display: none;
    }
    .carrinho-tabela tr {
        display: block;
        padding: 16px;
        border-bottom: 1px solid #f0f0f0;
    }
    .carrinho-tabela td {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border: none;
    }
    .carrinho-resumo {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }
}
</style>

<?php include_once '../layouts/footer.php'; ?>
