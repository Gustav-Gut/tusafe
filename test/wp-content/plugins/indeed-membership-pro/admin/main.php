<?php 
/********************************* ADMIN SECTION **************************/

add_action('init', 'ihc_admin_run_plugin_updates');
function ihc_admin_run_plugin_updates(){
	/*
	 * Put here the updates from one version to another
	 * @param none
	 * @return none
	 */
	//==================== DB 
	Ihc_Db::create_tables();	
	Ihc_Db::update_tables_structure();

	//==================== CRON JOBS
	if (!wp_get_schedule( 'ihc_notifications_job')){
		wp_schedule_event(time(), 'daily', 'ihc_notifications_job');
	}
	if (!wp_get_schedule( 'ihc_check_level_downgrade')){
		wp_schedule_event(time(), 'hourly', 'ihc_check_level_downgrade');// twice daily
	}
	if (!wp_get_schedule( 'ihc_check_verify_email_status')){
		wp_schedule_event(time(), 'daily', 'ihc_check_verify_email_status');
	}		
	
	
	//==================== Register Fields
	if (get_option('ihc_update_version10')===FALSE){
		
		/// REGISTER FIELDS
		$data = get_option('ihc_user_fields');		
		if ($data){
			require_once IHC_PATH . 'admin/includes/functions/register.php';
			//////////////// AVATAR
			if (ihc_array_value_exists($data, 'ihc_avatar', 'name')===FALSE){
				$field_data = array('name'=>'ihc_avatar', 'type'=>'upload_image', 'label'=>'Avatar');
				ihc_save_user_field($field_data);
			}
			//////////////// COUPON
			if (ihc_array_value_exists($data, 'ihc_coupon', 'name')===FALSE){
				$field_data = array('name'=>'ihc_coupon', 'type'=>'text', 'label'=>'Coupon');
				ihc_save_user_field($field_data);
			}
			//////////////// SELECT PAYMENT
			if (ihc_array_value_exists($data, 'payment_select', 'name')===FALSE){
				$field_data = array('name'=>'payment_select', 'type'=>'payment_select', 'label'=>'Select Payment', 'theme'=>'ihc-select-payment-theme-1');
				ihc_save_user_field($field_data);
			}
			//////////////// SOCIAL MEDIA
			if (ihc_array_value_exists($data, 'ihc_social_media', 'name')===FALSE){
				$field_data = array('name'=>'ihc_social_media', 'type'=>'social_media', 'label'=>'-');
				ihc_save_user_field($field_data);
			}
			//////// IHC_COUNTRY
			if (ihc_array_value_exists($data, 'ihc_country', 'name')===FALSE){
				$field_data = array('name'=>'ihc_country', 'type'=>'ihc_country', 'label'=>'Country');
				ihc_save_user_field($field_data);
			}				
			//////// ihc_invitation_code_field
			if (ihc_array_value_exists($data, 'ihc_invitation_code_field', 'name')===FALSE){
				$field_data = array('display_admin'=>0, 'display_public_reg'=>1, 'display_public_ap'=>0, 'name'=>'ihc_invitation_code_field', 'label'=>'Invitation Code', 'type'=>'ihc_invitation_code_field', 'native_wp' => 0, 'req' => 2, 'sublevel' => '');
				ihc_save_user_field($field_data);
			}				
			//////// IHC_STATE
			if (ihc_array_value_exists($data, 'ihc_state', 'name')===FALSE){
				$field_data = array('name'=>'ihc_state', 'type'=>'ihc_state', 'label'=>'State');
				ihc_save_user_field($field_data);
			}	
			
			/*
			//// IHC_DONATION		
			$data = get_option('ihc_user_fields');
			if (ihc_array_value_exists($data, 'ihc_donation', 'name')===FALSE){
				$field_data = array('name' => 'ihc_donation', 'type' => 'ihc_donation', 'label' => 'Donation');
				ihc_save_user_field($field_data);
			}
			*/	
			///////////// PASSWORD FIELD UPDATE
			$register_arr = get_option('ihc_user_fields');
			$key = ihc_array_value_exists($register_arr, 'pass1', 'name');
			$update_arr = $register_arr[$key];
			$update_arr['id'] = $key;
			if ($update_arr['display_admin']==2){
				$update_arr['display_admin'] = 1;
			}
			if ($update_arr['display_public_ap']==2){
				$update_arr['display_public_ap'] = 1;
			}
			ihc_update_register_fields($update_arr);
			
			$data = get_option('ihc_user_fields');			
			foreach ($data as $k => $v){
				$new_data[$k] = $v;
				if (isset($new_data[$k]['display'])){
					$new_data[$k]['display_admin'] = $new_data[$k]['display'];
					$new_data[$k]['display_public_reg'] = $new_data[$k]['display'];
					$new_data[$k]['display_public_ap'] = $new_data[$k]['display'];
					unset($new_data[$k]['display']);
				}
				if (empty($new_data[$k]['sublabel'])){
					$new_data[$k]['sublabel'] = '';
				}				
			}
			update_option('ihc_user_fields', $new_data);
		}
		
		/// NOTIFICATIONS
		Ihc_Db::create_notifications();	
			
		//UPDATE STRIPE TRANSACTIONS
		ihc_update_stripe_subscriptions();		
		update_option('ihc_update_version10', 1);//ihc_update_version
	}
}

