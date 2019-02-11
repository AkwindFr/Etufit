$(function(){
    $('#formAddSlot').submit(function(e){
        var form = $(this);
        e.preventDefault();
        var val_open = $('#open').val()
        var val_close = $('#close').val()
        var newVal_open = $('#open').val(slot + val_open)
        var newVal_close = $('#close').val(slot + val_close)

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            dataType: 'json',
            data: form.serialize(),
            success: function(data){
                if(data.success){
                    form.remove();
                    $('#view_add').html('<p style="color:green;">Créneau créé avec succès !</p>');
                    location.reload()
                } else {
                    $('#open').attr('placeholder', "Entrer une date de début ") // Pre-load the placeholder of selected event into the modal fields
                    $('#close').attr('placeholder', "Entrer une date de fin ")
                    $('#open').val("") // Pre-load the dates of selected event into the modal fields
                    $('#close').val("")
                    if(data.errors.referent){
                        $('#referent').removeClass('red_color')
                        $('#error_referent').remove()
                        $('#referent').before('<badge style="color:red;" id="error_referent">Referent invalide !</badge>')
                        $('#referent').addClass('red_color')
                    } else {
                        $('#referent').removeClass('red_color')
                        $('#error_referent').remove()
                    }

                    if(data.errors.places){
                        $('#places').removeClass('red_color')
                        $('#error_places').remove()
                        $('#places').before('<badge style="color:red;" id="error_places">Nombre de place invalide !</badge>')
                        $('#places').addClass('red_color')
                    } else {
                        $('#places').removeClass('red_color')
                        $('#error_places').remove()
                    }

                    if(data.errors.open){
                        $('#open').removeClass('red_color')
                        $('#error_open').remove()
                        $('#open').before('<badge style="color:red;" id="error_open">Horaire d\'ouverture invalide !</badge>')
                        $('#open').addClass('red_color')
                    } else {
                        $('#open').removeClass('red_color')
                        $('#error_open').remove()
                    }

                    if(data.errors.close){
                        $('#close').removeClass('red_color')
                        $('#error_close').remove()
                        $('#close').before('<badge style="color:red;" id="error_close">Horaire de fermeture invalide !</badge>')
                        $('#close').addClass('red_color')
                    } else {
                        $('#close').removeClass('red_color')
                        $('#error_close').remove()
                    }

                    if(data.errors.dateEnd){
                        $('#close').removeClass('red_color')
                        $('#error_close').remove()
                        $('#close').before('<badge style="color:red;" id="error_close">L\'horaire de cloture ne peut pas être antérieur à l\'ouverture !</badge>')
                        $('#close').addClass('red_color')
                    } else {
                        $('#close').removeClass('red_color')
                        $('#error_close').remove()
                    }

                    if(data.errors.dateOver){
                        $('#open').removeClass('red_color')
                        $('#error_date').remove()
                        $('#open').before('<badge style="color:red;" id="error_date">La date d\'ouverture demandée est dépassée !</badge>')
                        $('#open').addClass('red_color')
                    } else {
                        $('#open').removeClass('red_color')
                        $('#error_date').remove()
                    }
                }
            },
            error: function(){
                $('#view_add').html('<p style="color:red;">Erreur serveur, veuillez réessayer dans quelques instants</p>')
            }
        });
    });
})

