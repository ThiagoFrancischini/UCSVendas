<?php
if (!defined('BASE_URL')) require_once(__DIR__ . '/../../config.php');
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'CLIENTE') {
    header('Location: ' . BASE_URL . '/views/store/index.php'); exit;
}

// Aceita ?pedidos=1,2,3 ou ?pedido_id=1 (legado)
$rawIds = $_GET['pedidos'] ?? ($_GET['pedido_id'] ?? '');
$pedidoIds = array_filter(array_map('intval', explode(',', $rawIds)));

if (empty($pedidoIds)) {
    die('<pre>DEBUG pagamento.php: pedidoIds está vazio. GET=' . print_r($_GET, true) . ' rawIds=' . var_export($rawIds, true) . '</pre>');
}

include_once(__DIR__ . '/../../dao/postgres/PostgresDaoFactory.php');
$factory = new PostgresDaoFactory();
$conn    = $factory->getConnection();

$placeholders = implode(',', array_fill(0, count($pedidoIds), '?'));
$clienteId = (int)$_SESSION['cliente_id'];
$params = array_merge(array_values($pedidoIds), [$clienteId]);

$stmt = $conn->prepare("
    SELECT p.id, p.valor_total, p.status FROM pedido p
    WHERE p.id IN ($placeholders) AND p.cliente_id = ?
");
$stmt->execute($params);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($pedidos)) {
    // Verificar sem filtro de cliente para diagnóstico
    $stmtRaw = $conn->prepare("SELECT p.id, p.cliente_id, p.status FROM pedido p WHERE p.id IN ($placeholders)");
    $stmtRaw->execute(array_values($pedidoIds));
    $pedidosRaw = $stmtRaw->fetchAll(PDO::FETCH_ASSOC);
    die('<pre>DEBUG pagamento.php: query retornou vazio.
pedidoIds: ' . print_r($pedidoIds, true) . '
cliente_id da sessão: ' . $clienteId . '
pedidos sem filtro de cliente: ' . print_r($pedidosRaw, true) . '
SESSION: perfil=' . ($_SESSION['perfil'] ?? 'N/A') . ' cliente_id=' . ($_SESSION['cliente_id'] ?? 'N/A') . '
</pre>');
}

// Se todos já pagos, vai pra confirmação do primeiro
$pendentes = array_filter($pedidos, fn($p) => $p['status'] === 'AGUARDANDO_PAGAMENTO');
if (empty($pendentes)) {
    header('Location: ' . BASE_URL . '/views/store/pedido_confirmado.php?pedidos=' . implode(',', $pedidoIds)); exit;
}

$valorTotal = array_sum(array_column($pedidos, 'valor_total'));
$valorFormatado = number_format($valorTotal, 2, ',', '.');
$numPedidos = count($pedidoIds);
$idsLabel = count($pedidoIds) === 1
    ? '#' . $pedidoIds[array_key_first($pedidoIds)]
    : implode(', #', $pedidoIds);

$pixCode = 'PIX-UCSVENDAS-PEDIDOS-' . implode('-', $pedidoIds) . '-R$' . $valorFormatado;

include_once '../layouts/header.php';
?>

<main class="pagamento-page">
    <div class="pagamento-box">
        <div class="pagamento-header">
            <div class="pag-icone"><i class="fas fa-qrcode"></i></div>
            <h1>Pagamento via Pix</h1>
            <p class="pag-sub">Escaneie o QR Code abaixo para pagar</p>
        </div>

        <div class="pag-valor">
            <div>
                <span class="pag-valor-label">
                    <?php if ($numPedidos > 1): ?>
                        <?php echo $numPedidos; ?> pedidos: <strong>#<?php echo $idsLabel; ?></strong>
                    <?php else: ?>
                        Pedido <strong>#<?php echo $idsLabel; ?></strong>
                    <?php endif; ?>
                </span>
            </div>
            <span class="pag-valor-num">R$ <?php echo $valorFormatado; ?></span>
        </div>

        <?php if ($numPedidos > 1): ?>
        <div class="pag-breakdown">
            <?php foreach ($pedidos as $p): ?>
            <div class="pag-breakdown-item">
                <span>Pedido #<?php echo $p['id']; ?></span>
                <span>R$ <?php echo number_format($p['valor_total'], 2, ',', '.'); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="pag-qr-wrapper">
            <img
                src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=<?php echo urlencode($pixCode); ?>"
                alt="QR Code Pix"
                class="pag-qr"
                width="220" height="220"
            >
            <p class="pag-qr-hint">Pagamento simulado para fins de demonstração</p>
        </div>

        <div class="pag-codigo">
            <span class="pag-codigo-label">Código Pix</span>
            <div class="pag-codigo-box">
                <code id="pix-code"><?php echo htmlspecialchars($pixCode); ?></code>
                <button class="btn-copiar" onclick="copiarCodigo()" title="Copiar código"><i class="fas fa-copy"></i></button>
            </div>
            <span id="msg-copiado" class="msg-copiado">Copiado!</span>
        </div>

        <div id="msg-pagamento" class="msg-pagamento" style="display:none"></div>

        <button class="btn-confirmar-pag" id="btn-confirmar" onclick="confirmarPagamento()">
            <i class="fas fa-check-circle"></i> Pagamento Confirmado
        </button>

        <a href="<?php echo BASE_URL; ?>/views/store/carrinho.php" class="btn-cancelar-pag">
            Cancelar e voltar ao carrinho
        </a>
    </div>
</main>

<script>
window.BASE_URL   = '<?php echo BASE_URL; ?>';
window.PEDIDO_IDS = <?php echo json_encode(array_values($pedidoIds)); ?>;

function confirmarPagamento() {
    var btn = document.getElementById('btn-confirmar');
    var msg = document.getElementById('msg-pagamento');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
    msg.style.display = 'none';

    fetch(window.BASE_URL + '/api/confirma_pagamento.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pedido_ids: window.PEDIDO_IDS })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.sucesso) {
            window.location.href = window.BASE_URL + '/views/store/pedido_confirmado.php?pedidos=' + window.PEDIDO_IDS.join(',');
        } else {
            msg.textContent = data.mensagem || 'Erro ao confirmar pagamento.';
            msg.className = 'msg-pagamento erro';
            msg.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Pagamento Confirmado';
        }
    })
    .catch(function() {
        msg.textContent = 'Erro de conexão. Tente novamente.';
        msg.className = 'msg-pagamento erro';
        msg.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i> Pagamento Confirmado';
    });
}

