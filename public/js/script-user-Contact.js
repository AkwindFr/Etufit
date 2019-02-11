$(function(){
    $('#contact-form').submit(function(e){
        var form = $(this);
        e.preventDefault();

        // On entre dans la requête AJAX
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            dataType: 'json',
            data: form.serialize(),
            success: function(data){
                if(data.success){
                    $('#view_contact').addClass('green_color')
                    $('#view_contact').text('Votre message a bien été envoyé')
                    form.remove()
        // Affichage des erreurs
                }else{
                    if(data.errors.email){
                        $('#email').removeClass('red-text')
                        $('#error_email').remove()
                        $('#email').before('<badge style="color:red;" id="error_email">Email invalide !</badge>')
                        $('#email').addClass('red-text')
                    } else {
                        $('#email').removeClass('red-text')
                        $('#error_email').remove()
                    }

                    if(data.errors.msg){
                        $('#message').removeClass('red-text')
                        $('#error_message').remove()
                        $('#message').before('<badge style="color:red;" id="error_message">Message invalide !</badge>')
                        $('#message').addClass('red-text')
                    } else {
                        $('#message').removeClass('red-text')
                        $('#error_message').remove()
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
                $('#view_contact').html('<p style="color:red;">Erreur serveur, veuillez réessayer dans quelques instants</p>')
            }
        });
    });
});