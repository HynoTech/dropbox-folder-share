<?php
class DFS_Admin extends DropboxFolderSharePrincipal {

    public function __construct() {
        //parent::__construct();
    }

    function pagAdmin() {
        add_options_page(
                '[HT]DropBox Folder Share', /* Titulo Pagina */ 
                '[HT]DropBox Folder Share', /* Titulo Menu */ 
                parent::_PERMISOS_REQUERIDOS_, 
                parent::$nombre, 
                array(&$this, 'pagina_de_opciones')
        );
    }
function display_options_page() {

                        if( !current_user_can( parent::_PERMISOS_REQUERIDOS_ ) ) {
                                /* TRANSLATORS: no need to translate - standard WP core translation will be used */
                                wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
                        }

                        echo '
                <div class="wrap">
                <img src="" />
                <img src="'. parent::$url.'img/logo.png" />
                <form action="options.php" method="post">';
                        echo parent::_OPT_SEETINGS_;
                        settings_fields( parent::_OPT_SEETINGS_ . '-group' );
                        do_settings_sections( parent::$nombre );
                        submit_button();

                        echo '
                </form>';

          //              if( WP_DEBUG ) {
                                print '<pre>';
                                print_r( get_option(parent::_OPT_SEETINGS_) );
                                print '</pre>';
            //            }
                }
    function pagina_de_opciones() {
        require_once (parent::$url_path . 'admin/admin_page.php');
    }
function display_options_page2() {
echo '<div class="wrap">
    <div class="icon32" id="icon-options-general"></div>
    <h2>' . __( 'Theme Options' ) . '</h2>';

if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true ){
			echo '<div class="updated fade"><p>' . __( 'Theme options updated.' ) . '</p></div>';
}
?>
		<?php
			if( isset( $_GET[ 'tab' ] ) ) {
				$active_tab = $_GET[ 'tab' ];
			} // end if
		?>
	<h2 class="nav-tab-wrapper">
		<a href="?page=dropbox-folder-share&tab=display_options" class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>">Display Options</a>
		<a href="?page=dropbox-folder-share&tab=social_options" class="nav-tab <?php echo $active_tab == 'social_options' ? 'nav-tab-active' : ''; ?>">Social Options</a>
	</h2>


<?php
echo '<form action="options.php" method="post">';
settings_fields( parent::_OPT_SEETINGS_ .'-group');
do_settings_sections( parent::$nombre );
echo '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __( 'Save Changes' ) . '" /></p>';
echo '</form>';
echo '</div>';
}
    function plugin_admin_init() {
register_setting(
                                parent::_OPT_SEETINGS_ . '-group',
                                parent::_OPT_SEETINGS_, // option name
                                array( &$this, 'validate_options' ) // validation callback
                        );
add_settings_section(
                                        'dfs-instrucciones-settings', // id
                                        '', // title
                                        array( &$this, 'do_settings_section_instrucciones'), // callback for this section
                                        parent::$nombre // page menu_slug
                                );



        register_setting(parent::_OPT_SEETINGS_, 'main_options', 'DFS_options_validate');
        add_settings_section('idSeccionInstrucciones', '<Instrucciones de Uso', 'instruccionesSection',parent::$nombre);
        add_settings_field('idCampoInstrucciones', 'Instrucciones', 'plugin_setting_string', parent::$nombre, 'idSeccionInstrucciones');
        //add_settings_section('instrucciones', 'Instrucciones de Uso', 'instruccionesSection', 'configPage_unique');

        //register_setting('plugin_options', 'plugin_options', 'plugin_options_validate');
        //add_settings_section('plugin_main', 'Main Settings', 'plugin_section_text', 'plugin');
        //add_settings_section('plugin_conexion', 'Tipo de Conexion', 'HTML_seccionTipoConexion', 'plugin');
        //add_settings_field('plugin_text_string', 'Plugin Text Input', 'plugin_setting_string', 'plugin', 'plugin_main');
        //add_settings_field('id_tipo_conexion', 'Tipo de Conexion a usar :D', 'tipoConexionHTML', 'plugin', 'plugin_conexion');
    }
    
    
    
    function validate_options($input){
            $options = get_option(parent::_OPT_SEETINGS_);
    $options['test1'] = trim($input['test1']);
    $options['test2'] = trim($input['test2']);
    /* if (!preg_match('/^[a-z0-9]{32}$/i', $options['text_string'])) {
      $options['text_string'] = '';
      } */
    return $options;
    }
    
    function do_settings_section_instrucciones() {
?>
<div class="wrap">
        <div id="poststuff">
            <div class="metabox-holder columns-2" id="post-body">
                <div id="post-body-content">
                    <div class="stuffbox" id="instrucciones_hyno">
                        <h3><?php _e('Instrucciones de Uso', 'dropbox-folder-share'); ?></h3>
                        <div class="inside">
                            <ol>
                                <!--<li><?php _e('Usar el widget para compartir una carpeta', 'dropbox-folder-share'); ?></li>-->
                                <li><?php _e('Usa la etiqueta [dropbox-foldershare-hyno] para compartir una carpeta en tus post:', 'dropbox-folder-share'); ?><br />
                                    [dropbox-foldershare-hyno link="LNK_FOLDER" ver_como='lista'] (Parametros abajo)</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<?php
                }
    
    
    
    function instruccionesSection(){
        ?>
                    <div class="stuffbox" id="instrucciones_hyno">
                        <h3>YA ESTAMOS</h3>
                        <div class="inside">
YA ESTA
                        </div>
                    </div>
            <?php
    }
function plugin_setting_string() {
    $options = get_option('plugin_options');
    echo "<input id='plugin_text_string' name='plugin_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
}
}
