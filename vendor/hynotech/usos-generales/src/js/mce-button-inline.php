<?php
header('Content-Type: application/javascript');
$dataShortcode = unserialize($_GET['ht_sc']);
//var_dump($dataShortcode);
?>
/* global tinymce */
( function() {
	tinymce.PluginManager.add( '<?php echo $dataShortcode["Nombre"]; ?>_mce', function( editor ) {
		editor.addButton( '<?php echo $dataShortcode["Nombre"]; ?>_mce_button', {
            title: '<?php echo $dataShortcode["Boton"]["title"]; ?>',
            image : '<?php echo $dataShortcode["Boton"]["imgUrl"]; ?>',
			onclick: function() {
				wp.mce.<?php echo $dataShortcode["Nombre"]; ?>.popupwindow(editor);
			}
		});
	});
})();
