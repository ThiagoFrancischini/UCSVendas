<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'CLIENTE') {
    header('Location: ' . BASE_URL . '/views/store/index.php');
    exit;
}

$pedidoId = intval($_GET['pedido_id'] ?? 0);
if ($pedidoId <= 0) {
    header('Location: ' . BASE_URL . '/views/store/index.php');
    exit;
}

include_once '../layouts/header.php';
?>

<main class="confirmado-page">
    <div class="confirmado-box">
        <div class="confirmado-icone"><i class="fas fa-check"></i></div>
        <h1>Pedido realizado com sucesso!</h1>
        <p>Seu pedido <strong>#<?php echo $pedidoId; ?></strong> foi registrado.</p>
        <p class="confirmado-sub">Você pode acompanhar o status na área <em>Meus Pedidos</em>.</p>
        <div class="confirmado-acoes">
            <a href="<?php echo BASE_URL; ?>/views/store/meus_pedidos.php" class="btn-pedidos">Ver Meus Pedidos</a>
            <a href="<?php echo BASE_URL; ?>/views/store/index.php" class="btn-continuar">Continuar Comprando</a>
        </div>
    </div>
</main>

<style>
.confirmado-page { display: flex; justify-content: center; align-items: center; min-height: 60vh; padding: 20px; }
.confirmado-box { text-align: center; background: #fff; border: 1px solid #ececec; border-radius: 20px; padding: 60px 40px; box-shadow: 0 8px 32px rgba(0,0,0,0.06); max-width: 480px; width: 100%; }
.confirmado-icone { width: 72px; height: 72px; background: #2ecc71; border-radius: 50%; color: #fff; font-size: 36px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
.confirmado-box h1 { font-size: 24px; color: #2c3e50; margin-bottom: 12px; }
.confirmado-box p { color: #555; font-size: 15px; }
.confirmado-sub { color: #888; margin-top: 8px; font-size: 14px; }
.confirmado-acoes { display: flex; flex-direction: column; gap: 12px; margin-top: 32px; }
.btn-pedidos { padding: 12px 24px; background: #1abc9c; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 15px; }
.btn-pedidos:hover { background: #16a085; }
.btn-continuar { padding: 12px 24px; background: #f0f0f0; color: #333; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; }
.btn-continuar:hover { background: #e0e0e0; }
</style>

<?php include_once '../layouts/footer.php'; ?>
