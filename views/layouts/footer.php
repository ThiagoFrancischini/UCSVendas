<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
window.BASE_URL = window.BASE_URL || '<?php echo defined('BASE_URL') ? BASE_URL : '/UCSVendas/UCSVendas'; ?>';
document.addEventListener('DOMContentLoaded', function() {
    var badge = document.getElementById('carrinho-badge');
    if (badge) {
        var url = window.BASE_URL + '/api/carrinho/listar.php';
        fetch(url)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.sucesso && data.itens) {
                    var total = data.itens.reduce(function(sum, item) { return sum + item.quantidade; }, 0);
                    badge.textContent = total;
                    badge.style.display = total > 0 ? 'inline' : 'none';
                }
            })
            .catch(function() {});
    }
});
</script>
</body>
</html>