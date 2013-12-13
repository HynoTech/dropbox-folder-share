<?php

class DFS_TinyMCE extends DropboxFolderSharePrincipal {
    
    function __construct() {
        //parent::__construct();
    }

    function dropboxfoldershare_add_button($buttons) {
        array_push($buttons, "|", "DropBoxFolderShare");
        return $buttons;
    }

    function dropboxfoldershare_register_button($plugin_array) {
        $urlScriptMCE = $url_biblepost =  parent::$url . 'script/DropBoxFolderShare.js';
        $plugin_array['DropBoxFolderShare'] = $url_biblepost;
        return $plugin_array;
    }

    function dropboxfoldershare_plugin_mce_css($mce_css) {
        if (!empty($mce_css))
            $mce_css .= ',';

        $mce_css .= parent::$url . 'styles-hyno.css';

        return $mce_css;
    }

    function dropbox_foldershare_styles_and_scripts($posts) {
        if (empty($posts))
            return $posts;
        $shortcode_found = false; // usamos shortcode_found para saber si nuestro plugin esta siendo utilizado
        foreach ($posts as $post) {

            if (stripos($post->post_content, 'dropbox-foldershare-hyno')) { //shortcode a buscar
                $shortcode_found = true; // bingo!
                break;
            }
            if (stripos($post->post_content, 'DFS')) { //shortcode a buscar
                $shortcode_found = true; // bingo!
                break;
            }

            if (stripos($post->post_content, 'hyno_learn_more')) { //cambiamos testiy por cualquier shortcode
                $shortcode_found = true; // bingo!
                break;
            }
        }
        if ($shortcode_found) {
            // enqueue
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-tooltip');
            wp_enqueue_script('DFS-Script', parent::$url . 'scripts-hyno.js', array('jquery'));

            wp_enqueue_style('DFS-Style', parent::$url . 'styles-hyno.css'); //la ruta de nuestro css
            //wp_enqueue_script('bible-post-script', plugins_url('scripts-hyno.js', __FILE__)); //en caso de necesitar la ruta de nuestro script js
        }

        return $posts;
    }

}
