<?php
/**
 *
 * @package     HynoTech\DropboxFolderShare
 * @author      Antonio Salas (Hyno)
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: DropBox Folder Share
 * Plugin URI: http://www.hynotech.com/wp-plugins/dropbox-folder-share/
 * Description: Plugin que permitira incluir carpetas de DropBox en nuestras entradas de blog.
 * Version: 1.8.4
 * Author: Antonio Salas (Hyno)
 * Author URI: http://www.hynotech.com/
 * Twitter: AntonySH_
 * GitHub URI: https://github.com/HynoTech/dropbox-folder-share
 * Text Domain: dropbox-folder-share
 * Domain Path: /languages
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace HynoTech\DropboxFolderShare;

require __DIR__.'/vendor/autoload.php';

use HynoTech\UsosGenerales\TinyMce;

$basename = plugin_basename(__FILE__);
$nombre = dirname($basename);
$url = plugin_dir_url(__FILE__);
$url_path = plugin_dir_path(__FILE__);


define('DROPBOX_FOLDER_SHARE_PLUGIN_BASENAME',$basename); //Indica el directorio hasta el archivo actual "dropbox-folder-share/DropboxFolderShare.php"
define('DROPBOX_FOLDER_SHARE_PLUGIN_NOMBRE',$nombre); //Nombre de la carpeta "dropbox-folder-share"
define('DROPBOX_FOLDER_SHARE_PLUGIN_URL',$url); //URL completa dela carpeta actual "http://localhost:8080/wp/wp-content/plugins/dropbox-folder-share/"
define('DROPBOX_FOLDER_SHARE_PLUGIN_PATH',$url_path); //URL completa dela carpeta actual "d:\Projects\Hosts\wordpress\wp-content\plugins\dropbox-folder-share/"

//echo __DIR__;
/*
use TypistTech\Imposter\ImposterFactory;

$imposter = ImposterFactory::forProject(__DIR__);
$imposter->run();
*/


