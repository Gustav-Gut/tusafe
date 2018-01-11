<?php
if (!class_exists('Ihc_Db')):
	
class Ihc_Db{
	
	public function __construct(){}
	
	public static function create_tables(){
		/*
		 * @param none
		 * @return none
		 */
		global $wpdb;
		$table_name = $wpdb->prefix . "ihc_user_levels";
		if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE " . $table_name . " (
														id int(11) NOT NULL AUTO_INCREMENT,
														user_id int(11) NOT NULL,
														level_id int(11) NOT NULL,
														start_time datetime,
														update_time datetime,
														expire_time datetime,
														notification tinyint(1) DEFAULT 0,
														status int(3) NOT NULL,
														PRIMARY KEY (`id`)
			);";
			dbDelta ( $sql );
		}
		//ihc_debug_payments
		$table_name = $wpdb->prefix . "ihc_debug_payments";
		if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE " . $table_name . " (
						id int(11) NOT NULL AUTO_INCREMENT,
						source VARCHAR(200),
						message TEXT,
						insert_time datetime,
						PRIMARY KEY (`id`)
			);";
			dbDelta ( $sql );
		}			
		////////// indeed_members_payments
		$table_name = $wpdb->prefix . 'indeed_members_payments';
		if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE " . $table_name . " (
						id int(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
						txn_id VARCHAR(100) DEFAULT NULL,
						u_id int(9) DEFAULT NULL,
						payment_data text DEFAULT NULL,
						history TEXT,
						orders TEXT DEFAULT NULL,
						paydate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
				);";
			dbDelta($sql);
		}
		
		//ihc_notifications
		$table_name = $wpdb->prefix . "ihc_notifications";
		if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE " . $table_name . " (
						id int(11) NOT NULL AUTO_INCREMENT,
						notification_type VARCHAR(200),
						level_id VARCHAR(200),
						subject TEXT,
						message TEXT,
						status TINYINT(1),
						PRIMARY KEY (`id`)
					);";
			dbDelta($sql);
		}
	
		//ihc_coupons
		$table_name = $wpdb->prefix . "ihc_coupons";
		if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE " . $table_name . " (
						id int(11) NOT NULL AUTO_INCREMENT,
						code varchar(200),
						settings text,
						submited_coupons_count int(11),
						status tinyint(1),
						PRIMARY KEY (`id`)
			);";
			dbDelta ( $sql );
		}
			
		//ihc_orders
		$table = $wpdb->prefix . 'ihc_orders';
		if ($wpdb->get_var( "show tables like '$table'" )!=$table){
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE $table(
										id INT(11) NOT NULL AUTO_INCREMENT,
										uid INT(11),
										lid INT(11),
										amount_type VARCHAR(200),
										amount_value DECIMAL(12, 2) DEFAULT 0,
										automated_payment TINYINT(1) DEFAULT NULL,
										status VARCHAR(100),
										create_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,									
										PRIMARY KEY (`id`)
			);";
			dbDelta($sql);		
		}

		///ihc_orders_meta
		$table = $wpdb->prefix . 'ihc_orders_meta';
		if ($wpdb->get_var("show tables like '$table'")!=$table){
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE $table(
										id INT(11) NOT NULL AUTO_INCREMENT,
										order_id INT(11),
										meta_key VARCHAR(200),
										meta_value TEXT,
										PRIMARY KEY (`id`)
			);";
			dbDelta($sql);
		}
		
		//ihc_taxes
		$table = $wpdb->prefix . 'ihc_taxes';
		if ($wpdb->get_var( "show tables like '$table'" )!=$table){
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE $table(
										id INT(11) NOT NULL AUTO_INCREMENT,
										country_code VARCHAR(20),
										state_code VARCHAR(50) DEFAULT '',
										amount_value DECIMAL(12, 2) DEFAULT 0,
										label VARCHAR(200),
										description TEXT,
										status TINYINT(1),					
										PRIMARY KEY (`id`)
			);";
			dbDelta($sql);		
		}	
		
		/// IHC_DASHBOARD_NOTIFICATIONS
		$table_name = $wpdb->prefix . 'ihc_dashboard_notifications';
		if ($wpdb->get_var("show tables like '$table_name'")!=$table_name){
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE $table_name (
						type VARCHAR(40) NOT NULL,
						value INT(11) DEFAULT 0
			);";
			dbDelta($sql);			
			
			/// THIS TABLE WILL CONTAIN ONLY THIS TWO ENTRIES	
			$wpdb->query("INSERT INTO $table_name VALUES('users', 0);");
			$wpdb->query("INSERT INTO $table_name VALUES('orders', 0);");		
		}	
		
		/// ihc_cheat_off
		$table_name = $wpdb->prefix . 'ihc_cheat_off';
		if ($wpdb->get_var("show tables like '$table_name'")!=$table_name){
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			$sql = "CREATE TABLE $table_name (
						uid INT(11) NOT NULL,
						hash VARCHAR(40) NOT NULL
			);";
			dbDelta($sql);			
		}	

		//ihc_invitation_codes
		$table_name = $wpdb->prefix . "ihc_invitation_codes";
		if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE " . $table_name . " (
						id int(11) NOT NULL AUTO_INCREMENT,
						code varchar(200),
						settings text,
						submited int(11),
						repeat_limit int(11),
						status tinyint(1),
						PRIMARY KEY (`id`)
			);";
			dbDelta ( $sql );
		}		
		
		$table_name = $wpdb->prefix . 'ihc_gift_templates';
		if ($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){
			require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
			$sql = "CREATE TABLE " . $table_name . " (
						id INT(11) NOT NULL AUTO_INCREMENT,
						lid INT(11),
						settings TEXT,
						status TINYINT(2),
						PRIMARY KEY (`id`)
			);";
			dbDelta ( $sql );
		}		
												 
	}
	
	public static function update_tables_structure(){
		/*
		 * @param none
		 * @return none
		 */
		global $wpdb;	
		$table = $wpdb->prefix . 'indeed_members_payments';
		$data = $wpdb->get_row("SHOW COLUMNS FROM " . $table . " LIKE 'txn_id';");
		if (!$data){
			$q = 'ALTER TABLE ' . $wpdb->prefix . 'indeed_members_payments ADD history TEXT AFTER payment_data';
			$wpdb->query($q);
			$q = 'ALTER TABLE ' . $wpdb->prefix . 'indeed_members_payments ADD txn_id VARCHAR(100) DEFAULT NULL AFTER id';
			$wpdb->query($q);
		}
		
		$data = $wpdb->get_row("SHOW COLUMNS FROM " . $table . " LIKE 'orders';");
		if (!$data){
			$q = "ALTER TABLE $table ADD orders TEXT AFTER history";
			$wpdb->query($q);
		}
	
		$table = $wpdb->prefix . 'ihc_user_levels';
		$data = $wpdb->get_row("SHOW COLUMNS FROM " . $table . " LIKE 'notification';");
		if (!$data){
			$q = 'ALTER TABLE ' . $wpdb->prefix . 'ihc_user_levels ADD notification tinyint(1) DEFAULT 0 AFTER expire_time;';
			$wpdb->query($q);
		}	
		
		/// alter ihc_taxes if its case
		$table = $wpdb->prefix . 'ihc_taxes';
		$data = $wpdb->get_row("SHOW COLUMNS FROM " . $table . " LIKE 'state_code';");
		if (!$data){
			$q = "ALTER TABLE $table ADD state_code VARCHAR(50) DEFAULT '' AFTER country_code;";
			$wpdb->query($q);
		}				 
	}

	public static function do_uninstall(){
		/*
		 * @param none
		 * @return none
		 */
		$values = self::default_settings_groups();
		foreach ($values as $value){
			$data = ihc_return_meta_arr($value, true);
			foreach ($data as $k=>$v){
				delete_option($k);
			}	
		}
		
		delete_option('ihc_levels');//delete the levels
		delete_option('ihc_lockers');//delete the lockers
		delete_option('ihc_dashboard_allowed_roles');
		delete_option('ihc_custom_redirect_links_array');
		
		//delete table indeed_members_payments
		global $wpdb;
		$tables = array( 
						 $wpdb->prefix . "indeed_members_payments", 
						 $wpdb->prefix . "ihc_user_levels", 
						 $wpdb->prefix . "ihc_debug_payments", 
						 $wpdb->prefix . "ihc_notifications",
						 $wpdb->prefix . "ihc_coupons",
						 $wpdb->prefix . 'ihc_orders',
						 $wpdb->prefix . 'ihc_orders_meta',
						 $wpdb->prefix . 'ihc_taxes',
						 $wpdb->prefix . 'ihc_dashboard_notifications',
						 $wpdb->prefix . 'ihc_cheat_off',
						 $wpdb->prefix . 'ihc_invitation_codes',
						 $wpdb->prefix . 'ihc_gift_templates',
		);
		foreach ($tables as $table){
			$wpdb->query("DROP TABLE IF EXISTS $table;");
		}
		
		//delete user levels
		$users_obj = new WP_User_Query(array(
				'meta_query' => array(
						'relation' => 'OR',
						array(
								'key' => $wpdb->get_blog_prefix() . 'capabilities',
								'value' => 'subscriber',
								'compare' => 'like'
						),
						array(
								'key' => $wpdb->get_blog_prefix() . 'capabilities',
								'value' => 'pending_user',
								'compare' => 'like'
						)
				)
		));
		$users = $users_obj->results;
		if (!empty($users)){
			foreach ($users as $user){
				delete_user_meta($user->data->ID, 'ihc_user_levels');
			}	
		}		 
	}

	public static function create_notifications(){
		/*
		 * @param none
		 * @return none
		 */
		global $wpdb;
		$keys = array(	
						'email_check', 
						'email_check_success', 
						'reset_password', 
						'admin_user_register', 
						'reset_password_process', 
						'change_password', 
						'register', 
						'review_request',
						'approve_account',
						'bank_transfer',
						'register_lite_send_pass_to_user',
		);
		$table = $wpdb->prefix . "ihc_notifications"; 
		if (!function_exists('ihc_save_notification_metas')){
			require_once IHC_PATH . 'admin/includes/functions.php';		
		}
		foreach ($keys as $key){
			$check = $wpdb->get_row("SELECT id FROM $table WHERE notification_type='$key';");
			if (empty($check)){
				$notf_data = ihc_return_notification_pattern($key);
				$notf_data['message'] = @$notf_data['content'];
				$notf_data['notification_type'] = $key;
				$notf_data['level_id'] = -1;		
				ihc_save_notification_metas($notf_data);
				unset($notf_data);
			}
		}		 
	}
	
	public static function create_default_pages(){
			/*
			 * @param none
			 * @return none
			 */
		$insert_array = array(
						'ihc_general_user_page' => array(
											'title' => __('IUMP - Account Page', 'ihc'),
											'content' => '[ihc-user-page]',
						),
						'ihc_general_login_default_page' => array(
											'title' => __('IUMP - Login', 'ihc'),
											'content' => '[ihc-login-form]',
						),
						'ihc_general_logout_page' => array(
											'title' => __('IUMP - LogOut', 'ihc'),
											'content' => '[ihc-logout-link]',
						),
						'ihc_general_register_default_page' => array(
											'title' => __('IUMP - Register', 'ihc'),
											'content' => '[ihc-register]',
						),	
						'ihc_general_lost_pass_page' => array(
											'title' => __('IUMP - Reset Password', 'ihc'),
											'content' => '[ihc-pass-reset]',
						),	
						'ihc_subscription_plan_page' => array(
											'title' => __('IUMP - Subscription Plan', 'ihc'),
											'content' => '[ihc-select-level]',
						),								
						'ihc_general_tos_page' => array(
											'title' => __('IUMP - TOS Page', 'ihc'),
											'content' => 'Terms of Services',
						),	
						'ihc_general_register_view_user' => array(
											'title' => __('IUMP - Visitor Inside User Page', 'ihc'),
											'content' => '[ihc-visitor-inside-user-page]',
						),																	
		);

		foreach ($insert_array as $key=>$inside_arr){
			$exists = get_option($key);
			if (!$exists){
				$arr = array(
							'post_content' => $inside_arr['content'],
							'post_title' => $inside_arr['title'],
							'post_type' => 'page',
							'post_status' => 'publish',
				);
				$post_id = wp_insert_post($arr);
				update_option($key, $post_id);				
			}				
		}
	}	

	public static function create_default_redirects(){
		/*
		 * @param none
		 * @return none
		 */
		///DEFAULT REDIRECT
		$exists = get_option('ihc_general_redirect_default_page');
		if (!$exists){
			$arr = array(
							'post_content' => 'Redirected',
							'post_title' => 'IUMP - Default Redirect Page',
							'post_type' => 'page',
							'post_status' => 'publish',
			);
			$post_id = wp_insert_post($arr);
			update_option('ihc_general_redirect_default_page', $post_id);				
		}		
		
		///AFTER LOGIN
		$exists = get_option('ihc_general_logout_redirect');
		if (!$exists){
			$login = get_option('ihc_general_login_default_page');
			update_option('ihc_general_logout_redirect', $login);					
		}
		
		///AFTER REGISTER
		$exists = get_option('ihc_general_register_redirect');
		if ($exists){
			$account_page = get_option('ihc_general_user_page');
			update_option('ihc_general_register_redirect', $account_page);								
		}
					
		///AFTER LOGIN
		$exists = get_option('ihc_general_login_redirect');
		if (!$exists){
			$account_page = get_option('ihc_general_user_page');
			update_option('ihc_general_login_redirect', $account_page);			
		}
	}

	public static function create_extra_redirects(){
		/*
		 * @param none
		 * @return none
		 */
		 $login = get_option('ihc_general_login_default_page');
		 $account_page = get_option('ihc_general_user_page');
		 $register = get_option('ihc_general_register_default_page');
		 $logout = get_option('ihc_general_logout_page');
		 $lost_password = get_option('ihc_general_lost_pass_page');
		 if ($login){
		 	/// LOGIN
		 	update_post_meta($login, 'ihc_mb_type', 'show');
		 	update_post_meta($login, 'ihc_mb_who', 'unreg');
			update_post_meta($login, 'ihc_mb_block_type', 'redirect');
			update_post_meta($login, 'ihc_mb_redirect_to', $account_page);
		 }
		 if ($account_page){
		 	/// ACCOUNT PAGE
		 	update_post_meta($account_page, 'ihc_mb_type', 'show');
		 	update_post_meta($account_page, 'ihc_mb_who', 'reg');
			update_post_meta($account_page, 'ihc_mb_block_type', 'redirect');
			update_post_meta($account_page, 'ihc_mb_redirect_to', $login);		 	
		 }
		 if ($register){
		 	/// REGISTER PAGE
		 	update_post_meta($register, 'ihc_mb_type', 'show');
		 	update_post_meta($register, 'ihc_mb_who', 'unreg');
			update_post_meta($register, 'ihc_mb_block_type', 'redirect');
			update_post_meta($register, 'ihc_mb_redirect_to', $account_page);		 	
		 }
		 if ($logout){
		 	///LOGOUT
		 	update_post_meta($logout, 'ihc_mb_type', 'show');
		 	update_post_meta($logout, 'ihc_mb_who', 'reg');
			update_post_meta($logout, 'ihc_mb_block_type', 'redirect');
			update_post_meta($logout, 'ihc_mb_redirect_to', $login);			 	
		 }
		 if ($lost_password){
		 	///LOGOUT
		 	update_post_meta($lost_password, 'ihc_mb_type', 'show');
		 	update_post_meta($lost_password, 'ihc_mb_who', 'unreg');
			update_post_meta($lost_password, 'ihc_mb_block_type', 'redirect');
			update_post_meta($lost_password, 'ihc_mb_redirect_to', $account_page);			 	
		 }		 
	}

	public static function create_default_lockers(){
		/*
		 * @param none
		 * @return none
		 */
		 $data = get_option('ihc_lockers');
		 if ($data){
		 	return;
		 }
		 $array = array(
		 				'ihc_locker_name' => 'Loker with Form',
		 				'ihc_locker_custom_content' => '<h2>This content is locked</h2>Login To Unlock The Content!',
		 				'ihc_locker_custom_css' => '',
		 				'ihc_locker_template' => 3,
		 				'ihc_locker_login_template' => 'ihc-login-template-7',
		 				'ihc_locker_login_form' => 1,
		 				'ihc_locker_additional_links' => 1,
		 				'ihc_locker_display_sm' => 0,
		 );
		 self::save_update_locker_template($array);
		 $array = array(
		 				'ihc_locker_name' => 'Empty Showcase (only hide)',
		 				'ihc_locker_custom_content' => '',
		 				'ihc_locker_custom_css' => '.ihc-locker-wrap{}',
		 				'ihc_locker_template' => 1,
		 				'ihc_locker_login_template' => '',
		 				'ihc_locker_login_form' => 0,
		 				'ihc_locker_additional_links' => 0,
		 				'ihc_locker_display_sm' => 0,
		 );
		 self::save_update_locker_template($array);		 
	}
	
	public static function create_demo_levels(){
		/*
		 * @param none
		 * @return none
		 */
		if (!function_exists('ihc_save_level')){
			include_once IHC_PATH . 'admin/includes/functions/levels.php';
		}
		$array = array(
							'name'=>'free_demo', 
							'payment_type'=>'free',
							'price'=>'',					
						    'label'=>'Free',
							'description'=>'<strong>Free</strong> level allowing limited access on most of our Content sides. ',
							'price_text' => 'Sign up Now!',
							'order' => '',
							'access_type' => 'unlimited',
							'access_limited_time_type' => 'D',
							'access_limited_time_value' => '',
							'access_interval_start' => '',
							'access_interval_end' => '',
							'access_regular_time_type' => 'D',
							'access_regular_time_value' => '',
							'billing_type' => '',
							'billing_limit_num' => '2',
							'show_on' => '1',
							'afterexpire_level' => -1,
							'custom_role_level' => '-1',
							'start_date_content' => '0',
							'special_weekdays' => '',
							//trial
							'access_trial_time_value' => '',
							'access_trial_time_type' => 'D',
							'access_trial_price' => '',
							'access_trial_couple_cycles' => '',			
							'access_trial_type' => 1,
		);
		ihc_save_level($array, TRUE);	 
		$array = array(
							'name'=>'onetime_demo', 
							'payment_type'=>'payment',
							'price'=>10,					
						    'label'=>'One Time Plan',
							'description'=>'<h4><strong>Preimum Content!</strong></h4>
Is an <strong>One Time</strong> payment with a small fee. Just have a test.',
							'price_text' => 'only $10',
							'order' => '',
							'access_type' => 'unlimited',
							'access_limited_time_type' => 'D',
							'access_limited_time_value' => '',
							'access_interval_start' => '',
							'access_interval_end' => '',
							'access_regular_time_type' => 'D',
							'access_regular_time_value' => '',
							'billing_type' => '',
							'billing_limit_num' => '2',
							'show_on' => '1',
							'afterexpire_level' => -1,
							'custom_role_level' => '-1',
							'start_date_content' => '0',
							'special_weekdays' => '',
							//trial
							'access_trial_time_value' => '',
							'access_trial_time_type' => 'D',
							'access_trial_price' => '',
							'access_trial_couple_cycles' => '',			
							'access_trial_type' => 1,
		);
		ihc_save_level($array, TRUE);
		$array = array(
							'name'=>'recurring_demo', 
							'payment_type'=>'payment',
							'price'=>1,					
						    'label'=>'Recurring Plan',
							'description'=>'Is a <strong>Recurring</strong> Payment (monthly) on a small fee for testing purpose.
<h4>New Updates will be available!</h4>',
							'price_text' => 'only $1',
							'order' => '',
							'access_type' => 'regular_period',
							'access_limited_time_type' => 'D',
							'access_limited_time_value' => '',
							'access_interval_start' => '',
							'access_interval_end' => '',
							'access_regular_time_type' => 'M',
							'access_regular_time_value' => 1,
							'billing_type' => 'bl_ongoing',
							'billing_limit_num' => '2',
							'show_on' => '1',
							'afterexpire_level' => -1,
							'custom_role_level' => '-1',
							'start_date_content' => '0',
							'special_weekdays' => '',
							//trial
							'access_trial_time_value' => '',
							'access_trial_time_type' => 'D',
							'access_trial_price' => '',
							'access_trial_couple_cycles' => '',			
							'access_trial_type' => 1,
		);
		ihc_save_level($array, TRUE);						
	}
		
	public static function add_new_role(){
		/*
		 * @param none
		 * @return none
		 */
		add_role( 'pending_user', 'Pending', array( 'read' => false, 'level_0' => true ) );
		if (is_multisite()){
			global $wpdb;
			$table = $wpdb->base_prefix . 'blogs';
			$data = $wpdb->get_results("SELECT blog_id FROM $table;");
			if ($data){
				foreach ($data as $object){
					if (!empty($object->blog_id) && $object->blog_id>1){
						$prefix = $wpdb->base_prefix . $object->blog_id . '_' ;
						$table = $prefix . 'options';
						$option = $prefix . 'user_roles';
						$temp_data = $wpdb->get_row("SELECT option_value FROM $table WHERE option_name='$option';");
						if ($temp_data && !empty($temp_data->option_value)){
							$array_unserialize = unserialize($temp_data->option_value);
							if (empty($array_unserialize['pending_user'])){
								$array_unserialize['pending_user'] = array(
																			'name' => 'Pending', 
																			'capabilities' => array(
																										'read' => FALSE,
																										'level_0' => 1,
																			)
								);
								$array_serialize = serialize($array_unserialize);
								$wpdb->query("UPDATE $table SET option_value='$array_serialize' WHERE option_name='$option'; ");					
							}
						}
					}
				}	
			}
		}		 		 
	}
	
	public static function default_settings_groups(){
		/*
		 * @param none
		 * @return array
		 */
		return array(	
						'payment', 
		 				'payment_paypal', 
		 				'payment_stripe', 
		 				'payment_authorize',
						'payment_twocheckout', 
						'payment_bank_transfer', 
						'payment_braintree', 
						'payment_payza', 
						'login', 
						'login-messages', 
						'general-defaults',
						'general-captcha', 
						'general-subscription', 
						'general-msg', 
						'register', 
						'register-msg',
						'register-custom-fields', 
						'opt_in', 
						'notifications', 
						'extra_settings', 
						'account_page',
						'fb',
						'tw',
						'in',
						'tbr',
						'ig',
						'vk',
						'goo',
						'social_media', 
						'double_email_verification', 
						'licensing',
						'ihc_woo', 
						'ihc_bp',
						'admin_workflow',
						'public_workflow',
						'affiliate_options',
						'listing_users_inside_page',
						'listing_users',
						'ihc_taxes_settings',
		);
	}

	
	public static function save_settings_into_db(){
		/*
		 * @param none
		 * @return none
		 */
		//save the metas to db
		$values = self::default_settings_groups();
		foreach ($values as $value){
			ihc_return_meta_arr($value);
		}
	}	
	
	public static function save_udate_order_meta($order_id=0, $meta_key='', $meta_value=''){
		/*
		 * @param int, string, string
		 * @return boolean
		 */
		 if ($order_id && $meta_key){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders_meta';
			 $exists = $wpdb->get_row("SELECT id FROM $table WHERE order_id=$order_id AND meta_key='$meta_key';");
			 if ($exists && !empty($exists->id)){
			 	/// update
			 	$wpdb->query("UPDATE $table SET meta_value='$meta_value' WHERE order_id=$order_id AND meta_key='$meta_key';");
			 } else {
			 	/// insert
			 	$wpdb->query("INSERT INTO $table VALUES(null, $order_id, '$meta_key', '$meta_value');");
			 }
			 return TRUE;
		 }
		 return FALSE;
	}
	
	public static function delete_order($order_id=0){
		/*
		 * @param int
		 * @return none
		 */
		 if ($order_id){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders';
			 $wpdb->query("DELETE FROM $table WHERE id=$order_id;");
			 $table = $wpdb->prefix . 'ihc_orders_meta';
			 $wpdb->query("DELETE FROM $table WHERE order_id=$order_id;");
		 }
	}
	
	public static function delete_order_meta($order_id=0, $meta_key=''){
		/*
		 * @param int, string
		 * @return none
		 */
		 if ($order_id && $meta_key){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders_meta';
			 $wpdb->query("DELETE FROM $table WHERE order_id=$order_id AND meta_key='$meta_key';");
		 }		 
	}
		
	public static function get_order_meta($order_id=0, $meta_key=''){
		/*
		 * @param int, string
		 * @return string
		 */
		 if ($order_id && $meta_key){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders_meta';
			 $data = $wpdb->get_row("SELECT meta_value FROM $table WHERE order_id=$order_id AND meta_key='$meta_key';");
			 if ($data && isset($data->meta_value)){
			 	return $data->meta_value;
			 }
		 }
		 return '';
	}

	public static function get_order_id_by_meta_value_and_meta_type($meta_key='', $meta_value=''){
		/*
		 * @param string, string
		 * @return int
		 */
		 if ($meta_key && $meta_value){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders_meta';
			 $data = $wpdb->get_row("SELECT order_id FROM $table WHERE meta_key='$meta_key' AND meta_value='$meta_value' ;");
			 if ($data && isset($data->order_id)){
			 	return $data->order_id;
			 }
		 }
		 return 0;
	}
	
	public static function get_all_order_metas($order_id=0){
		/*
		 * @param int
		 * @return array
		 */
		 $array = array();
		 if ($order_id){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders_meta';
			 $data = $wpdb->get_results("SELECT meta_key, meta_value FROM $table WHERE order_id=$order_id;");
			 if ($data){
			 	foreach ($data as $object){
			 		$array[$object->meta_key] = $object->meta_value;	
				}
			 }		 	
		 }
		 return $array;
	}
	
	public static function get_all_order($limit=30, $offset=0, $uid=0){
		/*
		 * @param none
		 * @return array
		 */
		 global $wpdb;
		 $array = array();
		 $table = $wpdb->prefix . 'ihc_orders';
		 $q = "SELECT * FROM $table";
		 $q .= " WHERE 1=1";
		 if ($uid){
		 	$q .= " AND uid=$uid";
		 }
		 $q .= " ORDER BY create_date DESC LIMIT $limit OFFSET $offset;";
		 $data = $wpdb->get_results($q);
		 if ($data){
		 	foreach ($data as $object){
		 		$temp = (array)$object;
				$temp['metas'] = self::get_all_order_metas($temp['id']);
				$temp['user'] = self::get_username_by_wpuid($temp['uid']);
				$temp['transaction_id'] = (empty($temp['metas']) || empty($data['metas']['transaction_id'])) ? self::get_transaction_id_by_order_id($temp['id']) : $temp['metas']['transaction_id'];
				if (empty($temp['user'])){
					$temp['user'] = '-';
				}
				///payment type
				if (empty($temp['metas']['ihc_payment_type'])){
					$temp['metas']['ihc_payment_type'] = self::get_payment_type_by_transaction_id($temp['transaction_id']);
				}
				$temp['level'] = self::get_level_name_by_lid($temp['lid']);
		 		$array[] = $temp;
		 	}
		 }
		 return $array;
	}
	
	public static function get_payment_type_by_transaction_id($id=0){
		/*
		 * @param int
		 * @return string
		 */
		 if ($id){
		 	 global $wpdb;
		 	 $table = $wpdb->prefix . 'indeed_members_payments';
			 $q = $wpdb->prepare("SELECT * FROM $table WHERE id=%s", $id);
			 $data = $wpdb->get_row($q);
			 if ($data && !empty($data->payment_data)){
				$temp = json_decode($data->payment_data, TRUE);
				return (empty($temp['ihc_payment_type'])) ? '' : $temp['ihc_payment_type'];			 	
			 }			 
		 }
		 return '';
	}
	
	public static function get_count_orders($uid=0){
		/*
		 * @param none
		 * @return int
		 */
		 global $wpdb;
		 $table = $wpdb->prefix . 'ihc_orders';
		 $q = "SELECT COUNT(*) as num FROM $table";
		 $q .= " WHERE 1=1";
		 if ($uid){
		 	$q .= " AND uid=$uid ";
		 }
		 $data = $wpdb->get_row($q);
		 return (empty($data->num)) ? 0 : $data->num;	 
	}
	
	public static function get_username_by_wpuid($wpuid=0){
		/*
		 * @param int
		 * @return string
		 */
		if ($wpuid){
			global $wpdb;
			$table = $wpdb->base_prefix . 'users';
			$data = $wpdb->get_row("SELECT user_login FROM $table WHERE ID='$wpuid'");
			if (!empty($data->user_login)){
				return $data->user_login;
			}
		}
		return '';
	}	
	
	public static function get_level_name_by_lid($lid=0){
		/*
		 * @param int
		 * @return string
		 */
		if ($lid){
			$levels = get_option('ihc_levels');
			if (!empty($levels[$lid]) && !empty($levels[$lid]['label'])){
				return $levels[$lid]['label'];
			}
		}
		return '';
	}
	
	public static function get_transaction_id_by_order_id($order_id=0){
		/*
		 * @param int
		 * @return int
		 */
		if ($order_id){
			global $wpdb;
			$p = $wpdb->prefix . 'indeed_members_payments';
			$o = $wpdb->prefix . 'ihc_orders';
			$data = $wpdb->get_results("SELECT p.orders as orders, p.id as id FROM $p p INNER JOIN $o o ON p.u_id=o.uid WHERE o.id=$order_id");
			if ($data){
				foreach ($data as $object){
					if (isset($object->orders)){
						$temp_data = unserialize($object->orders);
						if ($temp_data && in_array($order_id, $temp_data)){
							return $object->id;
						}
					}	
				}
			}
		}
		return 0;		 
	}
	
	public static function get_order_data_by_id($order_id=0){
		/*
		 * @param none
		 * @return array
		 */
		 $array = array();
		 if ($order_id){
			 global $wpdb;			 
			 $table = $wpdb->prefix . 'ihc_orders';
			 $data = $wpdb->get_row("SELECT * FROM $table WHERE id=$order_id;");
			 if ($data){
			 	$array = (array)$data;
				$array['metas'] = self::get_all_order_metas($array['id']);
				$array['user'] = self::get_username_by_wpuid($array['uid']);
				$array['transaction_id'] = (empty($array['metas']) || empty($array['metas']['transaction_id'])) ? self::get_transaction_id_by_order_id($array['id']) : $array['metas']['transaction_id'];
				if (empty($array['user'])){
					$array['user'] = '-';
				}
				$array['level'] = self::get_level_name_by_lid($array['lid']);
			 }		 	
		 }
		 return $array;
	}	
	
	
	/// TAXES
	public static function save_tax($post_data=array()){
		/*
		 * @param array
		 * @return boolean
		 */
		 if ($post_data){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_taxes';
			 if (empty($post_data['id'])){
			 	//insert
			 	$data = $wpdb->get_row("SELECT * FROM $table WHERE country_code='" . $post_data['country_code'] . "' AND label='" . $post_data['label'] . "' AND state_code='" . $post_data['state_code'] . "' ");
			 	if (empty($data)){
				 	$wpdb->query("INSERT INTO $table 
				 						VALUES(null, 
				 								'" . $post_data['country_code'] . "', 
				 								'" . $post_data['state_code'] . "',
				 								'" . $post_data['amount_value'] . "',
				 								'" . $post_data['label'] . "',
				 								'" . $post_data['description'] . "',
				 								'" . $post_data['status'] . "' );"
					);
					return TRUE;			 		
			 	} else {
			 		return FALSE;
			 	}
			 } else {
			 	//update
			 	$data = $wpdb->get_row("SELECT id FROM $table WHERE country_code='" . $post_data['country_code'] . "' AND label='" . $post_data['label'] . "' ");
			 	if (isset($data) && isset($data->id) && $data->id!=$post_data['id']){
			 		return FALSE;
			 	}
				$wpdb->query("UPDATE $table SET 
				 								country_code='" . $post_data['country_code'] . "', 
				 								state_code='" . $post_data['state_code'] . "',
				 								amount_value='" . $post_data['amount_value'] . "',
				 								label='" . $post_data['label'] . "',
				 								description='" . $post_data['description'] . "',
				 								status='" . $post_data['status'] . "'	
				 						WHERE id='" . $post_data['id'] . "'		
				");
				return TRUE;
			 }
		 }
		 return FALSE;
	}
	
	public static function get_tax($id=0){
		/*
		 * @param int
		 * @return array
		 */
		 if (empty($id)){
		 	return array(
							'id' => 0,
							'country_code' => '',
							'state_code' => '',
							'amount_value' => '',
							'label' => '',
							'description' => '',
							'status' => 1,
			);
		 } else {
		 	global $wpdb;
			$table = $wpdb->prefix . 'ihc_taxes';
			$data = $wpdb->get_row("SELECT * FROM $table WHERE id=$id;");
			if ($data){
				return (array)$data;
			}
		 }
	}
	
	public static function get_all_taxes(){
		/*
		 * @param none
		 * @return array
		 */
		$array = array(); 
		global $wpdb;
		$table = $wpdb->prefix . 'ihc_taxes';
		$data = $wpdb->get_results("SELECT * FROM $table;");	
		if ($data){
			foreach ($data as $object){
				$array[] = (array)$object;
			}
		}
		return $array; 
	}
	
	public static function delete_tax($id=0){
		/*
		 * @param int
		 * @return none
		 */
		 if ($id){
			global $wpdb;
			$table = $wpdb->prefix . 'ihc_taxes';
			$wpdb->query("DELETE FROM $table WHERE id=$id;");			 	
		 }
	}
	
	public static function get_taxes_by_country($country='', $state=''){
		/*
		 * @param string, string
		 * @return array
		 */
		$array = array();  
		global $wpdb;
		$table = $wpdb->prefix . 'ihc_taxes';
		$q = "SELECT * FROM $table WHERE country_code='$country'";
		if ($state){
			$q .= " AND state_code='$state' ";
			$data = $wpdb->get_results($q);
			if (empty($data)){
				$q = "SELECT * FROM $table WHERE country_code='$country' AND state_code='' ";	
				$data = $wpdb->get_results($q);	
			}		
		} else {
			$q .= " AND state_code='' ";
			$data = $wpdb->get_results($q);
		}		

		if ($data){
			foreach ($data as $object){
				$array[] = (array)$object;
			}
		}	
		return $array; 	 
	}
	
	public static function get_taxes_rate_for_user($uid=0){
		/*
		 * @param int (user id)
		 * @return array
		 */
		 if (ihc_is_magic_feat_active('taxes') && $uid){
		 	 global $wpdb;
			 $country = get_user_meta($uid, 'ihc_country', TRUE);
 			 $state = get_user_meta($uid, 'ihc_state', TRUE);
			 $taxes = self::get_taxes_by_country($country, $state);
			 if ($taxes){
			 	/// taxes by country & state
			 	foreach ($taxes as $array){
			 		$return[$array['label']] = $array['amount_value'] . '%';
			 	}
			 } else {
			 	/// default taxes
			 	$taxes_settings = ihc_return_meta_arr('ihc_taxes_settings');
				if (!empty($taxes_settings['ihc_default_tax_label']) && !empty($taxes_settings['ihc_default_tax_value'])){
					$return[$taxes_settings['ihc_default_tax_label']] = $taxes_settings['ihc_default_tax_value'] . '%';
				}
			 }
			 return $return;
		 }
		 return array();
	}
	
	public static function increment_dashboard_notification($type=''){
		/*
		 * @param string ( affiliates || referrals )
		 * @return none
	 	 */
		global $wpdb;
		$table = $wpdb->prefix . 'ihc_dashboard_notifications';
		$wpdb->query("UPDATE $table SET value=value+1 WHERE type='$type';");		
		do_action('ihc_dashboard_notification_action', $type);	 	
	}
	
	public static function reset_dashboard_notification($type=''){
		/*
		 * @param string ( affiliates || referrals )
		 * @return none
		 */
		global $wpdb;
		$table = $wpdb->prefix . 'ihc_dashboard_notifications';
		$wpdb->query("UPDATE $table SET value=0 WHERE type='$type';");	
	}
		
	public static function get_dashboard_notification_value($type=''){
		/*
		 * @param string ( affiliates || referrals )
		 * @return none
		 */
		global $wpdb;
		$table = $wpdb->prefix . 'ihc_dashboard_notifications';
		$data = $wpdb->get_row("SELECT value FROM $table WHERE type='$type';");
		return (empty($data->value)) ? 0 : $data->value;			 	
	}	
	
	public static function save_update_locker_template($post_data=array()){
		/*
		 * @param array
		 * @return none
		 */
		$option_name = 'ihc_lockers';
		$meta_keys = ihc_locker_meta_keys();
		foreach ($meta_keys as $k=>$v){
			if (isset($post_data[$k])){
				$data[$k] = $post_data[$k];
			}
		}
		$data_db = get_option($option_name);
		if ($data_db!==FALSE){
			if (isset($post_data['template_id'])){
				$data_db[$post_data['template_id']] = $data;
			} else {
				end($data_db);
				$key = key($data_db);
				$key++;
				$data_db[$key] = $data;					
			}
			update_option($option_name, $data_db);
		} else {
			$data_db[1] = $data;
			add_option($option_name, $data_db);
		}	
	}
	
	public static function get_user_levels($uid=0, $check_expire=FALSE){
		/*
		 * @param int, bool
		 * @return array
		 */
		 $array = array();
		 if ($uid){
		 	 global $wpdb;
			 $levels = get_option('ihc_levels');
			 $table = $wpdb->prefix . "ihc_user_levels";
			 $data = $wpdb->get_results("SELECT * FROM $table WHERE user_id=$uid");
			 if ($data){
			 	foreach ($data as $object){
			 		$temp = (array)$object;
					$temp['label'] = $levels[$object->level_id]['label'];	
					if (!empty($levels[$object->level_id]['badge_image_url'])){
						$temp['badge_image_url'] = $levels[$object->level_id]['badge_image_url'];
					} else {
						$temp['badge_image_url'] = '';
					}
					if (self::is_user_level_active($uid, $object->level_id)){
						$temp['is_expired'] = FALSE;
					} else {
						$temp['is_expired'] = TRUE;
						if ($check_expire){
							continue;	
						}
					}
					$array[$object->level_id] = $temp;
			 	}
				/*
				if ($check_expire){
					foreach ($array as $id=>$value){
						if (!self::is_user_level_active($uid, $id)){
							unset($array[$id]);
						}
					}
				}
				 */
			 }
		 }
		 return $array;
	}
	
	public static function is_user_level_active($uid=0, $lid=0){
		/*
		 * @param int, int
		 * @return bool
		 */
		global $wpdb;
		$grace_period = get_option('ihc_grace_period');
		$data = $wpdb->get_row('SELECT expire_time, start_time FROM ' . $wpdb->prefix . 'ihc_user_levels WHERE user_id="' . $uid . '" AND level_id="' . $lid . '";');
		$current_time = time();
		if (!empty($data->start_time)){
			$start_time = strtotime($data->start_time);
			if ($current_time<$start_time){
				//it's not available yet
				return FALSE;
			}				
		}	
		if (!empty($data->expire_time)){
			$expire_time = strtotime($data->expire_time) + ((int)$grace_period * 24 * 60 *60);
			if ($current_time>$expire_time){
				//it's expired
				return FALSE;
			}
		}
		return TRUE;	 
	}
	
	public static function user_has_level($uid=0, $lid=0){
		/*
		 * @param int, int
		 * @return boolean
		 */
		 if ($uid && $lid!==FALSE){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_user_levels';
			 $data = $wpdb->get_row("SELECT * FROM $table WHERE user_id='$uid' AND level_id='$lid';");
			 if ($data && isset($data->start_time)){
			 	return TRUE;
			 }
		 }
		 return FALSE;
	}
	
	public static function cheat_off_get_hash($uid=0){
		/*
		 * @param int
		 * @return string
		 */
		 if ($uid){
			 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_cheat_off';		 
		 	 $data = $wpdb->get_row("SELECT hash FROM $table WHERE uid=$uid;");
			 if (!empty($data) && !empty($data->hash)){
			 	return $data->hash;
			 }
		 }
		 return '';
	}
	
	public static function cheat_off_set_hash($uid=0, $hash=''){
		/*
		 * @param int, string
		 * @return boolean
		 */
		 if ($uid && $hash){
			 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_cheat_off';		 
		 	 $data = $wpdb->get_row("SELECT hash FROM $table WHERE uid=$uid;");	
			 if (!empty($data) && !empty($data->hash)){
			 	/// update
			 	return $wpdb->query("UPDATE $table SET hash='$hash' WHERE uid=$uid;");
			 } else {
			 	/// insert
			 	return $wpdb->query("INSERT INTO $table VALUES($uid, '$hash');");			 	
			 }		 	 	
		 }
		 return FALSE;
	}
	
	public static function invitation_code_add_new($data=array()){
		/*
		 * @param array
		 * @return boolean
		 */
		 if ($data){
		 	global $wpdb;
			$table = $wpdb->prefix . 'ihc_invitation_codes';
			if (empty($data['repeat'])){
				$data['repeat'] = 1;
			}
			if (empty($data['how_many_codes'])){
				///single
				if (!empty($data['code'])){
					$data['code'] = ihc_make_string_simple($data['code']);
				 	$check = $wpdb->get_row("SELECT * FROM $table WHERE code='{$data['code']}';");
				 	if ($check && !empty($check->id)){
				 		return FALSE; ///already exists
				 	}					
					$wpdb->query("INSERT INTO $table VALUES(null, '{$data['code']}', '', 0, '{$data['repeat']}', 1);");
					return TRUE;
				}
			} else {
				/// multiple
				$prefix = $data['code_prefix'];
				$length = $data['code_length'] - strlen($data['code_prefix']);
				$limit = $data['how_many_codes'];
				while ($limit){
					$code = ihc_random_str($length);
					$code = $prefix . $code;	
					$code = str_replace(' ', '', $code);
					$code = ihc_make_string_simple($code);
					$check = $wpdb->get_row("SELECT * FROM $table WHERE code='$code';");
					if ($check){
						continue;
					}				
					$wpdb->query("INSERT INTO $table VALUES(null, '$code', '', 0, '{$data['repeat']}', 1);");
					$limit--;				
				}	
				return TRUE;					
			}
		 }
		 return FALSE;
	}
	
	public static function invitation_code_delete($id=0){
		/*
		 * @param int
		 * @return boolean
		 */
		 if (!empty($id)){
		 	global $wpdb;
			$table = $wpdb->prefix . 'ihc_invitation_codes';
			$wpdb->query("DELETE FROM $table WHERE id=$id;");
			return TRUE;		 	
		 }
		 return FALSE;
	}
	
	public static function invitation_code_check($code=''){
		/*
		 * @param string
		 * @return boolean
		 */
		 if (!empty($code)){
		 	global $wpdb;
			$table = $wpdb->prefix . 'ihc_invitation_codes';
			$check = $wpdb->get_row("SELECT * FROM $table WHERE code='$code';");
			if ($check && isset($check->submited) && isset($check->repeat_limit)){
				if ($check->submited<$check->repeat_limit){
					return TRUE;
				}
			}		 	
		 }
		 return FALSE;
	}
	
	public static function invitation_code_increment_submited_value($code=''){
		/*
		 * @param string
		 * @return boolean
		 */
		 if ($code){
		 	global $wpdb;
			$table = $wpdb->prefix . 'ihc_invitation_codes';
			$check = $wpdb->get_row("SELECT submited, repeat_limit FROM $table WHERE code='$code';");
			if ($check && isset($check->submited)){
				$increment_value = $check->submited + 1;
				if ($increment_value<=$check->repeat_limit){
					$wpdb->query("UPDATE $table SET submited=$increment_value WHERE code='$code';");
					return TRUE;					
				}
			}		 	
		 }
		 return FALSE;
	}
	
	public static function invitation_code_get_all(){
		/*
		 * @param none
		 * @return array
		 */
		$array = array();
		global $wpdb;
		$table = $wpdb->prefix . 'ihc_invitation_codes';
		$data = $wpdb->get_results("SELECT * FROM $table;");
		if ($data){
			foreach ($data as $object){
				$array[] = (array)$object;
			}
		} 
		return $array;
	}
	
	public static function invitation_code_does_exist_codes(){
		/*
		 * @param none
		 * @return boolean
		 */
		global $wpdb;
		$table = $wpdb->prefix . 'ihc_invitation_codes';
		$data = $wpdb->get_row("SELECT COUNT(*) as c FROM $table;");
		if ($data && isset($data->c) && $data->c>0){
			return TRUE;
		}
		return FALSE;
	}
	
	public static function download_monitor_get_count_for_user($uid=0, $type='files'){
		/*
		 * @param int, string. uid set as -1 means all registered users
		 * @return int
		 */
		 global $wpdb;
		 $table = $wpdb->base_prefix . 'download_log';

		 if ($type=='files'){
		 	$q = "SELECT COUNT(DISTINCT download_id) as c FROM $table WHERE";
		 } else {
		 	$q = "SELECT COUNT(*) as c FROM $table WHERE";
		 }
		 if ($uid==-1){
		 	/// all registered users
		 	$q .= " user_id<>0;";
		 } else {
		 	$q .= " user_id=$uid;";
		 }		 
		 $data = $wpdb->get_row($q);
		 if ($data && !empty($data->c)){
		 	return (int)$data->c;
		 }
		 return 0;
	}	
	
	public static function get_payment_tyoe_by_userId_levelId($uid=0, $lid=0){
		/*
		 * @param int, int
		 * @return string
		 */
		 $payment_type = '';
		 if ($uid && $lid){
		 	global $wpdb;
		 	$table = $wpdb->prefix . 'indeed_members_payments';
			$data = $wpdb->get_results("SELECT payment_data FROM $table WHERE u_id=$uid ORDER BY paydate DESC;");
			if ($data){
				foreach ($data as $object){
					$array = json_decode($object->payment_data, TRUE);
					
					if (empty($array['level']) && !empty($array['custom'])){
						$temp_paypal_data = json_decode(stripslashes($array['custom']), TRUE);
						$array['level'] = (isset($temp_paypal_data['level_id'])) ? $temp_paypal_data['level_id'] : '';
					}

					if (isset($array['level']) && $array['level']!='' && isset($array['ihc_payment_type'])){
						if ($lid==$array['level']){
							$payment_type = $array['ihc_payment_type'];
							break;			
						}
					} else if (isset($array['custom'])){
						$custom = json_decode($array['custom'], TRUE);
						if ($lid==$custom['level_id']){
							$payment_type = 'paypal';
							break;
						}
					}
				}
			}
		 }
		 return $payment_type;
	}
	
	public static function get_page_slug($post_id=0){
		/*
		 * @param int
		 * @return string
		 */
		 if ($post_id){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'posts';
			 $data = $wpdb->get_row("SELECT post_name FROM $table WHERE ID=$post_id;");
			 if ($data && !empty($data->post_name)){
			 	return $data->post_name;
			 }
		 }
		 return '';
	}
	
	public static function get_users_with_no_individual_page(){
	    /*
	     * @param none
	     * @return array
	     */
	     $array = array();
	     global $wpdb;
	     $table = $wpdb->base_prefix . 'usermeta';
	     $data = $wpdb->get_results("SELECT DISTINCT user_id FROM $table WHERE meta_key='ihc_individual_page';");
	     $not_in_string = '';
	     if ($data){
	         foreach ($data as $object){
	             $not_in[] = $object->user_id;
	         }
	        if ($not_in){
	            $not_in_string = implode(',', $not_in);
	        }
	     }
	     $table = $wpdb->base_prefix . 'users';
	     $q = "SELECT ID FROM $table WHERE 1=1";
	     if (!empty($not_in_string)){
	         $q .= " AND ID NOT IN ($not_in_string) ";
	     }
	     $our_target = $wpdb->get_results($q);
	     if ($our_target){
	         foreach ($our_target as $u){
	             $array[] = $u->ID;
	         }
	     }
	     return $array;
	}
	
	public static function get_excluded_payment_types_for_level_id($level_id=-1){
		/*
		 * @param int
		 * @return string
		 */
		 if ($level_id>-1){
		 	 $data = get_option('ihc_level_restrict_payment_values');
			 if ($data && !empty($data[$level_id])){
			 	return $data[$level_id];
			 }
		 }
		 return '';
	}
	
	public static function get_default_payment_gateway_for_level($lid=-1, $default_payment=''){
		/*
		 * @param int, string
		 * @return string
		 */
		 if ($lid>-1){
		 	 $data = get_option('ihc_levels_default_payments');
			 if ($data && !empty($data[$lid]) && $data[$lid]!=-1){
			 	if (!function_exists('ihc_check_payment_status')){
					require_once IHC_PATH . 'admin/includes/functions.php';
				}
				$check = ihc_check_payment_status($data[$lid]);
				if ($check['status'] && $check['settings']=='Completed'){
					return $data[$lid];
				}
			 }
		 }
		 return $default_payment;		 
	}
	
	public static function does_this_user_bought_something($uid=0){
		/*
		 * @param int
		 * @return boolean
		 */
		 $bool = FALSE;
		 if ($uid){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'indeed_members_payments';
			 $data = $wpdb->get_results("SELECT payment_data FROM $table WHERE u_id=$uid;");
			 if ($data){
			 	foreach ($data as $object){
			 		$temp = json_decode($object->payment_data, TRUE);
					if (!empty($temp['amount'])){
						$bool = TRUE;
						break;
					}
			 	}
			 }
		 }
		 return $bool;
	}
	
	public static function gift_templates_get_metas($id=0){
		/*
		 * @param int
		 * @return array
		 */
		 if (empty($id)){
		 	$array = array(
							'id' => 0,
							"discount_type" => "price",
							"discount_value" => '',
							'target_level' => -1,
							"reccuring" => '',
			);
		 } else {
		 	global $wpdb;
			$table = $wpdb->prefix . 'ihc_gift_templates';
			$data = $wpdb->get_row("SELECT lid, settings FROM $table WHERE id=$id;");
			if ($data && isset($data->lid) && isset($data->settings)){
				$array = unserialize($data->settings);
				$array['lid'] = $data->lid;
			}
		 }
		 return $array;
	}
	
	public static function gifts_do_save($data=array()){
		/*
		 * @param array
		 * @return boolean
		 */
		 if ($data){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_gift_templates';
			 if (empty($data['id'])){
			 	///insert
			 	$settings = array(
									'discount_type' => $data['discount_type'],
									"discount_value" => $data['discount_value'],
									'target_level' => $data['target_level'],
									"reccuring" => $data['reccuring'],
				);
				$settings = serialize($settings);
			 	$wpdb->query("INSERT INTO $table VALUES(null, '{$data['lid']}', '$settings', 1);");
			 } else {
			 	///update
			 	$settings = array(
									'discount_type' => $data['discount_type'],
									"discount_value" => $data['discount_value'],
									'target_level' => $data['target_level'],
									"reccuring" => $data['reccuring'],
				);
				$settings = serialize($settings);
			 	$wpdb->query("UPDATE $table SET lid='{$data['lid']}', settings='$settings' WHERE id='{$data['id']}';");			 	
			 }
			  	
		 }
		 return FALSE;
	}

	public static function gift_get_all_items($a_lid=''){
		/*
		 * @param int (aworded level id)
		 * @return array
		 */
		 global $wpdb;
		 $array = array();
		 $table = $wpdb->prefix . 'ihc_gift_templates';
		 $q = "SELECT * FROM $table";
		 if ($a_lid!=''){
		 	$q .= " WHERE lid=$a_lid OR lid=-1;";
		 }
		 $data = $wpdb->get_results($q);
		 if ($data){
		 	foreach ($data as $object){
		 		$temp = unserialize($object->settings);
				$item = $temp;
				$item['lid'] = $object->lid;
				$array[$object->id] = $item;
		 	}
		 }
		 return $array;
	}
	
	public static function gifts_do_delete($id=0){
		/*
		 * @param int
		 * @return none
		 */
		 if ($id){
			 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_gift_templates';
			 $wpdb->query("DELETE FROM $table WHERE id=$id;");
		 }
	}

	public static function get_gifts_by_uid($uid=0){
		/*
		 * @param int (user id)
		 * @return array
		 */
		 $array = array();
		 if ($uid){
		 	 $gifts = get_user_meta($uid, 'ihc_gifts', TRUE);
			 if ($gifts){
			 	 foreach ($gifts as $arr){
					 $temp = ihc_get_coupon_by_code($arr['code']);					 
					 $temp['is_active'] = self::is_gift_stil_active($arr['code']);
					 $array[] = $temp;
			 	 }
			 }
		 }
		 return $array;
	}
	
	public static function is_gift_stil_active($code=''){
		/*
		 * @param string
		 * @return bool
		 */
		 if ($code){
			 $coupon_data = ihc_get_coupon_by_code($code);
			 if ($coupon_data){
			 	if ($coupon_data['submited_coupons_count']<1){
			 		return TRUE;
			 	}
			 }
		 }
		 return FALSE;
	}
	
	public static function get_all_gift_codes($limit=30, $offset=0){
		/*
		 * @param int
		 * @return array
		 */
		 $array = array();
		 global $wpdb;
		 $table = $wpdb->prefix . 'ihc_coupons';
		 $data = $wpdb->get_results("SELECT * FROM $table WHERE status=2 ORDER BY id DESC LIMIT $limit OFFSET $offset ");
		 if ($data){
		 	foreach ($data as $object){
		 		$temp = unserialize($object->settings);
		 		$temp['username'] = self::get_username_by_wpuid(@$temp['uid']);
				$temp['code'] = $object->code;
				$temp['is_active'] = self::is_gift_stil_active($object->code);
				$array[$object->id] = $temp;
		 	}
		 }
		 return $array;
	}
	
	public static function get_count_all_gift_codes(){
		/*
		 * @param none
		 * @return int
		 */
		 global $wpdb;
		 $table = $wpdb->prefix . 'ihc_coupons';
		 $data = $wpdb->get_row("SELECT COUNT(*) as c FROM $table WHERE status=2;");
		 if ($data && isset($data->c)){
		 	return $data->c;
		 }		 
		 return 0;
	}
		
	public static function do_delete_generated_gift_code($coupon_id=0){
		/*
		 * @param int
		 * @return none
		 */
		 if ($coupon_id){
		 	 $metas = ihc_get_coupon_by_id($coupon_id);
			 if (isset($metas['uid']) && isset($metas['code'])){
			 	 $code = $metas['code'];
			 	 $meta_user = get_user_meta($metas['uid'], 'ihc_gifts', TRUE);
				 $key = ihc_array_value_exists($meta_user, $code, 'code');  
				 if ($key!==FALSE){
				 	 unset($meta_user[$key]);
					 update_user_meta($metas['uid'], 'ihc_gifts', $meta_user);
				 }
			 }
		 	 ihc_delete_coupon($coupon_id);
		 }
	}

	public static function is_order_id_for_uid($uid=0, $order_id=0){
		/*
		 * check if a order belong to a user
		 * @param int, int
		 * @return boolean
		 */
		 if ($uid && $order_id){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders';
			 $check = $wpdb->get_row("SELECT * FROM $table WHERE uid=$uid AND id=$order_id;");
			 if ($check && !empty($check->id)){
			 	return TRUE;
			 }
		 }
		 return FALSE;
	}
	
	public static function get_uid_by_order_id($order_id=0){
		/*
		 * @param int
		 * @return int
		 */
		 if ($order_id){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders';
			 $check = $wpdb->get_row("SELECT uid FROM $table WHERE id=$order_id;");
			 if ($check && !empty($check->uid)){
			 	return $check->uid;
			 }
		 }
		 return 0;		 
	}
	
	public static function transactions_get_total_for_user($uid=0){
		/*
		 * @param int
		 * @return int
		 */
		 if ($uid){
		 	 global $wpdb;
			 $table = $wpdb->prefix . "indeed_members_payments";
			 $data = $wpdb->get_row("SELECT COUNT(*) as c FROM $table WHERE u_id=$uid;");
			 if ($data && !empty($data->c)){
			 	return $data->c;
			 }
		 }
		 return 0;
	}
	
	public static function transaction_get_items_for_user($limit=999, $offset=0, $uid=0){
		/*
		 * @param int, int, int
		 * @return array
		 */
		 if ($uid){
		 	 global $wpdb;
		 	 $table = $wpdb->prefix . "indeed_members_payments";
			 $q = "SELECT * FROM $table";
			 $q .= " WHERE 1=1";
			 $q .= " AND u_id=$uid";
			 $q .= " ORDER BY paydate DESC LIMIT $limit OFFSET $offset;";	
			 $data = $wpdb->get_results($q);
			 if (!empty($data)){
				 return $data;	 				 	
			 }
		 }
		 return array();
	}
	
	public static function user_get_register_date($uid=0){
		/*
		 * @param int
		 * @return string
		 */
		 if ($uid){
		 	 global $wpdb;
			 $table = $wpdb->base_prefix . 'users';
			 $data = $wpdb->get_row("SELECT user_registered FROM $table WHERE ID=$uid;");
			 if ($data && !empty($data->user_registered)){
			 	return $data->user_registered;
			 }
		 }
		 return '';
	}
	
	public static function user_get_email($uid=0){
		/*
		 * @param int
		 * @return string
		 */
		 if ($uid){
		 	 global $wpdb;
			 $table = $wpdb->base_prefix . 'users';
			 $data = $wpdb->get_row("SELECT user_email FROM $table WHERE ID=$uid;");
			 if ($data && !empty($data->user_email)){
			 	return $data->user_email;
			 }
		 }
		 return '';		
	}

	public static function update_order_status($order_id=0, $new_status=''){
		/*
		 * @param int, string
		 * @return boolean
		 */
		 if ($order_id){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'ihc_orders';
			 $check = $wpdb->get_row("SELECT * FROM $table WHERE id=$order_id;");
			 if ($check && !empty($check->id)){
			 	return $wpdb->query("UPDATE $table SET status='$new_status' WHERE id=$order_id;");
			 }
		 }
	}
	
	public static function update_transaction_status($txn_id='', $new_status=''){
		/*
		 * @param int, string
		 * @return boolean 
		 */
		 if ($txn_id){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'indeed_members_payments';
			 $check = $wpdb->get_row("SELECT payment_data FROM $table WHERE txn_id='$txn_id';");
			 if ($check && !empty($check->payment_data)){
			 	$data = json_decode($check->payment_data, TRUE);
				$data['message'] = $new_status;
				$json = json_encode($data); 
			 	return $wpdb->query("UPDATE $table SET payment_data='$json' WHERE txn_id='$txn_id';");
			 }		 	 
		 }
	}
	
	public static function get_woo_product_id_for_lid($lid=0){
		/*
		 * @param int
		 * @return int
		 */
		 if ($lid!==FALSE){
		 	 global $wpdb;
			 $table = $wpdb->prefix . 'postmeta';
			 $data = $wpdb->get_row("SELECT post_id FROM $table WHERE meta_key='iump_woo_product_level_relation' AND meta_value='$lid';");
			 if ($data && isset($data->post_id)){
			 	return $data->post_id;
			 }
		 }
		 return 0;
	}
	
	public static function get_woo_product_level_relations(){
		/*
		 * @param none
		 * @return array
		 */
		 $array = array();
		 global $wpdb;
		 $table = $wpdb->prefix . 'postmeta';
		 $data = $wpdb->get_results("SELECT meta_value, post_id FROM $table WHERE meta_key='iump_woo_product_level_relation' AND meta_value!='' AND meta_value!='-1';");
		 if ($data){
		 	foreach ($data as $object){
		 		$temp['level_label'] = self::get_level_name_by_lid($object->meta_value);
				$temp['product_label'] = get_the_title($object->post_id);
				$temp['level_id'] = $object->meta_value;
				$temp['product_id'] = $object->post_id;
				$array[] = $temp;
		 	}
		 }
		 return $array;
	}

	public static function search_woo_products($search=''){
		/*
		 * @param string
		 * @return array
		 */
		$arr = array();
		if ($search){
			global $wpdb;
			$table = $wpdb->prefix . 'posts';
			$data = $wpdb->get_results("SELECT post_title, ID
											FROM $table
											WHERE
											post_title LIKE '%$search%'
											AND post_type='product'
											AND post_status='publish'
			");
			if ($data){
				foreach ($data as $object){
					$arr[$object->ID] = $object->post_title;
				}
			}
		}
		return $arr;
	}
	
	public static function unsign_woo_product_level_relation($lid=-1){
		/*
		 * @param int
		 * @return boolean
		 */
		 if ($lid>-1){
		 	 $product_id = self::get_woo_product_id_for_lid($lid);
			 if ($product_id){
			 	 update_post_meta($product_id, 'iump_woo_product_level_relation', '');
				 return TRUE;
			 }
		 }
		 return FALSE;
	}
		
}	
	
endif;


