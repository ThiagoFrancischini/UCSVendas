<?php
/**
 * Seed: cria um usuário ADMIN no banco se não existir.
 * Acesse via: http://localhost/UCSVendas/UCSVendas/tests/seed_admin.php
 */

require_once(__DIR__ . '/../dao/postgres/PostgresDaoFactory.php');

$email = 'admin@ucsvendas.com';
$senha = 'Admin@123';

$factory = new PostgresDaoFactory();
$conn    = $factory->getConnection();

// Verifica se já existe algum ADMIN
$stmtCheck = $conn->prepare("SELECT id, email FROM usuario WHERE perfil = 'ADMIN' LIMIT 1");
$stmtCheck->execute();
$existente = $stmtCheck->fetch(PDO::FETCH_ASSOC);

$criado = false;
$erro   = null;

if (!$existente) {
    // Verifica se o email já está em uso (pode ser CLIENTE ou FORNECEDOR com esse email)
    $stmtEmail = $conn->prepare("SELECT id FROM usuario WHERE email = ?");
    $stmtEmail->execute([$email]);
    if ($stmtEmail->fetch()) {
        $erro = "O e-mail '$email' já está cadastrado com outro perfil. Altere a variável \$email neste script.";
    } else {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmtInsert = $conn->prepare("INSERT INTO usuario (email, senha, perfil) VALUES (?, ?, 'ADMIN')");
        $stmtInsert->execute([$email, $hash]);
        $criado = true;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Seed Admin — UCS Vendas</title>
    <style>
    body { font-family: sans-serif; background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
    .box { background: #fff; border-radius: 12px; padding: 36px 40px; max-width: 480px; width: 100%; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
    h1 { font-size: 20px; margin: 0 0 20px; color: #1e293b; }
    .status { padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; }
    .status.ok      { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
    .status.info    { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
    .status.erro    { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    table { width: 100%; border-collapse: collapse; font-size: 14px; }
    td { padding: 8px 0; }
    td:first-child { color: #64748b; width: 100px; }
    td:last-child { font-weight: 600; color: #1e293b; }
    .btn { display: inline-block; margin-top: 24px; padding: 10px 20px; background: #6366f1; color: #fff; border-radius: 7px; text-decoration: none; font-weight: 600; font-size: 14px; }
    .btn:hover { background: #4f46e5; }
    </style>
</head>
<body>
<div class="box">
    <h1>🛡️ Seed — Usuário Admin</h1>

    <?php if ($erro): ?>
        <div class="status erro">❌ Erro: <?php echo htmlspecialchars($erro); ?></div>
    <?php elseif ($criado): ?>
        <div class="status ok">✅ Administrador criado com sucesso!</div>
        <table>
            <tr><td>E-mail</td><td><?php echo htmlspecialchars($email); ?></td></tr>
            <tr><td>Senha</td><td><?php echo htmlspecialchars($senha); ?></td></tr>
            <tr><td>Perfil</td><td>ADMIN</td></tr>
        </table>
    <?php else: ?>
        <div class="status info">ℹ️ Administrador já existe no banco.</div>
        <table>
            <tr><td>ID</td><td><?php echo (int)$existente['id']; ?></td></tr>
            <tr><td>E-mail</td><td><?php echo htmlspecialchars($existente['email']); ?></td></tr>
            <tr><td>Perfil</td><td>ADMIN</td></tr>
        </table>
    <?php endif; ?>

    <?php if (!$erro): ?>
        <a href="../views/admin/login.php" class="btn">Ir para o Login Admin →</a>
    <?php endif; ?>
</div>
</body>
</html>
