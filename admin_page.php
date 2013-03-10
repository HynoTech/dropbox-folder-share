<?php
/*
 *             delete_option("db_fs_hyno_show");
  delete_option("db_fs_hyno_icons");
  delete_option("db_fs_hyno_size");
  delete_option("db_fs_hyno_changed");
 */
$cxn = get_option("db_fs_hyno_conexion");
$fopen_sel = $cxn == "fopen" ? 'checked=checked' : '';
$curl_sel = $cxn == "curl" ? 'checked=checked' : '';

$show_as = get_option("db_fs_hyno_show");
$show_as_list = $show_as == "lista" ? 'checked=checked' : '';
$show_as_icons = $show_as == "iconos" ? 'checked=checked' : '';


$mostrar_iconos = get_option("db_fs_hyno_icons");
$txt_mostrar_iconos = $mostrar_iconos == "1" ? 'value="on" checked' : '';

$mostrar_size = get_option("db_fs_hyno_size");
$txt_mostrar_size = $mostrar_size == "1" ? 'value="on" checked' : '';

$mostrar_actualizacion = get_option("db_fs_hyno_changed");
$txt_mostrar_actualizacion = $mostrar_actualizacion == "1" ? 'value="on" checked' : '';
?>
<div class="wrap">
    <form method="post" name="options" target="_self" id="options">
        
        <img src="<?php echo plugins_url('img/logo.png', __FILE__); ?>" />

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
                    <div class="stuffbox" id="version_defecto">
                        <h3>
                            <?php _e('Valores Predeterminados', 'dropbox-folder-share'); ?>
                        </h3>
                        <div class="inside">
                            <table cellpadding="0" class="links-table">
                                <tbody>
                                    <tr>
                                        <th scope="row"><label for="db_fs_hyno_vista"><?php _e('Modo de Visualizacion', 'dropbox-folder-share'); ?></label></th>
                                        <td>
                                            <input type="radio" name="db_fs_hyno_vista" id="h_show_as" value="lista" <?php echo $show_as_list; ?> />
                                            <?php _e('Lista', 'dropbox-folder-share'); ?>
                                            <script type="text/javascript">
                                                jQuery(document).ready( function() {
                                                    jQuery('input:radio[name=db_fs_hyno_vista]').click(function(){
                                                        if(jQuery('#h_show_as').is(':checked')){
                                                            jQuery('#txt_id_pag').show("slow");
                                                        }
                                                        else{
                                                            jQuery('#txt_id_pag').hide("slow");
                                                        }
                                                    }); 
                                                });
                                            </script>
                                            <?php
                                            $contenido_mostrar = '';
                                            if ($show_as != 'lista') {
                                                $contenido_mostrar = "style='display: none;' ";
                                            }
                                            ?>
                                            <div id="txt_id_pag" <?php echo $contenido_mostrar; ?>>
                                                <input type="checkbox" name="chk_show_icons" <?php echo $txt_mostrar_iconos; ?>/><?php _e('Mostrar Iconos', 'dropbox-folder-share'); ?><br/>
                                                <input type="checkbox" name="chk_show_size" <?php echo $txt_mostrar_size; ?>/><?php _e('Mostrar Tamaño', 'dropbox-folder-share'); ?><br/>
                                                <input type="checkbox" name="chk_show_changed" <?php echo $txt_mostrar_actualizacion; ?>/><?php _e('Mostrar Fecha de Moficacion', 'dropbox-folder-share'); ?><br/>
                                            </div>
                                            <input type="radio" name="db_fs_hyno_vista" value="iconos" <?php echo $show_as_icons; ?> />
                                            <?php _e('Iconos', 'bible-post'); ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="stuffbox" id="modo_insercion">
                        <h3>
                            <?php _e('Configuracion de Conexion', 'dropbox-folder-share'); ?>
                        </h3>
                        <div class="inside">
                            <em><p> <?php _e('Seleccione el modo de conexion que mas le convenga.', 'dropbox-folder-share'); ?></p></em>
                            <input type="radio" name="db_fs_hyno_conexion" value="fopen" <?php echo $fopen_sel; ?> />
                            fopen<br />
                            <input type="radio" name="db_fs_hyno_conexion" disabled="disabled" value="curl" <?php echo $curl_sel; ?> />
                            curl<br />
                        </div>
                    </div>
                </div>
                <!-- /post-body-content -->
                <div class="postbox-container" id="postbox-container-1">
                    <div class="meta-box-sortables ui-sortable" id="side-sortables">
                        <div class="postbox " id="linksubmitdiv">
                            <h3 class="hndle"><span>
                                    <?php _e('Guardar Configuracion', 'dropbox-folder-share'); ?>
                                </span></h3>
                            <div class="inside">
                                <div id="submitlink" class="submitbox">
                                    <div id="minor-publishing">
                                        <div id="misc-publishing-actions">
                                            <p class="popular-tags"><?php _e('Hola, espero que disfruten de mi plugin. Me llevó un montón de horas para hacerlo. Una gran cantidad de galletas y jugo de uva que se derramó sobre el teclado en la creación de este plugin. Si te gusta, me podrías ayudar invitandome un café recién hecho.', 'dropbox-folder-share'); ?> </p>
                                            <div class="misc-pub-section center">
                                                <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                                                    <input type="hidden" name="cmd" value="_s-xclick">
                                                    <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
                                                           ">
                                                    <input type="image" src="<?php echo plugins_url('img/paypal_200x96.png', __FILE__); ?>" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                                                    <img alt="" border="0" src="https://www.paypalobjects.com/es_XC/i/scr/pixel.gif" width="1" height="1">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="major-publishing-actions">
                                        <div id="delete-action"> <a href="http://www.hyno.ok.pe" target="_blank">HynoTech Web</a> </div>
                                        <div id="publishing-action">
                                            <input type="submit" value="<?php _e('Guardar', 'dropbox-folder-share'); ?>" tabindex="4" id="publish" class="button-primary" name="bp_hyno_update" />
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="postbox-container" id="postbox-container-2">
                    <div class="meta-box-sortables ui-sortable" id="advanced-sortables">
                        <hr />
                        <div class="stuffbox" id="modo_insercion">
                            <h3>
                                <?php _e('Parametros de Shortcode', 'dropbox-folder-share'); ?>
                            </h3>
                            <div class="inside">
                                <p class="popular-tags"><em>[dropbox-foldershare-hyno link="LNK_FOLDER" ver_como='iconos']</em></p>
                                <table cellpadding="0" class="links-table">
                                    <tbody>
                                        <tr>
                                            <th scope="row">link</th>
                                            <td><?php _e('URL de la carpeta compartida de DropBox.', 'dropbox-folder-share'); ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">ver_como</th>
                                            <td><?php _e('Como se mostrara la carpeta (<b>iconos</b> o <b>lista</b>).', 'dropbox-folder-share'); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
