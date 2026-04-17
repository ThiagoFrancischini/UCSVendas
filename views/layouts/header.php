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
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="../dashboard/index.php">Meu Painel</a>
                    <a href="../../api/processa_logout.php">Sair</a>
                <?php else: ?>
                    <a href="../auth/registro.php">Criar conta</a>
                    <a href="../auth/login.php">Entrar</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>