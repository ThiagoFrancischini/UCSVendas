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

<main class="dash-main">
    <h1>Painel do Fornecedor</h1>
    
    <div class="dashboard-cards">
        <a href="produtos/index.php" class="dashboard-card card-teal">
            <div class="dashboard-card-icon"><i class="fas fa-box-open"></i></div>
            <h3>Meus Produtos</h3>
            <p>Gerencie seus produtos e estoque</p>
        </a>

        <a href="pedidos/index.php" class="dashboard-card card-blue">
            <div class="dashboard-card-icon"><i class="fas fa-receipt"></i></div>
            <h3>Pedidos</h3>
            <p>Consulte e gerencie os pedidos recebidos</p>
        </a>

        <a href="../perfil/index.php" class="dashboard-card card-gray">
            <div class="dashboard-card-icon"><i class="fas fa-user-circle"></i></div>
            <h3>Perfil</h3>
            <p>Visualize e edite seus dados</p>
        </a>
    </div>
</main>

<?php include_once '../layouts/footer.php'; ?>

<style>
.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-top: 28px;
}
.dashboard-card {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    padding: 28px 24px;
    background: #fff;
    border-radius: 14px;
    text-decoration: none;
    border: 1px solid var(--gray-200, #e2e8f0);
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
    transition: transform .2s, box-shadow .2s;
}
.dashboard-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,.1);
}
.dashboard-card-icon {
    width: 52px;
    height: 52px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 18px;
}
.card-teal  .dashboard-card-icon { background: #d1fae5; color: #059669; }
.card-blue  .dashboard-card-icon { background: #dbeafe; color: #2563eb; }
.card-purple .dashboard-card-icon { background: #ede9fe; color: #7c3aed; }
.card-orange .dashboard-card-icon { background: #fef3c7; color: #d97706; }
.card-gray  .dashboard-card-icon { background: #f1f5f9; color: #475569; }
.dashboard-card h3 {
    color: #1e293b;
    margin-bottom: 8px;
    font-size: 17px;
    font-weight: 700;
}
.dashboard-card p {
    color: #64748b;
    margin: 0;
    font-size: 13px;
    line-height: 1.5;
}
</style>