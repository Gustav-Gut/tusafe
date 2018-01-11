<?php 
function ihc_return_all_cpt( $excluded=array() ){
	//return all custom post type except the built in and the $excluded
	$args = array('public' => true, '_builtin' => false);
	$data = get_post_types($args);
	if(count($excluded)>0){
		foreach($excluded as $e){
			if(in_array($e, $data)) $data = array_diff($data, array($e) );
		}
	}
	return $data;
}

function ihc_meta_box_settings_html(){
	require_once IHC_PATH . 'admin/includes/meta_boxes/page_post_settings.php';	
}

function ihc_meta_box_replace_content_html(){
	require_once IHC_PATH . 'admin/includes/meta_boxes/replace_content.php';
}

function ihc_meta_box_default_pages_html(){
	require_once IHC_PATH . 'admin/includes/meta_boxes/default_pages.php';
}

function ihc_drip_content_return_meta_box(){
	/*
	 * @param none
	 * @return none
	 */
	require_once IHC_PATH . 'admin/includes/meta_boxes/drip_content.php';
}

function ihc_update_metas(){
	if(!isset($_REQUEST['ihc_submit'])) return;
	$metas = ihc_get_metas();
	foreach($metas as $k=>$v){
		if(isset($_REQUEST[$k])){
			$data = get_option($k);
			if($k!==FALSE){
				//update
				update_option($k, $_REQUEST[$k]);
			}else{
				//create
				add_option($k, $_REQUEST[$k]);
			}			
		}
	}
}

function ihc_delete_template(){
	if(isset($_REQUEST['i_delete_id']) && $_REQUEST['i_delete_id']!=''){
		$option_name = 'ihc_lockers';
		$data = get_option($option_name);
		if($data===FALSE || !isset($data[$_REQUEST['i_delete_id']])) return;
		unset($data[$_REQUEST['i_delete_id']]);
		update_option($option_name, $data);
		return;
	}
}


function ihc_save_update_metas($group){
	if (isset($_REQUEST['ihc_save'])){
		$data = ihc_return_meta_arr($group, true);
		foreach ($data as $k=>$v){
			if (isset($_REQUEST[$k])){
				$data_db = get_option($k);
				if($data_db!==FALSE) update_option($k, $_REQUEST[$k]);
				else add_option($k, $_REQUEST[$k]);
			}
		}		
	}
}

function ihc_save_update_metas_general_defaults($post_data=array()){
	/*
	 * @param array ($_POST)
	 * @return none
	 */	
	$data = ihc_return_meta_arr('general-defaults', true);
	
	//EXTRA CHECK - REWRITE RULE FOR Visitor Inside User Page
	if (isset($post_data['ihc_general_register_view_user'])){
		ihc_save_rewrite_rule_for_register_view_page($post_data['ihc_general_register_view_user']);
	}
	
	foreach ($data as $k=>$v){
		if (isset($post_data[$k])){
			update_option($k, $post_data[$k]);
		}
	}
}

