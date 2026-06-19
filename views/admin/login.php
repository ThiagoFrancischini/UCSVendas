<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['perfil']) && $_SESSION['perfil'] === 'ADMIN') {
    header('Location: ' . BASE_URL . '/views/admin/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo — UCS Vendas</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    body { background: #1e293b; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
    .admin-login-box { background: #fff; border-radius: 14px; padding: 40px 36px; width: 100%; max-width: 400px; box-shadow: 0 8px 32px rgba(0,0,0,.25); }
    .admin-login-box h1 { font-size: 22px; color: #1e293b; margin: 0 0 6px; }
    .admin-login-box p.sub { color: #64748b; font-size: 13px; margin: 0 0 28px; }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .form-group input { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 7px; font-size: 14px; box-sizing: border-box; }
    .form-group input:focus { outline: none; border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.15); }
    .btn-admin { width: 100%; padding: 11px; background: #6366f1; color: #fff; border: none; border-radius: 7px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 4px; }
    .btn-admin:hover { background: #4f46e5; }
    .btn-admin:disabled { background: #a5b4fc; cursor: not-allowed; }
    #msg { margin-top: 16px; padding: 10px 14px; border-radius: 6px; font-size: 13px; display: none; }
    #msg.erro { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .logo-admin { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; }
    .logo-admin i { font-size: 26px; color: #6366f1; }
    .logo-admin span { font-size: 18px; font-weight: 700; color: #1e293b; }
    </style>
</head>
<body>
    <div class="admin-login-box">
        <div class="logo-admin">
            <i class="fas fa-shield-halved"></i>
            <span>UCS Vendas Admin</span>
        </div>
        <h1>Acesso Administrativo</h1>
        <p class="sub">Área restrita. Somente administradores.</p>

        <form id="form-admin-login">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="admin@ucsvendas.com" required autofocus>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-admin" id="btn-entrar">Entrar</button>
        </form>
        <div id="msg"></div>
    </div>

    <script>
    var BASE_URL = '<?php echo BASE_URL; ?>';

    document.getElementById('form-admin-login').addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = document.getElementById('btn-entrar');
        var msg = document.getElementById('msg');
        btn.disabled = true;
        btn.textContent = 'Entrando...';
        msg.style.display = 'none';

        var formData = new FormData();
        formData.append('email', document.getElementById('email').value);
        formData.append('senha', document.getElementById('senha').value);

        fetch(BASE_URL + '/api/processa_login.php', { method: 'POST', body: formData })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.sucesso && data.perfil === 'ADMIN') {
                    window.location.href = BASE_URL + '/views/admin/index.php';
                } else if (data.sucesso) {
                    msg.textContent = 'Acesso negado — esta conta não é um administrador.';
                    msg.className = 'erro';
                    msg.style.display = 'block';
                    btn.disabled = false;
                    btn.textContent = 'Entrar';
                } else {
                    msg.textContent = data.mensagem || 'E-mail ou senha incorretos.';
                    msg.className = 'erro';
                    msg.style.display = 'block';
                    btn.disabled = false;
                    btn.textContent = 'Entrar';
                }
            })
            .catch(function() {
                msg.textContent = 'Erro de comunicação com o servidor.';
                msg.className = 'erro';
                msg.style.display = 'block';
                btn.disabled = false;
                btn.textContent = 'Entrar';
            });
    });
    </script>
</body>
</html>
