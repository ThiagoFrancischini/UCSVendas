<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../../config.php');
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'ADMIN') {
    header('Location: ' . BASE_URL . '/views/admin/login.php');
    exit;
}
?>
<?php include_once '../../layouts/header.php'; ?>

<main class="dash-main">
    <div class="page-header">
        <h1>Fornecedores</h1>
        <a href="../index.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Painel Admin</a>
    </div>

    <div class="filtro-section">
        <input type="text" id="filtro-busca" placeholder="Buscar por nome..." class="filtro-input">
    </div>

    <div id="msg-action" class="msg-action" style="display:none"></div>

    <div class="table-container">
        <table class="tabela-dados">
            <thead>
                <tr>
                    <th>Cód.</th>
                    <th>Nome</th>
                    <th>CNPJ</th>
                    <th>Telefone</th>
                    <th>Descrição</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody id="corpo-tabela">
                <tr><td colspan="6" style="text-align:center;padding:30px;color:#999">Carregando...</td></tr>
            </tbody>
        </table>
    </div>

    <div class="paginacao" id="paginacao"></div>
</main>

<!-- Modal de detalhes do fornecedor -->
<div id="modal-fornecedor" class="adm-modal" style="display:none">
    <div class="adm-modal-overlay" onclick="fecharModal()"></div>
    <div class="adm-modal-box">
        <div class="adm-modal-header">
            <h2><i class="fas fa-truck"></i> Detalhes do Fornecedor</h2>
            <button class="adm-modal-fechar" onclick="fecharModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="adm-modal-body" id="modal-corpo">
            <div style="text-align:center;padding:40px;color:#999">Carregando...</div>
        </div>
    </div>
</div>

<?php include_once '../../layouts/footer.php'; ?>

