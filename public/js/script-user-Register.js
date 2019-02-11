$(function(){
    $('#link_back').hide();
    $('#background').addClass('ub-back-opacity')
    $('#passwordRegister').on('focus', function(){
        $('#passwordRegister').before('<badge id="info_password">Une Majuscule, un chiffre de 0 à 9, un caractère spécial (/\?!*-+ ...)</badge>')
    })
    $('#passwordRegister').on('blur', function(){
        $('#info_password').remove()
    })
    $('#formRegister').submit(function(e){
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
                    var route = "{{ path('index') }}";
                    if(data.email){
                        var email = data.email
                    }
                    $('#access_login').remove()
                    $('#view_register').html('<p style="color:green;">Inscription réussi ! Un email de confirmation vous à été envoyé à l\'adresse ' + email + ' pour valider et terminer votre inscription</p>');
                    $('#link_back').show();
                } else {
                    if(data.errors.name){
                        $('#nameRegister').removeClass('red-text')
                        $('#error_nameRegister').remove()
                        $('#nameRegister').before('<badge style="color:red;" id="error_nameRegister">Nom invalide !</badge>')
                        $('#nameRegister').addClass('red-text')
                    } else {
                        $('#nameRegister').removeClass('red-text')
                        $('#error_nameRegister').remove()
                    }

                    if(data.errors.studentId){
                        $('#studentId').removeClass('red-text')
                        $('#error_studentId').remove()
                        $('#studentId').before('<badge style="color:red;" id="error_studentId">Numéro d\'étudiant invalide !</badge>')
                        $('#studentId').addClass('red-text')
                    } else {
                        $('#studentId').removeClass('red-text')
                        $('#error_studentId').remove()
                    }

                    if(data.errors.email){
                        $('#emailRegister').removeClass('red-text')
                        $('#error_emailRegister').remove()
                        $('#emailRegister').before('<badge style="color:red;" id="error_emailRegister">Email invalide !</badge>')
                        $('#emailRegister').addClass('red-text')
                    } else {
                        $('#emailRegister').removeClass('red-text')
                        $('#error_emailRegister').remove()
                    }

                    if(data.errors.emailBusy){
                        $('#emailRegister').removeClass('red-text')
                        $('#error_emailBusy').remove()
                        $('#emailRegister').before('<badge style="color:red;" id="error_emailBusy">Cet email est déjà lié à un compte !</badge>')
                        $('#emailRegister').addClass('red-text')
                    } else {
                        $('#emailRegister').removeClass('red-text')
                        $('#error_emailBusy').remove()
                    }

                    if(data.errors.password){
                        $('#passwordRegister').removeClass('red-text')
                        $('#error_passwordRegister').remove()
                        $('#passwordRegister').before('<badge style="color:red;" id="error_passwordRegister">Mot de passe invalide !</badge>')
                        $('#passwordRegister').addClass('red-text')
                    } else {
                        $('#passwordRegister').removeClass('red-text')
                        $('#error_passwordRegister').remove()
                    }

                    if(data.errors.confirmPassword){
                        $('confirmRegister').removeClass('red-text')
                        $('#passwordRegister').removeClass('red-text')
                        $('#error_confirm').remove()
                        $('#confirmRegister').before('<badge style="color:red;" id="error_confirm">Les mots de passe ne correspondent pas !</badge>')
                        $('#confirmRegister').addClass('red-text')
                        $('#passwordRegister').addClass('red-text')
                    } else {
                        $('#passwordRegister').removeClass('red-text')
                        $('#confirmRegister').removeClass('red-text')
                        $('#error_confirm').remove()
                    }

                    if(data.errors.captcha){
                        $('#error_recaptcha').removeClass('red-text')
                        $('#error_recaptcha').remove()
                        $('#recaptcha').before('<badge style="color:red;" id="error_recaptcha">Captcha invalide !</badge>')
                        $('#error_recaptcha').addClass('red-text')
                    } else {
                        $('#error_recaptcha').remove()
                    }
                }
            },
            error: function(){
                $('#view_register').html('<p style="color:red;">Erreur serveur, veuillez réessayer dans quelques instants</p>')
            }
        });
    });
});
