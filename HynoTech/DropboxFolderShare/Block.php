<?php
/**
 * Created by PhpStorm.
 * User: antony
 * Date: 07/08/18
 * Time: 12:03 AM
 */

namespace HynoTech\DropboxFolderShare;


class Block
{
	public function __construct() {
		add_filter( 'block_categories', array($this, 'registrarTipoBlock'), 10, 2);
	}

	function registrarBlock() {
		// Register JavasScript File build/index.js
		wp_register_script(
			'DFS-block',
			DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'build/index.js',//plugins_url( '../build/index.js', __FILE__ ),
			array( 'wp-blocks', 'wp-element', 'wp-editor' ),
			filemtime( DROPBOX_FOLDER_SHARE_PLUGIN_PATH . 'build/index.js' )
		);

		// Enviar Variables a JS Block
		wp_localize_script('DFS-block', 'DFSParams', array(
			'pluginUrl' => DROPBOX_FOLDER_SHARE_PLUGIN_URL,
		));


		// Register editor style src/editor.css
		wp_register_style(
			'DFS-block-editor-style',
			DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/css/editor.css', //plugins_url( 'css/editor.css', __FILE__ ),
			array( 'wp-edit-blocks' ),
			filemtime( DROPBOX_FOLDER_SHARE_PLUGIN_PATH . 'src/css/editor.css' )
		);
		// Register front end block style src/style.css
		wp_register_style(
			'DFS-block-frontend-style',
			DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/css/front.css', //plugins_url( 'css/front.css', __FILE__ ),
			array( ),
			filemtime( DROPBOX_FOLDER_SHARE_PLUGIN_PATH . 'src/css/front.css' )
		);
		// Register your block
		register_block_type( 'dropbox-folder-share/block-editor', array(
			'editor_script' => 'DFS-block',
			'editor_style' => 'DFS-block-editor-style',
			'style' => 'DFS-block-frontend-style',
			// 'render_callback' => array($this, 'renderCallback')
		) );

	}

	function renderCallback($attr) {
		echo "<pre>";
		var_dump($attr);
		echo "</pre>";
	}

	function registrarTipoBlock($categories, $post){


    	$arryExtra = [];

		if(!in_array('hynotech', array_column($categories, 'slug'))) {
			// var_dump("NO ENCONTRADO");
			$arryExtra = [
				[
					'slug' => 'hynotech',
					'title' => 'HynoTech',
				]
			];
		}

		return array_merge($categories, $arryExtra);
		return array_merge(
			$categories,
			array(
				array(
					'slug' => 'mario-blocks',
					'title' => __( 'Mario Blocks', 'mario-blocks' ),
				),
			)
		);
	}

}
