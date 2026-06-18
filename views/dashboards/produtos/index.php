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
    <div class="page-header">
        <h1>Meus Produtos</h1>
        <a href="adicionar.php" class="btn-primary"><i class="fas fa-plus"></i> Adicionar Produto</a>
    </div>

    <div class="filtro-section">
        <input type="text" id="filtro-produtos" placeholder="Buscar por nome ou descrição..." class="filtro-input">
    </div>

    <div id="msg-produtos" class="msg-produtos" style="display:none"></div>

    <div class="table-container">
        <table class="tabela-produtos">
            <thead>
                <tr>
                    <th>Cód.</th>
                    <th>Nome</th>
                    <th>Descrição</th>
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

<?php include_once '../../layouts/footer.php'; ?>

<style>
.page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:30px; }
.btn-primary { background:#1abc9c; color:#fff; padding:12px 24px; border-radius:4px; text-decoration:none; font-weight:600; }
.btn-primary:hover { background:#16a085; color:#fff; }
.filtro-section { margin-bottom:20px; }
.filtro-input { width:100%; padding:12px; font-size:14px; border:1px solid #ddd; border-radius:4px; box-sizing:border-box; }
.filtro-input:focus { outline:none; border-color:#1abc9c; }
.table-container { background:#fff; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,.05); overflow-x:auto; }
.tabela-produtos { width:100%; border-collapse:collapse; text-align:left; }
.tabela-produtos th { background:#2c3e50; color:#e2e8f0; padding:12px 16px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.6px; }
.tabela-produtos td { padding:13px 16px; border-bottom:1px solid #f1f5f9; color:#334155; font-size:14px; vertical-align:middle; }
.tabela-produtos tr:last-child td { border-bottom:none; }
.tabela-produtos tr:hover { background:#f1faff; }
.col-id { width:60px; }
.col-nome { width:25%; color:#2d3436 !important; font-weight:600; }
.col-descricao { width:50%; }
.col-actions { width:15%; text-align:center; }
.btn-group { display:flex; gap:8px; justify-content:center; }
.btn-tabela { padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none; cursor:pointer; border:none; transition:.15s; display:inline-flex; align-items:center; gap:5px; }
.btn-editar-sm { background:#3498db; color:#fff; }
.btn-editar-sm:hover { background:#2980b9; box-shadow:0 2px 6px rgba(52,152,219,.35); }
.btn-deletar-sm { background:#e74c3c; color:#fff; }
.btn-deletar-sm:hover { background:#c0392b; box-shadow:0 2px 6px rgba(231,76,60,.35); }
.paginacao { display:flex; justify-content:center; gap:8px; margin-top:20px; flex-wrap:wrap; }
.btn-pag { padding:8px 14px; border:1px solid #ddd; border-radius:4px; background:#fff; cursor:pointer; font-size:13px; }
.btn-pag:hover { background:#f0f0f0; }
.btn-pag.ativo { background:#1abc9c; color:#fff; border-color:#1abc9c; }
.msg-produtos { padding:12px 16px; border-radius:6px; margin-bottom:16px; }
.msg-produtos.erro { background:#fdf2f2; color:#c0392b; border:1px solid #f5c6c6; }
.msg-produtos.ok { background:#f0fff4; color:#27ae60; border:1px solid #b2dfdb; }
</style>

<script>
var paginaAtual = 1;
var buscaAtual  = '';
var timeoutBusca;

window.BASE_URL = '<?php echo BASE_URL; ?>';

document.getElementById('filtro-produtos').addEventListener('input', function() {
    clearTimeout(timeoutBusca);
    timeoutBusca = setTimeout(function() {
        buscaAtual = document.getElementById('filtro-produtos').value.trim();
        paginaAtual = 1;
        carregarProdutos();
    }, 300);
});

function carregarProdutos(pag) {
    if (pag) paginaAtual = pag;
    fetch(window.BASE_URL + '/api/lista_produtos_admin.php?busca=' + encodeURIComponent(buscaAtual) + '&pagina=' + paginaAtual)
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('corpo-tabela');
            if (!data.sucesso || !data.produtos.length) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:30px;color:#999">Nenhum produto encontrado.</td></tr>';
                document.getElementById('paginacao').innerHTML = '';
                return;
            }
            tbody.innerHTML = data.produtos.map(p => `
                <tr>
                    <td class="col-id">${p.id}</td>
                    <td class="col-nome">${esc(p.nome)}</td>
                    <td class="col-descricao">${esc(p.descricao)}</td>
                    <td class="col-actions">
                        <div class="btn-group">
                            <a href="editar.php?id=${p.id}" class="btn-tabela btn-editar-sm"><i class="fas fa-pen"></i> Editar</a>
                            <button class="btn-tabela btn-deletar-sm" onclick="deletarProduto(${p.id})"><i class="fas fa-trash"></i> Deletar</button>
                        </div>
                    </td>
                </tr>`).join('');
            renderPaginacao(data.paginas, data.pagina);
        });
}

function renderPaginacao(total, atual) {
    const div = document.getElementById('paginacao');
    if (total <= 1) { div.innerHTML = ''; return; }
    let html = '';
    for (let i = 1; i <= total; i++) {
        html += `<button class="btn-pag${i === atual ? ' ativo' : ''}" onclick="carregarProdutos(${i})">${i}</button>`;
    }
    div.innerHTML = html;
}

function deletarProduto(id) {
    if (!confirm('Tem certeza que deseja deletar este produto?')) return;
    const fd = new FormData();
    fd.append('produto_id', id);
    fetch(window.BASE_URL + '/api/processa_produto.php?acao=deletar', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            const msg = document.getElementById('msg-produtos');
            if (data.sucesso) {
                msg.textContent = 'Produto excluído com sucesso.';
                msg.className = 'msg-produtos ok';
                carregarProdutos();
            } else {
                msg.textContent = data.mensagem || 'Erro ao excluir produto.';
                msg.className = 'msg-produtos erro';
            }
            msg.style.display = 'block';
            setTimeout(() => { msg.style.display = 'none'; }, 4000);
        });
}

function esc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.addEventListener('DOMContentLoaded', carregarProdutos);
</script>
