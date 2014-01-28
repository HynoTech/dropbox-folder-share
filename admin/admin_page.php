<style type="text/css">
    #version_defecto .form-table{
        margin-left: 2em;
    }
</style>
<div class="wrap">
    <img src="<?php echo parent::$url . 'img/logo.png'; ?>" />
    
        <div id="poststuff">
            <div class="metabox-holder columns-2" id="post-body">
                <?php
                if (isset($_GET['tab'])) {
                    $active_tab = $_GET['tab'];
                } else {
                    $active_tab = 'configuraciones';
                } // end if
                ?>
                <div id="post-body-content">
                    <h2 class="nav-tab-wrapper">
                    <a href="?page=dropbox-folder-share&tab=configuraciones" class="nav-tab <?php echo $active_tab == 'configuraciones' ? 'nav-tab-active' : ''; ?>"><?php _e("Configuraciones",parent::$nombre); ?></a>
                    <a href="?page=dropbox-folder-share&tab=parametros" class="nav-tab <?php echo $active_tab == 'parametros' ? 'nav-tab-active' : ''; ?>"><?php _e("Parametros de Shorcode",parent::$nombre); ?></a>
                </h2>
                    <form action="options.php" method="post">
                    <div class="stuffbox" id="version_defecto">
                        <?php
                        if($active_tab == 'parametros'){
?>

                            <h3>
                                <?php _e('Parametros de Shortcode', parent::$nombre); ?>
                            </h3>
                            <div class="inside">
                                <p class="popular-tags"><em>[DFS link="LNK_FOLDER" ver_como='iconos']</em></p>
                                <table cellpadding="0" class="links-table">
                                    <tbody>
                                        <tr>
                                            <th scope="row">link</th>
                                            <td><?php _e('URL de la carpeta compartida de DropBox.', parent::$nombre); ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">ver_como</th>
                                            <td><?php _e('Como se mostrara la carpeta (<b>iconos</b> o <b>lista</b>).', parent::$nombre); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        <?php
                        }else{
                        settings_fields(parent::_OPT_SEETINGS_ . '-group');
                        do_settings_sections(parent::$nombre);    
                        }
                        ?>
                    </div>
                    <?php
                    if($active_tab != 'parametros'){
                    ?>
                <div class="postbox-container" id="postbox-container-2">
                    <div class="meta-box-sortables ui-sortable" id="advanced-sortables">
                        <div class="stuffbox" id="modo_insercion">
                            <div class="inside">
                                <center><?php submit_button(); ?></center>
                            </div>
                        </div>
                    </div>
                </div>
                    </form>
                    <?php
                    }
                    ?>

 
                    
                </div>
                <!-- /post-body-content -->
                <div class="postbox-container" id="postbox-container-1">
                    <div class="meta-box-sortables ui-sortable" id="side-sortables">
                        <div class="postbox " id="linksubmitdiv">
                            <h3 class="hndle"><span>
                                    <?php _e('Guardar Configuracion', parent::$nombre); ?>
                                </span></h3>
                            <div class="inside">
                                <div id="submitlink" class="submitbox">
                                    <div id="minor-publishing">
                                        <div id="misc-publishing-actions">
                                            <p class="popular-tags"><?php _e('Hola, espero que disfruten de mi plugin. Me llevó un montón de horas para hacerlo. Una gran cantidad de galletas y jugo de uva que se derramó sobre el teclado en la creación de este plugin. Si te gusta, me podrías ayudar invitandome un café recién hecho.', parent::$nombre); ?> </p>
                                            <div class="misc-pub-section center">
                                                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                                                    <input type="hidden" name="cmd" value="_s-xclick">
                                                    <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIHXwYJKoZIhvcNAQcEoIIHUDCCB0wCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYCgtYysV/TUopovmN/DKX/z2cDkIvM0GRbKzVEgzOunsIPmLBvfqOcKrH5irnI0lk+jzO5/8UYufUJtWeIDCQuBOBFJBv0zN4iap+mN+opJI3DJatQ8ZVFs+AtVB/lA2Ad3t46cObYzOn4dPVkvA7ACUEF1njbHCRJJb+PVpHRzAjELMAkGBSsOAwIaBQAwgdwGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQIWbPgKp4QbgWAgbiceQH9C9ZsRzX8hBRzLR061E9G7aUkIx8/pf0pUXXjNnonusXSCn3xbkj/gyQwxIWI9lHLgdZwwjsMp8FHKR1Vct6yXRz4WJXETQcKUVrnzkb/wpR4f/WXg/s4BWS20Vx7j8TQmamJF6IqNJxO1P+1Anhr6q4CAq/Ea7RqsVtKmiOfDu8WTDyN30zPhd9w3U63X7cRFakMNC4B8Pa2FeyJWdldHvIf4ne0iOHDDuFXpc4fhOhG7kTYoIIDhzCCA4MwggLsoAMCAQICAQAwDQYJKoZIhvcNAQEFBQAwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tMB4XDTA0MDIxMzEwMTMxNVoXDTM1MDIxMzEwMTMxNVowgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDBR07d/ETMS1ycjtkpkvjXZe9k+6CieLuLsPumsJ7QC1odNz3sJiCbs2wC0nLE0uLGaEtXynIgRqIddYCHx88pb5HTXv4SZeuv0Rqq4+axW9PLAAATU8w04qqjaSXgbGLP3NmohqM6bV9kZZwZLR/klDaQGo1u9uDb9lr4Yn+rBQIDAQABo4HuMIHrMB0GA1UdDgQWBBSWn3y7xm8XvVk/UtcKG+wQ1mSUazCBuwYDVR0jBIGzMIGwgBSWn3y7xm8XvVk/UtcKG+wQ1mSUa6GBlKSBkTCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb22CAQAwDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQUFAAOBgQCBXzpWmoBa5e9fo6ujionW1hUhPkOBakTr3YCDjbYfvJEiv/2P+IobhOGJr85+XHhN0v4gUkEDI8r2/rNk1m0GA8HKddvTjyGw/XqXa+LSTlDYkqI8OwR8GEYj4efEtcRpRYBxV8KxAW93YDWzFGvruKnnLbDAF6VR5w/cCMn5hzGCAZowggGWAgEBMIGUMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbQIBADAJBgUrDgMCGgUAoF0wGAYJKoZIhvcNAQkDMQsGCSqGSIb3DQEHATAcBgkqhkiG9w0BCQUxDxcNMTMxMjEzMTY1ODU5WjAjBgkqhkiG9w0BCQQxFgQU4oeEqGITyvVuWMIQW10fEJKluZcwDQYJKoZIhvcNAQEBBQAEgYAXU5Chxs0iN0h+WXkcbkWGIh1agsyBOLG8zQ4mtxaYuq+j574/R9Tybqg/Zza98HUOzKGWpOfDe8t6f0wbU7TFoL2UzvKNHC7WLGpHO8I37YS3XtSXK17FzUuDWAah0hH4/JqcsUa27f/bcfbDQ2ZAqn8pbhKyDXPD4UCyB5YVng==-----END PKCS7-----
                                                           ">
                                                    <input type="image" src="<?php echo parent::$url . 'img/paypal_200x96.png'; ?>" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                                                    <img alt="" border="0" src="https://www.paypalobjects.com/es_XC/i/scr/pixel.gif" width="1" height="1">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="major-publishing-actions">
                                        <div id="delete-action"> <a href="http://www.hynotech.com/" target="_blank">HynoTech Web</a> </div>
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
                            <div class="inside">
                                <em><?php _e('Gracias por Utilizar este plugin. Me gustaria leer sugerencias u opiniones para que juntos mejoremos esta herramienta, cualquier sugerencia para mejorar el plugin o reportar algun error nos ayuda muchisimo, no duden en hacernoslo saber. ',parent::$nombre); ?></em>
                                <br />
                                <em><?php _e('Pueden hacernos lleguar sus sugerencias, opiniones y/o criticas a travez del formulario de contactos de', parent::$nombre) ?> <a href="http://www.hynotech.com"> HynoTech.com</a></em>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