function ihc_check_default_pages_set($meta_box=false){
	$arr = array(
					'ihc_general_redirect_default_page' => __('Default Redirect', 'ihc'),
					'ihc_general_login_default_page' => __('Login', 'ihc'),
					'ihc_general_register_default_page' => __('Register', 'ihc'),
					'ihc_general_lost_pass_page' => __('Lost Password', 'ihc'),
					'ihc_general_logout_page' => __('LogOut', 'ihc'),
					'ihc_general_user_page' => __('Account User', 'ihc'),
					'ihc_general_tos_page' => __('TOS', 'ihc'),
					'ihc_subscription_plan_page' => __('Subscription Plan', 'ihc'),
					'ihc_general_register_view_user' => __('Visitor Inside User', 'ihc')
				);
	$str = '';
	
		if($meta_box){
			foreach($arr as $name=>$label){
				$value = get_option($name);
				
				//if page does not exists
				if($value!=-1 && (!get_post_status($value) || get_post_status($value)=='trash') ){
					$value = -1;
				}
				
				if($value==FALSE || $value==-1){
					$str .= '<div class="ihc-not-set">' . __('Default', 'ihc') . ' '.$label.' ' . __('Page', 'ihc') . ' <strong>' . __('Not set!', 'ihc') . '</strong></div>';
				}		
			}
			//return string for metabox
		}else{
			foreach($arr as $name=>$label){
				$value = get_option($name);
				
				//if page does not exists
				if($value!=-1 && (!get_post_status($value) || get_post_status($value)=='trash') ){
					$value = -1;
				}
				
				if($value==FALSE || $value==-1){
					if($str!='') $str .= '<span class="iump-separator"> | </span>';
					$str .= $label.' ' . __('Page', 'ihc');
				}		
			}
			//for general settings
			if($str){
				$str = '<div class="ihc-not-set"><strong>' . __('Some of the Default Pages are NOT Set:', 'ihc') . ' </strong>' . $str . '.</div>';
			}			
		}
	return $str;
}

function ihc_is_curl_enable(){
	/*
	 * @param none
	 * @return string
	 */
	if (!function_exists('curl_version')){
		return '<div class="ihc-not-set"><strong>' . __('Curl is disabled. Contact your hosting provider for more details', 'ihc') . ' </strong></div>';
	}
	return '';
}

function ihc_meta_box_page_type_message(){
	global $post;
	$str = '';
	if(get_post_type($post->ID)=='page'){
		//CHECK IF CURRENT PAGE IF REGISTER OR LOST PASSWORD
		$register_page = get_option('ihc_general_register_default_page');
		$lost_pass = get_option('ihc_general_lost_pass_page');
		$login_page = get_option('ihc_general_login_default_page');
		$redirect = get_option('ihc_general_redirect_default_page');
		$logout = get_option('ihc_general_logout_page'); 
		$user_page = get_option('ihc_general_user_page'); 
		$tos = get_option('ihc_general_tos_page');
		$subscription_plan = get_option('ihc_subscription_plan_page');
		$view_user_page = get_option('ihc_general_register_view_user');
		
		switch ($post->ID){
			case $register_page:
				$str .= _e('Register Page', 'ihc');
			break;
			case $lost_pass:
				$str .= _e('Lost Password Page', 'ihc');
			break;
			case $login_page:
				$str .= _e('Login Page', 'ihc');
			break;							
			case $redirect:
				$str .= __('Redirect Page.', 'ihc') . '<div class="ihc-meta-box-err-msg">' . __('You can only Replace the content.', 'ihc') . '</div>';
			break;
			case $logout:
				$str .= __('Logout Page', 'ihc');
			break;
			case $user_page:
				$str .= __('User Page', 'ihc');
			break;
			case $tos:
				$str .= __('TOS Page', 'ihc');
			break;
			case $subscription_plan:
				$str .= __('Subscription Plan Page', 'ihc');
			break;
			case $view_user_page:
				$str .= __('Visitor Inside User Page', 'ihc');
			break;
			default:
				return '';
			break;
		}
		if($str){
			$str = '<div class="ihc-meta-box-message"><span style="color:#333;">' . __('This Page is set as:', 'ihc') . ' </span>'.$str.'</div>';
		}
	}
	return $str;
}