add_action('init', 'ihc_add_bttn_func');
function ihc_add_bttn_func(){
	/*
	 * add the locker and shortcodes buttons for wp editor
	 * prevent indeed users to view them
	 * @param none
	 * @return none
	 */
	if (defined('DOING_AJAX') && DOING_AJAX) {
		return;
	}
	if (is_user_logged_in()){
		$uid = get_current_user_id();
		$role = '';
		$user = new WP_User( $uid );
		if ($user && !empty($user->roles) && !empty($user->roles[0]) && $user->roles[0]!='administrator'){
			$allowed_roles = get_option('ihc_dashboard_allowed_roles');
			if ($allowed_roles){
				$roles = explode(',', $allowed_roles);
				if ($roles && is_array($roles) && !in_array($user->roles[0], $roles)){
					wp_redirect(home_url());
					exit();					
				}
			} else {
					wp_redirect(home_url());
					exit();	
			}
			
		}

	    if (!current_user_can('edit_posts') || !current_user_can('edit_pages')){
	    	return;
	    }
	    if (get_user_option('rich_editing') == 'true') {
	    	/// add the buttons
	    	add_filter( 'mce_buttons', 'ihc_register_button' );
	    	add_filter( "mce_external_plugins", "ihc_js_bttns_return" );
	    }	    
	}
}

function ihc_register_button( $arr ) {
	array_push( $arr, 'ihc_button_locker' );
	array_push( $arr, 'ihc_button_forms' );
	return $arr;
}

function ihc_js_bttns_return( $arr ) {
	$arr['ihc_button_forms'] =  IHC_URL . 'admin/assets/js/ihc_buttons.js';
	$arr['ihc_button_locker'] =  IHC_URL . 'admin/assets/js/ihc_buttons.js';	
	return $arr;
}

/////////////// SETTINGS META BOX
add_action( 'add_meta_boxes', 'ihc_meta_boxes_settings');
function ihc_meta_boxes_settings(){
	include_once IHC_PATH . 'admin/includes/functions.php';
	$arr = ihc_get_post_types_be();
	$arr[] = 'post';
	$arr[] = 'page'; 
	foreach($arr as $v){
		add_meta_box(   'ihc_show_for',//id
						'Ultimate Membership Pro - Locker',
						'ihc_meta_box_settings_html',//function name
						$v,
						'side',
						'high' 
					);		
	}
}

////REPLACE CONTENT METABOX
add_action( 'add_meta_boxes', 'ihc_replace_content_meta_box' );
function ihc_replace_content_meta_box(){
	$arr = ihc_get_post_types_be();
	$arr[] = 'post';
	$arr[] = 'page';
	foreach($arr as $v){
		add_meta_box(   'ihc_replace_content',//id
						'Ultimate Membership Pro - Replace Content',
						'ihc_meta_box_replace_content_html',//function name
						$v,
						'normal',
						'high' 
					);
	}
}

////SET DEFAULT PAGES META BOX
add_action( 'add_meta_boxes', 'ihc_set_default_pages_meta_box' );
function ihc_set_default_pages_meta_box(){
	global $post;
	$set_arr = ihc_get_default_pages_il(true);
	//if ( ( $set_arr && count($set_arr) && in_array($post->ID, $set_arr) ) || ihc_get_default_pages_il()){
		add_meta_box(
				'ihc_default_pages_content',//id
				'Membership Pro - Page Type',
				'ihc_meta_box_default_pages_html',//function name
				'page',
				'side',
				'high'
		);
	//}	
}

////DRIP CONTENT SETTINGS
add_action( 'add_meta_boxes', 'ihc_drip_content_meta_box' );
function ihc_drip_content_meta_box(){
	$arr = ihc_get_post_types_be();
	$arr[] = 'post';
	$arr[] = 'page';
	foreach ($arr as $v){
		add_meta_box(   'ihc_drip_content',//id
				'Membership Pro - Drip Content',
				'ihc_drip_content_return_meta_box',//function name
				$v,
				'side',
				'high'
		);
	}
}

/////save/update custom metabox values
add_action('save_post', 'ihc_save_post_meta', 10, 1 );//save ihc_meta_box_settings_html values
function ihc_save_post_meta($post_id){
	$meta_arr = ihc_post_metas($post_id, true);
	foreach($meta_arr as $k=>$v){
		if(isset($_REQUEST[$k])){
			update_post_meta($post_id, $k, $_REQUEST[$k]);
		}		
	}

	//default pages
	if(isset($_REQUEST['ihc_set_page_as_default_something']) && $_REQUEST['ihc_set_page_as_default_something']!=-1 && isset($_REQUEST['ihc_post_id'])){
		$meta_name = $_REQUEST['ihc_set_page_as_default_something'];
		
		//EXTRA CHECK - REWRITE RULE FOR Visitor Inside User Page
		if ($meta_name=='ihc_general_register_view_user'){
			ihc_save_rewrite_rule_for_register_view_page($_REQUEST['ihc_post_id']);
		}
		
		if(get_option($meta_name)!==FALSE){
			update_option($meta_name, $_REQUEST['ihc_post_id']);
		}else{
			add_option($meta_name, $_REQUEST['ihc_post_id']);
		}
	}
}

///dashboard menu
add_action ( 'admin_menu', 'ihc_menu', 81 );
function ihc_menu() {
	add_menu_page ( 'Ultimate Membership Pro', 'Membership Pro Ultimate WP', 'manage_options',	'ihc_manage', 'ihc_manage', 'dashicons-universal-access-alt' );
}

$ext_menu = 'ihc_manage';		
include_once plugin_dir_path(__FILE__) . 'extensions_plus/index.php';



