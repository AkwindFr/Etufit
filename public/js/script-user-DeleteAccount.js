$(function () {
    $('#deleteAccount').on('click', function () {

        $.post("{{ url('deleteProfil') }}", function (data) {
            if (data.success_delete_user) {
                $('#deleteView').text('Compte supprimé')
            } else {
                if (data.errors.user_undefined) {
                    $('#deleteView').text('Utilisateur inconnu')
                }
            }
            if (data.success_delete_user) {
                setTimeout(function () {
                    if (data.redirect) {
                        window.location.replace(data.redirect);
                    }
                }, 1500)
            }
        })
    })
});