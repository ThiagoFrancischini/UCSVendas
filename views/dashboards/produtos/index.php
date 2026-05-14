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
        <div class="table-container">
            <table class="tabela-produtos">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="corpo-tabela">
                    <?php foreach ($produtos as $produto): ?>
                        <tr class="produto-row" 
                            data-nome="<?php echo strtolower(htmlspecialchars($produto->getNome())); ?>" 
                            data-descricao="<?php echo strtolower(htmlspecialchars($produto->getDescricao())); ?>">
                            
                            <td class="col-nome">
                                <strong><?php echo htmlspecialchars($produto->getNome()); ?></strong>
                            </td>
                            <td class="col-descricao">
                                <?php echo htmlspecialchars($produto->getDescricao()); ?>
                            </td>
                            <td class="col-actions">
                                <div class="btn-group">
                                    <a href="editar.php?id=<?php echo $produto->getId(); ?>" class="btn-tabela btn-editar-sm">Editar</a>
                                    <button class="btn-tabela btn-deletar-sm" onclick="deletarProduto(<?php echo $produto->getId(); ?>)">Deletar</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
.table-container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow-x: auto; /* Garante scroll em telas pequenas */
}

.tabela-produtos {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

.tabela-produtos th {
    background-color: #f8f9fa;
    padding: 15px;
    border-bottom: 2px solid #edf2f7;
    color: #2d3436;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

.tabela-produtos td {
    padding: 15px;
    border-bottom: 1px solid #edf2f7;
    color: #636e72;
    font-size: 14px;
    vertical-align: middle;
}

.produto-row:hover {
    background-color: #f1faff;
}

/* Colunas específicas */
.col-nome { width: 30%; color: #2d3436 !important; }
.col-descricao { width: 50%; }
.col-actions { width: 20%; text-align: center; }

/* Botões menores para a tabela */
.btn-group {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.btn-tabela {
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: 0.2s;
}

.btn-editar-sm { background-color: #3498db; color: white; }
.btn-editar-sm:hover { background-color: #2980b9; }

.btn-deletar-sm { background-color: #e74c3c; color: white; }
.btn-deletar-sm:hover { background-color: #c0392b; }

.produto-row.oculto {
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
            $('.produto-row').removeClass('oculto');
        } else {
            $('.produto-row').each(function() {
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