function copiarCodigo() {
    navigator.clipboard.writeText(document.getElementById('pix-code').textContent).then(function() {
        var msg = document.getElementById('msg-copiado');
        msg.style.opacity = '1';
        setTimeout(function() { msg.style.opacity = '0'; }, 1800);
    });
}
</script>

<style>
.pagamento-page { display:flex; justify-content:center; align-items:flex-start; padding:40px 20px; min-height:70vh; }
.pagamento-box { background:#fff; border:1px solid #e2e8f0; border-radius:20px; padding:40px 36px; max-width:460px; width:100%; box-shadow:0 8px 32px rgba(0,0,0,.07); display:flex; flex-direction:column; align-items:center; gap:24px; }
.pagamento-header { text-align:center; }
.pag-icone { width:64px; height:64px; background:#f0fdf4; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:28px; color:#1abc9c; margin:0 auto 16px; }
.pagamento-header h1 { font-size:22px; color:#1e293b; margin:0 0 6px; }
.pag-sub { color:#64748b; font-size:14px; margin:0; }
.pag-valor { width:100%; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px 20px; display:flex; justify-content:space-between; align-items:center; }
.pag-valor-label { font-size:13px; color:#64748b; }
.pag-valor-num { font-size:22px; font-weight:800; color:#1abc9c; white-space:nowrap; margin-left:12px; }
.pag-breakdown { width:100%; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; }
.pag-breakdown-item { display:flex; justify-content:space-between; padding:9px 16px; font-size:13px; color:#475569; border-bottom:1px solid #e2e8f0; }
.pag-breakdown-item:last-child { border-bottom:none; }
.pag-qr-wrapper { display:flex; flex-direction:column; align-items:center; gap:10px; }
.pag-qr { border:3px solid #e2e8f0; border-radius:12px; display:block; }
.pag-qr-hint { font-size:11px; color:#94a3b8; text-align:center; margin:0; }
.pag-codigo { width:100%; display:flex; flex-direction:column; gap:6px; }
.pag-codigo-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#94a3b8; }
.pag-codigo-box { display:flex; align-items:center; gap:8px; background:#f1f5f9; border-radius:8px; padding:10px 14px; }
.pag-codigo-box code { flex:1; font-size:11px; color:#475569; word-break:break-all; }
.btn-copiar { background:none; border:none; color:#6366f1; cursor:pointer; font-size:15px; padding:4px; flex-shrink:0; }
.btn-copiar:hover { color:#4f46e5; }
.msg-copiado { font-size:12px; color:#27ae60; font-weight:600; opacity:0; transition:opacity .3s; }
.msg-pagamento { width:100%; padding:12px 16px; border-radius:8px; font-size:14px; }
.msg-pagamento.erro { background:#fdf2f2; color:#c0392b; border:1px solid #f5c6c6; }
.btn-confirmar-pag { width:100%; padding:15px; background:#27ae60; color:#fff; border:none; border-radius:10px; font-size:16px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; transition:background .2s; }
.btn-confirmar-pag:hover:not(:disabled) { background:#219a52; box-shadow:0 4px 14px rgba(39,174,96,.35); }
.btn-confirmar-pag:disabled { background:#94a3b8; cursor:not-allowed; }
.btn-cancelar-pag { font-size:13px; color:#94a3b8; text-decoration:none; }
.btn-cancelar-pag:hover { color:#64748b; text-decoration:underline; }
</style>

<?php include_once '../layouts/footer.php'; ?>
