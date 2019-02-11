$(function(){
    $('#formPasswordForget').submit(function(e){
        var form = $(this);
        e.preventDefault();
        var email = $('#email').val();
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            dataType: 'json',
            data: form.serialize(),
            success: function(data){
                if(data.success){
                    form.remove();
                    $('#view_forget').html('<p style="color:green;">Email de changement de mot de passe envoyé à l\'adresse  <span class="ub-text-orange">' + email + '</span> !</p>');
                } else {
                    if(data.errors.email){
                        $('#emailForgetPsw').removeClass('red-text')
                        $('#error_forgetPsw').remove()
                        $('#emailForgetPsw').before('<badge style="color:red;" id="error_forgetPsw">Email invalide !</badge>')
                        $('#emailForgetPsw').addClass('red-text')
                    }

                    if(data.errors.undefined_account){
                        $('#emailForgetPsw').removeClass('red-text')
                        $('#error_undefined').remove()
                        $('#emailForgetPsw').before('<badge style="color:red;" id="error_undefined">Cet email n\'existe pas !</badge>')
                        $('#emailForgetPsw').addClass('red-text')
                    }
                }
            },
            error: function(){
                $('#view').html('Erreur serveur, veuillez réessayer dans quelques instants')
            }
        });
    });
});
