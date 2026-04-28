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
                    if (response.perfil === 'FORNECEDOR') {
                        window.location.href = '../dashboards/index.php';
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