<?php 
/////////LOGIN
function ihc_login($url){	
	if (isset($_REQUEST['log']) && $_REQUEST['log']!='' && isset($_REQUEST['pwd']) && $_REQUEST['pwd']!=''){
		
		/// CHECK RECAPTCHA
		if (get_option('ihc_login_show_recaptcha')){
			ihc_do_check_login_recaptcha($url);
		}
		
		$arr['user_login'] = sanitize_user($_REQUEST['log']);
		$arr['user_password'] = $_REQUEST['pwd'];
		$arr['remember'] = ( isset( $_REQUEST['rememberme'] ) == 'forever' ) ? true : false;
		///$user = wp_signon( $arr, false );
		$user = wp_signon($arr, TRUE);

		if (!is_wp_error($user)){
			
			//============== Check E-mail verification status
			ihc_check_email_verification_status($user->ID, $url);
			
			if (isset($user->roles[0]) && $user->roles[0]=='pending_user'){
				//=================== PENDING USER				
				wp_clear_auth_cookie();//logout
				do_action( 'wp_logout' );
				nocache_headers();
				$url = add_query_arg( array('ihc_login_pending' => 'true'), $url );
				wp_redirect( $url );
				exit();
			} else {				
				//================== LOGIN SUCCESS
				do_action('ihc_do_action_on_login', @$user->ID);
				$url = add_query_arg( array( 'ihc_success_login' => 'true' ), $url );
				
				//LOCKER REDIRECT
				if (!empty($_REQUEST['locker'])){
					wp_redirect($url);
					exit();
				}
				//LOCKER REDIRECT
				
				//$redirect_p_id = get_option('ihc_general_login_redirect');
				$redirect_p_id = ihc_get_login_redirect_post_id(@$user->ID);
				
				if ($redirect_p_id && $redirect_p_id!=-1){
					$redirect_url = get_permalink($redirect_p_id);
					if (!$redirect_url){
						$redirect_url = ihc_get_redirect_link_by_label($redirect_p_id, @$user->ID);
					}
					if ($redirect_url){
						wp_redirect($redirect_url);
						exit();						
					}
				}			
				wp_redirect( $url );
				exit();
			}
		}
	}
	
	//===================== LOGIN FAILD
	$url = add_query_arg( array('ihc_login_fail'=>'true'), $url );
	wp_redirect( $url );
	exit();
}

function ihc_login_social($login_data){
	/*
	 * @param array
	 * @return none
	 */
	$uid = -1;	
	
	$meta_key = "ihc_" . $login_data['sm_type'];
	global $wpdb;
	$data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "usermeta
								WHERE meta_key='" . $meta_key . "'
								AND meta_value='" . $login_data['sm_uid'] . "';");
	if (isset($data) && isset($data->user_id)){
		$uid = $data->user_id;
	}
	if ($uid>-1){		
		$user_data = get_userdata($uid);		
		if (isset($user_data->roles['0']) && $user_data->roles['0']=='pending_user'){
			//=================== PENDING USER	
			if (isset($login_data['url'])){
				$url = add_query_arg( array('ihc_login_pending' => 'true'), $login_data['url'] );
				wp_redirect( $url );
				exit();
			}	
			return;
		}
		
		
		//======================== EMAIL VERIFICATION STATUS
		ihc_check_email_verification_status($uid, @$login_data['url']);
		
		
		//======================== LOGIN SUCCESS
		wp_set_auth_cookie($uid);//we set the user
		

		/********** REDIRECT ************/
		
		//LOCKER REDIRECT
		if (!empty($login_data['is_locker']) && !empty($login_data['url'])){
			wp_redirect( $login_data['url'] );
			exit();
		}
		//LOCKER REDIRECT
		
		//$redirect = get_option('ihc_general_login_redirect');
		$redirect = ihc_get_login_redirect_post_id($uid);

		if ($redirect && $redirect!=-1){
			$redirect_url = get_permalink($redirect);
			if (!$redirect_url){
				//custom redirect url
				$redirect_url = ihc_get_redirect_link_by_label($redirect, $uid);
			}
			if ($redirect_url){
				wp_redirect($redirect_url);
				exit();				
			}
		} else if (isset($login_data['url'])){
			do_action('ihc_do_action_on_login', @$uid);
			$url = $login_data['url'];
			$url = add_query_arg( array( 'ihc_success_login' => 'true' ), $url );
		} else {
			$url = home_url();
		}
		
		wp_redirect( $url );
		exit();
	}
	
	//======================== LOGIN FAILD
	if (isset($login_data['url'])){
		$url = add_query_arg( array('ihc_login_fail' => 'true'), $login_data['url'] );
		wp_redirect( $url );
		exit();
	}	
}

function ihc_check_email_verification_status($uid, $redirect_url=''){
	/*
	 * logout and redirect if verification status is -1
	 * @param uid - int, redirect_url - string
	 * @return 
	 */
	$email_verification = get_user_meta($uid, 'ihc_verification_status', TRUE);
	if ($email_verification==-1){
		wp_clear_auth_cookie();//logout
		do_action('wp_logout');
		nocache_headers();
		if (!$redirect_url){
			$redirect_url = home_url();
		}
		$redirect_url = add_query_arg( array( 'ihc_pending_email' => 'true' ), $redirect_url );
		wp_redirect( $redirect_url );
		exit();		
	}	
}

function ihc_do_check_login_recaptcha($url=''){
	/*
	 * REDIRECT IF CAPTCHA IS NOT COMPLETED 
	 * @param string
	 * @return none
	 */
	$secret = get_option('ihc_recaptcha_private');
	if ($secret){
		if (isset($_POST['g-recaptcha-response'])){					
			if (!class_exists('ReCaptcha')){
				require_once IHC_PATH . 'classes/ReCaptcha/ReCaptcha.php';								
			}
			$recaptcha = new ReCaptcha($secret);
			$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
			if (!$resp->isSuccess()){
				$captcha_error = TRUE;
			}
		} else {
			$captcha_error = TRUE;
		}		
	}
	if (!empty($captcha_error)){
		$url = add_query_arg( array('ihc_fail_captcha'=>'true'), $url );
		wp_redirect( $url );
		exit();
	}
}


function ihc_get_login_redirect_post_id($uid=0){
	/*
	 * @param none
	 * @return int
	 */
	 $id = get_option('ihc_general_login_redirect');
	 if (ihc_is_magic_feat_active('login_level_redirect')){
		 $user_levels = Ihc_Db::get_user_levels($uid);
	 	 $custom = get_option('ihc_login_level_redirect_rules');
		 if ($custom && $user_levels){
		 	$priority = get_option('ihc_login_level_redirect_priority');
		 	foreach ($priority as $lid){
		 		if (isset($user_levels[$lid]) && isset($custom[$lid])){
		 			$id = $custom[$lid];
					break;
		 		}
		 	}
		 }		 
	 }
	 return $id;
}
