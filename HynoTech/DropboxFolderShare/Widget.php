<?php
/**
 * Created by PhpStorm.
 * User: antony
 * Date: 07/08/18
 * Time: 12:03 AM
 */

namespace HynoTech\DropboxFolderShare;


class Widget extends \WP_Widget
{

    protected static $did_script = false;

    function registrarWidget() {
        register_widget('HynoTech\DropboxFolderShare\Widget');
    }

    function __construct() {
        // Constructor del Widget
        $options = array(
            /*'classname' => 'mi-estilo',*/
            'description' => 'Compartir carpeta de dropbox.'
        );
	parent::__construct('DropboxFolderShareWidget', 'Dropbox Folder Share', $options);
        add_action('wp_enqueue_scripts', array($this, 'scripts'));
    }

    function form($instance) {
        // Construye el formulario de administración
        // Valores por defecto
        $options = get_option(Principal::_OPT_SEETINGS_);
        $defaults = array(
            'titulo' => 'Dropbox Folder Share',
            'link' => '',
            'show_icon' => $options['showIcons'],
            'show_size' => $options['showSize'],
            'show_change' => $options['showChange']
        );
        // Se hace un merge, en $instance quedan los valores actualizados
        $instance = wp_parse_args((array)$instance, $defaults);
        // Cogemos los valores
        $titulo = $instance['titulo'];
        $link = $instance['link'];
        $show_icon = $instance['show_icon'];
        $show_size = $instance['show_size'];
        $show_change = $instance['show_change'];
        // Mostramos el formulario
        ?>
        <p>
            <?php _e('Titulo', 'dropbox-folder-share') ?>
            <input class="widefat" type="text" name="<?php echo $this->get_field_name('titulo'); ?>"
                   value="<?php echo esc_attr($titulo); ?>"/>
        </p>
        <p>
            <?php _e('URL de Dropbox', 'dropbox-folder-share') ?>
            <input class="widefat" type="text" name="<?php echo $this->get_field_name('link'); ?>"
                   value="<?php echo esc_attr($link); ?>"/>
        </p>
        <p>
            <?php _e('Iconos', 'dropbox-folder-share') ?>
            <input type="checkbox"
                   name="<?php echo $this->get_field_name('show_icon'); ?>" <?php echo checked(1, $show_icon, false); ?>
                   value="1">
        </p>
        <p>
            <?php _e('Tamaño', 'dropbox-folder-share') ?>
            <input type="checkbox"
                   name="<?php echo $this->get_field_name('show_size'); ?>" <?php echo checked(1, $show_size, false); ?>
                   value="1">
        </p>
        <p>
            <?php _e('Modificado', 'dropbox-folder-share') ?>
            <input type="checkbox"
                   name="<?php echo $this->get_field_name('show_change'); ?>" <?php echo checked(1, $show_change, false); ?>
                   value="1">
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        // Guarda las opciones del Widget
        $instance = $old_instance;
        // Con sanitize_text_field elimiamos HTML de los campos
        $instance['titulo'] = sanitize_text_field($new_instance['titulo']);
        $instance['link'] = sanitize_text_field($new_instance['link']);
        $instance['show_icon'] = $new_instance['show_icon'];
        $instance['show_size'] = $new_instance['show_size'];
        $instance['show_change'] = $new_instance['show_change'];

        return $instance;
    }

    function widget($args, $instance) {
        // Construye el código para mostrar el widget públicamente
        // Extraemos los argumentos del area de widgets

        extract($args);
        $titulo = apply_filters('widget_title', $instance['titulo']);
        $link = $instance['link'];
        $show_icon = $instance['show_icon'];
        $show_size = $instance['show_size'];
        $show_change = $instance['show_change'];
        echo $before_widget;
        echo $before_title;
        echo $titulo;
        echo $after_title;
        //echo '<p>'.$link.'</p>';
        echo do_shortcode("[DFS link='$link' show_icon='$show_icon' show_size='$show_size' show_change='$show_change']");
        echo $after_widget;
    }

    function add_icon_to_custom_widget() {
        ?>
        <style>
            *[id*="_dropboxfoldersharewidget"] > div.widget-top > div.widget-title > h3:before {
                content: url(' ');
                background-image: url('<?php echo plugins_url( 'img/HT-Works.png', __FILE__ ); ?>');
                background-size: 25px 13px;
                width: 26px;
                float: left;
                background-repeat: no-repeat;
            }
        </style>
        <?php
    }

    function scripts() {

        if (!self::$did_script && is_active_widget(false, false, $this->id_base, true)) {

            $objDFSPrincipal = new Principal();
            $objDFSPrincipal->incluir_JS_CSS();
            self::$did_script = true;
        }

    }

}
