$(function(){
    /* hide all content
    $('#main_content').hide();
    */

    // modal connection
    $('.modal').modal();
    // select register form
    $('select').material_select();
    $('.button-collapse').sideNav();

    $('.timepicker').on('mousedown',function(event){
        event.preventDefault();
    })


    /* loader */
    // setTimeout(function(){
    //     // show all content
    //     $('#main_content').fadeIn();
    //     // hide preloader
    //     $("#loader").fadeOut();
    //     $("#loader").hide();
    // }, 400);
});



