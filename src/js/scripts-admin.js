/**
 * Created by Antony on 6/02/2016.
 */

jQuery(function($) {
    /**
     * TABS
     */
    var hash = window.location.hash;
    if (hash != '') {
        $('.nav-tab-wrapper').children().removeClass('nav-tab-active');
        $('.nav-tab-wrapper a[href="' + hash + '"]').addClass('nav-tab-active');

        $('.tabs-content').children().addClass('hidden');
        $('.tabs-content div' + hash.replace('#', '#tab-')).removeClass('hidden');
    }

    $('.nav-tab-wrapper a').click(function () {
        var tab_id = $(this).attr('href').replace('#', '#tab-');

        // active tab
        $(this).parent().children().removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        // active tab content
        $('.tabs-content').children().addClass('hidden');
        $('.tabs-content div' + tab_id).removeClass('hidden');
    });
});