function ihc_get_default_pages_il($return_set=false){
	$unset_arr = FALSE;
	$set_arr = FALSE;
	$arr_labels = array( 'ihc_general_register_default_page' => __('Register', 'ihc'),
						 'ihc_general_lost_pass_page' => __('Lost Password', 'ihc'),
						 'ihc_general_login_default_page' => __('Login', 'ihc'),
						 'ihc_general_redirect_default_page' => __('Redirect', 'ihc'),
						 'ihc_general_logout_page' => __('LogOut', 'ihc'),
						 'ihc_general_user_page' => __('Account User', 'ihc'),
						 'ihc_general_tos_page' => __('TOS', 'ihc'),
						 'ihc_subscription_plan_page' => __('Subscription', 'ihc'),
						 'ihc_general_register_view_user' => __('Visitor Inside User', 'ihc')
						);
	foreach($arr_labels as $name=>$label){
		$data = get_option($name);
		$arr_ids[$name] = -1;
		
		if ($data){
			$arr_ids[$name] = $data;
			/////testing if page really exists
			if($arr_ids[$name]!=-1 && (!get_post_status($arr_ids[$name]) || get_post_status($arr_ids[$name])=='trash') ){
				$arr_ids[$name] = -1;
			}			
		}
		if ($arr_ids[$name]==-1){
			$unset_arr[$name] = $label;
		} else {
			$set_arr[$name] = $data;
		}
	}
	if($return_set)	return $set_arr;
	return $unset_arr;
}

function ihc_delete_users($single_id=0, $ids=array()){
	/*
	 * DELETE USERS
	 * @param int, array
	 * @return none 
	 */
	if (!empty($single_id)){
		$ids[] = $single_id;
	}
	if ($ids){
		global $wpdb;
		$user_levels_table = $wpdb->prefix . "ihc_user_levels";
		foreach ($ids as $id){
			do_action('ihc_delete_user_action', $id);
			//send notification
			ihc_send_user_notifications($id, 'delete_account');			
			//delete
			wp_delete_user($id);
			$wpdb->query('DELETE FROM ' . $user_levels_table . ' WHERE user_id="' . $id . '";');
		}
	}
}

function ihc_get_user_custom_fields(){
	$data = get_option('ihc_user_fields');
	if($data!==FALSE){
		$not_native = array();
		foreach($data as $key=>$value){
			if($value['native_wp']==0){
				$not_native[] = array('name' => $value['name'], 'type' => $value['type'], 'label' => $value['label']);
			}
		}
		return $not_native;
	}
	return FALSE;
}

function ihc_delete_payment_entry($id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'indeed_members_payments';
	$wpdb->query( 'DELETE FROM '.$table_name.' WHERE id='.$id.'; ' );
}

function ihc_save_block_urls(){
	if (isset($_REQUEST['ihc_save_block_url'])){
		foreach (array('ihc_block_url_entire', 'ihc_block_url_word') as $val){
			if (isset($_REQUEST[$val.'-url']) && $_REQUEST[$val.'-url']){
				$data = get_option($val);
				if ($data){
					$key = ihc_array_value_exists($data, $_REQUEST[$val.'-url'], 'url');
					
					if ($key===FALSE){						
						$key = end((array_keys($data))) + 1;						
					}
				} else {
					$key = 1;
				}

				$data[$key] = array(
										'url' => $_REQUEST[$val.'-url'],
										'redirect' => $_REQUEST[$val.'-redirect'],
										'target_users' => $_REQUEST[$val.'-target_users'],
									);
				if (isset($_REQUEST['block_or_show'])){
					$data[$key]['block_or_show'] = $_REQUEST['block_or_show'];
				}
				update_option($val, $data);
			}
		}
	}
}

function ihc_delete_block_urls(){
	if (isset($_REQUEST['delete_block_url']) && $_REQUEST['delete_block_url']){
		$data = get_option('ihc_block_url_entire');
		if (isset($data[$_REQUEST['delete_block_url']])) unset($data[$_REQUEST['delete_block_url']]);
		update_option('ihc_block_url_entire', $data);
	}
	if (isset($_REQUEST['delete_block_regex']) && $_REQUEST['delete_block_regex']){
		$data = get_option('ihc_block_url_word');
		if (isset($data[$_REQUEST['delete_block_regex']])) unset($data[$_REQUEST['delete_block_regex']]);
		update_option('ihc_block_url_word', $data);		
	}
}


