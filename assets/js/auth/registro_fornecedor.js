$(document).ready(function() {
    $('#form-registro-fornecedor').on('submit', function(e) {
        e.preventDefault();

        let formData = $(this).serialize();
        let mensagemDiv = $('#mensagem-registro-forn');

        mensagemDiv.removeClass('alert-error alert-success').hide().text('');

        $.ajax({
            url: '../../api/processa_registro_fornecedor.php',
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(response) {
                if (response.sucesso) {
                    mensagemDiv.addClass('alert alert-success').text('Empresa cadastrada com sucesso! Redirecionando para o login...').show();
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 2000);
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