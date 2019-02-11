$(function(){
    $('#formPasswordChange').submit(function(e){
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
                    $('#view_change').html('<p style="color:green;">Votre mot de passe a été modifié avec succès ! <a href="#login-modal" class="btn ub-orange modal-trigger right>Connexion ?</a></p>');
                } else {
                    if(data.errors.password){
                        $('#newPassword').removeClass('red-text')
                        $('#error_password').remove()
                        $('#newPassword').before('<badge style="color:red;" id="error_password">Mot de passe invalide !</badge>')
                        $('#newPassword').addClass('red-text')
                    }

                    if(data.errors.confirmPassword){
                        $('#confirmPassword').removeClass('red-text')
                        $('#error_confirm').remove()
                        $('#confirmPassword').before('<badge style="color:red;" id="error_confirm">Les mots de passe ne correspondent pas !</badge>')
                        $('#confirmPassword').addClass('red-text')
                    }

                    if(data.errors.undefined_user){
                        $('#view').removeClass('red-text')
                        $('#error_undefined').remove()
                        $('#view').before('<badge style="color:red;" id="error_undefined">L\'utilisateur recherché n\'existe pas !</badge>')
                        $('#view').addClass('red-text')
                    }
                }
            },
            error: function(){
                $('#view').html('Erreur serveur, veuillez réessayer dans quelques instants')
            }
        });
    });
})