/* STATISTIC FUNCTIONS (for dashboard) */
function ihc_get_users_counts($type = 1){
	/*
	 * @param int : 1 = total, 2 = pending users, 3 = approved users 
	 * @return counts of users
	 * all (without admin)
	 * pending users
	 * approved users (without admin)
	 *  
	 */
	global $wpdb;	
	$meta_key = $wpdb->get_blog_prefix() . 'capabilities';
	$table = $wpdb->base_prefix . 'usermeta';	
	if ($type==1){
		//all
		$data = $wpdb->get_row("SELECT COUNT(DISTINCT(user_id)) as c FROM $table WHERE meta_key='$meta_key' AND meta_value NOT LIKE '%administrator%';");
		if ($data && isset($data->c)){
			return $data->c;
		}
	} else if($type==2){
		//pending users
		$data = $wpdb->get_row("SELECT COUNT(DISTINCT(user_id)) as c FROM $table WHERE meta_key='$meta_key' AND meta_value LIKE '%pending_user%';");	
		if ($data && isset($data->c)){
			return $data->c;
		}		
	} else {
		//approved users
		$data = $wpdb->get_row("SELECT COUNT(DISTINCT(user_id)) as c FROM $table WHERE meta_key='$meta_key' AND meta_value NOT LIKE '%administrator%' AND meta_value NOT LIKE '%pending_user%';");
		if ($data && isset($data->c)){
			return $data->c;
		}
	}
	return 0;
}

function ihc_get_last_five_users(){
	global $wpdb;
	$users = FALSE;
	$users_obj = new WP_User_Query(array(
									    'meta_query' => array(
													        array(
													            'key' => $wpdb->get_blog_prefix() . 'capabilities',
													            'value' => 'administrator',
													            'compare' => 'NOT LIKE'
													        )
													    ),
										'orderby' => 'user_registered',
										'order' => 'DESC',
										'number' => 5,
				));
	if (isset($users_obj->results) && count($users_obj->results)) $users = $users_obj->results;
	return $users;
}

function ihc_get_top_level(){
	global $wpdb;
	$return_value = FALSE;
	//get all levels
	$levels_data = get_option('ihc_levels');
	if ($levels_data && count($levels_data)){
		$levels_arr = array();
		foreach ($levels_data as $k=>$v){
			$levels_arr[$k] = 0;
		}
		$users_obj = new WP_User_Query(array(
				'meta_query' => array(
													        array(
													            'key' => $wpdb->get_blog_prefix() . 'capabilities',
													            'value' => 'administrator',
													            'compare' => 'NOT LIKE'
													        )
													    ),
				'offset' => 0,
		));
		if (isset($users_obj->results) && count($users_obj->results) ){
			foreach ($users_obj->results as $user){
				$user_levels = get_user_meta($user->data->ID, 'ihc_user_levels', true);
				
				if ($user_levels){
					if (strpos($user_levels, ',')!==FALSE){
						$u_level = explode(',', $user_levels);
						foreach ($u_level as $level_id){
							if (isset($levels_arr[(int)$level_id])){
								$levels_arr[(int)$level_id]++;
							}							
						}
					} else {
						if (isset($levels_arr[(int)$user_levels])){
							$levels_arr[(int)$user_levels]++;
						}
					}
				}

			}
		}
		asort($levels_arr);
		end($levels_arr);
		$return_value = key($levels_arr);
		$return_value = $levels_data[$return_value]['name'];
	}	
	return $return_value;
}

