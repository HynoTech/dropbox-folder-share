<?php
/**
 * Created by PhpStorm.
 * User: antony
 * Date: 07/08/18
 * Time: 01:37 AM
 */

namespace HynoTech\DropboxFolderShare;


class TinyMCE extends Principal {

    function __construct() {
        //parent::__construct();
    }

    function dropboxfoldershare_add_button($buttons) {
        array_push($buttons, "|", "DropBoxFolderShare");
        return $buttons;
    }

    function dropboxfoldershare_register_button($plugin_array) {
        $plugin_array['DropBoxFolderShare'] = DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/script/DropBoxFolderShare.js';
        return $plugin_array;
    }

    function dropboxfoldershare_plugin_mce_css($mce_css) {
        if (!empty($mce_css))
            $mce_css .= ',';

        $mce_css .= DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/css/styles-hyno.css';

        return $mce_css;
    }
    function dropboxfoldershare_add_tinymce_translations( $locales ) {

        // Make sure the _WP_Editors exists, if not, load it
        if ( ! class_exists( '_WP_Editors' ) ) {
            require(ABSPATH . WPINC . '/class-wp-editor.php');
        }

        $strings = array(
            'titulo'        => 'DropBox Folder Share WP',
            'descripcion'   => __( 'Carpeta Dropbox', 'dropbox-folder-share' ),
            'txt_url'       => __( 'URL de carpeta', 'dropbox-folder-share' ),
            'txt_necesario' => __( 'Campos Obligatorios', 'dropbox-folder-share' ),
        );

        $locale = _WP_Editors::$mce_locale;
        $translated = 'tinyMCE.addI18n("' . $locale . '.DropBoxFolderShare", ' . json_encode( $strings ) . ");\n";

        //return $translated;

        $locales['DropBoxFolderShare'] = $translated;
        return $locales;
    }

    function dropbox_foldershare_styles_and_scripts($posts) {
        if (empty($posts))
            return $posts;
        $shortcode_found = false; // usamos shortcode_found para saber si nuestro plugin esta siendo utilizado

        //var_dump(count($posts));

        if ( is_array( $posts ) ) {
            foreach ( $posts as $post ) {

                if ( stripos( $post->post_content, 'dropbox-foldershare-hyno' ) ) { //shortcode a buscar
                    $shortcode_found = true; // bingo!
                    break;
                }
                if ( stripos( $post->post_content, 'DFS' ) ) { //shortcode a buscar
                    $shortcode_found = true; // bingo!
                    break;
                }

                if ( stripos( $post->post_content, 'hyno_learn_more' ) ) { //cambiamos testiy por cualquier shortcode
                    $shortcode_found = true; // bingo!
                    break;
                }
            }
        } else {
            echo "<pre>";
            echo $posts;
            echo "----";
            echo stripos( $posts, 'DFS' );
            echo "</pre>";
            if ( stripos( $posts, 'DFS' ) ) { //shortcode a buscar
                $shortcode_found = true; // bingo!

                $posts = do_shortcode( $posts );
            }
            echo "<h3> $shortcode_found </h3>";
        }


        if ($shortcode_found) {
            echo "<h3> $shortcode_found -- ESTA AQUI</h3>";
            // enqueue
            wp_enqueue_script('jquery');
            wp_enqueue_script('thickbox');
            wp_enqueue_script('jquery-ui-tooltip');
            wp_enqueue_script('DFS-Script', DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/scripts-hyno.js', array('jquery'));

            $url_imgLoader = DROPBOX_FOLDER_SHARE_PLUGIN_URL."/src/img/gears.svg";

            wp_localize_script( 'DFS-Script', 'objDFS',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ) ,
                    'dfs_nonce' => wp_create_nonce('dfs_nonce'),
                    'url_imgLoader' => $url_imgLoader
                )
            );

            wp_enqueue_style('thickbox');
            wp_enqueue_style('DFS-Style', DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/css/styles-hyno.css'); //la ruta de nuestro css


            //wp_enqueue_script('bible-post-script', plugins_url('scripts-hyno.js', __FILE__)); //en caso de necesitar la ruta de nuestro script js
        }

        return $posts;
    }

}