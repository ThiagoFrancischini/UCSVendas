let pedidoAtual = null;
let paginaAtualItens = 1;
let totalPaginasItens = 1;
let carrosselIndex = 0;
let carrosselFotos = [];

document.addEventListener('DOMContentLoaded', carregarMeusPedidos);

function carregarMeusPedidos() {
    fetch(window.BASE_URL + '/api/meus_pedidos.php')
        .then(r => r.json())
        .then(data => {
            document.getElementById('pedidos-loading').style.display = 'none';
            if (!data.sucesso || !data.pedidos.length) {
                document.getElementById('pedidos-vazio').style.display = 'block';
                return;
            }
            const lista = document.getElementById('lista-pedidos');
            lista.innerHTML = '';
            data.pedidos.forEach(p => lista.innerHTML += cardPedido(p));
        });
}

function cardPedido(p) {
    const dataFormatada = formatarData(p.data_pedido);
    const valorFmt = parseFloat(p.valor_total).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    return `<div class="pedido-card">
        <div class="pedido-card-header">
            <div class="pedido-num-wrap">
                <span class="pedido-num">Pedido #${p.id}</span>
            </div>
            <span class="pedido-status status-${p.status}">${traduzirStatus(p.status)}</span>
        </div>
        <div class="pedido-card-meta">
            <span class="pedido-data"><i class="fas fa-calendar-alt"></i> ${dataFormatada}</span>
        </div>
        <div class="pedido-card-footer">
            <span class="pedido-valor">${valorFmt}</span>
            <button class="btn-ver-detalhes" onclick="abrirModal(${p.id})"><i class="fas fa-eye"></i> Ver Detalhes</button>
        </div>
    </div>`;
}

function abrirModal(pedidoId) {
    pedidoAtual = pedidoId;
    paginaAtualItens = 1;
    document.getElementById('modal-titulo').textContent = `Pedido #${pedidoId}`;
    document.getElementById('modal-pedido').style.display = 'flex';
    document.getElementById('detalhe-tabela').style.display = 'none';
    document.getElementById('modal-itens-loading').style.display = 'block';
    document.getElementById('modal-itens-loading').textContent = 'Carregando itens...';
    document.getElementById('paginacao-itens').innerHTML = '';
    document.getElementById('carrossel').style.display = 'none';
    carregarItens(pedidoId, 1);
}

function fecharModal() {
    document.getElementById('modal-pedido').style.display = 'none';
    pedidoAtual = null;
}

function carregarItens(pedidoId, pagina) {
    paginaAtualItens = pagina;
    const url = `${window.BASE_URL}/api/pedido_detalhe.php?pedido_id=${pedidoId}&pagina=${pagina}&por_pagina=5`;
    fetch(url)
        .then(r => r.json())
        .then(data => {
            document.getElementById('modal-itens-loading').style.display = 'none';
            if (!data.sucesso) return;
            totalPaginasItens = data.paginas;

            const tbody = document.getElementById('detalhe-itens');
            tbody.innerHTML = '';
            carrosselFotos = [];
            carrosselIndex = 0;

            data.itens.forEach(item => {
                const unit = parseFloat(item.valor_unitario).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                const tot  = parseFloat(item.valor_total_item).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                tbody.innerHTML += `<tr>
                    <td>${item.produto_nome}</td>
                    <td><span class="fornecedor-tag">${item.fornecedor_nome || '—'}</span></td>
                    <td>${item.quantidade}</td>
                    <td>${unit}</td>
                    <td>${tot}</td>
                </tr>`;
                if (item.foto) carrosselFotos.push(item.foto);
            });

            document.getElementById('detalhe-tabela').style.display = 'table';
            renderizarCarrossel();
            renderizarPaginacaoItens(pedidoId, pagina, data.paginas);
        });
}

function renderizarCarrossel() {
    const carr = document.getElementById('carrossel');
    const track = document.getElementById('carr-track');
    track.innerHTML = '';
    if (!carrosselFotos.length) { carr.style.display = 'none'; return; }
    carrosselFotos.forEach(foto => {
        const div = document.createElement('div');
        div.className = 'carr-foto';
        // URL de internet: usa background-image diretamente
        if (foto.startsWith('http://') || foto.startsWith('https://') || foto.startsWith('/')) {
            div.style.backgroundImage = `url('${foto}')`;
        } else {
            // legado BYTEA base64 — já não deve ocorrer após migração
            div.style.backgroundImage = `url('data:image/jpeg;base64,${btoa(foto)}')`;
        }
        track.appendChild(div);
    });
    carr.style.display = 'flex';
    atualizarCarrossel();
}

function moverCarrossel(dir) {
    carrosselIndex = Math.max(0, Math.min(carrosselFotos.length - 1, carrosselIndex + dir));
    atualizarCarrossel();
}

function atualizarCarrossel() {
    const itemW = 120 + 12;
    document.getElementById('carr-track').style.transform = `translateX(-${carrosselIndex * itemW}px)`;
}

function renderizarPaginacaoItens(pedidoId, atual, total) {
    const pag = document.getElementById('paginacao-itens');
    pag.innerHTML = '';
    for (let i = 1; i <= total; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        if (i === atual) btn.classList.add('active');
        btn.onclick = () => carregarItens(pedidoId, i);
        pag.appendChild(btn);
    }
}

function formatarData(str) {
    if (!str) return '';
    const d = new Date(str);
    return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
}

function traduzirStatus(s) {
    const map = { PENDENTE: 'Pendente', CONFIRMADO: 'Confirmado', ENVIADO: 'Enviado', ENTREGUE: 'Entregue', CANCELADO: 'Cancelado' };
    return map[s] || s;
}
