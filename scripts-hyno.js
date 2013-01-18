
/////// Shortcodes Javascript ///////
jQuery(document).ready(function ($) {
    // learn more
    $et_learn_more = $('.hyno_learn_more .heading-more');
    $et_learn_more.live('click', function () {
        if ($(this).hasClass('open')) $(this).removeClass('open');
        else $(this).addClass('open');

        $(this).parent('.img_bible').find('.learn-more-content').animate({
            opacity: 'toggle',
            height: 'toggle'
        }, 300);
    });

    $('.heading-more').not('.open').find('.learn-more-content').css({
        'visibility': 'visible',
        'display': 'none'
    });
});