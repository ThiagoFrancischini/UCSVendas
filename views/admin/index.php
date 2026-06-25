<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'ADMIN') {
    header('Location: ' . BASE_URL . '/views/admin/login.php');
    exit;
}
?>
<?php include_once '../layouts/header.php'; ?>

<main class="dash-main">
    <h1>Painel do Administrador</h1>

    <div class="dashboard-cards">
        <a href="clientes/index.php" class="dashboard-card card-green">
            <div class="dashboard-card-icon"><i class="fas fa-users"></i></div>
            <h3>Clientes</h3>
            <p>Visualize e gerencie os clientes cadastrados</p>
        </a>

        <a href="fornecedores/index.php" class="dashboard-card card-purple">
            <div class="dashboard-card-icon"><i class="fas fa-truck"></i></div>
            <h3>Fornecedores</h3>
            <p>Visualize e gerencie os fornecedores cadastrados</p>
        </a>

        <a href="usuarios/index.php" class="dashboard-card card-orange">
            <div class="dashboard-card-icon"><i class="fas fa-id-card"></i></div>
            <h3>Usuários</h3>
            <p>Visualize e remova usuários do sistema</p>
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
.card-green  .dashboard-card-icon { background: #d1fae5; color: #059669; }
.card-purple .dashboard-card-icon { background: #ede9fe; color: #7c3aed; }
.card-orange .dashboard-card-icon { background: #fef3c7; color: #d97706; }
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
