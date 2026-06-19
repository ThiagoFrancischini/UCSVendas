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

<main>
    <div class="page-header">
        <h1>Clientes</h1>
        <a href="../index.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Painel Admin</a>
    </div>

    <div class="filtro-section">
        <input type="text" id="filtro-busca" placeholder="Buscar por nome ou e-mail..." class="filtro-input">
    </div>

    <div id="msg-action" class="msg-action" style="display:none"></div>

    <div class="table-container">
        <table class="tabela-dados">
            <thead>
                <tr>
                    <th>Cód.</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody id="corpo-tabela">
                <tr><td colspan="5" style="text-align:center;padding:30px;color:#999">Carregando...</td></tr>
            </tbody>
        </table>
    </div>

    <div class="paginacao" id="paginacao"></div>
</main>

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
.btn-group { display:flex; gap:8px; justify-content:center; }
.btn-tabela { padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer; border:none; transition:.15s; display:inline-flex; align-items:center; gap:5px; }
.btn-deletar-sm { background:#e74c3c; color:#fff; }
.btn-deletar-sm:hover { background:#c0392b; box-shadow:0 2px 6px rgba(231,76,60,.35); }
.paginacao { display:flex; justify-content:center; gap:8px; margin-top:20px; flex-wrap:wrap; }
.btn-pag { padding:8px 14px; border:1px solid #ddd; border-radius:4px; background:#fff; cursor:pointer; font-size:13px; }
.btn-pag:hover { background:#f0f0f0; }
.btn-pag.ativo { background:#6366f1; color:#fff; border-color:#6366f1; }
.msg-action { padding:12px 16px; border-radius:6px; margin-bottom:16px; }
.msg-action.erro { background:#fdf2f2; color:#c0392b; border:1px solid #f5c6c6; }
.msg-action.ok { background:#f0fff4; color:#27ae60; border:1px solid #b2dfdb; }
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
    fetch(window.BASE_URL + '/api/lista_clientes.php?busca=' + encodeURIComponent(buscaAtual) + '&pagina=' + paginaAtual)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var tbody = document.getElementById('corpo-tabela');
            if (!data.sucesso || !data.clientes.length) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#999">Nenhum cliente encontrado.</td></tr>';
                document.getElementById('paginacao').innerHTML = '';
                return;
            }
            tbody.innerHTML = data.clientes.map(function(c) {
                return '<tr>' +
                    '<td>' + c.id + '</td>' +
                    '<td><strong>' + esc(c.nome) + '</strong></td>' +
                    '<td>' + esc(c.email) + '</td>' +
                    '<td>' + esc(c.telefone) + '</td>' +
                    '<td style="text-align:center"><div class="btn-group">' +
                    '<button class="btn-tabela btn-deletar-sm" onclick="deletar(' + c.usuario_id + ')"><i class="fas fa-trash"></i> Excluir</button>' +
                    '</div></td>' +
                    '</tr>';
            }).join('');
            renderPaginacao(data.paginas, data.pagina);
        });
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

function deletar(usuarioId) {
    if (!confirm('Excluir este cliente? Esta ação também removerá a conta de usuário e não pode ser desfeita.')) return;
    fetch(window.BASE_URL + '/api/deleta_usuario.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ usuario_id: usuarioId })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) { mostrarMsg(data.sucesso, data.sucesso ? 'Cliente excluído.' : (data.mensagem || 'Erro.')); });
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

document.addEventListener('DOMContentLoaded', carregar);
</script>
