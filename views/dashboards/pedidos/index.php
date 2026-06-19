<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../../config.php');
}
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'FORNECEDOR') {
    header('Location: ' . BASE_URL . '/views/store/index.php');
    exit;
}

include_once '../../layouts/header.php';
?>

<main class="ped-admin-page">
    <div class="ped-admin-header">
        <h1>Gestão de Pedidos</h1>
        <div class="ped-busca">
            <input type="number" id="busca-numero" placeholder="Nº do pedido" min="1">
            <input type="text" id="busca-cliente" placeholder="Nome do cliente">
            <button class="btn-buscar" onclick="buscarPedidos(1)">Buscar</button>
            <button class="btn-limpar" onclick="limparBusca()">Limpar</button>
        </div>
    </div>

    <div id="pedidos-loading" class="loading-msg">Carregando pedidos...</div>
    <div id="pedidos-vazio" class="vazio-msg" style="display:none">Nenhum pedido encontrado.</div>

    <div id="lista-pedidos" style="display:none">
        <table class="ped-tabela">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="ped-tbody"></tbody>
        </table>
        <div class="paginacao" id="paginacao"></div>
    </div>

    <!-- Modal de detalhes -->
    <div id="modal-pedido" class="modal" style="display:none">
        <div class="modal-overlay" onclick="fecharModal()"></div>
        <div class="modal-box">
            <div class="modal-header">
                <h2 id="modal-titulo">Detalhes do Pedido</h2>
                <button class="modal-fechar" onclick="fecharModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body-inner">

            <div class="modal-info" id="modal-info"></div>

            <!-- Alterar status -->
            <div class="modal-status">
                <label>Alterar Status:</label>
                <select id="select-status">
                    <option value="PENDENTE">Pendente</option>
                    <option value="CONFIRMADO">Confirmado</option>
                    <option value="ENVIADO">Enviado</option>
                    <option value="ENTREGUE">Entregue</option>
                    <option value="CANCELADO">Cancelado</option>
                </select>
                <button class="btn-status" onclick="salvarStatus()">Salvar Status</button>
                <span id="msg-status" class="msg-status"></span>
            </div>

            <!-- Carrossel de fotos -->
            <div class="carrossel" id="carrossel" style="display:none">
                <button class="carr-btn" onclick="moverCarrossel(-1)"><i class="fas fa-chevron-left"></i></button>
                <div class="carr-track-wrapper">
                    <div class="carr-track" id="carr-track"></div>
                </div>
                <button class="carr-btn" onclick="moverCarrossel(1)"><i class="fas fa-chevron-right"></i></button>
            </div>

            <div id="modal-itens-loading" class="loading-msg">Carregando itens...</div>
            <table class="detalhe-tabela" id="detalhe-tabela" style="display:none">
                <thead>
                    <tr><th>Produto</th><th>Qtd</th><th>Unit.</th><th>Total Item</th></tr>
                </thead>
                <tbody id="detalhe-itens"></tbody>
            </table>
            <div class="paginacao" id="paginacao-itens"></div>
            </div><!-- /modal-body-inner -->
        </div>
    </div>
</main>

<script>
window.BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>/assets/js/dashboard/pedidos.js"></script>

