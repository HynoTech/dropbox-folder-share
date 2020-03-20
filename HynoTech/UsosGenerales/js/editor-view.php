<?php
header('Content-Type: application/javascript');
$dataShortcode = unserialize($_GET['ht_sc']);
?>
/* global tinyMCE */
(function($){
	var media = wp.media, shortcode_string = '<?php echo $dataShortcode["Nombre"]; ?>';
	wp.mce = wp.mce || {};
	wp.mce.<?php echo $dataShortcode["Nombre"]; ?> = {
		shortcode_data: {},
		template: media.template( 'bloque_editor' ),
		getContent: function() {
			var options = this.shortcode.attrs.named;
			options.innercontent = this.shortcode.content;
			return this.template(options);
		},
		View: { // before WP 4.2:
			template: media.template( 'bloque_editor' ),
			postID: $('#post_ID').val(),
			initialize: function( options ) {
				this.shortcode = options.shortcode;
				wp.mce.<?php echo $dataShortcode["Nombre"]; ?>.shortcode_data = this.shortcode;
			},
			getHtml: function() {
				var options = this.shortcode.attrs.named;
				options.innercontent = this.shortcode.content;
				return this.template(options);
			}
		},
		edit: function( data ) {
			var shortcode_data = wp.shortcode.next(shortcode_string, data);
			var values = shortcode_data.shortcode.attrs.named;
			values.innercontent = shortcode_data.shortcode.content;
			wp.mce.<?php echo $dataShortcode["Nombre"]; ?>.popupwindow(tinyMCE.activeEditor, values);
		},
		// this is called from our tinymce plugin, also can call from our "edit" function above
		// wp.mce.<?php echo $dataShortcode["Nombre"]; ?>.popupwindow(tinyMCE.activeEditor, "bird");
		<?php include_once $dataShortcode['TemplateJs']; ?>
	};
	wp.mce.views.register( shortcode_string, wp.mce.<?php echo $dataShortcode["Nombre"]; ?> );
}(jQuery));

