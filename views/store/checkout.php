<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}
if (session_status() == PHP_SESSION_NONE) session_start();
include_once '../layouts/header.php';
?>

<main class="checkout-page">
    <h1>Finalizar Pedido</h1>

    <!-- Resumo do carrinho -->
    <section class="checkout-resumo" id="checkout-resumo">
        <h2>Resumo do Carrinho</h2>
        <div id="checkout-itens-loading">Carregando...</div>
        <table class="checkout-tabela" id="checkout-tabela" style="display:none">
            <thead>
                <tr><th>Produto</th><th>Qtd</th><th>Unitário</th><th>Subtotal</th></tr>
            </thead>
            <tbody id="checkout-itens"></tbody>
        </table>
        <div class="checkout-total" id="checkout-total" style="display:none">
            Total: <strong id="checkout-total-valor">R$ 0,00</strong>
        </div>
    </section>

    <!-- Bloco de login/registro (exibido apenas se não logado como cliente) -->
    <?php if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'CLIENTE'): ?>
    <section class="checkout-auth" id="checkout-auth">
        <h2>Identificação</h2>
        <div class="auth-tabs">
            <button class="auth-tab active" data-tab="login" onclick="mostrarTab('login')">Já tenho conta</button>
            <button class="auth-tab" data-tab="registro" onclick="mostrarTab('registro')">Sou novo cliente</button>
        </div>

        <!-- Login -->
        <div id="tab-login" class="auth-form">
            <div id="msg-login" class="form-msg"></div>
            <label>E-mail</label>
            <input type="email" id="login-email" placeholder="seu@email.com">
            <label>Senha</label>
            <input type="password" id="login-senha" placeholder="Senha">
            <button class="btn-primary" onclick="fazerLogin()">Entrar</button>
        </div>

        <!-- Registro rápido -->
        <div id="tab-registro" class="auth-form" style="display:none">
            <div id="msg-registro" class="form-msg"></div>
            <label>Nome</label>
            <input type="text" id="reg-nome" placeholder="Seu nome completo">
            <label>E-mail</label>
            <input type="email" id="reg-email" placeholder="seu@email.com">
            <label>Senha</label>
            <input type="password" id="reg-senha" placeholder="Mínimo 6 caracteres">
            <label>Telefone</label>
            <input type="text" id="reg-telefone" placeholder="(00) 00000-0000">
            <label>CEP</label>
            <input type="text" id="reg-cep" placeholder="00000-000">
            <label>Rua</label>
            <input type="text" id="reg-rua" placeholder="Rua">
            <label>Número</label>
            <input type="text" id="reg-numero" placeholder="Número">
            <label>Bairro</label>
            <input type="text" id="reg-bairro" placeholder="Bairro">
            <label>Cidade</label>
            <input type="text" id="reg-cidade" placeholder="Cidade">
            <label>Estado</label>
            <input type="text" id="reg-estado" placeholder="RS" maxlength="2">
            <button class="btn-primary" onclick="fazerRegistro()">Criar conta e continuar</button>
        </div>
    </section>
    <?php endif; ?>

    <!-- Botão finalizar (visível apenas se logado como cliente) -->
    <div class="checkout-actions" id="checkout-actions" style="<?php echo (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'CLIENTE') ? 'display:none' : ''; ?>">
        <div id="msg-checkout" class="form-msg"></div>
        <button class="btn-finalizar" id="btn-finalizar" onclick="finalizarPedido()">Confirmar Pedido</button>
        <a href="<?php echo BASE_URL; ?>/views/store/carrinho.php" class="btn-voltar">Voltar ao Carrinho</a>
    </div>
</main>

<script>
window.BASE_URL = '<?php echo BASE_URL; ?>';
window.LOGADO_COMO_CLIENTE = <?php echo (isset($_SESSION['perfil']) && $_SESSION['perfil'] === 'CLIENTE') ? 'true' : 'false'; ?>;
</script>
<script src="<?php echo BASE_URL; ?>/assets/js/store/checkout.js"></script>

<style>
.checkout-page { max-width: 760px; margin: 40px auto; padding: 0 20px; }
.checkout-page h1 { font-size: 26px; margin-bottom: 24px; }
.checkout-resumo, .checkout-auth { background: #fff; border: 1px solid #ececec; border-radius: 16px; padding: 28px; margin-bottom: 24px; box-shadow: 0 4px 16px rgba(0,0,0,0.04); }
.checkout-resumo h2, .checkout-auth h2 { font-size: 18px; margin-bottom: 16px; color: #2c3e50; }
.checkout-tabela { width: 100%; border-collapse: collapse; }
.checkout-tabela th, .checkout-tabela td { padding: 12px 16px; border-bottom: 1px solid #f0f0f0; text-align: left; }
.checkout-tabela th { font-size: 12px; color: #888; text-transform: uppercase; }
.checkout-total { text-align: right; font-size: 18px; margin-top: 16px; }
.checkout-total strong { font-size: 26px; color: #1abc9c; margin-left: 10px; }
.auth-tabs { display: flex; gap: 0; margin-bottom: 20px; border-bottom: 2px solid #ececec; }
.auth-tab { background: none; border: none; padding: 12px 20px; cursor: pointer; font-size: 15px; color: #888; font-weight: 600; border-bottom: 2px solid transparent; margin-bottom: -2px; }
.auth-tab.active { color: #1abc9c; border-bottom-color: #1abc9c; }
.auth-form label { display: block; font-size: 13px; font-weight: 600; color: #555; margin: 12px 0 4px; }
.auth-form input { width: 100%; padding: 10px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; box-sizing: border-box; }
.form-msg { padding: 10px 14px; border-radius: 8px; margin-bottom: 12px; display: none; }
.form-msg.erro { background: #fdf2f2; color: #c0392b; display: block; }
.form-msg.ok { background: #f0fdf4; color: #16a085; display: block; }
.btn-primary { margin-top: 16px; width: 100%; padding: 12px; background: #1abc9c; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; }
.btn-primary:hover { background: #16a085; box-shadow: 0 2px 8px rgba(26,188,156,.3); }
.checkout-actions { display: flex; flex-direction: column; gap: 12px; }
.btn-finalizar { padding: 14px 28px; background: #27ae60; color: #fff; border: none; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; }
.btn-finalizar:hover { background: #219a52; box-shadow: 0 3px 10px rgba(39,174,96,.35); }
.btn-finalizar:disabled { background: #94a3b8; cursor: not-allowed; box-shadow: none; }
.btn-voltar { text-align: center; color: #64748b; font-size: 14px; text-decoration: none; padding: 8px; display: block; border-radius: 8px; transition: background .12s; }
.btn-voltar:hover { background: #f1f5f9; color: #334155; }
@media (max-width: 600px) {
    .checkout-tabela thead { display: none; }
    .checkout-tabela tr { display: block; border-bottom: 1px solid #f0f0f0; padding: 12px 0; }
    .checkout-tabela td { display: flex; justify-content: space-between; border: none; padding: 4px 16px; }
}
</style>

<?php include_once '../layouts/footer.php'; ?>
