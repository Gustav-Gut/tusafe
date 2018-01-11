<?php 

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
include plugin_dir_path(__FILE__) . 'utilities.php';
require_once plugin_dir_path(__FILE__) . 'classes/Ihc_Db.class.php';
Ihc_Db::do_uninstall();

