
/////// Shortcodes Javascript ///////
jQuery(document).ready(function($) {
    //$("td select").msDropDown();

});

jQuery(document).ready(function(e) {
    try {
        e("body select").msDropDown();
    } catch (e) {
        alert(e.message);
    }
});
/*
jQuery.noConflict();
		//jquery stuff
		(function($) {
			//$('p').css('color','#ff0000');
                        $("select").msDropDown();
		})(jQuery);
 */ 