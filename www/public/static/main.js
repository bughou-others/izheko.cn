$(function(){
    $('#my-history-a').bind('click mouseenter', function(){
        $('#my-history-span').addClass('my-history-span-on');
        $('#my-history').css('display', 'block');
    });
    $('#my-history-span').mouseleave(function(){
        $('#my-history').css('display', 'none');
        $(this).removeClass('my-history-span-on');
    });
});
