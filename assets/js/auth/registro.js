$(document).ready(function() {
    function formatarTelefone() {
        var campo = $('#telefone');
        var valor = campo.val().replace(/\D/g, '');
        if (valor.length > 0) {
            if (valor.length > 10) {
                valor = valor.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
            } else {
                valor = valor.replace(/^(\d{2})(\d{4,5}).*/, '($1) $2');
            }
        }
        campo.val(valor);
    }

    function formatarCEP() {
        var campo = $('#cep');
        var valor = campo.val().replace(/\D/g, '');
        if (valor.length > 5) {
            valor = valor.replace(/^(\d{5})(\d{3}).*/, '$1-$2');
        }
        campo.val(valor);
    }

    function formatarCartao() {
        var campo = $('#cartao');
        var valor = campo.val().replace(/\D/g, '');
        var formatted = '';
        for (var i = 0; i < valor.length && i < 16; i++) {
            if (i > 0 && i % 4 === 0) formatted += ' ';
            formatted += valor[i];
        }
        campo.val(formatted);
    }

    function formatarEstado() {
        var campo = $('#estado');
        campo.val(campo.val().toUpperCase().replace(/[^A-Z]/g, '').substring(0, 2));
    }

    function exibirLoading() {
        if ($('.loading-overlay').length === 0) {
            $('body').append('<div class="loading-overlay"><div class="spinner"></div></div>');
        }
    }

    function removerLoading() {
        $('.loading-overlay').remove();
    }

    $('#telefone').on('blur', formatarTelefone);
    $('#cep').on('blur', formatarCEP);
    $('#cartao').on('blur', formatarCartao);
    $('#estado').on('blur', formatarEstado);

    $('#form-registro').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var mensagemDiv = $('#mensagem-registro');
        var botao = form.find('button');
        var textoOriginal = botao.text();

        mensagemDiv.removeClass('alert-error alert-success').hide().text('');
        $('.form-group').removeClass('has-error');

        botao.prop('disabled', true).addClass('btn-loading').text('Cadastrando...');
        exibirLoading();

        $.ajax({
            url: '../../api/processa_registro.php',
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            success: function(response) {
                if (response.sucesso) {
                    mensagemDiv.addClass('alert alert-success').text('Cadastro realizado com sucesso! Redirecionando para o login...').show();
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    mensagemDiv.addClass('alert alert-error').text(response.mensagem).show();
                    $('html, body').scrollTop(0);
                    if (response.campo) {
                        var campoId = response.campo;
                        var $campo = $('#' + campoId);
                        if ($campo.length) {
                            $campo.closest('.form-group').addClass('has-error');
                            setTimeout(function() {
                                $('html, body').animate({ scrollTop: $campo.offset().top - 150 }, 300);
                                $campo.focus();
                            }, 100);
                        }
                    }
                }
            },
            error: function() {
                mensagemDiv.addClass('alert alert-error').text('Erro de comunicação com o servidor.').show();
                $('html, body').scrollTop(0);
            },
            complete: function() {
                botao.prop('disabled', false).removeClass('btn-loading').text(textoOriginal);
                removerLoading();
            }
        });
    });
});