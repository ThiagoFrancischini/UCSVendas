let pedidoAtual = null;
let paginaAtual = 1;
let buscaAtual = { numero: 0, cliente: '' };
let carrosselIndex = 0;
let carrosselFotos = [];

document.addEventListener('DOMContentLoaded', () => buscarPedidos(1));

function buscarPedidos(pagina) {
    paginaAtual = pagina;
    buscaAtual.numero = parseInt(document.getElementById('busca-numero').value) || 0;
    buscaAtual.cliente = document.getElementById('busca-cliente').value.trim();

    document.getElementById('pedidos-loading').style.display = 'block';
    document.getElementById('lista-pedidos').style.display = 'none';
    document.getElementById('pedidos-vazio').style.display = 'none';

    let url = `${window.BASE_URL}/api/pedidos.php?pagina=${pagina}`;
    if (buscaAtual.numero > 0) url += `&numero=${buscaAtual.numero}`;
    else if (buscaAtual.cliente) url += `&busca=${encodeURIComponent(buscaAtual.cliente)}`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            document.getElementById('pedidos-loading').style.display = 'none';
            if (!data.sucesso || !data.pedidos.length) {
                document.getElementById('pedidos-vazio').style.display = 'block';
                return;
            }
            renderizarTabela(data.pedidos);
            renderizarPaginacao(data.paginas, pagina);
            document.getElementById('lista-pedidos').style.display = 'block';
        });
}

function limparBusca() {
    document.getElementById('busca-numero').value = '';
    document.getElementById('busca-cliente').value = '';
    buscarPedidos(1);
}

function renderizarTabela(pedidos) {
    const tbody = document.getElementById('ped-tbody');
    tbody.innerHTML = '';
    pedidos.forEach(p => {
        tbody.innerHTML += `<tr>
            <td>#${p.id}</td>
            <td>${p.cliente_nome}</td>
            <td>${formatarData(p.data_pedido)}</td>
            <td><span class="ped-status status-${p.status}">${traduzirStatus(p.status)}</span></td>
            <td>R$ ${p.valor_total}</td>
            <td><button class="btn-detalhe" onclick="abrirModal(${p.id}, '${p.status}')"><i class="fas fa-eye"></i> Detalhes</button></td>
        </tr>`;
    });
}

function renderizarPaginacao(total, atual) {
    const pag = document.getElementById('paginacao');
    pag.innerHTML = '';
    for (let i = 1; i <= total; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        if (i === atual) btn.classList.add('active');
        btn.onclick = () => buscarPedidos(i);
        pag.appendChild(btn);
    }
}

function abrirModal(pedidoId, status) {
    pedidoAtual = { id: pedidoId, status };
    document.getElementById('modal-titulo').textContent = `Pedido #${pedidoId}`;
    document.getElementById('select-status').value = status;
    document.getElementById('msg-status').textContent = '';
    document.getElementById('msg-status').className = 'msg-status';
    document.getElementById('modal-itens-loading').style.display = 'block';
    document.getElementById('modal-itens-loading').textContent = 'Carregando itens...';
    document.getElementById('detalhe-tabela').style.display = 'none';
    document.getElementById('paginacao-itens').innerHTML = '';
    document.getElementById('carrossel').style.display = 'none';
    document.getElementById('modal-pedido').style.display = 'flex';
    carregarDetalhes(pedidoId, 1);
    carregarInfoPedido(pedidoId);
}

function fecharModal() {
    document.getElementById('modal-pedido').style.display = 'none';
    pedidoAtual = null;
}

function carregarInfoPedido(pedidoId) {
    fetch(`${window.BASE_URL}/api/pedidos.php?numero=${pedidoId}`)
        .then(r => r.json())
        .then(data => {
            if (!data.sucesso || !data.pedidos.length) return;
            const p = data.pedidos[0];
            document.getElementById('modal-info').innerHTML = `
                <strong>Cliente:</strong> ${p.cliente_nome}<br>
                <strong>Data:</strong> ${formatarData(p.data_pedido)}<br>
                <strong>Valor Total:</strong> R$ ${p.valor_total}<br>
                ${p.data_envio ? `<strong>Enviado em:</strong> ${formatarData(p.data_envio)}<br>` : ''}
                ${p.data_cancelamento ? `<strong>Cancelado em:</strong> ${formatarData(p.data_cancelamento)}<br>` : ''}
            `;
        });
}

function carregarDetalhes(pedidoId, pagina) {
    const url = `${window.BASE_URL}/api/pedido_detalhe.php?pedido_id=${pedidoId}&pagina=${pagina}&por_pagina=5`;
    fetch(url)
        .then(r => r.json())
        .then(data => {
            document.getElementById('modal-itens-loading').style.display = 'none';
            if (!data.sucesso) return;

            const tbody = document.getElementById('detalhe-itens');
            tbody.innerHTML = '';
            carrosselFotos = [];
            carrosselIndex = 0;

            data.itens.forEach(item => {
                const unit = parseFloat(item.valor_unitario).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                const tot  = parseFloat(item.valor_total_item).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                tbody.innerHTML += `<tr>
                    <td>${item.produto_nome}</td>
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
        div.style.backgroundImage = `url('${foto}')`;
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
    const itemW = 110 + 12;
    document.getElementById('carr-track').style.transform = `translateX(-${carrosselIndex * itemW}px)`;
}

function renderizarPaginacaoItens(pedidoId, atual, total) {
    const pag = document.getElementById('paginacao-itens');
    pag.innerHTML = '';
    for (let i = 1; i <= total; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        if (i === atual) btn.classList.add('active');
        btn.onclick = () => carregarDetalhes(pedidoId, i);
        pag.appendChild(btn);
    }
}

function salvarStatus() {
    if (!pedidoAtual) return;
    const novoStatus = document.getElementById('select-status').value;
    const msg = document.getElementById('msg-status');
    msg.className = 'msg-status';
    msg.textContent = 'Salvando...';

    fetch(window.BASE_URL + '/api/atualiza_status_pedido.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pedido_id: pedidoAtual.id, status: novoStatus }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            msg.textContent = 'Status atualizado!';
            msg.className = 'msg-status ok';
            pedidoAtual.status = novoStatus;
            buscarPedidos(paginaAtual);
            carregarInfoPedido(pedidoAtual.id);
        } else {
            msg.textContent = data.mensagem || 'Erro ao salvar.';
            msg.className = 'msg-status erro';
        }
    });
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