function ihc_get_level_user_counts(){
	global $wpdb;	
	//get all levels
	$levels_data = get_option('ihc_levels');
	$levels_arr = array();
	$arr = FALSE;
	if ($levels_data && count($levels_data)){
		$levels_arr = array();
		foreach ($levels_data as $k=>$v){
			$levels_arr[$k] = 0;
		}
		$users_obj = new WP_User_Query(array(
				'meta_query' => array(
													        array(
													            'key' => $wpdb->get_blog_prefix() . 'capabilities',
													            'value' => 'administrator',
													            'compare' => 'NOT LIKE'
													        )
													    ),
				'offset' => 0,
		));
		if (isset($users_obj->results) && count($users_obj->results) ){
			foreach ($users_obj->results as $user){
				$user_levels = get_user_meta($user->data->ID, 'ihc_user_levels', true);
	
				if ($user_levels){
					if (strpos($user_levels, ',')!==FALSE){
						$u_level = explode(',', $user_levels);
						foreach ($u_level as $level_id){
							if (isset($levels_arr[(int)$level_id])){
								$levels_arr[(int)$level_id]++;
							}
						}
					} else {
						if (isset($levels_arr[(int)$user_levels])){
							$levels_arr[(int)$user_levels]++;
						}
					}
				}
	
			}
		}
	}
	foreach ($levels_arr as $k=>$v){
		$new_key = $levels_data[$k]['name'];
		$arr[$new_key] = $v;
	}
	return $arr;
}

function ihc_get_transactions_count(){
	global $wpdb;
	$count = 0;
	$data = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'indeed_members_payments;' );
	if($data && count($data)){
		$count = count($data);
	}	
	return $count;
}

function ihc_get_total_amount(){
	/*
	 * @param none
	 * @return float
	 */
	global $wpdb;
	$count = 0;
	$data = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'indeed_members_payments;' );
	if ($data && count($data)){
		foreach ($data as $obj){
			if (!empty($obj->history)){
				@$history_data = unserialize($obj->history);	
				if ($history_data && is_array($history_data)){
					// calculating with reccuring payments from entire history
					foreach ($history_data as $arr){
						$amount = 0;
						if (isset($arr['amount'])){
							if (isset($arr['ihc_payment_type']) && !empty($arr['ihc_payment_type']) && $arr['ihc_payment_type']=='stripe' && (empty($arr['type']) || $arr['type']!='charge.succeeded') ){
								$amount = 0;//stripe first row entry
							} else {
								$amount = (float)$arr['amount'];
							}
						} else if (isset($arr['mc_gross'])){
							$amount = (float)$arr['mc_gross'];
						} else if (isset($arr['x_amount'])){
							$amount = (float)$arr['x_amount'];
						}
						$count += $amount;
					}
				} else {
					$history_not_available = TRUE;
				}
			} else {
				$history_not_available = TRUE;
			}
			
			// history not available, add from last payment
			if (!empty($history_not_available)){
				$amount = 0;
				if (isset($obj->payment_data)){
					$arr = json_decode($obj->payment_data, TRUE);
					if (isset($arr['amount'])){
						$amount = (float)$arr['amount'];
					} else if (isset($arr['mc_gross'])){
						$amount = (float)$arr['mc_gross'];
					} else if (isset($arr['x_amount'])){
						$amount = (float)$arr['x_amount'];
					}
				}
				$count = $count + $amount;
				unset($history_not_available);
			}
			// history not available, add from last payment

		}
	}	
	return $count;
}

function ihc_get_levels_top_by_transactions(){
	global $wpdb;
	$levels_arr = array();
	$arr = FALSE;
	//get all levels
	$levels_data = get_option('ihc_levels');
	$arr = FALSE;
	if ($levels_data && count($levels_data)){
		$levels_arr = array();
		foreach ($levels_data as $k=>$v){
			$levels_arr[$k] = 0;
		}
		$data = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'indeed_members_payments;' );
		if ($data && count($data)){
			foreach ($data as $k=>$v){
				$payment_data = json_decode($v->payment_data);
				if (isset($payment_data->custom)){
					$payment_data = json_decode($payment_data->custom, true);
					$level_id = $payment_data['level_id'];
					if (isset($levels_arr[$level_id])){
						$levels_arr[$level_id]++;
					}					
				}
			}			
		}
	}	
	if (count($levels_arr)){
		foreach ($levels_arr as $k=>$v){
			$arr[$levels_data[$k]['name']] = $v;
		}
	}
	return $arr;
}

function ihc_get_last_five_transactions(){
	global $wpdb;
	$obj = '';
	$data = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'indeed_members_payments ORDER BY id DESC LIMIT 5;' );
	if ($data) $obj = $data;
	return $obj;
}

