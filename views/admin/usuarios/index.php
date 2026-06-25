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
        <h1>Usuários</h1>
        <a href="../index.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Painel Admin</a>
    </div>

    <div class="filtro-section">
        <input type="text" id="filtro-busca" placeholder="Buscar por e-mail..." class="filtro-input">
    </div>

    <div id="msg-action" class="msg-action" style="display:none"></div>

    <div class="table-container">
        <table class="tabela-dados">
            <thead>
                <tr>
                    <th>Cód.</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody id="corpo-tabela">
                <tr><td colspan="4" style="text-align:center;padding:30px;color:#999">Carregando...</td></tr>
            </tbody>
        </table>
    </div>

    <div class="paginacao" id="paginacao"></div>
</main>

<!-- Modal redefinir senha -->
<div id="modal-senha" class="adm-modal" style="display:none">
    <div class="adm-modal-overlay" onclick="fecharModal()"></div>
    <div class="adm-modal-box" style="max-width:420px">
        <div class="adm-modal-header">
            <h2><i class="fas fa-key"></i> Redefinir Senha</h2>
            <button class="adm-modal-fechar" onclick="fecharModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="adm-modal-body">
            <p id="modal-email-usuario" style="font-size:14px;color:#64748b;margin-bottom:20px;"></p>
            <label class="campo-label">Nova senha <span style="color:#94a3b8;font-size:11px">(mínimo 6 caracteres)</span></label>
            <div style="position:relative;margin-bottom:16px;">
                <input type="password" id="nova-senha" class="campo-input" placeholder="Digite a nova senha">
                <button type="button" onclick="toggleSenha()" class="btn-ver-senha" title="Mostrar/ocultar senha"><i class="fas fa-eye" id="icone-olho"></i></button>
            </div>
            <div id="msg-senha" class="msg-action" style="display:none;margin-bottom:12px;"></div>
            <button class="btn-salvar-senha" onclick="salvarSenha()"><i class="fas fa-check"></i> Salvar Nova Senha</button>
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
.badge-perfil { display:inline-block; padding:3px 10px; border-radius:12px; font-size:11px; font-weight:700; }
.badge-CLIENTE    { background:#dff0d8; color:#3c763d; }
.badge-FORNECEDOR { background:#d9edf7; color:#31708f; }
.badge-ADMIN      { background:#f3e8ff; color:#7c3aed; }
.btn-group { display:flex; gap:8px; justify-content:center; flex-wrap:nowrap; }
.btn-tabela { padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer; border:none; transition:.15s; display:inline-flex; align-items:center; gap:5px; white-space:nowrap; }
.btn-senha-sm { background:#f59e0b; color:#fff; }
.btn-senha-sm:hover { background:#d97706; box-shadow:0 2px 6px rgba(245,158,11,.35); }
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
.campo-label { display:block; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#64748b; margin-bottom:6px; }
.campo-input { width:100%; padding:11px 44px 11px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; box-sizing:border-box; }
.campo-input:focus { outline:none; border-color:#6366f1; }
.btn-ver-senha { position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; color:#94a3b8; cursor:pointer; padding:4px; font-size:15px; }
.btn-ver-senha:hover { color:#334155; }
.btn-salvar-senha { width:100%; padding:12px; background:#6366f1; color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; }
.btn-salvar-senha:hover { background:#4f46e5; }
</style>

<script>
var paginaAtual   = 1;
var buscaAtual    = '';
var timeoutBusca;
var usuarioAtivo  = null;

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
    fetch(window.BASE_URL + '/api/lista_usuarios.php?busca=' + encodeURIComponent(buscaAtual) + '&pagina=' + paginaAtual)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var tbody = document.getElementById('corpo-tabela');
            if (!data.sucesso || !data.usuarios.length) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:30px;color:#999">Nenhum usuário encontrado.</td></tr>';
                document.getElementById('paginacao').innerHTML = '';
                return;
            }
            var sessaoId = <?php echo (int)$_SESSION['usuario_id']; ?>;
            tbody.innerHTML = data.usuarios.map(function(u) {
                if (u.id === sessaoId) {
                    return '<tr>' +
                        '<td>' + u.id + '</td>' +
                        '<td>' + esc(u.email) + '</td>' +
                        '<td><span class="badge-perfil badge-' + esc(u.perfil) + '">' + esc(u.perfil) + '</span></td>' +
                        '<td style="text-align:center"><span style="color:#94a3b8;font-size:12px;">conta atual</span></td>' +
                        '</tr>';
                }
                return '<tr>' +
                    '<td>' + u.id + '</td>' +
                    '<td>' + esc(u.email) + '</td>' +
                    '<td><span class="badge-perfil badge-' + esc(u.perfil) + '">' + esc(u.perfil) + '</span></td>' +
                    '<td style="text-align:center"><div class="btn-group">' +
                    '<button class="btn-tabela btn-senha-sm" onclick="abrirModalSenha(' + u.id + ', \'' + esc(u.email) + '\')"><i class="fas fa-key"></i> Redefinir Senha</button>' +
                    '<button class="btn-tabela btn-deletar-sm" onclick="deletar(' + u.id + ')"><i class="fas fa-trash"></i> Excluir</button>' +
                    '</div></td>' +
                    '</tr>';
            }).join('');
            renderPaginacao(data.paginas, data.pagina);
        });
}

function abrirModalSenha(id, email) {
    usuarioAtivo = id;
    document.getElementById('modal-email-usuario').textContent = 'Usuário: ' + email;
    document.getElementById('nova-senha').value = '';
    document.getElementById('msg-senha').style.display = 'none';
    document.getElementById('modal-senha').style.display = 'flex';
    setTimeout(function() { document.getElementById('nova-senha').focus(); }, 100);
}

function fecharModal() {
    document.getElementById('modal-senha').style.display = 'none';
    usuarioAtivo = null;
}

function toggleSenha() {
    var input = document.getElementById('nova-senha');
    var icone = document.getElementById('icone-olho');
    if (input.type === 'password') {
        input.type = 'text';
        icone.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icone.className = 'fas fa-eye';
    }
}

function salvarSenha() {
    var senha = document.getElementById('nova-senha').value;
    var msgEl = document.getElementById('msg-senha');
    msgEl.style.display = 'none';

    if (!senha || senha.length < 6) {
        msgEl.textContent = 'A senha deve ter pelo menos 6 caracteres.';
        msgEl.className = 'msg-action erro';
        msgEl.style.display = 'block';
        return;
    }

    fetch(window.BASE_URL + '/api/redefine_senha.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ usuario_id: usuarioAtivo, nova_senha: senha })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.sucesso) {
            fecharModal();
            mostrarMsg(true, 'Senha redefinida com sucesso.');
        } else {
            msgEl.textContent = data.mensagem || 'Erro ao redefinir senha.';
            msgEl.className = 'msg-action erro';
            msgEl.style.display = 'block';
        }
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

function deletar(id) {
    if (!confirm('Excluir este usuário? Esta ação não pode ser desfeita.')) return;
    fetch(window.BASE_URL + '/api/deleta_usuario.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ usuario_id: id })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) { mostrarMsg(data.sucesso, data.sucesso ? 'Usuário excluído.' : (data.mensagem || 'Erro.')); });
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
