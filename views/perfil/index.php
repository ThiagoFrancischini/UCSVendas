<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . '/views/auth/login.php');
    exit;
}

$perfil = $_SESSION['perfil'];
$usuarioId = $_SESSION['usuario_id'];

include_once(__DIR__ . '/../../dao/postgres/PostgresDaoFactory.php');

$factory = new PostgresDaoFactory();
$usuarioDao = $factory->getUsuarioDao();
$enderecoDao = $factory->getEnderecoDao();

$usuario = $usuarioDao->buscaPorId($usuarioId);
$dados = [
    'email' => $usuario->getEmail(),
    'nome' => '',
    'telefone' => '',
    'cnpj' => '',
    'cartao' => '',
    'endereco' => null
];

if ($perfil === 'FORNECEDOR') {
    $fornecedorDao = $factory->getFornecedorDao();
    $fornecedor = $fornecedorDao->buscaPorUsuarioId($usuarioId);
    if ($fornecedor) {
        $dados['nome'] = $fornecedor->getNome();
        $dados['telefone'] = $fornecedor->getTelefone();
        $dados['cnpj'] = $fornecedor->getCnpj();
        $endereco = $enderecoDao->buscaPorId($fornecedor->getEnderecoId());
        $dados['endereco'] = $endereco;
    }
} else {
    $clienteDao = $factory->getClienteDao();
    $cliente = $clienteDao->buscaPorUsuarioId($usuarioId);
    if ($cliente) {
        $dados['nome'] = $cliente->getNome();
        $dados['telefone'] = $cliente->getTelefone();
        $dados['cartao'] = $cliente->getCartaoCredito();
        $endereco = $enderecoDao->buscaPorId($cliente->getEnderecoId());
        $dados['endereco'] = $endereco;
    }
}
?>
<?php include_once '../layouts/header.php'; ?>

