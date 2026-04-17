$(document).ready(function() {
    $('#form-produto').on('submit', function(e) {
        e.preventDefault();

        let formData = $(this).serialize();
        let msg = $('#mensagem-produto');

        $.ajax({
            url: '../../api/processa_produto.php',
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(response) {
                if (response.sucesso) {
                    msg.html('<div class="alert alert-success">Produto cadastrado!</div>');
                    $('#form-produto')[0].reset();
                } else {
                    msg.html('<div class="alert alert-error">' + response.mensagem + '</div>');
                }
            }
        });
    });
});