<style>
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
.btn-voltar { font-size:13px; color:#6366f1; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
.btn-voltar:hover { text-decoration:underline; }
.filtro-section { margin-bottom:20px; }
.filtro-input { width:100%; padding:12px; font-size:14px; border:1px solid #ddd; border-radius:4px; box-sizing:border-box; }
.filtro-input:focus { outline:none; border-color:#6366f1; }
.table-container { background:#fff; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,.05); overflow-x:auto; }
.tabela-dados { width:100%; border-collapse:collapse; text-align:left; }
.tabela-dados th { background:#2c3e50; color:#e2e8f0; padding:12px 16px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.6px; }
.tabela-dados td { padding:13px 16px; border-bottom:1px solid #f1f5f9; color:#334155; font-size:14px; vertical-align:middle; }
.tabela-dados tr:last-child td { border-bottom:none; }
.tabela-dados tr:hover { background:#f1faff; }
.btn-group { display:flex; gap:8px; justify-content:center; flex-wrap:nowrap; }
.btn-tabela { padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer; border:none; transition:.15s; display:inline-flex; align-items:center; gap:5px; white-space:nowrap; }
.btn-detalhe-sm { background:#6366f1; color:#fff; }
.btn-detalhe-sm:hover { background:#4f46e5; box-shadow:0 2px 6px rgba(99,102,241,.35); }
.btn-deletar-sm { background:#e74c3c; color:#fff; }
.btn-deletar-sm:hover { background:#c0392b; box-shadow:0 2px 6px rgba(231,76,60,.35); }
.paginacao { display:flex; justify-content:center; gap:8px; margin-top:20px; flex-wrap:wrap; }
.btn-pag { padding:8px 14px; border:1px solid #ddd; border-radius:4px; background:#fff; cursor:pointer; font-size:13px; }
.btn-pag:hover { background:#f0f0f0; }
.btn-pag.ativo { background:#6366f1; color:#fff; border-color:#6366f1; }
.msg-action { padding:12px 16px; border-radius:6px; margin-bottom:16px; }
.msg-action.erro { background:#fdf2f2; color:#c0392b; border:1px solid #f5c6c6; }
.msg-action.ok { background:#f0fff4; color:#27ae60; border:1px solid #b2dfdb; }

/* Modal */
.adm-modal { position:fixed; inset:0; z-index:1000; display:flex; align-items:center; justify-content:center; }
.adm-modal-overlay { position:absolute; inset:0; background:rgba(0,0,0,.45); }
.adm-modal-box { position:relative; background:#fff; border-radius:14px; width:100%; max-width:520px; max-height:90vh; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.adm-modal-header { background:#2c3e50; color:#fff; padding:18px 24px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
.adm-modal-header h2 { margin:0; font-size:16px; display:flex; align-items:center; gap:10px; }
.adm-modal-fechar { background:none; border:none; color:#fff; font-size:18px; cursor:pointer; padding:4px 8px; border-radius:6px; transition:background .15s; }
.adm-modal-fechar:hover { background:rgba(255,255,255,.15); }
.adm-modal-body { padding:24px; overflow-y:auto; }
.detalhe-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.detalhe-item { display:flex; flex-direction:column; gap:3px; }
.detalhe-item.full { grid-column:1/-1; }
.detalhe-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#94a3b8; }
.detalhe-valor { font-size:14px; color:#1e293b; font-weight:500; }
.detalhe-secao { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#6366f1; margin:20px 0 12px; padding-bottom:6px; border-bottom:1px solid #e2e8f0; grid-column:1/-1; }
.detalhe-stat { display:flex; align-items:center; gap:10px; background:#f1f5f9; border-radius:8px; padding:12px 16px; }
.detalhe-stat i { font-size:20px; color:#6366f1; }
.detalhe-stat-info strong { display:block; font-size:18px; color:#1e293b; }
.detalhe-stat-info span { font-size:12px; color:#64748b; }
</style>

<script>
var paginaAtual = 1;
var buscaAtual  = '';
var timeoutBusca;

window.BASE_URL = '<?php echo BASE_URL; ?>';

document.getElementById('filtro-busca').addEventListener('input', function() {
    clearTimeout(timeoutBusca);
    timeoutBusca = setTimeout(function() {
        buscaAtual = document.getElementById('filtro-busca').value.trim();
        paginaAtual = 1;
        carregar();
    }, 300);
});

function carregar(pag) {
    if (pag) paginaAtual = pag;
    fetch(window.BASE_URL + '/api/lista_fornecedores.php?busca=' + encodeURIComponent(buscaAtual) + '&pagina=' + paginaAtual)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var tbody = document.getElementById('corpo-tabela');
            if (!data.sucesso || !data.fornecedores.length) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#999">Nenhum fornecedor encontrado.</td></tr>';
                document.getElementById('paginacao').innerHTML = '';
                return;
            }
            tbody.innerHTML = data.fornecedores.map(function(f) {
                return '<tr>' +
                    '<td>' + f.id + '</td>' +
                    '<td><strong>' + esc(f.nome) + '</strong></td>' +
                    '<td>' + esc(f.cnpj) + '</td>' +
                    '<td>' + esc(f.telefone) + '</td>' +
                    '<td>' + esc(f.descricao) + '</td>' +
                    '<td style="text-align:center"><div class="btn-group">' +
                    '<button class="btn-tabela btn-detalhe-sm" onclick="abrirModal(' + f.id + ')"><i class="fas fa-eye"></i> Detalhes</button>' +
                    '<button class="btn-tabela btn-deletar-sm" onclick="deletar(' + f.id + ')"><i class="fas fa-trash"></i> Excluir</button>' +
                    '</div></td>' +
                    '</tr>';
            }).join('');
            renderPaginacao(data.paginas, data.pagina);
        });
}

function abrirModal(fornecedorId) {
    document.getElementById('modal-fornecedor').style.display = 'flex';
    document.getElementById('modal-corpo').innerHTML = '<div style="text-align:center;padding:40px;color:#999">Carregando...</div>';
    fetch(window.BASE_URL + '/api/detalhe_fornecedor.php?id=' + fornecedorId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.sucesso) {
                document.getElementById('modal-corpo').innerHTML = '<p style="color:#e74c3c">' + esc(data.mensagem) + '</p>';
                return;
            }
            var f = data.fornecedor;
            document.getElementById('modal-corpo').innerHTML =
                '<div class="detalhe-grid">' +
                    '<div class="detalhe-stat full">' +
                        '<i class="fas fa-box-open"></i>' +
                        '<div class="detalhe-stat-info"><strong>' + f.num_produtos + '</strong><span>Produto(s) cadastrados</span></div>' +
                    '</div>' +
                    '<div class="detalhe-secao">Dados do Fornecedor</div>' +
                    '<div class="detalhe-item full"><span class="detalhe-label">Nome</span><span class="detalhe-valor">' + esc(f.nome) + '</span></div>' +
                    '<div class="detalhe-item"><span class="detalhe-label">CNPJ</span><span class="detalhe-valor">' + esc(f.cnpj || '—') + '</span></div>' +
                    '<div class="detalhe-item"><span class="detalhe-label">Telefone</span><span class="detalhe-valor">' + esc(f.telefone || '—') + '</span></div>' +
                    '<div class="detalhe-item"><span class="detalhe-label">E-mail</span><span class="detalhe-valor">' + esc(f.email || '—') + '</span></div>' +
                    '<div class="detalhe-item full"><span class="detalhe-label">Descrição</span><span class="detalhe-valor">' + esc(f.descricao || '—') + '</span></div>' +
                    '<div class="detalhe-secao">Endereço</div>' +
                    '<div class="detalhe-item full"><span class="detalhe-label">Logradouro</span><span class="detalhe-valor">' + esc(f.endereco || '—') + '</span></div>' +
                    '<div class="detalhe-item"><span class="detalhe-label">Bairro</span><span class="detalhe-valor">' + esc(f.bairro || '—') + '</span></div>' +
                    '<div class="detalhe-item"><span class="detalhe-label">CEP</span><span class="detalhe-valor">' + esc(f.cep || '—') + '</span></div>' +
                    '<div class="detalhe-item"><span class="detalhe-label">Cidade</span><span class="detalhe-valor">' + esc(f.cidade || '—') + '</span></div>' +
                    '<div class="detalhe-item"><span class="detalhe-label">Estado</span><span class="detalhe-valor">' + esc(f.estado || '—') + '</span></div>' +
                '</div>';
        });
}

function fecharModal() {
    document.getElementById('modal-fornecedor').style.display = 'none';
}

function renderPaginacao(total, atual) {
    var div = document.getElementById('paginacao');
    if (total <= 1) { div.innerHTML = ''; return; }
    var html = '';
    for (var i = 1; i <= total; i++) {
        html += '<button class="btn-pag' + (i === atual ? ' ativo' : '') + '" onclick="carregar(' + i + ')">' + i + '</button>';
    }
    div.innerHTML = html;
}

function deletar(id) {
    if (!confirm('Excluir este fornecedor? Esta ação não pode ser desfeita.')) return;
    fetch(window.BASE_URL + '/api/deleta_fornecedor.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ fornecedor_id: id })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) { mostrarMsg(data.sucesso, data.sucesso ? 'Fornecedor excluído.' : (data.mensagem || 'Erro.')); });
}

function mostrarMsg(ok, texto) {
    var msg = document.getElementById('msg-action');
    msg.textContent = texto;
    msg.className = 'msg-action ' + (ok ? 'ok' : 'erro');
    msg.style.display = 'block';
    if (ok) carregar();
    setTimeout(function() { msg.style.display = 'none'; }, 4000);
}

function esc(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.addEventListener('keydown', function(e) { if (e.key === 'Escape') fecharModal(); });
document.addEventListener('DOMContentLoaded', carregar);
</script>
