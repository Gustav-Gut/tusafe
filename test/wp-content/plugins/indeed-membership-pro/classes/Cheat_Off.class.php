<?php
if (!class_exists('Cheat_Off')):
	
class Cheat_Off{
	
	public function __construct(){
		/*
		 * @param none
		 * @return none
		 */
		 add_action('init', array($this, 'init_check'));
	}
	
	public function init_check(){
		/*
		 * @param none
		 * @return none
		 */
		 global $current_user;
		 if (empty($current_user) || empty($current_user->ID)){
		 	return; /// Not available for visitors
		 }
		 if (current_user_can("manage_options")){
		 	return;
		 }
		 if (ihc_get_user_type()=='admin'){
		 	return; /// not available for admin
		 }
		 $cheat_off_enabled = get_option('ihc_cheat_off_enable');
		 if (empty($cheat_off_enabled)){
		 	return; /// Module is disabled
		 }
		 
		 /// user has cookie??
		 if (!empty($_COOKIE['ihc_cheat_off'])){
		 	 $hash_from_cookie = $_COOKIE['ihc_cheat_off'];
			 $hash_from_db = Ihc_Db::cheat_off_get_hash($current_user->ID);
			 if ($hash_from_cookie && $hash_from_db && $hash_from_cookie!=$hash_from_db){
			 	/// logout and redirect home
				///unset cookie
				setcookie('ihc_cheat_off', '', time()-3600, '/');
				///and then logout
				$this->logout();
			 }
		 } else {
			$hash = $current_user->ID . '_' . ihc_random_str(8);
			$inserted = Ihc_Db::cheat_off_set_hash($current_user->ID, $hash);
			if ($inserted){
				$num_of_days = 365;
				$num_of_days_option = get_option('ihc_cheat_off_cookie_time');
				if (!empty($num_of_days_option)){
					$num_of_days = $num_of_days_option;
				}
				$cookie_time = time() + $num_of_days * 24 * 60 * 60;//one year
			 	setcookie('ihc_cheat_off', $hash, $cookie_time, '/');				
			}
		 }
		 
	}

	private function logout(){
		/*
		 * @param none
		 * @return none
		 */
		$url = get_option('ihc_cheat_off_redirect');
		if ($url && $url!=-1){
			$link = get_permalink($url);
			if (!$link){
				$link = ihc_get_redirect_link_by_label($url);
			}
		}
		if (empty($link)){
			///$link = IHC_PROTOCOL . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			$link = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}			
		
		wp_clear_auth_cookie();
		do_action('wp_logout');
		nocache_headers();
		wp_redirect($link);
		exit();		 
	}
	
}
	
endif;
