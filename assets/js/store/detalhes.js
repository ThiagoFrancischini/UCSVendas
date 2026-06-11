document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const produtoId = params.get('id');

    if (!produtoId) {
        document.getElementById('detalhes-loading').style.display = 'none';
        const erro = document.getElementById('detalhes-erro');
        erro.textContent = 'Nenhum produto informado.';
        erro.style.display = 'block';
        return;
    }

    carregarProduto(produtoId);
});

function carregarProduto(id) {
    const apiUrl = window.BASE_URL
        ? window.BASE_URL + '/api/detalhes_produto.php?id=' + id
        : '../api/detalhes_produto.php?id=' + id;

    fetch(apiUrl)
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (!data.sucesso) {
                throw new Error(data.mensagem || 'Erro ao carregar produto.');
            }
            renderizarProduto(data.produto);
        })
        .catch(function(error) {
            document.getElementById('detalhes-loading').style.display = 'none';
            var erro = document.getElementById('detalhes-erro');
            erro.textContent = error.message;
            erro.style.display = 'block';
        });
}

function renderizarProduto(produto) {
    document.getElementById('detalhes-loading').style.display = 'none';
    document.getElementById('detalhes-content').style.display = 'block';

    document.title = produto.nome + ' - UCS Vendas';

    var img = document.getElementById('detalhes-imagem');
    if (produto.foto) {
        img.style.backgroundImage = "url('" + produto.foto + "')";
    } else {
        img.style.backgroundColor = '#f4f4f4';
    }

    document.getElementById('detalhes-nome').textContent = produto.nome;
    document.getElementById('detalhes-preco').textContent =
        produto.preco !== null ? 'R$ ' + produto.preco : 'Preço indisponível';

    var estoqueEl = document.getElementById('detalhes-estoque');
    if (produto.quantidade_total > 0) {
        estoqueEl.textContent = produto.quantidade_total + ' em estoque';
        estoqueEl.className = 'detalhes-estoque';
    } else {
        estoqueEl.textContent = 'Fora de estoque';
        estoqueEl.className = 'detalhes-estoque sem-estoque';
    }

    document.getElementById('detalhes-fornecedor-nome').textContent = produto.fornecedor_nome;
    document.getElementById('detalhes-descricao').textContent = produto.descricao;

    var qtdInput = document.getElementById('qtd-input');
    if (produto.quantidade_total > 0) {
        qtdInput.max = produto.quantidade_total;
    } else {
        qtdInput.disabled = true;
    }

    var btn = document.getElementById('btn-adicionar-carrinho');
    if (produto.quantidade_total <= 0) {
        btn.disabled = true;
        btn.textContent = 'Indisponível';
    } else {
        btn.onclick = function() { adicionarAoCarrinho(produto); };
    }
}

function adicionarAoCarrinho(produto) {
    var qtd = parseInt(document.getElementById('qtd-input').value);
    if (isNaN(qtd) || qtd < 1) {
        mostrarMsg('Informe uma quantidade válida.', 'erro');
        return;
    }
    if (qtd > produto.quantidade_total) {
        mostrarMsg('Quantidade solicitada excede o estoque disponível (' + produto.quantidade_total + ').', 'erro');
        return;
    }

    var apiUrl = window.BASE_URL
        ? window.BASE_URL + '/api/carrinho/adicionar.php'
        : '../api/carrinho/adicionar.php';

    fetch(apiUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            produto_id: produto.id,
            nome: produto.nome,
            foto: produto.foto,
            preco: produto.preco_num,
            quantidade: qtd
        })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.sucesso) {
            mostrarMsg('Produto adicionado ao carrinho!', 'sucesso');
            atualizarBadgeCarrinho();
        } else {
            mostrarMsg(data.mensagem || 'Erro ao adicionar ao carrinho.', 'erro');
        }
    })
    .catch(function() {
        mostrarMsg('Erro de conexão. Tente novamente.', 'erro');
    });
}

function mostrarMsg(texto, tipo) {
    var el = document.getElementById('detalhes-msg');
    el.textContent = texto;
    el.className = 'detalhes-msg ' + tipo;
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
