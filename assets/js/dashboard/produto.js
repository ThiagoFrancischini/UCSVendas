$(document).ready(function() {
    function exibirLoading() {
        if ($('.loading-overlay').length === 0) {
            $('body').append('<div class="loading-overlay"><div class="spinner"></div></div>');
        }
    }

    function removerLoading() {
        $('.loading-overlay').remove();
    }

    $('#form-produto').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var mensagemDiv = $('#mensagem-produto');
        var botao = form.find('button[type="submit"]');
        var textoOriginal = botao.text();
        var produtoId = $('#produto_id').val();

        mensagemDiv.removeClass('alert-error alert-success').hide().text('');
        $('.form-group').removeClass('has-error');

        botao.prop('disabled', true).addClass('btn-loading').text('Salvando...');
        exibirLoading();

        // Definir a URL baseado se é edição ou novo produto
        var url = produtoId ? '../../../api/processa_produto.php?acao=editar' : '../../../api/processa_produto.php';

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            success: function(response) {
                if (response.sucesso) {
                    var mensagem = produtoId ? 'Produto atualizado com sucesso!' : 'Produto cadastrado com sucesso!';
                    mensagemDiv.addClass('alert alert-success').text(mensagem).show();
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    mensagemDiv.addClass('alert alert-error').text(response.mensagem).show();
                    $('html, body').scrollTop(0);
                    if (response.campo) {
                        $('#' + response.campo).closest('.form-group').addClass('has-error');
                    }
                }
            },
            error: function() {
                mensagemDiv.addClass('alert alert-error').text('Erro de comunicação com o servidor.').show();
                $('html, body').scrollTop(0);
            },
            complete: function() {
                botao.prop('disabled', false).removeClass('btn-loading');
                // Garantir que o texto volta ao original
                if (produtoId) {
                    botao.text('Salvar Alterações');
                } else {
                    botao.text('Publicar Produto');
                }
                removerLoading();
            }
        });
    });
});