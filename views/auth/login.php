<?php include_once '../layouts/header.php'; ?>

<main>
    <h1>Login</h1>
    
    <div id="mensagem-login"></div>
    
    <form id="form-login">
        <div>
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>
        </div>
        
        <div>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
        </div>
        
        <button type="submit">Entrar</button>
    </form>
</main>

<?php include_once '../layouts/footer.php'; ?>

<script src="../../assets/js/auth/login.js"></script>