if (!\class_exists("DropboxFolderSharePrincipal")) {

    $objDropboxFolderSharePrincipal = new Principal();
    //include_once 'src/class/http_build_url.php';
    if (!class_exists("DFS_TinyMCE")) {
        //$objDFS_TinyMCE = new \HynoTech\DropboxFolderShare\TinyMCE();

        add_filter( "the_posts", array( &$objDropboxFolderSharePrincipal, "dropbox_foldershare_styles_and_scripts" ) );
        //add_filter("widget_text", array(&$objDropboxFolderSharePrincipal, "dropbox_foldershare_styles_and_scripts"));
    }

    $objDropboxFolderShareWidget = new Widget();

    add_action( 'widgets_init', array( &$objDropboxFolderShareWidget, 'registrarWidget' ) );
    add_action( 'admin_head-widgets.php', array( &$objDropboxFolderShareWidget, 'add_icon_to_custom_widget' ) );

    // TinyMCE desde Repo Composer

    $opcion = get_option( $objDropboxFolderSharePrincipal::_OPT_SEETINGS_ );


    $dataShortcode = array(
        'Nombre'     => 'DFS',
        'Tipo'       => 'SinContenido',
        'Editor'     => array(
            'ver' => true,
            'verBoton' => true
        ),
        'Boton'      => array(
            'imgUrl'    => plugins_url( 'src/img/TinyMCE_Button.png', __FILE__ ),
            'title'     => 'Dropbox Folder Share',
            'controles' => array(
                array(
                    'type'    => 'textbox',
                    'name'    => 'link',
                    'classes' => '',
                    //'multiline' => 'true',
                    'tooltip' => __( "URL de carpeta compartida de dropbox.", "dropbox-folder-share" ),
                    'label'   => __( "URL de Dropbox.", "dropbox-folder-share" )
                ),
                array(
                    'type'    => 'checkbox',
                    'name'    => 'show_icon',
                    'classes' => '',
                    'tooltip' => __( "Mostrar iconos.", "dropbox-folder-share" ),
                    'label'   => __( "Ver Iconos.", "dropbox-folder-share" ),
                    'checked' => ( $opcion['showIcons'] == 1 ) ? 'checked' : ''
                ),
                array(
                    'type'    => 'checkbox',
                    'name'    => 'show_size',
                    'classes' => '',
                    'tooltip' => __( "Mostrar Tamaño.", "dropbox-folder-share" ),
                    'label'   => __( "Ver Tamaño.", "dropbox-folder-share" ),
                    'checked' => ( $opcion['showSize'] == 1 ) ? 'checked' : ''
                ),
                array(
                    'type'    => 'checkbox',
                    'name'    => 'show_change',
                    'classes' => '',
                    'tooltip' => __( "Mostrar fecha de modificacion.", "dropbox-folder-share" ),
                    'label'   => __( "Ver Modificado.", "dropbox-folder-share" ),
                    'checked' => ( $opcion['showChange'] == 1 ) ? 'checked' : ''
                )
            )
        ),
        'Template'   => dirname( __FILE__ ) . "/src/template/editorTemplate.php",
        'TemplateJs' => dirname( __FILE__ ) . "/src/template/TinyEditorConfig.js",
        'ajax'       => array(
            'object'       => 'objDFS',
            'array_values' => array()
        )
    );

    $dataShortcode2           = $dataShortcode;
    $dataShortcode2["Nombre"] = "dropbox-foldershare-hyno";
    //$dataShortcode2['Editor']['ver'] = false;
    $dataShortcode2['Editor']['verBoton'] = false;


    $objTinyMCE = new TinyMce;

    $objTinyMCE::get_instance()->init( $dataShortcode, array( &$objDropboxFolderSharePrincipal, 'replace_shortcode' ) );

    $objTinyMCE::get_instance()->init( $dataShortcode2, array(
        &$objDropboxFolderSharePrincipal,
        'replace_shortcode'
    ) );


    //add_action( 'admin_init', array( &$objDropboxFolderSharePrincipal, 'my_detect_acf' ) );

    add_action( 'admin_head', array( &$objDropboxFolderSharePrincipal, 'nota_assets' ) );
    //amarkal_reset_admin_notification($objDropboxFolderSharePrincipal::_OPT_SEETINGS_. "-nota-nota_actualizacion");
    //amarkal_reset_admin_notification($objDropboxFolderSharePrincipal::_OPT_SEETINGS_. "-nota-donacion");

    amarkal_admin_notification(
        $objDropboxFolderSharePrincipal::_OPT_SEETINGS_ . "-nota-nota_actualizacion",
        $objDropboxFolderSharePrincipal->nota_actualizacion(),
        'success',
        true
    );
    amarkal_admin_notification(
        $objDropboxFolderSharePrincipal::_OPT_SEETINGS_ . "-nota-donacion",
        //__('Listen carefully, this is an <strong>warning</strong> message.','slug'),
        $objDropboxFolderSharePrincipal->nota_donacion(),
        'error',
        true
    );


    //add_shortcode('dropbox-foldershare-hyno', array(&$objDropboxFolderSharePrincipal, 'replace_shortcode'));
    //add_shortcode('DFS', array(&$objDropboxFolderSharePrincipal, 'replace_shortcode'));

    //AJAX
    add_action( 'wp_ajax_getFolderContent', array(&$objDropboxFolderSharePrincipal, 'ajaxReplaceShortcode') );
    add_action( 'wp_ajax_nopriv_getFolderContent', array(&$objDropboxFolderSharePrincipal, 'ajaxReplaceShortcode' ) );

    add_action( 'wp_ajax_getFolderHeaders', array( &$objDropboxFolderSharePrincipal, 'ajaxGetHeaders' ) );
    add_action( 'wp_ajax_nopriv_getFolderHeaders', array( &$objDropboxFolderSharePrincipal, 'ajaxGetHeaders') );

    add_action( 'wp_ajax_getImgBase64', array( &$objDropboxFolderSharePrincipal, 'ajaxGetImgBase64' ) );
    add_action( 'wp_ajax_nopriv_getImgBase64', array( &$objDropboxFolderSharePrincipal, 'ajaxGetImgBase64') );

}
