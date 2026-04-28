<?php
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
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <a href="../../index.php" class="logo">UCS Vendas</a>
            <nav class="nav-links">
                <?php if (isset($_SESSION['perfil'])): ?>
                    <?php if ($_SESSION['perfil'] === 'FORNECEDOR'): ?>
                        <a href="../dashboards/index.php">Meu Painel</a>
                    <?php else: ?>
                        <a href="../dashboards/index.php">Minhas Compras</a>
                    <?php endif; ?>
                    <a href="../../api/processa_logout.php">Sair</a>
                <?php else: ?>
                    <a href="../auth/registro_fornecedor.php">Venda na UCS Vendas</a>
                    <a href="../auth/registro.php">Criar conta</a>
                    <a href="../auth/login.php">Entrar</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>