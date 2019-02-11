// Formulaire phase 2
$(function(){
    $('#formProfilPassword').submit(function(e){ // On sélectionne le formulaire
        var form = $(this);
        e.preventDefault(); // Prévient l'événement par défaut

        $.ajax({
            url: form.attr('action'), // l'adresse ou on envoie les données
            type: form.attr('method'), // type de méthod ici "POST"
            dataType: 'json', // Type de donnée échangée
            data: form.serialize(), // mise en forme des données pour l'envoie et la réception
            success: function(data){ // En cas de succès de la requête
                if(data.success){ // SI la donnée succes existe
                    form.remove(); // On suprime le formulaire
                    $('#view_profil_password').html('<p style="color:green;">Votre mot de passe a été modifié avec succès</p>'); // On affiche le message de succès
                } else {
                    if(data.errors.password){ // Si l'erreur password existe dans le tableau errors
                        $('#profilPassword').removeClass('red-text') // on nettoie le champ avant d'insérer le nouveau message
                        $('#error_password').remove() // on nettoie le champ avant d'insérer le nouveau message
                        $('#profilPassword').before('<badge style="color:red;" id="error_password">Mot de passe invalide !</badge>') // On affiche le message d'ereur
                        $('#profilPassword').addClass('red-text') // On ajoute la classe pour avoir un texte rouge
                    }

                    if(data.errors.newPassword){
                        $('#newPassword').removeClass('red-text')
                        $('#error_new').remove()
                        $('#newPassword').before('<badge style="color:red;" id="error_new">Nouveau mot de passe invalide !</badge>')
                        $('#newPassword').addClass('red-text')
                    }

                    if(data.errors.confirmPassword){
                        $('#confirmPassword').removeClass('red-text')
                        $('#error_confirm').remove()
                        $('#confirmPassword').before('<badge style="color:red;" id="error_confirm">Les mots de passe ne correspondent pas !</badge>')
                        $('#confirmPassword').addClass('red-text')
                    }

                    if(data.errors.user_undefined){
                        $('#view_profil_password').removeClass('red-text')
                        $('#error_undefined').remove()
                        $('#view_profil_password').before('<badge style="color:red;" id="error_undefined">L\'utilisateur recherché n\'existe pas !</badge>')
                        $('#view_profil_password').addClass('red-text')
                    }

                    if(data.errors.wrong_password){
                        $('#profilPassword').removeClass('red-text')
                        $('#error_password').remove()
                        $('#profilPassword').before('<badge style="color:red;" id="error_">Le mot de passe ne correspond pas au compte !</badge>')
                        $('#profilPassword').addClass('red-text')
                    }
                }
            },
            error: function(){
                $('#view_profil_password').html('Erreur serveur, veuillez réessayer dans quelques instants')
            }
        });
    });
})