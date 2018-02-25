<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 11/03/2017
 * Time: 17:09
 */

namespace HynoTech\UsosGenerales;


class TinyMce{
    private static $instance = null;
    var $dataShortcode = array();
    public static function get_instance() {
        if ( ! self::$instance )
            self::$instance = new self;
        return self::$instance;
    }
    public function init($dataShortcode, $arrayObjTraductor){

        $this->dataShortcode = $dataShortcode;

        // comment this 'add_action' out to disable shortcode backend mce view feature
        add_action( 'admin_init', array( $this, 'init_plugin' ), 20 );
        add_shortcode( $this->dataShortcode['Nombre'], $arrayObjTraductor );
        self::$instance = null;
    }

    public function init_plugin() {
        //
        // This plugin is a back-end admin ehancement for posts and pages
        //
        if ( current_user_can('edit_posts') || current_user_can('edit_pages') ) {

            if ($this->dataShortcode['Editor']['ver']){
                add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );
                add_action( 'admin_head', array( $this, 'admin_head' ) );
            }
            if ($this->dataShortcode['Editor']['verBoton']){
                add_filter("mce_external_plugins", array($this, 'mce_plugin'));
                add_filter("mce_buttons", array($this, 'mce_button'));
            }
        }
    }

    public function mce_plugin($plugin_array){
        $plugin_array[$this->dataShortcode['Nombre'].'_mce'] = plugins_url( 'js/mce-button-inline.php?ht_sc='.urlencode(serialize($this->dataShortcode)), __FILE__ );
        return $plugin_array;
    }
    public function mce_button($buttons){
        array_push($buttons, $this->dataShortcode['Nombre'].'_mce_button');
        return $buttons;
    }
    /**
     * Outputs the view inside the wordpress editor.
     */
    public function print_media_templates() {
        if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
            return;
        echo '<script type="text/html" id="tmpl-bloque_editor">';
        include_once $this->dataShortcode['Template'];
        echo '</script>';
    }
    public function admin_head() {
        $current_screen = get_current_screen();
        if ( ! isset( $current_screen->id ) || $current_screen->base !== 'post' ) {
            return;
        }
        wp_enqueue_script( $this->dataShortcode['Nombre'].'-editor-view', plugins_url( 'js/editor-view.php?ht_sc='.urlencode(serialize($this->dataShortcode)), __FILE__ ), array( 'shortcode', 'wp-util', 'jquery' ), false, true );

        if(isset($this->dataShortcode['ajax'])){
            $arrDefaults = array(
                'ajax_url' => admin_url( 'admin-ajax.php' ) ,
                'dfs_nonce' => wp_create_nonce('dfs_nonce')
            );
            $array_merged = array_merge($arrDefaults,$this->dataShortcode['ajax']['array_values']);
            wp_localize_script(
                $this->dataShortcode['Nombre'].'-editor-view',
                $this->dataShortcode['ajax']['object'],
                $array_merged
            );
        }

    }
}