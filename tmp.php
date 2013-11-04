<?php
define('WP_USE_THEMES', false);
require('../../../wp-blog-header.php');
include_once 'class/DropboxFolderShare.php';
$objDFS = new DropboxFolderShare2;
$objDFS->verCarpeta();
$objDFS->
?>