function ihc_generate_color(){
    mt_srand((double)microtime()*1000000); 
    $color_code = '';
    while(strlen($color_code)<6){
        $color_code .= sprintf("%02X", mt_rand(0, 255));
    }
	return '#'.$color_code;
}


function ihc_get_notification_metas($id=FALSE){
	/*
	 *
 	 */
	global $wpdb;
	if ($id){
		return (array)$wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "ihc_notifications` WHERE id='".$id."';");
	} else {
		return array('notification_type'=>'', 'level_id'=>-1, 'subject'=>'', 'message'=>'',);
	}

}

function ihc_save_notification_metas($post_data){
	/*
	 *
	 */
	global $wpdb;
	if (isset($post_data['notification_id'])){
		//update
		$wpdb->query("UPDATE `" . $wpdb->prefix . "ihc_notifications`
						SET notification_type = '".$post_data['notification_type']."',
						level_id = '".$post_data['level_id']."',
						subject = '".$post_data['subject']."',
						message = '".$post_data['message']."'
						WHERE id  = '".$post_data['notification_id']."'	
				");		
	} else {
		//create
		$wpdb->query("INSERT INTO `" . $wpdb->prefix . "ihc_notifications` 
						VALUES(null, '".$post_data['notification_type']."', '".$post_data['level_id']."', '".$post_data['subject']."', '".$post_data['message']."', '1')");
	}
		
}

function ihc_get_all_notification_available(){
	global $wpdb;
	$data = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "ihc_notifications`;" );
	return $data;
}

function ihc_delete_notification($id){
	global $wpdb;
	$wpdb->query("DELETE FROM `" . $wpdb->prefix . "ihc_notifications` WHERE id='".$id."';" );	
}

function ihc_general_options_print_page_links($id=FALSE){
	if ($id!=-1 && $id!==FALSE){
		$target_page_link = get_permalink($id);
		if ($target_page_link) {
			echo '<div class="ihc-general-options-link-pages">' . __('Link:', 'ihc') . ' <a href="' . $target_page_link . '" target="_blank">' . $target_page_link . '</a></div>';
		}
	}
	return '';
}

function ihc_check_payment_status($p_type=''){
	/*
	 * @param string
	 * @return array
	 */
	$return = array();
	$return['active'] = '';
	$return['status'] = 0;
	$return['settings'] = 'Uncompleted';
	switch($p_type){
		case 'paypal':
					  $arr = ihc_return_meta_arr('payment_paypal');
					  if ($arr['ihc_paypal_status'] == 1) { $return['active'] = 'paypal-active'; $return['status'] = 1; }
					  if ($arr['ihc_paypal_email'] != '') $return['settings'] = 'Completed';
					  break;
		case 'stripe':
					  $arr = ihc_return_meta_arr('payment_stripe');
					  if ($arr['ihc_stripe_status'] == 1) { $return['active'] = 'stripe-active'; $return['status'] = 1; }
					  if ($arr['ihc_stripe_secret_key'] != '' && $arr['ihc_stripe_publishable_key'] != '') $return['settings'] = 'Completed';
					  break;
		case 'authorize':
					  $arr = ihc_return_meta_arr('payment_authorize');
					  if ($arr['ihc_authorize_status'] == 1) { $return['active'] = 'authorize-active'; $return['status'] = 1; }
					  if ($arr['ihc_authorize_login_id'] != '' && $arr['ihc_authorize_transaction_key'] != '') $return['settings'] = 'Completed';
					  break;	
		case 'twocheckout':
			$arr = ihc_return_meta_arr('payment_twocheckout');
			if ($arr['ihc_twocheckout_status'] == 1) {
				$return['active'] = 'twocheckout-active'; $return['status'] = 1;
			}
			if ($arr['ihc_twocheckout_api_user'] != '' && $arr['ihc_twocheckout_api_pass'] != '' 
				&& $arr['ihc_twocheckout_private_key'] != '' && $arr['ihc_twocheckout_account_number'] != ''
				&& $arr['ihc_twocheckout_secret_word'] != '' ) $return['settings'] = 'Completed';
			break;
		case 'bank_transfer':
			$arr = ihc_return_meta_arr('payment_bank_transfer');
			if ($arr['ihc_bank_transfer_status'] == 1) {
				$return['active'] = 'bank_transfer-active'; $return['status'] = 1;
			}	
			if (isset($arr['ihc_bank_transfer_message'])){
				$return['settings'] = 'Completed';
			}		
			break;
		case 'braintree':
			$arr = ihc_return_meta_arr('payment_braintree');
			if ($arr['ihc_braintree_status'] == 1) {
				$return['active'] = 'braintree-active'; $return['status'] = 1;
			}	
			if (!empty($arr['ihc_braintree_merchant_id']) && !empty($arr['ihc_braintree_public_key']) && !empty($arr['ihc_braintree_private_key'])){
				$return['settings'] = 'Completed';
			}					
			break;
		case 'payza':
			$arr = ihc_return_meta_arr('payment_payza');
			if ($arr['ihc_payza_status'] == 1) {
				$return['active'] = 'payza-active'; $return['status'] = 1;
			}	
			if (!empty($arr['ihc_payza_email'])){
				$return['settings'] = 'Completed';
			}					
			break;			
	}
	return $return;
}

function ihc_generate_coupon_box($id=0, $settings=array(), $url=''){
	/*
	 * @param id = int, settings = array, url = string
	 * @return string
	 */
	$div_id = "ihc_coupon_box_" . $id;
	?>
	<div class="ihc-coupon-admin-box-wrap" id="<?php echo $div_id;?>">
		<div class="ihc-coupon-box-wrap" id="" style="background-color: <?php echo $settings['settings']['box_color'];?>">
			<div class="ihc-coupon-box-main">
				<div class="ihc-coupon-box-title"><?php echo $settings['code'];?></div>
				<div class="ihc-coupon-box-content">
					<div class="ihc-coupon-box-levels"><?php 
						_e("Target Levels: ", "ihc");
						echo '<span>';
						if ($settings['settings']['target_level']==-1){
							_e("All", "ihc");	
						} else {
							$level_data = ihc_get_level_by_id($settings['settings']['target_level']);
							echo $level_data['label'];
						}
						echo '</span>';
					?></div>
					
					
				</div>
				<div class="ihc-coupon-box-links-wrap">
					<div class="ihc-coupon-box-links">
						<a href="<?php echo $url . '&id=' . $id;?>" class="ihc-coupon-box-link">Edit</a>
						<div class="ihc-coupon-box-link" onClick="ihc_delete_coupon(<?php echo $id;?>, '#<?php echo $div_id;?>');">Delete</div>
					</div>
				</div>
			</div>
			<div class="ihc-coupon-box-bottom">
				<div class="ihc-coupon-box-bottom-disccount"><?php
						echo $settings['settings']['discount_value'];
						if ($settings['settings']['discount_type']=='percentage'){
							echo "%";
						} else {
							echo ' '.get_option('ihc_currency');
						}
					?></div>
				<div class="ihc-coupon-box-bottom-submitted"><?php 
					_e("Submited Coupons:", "ihc");
					echo ' <strong>'.$settings['submited_coupons_count'];
					if (!empty($settings['settings']['repeat'])){
						 echo "/" . $settings['settings']['repeat'];
					}
					echo '</strong>';
				?></div> 
				 
				<div class="ihc-coupon-box-bottom-date"><?php 
						if ($settings['settings']['period_type']=='unlimited'){ echo '<span style="line-height: 37px;">'.__("No Date range", 'ihc').'</span>';
						}else if (!empty($settings['settings']['start_time']) && !empty($settings['settings']['end_time'])) {
							echo __("From ", "ihc") .'<span>'. $settings['settings']['start_time'] . "</span><br/> " . __("to ", "ihc") .'<span>'. $settings['settings']['end_time'].'</span>';	
						} else {
							echo '-';	
						}
					?>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<?php 	
}


function ihc_add_new_redirect_link($post_data=array()){
	/*
	 * @param array $_POST
	 * @return none
	 */
	if (!empty($post_data)){
		if (strpos($post_data['url'], 'http')===FALSE){
			$post_data['url'] = "http://" . $post_data['url'];
		}
		$data = get_option("ihc_custom_redirect_links_array");
		if ($data && is_array($data)){
			if (!array_key_exists($post_data['name'], $data)){
				$data[$post_data['name'] ] = $post_data['url'];
			}
		} else {
			$data[$post_data['name'] ] = $post_data['url'];
		}
		update_option("ihc_custom_redirect_links_array", $data);
	}
}

function ihc_delete_redirect_link($name=''){
	/*
	 * @param string
	 * @return none
	 */
	$data = get_option("ihc_custom_redirect_links_array");
	if (isset($data[$name])){
		unset($data[$name]);
	}
	update_option("ihc_custom_redirect_links_array", $data);
}

function ihc_get_redirect_links_as_arr_for_select(){
	/*
	 * @param none
	 * @return array
	 */
	$return = array();
	$redirect_links = get_option("ihc_custom_redirect_links_array");
	if (is_array($redirect_links) && count($redirect_links)){
		foreach ($redirect_links as $k=>$v){
			$return[$k] = __("Custom Link: ", 'ihc') . $k;
		}
	}
	if (ihc_is_magic_feat_active('individual_page')){
		$return['#individual_page#'] = __('Individual Page (from Magic Features)', 'ihc');
	}
	return $return;
}

function ihc_check_payment_gateways(){
	/*
	 * @param none
	 * @return string
	 */
	$levels = get_option('ihc_levels');
	if ($levels){
		$paid_levels = FALSE;
		foreach ($levels as $level){
			if ($level['payment_type']=='payment'){
				$paid_levels = TRUE;
			}
		}
		if ($paid_levels){
			$payments_gateways = array('paypal', 'authorize', 'twocheckout', 'bank_transfer', 'stripe', 'braintree', 'payza');
			$err_msg = TRUE;
			foreach ($payments_gateways as $payment_gateway){
				if (ihc_check_payment_available($payment_gateway)){
					$err_msg = FALSE;
					break;
				}
			}
			
			if ($err_msg){
				return '<div class="ihc-not-set" style="margin-top:5px;"><strong>' . __('No Payment Gateway was activated or properly set!', 'ihc') . '</strong></div>';
			}
			
			$default_payment = get_option('ihc_payment_selected');
			if (!ihc_check_payment_available($default_payment)){
				return '<div class="ihc-not-set" style="margin-top:5px;"><strong>' . __("Default Payment Gateway it's not activated or properly set!", 'ihc') . '</strong></div>';				
			}
			
		}
	}
}
function get_ump_version(){
	/*
	 * @param none
	 * @return float
	 */
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_data = get_plugin_data( IHC_PATH . 'indeed-membership-pro.php', false, false);
	return $plugin_data['Version'];
}

function ihc_save_block_group($group_name='', $post_data=array(), $key=''){
	/*
	 * @param string, array, string .  key can be post_type (post, pages, products) || category name
	 * @return none
	 */
	if ($group_name && $post_data){
		$data = get_option($group_name);
		if (empty($data)){
			$key = 0;
		} else {
			end($data);
			$key = key($data);
			$key++;
		}
		$data[$key] = $post_data;
		update_option($group_name, $data);
	}
}

function ihc_delete_block_group($group_name='', $key=0){
	/*
	 * @param string, int
	 * @return none
	 */
	 if (isset($group_name) && isset($key)){	 	
	 	 $data = get_option($group_name);		
		 if (!empty($data[$key])){
		 	unset($data[$key]);
		 }
		 update_option($group_name, $data);
	 }
}