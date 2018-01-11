<?php
if (empty($no_load)){
	require_once '../../../wp-load.php';	
}
require_once 'classes/ResetPassword.class.php';

if (!empty($_GET['do_reset_pass']) && !empty($_GET['uid']) && !empty($_GET['c'])){
	/// DO RESET PASSWORD
	$object = new IHC\ResetPassword();
	$object->proceed($_GET['uid'], $_GET['c']);
	
	$redirect = get_option('ihc_general_password_redirect'); /// PASSWORD REDIRECT	
}

/// AND OUT
if (empty($redirect)){
	$redirect = get_option('ihc_general_redirect_default_page'); /// STANDARD REDIRECT
}
if (!empty($redirect) && $redirect!=-1){
	$redirect_url = get_permalink($redirect);
} else {
	$redirect_url = get_home_url();	/// HOME
}

wp_safe_redirect($redirect_url);
exit();