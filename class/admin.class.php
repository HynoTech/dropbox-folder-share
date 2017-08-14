<?php

class DFS_Admin extends DropboxFolderSharePrincipal
{
    protected $plugin_screen_hook_suffix = null;

    public function __construct()
    {
        //parent::__construct();
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    function pagAdmin()
    {
        $this->plugin_screen_hook_suffix = add_options_page(
            '[HT]Dropbox Folder Share', //Titulo Pagina
            '<img width="17px" src="'. parent::$url .'/img/HT-Works.png" alt="" /> Dropbox Folder Share', // Titulo Menu
            parent::_PERMISOS_REQUERIDOS_, //Permisos Necesarios
            parent::$nombre, //id Unico del Menu
            array(&$this, 'pagina_de_opciones') //Que mostrar?
        );
    }

    function pagina_de_opciones()
    {
        require_once(parent::$url_path . 'admin/admin_page.php');
    }

    function plugin_admin_init()
    {
        register_setting(
            parent::_OPT_SEETINGS_ . '-group', //Nombre del Grupo de Opciones
            parent::_OPT_SEETINGS_, // Nombre de la Opcion a Guardar
            array(&$this, 'validate_options') // Funcion de Validacion de Opciones
        );

        $secciones = array(
            "visualizacion" => array(
                'titulo' => __("Visualizacion", "dropbox-folder-share"),
                'campos' => array(
	                'UseAjax'            => __('Usar Ajax', "dropbox-folder-share"),
	                'ShowIcons'          => __('Mostrar Iconos', "dropbox-folder-share"),
	                'ShowSize'           => __('Mostrar Tamaño de Archivo', "dropbox-folder-share"),
	                'ShowChange'         => __('Mostrar Fecha de Modificacion', "dropbox-folder-share"),
	                'ShowThumbnail'      => __( 'Mostrar Miniaturas', "dropbox-folder-share" ),
	                'dbNativeViewer'     => __( 'Usar visualizador de archivos Nativo de Dropbox', "dropbox-folder-share" ),
	                'imagesPopup'        => __('Mostrar imagenes usando ThickBox', "dropbox-folder-share"),
	                'thickboxTypesPopup' => __('Extensiones adicionales en ThickBox', "dropbox-folder-share"),
	                'defaultHeightBox'   => __( 'Altura maxima del contenedor', "dropbox-folder-share" ),
	                'showInEditor'       => __( 'Mostrar bloque en el editor', "dropbox-folder-share" ),
	                'datetimeFormat'     => __( 'Formato de fecha y hora', "dropbox-folder-share" )
                )
            ),
            "vinculacion" => array(
                'titulo' => __("Vinculacion", "dropbox-folder-share"),
                'campos' => array(
                    'allowDownload' => __('Permitir Descarga de Archivos', "dropbox-folder-share"),
                    'allowDownloadFolder' => __('Permitir Descarga de Carpeta (comprimido)', "dropbox-folder-share"),
                    'allowBrowseFolder' => __('Permitir Navegacion entre Carpetas', "dropbox-folder-share"),
                    'link2Folder' => __('Dejar Link de Carpeta Compartida', "dropbox-folder-share"),
                )
            ),
            "conexion" => array(
                'titulo' => __("Conexion", "dropbox-folder-share"),
                'campos' => array(
                    'tipoConexion' => __('Tipo de Conexion a Usar', "dropbox-folder-share")
                )
            )
        );

        foreach ($secciones as $indice => $contenido) {
            add_settings_section(
                'DFS_SECCION_' . $indice,                 // para usarse en el atributo ID de las etiquetas
                $contenido['titulo'],                   // Titulo de la Seccion
                array(&$this, 'printSeccion_' . $indice), // Funcion que llena la seccion con contenido (HTML)
                parent::$nombre                         // page menu_slug para ser usado en do_settings_sections()
            );
            foreach ($contenido['campos'] as $idxCampo => $tituloCampo) {
                add_settings_field(
                    'id_' . $indice . '_' . $idxCampo,                    // texto a ser usado en el ID de las etiquetas
                    $tituloCampo,                                   //Titulo del Campo
                    array(&$this, 'print_' . $indice . '_' . $idxCampo . 'Input'),    // funcion que da la salida del campo como parte del form
                    parent::$nombre,                                //pagina de menu que mostrara este campo (do_settings_sections())
                    'DFS_SECCION_' . $indice,                         // id de la seccion a la que pertenece (primer argumento de add_settings_section()
                    array(
                        'label_for' => 'id_' . $indice . '_' . $idxCampo  //array de datos adicionales a enviar al callback
                    )
                );
            }
        }
    }

    //HTML DE  SECCION VISUALIZACION
    function printSeccion_visualizacion()
    {
        echo '';
    }

    //campo ShowIcon
    function print_visualizacion_UseAjaxInput()
    {
        $options = get_option(parent::_OPT_SEETINGS_);
        ?>
        <input
            id="id_visualizacion_UseAjax"
            type="checkbox"
            name="<?php echo parent::_OPT_SEETINGS_; ?>[UseAjax]"
            value="1"
            <?php echo checked(1, $options['UseAjax'], false); ?>
        />
        <?php
    }

    //campo ShowIcon
    function print_visualizacion_ShowIconsInput()
    {
        $options = get_option(parent::_OPT_SEETINGS_);
        //echo "<pre>";
        //print_r($options);
        //echo "</pre>";
        ?>
        <input
            id="id_visualizacion_ShowIcons"
            type="checkbox"
            name="<?php echo parent::_OPT_SEETINGS_; ?>[showIcons]"
            value="1"
            <?php echo checked(1, $options['showIcons'], false); ?>
        />
        <?php
    }

    //Campo ShowSize
    function print_visualizacion_ShowSizeInput()
    {
        $options = get_option(parent::_OPT_SEETINGS_);
        ?>
        <input
            id="id_visualizacion_ShowSize"
            type="checkbox"
            name="<?php echo parent::_OPT_SEETINGS_; ?>[showSize]"
            value="1"
            <?php echo checked(1, $options['showSize'], false); ?>
        />
        <?php
    }

    //Campo ShowChange
    function print_visualizacion_ShowChangeInput()
    {
        $options = get_option(parent::_OPT_SEETINGS_);
        ?>
        <input
            id="id_visualizacion_ShowChange"
            type="checkbox"
            name="<?php echo parent::_OPT_SEETINGS_; ?>[showChange]"
            value="1"
            <?php echo checked(1, $options['showChange'], false); ?>
        />
        <br/><br/>
        <?php
    }

	//Campo showThumbnail
	function print_visualizacion_ShowThumbnailInput() {
		$options = get_option( parent::_OPT_SEETINGS_ );
		if ( ! isset( $options['showThumbnail'] ) ) {
			$options['showThumbnail'] = 1;
		}
		?>
        <input
                id="id_visualizacion_ShowThumbnail"
                type="checkbox"
                name="<?php echo parent::_OPT_SEETINGS_; ?>[showThumbnail]"
                value="1"
			<?php echo checked( 1, $options['showThumbnail'], false ); ?>
        />
        <br/><br/>
		<?php
	}

    //Campo imagesPopup
    function print_visualizacion_imagesPopupInput()
    {
        $options = get_option(parent::_OPT_SEETINGS_);
        ?>
        <input
            id="id_visualizacion_imagesPopup"
            type="checkbox"
            name="<?php echo parent::_OPT_SEETINGS_; ?>[imagesPopup]"
            value="1"
            <?php echo checked(1, $options['imagesPopup'], false); ?>
        />
        <br/><br/>
        <?php
    }

	//Campo
	function print_visualizacion_dbNativeViewerInput() {
		$options = get_option( parent::_OPT_SEETINGS_ );
		if ( ! isset( $options['dbNativeViewer'] ) ) {
			$options['dbNativeViewer'] = 1;
		}
		?>
        <input
                id="id_visualizacion_dbNativeViewer"
                type="checkbox"
                name="<?php echo parent::_OPT_SEETINGS_; ?>[dbNativeViewer]"
                value="1"
			<?php echo checked( 1, $options['dbNativeViewer'], false ); ?>
        />
        <br/><br/>
		<?php
	}
    //Campo thickboxTypesPopup
    function print_visualizacion_thickboxTypesPopupInput()
    {
        $options = get_option(parent::_OPT_SEETINGS_);
        ?>
        <input
                id="id_visualizacion_thickboxTypes"
            type="text"
            name="<?php echo parent::_OPT_SEETINGS_; ?>[thickboxTypes]"
            value="<?php echo $options['thickboxTypes']; ?>"
        />
        <p class="description"><?php _e("Separado por comas", "dropbox-folder-share"); ?></p>
        <br/><br/>
        <?php
    }
    //Campo defaultHeightBox
    function print_visualizacion_defaultHeightBoxInput()
    {
        $options = get_option(parent::_OPT_SEETINGS_);
        ?>
        <input
            id="id_visualizacion_defaultHeightBox"
            type="text"
            name="<?php echo parent::_OPT_SEETINGS_; ?>[defaultHeight]"
            value="<?php echo $options['defaultHeight']; ?>"
        />
        <p class="description"><?php _e("0 para desabilitar y dejar altura automatica", "dropbox-folder-share"); ?></p>
        <br/><br/>
        <?php
    }

	function print_visualizacion_showInEditorInput() {
		$options = get_option( parent::_OPT_SEETINGS_ );
		?>
        <input
                id="id_visualizacion_showInEditor"
                type="checkbox"
                name="<?php echo parent::_OPT_SEETINGS_; ?>[showInEditor]"
                value="1"
			<?php echo checked( 1, $options['showInEditor'], false ); ?>
        />
        <p class="description"><?php _e( "Permite visualizar un bloque en el editor cuando se visualiza una carpeta compartida", "dropbox-folder-share" ); ?></p>
        <br/><br/>
		<?php
	}

	function print_visualizacion_datetimeFormatInput() {
		$options = get_option( parent::_OPT_SEETINGS_ );
		if ( ! isset( $options['datetimeFormat'] ) ) {
			$options['datetimeFormat'] = get_option( 'date_format' ) . " " . get_option( 'time_format' );
		}
		?>
        <input
                id="id_visualizacion_datetimeFormat"
                type="text"
                name="<?php echo parent::_OPT_SEETINGS_; ?>[datetimeFormat]"
                value="<?php echo $options['datetimeFormat']; ?>"
        />
        <br/><br/>
		<?php
	}
    //FIN SECCION VISUALIZACION

    //HTML DE SECCION VINCULACION
    function printSeccion_vinculacion()
    {
        echo '';
    }

    //Seccion allowDownload
    function print_vinculacion_allowDownloadInput()
    {
        $options = get_option(parent::_OPT_SEETINGS_);
        ?>
        <input
            id="id_vinculacion_allowDownload"
            type="checkbox"
            name="<?php echo parent::_OPT_SEETINGS_; ?>[allowDownload]"
            value="1"
            <?php echo checked(1, $options['allowDownload'], false); ?>
        />
        <?php
    }

    //Seccion allowDownloadFolder
    function print_vinculacion_allowDownloadFolderInput()
    {
        $options = get_option(parent::_OPT_SEETINGS_);
        //var_dump($options);
        ?>
        <input
            id="id_vinculacion_allowDownloadFolder"
            type="checkbox"
            name="<?php echo parent::_OPT_SEETINGS_; ?>[allowDownloadFolder]"
            value="1"
            <?php echo checked(1, $options['allowDownloadFolder'], false); ?>
        />
        <?php
    }

    //Seccion allowBrowseFolder
    function print_vinculacion_allowBrowseFolderInput()
    {
        $options = get_option(parent::_OPT_SEETINGS_);
        //var_dump($options);
        //allowDownload
        $disable =($options['allowDownload'] != '1')?'disabled':'';
        ?>
        <input
            <?php echo $disable; ?>
            id="id_vinculacion_allowBrowseFolder"
            type="checkbox"
            name="<?php echo parent::_OPT_SEETINGS_; ?>[allowBrowseFolder]"
            value="1"
            <?php echo checked(1, $options['allowBrowseFolder'], false); ?>
        />
        <?php
    }

    //Seccion link2Folder
    function print_vinculacion_link2FolderInput()
    {
        $options = get_option(parent::_OPT_SEETINGS_);
        ?>
        <input
            id="id_vinculacion_link2Folder"
            type="checkbox"
            name="<?php echo parent::_OPT_SEETINGS_; ?>[link2Folder]"
            value="1"
            <?php echo checked(1, $options['link2Folder'], false); ?>
        />
        <br/><br/>
        <?php
    }
    //FIN SECCION VINCULACION

    //HTML SECCION CONEXION
    function printSeccion_conexion()
    {
        echo '';
    }

    //Seccion tipoConexion
    function print_conexion_tipoConexionInput()
    {
        $options = get_option(parent::_OPT_SEETINGS_);
        ?>
        <select id="id_conexion_tipoConexion" name="<?php echo parent::_OPT_SEETINGS_; ?>[tipoConexion]">
            <option value="fopen" <?php echo selected($options['tipoConexion'], "fopen", false); ?>>fopen</option>
            <option value="curl" <?php echo selected($options['tipoConexion'], "curl", false); ?>>cURL</option>
        </select>
        <br/><br/>
        <?php
    }

    //FUNCION DE VALIDACION DE DATOS
    function validate_options($input)
    {

        $options = get_option(parent::_OPT_SEETINGS_);
        $options['UseAjax'] = trim($input['UseAjax']);
        $options['showIcons'] = trim($input['showIcons']);
        $options['showSize'] = trim($input['showSize']);
        $options['showChange'] = trim($input['showChange'] );
	    $options['showThumbnail'] = trim( $input['showThumbnail'] );
	    $options['dbNativeViewer'] = trim( $input['dbNativeViewer']);
        $options['imagesPopup'] = trim($input['imagesPopup']);
        $options['thickboxTypes'] = trim($input['thickboxTypes']);
        $options['defaultHeight'] = trim($input['defaultHeight'] );
	    $options['showInEditor'] = trim( $input['showInEditor']);
	    $options['datetimeFormat'] = trim( $input['datetimeFormat'] );

        $options['allowDownload'] = trim($input['allowDownload']);
        $options['allowDownloadFolder'] = trim($input['allowDownloadFolder']);
        $options['allowBrowseFolder'] = trim($input['allowBrowseFolder']);
        $options['link2Folder'] = trim($input['link2Folder']);

        $options['tipoConexion'] = trim($input['tipoConexion']);

        /* if (!preg_match('/^[a-z0-9]{32}$/i', $options['text_string'])) {
          $options['text_string'] = '';
          } */
        return $options;
    }




    /**
     * Load JS files and their dependencies
     *
     * @since 2.0.0
     * @return
     */
    public function enqueue_admin_scripts() {
        if (!isset($this->plugin_screen_hook_suffix)) {
            return;
        }

        $screen = get_current_screen();
        if ($this->plugin_screen_hook_suffix == $screen->id) {
            //wp_enqueue_media();
            wp_enqueue_script('DFS-Script', parent::$url . 'scripts-admin.js', array('jquery'));
            /*
            wp_localize_script($this->plugin_slug . '-admin-script', 'wpmm_vars', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'plugin_url' => admin_url('options-general.php?page=' . $this->plugin_slug)
            ));
            */
        }

        //wp_enqueue_script('DFS-Script', parent::$url . 'scripts-hyno.js', array('jquery'));
    }

    /**
     * Get plugin info
     *
     * @param string $plugin_slug
     * @return array
     */
    function wpmm_plugin_info($plugin_slug) {
        add_filter('extra_plugin_headers', create_function('', 'return array("GitHub URI","Twitter");'));
        $plugin_data = get_plugin_data(parent::$url_path."".parent::$nombre.".php");

        return $plugin_data;
    }
}

