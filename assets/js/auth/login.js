$(document).ready(function() {
    $('#form-login').on('submit', function(e) {
        e.preventDefault();

        let email = $('#email').val();
        let senha = $('#senha').val();
        let mensagemDiv = $('#mensagem-login');

        mensagemDiv.removeClass('alert-error alert-success').hide().text('');

        $.ajax({
            url: '../../api/processa_login.php',
            type: 'POST',
            dataType: 'json',
            data: {
                email: email,
                senha: senha
            },
            success: function(response) {
                if (response.sucesso) {
                    var params = new URLSearchParams(window.location.search);
                    var redirect = params.get('redirect');
                    if (response.perfil === 'FORNECEDOR') {
                        window.location.href = '../dashboards/index.php';
                    } else if (response.perfil === 'ADMIN') {
                        window.location.href = '../admin/index.php';
                    } else if (redirect === 'checkout') {
                        window.location.href = '../store/checkout.php';
                    } else {
                        window.location.href = '../store/index.php';
                    }
                } else {
                    mensagemDiv.addClass('alert alert-error').text(response.mensagem).show();
                }
            },
            error: function() {
                mensagemDiv.addClass('alert alert-error').text('Erro de comunicação com o servidor.').show();
            }
        });
    });
});