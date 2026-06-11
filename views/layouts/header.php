<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UCS Vendas</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <a href="<?php echo BASE_URL; ?>/index.php" class="logo">UCS Vendas</a>
            <nav class="nav-links">
                <a href="<?php echo BASE_URL; ?>/views/store/carrinho.php" class="carrinho-link">&#128722; <span id="carrinho-badge" class="carrinho-badge" style="display:none">0</span></a>
                <?php if (isset($_SESSION['perfil'])): ?>
                    <?php if ($_SESSION['perfil'] === 'FORNECEDOR'): ?>
                        <a href="<?php echo BASE_URL; ?>/views/dashboards/index.php">Painel</a>
                        <a href="<?php echo BASE_URL; ?>/views/dashboards/produtos/index.php">Produtos</a>
                        <div class="dropdown">
                            <a href="javascript:void(0)" class="dropdown-toggle">Perfil ▾</a>
                            <div class="dropdown-menu">
                                <a href="<?php echo BASE_URL; ?>/views/perfil/index.php">Ver Perfil</a>
                                <a href="<?php echo BASE_URL; ?>/api/processa_logout.php" class="logout-link">Sair</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>/views/perfil/index.php">Minha Conta</a>
                        <a href="<?php echo BASE_URL; ?>/api/processa_logout.php">Sair</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/views/auth/registro_fornecedor.php">Vender</a>
                    <a href="<?php echo BASE_URL; ?>/views/auth/registro.php">Criar conta</a>
                    <a href="<?php echo BASE_URL; ?>/views/auth/login.php" class="btn-login">Entrar</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>