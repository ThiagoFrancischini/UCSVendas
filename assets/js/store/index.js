document.addEventListener('DOMContentLoaded', function() {
    carregarProdutos();
    const filtro = document.getElementById('filtro-loja');
    if (filtro) {
        filtro.addEventListener('input', filtrarProdutos);
    }
});

let produtosLoja = [];

function carregarProdutos() {
    const mensagem = document.getElementById('mensagem-loja');
    mensagem.textContent = 'Carregando produtos...';

    const apiUrl = window.BASE_URL ? `${window.BASE_URL}/api/lista_produtos.php` : '../api/lista_produtos.php';
    console.info('Carregando produtos via API:', apiUrl);

    fetch(apiUrl)
        .then(response => {
            return response.text().then(text => {
                if (!response.ok) {
                    const mensagem = text || `HTTP ${response.status}`;
                    console.error('API retornou status não OK:', response.status, text);
                    throw new Error(mensagem);
                }
                return text;
            });
        })
        .then(text => {
            if (!text || text.trim().length === 0) {
                throw new Error('Servidor retornou resposta vazia. Verifique se a URL está correta e se o PHP está funcionando.');
            }

            let data;
            try {
                data = JSON.parse(text);
            } catch (error) {
                console.error('Texto recebido do servidor:', text);
                throw new Error('Resposta inválida do servidor: ' + text);
            }

            if (!data.sucesso) {
                throw new Error(data.mensagem || 'Erro ao carregar produtos.');
            }
            produtosLoja = data.produtos;
            renderizaProdutos(produtosLoja);
            mensagem.textContent = produtosLoja.length === 0 ? 'Nenhum produto disponível no momento.' : '';
        })
        .catch(error => {
            const mensagem = document.getElementById('mensagem-loja');
            mensagem.textContent = error.message;
            console.error('Erro ao carregar produtos:', error);
        });
}

function renderizaProdutos(produtos) {
    const grid = document.getElementById('grid-produtos');
    grid.innerHTML = '';

    if (!produtos || produtos.length === 0) {
        return;
    }

    produtos.forEach(produto => {
        const card = document.createElement('article');
        card.className = 'produto-card';

        const link = document.createElement('a');
        link.href = window.BASE_URL
            ? window.BASE_URL + '/views/store/detalhes.php?id=' + produto.id
            : '../detalhes.php?id=' + produto.id;
        link.className = 'produto-link';
        link.style.textDecoration = 'none';
        link.style.color = 'inherit';

        const imagem = document.createElement('div');
        imagem.className = 'produto-imagem';
        if (produto.foto) {
            imagem.style.backgroundImage = `url('${produto.foto}')`;
        } else {
            imagem.style.backgroundColor = '#f4f4f4';
        }
        link.appendChild(imagem);

        const conteudo = document.createElement('div');
        conteudo.className = 'produto-conteudo';

        const titulo = document.createElement('h3');
        titulo.textContent = produto.nome;
        conteudo.appendChild(titulo);

        const descricao = document.createElement('p');
        descricao.textContent = produto.descricao;
        conteudo.appendChild(descricao);

        const info = document.createElement('div');
        info.className = 'produto-info';
        info.innerHTML = `
            <span>${produto.quantidade_total > 0 ? produto.quantidade_total + ' em estoque' : 'Sem estoque'}</span>
            <strong>${produto.preco !== null ? 'R$ ' + produto.preco : 'Preço indisponível'}</strong>
        `;
        conteudo.appendChild(info);

        link.appendChild(conteudo);
        card.appendChild(link);
        grid.appendChild(card);
    });
}

function filtrarProdutos() {
    const termo = document.getElementById('filtro-loja').value.toLowerCase();
    const produtosFiltrados = produtosLoja.filter(produto => {
        return produto.nome.toLowerCase().includes(termo) || produto.descricao.toLowerCase().includes(termo);
    });
    renderizaProdutos(produtosFiltrados);
}
