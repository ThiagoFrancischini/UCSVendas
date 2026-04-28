<?php
if (!defined('BASE_URL')) {
    require_once(__DIR__ . '/../../config.php');
}
include_once '../layouts/header.php';
?>

<main class="vitrine">
    <h2>Destaques</h2>
    <div id="grid-produtos">
    </div>
</main>

<?php include_once '../layouts/footer.php'; ?>
<script src="<?php echo BASE_URL; ?>/assets/js/store/index.js"></script>