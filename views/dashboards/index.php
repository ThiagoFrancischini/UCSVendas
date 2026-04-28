<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'FORNECEDOR') {
    header('Location: ' . BASE_URL . '/views/store/index.php');
    exit;
}
?>
<?php include_once '../layouts/header.php'; ?>

<main>
    <h1>Painel do Fornecedor</h1>
    
    <div class="dashboard-cards">
        <a href="produtos/index.php" class="dashboard-card">
            <h3>Meus Produtos</h3>
            <p>Gerencie seus produtos e estoque</p>
        </a>
        
        <a href="../perfil/index.php" class="dashboard-card">
            <h3>Perfil</h3>
            <p>Visualize e edite seus dados</p>
        </a>
    </div>
</main>

<?php include_once '../layouts/footer.php'; ?>

<style>
.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 30px;
}
.dashboard-card {
    display: block;
    padding: 30px;
    background-color: #f9fbfc;
    border-radius: 8px;
    text-decoration: none;
    transition: transform 0.2s, box-shadow 0.2s;
}
.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.dashboard-card h3 {
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 20px;
}
.dashboard-card p {
    color: #7f8c8d;
    margin: 0;
}
</style>