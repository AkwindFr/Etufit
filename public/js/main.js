$(function(){
    // hide all content
    $('#main_content').hide();
    // modal connection
    $('.modal').modal();
    // select register form
    $('select').material_select();

    setTimeout(function(){
        // show all content
        $('#main_content').fadeIn();
        // hide preloader
        $("#loader").fadeOut();
        $("#loader").hide();

        $('.button-collapse').sideNav();
    }, 400);
});



