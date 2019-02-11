$(function(){
    $('#progressBar').hide();
    $('#formLogin').submit(function(e){
        var form = $(this);
        e.preventDefault();
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            dataType: 'json',
            data: form.serialize(),
            success: function(data){
                if(data.success){
                    form.remove();
                // Progress bar
                    // Progress bar
                    $('#progressBar').show();
                    $('#view_login').html('<p style="color:green;" id="plouf"> Connexion réussi !</p>');
                    $('#view_login').addClass('red_color')
                    setTimeout(function(){
                        $('#progressBar').remove();
                            location.reload()
                    }, 1500)
                } else {
                    if(data.errors.email){
                        $('#emailLogin').removeClass('red-text')
                        $('#error_emailLogin1').remove()
                        $('#emailLogin').before('<badge style="color:red;" id="error_emailLogin1">Email invalide !</badge>')
                        $('#emailLogin').addClass('red-text')
                    } else {
                        $('#emailLogin').removeClass('red-text')
                        $('#error_emailLogin1').remove()
                    }

                    if(data.errors.undefined_account){
                        $('#error_email').removeClass('red-text')
                        $('#error_emailLogin2').remove()
                        $('#emailLogin').before('<badge style="color:red;" id="error_emailLogin2">Ce compte n\'existe pas !</badge>')
                        $('#emailLogin').addClass('red-text')
                    } else {
                        $('#emailLogin').removeClass('red-text')
                        $('#error_emailLogin2').remove()
                    }

                    if(data.errors.password){
                        $('#passwordLogin').removeClass('red-text')
                        $('#error_passwordLogin1').remove()
                        $('#passwordLogin').before('<badge style="color:red;" id="error_passwordLogin1">Mot de passe invalide !</badge>')
                        $('#passwordLogin').addClass('red-text')
                    } else {
                        $('#passwordLogin').removeClass('red-text')
                        $('#error_passwordLogin1').remove()
                    }

                    if(data.errors.invalid_password){
                        $('#passwordLogin').removeClass('red-text')
                        $('#error_passwordLogin2').remove()
                        $('#passwordLogin').before('<badge style="color:red;" id="error_passwordLogin2">Le mot de passe et l\'adresse email ne correspondent pas !</badge>')
                        $('#passwordLogin').addClass('red-text')
                    } else {
                        $('#passwordLogin').removeClass('red-text')
                        $('#error_passwordLogin2').remove()
                    }
                }
            },
            error: function(){
                $('#view_login').html('<p style="color:red;">Erreur serveur, veuillez réessayer dans quelques instants</p>')
            }
        });
    });
});