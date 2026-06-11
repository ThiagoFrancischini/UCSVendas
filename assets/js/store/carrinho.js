document.addEventListener('DOMContentLoaded', function() {
    carregarCarrinho();
});

function carregarCarrinho() {
    var apiUrl = window.BASE_URL
        ? window.BASE_URL + '/api/carrinho/listar.php'
        : '../api/carrinho/listar.php';

    fetch(apiUrl)
        .then(function(response) { return response.json(); })
        .then(function(data) {
            document.getElementById('carrinho-loading').style.display = 'none';

            if (!data.sucesso || !data.itens || data.itens.length === 0) {
                document.getElementById('carrinho-content').style.display = 'none';
                document.getElementById('carrinho-vazio').style.display = 'block';
                return;
            }

            document.getElementById('carrinho-content').style.display = 'block';
            renderizarItens(data.itens, data.total_geral);
        })
        .catch(function() {
            document.getElementById('carrinho-loading').style.display = 'none';
            document.getElementById('carrinho-content').style.display = 'none';
            document.getElementById('carrinho-vazio').style.display = 'block';
            document.querySelector('.carrinho-vazio p').textContent = 'Erro ao carregar carrinho.';
        });
}

function renderizarItens(itens, totalGeral) {
    var tbody = document.getElementById('carrinho-itens');
    tbody.innerHTML = '';

    itens.forEach(function(item) {
        var subtotal = (item.preco * item.quantidade).toFixed(2).replace('.', ',');

        var tr = document.createElement('tr');

        var fotoStyle = item.foto
            ? "background-image: url('" + item.foto + "')"
            : 'background-color: #f4f4f4';

        tr.innerHTML =
            '<td><div class="carrinho-item-info"><div class="carrinho-item-foto" style="' + fotoStyle + '"></div><span class="carrinho-item-nome">' + item.nome + '</span></div></td>' +
            '<td class="carrinho-item-preco">R$ ' + item.preco.toFixed(2).replace('.', ',') + '</td>' +
            '<td><input type="number" class="carrinho-qtd-input" value="' + item.quantidade + '" min="1" data-produto-id="' + item.produto_id + '"></td>' +
            '<td class="carrinho-item-subtotal">R$ ' + subtotal + '</td>' +
            '<td><button class="btn-remover" data-produto-id="' + item.produto_id + '">Remover</button></td>';

        tbody.appendChild(tr);
    });

    document.getElementById('carrinho-total-geral').textContent = 'R$ ' + totalGeral;

    document.querySelectorAll('.carrinho-qtd-input').forEach(function(input) {
        input.addEventListener('change', function() {
            atualizarQuantidade(this.dataset.produtoId, parseInt(this.value));
        });
    });

    document.querySelectorAll('.btn-remover').forEach(function(btn) {
        btn.addEventListener('click', function() {
            removerItem(this.dataset.produtoId);
        });
    });
}

function atualizarQuantidade(produtoId, quantidade) {
    if (isNaN(quantidade) || quantidade < 1) {
        carregarCarrinho();
        return;
    }

    var apiUrl = window.BASE_URL
        ? window.BASE_URL + '/api/carrinho/atualizar.php'
        : '../api/carrinho/atualizar.php';

    fetch(apiUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ produto_id: produtoId, quantidade: quantidade })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.sucesso) {
            carregarCarrinho();
            atualizarBadgeCarrinho();
        }
    })
    .catch(function() {});
}

function removerItem(produtoId) {
    var apiUrl = window.BASE_URL
        ? window.BASE_URL + '/api/carrinho/remover.php'
        : '../api/carrinho/remover.php';

    fetch(apiUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ produto_id: produtoId })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.sucesso) {
            carregarCarrinho();
            atualizarBadgeCarrinho();
        }
    })
    .catch(function() {});
}

function atualizarBadgeCarrinho() {
    var badge = document.getElementById('carrinho-badge');
    if (!badge) return;

    var apiUrl = window.BASE_URL
        ? window.BASE_URL + '/api/carrinho/listar.php'
        : '../api/carrinho/listar.php';

    fetch(apiUrl)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.sucesso) {
                var total = data.itens.reduce(function(sum, item) { return sum + item.quantidade; }, 0);
                badge.textContent = total;
                badge.style.display = total > 0 ? 'inline' : 'none';
            }
        })
        .catch(function() {});
}
