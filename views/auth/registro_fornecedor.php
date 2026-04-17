<?php include_once '../layouts/header.php'; ?>

<main>
    <h1>Seja um Fornecedor</h1>
    <p style="margin-bottom: 20px; color: #7f8c8d;">Cadastre sua empresa e comece a vender no UCS Vendas hoje mesmo.</p>
    
    <div id="mensagem-registro-forn"></div>
    
    <form id="form-registro-fornecedor">
        <h3>Dados de Acesso</h3>
        <div class="form-group">
            <label for="email">E-mail Corporativo:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
        </div>
        
        <h3>Dados da Empresa</h3>
        <div class="form-group">
            <label for="nome">Nome da Empresa:</label>
            <input type="text" id="nome" name="nome" required>
        </div>
        <div class="form-group">
            <label for="descricao">Descrição do Negócio:</label>
            <textarea id="descricao" name="descricao" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="telefone">Telefone / WhatsApp:</label>
            <input type="text" id="telefone" name="telefone" required>
        </div>
        <div class="form-group">
            <label for="cnpj">CNPJ:</label>
            <input type="text" id="cnpj" name="cnpj" required>
        </div>

        <h3>Endereço Sede</h3>
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
        
        <button type="submit">Cadastrar Empresa</button>
    </form>
</main>

<?php include_once '../layouts/footer.php'; ?>
<script src="../../assets/js/auth/registro_fornecedor.js"></script>