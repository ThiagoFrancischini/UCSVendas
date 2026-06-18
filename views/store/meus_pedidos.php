<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'CLIENTE') {
    header('Location: ' . BASE_URL . '/views/auth/login.php');
    exit;
}

include_once '../layouts/header.php';
?>

<main class="meus-pedidos-page">
    <h1>Meus Pedidos</h1>
    <div id="pedidos-loading" class="loading-msg">Carregando pedidos...</div>
    <div id="pedidos-vazio" class="vazio-msg" style="display:none">Você ainda não fez nenhum pedido.</div>
    <div id="lista-pedidos"></div>

    <!-- Modal de detalhes do pedido -->
    <div id="modal-pedido" class="modal" style="display:none">
        <div class="modal-overlay" onclick="fecharModal()"></div>
        <div class="modal-box">
            <div class="modal-header">
                <h2 id="modal-titulo">Detalhes do Pedido</h2>
                <button class="modal-fechar" onclick="fecharModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body-inner">

            <!-- Carrossel de fotos -->
            <div class="carrossel" id="carrossel" style="display:none">
                <button class="carr-btn carr-prev" onclick="moverCarrossel(-1)"><i class="fas fa-chevron-left"></i></button>
                <div class="carr-track-wrapper">
                    <div class="carr-track" id="carr-track"></div>
                </div>
                <button class="carr-btn carr-next" onclick="moverCarrossel(1)"><i class="fas fa-chevron-right"></i></button>
            </div>

            <!-- Itens do pedido -->
            <div id="modal-itens-loading" class="loading-msg">Carregando itens...</div>
            <table class="detalhe-tabela" id="detalhe-tabela" style="display:none">
                <thead>
                    <tr><th>Produto</th><th>Fornecedor</th><th>Qtd</th><th>Unit.</th><th>Total</th></tr>
                </thead>
                <tbody id="detalhe-itens"></tbody>
            </table>

            <!-- Paginação de itens -->
            <div class="paginacao" id="paginacao-itens"></div>
            </div><!-- /modal-body-inner -->
        </div>
    </div>
</main>

<script>
window.BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>/assets/js/store/meus_pedidos.js"></script>

<style>
.meus-pedidos-page { max-width: 900px; margin: 40px auto; padding: 0 20px; }
.meus-pedidos-page h1 { font-size: 26px; margin-bottom: 24px; }
.loading-msg, .vazio-msg { text-align: center; padding: 60px 0; color: #888; font-size: 16px; }
.pedido-card { background: #fff; border: 1px solid #e8edf3; border-radius: 16px; padding: 22px 28px; margin-bottom: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); transition: box-shadow .2s; }
.pedido-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.09); }
.pedido-card-header { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
.pedido-num-wrap { display: flex; flex-direction: column; gap: 2px; }
.pedido-num { font-size: 17px; font-weight: 700; color: #1e293b; }
.pedido-card-meta { margin-top: 8px; }
.pedido-data { font-size: 13px; color: #94a3b8; }
.pedido-data i { margin-right: 4px; }
.pedido-status { padding: 5px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
.status-PENDENTE { background: #fef9c3; color: #854d0e; }
.status-CONFIRMADO { background: #dbeafe; color: #1e40af; }
.status-ENVIADO { background: #cffafe; color: #0e7490; }
.status-ENTREGUE { background: #dcfce7; color: #166534; }
.status-CANCELADO { background: #fee2e2; color: #991b1b; }
.pedido-card-footer { margin-top: 16px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 14px; }
.pedido-valor { font-size: 22px; font-weight: 800; color: #1abc9c; letter-spacing: -.3px; }
.btn-ver-detalhes { padding: 9px 20px; background: #1abc9c; color: #fff; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; transition: background .15s; }
.btn-ver-detalhes:hover { background: #16a085; }
.fornecedor-tag { display: inline-block; background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 10px; font-size: 12px; font-weight: 600; white-space: nowrap; }
/* Modal */
.modal { position: fixed; inset: 0; z-index: 1000; display: flex; align-items: center; justify-content: center; }
.modal-overlay { position: absolute; inset: 0; background: rgba(15,23,42,.5); backdrop-filter: blur(2px); }
.modal-box { position: relative; background: #fff; border-radius: 16px; overflow: hidden; max-width: 720px; width: 92%; max-height: 88vh; display: flex; flex-direction: column; z-index: 1; box-shadow: 0 20px 60px rgba(0,0,0,.2); }
.modal-header { background: #2c3e50; color: #fff; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
.modal-header h2 { color: #fff; margin: 0; font-size: 17px; }
.modal-fechar { background: none; border: none; color: rgba(255,255,255,.7); font-size: 16px; cursor: pointer; padding: 4px 8px; border-radius: 6px; width: auto; transition: color .15s, background .15s; }
.modal-fechar:hover { color: #fff; background: rgba(255,255,255,.12); box-shadow: none; }
.modal-body-inner { padding: 24px; overflow-y: auto; flex: 1; }
/* Carrossel */
.carrossel { display: flex; align-items: center; gap: 8px; margin-bottom: 24px; overflow: hidden; }
.carr-track-wrapper { flex: 1; overflow: hidden; }
.carr-track { display: flex; gap: 12px; transition: transform 0.3s; }
.carr-foto { width: 120px; height: 120px; border-radius: 10px; background-size: cover; background-position: center; background-color: #f1f5f9; flex-shrink: 0; }
.carr-btn { background: #1abc9c; color: #fff; border: none; border-radius: 50%; width: 34px; height: 34px; font-size: 13px; cursor: pointer; flex-shrink: 0; display: flex; align-items: center; justify-content: center; padding: 0; }
.carr-btn:hover { background: #16a085; box-shadow: 0 2px 6px rgba(26,188,156,.4); }
/* Tabela */
.detalhe-tabela { width: 100%; border-collapse: collapse; }
.detalhe-tabela th { background: #2c3e50; color: #e2e8f0; font-size: 11px; text-transform: uppercase; padding: 10px 14px; text-align: left; letter-spacing: .5px; }
.detalhe-tabela td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
/* Paginação */
.paginacao { display: flex; gap: 6px; margin-top: 16px; justify-content: center; flex-wrap: wrap; }
.paginacao button { padding: 6px 12px; border: 1px solid #ddd; border-radius: 6px; background: #fff; cursor: pointer; font-size: 13px; }
.paginacao button.active { background: #1abc9c; color: #fff; border-color: #1abc9c; }
@media (max-width: 600px) {
    .pedido-card-header { flex-direction: column; align-items: flex-start; }
    .detalhe-tabela thead { display: none; }
    .detalhe-tabela tr { display: block; border-bottom: 1px solid #f0f0f0; }
    .detalhe-tabela td { display: flex; justify-content: space-between; border: none; padding: 6px 14px; }
}
</style>

<?php include_once '../layouts/footer.php'; ?>
