document.addEventListener('DOMContentLoaded', function () {
    carregarResumo();
});

function carregarResumo() {
    fetch(window.BASE_URL + '/api/carrinho/listar.php')
        .then(r => r.json())
        .then(data => {
            document.getElementById('checkout-itens-loading').style.display = 'none';
            if (!data.sucesso || !data.itens.length) {
                document.getElementById('checkout-itens-loading').textContent = 'Seu carrinho está vazio.';
                document.getElementById('checkout-itens-loading').style.display = 'block';
                return;
            }
            const tbody = document.getElementById('checkout-itens');
            tbody.innerHTML = '';
            data.itens.forEach(item => {
                const sub = (item.preco * item.quantidade).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                const unit = parseFloat(item.preco).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                tbody.innerHTML += `<tr>
                    <td>${item.nome}</td>
                    <td>${item.quantidade}</td>
                    <td>${unit}</td>
                    <td>${sub}</td>
                </tr>`;
            });
            document.getElementById('checkout-tabela').style.display = 'table';
            document.getElementById('checkout-total-valor').textContent = 'R$ ' + data.total_geral;
            document.getElementById('checkout-total').style.display = 'block';
        });
}

function finalizarPedido() {
    const btn = document.getElementById('btn-finalizar');
    const msg = document.getElementById('msg-checkout');
    msg.className = 'form-msg';
    msg.style.display = 'none';
    btn.disabled = true;
    btn.textContent = 'Processando...';

    fetch(window.BASE_URL + '/api/processa_pedido.php', { method: 'POST' })
        .then(r => r.json())
        .then(data => {
            if (!data.sucesso) {
                msg.textContent = data.mensagem || 'Erro ao finalizar pedido.';
                msg.className = 'form-msg erro';
                btn.disabled = false;
                btn.textContent = 'Confirmar Pedido';
                return;
            }
            window.location.href = data.redirecionar || (window.BASE_URL + '/views/store/pagamento.php?pedidos=' + (data.pedido_ids || [data.pedido_id]).join(','));
        })
        .catch(() => {
            msg.textContent = 'Erro de conexão.';
            msg.className = 'form-msg erro';
            btn.disabled = false;
            btn.textContent = 'Confirmar Pedido';
        });
}