<main>
    <div class="page-header">
        <h1>Meu Perfil</h1>
        <button id="btn-editar" class="btn-editar">Editar Perfil</button>
    </div>

    <div id="mensagem-perfil"></div>
    
    <div id="visualizacao-perfil">
        <section class="perfil-dados">
            <h3><?php echo $perfil === 'FORNECEDOR' ? 'Dados da Empresa' : 'Dados Pessoais'; ?></h3>
            
            <div class="info-group">
                <label><?php echo $perfil === 'FORNECEDOR' ? 'Nome da Empresa' : 'Nome'; ?>:</label>
                <p><?php echo htmlspecialchars($dados['nome']); ?></p>
            </div>
            
            <div class="info-group">
                <label>E-mail:</label>
                <p><?php echo htmlspecialchars($dados['email']); ?></p>
            </div>
            
            <div class="info-group">
                <label>Telefone:</label>
                <p><?php echo htmlspecialchars($dados['telefone']); ?></p>
            </div>
            
            <?php if ($perfil === 'FORNECEDOR'): ?>
            <div class="info-group">
                <label>CNPJ:</label>
                <p><?php echo htmlspecialchars($dados['cnpj']); ?></p>
            </div>
            <?php else: ?>
            <div class="info-group">
                <label>Cartão de Crédito:</label>
                <p><?php echo htmlspecialchars($dados['cartao']); ?></p>
            </div>
            <?php endif; ?>
        </section>
        
        <?php if ($dados['endereco']): ?>
        <section class="perfil-endereco">
            <h3>Endereço</h3>
            
            <div class="info-group">
                <label>Rua:</label>
                <p><?php echo htmlspecialchars($dados['endereco']->getRua()); ?>, <?php echo htmlspecialchars($dados['endereco']->getNumero()); ?></p>
            </div>
            
            <?php if ($dados['endereco']->getComplemento()): ?>
            <div class="info-group">
                <label>Complemento:</label>
                <p><?php echo htmlspecialchars($dados['endereco']->getComplemento()); ?></p>
            </div>
            <?php endif; ?>
            
            <div class="info-group">
                <label>Bairro:</label>
                <p><?php echo htmlspecialchars($dados['endereco']->getBairro()); ?></p>
            </div>
            
            <div class="info-group">
                <label>Cidade / Estado:</label>
                <p><?php echo htmlspecialchars($dados['endereco']->getCidade()); ?> / <?php echo htmlspecialchars($dados['endereco']->getEstado()); ?></p>
            </div>
            
            <div class="info-group">
                <label>CEP:</label>
                <p><?php echo htmlspecialchars($dados['endereco']->getCep()); ?></p>
            </div>
        </section>
        <?php endif; ?>
    </div>

    <div id="edicao-perfil" style="display: none;">
        <form id="form-perfil">
            <section class="perfil-dados">
                <h3><?php echo $perfil === 'FORNECEDOR' ? 'Editar Dados da Empresa' : 'Editar Dados Pessoais'; ?></h3>
                
                <div class="form-group">
                    <label for="nome"><?php echo $perfil === 'FORNECEDOR' ? 'Nome da Empresa' : 'Nome'; ?>:</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($dados['nome']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="telefone">Telefone:</label>
                    <input type="tel" id="telefone" name="telefone" value="<?php echo htmlspecialchars($dados['telefone']); ?>" required>
                </div>
                
                <?php if ($perfil === 'FORNECEDOR'): ?>
                <div class="form-group">
                    <label for="cnpj">CNPJ:</label>
                    <input type="text" id="cnpj" name="cnpj" value="<?php echo htmlspecialchars($dados['cnpj']); ?>" required>
                </div>
                <?php endif; ?>
            </section>
            
            <?php if ($dados['endereco']): ?>
            <section class="perfil-endereco">
                <h3>Editar Endereço</h3>
                
                <div class="form-group">
                    <label for="rua">Rua:</label>
                    <input type="text" id="rua" name="rua" value="<?php echo htmlspecialchars($dados['endereco']->getRua()); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="numero">Número:</label>
                    <input type="text" id="numero" name="numero" value="<?php echo htmlspecialchars($dados['endereco']->getNumero()); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="complemento">Complemento:</label>
                    <input type="text" id="complemento" name="complemento" value="<?php echo htmlspecialchars($dados['endereco']->getComplemento() ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="bairro">Bairro:</label>
                    <input type="text" id="bairro" name="bairro" value="<?php echo htmlspecialchars($dados['endereco']->getBairro()); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="cidade">Cidade:</label>
                    <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($dados['endereco']->getCidade()); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="estado">Estado:</label>
                    <input type="text" id="estado" name="estado" value="<?php echo htmlspecialchars($dados['endereco']->getEstado()); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="cep">CEP:</label>
                    <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($dados['endereco']->getCep()); ?>" required>
                </div>
            </section>
            <?php endif; ?>
            
            <div class="form-actions">
                <button type="submit">Salvar Alterações</button>
                <button type="button" id="btn-cancelar" class="btn-cancel">Cancelar</button>
            </div>
        </form>
    </div>
</main>

<?php include_once '../layouts/footer.php'; ?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.btn-editar {
    background-color: #3498db;
    color: white;
    padding: 8px 12px;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    font-size: 13px;
}
.btn-editar:hover {
    background-color: #2980b9;
}
.perfil-dados, .perfil-endereco {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}
.perfil-dados h3, .perfil-endereco h3 {
    margin-top: 0;
    color: #2c3e50;
    font-size: 18px;
}
.info-group {
    margin-bottom: 15px;
}
.info-group label {
    display: block;
    font-weight: bold;
    color: #34495e;
    margin-bottom: 5px;
}
.info-group p {
    color: #7f8c8d;
    margin: 0;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    font-weight: bold;
    color: #34495e;
    margin-bottom: 5px;
}
.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}
.form-group input:focus {
    outline: none;
    border-color: #1abc9c;
    box-shadow: 0 0 5px rgba(26, 188, 156, 0.3);
}
.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}
.form-actions button {
    flex: 1;
    padding: 12px 20px;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    font-size: 14px;
}
.form-actions button[type="submit"] {
    background-color: #1abc9c;
    color: white;
    border: none;
}
.form-actions button[type="submit"]:hover {
    background-color: #16a085;
}
.btn-cancel {
    background-color: #95a5a6;
    color: white;
    border: none;
}
.btn-cancel:hover {
    background-color: #7f8c8d;
}
.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}
.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<script>
$(document).ready(function() {
    $('#btn-editar').on('click', function() {
        $('#visualizacao-perfil').hide();
        $('#edicao-perfil').show();
        $('#btn-editar').hide();
    });

    $('#btn-cancelar').on('click', function() {
        $('#edicao-perfil').hide();
        $('#visualizacao-perfil').show();
        $('#btn-editar').show();
        $('#mensagem-perfil').removeClass('alert alert-success alert-error').hide().text('');
    });

    $('#form-perfil').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var mensagemDiv = $('#mensagem-perfil');
        var botao = form.find('button[type="submit"]');
        var textoOriginal = botao.text();

        mensagemDiv.removeClass('alert-error alert-success').hide().text('');
        $('.form-group').removeClass('has-error');

        botao.prop('disabled', true).text('Salvando...');

        $.ajax({
            url: '../../api/processa_perfil.php',
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            success: function(response) {
                if (response.sucesso) {
                    mensagemDiv.addClass('alert alert-success').text('Perfil atualizado com sucesso!').show();
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    mensagemDiv.addClass('alert alert-error').text(response.mensagem).show();
                    $('html, body').scrollTop(0);
                }
            },
            error: function() {
                mensagemDiv.addClass('alert alert-error').text('Erro de comunicação com o servidor.').show();
                $('html, body').scrollTop(0);
            },
            complete: function() {
                botao.prop('disabled', false).text(textoOriginal);
            }
        });
    });
});
</script>