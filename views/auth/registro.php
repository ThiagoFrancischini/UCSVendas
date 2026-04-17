<?php include_once '../layouts/header.php'; ?>

<main>
    <h1>Criar Conta</h1>
    
    <div id="mensagem-registro"></div>
    
    <form id="form-registro">
        <h3>Dados de Acesso</h3>
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
        </div>
        
        <h3>Dados Pessoais</h3>
        <div class="form-group">
            <label for="nome">Nome Completo:</label>
            <input type="text" id="nome" name="nome" required>
        </div>
        <div class="form-group">
            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" required>
        </div>
        <div class="form-group">
            <label for="cartao">Cartão de Crédito:</label>
            <input type="text" id="cartao" name="cartao">
        </div>
        
        <h3>Endereço</h3>
        <div class="form-group">
            <label for="cep">CEP:</label>
            <input type="text" id="cep" name="cep" required>
        </div>
        <div class="form-group">
            <label for="rua">Rua:</label>
            <input type="text" id="rua" name="rua" required>
        </div>
        <div class="form-group">
            <label for="numero">Número:</label>
            <input type="text" id="numero" name="numero" required>
        </div>
        <div class="form-group">
            <label for="complemento">Complemento:</label>
            <input type="text" id="complemento" name="complemento">
        </div>
        <div class="form-group">
            <label for="bairro">Bairro:</label>
            <input type="text" id="bairro" name="bairro" required>
        </div>
        <div class="form-group">
            <label for="cidade">Cidade:</label>
            <input type="text" id="cidade" name="cidade" required>
        </div>
        <div class="form-group">
            <label for="estado">Estado:</label>
            <input type="text" id="estado" name="estado" required>
        </div>
        
<button type="submit">Cadastrar</button>
    </form>
</main>

<?php include_once '../layouts/footer.php'; ?>

<script src="../../assets/js/auth/registro.js"></script>