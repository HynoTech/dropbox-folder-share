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
	                <i class="fas fa-paperclip"></i> <?php echo ! empty( $plugin_data['PluginURI'] ) ? '<a href="' . $plugin_data['PluginURI'] . '" target="_blank">' . $plugin_data['AuthorName'] . '</a>' : ''; ?></li>
                <li>
	                <i class="fab fa-twitter" style="color: #1da1f2;"></i> <?php echo ! empty( $plugin_data['Twitter'] ) ? '<a href="http://twitter.com/' . $plugin_data['Twitter'] . '" target="_blank">@' . $plugin_data['Twitter'] . '</a>' : ''; ?></li>
	            <li>
		            <i class="fab fa-whatsapp" style="color: #00e676;"></i> <?php echo ! empty( $plugin_data['Twitter'] ) ? '<a href="https://wa.me/' . $plugin_data['WhatsAppBusiness'] . '" target="_blank">+' . $plugin_data['WhatsAppBusiness'] . '</a>' : ''; ?></li>
                <li>
	                <i class="fab fa-github"></i> <?php echo ! empty( $plugin_data['GitHub URI'] ) ? '<a href="' . $plugin_data['GitHub URI'] . '" target="_blank">' . basename( $plugin_data['GitHub URI'] ) . '</a>' : ''; ?></li>
                <li>
	                <i class="fab fa-facebook" style="color: #188bee;"></i> <?php echo ! empty( $plugin_data['Facebook Page'] ) ? '<a href="' . $plugin_data['Facebook Page'] . '" target="_blank">' . basename( $plugin_data['Facebook Page'] ) . '</a>' : ''; ?></li>
            </ul>
        </div>
    </div>

    <div class="sidebar_box info_box">
        <div class="inside">
            <p class="popular-tags"><?php _e('Hola, espero que disfruten de este plugin. Me llevó un montón de horas para hacerlo, una gran cantidad de galletas y jugo de uva que se derramó sobre el teclado en la creación de este plugin. Si te gusta, podrías ayudarme invitandome un café recién hecho.', "dropbox-folder-share"); ?> </p>
            <div class="misc-pub-section center">
                <?php echo DROPBOX_FOLDER_SHARE_HTML_DONACIONES; ?>
            </div>
        </div>
    </div>


</div>