<style>
.ped-admin-page { max-width: none; background: transparent; box-shadow: none; border: none; padding: 32px 40px; margin: 0; }
.ped-admin-header { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 24px; }
.ped-admin-header h1 { font-size: 26px; margin: 0; }
.ped-busca { display: flex; gap: 8px; flex-wrap: wrap; }
.ped-busca input { padding: 9px 14px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
.btn-buscar { padding: 9px 18px; background: #1abc9c; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; }
.btn-buscar:hover { background: #16a085; }
.btn-limpar { padding: 9px 14px; background: #f0f0f0; color: #555; border: none; border-radius: 8px; cursor: pointer; }
.loading-msg, .vazio-msg { text-align: center; padding: 60px 0; color: #888; }
.ped-tabela { width: 100%; border-collapse: collapse; background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.06); border: 1px solid #e2e8f0; }
.ped-tabela th { background: #2c3e50; color: #e2e8f0; padding: 12px 16px; font-size: 11px; font-weight: 600; text-transform: uppercase; text-align: left; letter-spacing: .6px; }
.ped-tabela td { padding: 13px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; color: #334155; }
.ped-tabela tr:last-child td { border-bottom: none; }
.ped-status { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.status-PENDENTE { background: #fff3cd; color: #856404; }
.status-CONFIRMADO { background: #cfe2ff; color: #084298; }
.status-ENVIADO { background: #d1ecf1; color: #0c5460; }
.status-ENTREGUE { background: #d4edda; color: #155724; }
.status-CANCELADO { background: #f8d7da; color: #721c24; }
.btn-detalhe { padding: 6px 14px; background: #1abc9c; color: #fff; border: none; border-radius: 6px; font-size: 13px; cursor: pointer; }
.btn-detalhe:hover { background: #16a085; }
.paginacao { display: flex; gap: 6px; margin-top: 20px; justify-content: center; flex-wrap: wrap; }
.paginacao button { padding: 7px 13px; border: 1px solid #ddd; border-radius: 6px; background: #fff; cursor: pointer; font-size: 13px; }
.paginacao button.active { background: #1abc9c; color: #fff; border-color: #1abc9c; }
/* Modal */
.modal { position: fixed; inset: 0; z-index: 1000; display: flex; align-items: center; justify-content: center; }
.modal-overlay { position: absolute; inset: 0; background: rgba(15,23,42,.5); backdrop-filter: blur(2px); }
.modal-box { position: relative; background: #fff; border-radius: 16px; overflow: hidden; max-width: 760px; width: 92%; max-height: 88vh; display: flex; flex-direction: column; z-index: 1; box-shadow: 0 20px 60px rgba(0,0,0,.2); }
.modal-header { background: #2c3e50; color: #fff; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
.modal-header h2 { color: #fff; margin: 0; font-size: 17px; }
.modal-fechar { background: none; border: none; color: rgba(255,255,255,.7); font-size: 16px; cursor: pointer; padding: 4px 8px; border-radius: 6px; width: auto; transition: color .15s, background .15s; }
.modal-fechar:hover { color: #fff; background: rgba(255,255,255,.12); box-shadow: none; }
.modal-body-inner { padding: 24px; overflow-y: auto; flex: 1; }
.modal-info { background: #f8fafc; border-radius: 10px; padding: 16px; margin-bottom: 18px; font-size: 14px; line-height: 1.8; border: 1px solid #e2e8f0; }
.modal-status { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
.modal-status label { font-size: 14px; font-weight: 600; }
.modal-status select { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 14px; }
.btn-status { padding: 8px 18px; background: #3498db; color: #fff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 13px; }
.btn-status:hover { background: #2980b9; box-shadow: 0 2px 6px rgba(52,152,219,.35); }
.msg-status { font-size: 13px; font-weight: 600; }
.msg-status.ok { color: #27ae60; }
.msg-status.erro { color: #e74c3c; }
/* Carrossel */
.carrossel { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
.carr-track-wrapper { flex: 1; overflow: hidden; }
.carr-track { display: flex; gap: 12px; transition: transform 0.3s; }
.carr-foto { width: 110px; height: 110px; border-radius: 10px; background-size: cover; background-position: center; background-color: #f1f5f9; flex-shrink: 0; }
.carr-btn { background: #1abc9c; color: #fff; border: none; border-radius: 50%; width: 34px; height: 34px; font-size: 13px; cursor: pointer; flex-shrink: 0; display: flex; align-items: center; justify-content: center; padding: 0; }
.carr-btn:hover { background: #16a085; box-shadow: 0 2px 6px rgba(26,188,156,.4); }
/* Tabela detalhe */
.detalhe-tabela { width: 100%; border-collapse: collapse; }
.detalhe-tabela th { background: #2c3e50; color: #e2e8f0; font-size: 11px; text-transform: uppercase; padding: 10px 14px; text-align: left; letter-spacing: .5px; }
.detalhe-tabela td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
@media (max-width: 700px) {
    .ped-admin-header { flex-direction: column; align-items: flex-start; }
    .ped-tabela thead { display: none; }
    .ped-tabela tr { display: block; border-bottom: 1px solid #f0f0f0; padding: 12px 0; }
    .ped-tabela td { display: flex; justify-content: space-between; border: none; padding: 6px 18px; }
}
</style>

<?php include_once '../../layouts/footer.php'; ?>
