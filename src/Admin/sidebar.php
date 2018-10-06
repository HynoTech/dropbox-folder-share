<?php
/**
 * Created by PhpStorm.
 * User: Antony
 * Date: 6/02/2016
 * Time: 23:39
 */
?>
<div id="sidebar" class="wrapper-cell">

    <div class="sidebar_box info_box">
        <h3><?php _e('Informacion del Plugin', "dropbox-folder-share"); ?></h3>
        <div class="inside">
            <img src="<?php echo DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/img/HT.svg'; ?>" />
            <?php $plugin_data = self::wpmm_plugin_info(DROPBOX_FOLDER_SHARE_PLUGIN_NOMBRE); ?>
            <ul>
                <li><?php _e('Nombre', "dropbox-folder-share"); ?>:
                    <?php
                    echo !empty($plugin_data['Name']) ? $plugin_data['Name'] : '';
                    echo !empty($plugin_data['Version']) ? ' v' . $plugin_data['Version'] : '';
                    ?>
                </li>
                <li><?php _e('Autor', "dropbox-folder-share"); ?>
                    : <?php echo !empty($plugin_data['AuthorName']) ? $plugin_data['AuthorName'] : ''; ?></li>
                <li>
                    Website: <?php echo ! empty( $plugin_data['PluginURI'] ) ? '<a href="' . $plugin_data['PluginURI'] . '" target="_blank">' . $plugin_data['AuthorName'] . '</a>' : ''; ?></li>
                <li>
                    Twitter: <?php echo ! empty( $plugin_data['Twitter'] ) ? '<a href="http://twitter.com/' . $plugin_data['Twitter'] . '" target="_blank">@' . $plugin_data['Twitter'] . '</a>' : ''; ?></li>
                <li>
                    GitHub: <?php echo ! empty( $plugin_data['GitHub URI'] ) ? '<a href="' . $plugin_data['GitHub URI'] . '" target="_blank">' . basename( $plugin_data['GitHub URI'] ) . '</a>' : ''; ?></li>
            </ul>
        </div>
    </div>

    <div class="sidebar_box info_box">
        <div class="inside">
            <p class="popular-tags"><?php _e('Hola, espero que disfruten de mi plugin. Me llevó un montón de horas para hacerlo. Una gran cantidad de galletas y jugo de uva que se derramó sobre el teclado en la creación de este plugin. Si te gusta, me podrías ayudar invitandome un café recién hecho.', "dropbox-folder-share"); ?> </p>
            <div class="misc-pub-section center">
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
                                                           ">
                    <input type="image" src="<?php echo DROPBOX_FOLDER_SHARE_PLUGIN_URL . 'src/img/paypal_200x96.png'; ?>" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/es_XC/i/scr/pixel.gif" width="1" height="1">
                </form>
            </div>
        </div>
    </div>


</div>