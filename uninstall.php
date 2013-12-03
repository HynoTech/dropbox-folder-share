<?php
/**
 * Code used when the plugin is removed (not just deactivated but actively deleted by the WordPress Admin).
 *
 * @license http://creativecommons.org/licenses/GPL/2.0/ GNU General Public License, version 2
 */

if (!current_user_can('activate_plugins') || (!defined('ABSPATH') || !defined('WP_UNINSTALL_PLUGIN')))
    exit();


delete_option('dropbox-folder-share-options');