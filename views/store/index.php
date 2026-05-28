<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}
include_once '../layouts/header.php';
?>

<main class="vitrine">
    <div class="page-header">
        <div>
            <h2>Vitrine</h2>
            <p>Conheça os produtos disponíveis de nossos fornecedores.</p>
        </div>
        <div class="search-wrapper">
            <input type="text" id="filtro-loja" placeholder="Buscar por nome ou descrição...">
        </div>
    </div>

    <div id="grid-produtos" class="grid-produtos"></div>
    <div id="mensagem-loja" class="mensagem-loja"></div>
</main>

<script>
    window.BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>/assets/js/store/index.js"></script>

<style>
.vitrine {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

.page-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
}

.page-header h2 {
    margin: 0 0 8px;
    font-size: 32px;
}

.page-header p {
    margin: 0;
    color: #555;
}

.search-wrapper {
    width: 100%;
    max-width: 360px;
}

#filtro-loja {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
}

.grid-produtos {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
}

.produto-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
    min-height: 100%;
}

.produto-imagem {
    width: 100%;
    min-height: 220px;
    background-size: cover;
    background-position: center;
    background-color: #f4f4f4;
}

.produto-conteudo {
    flex: 1;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.produto-conteudo h3 {
    margin: 0;
    font-size: 20px;
    color: #222;
}

.produto-conteudo p {
    margin: 0;
    color: #555;
    line-height: 1.6;
    min-height: 56px;
}

.produto-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-top: auto;
    padding-top: 12px;
    border-top: 1px solid #f0f0f0;
    color: #333;
    font-weight: 600;
}

.mensagem-loja {
    margin-top: 24px;
    font-size: 16px;
    color: #666;
}

@media (max-width: 640px) {
    .page-header {
        align-items: flex-start;
    }
}
</style>

<?php include_once '../layouts/footer.php'; ?>