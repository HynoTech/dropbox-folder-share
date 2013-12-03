<?php

class DFS_Admin extends DropboxFolderSharePrincipal {

    public function __construct() {
        //parent::__construct();
    }

    function pagAdmin() {
        add_options_page(
                '[HT]DropBox Folder Share', //Titulo Pagina 
                '[HT]DropBox Folder Share', // Titulo Menu 
                parent::_PERMISOS_REQUERIDOS_, //Permisos Necesarios
                parent::$nombre, //id Unico del Menu
                array(&$this, 'pagina_de_opciones') //Que mostrar?
        );
    }
    
   function pagina_de_opciones() {
        require_once (parent::$url_path . 'admin/admin_page.php');
    }

    function plugin_admin_init() {
        register_setting(
                parent::_OPT_SEETINGS_ . '-group', //Nombre del Grupo de Opciones
                parent::_OPT_SEETINGS_ , // Nombre de la Opcion a Guardar
                array(&$this, 'validate_options') // Funcion de Validacion de Opciones
        );
        
        $secciones = array(
            "visualizacion" => array(
                'titulo' => __("Visualizacion",  parent::$nombre),
                'campos' => array(
                    'SeeAs' => __('Modo de Visualizacion',  parent::$nombre),
                    'ShowIcons' => __('Mostrar Iconos',  parent::$nombre),
                    'ShowSize' => __('Mostrar Tamaño de Archivo',  parent::$nombre),
                    'ShowChange' => __('Mostrar Fecha de Modificacion',  parent::$nombre)
                )
            ),
            "vinculacion" => array(
                'titulo' =>  __("Vinculacion",  parent::$nombre),
                'campos' => array(
                    'allowDownload' => __('Permitir Descarga de Archivos',  parent::$nombre),
                    'link2Folder' => __('Dejar Link de Carpeta Compartida',  parent::$nombre)
                )
            ),
            "conexion" => array(
                'titulo' =>  __("Conexion",  parent::$nombre),
                'campos' => array(
                    'tipoConexion' => __('Tipo de Conexion a Usar',  parent::$nombre)
                )
            )
        );

        foreach ($secciones as $indice => $contenido) {
            add_settings_section(
                    'DFS_SECCION_'.$indice,                 // para usarse en el atributo ID de las etiquetas
                    $contenido['titulo'],                   // Titulo de la Seccion
                    array(&$this, 'printSeccion_' .$indice), // Funcion que llena la seccion con contenido (HTML)
                    parent::$nombre                         // page menu_slug para ser usado en do_settings_sections()
            );
            foreach ($contenido['campos'] as $idxCampo => $tituloCampo) {
                add_settings_field(
                        'id_'.$indice.'_'.$idxCampo,                    // texto a ser usado en el ID de las etiquetas
                        $tituloCampo,                                   //Titulo del Campo
                        array(&$this, 'print_'.$indice.'_'. $idxCampo .'Input'),    // funcion que da la salida del campo como parte del form
                        parent::$nombre,                                //pagina de menu que mostrara este campo (do_settings_sections())
                        'DFS_SECCION_'.$indice,                         // id de la seccion a la que pertenece (primer argumento de add_settings_section()
                        array(
                            'label_for' => 'id_'.$indice.'_'.$idxCampo  //array de datos adicionales a enviar al callback
                        )
                );
            }
        }
    }
    
    //HTML DE  SECCION VISUALIZACION 
    function printSeccion_visualizacion(){
        echo '';
    }
    //Campo SeeAs
    function print_visualizacion_SeeAsInput(){
        $options = get_option(parent::_OPT_SEETINGS_);
        ?>

                        <select id="id_visualizacion_SeeAs" name="<?php echo parent::_OPT_SEETINGS_; ?>[SeeAs]">
                            <option value="lista" <?php echo selected($options['SeeAs'], "lista", false); ?>><?php _e('Lista',parent::$nombre) ?></option>
                            <option value="iconos" <?php echo selected($options['SeeAs'], "iconos", false); ?>><?php _e('Iconos',parent::$nombre) ?></option>
                        </select>
        <?php
    }
    //campo ShowIcon
    function print_visualizacion_ShowIconsInput() {
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
    function print_visualizacion_ShowSizeInput() {
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
    function print_visualizacion_ShowChangeInput() {
        $options = get_option(parent::_OPT_SEETINGS_);
        ?>
        <input 
            id="id_visualizacion_ShowChange" 
            type="checkbox" 
            name="<?php echo parent::_OPT_SEETINGS_; ?>[showChange]" 
            value="1"
            <?php echo checked(1, $options['showChange'], false); ?> 
            />
        <br /><br />
        <?php
    }
    //FIN SECCION VISUALIZACION
    
    //HTML DE SECCION VINCULACION
    function printSeccion_vinculacion(){
        echo '';
    }
    //Seccion allowDownload
    function print_vinculacion_allowDownloadInput() {
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
    //Seccion link2Folder
    function print_vinculacion_link2FolderInput() {
        $options = get_option(parent::_OPT_SEETINGS_);
        ?>
        <input 
            id="id_vinculacion_link2Folder" 
            type="checkbox" 
            name="<?php echo parent::_OPT_SEETINGS_; ?>[link2Folder]" 
            value="1"
            <?php echo checked(1, $options['link2Folder'], false); ?> 
            />
        <br /><br />
        <?php
    }
    //FIN SECCION VINCULACION
    
    //HTML SECCION CONEXION
    function printSeccion_conexion(){
        echo '';
    }
    //Seccion tipoConexion
    function print_conexion_tipoConexionInput(){
        $options = get_option(parent::_OPT_SEETINGS_);
        ?>
                        <select id="id_conexion_tipoConexion" name="<?php echo parent::_OPT_SEETINGS_; ?>[tipoConexion]">
                            <option value="fopen" <?php echo selected($options['tipoConexion'], "fopen", false); ?>>fopen</option>
                            <option value="curl" <?php echo selected($options['tipoConexion'], "curl", false); ?>>cURL</option>
                        </select>
        <br /><br />
        <?php
    }
    
    //FUNCION DE VALIDACION DE DATOS
    function validate_options($input) {
        $options = get_option(parent::_OPT_SEETINGS_);
        $options['SeeAs'] = trim($input['SeeAs']);
        $options['showIcons'] = trim($input['showIcons']);
        $options['showSize'] = trim($input['showSize']);
        $options['showChange'] = trim($input['showChange']);
        
        $options['allowDownload'] = trim($input['allowDownload']);
        $options['link2Folder'] = trim($input['link2Folder']);

        $options['tipoConexion'] = trim($input['tipoConexion']);


        /* if (!preg_match('/^[a-z0-9]{32}$/i', $options['text_string'])) {
          $options['text_string'] = '';
          } */
        return $options;
    }
}

 