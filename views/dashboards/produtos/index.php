<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../../config.php');
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'FORNECEDOR') {
    header('Location: ' . BASE_URL . '/views/store/index.php');
    exit;
}

include_once(__DIR__ . '/../../../dao/postgres/PostgresDaoFactory.php');

$factory = new PostgresDaoFactory();
$fornecedorDao = $factory->getFornecedorDao();
$produtoDao = $factory->getProdutoDao();

$fornecedor = $fornecedorDao->buscaPorUsuarioId($_SESSION['usuario_id']);
$produtos = $produtoDao->buscaPorFornecedorId($fornecedor->getId());
?>
<?php include_once '../../layouts/header.php'; ?>

<main>
    <div class="page-header">
        <h1>Meus Produtos</h1>
        <a href="adicionar.php" class="btn-primary">+ Adicionar Produto</a>
    </div>
    
    <?php if (!empty($produtos)): ?>
        <div class="filtro-section">
            <input type="text" id="filtro-produtos" placeholder="Buscar por nome ou descrição..." class="filtro-input">
        </div>
    <?php endif; ?>
    
    <?php if (empty($produtos)): ?>
        <div class="empty-state">
            <p>Você ainda não tem produtos cadastrados.</p>
            <a href="adicionar.php" class="btn-link">Clique aqui para adicionar seu primeiro produto</a>
        </div>
    <?php else: ?>
        <div id="grid-produtos">
            <?php foreach ($produtos as $produto): ?>
                <div class="produto-card" data-nome="<?php echo strtolower(htmlspecialchars($produto->getNome())); ?>" data-descricao="<?php echo strtolower(htmlspecialchars($produto->getDescricao())); ?>">
                    <div class="produto-info">
                        <h3><?php echo htmlspecialchars($produto->getNome()); ?></h3>
                        <p><?php echo htmlspecialchars($produto->getDescricao()); ?></p>
                    </div>
                    <div class="produto-actions">
                        <a href="editar.php?id=<?php echo $produto->getId(); ?>" class="btn-editar">Editar</a>
                        <button class="btn-deletar" onclick="deletarProduto(<?php echo $produto->getId(); ?>)">Deletar</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php include_once '../../layouts/footer.php'; ?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}
.btn-primary {
    background-color: #1abc9c;
    color: white;
    padding: 12px 24px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
}
.btn-primary:hover {
    background-color: #16a085;
    color: white;
}
.filtro-section {
    margin-bottom: 20px;
}
.filtro-input {
    width: 100%;
    padding: 12px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}
.filtro-input:focus {
    outline: none;
    border-color: #1abc9c;
    box-shadow: 0 0 5px rgba(26, 188, 156, 0.3);
}
.empty-state {
    text-align: center;
    padding: 50px;
    background-color: #f9fbfc;
    border-radius: 8px;
}
.btn-link {
    color: #1abc9c;
    text-decoration: underline;
}
.produto-card {
    background-color: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.produto-card h3 {
    margin-bottom: 10px;
    font-size: 18px;
}
.produto-card p {
    color: #7f8c8d;
    font-size: 14px;
}
.produto-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #ecf0f1;
}
.btn-editar {
    flex: 1;
    padding: 10px;
    background-color: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    text-align: center;
    font-weight: 600;
    border: none;
    cursor: pointer;
    font-size: 14px;
}
.btn-editar:hover {
    background-color: #2980b9;
}
.btn-deletar {
    flex: 1;
    padding: 10px;
    background-color: #e74c3c;
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    cursor: pointer;
    font-size: 14px;
}
.btn-deletar:hover {
    background-color: #c0392b;
}
.produto-card.oculto {
    display: none;
}
</style>

<script>
function deletarProduto(produtoId) {
    if (confirm('Tem certeza que deseja deletar este produto?')) {
        $.ajax({
            url: '../../../api/processa_produto.php?acao=deletar',
            type: 'POST',
            dataType: 'json',
            data: { produto_id: produtoId },
            success: function(response) {
                if (response.sucesso) {
                    alert('Produto deletado com sucesso!');
                    location.reload();
                } else {
                    alert(response.mensagem);
                }
            },
            error: function() {
                alert('Erro ao deletar o produto.');
            }
        });
    }
}

$(document).ready(function() {
    $('#filtro-produtos').on('keyup', function() {
        var filtro = $(this).val().toLowerCase();
        
        if (filtro === '') {
            $('.produto-card').removeClass('oculto');
        } else {
            $('.produto-card').each(function() {
                var nome = $(this).data('nome');
                var descricao = $(this).data('descricao');
                
                if (nome.includes(filtro) || descricao.includes(filtro)) {
                    $(this).removeClass('oculto');
                } else {
                    $(this).addClass('oculto');
                }
            });
        }
    });
});
</script>