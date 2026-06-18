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

function mostrarTab(tab) {
    document.querySelectorAll('.auth-tab').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.auth-form').forEach(f => f.style.display = 'none');
    document.querySelector(`[data-tab="${tab}"]`).classList.add('active');
    document.getElementById('tab-' + tab).style.display = 'block';
}

function fazerLogin() {
    const email = document.getElementById('login-email').value.trim();
    const senha = document.getElementById('login-senha').value;
    const msg   = document.getElementById('msg-login');
    msg.className = 'form-msg';
    msg.style.display = 'none';

    if (!email || !senha) {
        msg.textContent = 'Preencha e-mail e senha.';
        msg.className = 'form-msg erro';
        return;
    }

    const fd = new FormData();
    fd.append('email', email);
    fd.append('senha', senha);

    fetch(window.BASE_URL + '/api/processa_login.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.sucesso) {
                msg.textContent = data.mensagem || 'Erro ao fazer login.';
                msg.className = 'form-msg erro';
                return;
            }
            if (data.perfil !== 'CLIENTE') {
                msg.textContent = 'Apenas clientes podem fazer pedidos. Use uma conta de cliente.';
                msg.className = 'form-msg erro';
                return;
            }
            // Login OK — recarrega para exibir botão de finalizar
            location.reload();
        });
}

function fazerRegistro() {
    const msg = document.getElementById('msg-registro');
    msg.className = 'form-msg';
    msg.style.display = 'none';

    const dados = {
        nome: document.getElementById('reg-nome').value.trim(),
        email: document.getElementById('reg-email').value.trim(),
        senha: document.getElementById('reg-senha').value,
        telefone: document.getElementById('reg-telefone').value.trim(),
        cep: document.getElementById('reg-cep').value.trim(),
        rua: document.getElementById('reg-rua').value.trim(),
        numero: document.getElementById('reg-numero').value.trim(),
        bairro: document.getElementById('reg-bairro').value.trim(),
        cidade: document.getElementById('reg-cidade').value.trim(),
        estado: document.getElementById('reg-estado').value.trim(),
    };

    for (const [k, v] of Object.entries(dados)) {
        if (!v) {
            msg.textContent = 'Preencha todos os campos.';
            msg.className = 'form-msg erro';
            return;
        }
    }

    const fd2 = new FormData();
    Object.entries(dados).forEach(([k, v]) => fd2.append(k, v));
    fetch(window.BASE_URL + '/api/processa_registro.php', { method: 'POST', body: fd2 })
    .then(r => r.json())
    .then(data => {
        if (!data.sucesso) {
            msg.textContent = data.mensagem || 'Erro ao criar conta.';
            msg.className = 'form-msg erro';
            return;
        }
        // Faz login automático
        const fd = new FormData();
        fd.append('email', dados.email);
        fd.append('senha', dados.senha);
        fetch(window.BASE_URL + '/api/processa_login.php', { method: 'POST', body: fd })
            .then(() => location.reload());
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
            window.location.href = window.BASE_URL + '/views/store/pedido_confirmado.php?pedido_id=' + data.pedido_id;
        })
        .catch(() => {
            msg.textContent = 'Erro de conexão.';
            msg.className = 'form-msg erro';
            btn.disabled = false;
            btn.textContent = 'Confirmar Pedido';
        });
}