function ihc_manage(){
	include_once IHC_PATH . 'admin/includes/functions.php';
	require_once IHC_PATH . 'admin/includes/manage-page.php';
}

add_action("admin_enqueue_scripts", 'ihc_head');
function ihc_head(){
	wp_enqueue_style( 'ihc_admin_style', IHC_URL . 'admin/assets/css/style.css' );
	wp_enqueue_style( 'ihc_public_style', IHC_URL . 'assets/css/style.css' );
	wp_enqueue_style( 'ihc-font-awesome', IHC_URL . 'assets/css/font-awesome.css' );
	wp_enqueue_style( 'ihc_templates_style', IHC_URL . 'assets/css/templates.css' );
	wp_enqueue_style( 'ihc_select2_style', IHC_URL . 'assets/css/select2.min.css' );	
	wp_enqueue_media();
	wp_register_script( 'ihc-back_end', IHC_URL . 'admin/assets/js/back_end.js', array(), null );
	wp_localize_script( 'ihc-back_end', 'ihc_site_url', get_site_url() );
	//wp_enqueue_style( 'ihc_front_end_style', IHC_URL . 'assets/css/style.css' );
	wp_enqueue_style( 'ihc_jquery-ui.min.css', IHC_URL . 'admin/assets/css/jquery-ui.min.css');
	wp_enqueue_script('jquery-ui-datepicker');
	
	wp_enqueue_style( 'ihc_bootstrap-slider', IHC_URL . 'admin/assets/css/bootstrap-slider.css' );
	wp_enqueue_script( 'ihc-bootstrap-slider', IHC_URL . 'admin/assets/js/bootstrap-slider.js' );
	
	if (isset($_REQUEST['page']) && $_REQUEST['page']=='ihc_manage'){	
		wp_enqueue_style( 'ihc_bootstrap', IHC_URL . 'admin/assets/css/bootstrap.css' );
		wp_enqueue_style( 'ihc_bootstrap-res[', IHC_URL . 'admin/assets/css/bootstrap-responsive.min.css' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-sortable' );	
		wp_enqueue_script('jquery-ui-autocomplete');	
		wp_enqueue_script( 'ihc-flot', IHC_URL . 'admin/assets/js/jquery.flot.js' );
		wp_enqueue_script( 'ihc-flot-pie', IHC_URL . 'admin/assets/js/jquery.flot.pie.js' );
		wp_enqueue_script( 'ihc-jquery_form_module', IHC_URL . 'assets/js/jquery.form.js', array(), null );
		wp_enqueue_script( 'ihc-jquery_upload_file', IHC_URL . 'assets/js/jquery.uploadfile.min.js', array(), null );	
		wp_enqueue_script( 'ihc-front_end_js', IHC_URL . 'assets/js/functions.js', array(), null );
		wp_enqueue_script( 'ihc-select2', IHC_URL . 'assets/js/select2.min.js', array(), null );
		wp_enqueue_script( 'ihc-print-this', IHC_URL . 'assets/js/printThis.js', array(), null );
	}
}

///CUSTOM NAV MENU
require_once IHC_PATH . 'admin/includes/custom-nav-menu.php';

//AJAX CALL FOR POPUP
add_action( 'wp_ajax_ihc_ajax_admin_popup', 'ihc_ajax_admin_popup' );
function ihc_ajax_admin_popup(){
	include_once IHC_PATH . 'admin/includes/popup-locker.php';
	die();
}

//AJAX CALL FOR DELETE USER
add_action( 'wp_ajax_ihc_delete_user_via_ajax', 'ihc_delete_user_via_ajax' );
function ihc_delete_user_via_ajax(){
	if ($_REQUEST['id']){
		require_once IHC_PATH . 'admin/includes/functions.php';
		ihc_delete_users($_REQUEST['id']);
	}
	die();
}


//ajax call for popup forms 
add_action( 'wp_ajax_ihc_ajax_admin_popup_the_forms', 'ihc_ajax_admin_popup_the_forms');
function ihc_ajax_admin_popup_the_forms(){
	include_once IHC_PATH . 'admin/includes/popup-forms.php';
	die();	
}

//AJAX CALL PREVIEW TEMPLATE IN POPUP
add_action( 'wp_ajax_ihc_ajax_template_popup_preview', 'ihc_ajax_template_popup_preview' );
function ihc_ajax_template_popup_preview(){
	if (isset($_REQUEST['template']) && $_REQUEST['template']!=''){
		//get id
		$arr = explode('_', $_REQUEST['template']);
		if(isset($arr[1]) && $arr[1]!=''){
			include IHC_PATH . 'public/locker-layouts.php';
			echo ihc_print_locker_template($arr[1]);		
		}		
	}
	die();
}

//AJAX CALL PREVIEW LOGIN FORM
add_action( 'wp_ajax_ihc_login_form_preview', 'ihc_login_form_preview' );
function ihc_login_form_preview(){
		$meta_arr['ihc_login_remember_me'] = $_REQUEST['remember'];
		$meta_arr['ihc_login_register'] = $_REQUEST['register'];
		$meta_arr['ihc_login_pass_lost'] = $_REQUEST['pass_lost'];
		$meta_arr['ihc_login_template'] = $_REQUEST['template'];
		$meta_arr['ihc_login_custom_css'] = $_REQUEST['css'];
		$meta_arr['ihc_login_show_sm'] = $_REQUEST['ihc_login_show_sm'];
		$meta_arr['ihc_login_show_recaptcha'] = $_REQUEST['ihc_login_show_recaptcha'];
		echo ihc_print_form_login($meta_arr);
	die();
}

//ajax preview locker
add_action( 'wp_ajax_ihc_locker_preview_ajax', 'ihc_locker_preview_ajax' );
function ihc_locker_preview_ajax(){
	include IHC_PATH . 'public/locker-layouts.php';
	if (isset($_REQUEST['locker_id'])){
		//ihc_print_locker_template(template id, meta array, preview)
		if (isset($_REQUEST['popup_display']) && $_REQUEST['popup_display']){
			//preview in a popup
			$str = '
					<div class="ihc-popup-wrapp" id="popup_box">
						<div class="ihc-the-popup">
						<div class="ihc-popup-top">
							<div class="title">Preview Locker</div>
							<div class="close-bttn" onclick="ihc_closePopup();"></div>
							<div class="clear"></div>
						</div>
							<div class="ihc-popup-content" style="text-align: center;">
								<div style="margin: 0 auto;">
									'.ihc_print_locker_template($_REQUEST['locker_id'], false, true).'
								</div>
							</div>
						</div>
					</div>
			';	
		} else {
			// html
			$str = ihc_print_locker_template($_REQUEST['locker_id'], false, true);
		}
		
		echo $str;
		
	} else {
		$meta_arr = $_REQUEST;
		echo ihc_print_locker_template(false, $meta_arr, true);		
	}

	die();
}

//ajax preview locker
add_action( 'wp_ajax_ihc_register_preview_ajax', 'ihc_register_preview_ajax' );
function ihc_register_preview_ajax(){
	if (!class_exists('UserAddEdit')){
		include_once IHC_PATH . 'classes/UserAddEdit.class.php';		
	}
	$args = array(
			'user_id' => false,
			'type' => 'create',
			'tos' => true,
			'captcha' => true,
			'action' => '',
			'is_public' => true,
			'disabled_submit_form' => 'disabled',
			'register_template' => $_REQUEST['template'],
			'preview' => TRUE,
	);
	$obj_form = new UserAddEdit();
	$obj_form->setVariable($args);//setting the object variables
	$str = '';
	$str .= '<style>' . $_REQUEST['custom_css'] . '</style>';
	$str .= '<div class="iump-register-form  '.$_REQUEST['template'].'">' . $obj_form->form() . '</div>';
	echo $str;
	die();
}

//ajax approve user
add_action( 'wp_ajax_ihc_approve_new_user', 'ihc_approve_new_user' );
function ihc_approve_new_user(){
	/*
	 * @param none
	 * @return none
	 */
	if (isset($_REQUEST['uid']) && $_REQUEST['uid']){
		$uid = wp_update_user(array( 'ID' => $_REQUEST['uid'], 'role' => 'subscriber'));		
		if ($_REQUEST['uid']==$uid){
			ihc_send_user_notifications($_REQUEST['uid'], 'approve_account');
			echo 1;
		}
	}
	die();
}

//ajax approve email address
add_action( 'wp_ajax_ihc_approve_user_email', 'ihc_approve_user_email' );
function ihc_approve_user_email(){
	if (isset($_REQUEST['uid']) && $_REQUEST['uid']){
		update_user_meta($_REQUEST['uid'], 'ihc_verification_status', 1);
		ihc_send_user_notifications($_REQUEST['uid'], 'email_check_success');//approve_account
		echo 1;
	}
	die();
}

//ajax reorder levels
add_action( 'wp_ajax_ihc_reorder_levels', 'ihc_reorder_levels' );
function ihc_reorder_levels(){
	$json = stripslashes($_REQUEST['json_data']);
	$json_arr = json_decode($json);	
	$i = 0;
	$data = get_option('ihc_levels');
	foreach ($json_arr as $k){
		$data[$k]['order'] = $i;
		$i++;
	}
	update_option('ihc_levels', $data);
	die();
}

//ajax reorder levels
add_action( 'wp_ajax_ihc_preview_select_level', 'ihc_preview_select_level' );
function ihc_preview_select_level(){
	include IHC_PATH . 'public/shortcodes.php';
	$attr = array(
					'template' => $_REQUEST['template'], 
					'css' => $_REQUEST['custom_css'],
	);
	echo ihc_user_select_level($attr);
	die();
}

//////////////aweber
add_action( 'wp_ajax_ihc_update_aweber', 'ihc_update_aweber' );
function ihc_update_aweber(){
	include_once IHC_PATH .'classes/email_services/aweber/aweber_api.php';
	list($consumer_key, $consumer_secret, $access_key, $access_secret) = AWeberAPI::getDataFromAweberID( $_REQUEST['auth_code'] );	
	update_option( 'ihc_aweber_consumer_key', $consumer_key );
	update_option( 'ihc_aweber_consumer_secret', $consumer_secret );
	update_option( 'ihc_aweber_acces_key', $access_key );
	update_option( 'ihc_aweber_acces_secret', $access_secret );
	echo 1;
	die();	
}

add_action('wp_ajax_ihc_get_cc_list', 'ihc_get_cc_list');
add_action('wp_ajax_nopriv_ihc_get_cc_list', 'ihc_get_cc_list');
function ihc_get_cc_list(){
	echo json_encode(ihc_return_cc_list($_REQUEST['ihc_cc_user'],$_REQUEST['ihc_cc_pass']));
	die();	
}

///////VC SECTION
add_action( 'init', 'ihc_check_vc' );

function ihc_check_vc(){
	if (function_exists('vc_map')){
		require_once IHC_PATH . 'admin/includes/vc_map.php';
	}
}

//ajax call for popup forms
add_action( 'wp_ajax_ihc_return_csv_link', 'ihc_return_csv_link');
function ihc_return_csv_link(){
	echo ihc_make_csv_user_list();
	die();
}

//ajax call for delete coupon
add_action( 'wp_ajax_ihc_delete_coupon_ajax', 'ihc_delete_coupon_ajax');
function ihc_delete_coupon_ajax(){
	ihc_delete_coupon($_REQUEST['id']);
	echo 1;
	die();
}

//ajax notification templates
add_action( 'wp_ajax_ihc_notification_templates_ajax', 'ihc_notification_templates_ajax');
function ihc_notification_templates_ajax(){
	/*
	 * @param [string]
	 * @return array
	 */
	if (!empty($_REQUEST['type'])){
		echo json_encode(ihc_return_notification_pattern($_REQUEST['type']));
	}
	die();
}

function ihc_return_notification_pattern($type=''){
	/*
	 * @param string
	 * @return array
	 */
	 $template = array('subject'=>'', 'content'=>'');
		switch ($type){
			case 'register':
$template['subject'] = '{blogname}: Welcome to {blogname}';
$template['content'] = '<p>Hi {username},</p><br/>

<p>Thanks for registering on {blogname}. Your account is now active.</p><br/>

<p>To login please fill out your credentials on:<br/>
{login_page}</p><br/>

<p>Your Username: {username}</p><br/><br/>


<p>Have a nice day!</p>';
				break;
				
			case 'review_request':
$template['subject'] = '{blogname}: Welcome to {blogname}';
$template['content'] = '<p>Hi {username},</p><br/>

<p>Thanks for registering on {blogname}. Your account is waiting to be approved.</p><br/>

<p>Once your Account is approved you can login using your credentials on:<br/>
<a href="{login_page}">{login_page}</a></p><br/>

<p>Your Username: {username}</p><br/><br/>


<p>Have a nice day!</p>';				
				break;
				
			case 'payment':
				$template['subject'] = '{blogname}: Payment proceed';
				$template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

<p>You have proceed a new Payment into your account on {blogname}.</p><br/><br/>


<p>Thanks for your payment!</p>';				
				break;
				
			case 'user_update':
				$template['subject'] = '{blogname}: Your Account has been Updated';
				$template['content'] = '<p>Hi {username},</p><br/>

<p>Your Account has been Updated.</p><br/>

<p>To visit your Profile page follow the next link:<br/>
<a href="{account_page}">{account_page}</a></p><br/>

<p>Have a nice day!</p>';				
				break;
				
			case 'before_expire':
				$template['subject'] = '{blogname}: Your Subscription Expire';
				$template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

<p>Your Subscription {current_level} is going to expire on {current_level_expire_date}.</p><br/>

<p>To update your Subscriptions, please, visit your Profile page on:<br/>
<a href="{account_page}">{account_page}</a></p><br/>

<p>Have a nice day!</p>';			
				break;
			case 'second_before_expire':
				$template['subject'] = '{blogname}: Your Subscription Expire';
				$template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

<p>Your Subscription {current_level} is going to expire on {current_level_expire_date}.</p><br/>

<p>To update your Subscriptions, please, visit your Profile page on:<br/>
<a href="{account_page}">{account_page}</a></p><br/>

<p>Have a nice day!</p>';			
				break;
			case 'third_before_expire':
				$template['subject'] = '{blogname}: Your Subscription Expire';
				$template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

<p>Your Subscription {current_level} is going to expire on {current_level_expire_date}.</p><br/>

<p>To update your Subscriptions, please, visit your Profile page on:<br/>
<a href="{account_page}">{account_page}</a></p><br/>

<p>Have a nice day!</p>';			
				break;								
			case 'expire':
				$template['subject'] = '{blogname}: Your Subscription has Expired';
				$template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

<p>Your Subscription {current_level} has expired on {current_level_expire_date}.</p><br/>

<p>To update your Subscriptions, please, visit your Profile page on:<br/>
<a href="{account_page}">{account_page}</a></p><br/>

<p>Have a nice day!</p>';			
				break;
				
			case 'email_check':
				$template['subject'] = '{blogname}: Email Verification';
				$template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

<p>You must confirm/validate your Email Account before logging in.</p><br/>

<p>Please click on the following link to successfully activate your account:<br/>
<a href="{verify_email_address_link}">click here</a></p><br/>

<p>Have a nice day!</p><br/>';			
				break;
				
			case 'email_check_success':
				$template['subject'] = '{blogname}: Email Verification Successfully';
				$template['content'] = '<p>Hi {first_name} {last_name},</p><br/>

<p>Your account is now verified at {blogname}.</p><br/>

<p>Have a nice day!</p><br/>';				
				break;
				
			case 'reset_password_process':
				$template['subject'] = '{blogname}: Reset Password request';
				$template['content'] = '<p>Hi {first_name} {last_name},</p></br>
	
<p>You or someone else has requested to change password for your account: {username}</p></br>
	
<p>To confirm this request click <a href="{password_reset_link}">here</a></p></br>

<p>A new generated Password will be sent via Email next after the request was confirmed.</p>	

<p>If you did not request for a new password, please ignore this Email notification.</p>';
				break;	

			case 'reset_password':
				$template['subject'] = '{blogname}: Reset Password request';
				$template['content'] = '<p>Hi {first_name} {last_name},</p></br>

<p>You or someone else has requested to change password for your account: {username}</p></br>

<p>Your new Password is: <strong>{NEW_PASSWORD}</strong></p></br>

<p>To update your Password once you are logged from your Profile Page:
<a href="{account_page}">{account_page}</a></p></br>

<p>If you did not request for a new password, please ignore this Email notification.</p>';
				break;
				
			case 'change_password':
				$template['subject'] = '{blogname}: Your Password has been changed';
				$template['content'] = '<p>Hi {first_name} {last_name},</p><br/>
				
<p>Your Password has been changed.</p><br/>

<p>To login please fill out your credentials on:<br/>
<a href="{login_page}">{login_page}</a></p><br/>

<p>Your Username: {username}</p><br/>

<p>Have a nice day!</p>';
				break;	
							
			case 'delete_account':
				$template['subject'] = '{blogname}: Your Account has been deleted';
				$template['content'] = '<p>Hi {username},</p><br/>
				
<p>Your account has been deleted from {blogname}.</p><br/>

<p>Have a nice day!</p>';			
				break;
				
			case 'bank_transfer':
					$template['subject'] = '{blogname}: Payment Inform';
					$template['content'] = 'Hi {username},

Please proceed the bank transfer payment for: {currency}{amount}

<strong>Payment Details:</strong> Subscription {level_name} for {username} with Identification: {user_id}_{level_id}

&nbsp;

<strong>Bank Details:</strong>

IBAN:xxxxxxxxxxxxxxxxxxxx

Bank NAME';
				break;		
						
			case 'approve_account':
					$template['subject'] = '{blogname}: Your Account has been activated';
					$template['content'] = '<p>Hi {username},</p><br/>
					
<p>Your Account has been activated!</p><br/>
				
<p>Have a nice day!</p>';
				break;
				
			case 'admin_user_register':
				/// ADMIN - USER REGISTER			
					$template['subject'] = '{blogname}: New Membership User registration';
					$template['content'] = '<html><head></head><body><p>New Membership User registration on: <strong> {blogname} </strong></p>
					
<p><strong> Username:</strong> {username}</p>

<p><strong> Email:</strong> {user_email}</p>

<p><strong> Level Name:</strong> {level_name}</p>

<p>Have a nice day!</p>
					</body></html>';
				break;		
				
			case 'admin_before_user_expire_level':
				/// ADMIN - Before Level Expire
					$template['subject'] = '{blogname}: User Level Expire';
					$template['content'] = '<html><head></head><body>
<p>Subscription {current_level} for <strong> Username: {username}</strong> is going to expire on {current_level_expire_date}.</p><br/>

<p>Have a nice day!</p>
					</body></html>';				
				break;	
				
			case 'admin_second_before_user_expire_level':
				/// ADMIN - Before Level Expire
					$template['subject'] = '{blogname}: User Level Expire';
					$template['content'] = '<html><head></head><body>
<p>Subscription {current_level} for <strong> Username: {username}</strong> is going to expire on {current_level_expire_date}.</p><br/>

<p>Have a nice day!</p>
					</body></html>';				
				break;					

			case 'admin_third_before_user_expire_level':
				/// ADMIN - Before Level Expire
					$template['subject'] = '{blogname}: User Level Expire';
					$template['content'] = '<html><head></head><body>
<p>Subscription {current_level} for <strong> Username: {username}</strong> is going to expire on {current_level_expire_date}.</p><br/>

<p>Have a nice day!</p>
					</body></html>';				
				break;	
								
			case 'admin_user_expire_level':
				/// ADMIN - After Level Expired
					$template['subject'] = '{blogname}: User Level Expire';
					$template['content'] = '<html><head></head><body>
<p>Subscription {current_level} for<strong> Username: {username}</strong> has expired on {current_level_expire_date}.</p>
<p>Have a nice day!</p>
					</body></html>';					
				break;	
				
			case 'admin_user_payment':
				/// ADMIN - New Payment Completed
					$template['subject'] = '{blogname}: New Payment Completed';
					$template['content'] = '<html><head></head><body>
<p><strong> User: {username}</strong> has completed a new payment.</p>
<p>Have a nice day!</p>
					</body></html>';				
				break;
			case 'admin_user_profile_update':
				/// ADMIN - User Profile Update
					$template['subject'] = '{blogname}: User Update Profile';
					$template['content'] = '<html><head></head><body>
<p><strong> User: {username}</strong> has updated his profile.</p>
<p>Have a nice day!</p>
					</body></html>';				
				break;
			case 'register_lite_send_pass_to_user':
					$template['subject'] = '{blogname}: Your Password';
					$template['content'] = '<html><head></head><body>
<p>Hi {username}</p>
<p>Your password for {blogname} is {NEW_PASSWORD}</p>
					</body></html>';				
				break;		
				
			case 'ihc_cancel_subscription_notification-admin':
					$template['subject'] = '{blogname}: Subscription Canceled';
					$template['content'] = '<html><head></head><body>
<p>{current_level} for {username} was canceled.</p>
					</body></html>';						
				break;	
			case 'ihc_delete_subscription_notification-admin':
					$template['subject'] = '{blogname}: Subscription Deleted';
					$template['content'] = '<html><head></head><body>
<p>{current_level} for {username} was deleted.</p>
					</body></html>';						
				break;							
			case 'ihc_order_placed_notification-admin':
					$template['subject'] = '{blogname}: New Order placed';
					$template['content'] = '<html><head></head><body>
<p>{username} has placed a new order.</p>
					</body></html>';						
				break;	
			case 'ihc_new_subscription_assign_notification-admin':
					$template['subject'] = '{blogname}: New Subscription assign';
					$template['content'] = '<html><head></head><body>
<p>{username} subscribe for {current_level}.</p>
					</body></html>';				
				break;	
			case 'ihc_order_placed_notification-user':
					$template['subject'] = '{blogname}: New Order placed';
					$template['content'] = '<html><head></head><body>
<p>Hello {username}! You just placed a new order on <strong> {blogname} </strong>.</p>
<p>Have a nice day!</p>
					</body></html>';				
				break;
			case 'ihc_subscription_activated_notification':
					$template['subject'] = '{blogname}: Subscription Activated';
					$template['content'] = '<html><head></head><body>
<p>Hello {username}! Your subscription on <strong> {blogname} </strong> just got activated.</p>
<p>Have a nice day!</p>
					</body></html>';			
				break;
			case 'ihc_delete_subscription_notification-user':
					$template['subject'] = '{blogname}: Subscription deleted';
					$template['content'] = '<html><head></head><body>
<p>Hello {username}! One of Your subscriptioms on <strong> {blogname} </strong> was completely deleted.</p>
<p>Have a nice day!</p>
					</body></html>';				
				break;
			case 'ihc_cancel_subscription_notification-user':
					$template['subject'] = '{blogname}: Subscription cancel';
					$template['content'] = '<html><head></head><body>
<p>Hello {username}! One of Your subscriptioms on <strong> {blogname} </strong> was canceled.</p>
<p>Have a nice day!</p>
					</body></html>';				
				break;				
		}
	return $template;	 
}

/////////////////////////// DASHBOARD LIST POST/PAGES/CUSTOM POST TYPE ULTIMATE MEMBERSHIP PRO COLUMN WIHT DEFAULT PAGES/RESTRINCTED AND DRIP CONTENT

add_filter( 'display_post_states', 'ihc_custom_column_dashboard_print', 999, 2 );
function ihc_custom_column_dashboard_print($states, $post){
	/*
	 * @param string
	 * @return none, print a string if its case
	 */
	if (isset($post->ID) ){
			$str = '';
			//////////// DEFAULT PAGES
			if (get_post_type($post->ID)=='page'){			
				$register_page = get_option('ihc_general_register_default_page');
				$lost_pass = get_option('ihc_general_lost_pass_page');
				$login_page = get_option('ihc_general_login_default_page');
				$redirect = get_option('ihc_general_redirect_default_page');
				$logout = get_option('ihc_general_logout_page');
				$user_page = get_option('ihc_general_user_page');
				$tos = get_option('ihc_general_tos_page');
				$subscription_plan = get_option('ihc_subscription_plan_page');
				$view_user_page = get_option('ihc_general_register_view_user');
				
				switch($post->ID){
					case $register_page:
						$print = __('Register Page', 'ihc');
						break;
					case $lost_pass:
						$print = __('Lost Password Page', 'ihc');
						break;
					case $login_page:
						$print = __('Login Page', 'ihc');
						break;
					case $redirect:
						$print = __('Redirect Page', 'ihc');
						break;
					case $logout:
						$print = __('Logout Page', 'ihc');
						break;
					case $user_page:
						$print = __('User Page', 'ihc');
						break;
					case $tos:
						$print = __('TOS Page', 'ihc');
						break;
					case $subscription_plan:
						$print = __('Subscription Plan Page', 'ihc');
						break;
					case $view_user_page:
						$print = __('Visitor Inside User Page', 'ihc');
						break;
				}
				if (!empty($print)){
					$str .= '<div class="ihc-dashboard-list-posts-col-default-pages">' . $print . '</div>';
				}
			}
			
			$post_meta = ihc_post_metas($post->ID);
			////////// RESTRICTIONS
			if (!empty($post_meta['ihc_mb_who'])){
				$str .= '<div class="ihc-dashboard-list-posts-col-restricted-posts">' . __("Restricted", 'ihc') . '</div>';
			}
			
			//////////// DRIP CONTENT			
			if (!empty($post_meta['ihc_drip_content']) && $post_meta['ihc_mb_type']=='show' && !empty($post_meta['ihc_mb_who'])){
				$str .= '<div class="ihc-dashboard-list-posts-col-drip-content">' . __("Drip Content", 'ihc') . '</div>';
			}
			if (!empty($str))
			$states[] = $str;
	}
	return $states;
}

add_action('wp_ajax_ihc_delete_currency_code_ajax', 'ihc_delete_currency_code_ajax');
add_action('wp_ajax_nopriv_ihc_delete_currency_code_ajax', 'ihc_delete_currency_code_ajax');
function ihc_delete_currency_code_ajax(){
	if (isset($_REQUEST['code'])){
		$data = get_option('ihc_currencies_list');
		if (!empty($data[$_REQUEST['code']])){
			unset($data[$_REQUEST['code']]);
			echo 1;
		}
		update_option('ihc_currencies_list', $data);
	}	
	die();
}

add_action('wp_ajax_ihc_preview_user_listing', 'ihc_preview_user_listing');
add_action('wp_ajax_nopriv_ihc_preview_user_listing', 'ihc_preview_user_listing');
function ihc_preview_user_listing(){
	if (!empty($_REQUEST['shortcode'])){
		define('IS_PREVIEW', TRUE);
		$shortcode = stripslashes($_REQUEST['shortcode']);
		require_once IHC_PATH . 'public/shortcodes.php';
		echo do_shortcode($shortcode);
	}
	die();
}

add_action( 'update_option_permalink_structure' , 'ihc_update_permalink_structure_action', 99, 2 );
function ihc_update_permalink_structure_action( $old_value, $new_value ) {
	/*
	 * @param string, string
	 * @return none
	 */
	update_option('indeed_do_rewrite_update', TRUE);
}

add_action('init', 'ihc_do_rewrite_update', 1);
function ihc_do_rewrite_update(){
	/*
	 * @param none
	 * @return none
	 */
	if (get_option('indeed_do_rewrite_update')){
		$page_id = get_option('ihc_general_register_view_user');
		ihc_save_rewrite_rule_for_register_view_page($page_id);
		update_option('indeed_do_rewrite_update', FALSE);
	}
}

add_action('wp_ajax_ihc_delete_user_level_relationship', 'ihc_delete_user_level_relationship');
function ihc_delete_user_level_relationship(){
	/*
	 * @param none
	 * @return none
	 */
	if (isset($_REQUEST['lid']) && isset($_REQUEST['uid'])){
		$levels_str = get_user_meta($_REQUEST['uid'], 'ihc_user_levels', true);
		$levels_arr = explode(',', $levels_str);
		if (!is_array($_REQUEST['lid'])){
			$lid_arr[] = $_REQUEST['lid'];
		}
		$levels_arr = array_diff($levels_arr, $lid_arr);
		$levels_str = implode(',', $levels_arr);
		update_user_meta($_REQUEST['uid'], 'ihc_user_levels', $levels_str);
		global $wpdb;
		$table_name = $wpdb->prefix . "ihc_user_levels";
		$wpdb->query('DELETE FROM ' . $table_name . ' WHERE user_id="' . $_REQUEST['uid'] . '" AND level_id="' . $_REQUEST['lid'] . '";');
		echo 1;
	}
	die();
}


add_action('wp_ajax_ihc_make_user_affiliate', 'ihc_make_user_affiliate');
add_action('wp_ajax_nopriv_ihc_make_user_affiliate', 'ihc_make_user_affiliate');
function ihc_make_user_affiliate(){
	/*
	 * @param none
	 * @return none
	 */
	if (isset($_REQUEST['uid']) && isset($_REQUEST['act']) && defined('UAP_PATH')){
		if (!class_exists('Uap_Db')){
			require_once UAP_PATH . 'classes/Uap_Db.class.php';
			$indeed_db = new Uap_Db;
		} else {
			global $indeed_db;
		}
		if ($_REQUEST['act']==0){
			// remove from affiliates
			$indeed_db->remove_user_from_affiliate($_REQUEST['uid']);
		} else {
			/// add to affiliates
			$inserted = $indeed_db->save_affiliate($_REQUEST['uid']);
			if ($inserted){
				/// put default rank on this new affiliate
				$default_rank = get_option('uap_register_new_user_rank');
				$indeed_db->update_affiliate_rank_by_uid($_REQUEST['uid'], $default_rank);		
				echo $inserted;		
			}		
		}
	}	
	  
	die(); 
}

add_action('wp_ajax_ihc_check_mail_server', 'ihc_check_mail_server');
function ihc_check_mail_server(){
	/*
	 * @param none
	 * @return int
	 */
	 $from_email = '';
	 $from_name = '';
	 $from_email = get_option('ihc_notification_email_from');
	 if (!$from_email){
		$from_email = get_option('admin_email');
	 }
	 $from_name = get_option('ihc_notification_name');
	 if (empty($from_name)){
		$from_name = get_option("blogname");
	 }		 
	 $headers[] = "From: $from_name <$from_email>";	
	 $headers[] = 'Content-Type: text/html; charset=UTF-8';
	 
	 $to = get_option('admin_email');
	 $subject = get_option('blogname') . ': ' . __('Testing Your E-mail Server', 'ihc');
	 $content = __('Just a simple message to test if Your E-mail Server is working', 'ihc'); 
	 wp_mail($to, $subject, $content, $headers);
	 echo 1;
	 die();
}

add_action('init', 'ihc_do_rewrite_rule', 10, 0);
function ihc_do_rewrite_rule(){
	/*
	 * @param none
	 * @return none
	 */
	$inside_page = get_option('ihc_general_register_view_user');
	if ($inside_page){
		$page_slug = Ihc_Db::get_page_slug($inside_page);
		add_rewrite_rule($page_slug . "/([^/]+)/?",'index.php?pagename=' . $page_slug . '&ihc_name=$matches[1]', 'top');
		flush_rewrite_rules();	
	}
}


add_action('wp_ajax_ihc_do_generate_individual_pages', 'ihc_do_generate_individual_pages');
function ihc_do_generate_individual_pages(){
	/*
	 * @param none
	 * @return none
	 */
	$users = Ihc_Db::get_users_with_no_individual_page();
	if ($users){
		if (!class_exists('IndividualPage')){
			include_once IHC_PATH . 'classes/IndividualPage.class.php';
		}
		$object = new IndividualPage();
		$object->generate_pages_for_users($users);		
	}
	die();
}	


add_action('wp_ajax_ihc_preview_invoice_via_ajax', 'ihc_preview_invoice_via_ajax');
function ihc_preview_invoice_via_ajax(){
	/*
	 * @param none
	 * @return none
	 */
	$temp = $_REQUEST['m'];
	foreach ($temp as $k=>$array){
		$metas[$array['name']] = $array['value'];
	}
	require IHC_PATH . 'classes/Ihc_Invoice.class.php';
	$object = new Ihc_Invoice(1, 0, $metas);
	echo $object->output();
	die();
}

