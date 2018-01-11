<?php 
function ihc_post_metas($post_id, $return_name=FALSE){
	/*
	 * @param int, bool
	 * @return array
	 */	
	$arr = array(
					'ihc_mb_type' => 'show',
					'ihc_mb_who' => '',
					'ihc_mb_block_type' => 'redirect',
					'ihc_mb_redirect_to' => -1,
					'ihc_replace_content' => '',
					//DRIP CONTENT
					'ihc_drip_content' => 0,
					'ihc_drip_start_type' => 1,
					'ihc_drip_end_type' => 1,
					'ihc_drip_start_numeric_type' => 'days',
					'ihc_drip_start_numeric_value' => '',
					'ihc_drip_end_numeric_type' => 'days',
					'ihc_drip_end_numeric_value' => '',
					'ihc_drip_start_certain_date' => '',
					'ihc_drip_end_certain_date' => '',
				);
	if($return_name==TRUE) return $arr;
	foreach($arr as $k=>$v){
		$data = get_post_meta($post_id, $k, true);
		if( $data!==FALSE && $data!='' )
			$arr[$k] = $data;
	}
	return $arr;	
}

function ihc_get_all_pages(){
	/*
	 * @param none
	 * @return array
	 */
	$arr = array();
	$args = array(
			'sort_order' => 'ASC',
			'sort_column' => 'post_title',
			'hierarchical' => 1,
			'child_of' => 0,
			'parent' => -1,
			'number' => '',
			'offset' => 0,
			'post_type' => 'page',
			'post_status' => 'publish'
	);
	$pages = get_pages($args);
	if (isset($pages) && count($pages)>0){
		foreach ($pages as $page){
			if ($page->post_title=='') $page->post_title = '(no title)';
			$arr[$page->ID] = $page->post_title;
		}
	}
	return $arr;
}


function ihc_locker_meta_keys(){
	/*
	 * @param none
	 * @return array
	 */
	//meta keys for ihc_lockers
	$arr = array(
					'ihc_locker_name' => 'Untitled Locker',
					'ihc_locker_custom_content' => '<h2>This content is locked</h2>
													Login To Unlock The Content!',
					'ihc_locker_custom_css' => '.ihc-locker-wrap{}',
					'ihc_locker_template' => '',
					'ihc_locker_login_template' => '',
					'ihc_locker_login_form' => 1,
					'ihc_locker_additional_links' => 1,
					'ihc_locker_display_sm' => 0,
				 );
	return $arr;
}

function ihc_return_meta($name, $id=false){
	/*
	 * @param string, string|bool
	 * @return ...
	 */
	$data = get_option($name);
	if ($data!==FALSE){
		if($data && isset($data[$id])) return $data[$id];
		return $data;
	}
	else return FALSE;	
}

function ihc_return_meta_arr($type, $only_name=false, $return_default=false){
	/*
	 * @param string, bool, bool
	 * @return array
	 */
	//all metas
	switch ($type){
		case 'payment':
			$arr = array(
							'ihc_currency' => 'USD',
							'ihc_currency_position' => 'right',
							'ihc_custom_currency_code' => '',
							'ihc_payment_set' => 'predefined',
							'ihc_payment_selected' => 'bank_transfer',
						);
		break;
		case 'payment_paypal':
			$arr = array(
							'ihc_paypal_email' => '',							
							'ihc_paypal_sandbox' => 0,
							'ihc_paypal_return_page' => -1,
							'ihc_paypal_status' => 0,
							'ihc_paypal_label' => 'PayPal',
							'ihc_paypal_select_order' => 1,
						);
		break;
		case 'payment_stripe':
			$arr = array(
							'ihc_stripe_secret_key' => '',
							'ihc_stripe_publishable_key' => '',			
							'ihc_stripe_status' => 0,
							'ihc_stripe_label' => 'Stripe',
							'ihc_stripe_select_order' => 2,
			);
		break;
		case 'payment_authorize':
			$arr = array(
							'ihc_authorize_login_id' => '',
							'ihc_authorize_transaction_key' => '',			
							'ihc_authorize_sandbox' => 0,
							'ihc_authorize_status' => 0,
							'ihc_authorize_label' => 'Authorize',
							'ihc_authorize_select_order' => 3,
			);
		break;
		case 'payment_twocheckout':
			$arr = array(
							'ihc_twocheckout_status' => 0,
							'ihc_twocheckout_sandbox' => 0,
							'ihc_twocheckout_api_user' => '',
							'ihc_twocheckout_api_pass' => '',
							'ihc_twocheckout_private_key' => '',
							'ihc_twocheckout_account_number' => '',
							'ihc_twocheckout_secret_word' => '',
							'ihc_twocheckout_label' => '2Checkout',
							'ihc_twocheckout_select_order' => 4,
			);			
		break;
		case 'payment_bank_transfer':
			$arr = array(
					'ihc_bank_transfer_status' => 1,
					'ihc_bank_transfer_message' => '<p>Hi {username},</p>
<br/>
<p>Please proceed the bank transfer payment for: {currency}{amount}</p>

<p><strong>Payment Details:</strong> Subscription {level_name} for {username} with Identification: {user_id}_{level_id}</p>

<br/>

<strong>Bank Details:</strong><br/>

IBAN:xxxxxxxxxxxxxxxxxxxx<br/>

Bank NAME<br/>',
					'ihc_bank_transfer_label' => 'Bank Transfer',
					'ihc_bank_transfer_select_order' => 5,
			);			
		break;
		case 'payment_braintree':
			$arr = array(
					'ihc_braintree_status' => 0,
					'ihc_braintree_sandbox' => 0,
					'ihc_braintree_merchant_id' => '',
					'ihc_braintree_public_key' => '',
					'ihc_braintree_private_key' => '',
			);
			break;
		case 'payment_payza':
			$arr = array(
					'ihc_payza_status' => 0,
					'ihc_payza_email' => '',
			);
			break;			
		case 'login':
			$arr = array(
						   'ihc_login_remember_me' => 1,
						   'ihc_login_register' => 1,
						   'ihc_login_pass_lost' => 1,
						   'ihc_login_template' => 'ihc-login-template-10',
						   'ihc_login_custom_css' => '',
						   'ihc_login_show_sm' => 0,
						   'ihc_login_show_recaptcha' => 0,
						);
		break;
		case 'login-messages':
			$arr = array(
							'ihc_login_succes' => 'Welcome on our Website!',
							'ihc_login_pending' => 'Your account was not been approved yet. Please, try again later',
							'ihc_login_error' => 'Invalid Email Address or Password entered',
							'ihc_reset_msg_pass_err' => 'Invalid Email Address or Username entered',
							'ihc_reset_msg_pass_ok' => 'A new password has been send to your email address',	
							'ihc_login_error_email_pending' => 'E-mail address has not been verified yet',
							'ihc_login_error_on_captcha' => 'Captcha Error',
							'ihc_login_error_ajax' => 'Please complete all required fields!',
						);
		break;
		case 'general-defaults':
			$arr = array(
							//default pages							
							'ihc_general_login_default_page' => '',
							'ihc_general_register_default_page'=>'',
							'ihc_general_lost_pass_page' => '',
							'ihc_general_logout_page' => '',
							'ihc_general_user_page' => '',
							'ihc_general_tos_page' => '',
							'ihc_subscription_plan_page' => '',
							'ihc_general_register_view_user' => '',
							//redirects
							'ihc_general_redirect_default_page' => '',
							'ihc_general_logout_redirect' => '',
							'ihc_general_register_redirect' => '',
							'ihc_general_login_redirect' => '',			
							'ihc_general_password_redirect' => '',
							/// prevent listing hidden post, pages
							'ihc_listing_show_hidden_post_pages' => 0,				
						);
		break;
		case 'general-captcha':
			//recapcha
			$arr = array(
							'ihc_recaptcha_public' => '',
							'ihc_recaptcha_private' => '',			
						);
		break;
		case 'general-subscription':
			$arr = array(
							'ihc_level_template' => 'ihc_level_template_5',
							'ihc_select_level_custom_css' => '.ich_level_wrap{}',
						);
		break;
		case 'general-msg':
			$arr = array(
							'ihc_general_update_msg' => 'Successfully Update!',
						);
		break;				
		case 'register':
			$arr = array(
							'ihc_register_template' => 'ihc-register-9',						
							'ihc_register_admin_notify' => 1,
							'ihc_register_pass_min_length' => 6,
							'ihc_register_pass_options' => 1,
							'ihc_register_new_user_level' => -1,//'none'
							'ihc_register_new_user_role' => 'subscriber',
							'ihc_register_custom_css' => '',
							'ihc_register_terms_c' => 'Accept our Terms&Conditions',							
							'ihc_subscription_type' => 'subscription_plan',
							'ihc_register_opt-in' => 0,
							'ihc_register_opt-in-type' => 'email_list',
							'ihc_register_show_level_price' => 1,
							'ihc_register_auto_login' => 0,
							'ihc_register_double_email_verification' => 0,
							'ihc_automatically_switch_role' => 0,
							'ihc_automatically_new_role' => 'subscriber',
						);
		break;
		case 'register-msg':
			$arr = array(
							//messages
							'ihc_register_username_taken_msg' => 'Username is taken',
							'ihc_register_error_username_msg' => 'Invalid Username',
							'ihc_register_email_is_taken_msg' => 'Email address is taken',
							'ihc_register_invalid_email_msg' => 'You must enter a valid Email Address.',
							'ihc_register_emails_not_match_msg' => 'Email Addresses do not match!',
							'ihc_register_pass_not_match_msg' => 'Password do not match',
							'ihc_register_pass_letter_digits_msg' => 'Password must contains characters and digits!',
							'ihc_register_pass_let_dig_up_let_msg' => 'Password must contains characters, digits and minimum one uppercase letter!',
							'ihc_register_pass_min_char_msg' => 'Password must contains minimum {X} characters!',
							'ihc_register_pending_user_msg' => 'Your Account has not been approved yet. Please try again later!',
							'ihc_register_err_req_fields' => 'Please complete all required fields!',
							'ihc_register_err_recaptcha' => 'Captcha Error',
							'ihc_register_err_tos' => 'Error On Terms & Conditions',
							'ihc_register_success_meg' => '<h4>Successfully Register!</h4>
<br/>',
							'ihc_register_update_msg' => 'Successfully Updated!',			
						);			
		break;
		case 'register-custom-fields':
			$arr = array(
							'ihc_user_fields' => ihc_native_user_field(),
						);
		break;
		case 'opt_in':
			$arr = array(
							'ihc_main_email' => '',
							//aweber
							'ihc_aweber_auth_code' => '',
							'ihc_aweber_list' => '',
							'ihc_aweber_consumer_key' => '',
							'ihc_aweber_consumer_secret' => '',
							'ihc_aweber_acces_key' => '',
							'ihc_aweber_acces_secret' => '',
							//mailchimp
							'ihc_mailchimp_api' => '',
							'ihc_mailchimp_id_list' => '',
							//get response
							'ihc_getResponse_api_key' => '',
							'ihc_getResponse_token' => '',
							//campaign monitor
							'ihc_cm_api_key' => '',
							'ihc_cm_list_id' => '',
							//icontact
							'ihc_icontact_user' => '',
							'ihc_icontact_appid' => '',
							'ihc_icontact_pass' => '',
							'ihc_icontact_list_id' => '',
							//constant contact
							'ihc_cc_user' => '',
							'ihc_cc_pass' => '',
							'ihc_cc_list' => '',
							//Wysija Contact
							'ihc_wysija_list_id' => '',
							//MyMail
							'ihc_mymail_list_id' => '',
							//Mad Mimi
							'ihc_madmimi_username' => '',
							'ihc_madmimi_apikey' => '',
							'ihc_madmimi_listname' => '',
							//indeed email list
							'ihc_email_list' => '',
							// active campaign
							'ihc_active_campaign_apiurl' => '',
							'ihc_active_campaign_apikey' => '',
							'ihc_active_campaign_listId' => '',
						);
		break;
		case 'notifications':
			$arr = array(
							'ihc_notification_email_from' => '',
							'ihc_notification_before_time' => 5,
							'ihc_notification_before_time_second' => 3,
							'ihc_notification_before_time_third' => 1,
							'ihc_notification_name' => '',
							'ihc_notification_email_addresses' => '',
						);
		break;
		case 'extra_settings':
			$arr = array(
							'ihc_grace_period' => '',
							'ihc_debug_payments_db' => '',
							'ihc_upload_extensions' => 'txt,doc,pdf,jpg,jpeg,png,gif,mp3,zip',
							'ihc_upload_max_size' => 5,
							'ihc_avatar_max_size' => 1,							
						);
			break;
		case 'account_page':
			$arr = array(	'ihc_ap_theme' => 'ihc-ap-theme-1',
							'ihc_ap_edit_show_avatar' => 1,
							'ihc_ap_edit_show_level' => 1,
							'ihc_ap_tabs' => 'overview,profile,subscription,logout,help,transactions,orders,social',
							'ihc_ap_welcome_msg' => '<span class="iump-user-page-mess-special">Hello</span> <span class="iump-user-page-name"> {last_name} {first_name}</span>,
														<span class="iump-user-page-mess">you\'re logged as</span><span class="iump-user-page-mess-special"> {username}</span>
														<div class="iump-user-page-mess"><span>{flag}</span>Member since {user_registered}</div>														
														',
							'ihc_account_page_custom_css' => '',
							'ihc_ap_social_plus_message' => '',
							
							'ihc_ap_overview_menu_label' => 'Overview',
							'ihc_ap_overview_title' => 'Overview',
							'ihc_ap_overview_msg' => 'Hey There,
														This is the Overview section.
														&nbsp;
														Enjoy the sun.',							
							'ihc_ap_profile_menu_label' => 'Profile',
							'ihc_ap_profile_title' => 'Profile',
							'ihc_ap_profile_msg' => '',		
							'ihc_ap_subscription_menu_label' => 'Subscription',
							'ihc_ap_subscription_title' => '',
							'ihc_ap_subscription_msg' => '',	
							'ihc_ap_subscription_table_enable' => 1,
							'ihc_ap_subscription_plan_enable' => 1,	
							'ihc_ap_social_menu_label' => 'Social Plus',
							'ihc_ap_social_title' => 'Social Plus',
							'ihc_ap_social_msg' => '',			
							'ihc_ap_transactions_menu_label' => 'Transactions',
							'ihc_ap_transactions_title' => 'Transactions',
							'ihc_ap_transactions_msg' => '',		
							'ihc_ap_orders_menu_label' => 'Orders',
							'ihc_ap_orders_title' => 'Orders',
							'ihc_ap_orders_msg' => '',		
							'ihc_ap_membeship_gifts_menu_label' => 'Membership Gifts',
							'ihc_ap_membeship_gifts_title' => 'Membership Gifts',
							'ihc_ap_membeship_gifts_msg' => '[ihc-list-gifts]',			
							'ihc_ap_membership_cards_menu_label' => 'Membership Cards',
							'ihc_ap_membership_cards_title' => 'Membership Cards',
							'ihc_ap_membership_cards_msg' => '[ihc-membership-card]',	
							'ihc_ap_help_menu_label' => 'Help',
							'ihc_ap_help_title' => 'Help',
							'ihc_ap_help_msg' => '',		
							'ihc_ap_logout_menu_label' => 'LogOut',
							'ihc_ap_footer_msg' => '',
							'ihc_ap_top_background_image' => '',
							'ihc_ap_edit_background' => 1,
							'ihc_ap_top_template' => 'ihc-ap-top-theme-2',	
							'ihc_ap_edit_show_level' => 1,																					
					);
			break;
		case 'fb':
			$arr = array(
							'ihc_fb_app_id' => '',
							'ihc_fb_app_secret' => '',
							'ihc_fb_status' => 0,
						);
			break;
		case 'tw':
			$arr = array(
							'ihc_tw_app_key' => '',
							'ihc_tw_app_secret' => '',
							'ihc_tw_status' => 0,
			);
			break;	
		case 'in':
			$arr = array(
							'ihc_in_app_key' => '',
							'ihc_in_app_secret' => '',
							'ihc_in_status' => 0,
			);
			break;
		case 'tbr':
			$arr = array(
							'ihc_tbr_app_key' => '',
							'ihc_tbr_app_secret' => '',
							'ihc_tbr_status' => 0,
			);
			break;	
		case 'ig':
				$arr = array(
					'ihc_ig_app_id' => '',
					'ihc_ig_app_secret' => '',
					'ihc_ig_status' => 0,
				);
			break;
		case 'vk':
				$arr = array(
					'ihc_vk_app_id' => '',
					'ihc_vk_app_secret' => '',
					'ihc_vk_status' => 0,
				);
			break;	
		case 'goo':
				$arr = array(
					'ihc_goo_app_id' => '',
					'ihc_goo_app_secret' => '',
					'ihc_goo_status' => 0,
				);
			break;	
		case 'social_media':
			$arr = array(
							"ihc_sm_template" => "ihc-sm-template-1",
							"ihc_sm_custom_css" => ".ihc-sm-wrapp-fe{}",
							"ihc_sm_show_label" => 1,
							'ihc_sm_top_content' => '<div class="ihc-top-social-login"> - OR - </div>',
							'ihc_sm_bottom_content' => '',
						);
			break;	
		case 'double_email_verification':
			$arr = array(
							'ihc_double_email_expire_time' => -1,
							'ihc_double_email_redirect_success' => '',
							'ihc_double_email_redirect_error' => '',
							'ihc_double_email_delete_user_not_verified' => -1,
						);
			break;
		case 'licensing':
			$arr = array(
							'ihc_license_set' => 0,
							'ihc_envato_code' => '',		
						);
			break;
		case 'listing_users':
			$arr = array(
							'ihc_listing_users_custom_css' => '',
							'ihc_listing_users_responsive_small' => 1,
							'ihc_listing_users_responsive_medium' => 2,
							'ihc_listing_users_responsive_large' => 0,
							'ihc_listing_users_target_blank' => 0,
						);
			break;
		case 'listing_users_inside_page':
			$arr = array(
							'ihc_listing_users_inside_page_content' => '<div class="iump-user-page-avatar">
<img src="{AVATAR_HREF}" />
</div>
<div class="ihc-account-page-top-mess">
<p><span class="iump-user-page-name"> {first_name} {last_name}</span>,</p>
<p><span class="iump-user-page-mess">Username:</span><span class="iump-user-page-mess-special"> {username}</span>
</p>
<p><span class="iump-user-page-mess">and his/her awesome e-mail address is : <strong>{user_email}</strong></span></p>
{IHC_SOCIAL_MEDIA_LINKS}
</div>
<div class="iump-clear"></div>',
							'ihc_listing_users_inside_page_custom_css' => '.ihc-public-wrapp-visitor-user{

}',							
							'ihc_listing_users_inside_page_type' => 'basic',
							'ihc_listing_users_inside_page_show_avatar' => 1,
							'ihc_listing_users_inside_page_show_level' => 1,
							'ihc_listing_users_inside_page_show_banner' => 1,
							'ihc_listing_users_inside_page_show_since' => 1,
							'ihc_listing_users_inside_page_show_name' => 1,
							'ihc_listing_users_inside_page_show_username' => 1,
							'ihc_listing_users_inside_page_show_email' => 1,
							'ihc_listing_users_inside_page_show_flag' => 1,
							'ihc_listing_users_inside_page_show_custom_fields' => '',
							'ihc_listing_users_inside_page_extra_custom_content' => '',
							'ihc_listing_users_inside_page_color_scheme' => '',
							'ihc_listing_users_inside_page_template' => 'template-1',
							'ihc_listing_users_inside_page_banner_href' => '',
			);			
			break;
		case 'affiliate_options':
			$arr = array(
							'ihc_ap_show_aff_tab' => 0,
							'ihc_ap_aff_msg' => '[uap-user-become-affiliate]',
			);
			break;	
		case 'ihc_taxes_settings':
			$arr = array(
							'ihc_enable_taxes' => 0,
							'ihc_show_taxes' => 0,
							'ihc_default_tax_label' => '',
							'ihc_default_tax_value' => 0,
			); 
			break;	
		case 'admin_workflow':
			$arr = array(
							'ihc_admin_workflow_dashboard_notifications' => 1,
							'ihc_debug_payments_db' => '',
							'ihc_order_prefix_code' => 'IUMP',
			);		
			break;
		case 'public_workflow':
			$arr = array(
							'ihc_listing_show_hidden_post_pages' => 0,
							'ihc_grace_period' => '',
			);		
			break;			
		case 'ihc_woo':
			$arr = array(
							'ihc_woo_account_page_enable' => 0,
							'ihc_woo_account_page_name' => '',
							'ihc_woo_account_page_menu_position' => 5,
			);			
			break;
		case 'ihc_bp':
			$arr = array(
							'ihc_bp_account_page_enable' => 0,
							'ihc_bp_account_page_name' => '',
							'ihc_bp_account_page_position' => 5,							
			);			
			break;	
		case 'ihc_membership_card':
			$arr = array(
							'ihc_membership_card_enable' => 0,
							'ihc_membership_card_image' => IHC_URL . 'assets/images/default-logo.png',
							'ihc_membership_card_size' => 'ihc-membership-card-medium',
							'ihc_membership_card_template' => 'ihc-membership-card-2',
							'ihc_membership_member_since_enable' => 1,
							'ihc_membership_member_since_label' => __('Member Since: ', 'ihc'),
							'ihc_membership_member_level_label' => __('Level: ', 'ihc'),
							'ihc_membership_member_level_expire' => 1,
							'ihc_membership_member_level_expire_label' => __('Level Expire Date: ', 'ihc'),		
							'ihc_membership_card_custom_css' => '.ihc-membership-card-wrapp{}',		
							'ihc_membership_card_exclude_levels' => '',			
			);
			break;
		case 'ihc_cheat_off':
			$arr = array(
							'ihc_cheat_off_enable' => 0,
							'ihc_cheat_off_cookie_time' => 365,
							'ihc_cheat_off_redirect' => '',
			);
			break;
		case 'ihc_invitation_code':
			$arr = array(
							'ihc_invitation_code_enable' => 0,
							'ihc_invitation_code_err_msg' => __('Your Invitation Code is wrong.', 'ihc'),
			);
			break;
		case 'download_monitor_integration':
			$arr = array(
							'ihc_download_monitor_enabled' => 0,
							'ihc_download_monitor_limit_type' => 'files',
							'ihc_download_monitor_values' => '',
			);			
			break;
		case 'register_lite':
			$arr = array(
							'ihc_register_lite_enabled' => 1,
							'ihc_register_lite_template' => 'ihc-register-3',
							'ihc_register_lite_custom_css' => '',
							'ihc_register_lite_opt_in' => 0,
							'ihc_register_lite_opt_in_type' => '',
							'ihc_register_lite_double_email_verification' => '',
							'ihc_register_lite_user_role' => 'subscriber',
							'ihc_register_lite_auto_login' => 1,
							'ihc_register_lite_redirect' => '',
			);		
			break;
		case 'individual_page':
			$arr = array(
							'ihc_individual_page_enabled' => 0,
							'ihc_individual_page_parent' => -1,
							'ihc_individual_page_default_content' => '',
			);
			break;
		case 'level_restrict_payment':
			$arr = array(
							'ihc_level_restrict_payment_enabled' => 0,
							'ihc_levels_default_payments' => '',
							'ihc_level_restrict_payment_values' => '',							
			);
			break;
		case 'level_subscription_plan_settings':
			$arr = array(
							'ihc_level_subscription_plan_settings_enabled' => 0,	
							'ihc_show_renew_link' => 1,
							'ihc_show_delete_link' => 1,								
							'ihc_level_subscription_plan_settings_restr_levels' => '',
							'ihc_level_subscription_plan_settings_condt' => '',
			);		
			break;
		case 'gifts':
			$arr = array(
							'ihc_gifts_enabled' => 0,
							'ihc_gifts_user_get_multiple_on_recurring' => 0,
			);
			break;
		case 'login_level_redirect':
			$arr = array(
							'ihc_login_level_redirect_on' => 0,
							'ihc_login_level_redirect_rules' => '',
							'ihc_login_level_redirect_priority' => '',
			);
			break;
		case 'wp_social_login':
			$arr = array(
							'ihc_wp_social_login_on' => 0,
							'ihc_wp_social_login_redirect_page' => '',
							'ihc_wp_social_login_default_role' => '',
							'ihc_wp_social_login_default_level' => '',
			);
			break;
		case 'list_access_posts':
			$arr = array(
							'ihc_list_access_posts_on' => 0,
							'ihc_list_access_posts_title' => '',
							'ihc_list_access_posts_item_details' => 'post_title',
							'ihc_list_access_posts_custom_css' => '.iump-list-access-posts-wrapp{}',
							'ihc_list_access_posts_order_by' => 'post_date',
							'ihc_list_access_posts_order_type' => 'DESC',
							'ihc_list_access_posts_template' => '',
							'ihc_list_access_posts_order_limit' => '',
							'ihc_list_access_posts_per_page_value' => 25,
							'ihc_list_access_posts_order_post_type' => 'post,page',
							'ihc_list_access_posts_order_exclude_levels' => '',
			);
			break;
		case 'invoices':
			$arr = array(
							'ihc_invoices_on' => 0,
							'ihc_invoices_company_field' => '<div><b>Your CompanyName LLC</b></div>
<div>Unique Code: #99991239</div>
<div>Company Address: Your Email Address</div>',
							'ihc_invoices_bill_to' => '<div><b>Bill to</b></div>
<div><b>Name: </b>{first_name} {last_name} </div>
<div><b>E-mail: </b>{user_email} </div>
<div><b>Address: </b>{CUSTOM_FIELD_addr1}</div>',
							'ihc_invoices_title' => 'Your Order Invoice',
							'ihc_invoices_template' => '',
							'ihc_invoices_logo' => IHC_URL . 'assets/images/default-logo1.png',
							'ihc_invoices_custom_css' => '',
							'ihc_invoices_footer' => 'If you have any questions about this Invoice, please contact us!',
			);
			break;
		case 'woo_payment':
			$arr = array(
							'ihc_woo_payment_on' => 0,
			);
			break;	
		case 'badges':
			$arr = array(
							'ihc_badges_on' => 0,
							'ihc_badge_custom_css' => '.iump-badge-wrapper .iump-badge {
    width: 50px;
}',
			);
			break;				
			break;	
	}
	
	if ($return_default){
		//return default values
		return $arr;
	}
	
	if (isset($arr)){
		if ($only_name){
			return $arr;
		}
		foreach ($arr as $k=>$v){
			$data = get_option($k);
			if ($data!==FALSE){
				$arr[$k] = $data;
			} else {
				add_option($k, $v);
			}
		}
		return $arr;
	}
	return FALSE;
}

function ihc_native_user_field(){
	/*
	 * @param none
	 * @return array
	 */
	//$arr[] = array('display_public_reg'=>'', 'display_public_ap'=>'', 'display_admin'=>'', 'name'=>'', 'label'=>'', 'type'=>'', 'native_wp' => '', 'req' => '' );
	//order will be each key . ex: array( n=>array())
	//arr[]['display'] 0 not show, 1 show, 2 show always cannot be removed from register form
	//arr['req'] 0 not, 1 require, 2 if is selected it will be automatically require
	$arr = array(
			array( 'display_admin'=>2, 'display_public_reg'=>2, 'display_public_ap'=>2, 'name'=>'user_login', 'label'=>'Username', 'type'=>'text', 'native_wp' => 1, 'req' => 1, 'sublevel' => '' ),
			array( 'display_admin'=>2, 'display_public_reg'=>2, 'display_public_ap'=>2, 'name'=>'user_email', 'label'=>'Email', 'type'=>'text', 'native_wp' => 1, 'req' => 1, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'confirm_email', 'label'=>'Confirm Email', 'type'=>'text', 'native_wp' => 0, 'req' => 2, 'sublevel' => '' ),
			array( 'display_admin'=>1, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'first_name', 'label'=>'First Name', 'type'=>'text', 'native_wp' => 1, 'req' => 1, 'sublevel' => '' ),
			array( 'display_admin'=>1, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'last_name', 'label'=>'Last Name', 'type'=>'text', 'native_wp' => 1, 'req' => 1, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'user_url', 'label'=>'Website', 'type'=>'text', 'native_wp' => 1, 'req' => 0, 'sublevel' => '' ),
			array( 'display_admin'=>1, 'display_public_reg'=>2, 'display_public_ap'=>1, 'name'=>'pass1', 'label'=>'Password', 'type'=>'password', 'native_wp' => 1, 'req' => 1, 'sublevel' => '' ),
			array( 'display_admin'=>1, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'pass2', 'label'=>'Confirm Password', 'type'=>'password', 'native_wp' => 1, 'req' => 2, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'description', 'label'=>'Biographical Info', 'type'=>'textarea', 'native_wp' => 1, 'req' => 0, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'phone', 'label'=>'Phone', 'type'=>'number', 'native_wp' => 0, 'req' => 0, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'addr1', 'label'=>'Address 1', 'type'=>'textarea', 'native_wp' => 0, 'req' => 0, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'addr2', 'label'=>'Address 2', 'type'=>'textarea', 'native_wp' => 0, 'req' => 0, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'zip', 'label'=>'Zip', 'type'=>'text', 'native_wp' => 0, 'req' => 0, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'city', 'label'=>'City', 'type'=>'text', 'native_wp' => 0, 'req' => 0, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'thestate', 'label'=>'State', 'type'=>'text', 'native_wp' => 0, 'req' => 0, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'country', 'label'=>'Country', 'type'=>'text', 'native_wp' => 0, 'req' => 0, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'ihc_avatar', 'label'=>'Avatar', 'type'=>'upload_image', 'native_wp' => 0, 'req' => 0, 'sublevel' => '' ),			
			array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'ihc_coupon', 'label'=>'Coupon', 'type'=>'text', 'native_wp' => 0, 'req' => 0, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'ihc_social_media', 'label'=>'-', 'type'=>'social_media', 'native_wp' => 0, 'req' => 0, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'payment_select', 'label'=>'Select Payment', 'type'=>'payment_select', 'native_wp' => 0, 'req' => 0, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'ihc_country', 'label'=>'Country', 'type'=>'ihc_country', 'native_wp' => 1, 'req' => 2, 'sublevel' => '' ),
			array( 'display_admin'=>1, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'tos', 'label'=>'Accept', 'type'=>'checkbox', 'native_wp' => 0, 'req' => 2, 'sublevel' => '' ),
			array( 'display_admin'=>1, 'display_public_reg'=>1, 'display_public_ap'=>1, 'name'=>'recaptcha', 'label'=>'Capcha', 'type'=>'capcha', 'native_wp' => 0, 'req' => 2, 'sublevel' => '' ),
			array( 'display_admin'=>0, 'display_public_reg'=>1, 'display_public_ap'=>0, 'name'=>'ihc_invitation_code_field', 'label'=>'Invitation Code', 'type'=>'ihc_invitation_code_field', 'native_wp' => 0, 'req' => 2, 'sublevel' => '' ),	
			array( 'display_admin'=>0, 'display_public_reg'=>0, 'display_public_ap'=>0, 'name'=>'ihc_state', 'label'=>'State', 'type'=>'ihc_state', 'native_wp' => 1, 'req' => 2, 'sublevel' => '' ),
	);
	
	return $arr;
}

function ihc_get_user_reg_fields(){
	/*
	 * @param none
	 * @return array
	 */
	$option_name = 'ihc_user_fields';
	$data = get_option($option_name);
	if ($data!==FALSE){
		return $data;
	} else {
		$data = ihc_native_user_field();
		add_option($option_name, $data);
		return $data;
	}
}

function ihc_print_form_password($meta_arr){
	/*
	 * @param attr
	 * @return string with form for lost password
	 */
	$str = '';
	
	if($meta_arr['ihc_login_custom_css']){
		$str .= '<style>'.$meta_arr['ihc_login_custom_css'].'</style>';
	}
	
	$str .= '<div class="ihc-pass-form-wrap '.$meta_arr['ihc_login_template'].'">';
	$str .= '<form action="" method="post" >'
					. '<input name="ihcaction" type="hidden" value="reset_pass">';
	
	switch($meta_arr['ihc_login_template']){
	
	case 'ihc-login-template-3':
		$str .=  '<div class="impu-form-line-fr">'
						. '<input type="text" value="" name="email_or_userlogin" placeholder="' . __('Username or E-mail', 'ihc') . '" />'
					. '</div>'
					. '<div class="impu-form-submit">'
						. '<input type="submit" value="' . __('Get New Password', 'ihc') . '" name="Submit" class="button button-primary button-large">'
					. '</div>';
	break;
	
	case 'ihc-login-template-4':
		$str .=  '<div class="impu-form-line-fr">'
						. '<i class="fa-ihc fa-username-ihc"></i><input type="text" value="" name="email_or_userlogin" placeholder="'.__('Username or E-mail', 'ihc').'" />'
					. '</div>'
					. '<div class="impu-form-submit">'
						. '<input type="submit" value="' . __('Get New Password', 'ihc') . '" name="Submit" class="button button-primary button-large">'
					. '</div>';
	break;	
	
	case 'ihc-login-template-8':
		$str .=  '<div class="impu-form-line-fr">'
						. '<i class="fa-ihc fa-username-ihc"></i><input type="text" value="" name="email_or_userlogin" placeholder="'.__('Username or E-mail', 'ihc').'" />'
					. '</div>'
					. '<div class="impu-form-submit">'
						. '<input type="submit" value="' . __('Get New Password', 'ihc') . '" name="Submit" class="button button-primary button-large">'
					. '</div>';
	break;	
	
	case 'ihc-login-template-9':
		$str .=  '<div class="impu-form-line-fr">'
						. '<i class="fa-ihc fa-username-ihc"></i><input type="text" value="" name="email_or_userlogin" placeholder="'.__('Username or E-mail', 'ihc').'" />'
					. '</div>'
					. '<div class="impu-form-submit">'
						. '<input type="submit" value="' . __('Get New Password', 'ihc') . '" name="Submit" class="button button-primary button-large">'
					. '</div>';
	break;
	
	case 'ihc-login-template-10':
		$str .=  '<div class="impu-form-line-fr">'
						. '<i class="fa-ihc fa-username-ihc"></i><input type="text" value="" name="email_or_userlogin" placeholder="'.__('Username or E-mail', 'ihc').'" />'
					. '</div>'
					. '<div class="impu-form-submit">'
						. '<input type="submit" value="' . __('Get New Password', 'ihc') . '" name="Submit" class="button button-primary button-large">'
					. '</div>';
	break;	
						
	default:
		$str .=  '<div class="impu-form-line-fr">'
					. '<span class="impu-form-label-fr impu-form-label-username">' . __('Username or E-mail', 'ihc') . ': </span>'
						. '<input type="text" value="" name="email_or_userlogin" />'
					. '</div>'
					. '<div class="impu-form-submit">'
						. '<input type="submit" value="' . __('Get New Password', 'ihc') . '" name="Submit" class="button button-primary button-large">'
					. '</div>';
	break;
	}
	$str .=   '</form>';		
	$str .= '</div>';			
	return $str;
}

function ihc_print_form_login($meta_arr){
	/*
	 * @param array
	 * @return string
	 */
	$str = '';
	if($meta_arr['ihc_login_custom_css']){
		$str .= '<style>'.$meta_arr['ihc_login_custom_css'].'</style>';
	}
	
	$sm_string = (!empty($meta_arr['ihc_login_show_sm'])) ? ihc_print_social_media_icons('login', array(), @$meta_arr['is_locker']) : '';
	
	$str .= '<div class="ihc-login-form-wrap '.$meta_arr['ihc_login_template'].'">'
			.'<form action="" method="post" id="ihc_login_form">'
			. '<input type="hidden" name="ihcaction" value="login" />';
	
	if (!empty($meta_arr['is_locker'])){
		$str .= '<input type="hidden" name="locker" value="1" />';	
	}
	
	$captcha = '';
	if (!empty($meta_arr['ihc_login_show_recaptcha'])){
		$key = get_option('ihc_recaptcha_public');
		if ($key){
			$captcha .= '<div class="g-recaptcha-wrapper">';
			$captcha .= '<div class="g-recaptcha" data-sitekey="' . $key . '"></div>';
			$captcha .= '<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=en"></script>';				
			$captcha .= '</div>';
		}	
	}
	
	$user_field_id = 'iump_login_username';
	$password_field_id = 'iump_login_password';
	
	switch($meta_arr['ihc_login_template']){
	
	case 'ihc-login-template-2':
		//<<<< FIELDS		
		$str .= '<div class="impu-form-line-fr">' . '<span class="impu-form-label-fr impu-form-label-username">'.__('Username', 'ihc').':</span>'
				. '<input type="text" value="" name="log" id="' . $user_field_id . '" />'
				. '</div>'
				. '<div class="impu-form-line-fr">' . '<span class="impu-form-label-fr impu-form-label-pass">'.__('Password', 'ihc').':</span>'
				. '<input type="password" value="" name="pwd" id="' . $password_field_id . '" />'
				. '</div>';
		//>>>>
		$str .= $sm_string;			
		//<<<< REMEMBER ME			
		if($meta_arr['ihc_login_remember_me']){
			$str .= '<div class="impu-form-line-fr impu-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="impu-form-input-remember" /><span class="impu-form-label-fr impu-form-label-remember">'.__('Remember Me', 'ihc').'</span> </div>';
		}
		//>>>>
		
		//<<<< ADDITIONAL LINKS
		if($meta_arr['ihc_login_register'] || $meta_arr['ihc_login_pass_lost']){
		$str .= '<div class="impu-form-line-fr impu-form-links">';
			if($meta_arr['ihc_login_register']){
				$pag_id = get_option('ihc_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="impu-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'ihc').'</a></div>';
				}
			}
			if($meta_arr['ihc_login_pass_lost']){
				$pag_id = get_option('ihc_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="impu-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'ihc').'</a></div>';
				}
			}
		$str .= '</div>';
		}
		//>>>>
		
		$str .= $captcha;
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .=    '<div class="impu-form-line-fr impu-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'ihc').'" name="Submit" '.$disabled.'/>'
				 . '</div>';
		//>>>>
	break;
		
	case 'ihc-login-template-3':
		//<<<< FIELDS		
		$str .= '<div class="impu-form-line-fr">'
				. '<input type="text" value="" name="log" id="' . $user_field_id . '" placeholder="'.__('Username', 'ihc').'"/>'
				. '</div>'
				. '<div class="impu-form-line-fr">'
				. '<input type="password" value="" id="' . $password_field_id . '" name="pwd" placeholder="'.__('Password', 'ihc').'"/>'
				. '</div>';
		//>>>>
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}

		$str .= $captcha;

		$str .=    '<div class="impu-form-line-fr impu-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'ihc').'" name="Submit" '.$disabled.'/>'
				 . '</div>';
		
		$str .= $sm_string;
		$str .= '<div class="impu-temp3-bottom">';		 
		//<<<< REMEMBER ME			
		if($meta_arr['ihc_login_remember_me']){
			$str .= '<div class="impu-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="impu-form-input-remember" /><span class="impu-form-label-remember">'.__('Remember Me', 'ihc').'</span> </div>';
		}
		//>>>>
		
		//<<<< ADDITIONAL LINKS
		if($meta_arr['ihc_login_register'] || $meta_arr['ihc_login_pass_lost']){
		$str .= '<div  class="impu-form-links">';
			if($meta_arr['ihc_login_register']){
				$pag_id = get_option('ihc_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="impu-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'ihc').'</a></div>';
				}
			}
			if($meta_arr['ihc_login_pass_lost']){
				$pag_id = get_option('ihc_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="impu-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'ihc').'</a></div>';
				}
			}
			
			$str .= '</div>';
		}
		//>>>>	
		$str .= '<div class="iump-clear"></div>';
		$str .= '</div>';
		
		break;
		
	case 'ihc-login-template-4':
		//<<<< FIELDS		
		$str .= '<div class="impu-form-line-fr">'
				. '<i class="fa-ihc fa-username-ihc"></i><input type="text" value="" id="' . $user_field_id . '" name="log" placeholder="'.__('Username', 'ihc').'"/>'
				. '</div>'
				. '<div class="impu-form-line-fr">'
				. '<i class="fa-ihc fa-pass-ihc"></i><input type="password" value="" id="' . $password_field_id . '" name="pwd" placeholder="'.__('Password', 'ihc').'"/>'
				. '</div>';
		//>>>>
		//<<<< REMEMBER ME			
		if($meta_arr['ihc_login_remember_me']){
			$str .= '<div class="impu-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="impu-form-input-remember" /><span class="impu-form-label-remember">'.__('Remember Me', 'ihc').'</span> </div>';
		}
		//>>>>
		
		$str .= $captcha;
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .=    '<div class="impu-form-line-fr impu-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'ihc').'" name="Submit" '.$disabled.' />'
				 . '</div>';
		
		$str .= $sm_string;		 
		//<<<< ADDITIONAL LINKS
		if($meta_arr['ihc_login_register'] || $meta_arr['ihc_login_pass_lost']){
		$str .= '<div  class="impu-form-links">';
			if($meta_arr['ihc_login_register']){
				$pag_id = get_option('ihc_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="impu-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'ihc').'</a></div>';
				}
			}
			if($meta_arr['ihc_login_pass_lost']){
				$pag_id = get_option('ihc_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="impu-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'ihc').'</a></div>';
				}
			}
			
		$str .= '</div>';
		}
		//>>>>
		
		break;
	case 'ihc-login-template-5':	
		//<<<< FIELDS		
		$str .= '<div class="impu-form-line-fr">' . '<span class="impu-form-label-fr impu-form-label-username">'.__('Username', 'ihc').':</span>'
				. '<input type="text" value="" id="' . $user_field_id . '" name="log" />'
				. '</div>'
				. '<div class="impu-form-line-fr">' . '<span class="impu-form-label-fr impu-form-label-pass">'.__('Password', 'ihc').':</span>'
				. '<input type="password" value="" id="' . $password_field_id . '" name="pwd" />'
				. '</div>';
		//>>>>
		$str .=    '<div class="impu-temp5-row">';	
		$str .=    '<div class="impu-temp5-row-left">';		
		//<<<< REMEMBER ME			
		if($meta_arr['ihc_login_remember_me']){
			$str .= '<div class="impu-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="impu-form-input-remember" /><span class="impu-form-label-fr impu-form-label-remember">'.__('Remember Me', 'ihc').'</span> </div>';
		}
		//>>>>
		//<<<< ADDITIONAL LINKS
		if($meta_arr['ihc_login_register'] || $meta_arr['ihc_login_pass_lost']){
		$str .= '<div  class="impu-form-line-fr impu-form-links">';
			if($meta_arr['ihc_login_register']){
				$pag_id = get_option('ihc_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="impu-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'ihc').'</a></div>';
				}
			}
			if($meta_arr['ihc_login_pass_lost']){
				$pag_id = get_option('ihc_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="impu-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'ihc').'</a></div>';
				}
			}
		$str .= '</div>';
		}
		//>>>>
		$str .= '</div>';
		
		$str .= $captcha;
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .=    '<div class="impu-form-line-fr impu-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'ihc').'" name="Submit" '.$disabled.'/>'
				 . '</div>';
		//>>>>
		$str .= '<div class="iump-clear"></div>';
		
		
		$str .= $sm_string;
		
		$str .= '</div>';	
		
		break;
		case 'ihc-login-template-6':	
		//<<<< FIELDS		
		$str .= '<div class="impu-form-line-fr">' . '<span class="impu-form-label-fr impu-form-label-username">'.__('Username', 'ihc').':</span>'
				. '<input type="text" value="" name="log" id="' . $user_field_id . '" />'
				. '</div>'
				. '<div class="impu-form-line-fr">' . '<span class="impu-form-label-fr impu-form-label-pass">'.__('Password', 'ihc').':</span>'
				. '<input type="password" value="" id="' . $password_field_id . '" name="pwd" />'
				. '</div>';
		//>>>>
		$str .= $sm_string;
		//<<<< ADDITIONAL LINKS
		if($meta_arr['ihc_login_register'] || $meta_arr['ihc_login_pass_lost']){
		$str .= '<div  class="impu-form-links">';
			if($meta_arr['ihc_login_register']){
				$pag_id = get_option('ihc_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="impu-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'ihc').'</a></div>';
				}
			}
			if($meta_arr['ihc_login_pass_lost']){
				$pag_id = get_option('ihc_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="impu-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'ihc').'</a></div>';
				}
			}
		$str .= '</div>';
		}
		//>>>>
		$str .=    '<div class="impu-temp6-row">';	
		$str .=    '<div class="impu-temp6-row-left">';		
		//<<<< REMEMBER ME			
		if($meta_arr['ihc_login_remember_me']){
			$str .= '<div class="impu-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="impu-form-input-remember" /><span class="impu-form-label-fr impu-form-label-remember">'.__('Remember Me', 'ihc').'</span> </div>';
		}
		//>>>>
		
		$str .= '</div>';
		
		$str .= $captcha;
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .=    '<div class="impu-form-line-fr impu-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'ihc').'" name="Submit" '.$disabled.'/>'
				 . '</div>';
		//>>>>
		$str .= '<div class="iump-clear"></div>';
		$str .= '</div>';	
		
		break;	
		
		case 'ihc-login-template-7':	
		//<<<< FIELDS		
		$str .= '<div class="impu-form-line-fr">' . '<span class="impu-form-label-fr impu-form-label-username">'.__('Username', 'ihc').':</span>'
				. '<input type="text" value="" name="log" id="' . $user_field_id . '"/>'
				. '</div>'
				. '<div class="impu-form-line-fr">' . '<span class="impu-form-label-fr impu-form-label-pass">'.__('Password', 'ihc').':</span>'
				. '<input type="password" value="" id="' . $password_field_id . '" name="pwd" />'
				. '</div>';
		//>>>>
		$str .= $sm_string;
		$str .=    '<div class="impu-temp5-row">';	
		$str .=    '<div class="impu-temp5-row-left">';		
		//<<<< REMEMBER ME			
		if($meta_arr['ihc_login_remember_me']){
			$str .= '<div class="impu-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="impu-form-input-remember" /><span class="impu-form-label-fr impu-form-label-remember">'.__('Remember Me', 'ihc').'</span> </div>';
		}
		//>>>>
		//<<<< ADDITIONAL LINKS
		if($meta_arr['ihc_login_register'] || $meta_arr['ihc_login_pass_lost']){
		$str .= '<div  class="impu-form-links">';
			if($meta_arr['ihc_login_register']){
				$pag_id = get_option('ihc_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="impu-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'ihc').'</a></div>';
				}
			}
			if($meta_arr['ihc_login_pass_lost']){
				$pag_id = get_option('ihc_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="impu-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'ihc').'</a></div>';
				}
			}
		$str .= '</div>';
		}
		//>>>>
		$str .= '</div>';
		
		$str .= $captcha;
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .=    '<div class="impu-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'ihc').'" name="Submit" '.$disabled.'/>'
				 . '</div>';
		//>>>>
		$str .= '<div class="iump-clear"></div>';
		$str .= '</div>';	
		
		break;
		
	case 'ihc-login-template-8':
		//<<<< FIELDS		
		$str .= '<div class="impu-form-line-fr">'
				. '<i class="fa-ihc fa-username-ihc"></i><input type="text" value="" id="' . $user_field_id . '" name="log" placeholder="'.__('Username', 'ihc').'"/>'
				. '</div>'
				. '<div class="impu-form-line-fr">'
				. '<i class="fa-ihc fa-pass-ihc"></i><input type="password" value="" id="' . $password_field_id . '" name="pwd" placeholder="'.__('Password', 'ihc').'"/>'
				. '</div>';
		//>>>>
		//<<<< REMEMBER ME			
		if($meta_arr['ihc_login_remember_me']){
			$str .= '<div class="impu-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="impu-form-input-remember" /><span class="impu-form-label-remember">'.__('Remember Me', 'ihc').'</span> </div>';
		}
		//>>>>
		
		$str .= $captcha;
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .=    '<div class="impu-form-line-fr impu-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'ihc').'" name="Submit" '.$disabled.' />'
				 . '</div>';
				 
		$str .= $sm_string;
		
		//<<<< ADDITIONAL LINKS
		if($meta_arr['ihc_login_register'] || $meta_arr['ihc_login_pass_lost']){
		$str .= '<div  class="impu-form-links">';
			if($meta_arr['ihc_login_register']){
				$pag_id = get_option('ihc_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="impu-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'ihc').'</a></div>';
				}
			}
			if($meta_arr['ihc_login_pass_lost']){
				$pag_id = get_option('ihc_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="impu-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'ihc').'</a></div>';
				}
			}
			
		$str .= '</div>';
		}
		//>>>>
		
		break;	
	case 'ihc-login-template-9':
		//<<<< FIELDS		
		$str .= '<div class="impu-form-line-fr">'
				. '<i class="fa-ihc fa-username-ihc"></i><input type="text" value="" id="' . $user_field_id . '" name="log" placeholder="'.__('Username', 'ihc').'"/>'
				. '</div>'
				. '<div class="impu-form-line-fr">'
				. '<i class="fa-ihc fa-pass-ihc"></i><input type="password" value="" id="' . $password_field_id . '" name="pwd" placeholder="'.__('Password', 'ihc').'"/>'
				. '</div>';
		//>>>>
		//<<<< REMEMBER ME			
		if($meta_arr['ihc_login_remember_me']){
			$str .= '<div class="impu-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="impu-form-input-remember" /><span class="impu-form-label-remember">'.__('Remember Me', 'ihc').'</span> </div>';
		}
		//>>>>
		if($meta_arr['ihc_login_pass_lost']){
				$pag_id = get_option('ihc_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="impu-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'ihc').'</a></div>';
				}
			}
		$str .= '<div class="ihc-clear"></div>';
		$str .= $captcha;
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .=    '<div class="impu-form-line-fr impu-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'ihc').'" name="Submit" '.$disabled.' />'
				 . '</div>';
		
		$str .= $sm_string;		 
		//<<<< ADDITIONAL LINKS
		if($meta_arr['ihc_login_register'] || $meta_arr['ihc_login_pass_lost']){
		$str .= '<div  class="impu-form-links">';
			if($meta_arr['ihc_login_register']){
				$pag_id = get_option('ihc_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="impu-form-links-reg">'.__('Dont have an account?', 'ihc').'<a href="'.$register_page.'">'.__('Sign Up', 'ihc').'</a></div>';
				}
			}
			
			
		$str .= '</div>';
		}
		//>>>>
		
		break;	
	case 'ihc-login-template-10':
		//<<<< FIELDS		
		$str .= '<div class="impu-form-line-fr">'
				. '<i class="fa-ihc fa-username-ihc"></i><input type="text" value="" id="' . $user_field_id . '" name="log" placeholder="'.__('Username', 'ihc').'"/>'
				. '</div>'
				. '<div class="impu-form-line-fr">'
				. '<i class="fa-ihc fa-pass-ihc"></i><input type="password" value="" id="' . $password_field_id . '" name="pwd" placeholder="'.__('Password', 'ihc').'"/>'
				. '</div>';
		//>>>>
		//<<<< REMEMBER ME			
		if($meta_arr['ihc_login_remember_me']){
			$str .= '<div class="impu-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="impu-form-input-remember" /><span class="impu-form-label-remember">'.__('Remember Me', 'ihc').'</span> </div>';
		}
		//>>>>
		if($meta_arr['ihc_login_pass_lost']){
				$pag_id = get_option('ihc_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="impu-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'ihc').'</a></div>';
				}
			}
		$str .= '<div class="ihc-clear"></div>';
		$str .= $captcha;
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .=    '<div class="impu-form-line-fr impu-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'ihc').'" name="Submit" '.$disabled.' />'
				 . '</div>';
		
		$str .= $sm_string;		 
		//<<<< ADDITIONAL LINKS
		if($meta_arr['ihc_login_register'] || $meta_arr['ihc_login_pass_lost']){
		$str .= '<div  class="impu-form-links">';
			if($meta_arr['ihc_login_register']){
				$pag_id = get_option('ihc_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="impu-form-links-reg">'.__('Dont have an account?', 'ihc').'<a href="'.$register_page.'">'.__('Sign Up', 'ihc').'</a></div>';
				}
			}
		$str .= '</div>';
		}
		//>>>>
		
		break;						
	default:			
		//<<<< FIELDS		
		$str .= '<div class="impu-form-line-fr">' . '<span class="impu-form-label-fr impu-form-label-username">'.__('Username', 'ihc').':</span>'
				. '<input type="text" value="" name="log" id="' . $user_field_id . '" />'
				. '</div>'
				. '<div class="impu-form-line-fr">' . '<span class="impu-form-label-fr impu-form-label-pass">'.__('Password', 'ihc').':</span>'
				. '<input type="password" value="" name="pwd" id="' . $password_field_id . '" />'
				. '</div>';
		//>>>>
		$str .= $sm_string;	
		//<<<< REMEMBER ME			
		if($meta_arr['ihc_login_remember_me']){
			$str .= '<div class="impu-form-line-fr impu-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="impu-form-input-remember" /><span class="impu-form-label-fr impu-form-label-remember">'.__('Remember Me').'</span> </div>';
		}
		//>>>>
		
		//<<<< ADDITIONAL LINKS
		if($meta_arr['ihc_login_register'] || $meta_arr['ihc_login_pass_lost']){
		$str .= '<div  class="impu-form-line-fr impu-form-links">';
			if($meta_arr['ihc_login_register']){
				$pag_id = get_option('ihc_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="impu-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'ihc').'</a></div>';
				}
			}
			if($meta_arr['ihc_login_pass_lost']){
				$pag_id = get_option('ihc_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="impu-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'ihc').'</a></div>';
				}
			}
		$str .= '</div>';
		}
		//>>>>
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		
		$str .= $captcha;

		$str .=    '<div class="impu-form-line-fr impu-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'ihc').'" name="Submit" '.$disabled.' class="button button-primary button-large"/>'
				 . '</div>';
		//>>>>
		break;
	}
	
	$str .=   '</form>';
	
	/// ERROR MESSAGE
	 if (!empty($_GET['ihc_pending_email'])){
		/************************ PENDING EMAIL ********************/
		$login_faild = get_option('ihc_login_error_email_pending', true);
		if (empty($login_faild)){
			$arr = ihc_return_meta_arr('login-messages', false, true);
			//print_r($arr);
			if (isset($arr['ihc_login_error_email_pending']) && $arr['ihc_login_error_email_pending']){
				$login_faild = $arr['ihc_login_error_email_pending'];
			} else {
				$login_faild = __('Error', 'ihc');
			}
		}
		$str .= '<div class="ihc-login-error-wrapper"><div class="ihc-login-error">' . ihc_correct_text($login_faild) . '</div></div>';
	} else if (!empty($_GET['ihc_login_fail'])){
		/************************** FAIL *****************************/
		$login_faild = ihc_correct_text( get_option('ihc_login_error', true) );
		if (empty($login_faild)){
			$arr = ihc_return_meta_arr('login-messages', false, true);
			if (isset($arr['ihc_login_error']) && $arr['ihc_login_error']){
				$login_faild = $arr['ihc_login_error'];
			} else {
				$login_faild = __('Error', 'ihc');
			}			
		}
		$str .= '<div class="ihc-login-error-wrapper"><div class="ihc-login-error">' . ihc_correct_text($login_faild) . '</div></div>';
	} else if (!empty($_GET['ihc_login_pending'])){
		/*********************** PENDING ******************************/
		$str .= '<div class="ihc-login-pending">' . ihc_correct_text(get_option('ihc_login_pending', true)) . '</div>';
	} else if (!empty($_GET['ihc_fail_captcha'])){
		$login_faild = ihc_correct_text(get_option('ihc_login_error_on_captcha'));
		if (!$login_faild){
			$login_faild = __('Captcha Error', 'uap');
		}
		$str .= '<div class="ihc-login-error-wrapper"><div class="ihc-login-error">' . $login_faild . '</div></div>';
	}
	/// ERROR MESSAGE
	
	$str .= '</div>';
	
	$err_msg = __('Please complete all require fields!', 'ihc');
	$custom_err_msg = get_option('ihc_login_error_ajax');
	if ($custom_err_msg){
		$err_msg = $custom_err_msg;
	}
	$str .= "<script>
		jQuery(document).ready(
			function(){
				jQuery('#$user_field_id').on('blur', function(){
					ihc_check_login_field('log', '$err_msg');
				});	
				jQuery('#$password_field_id').on('blur', function(){
					ihc_check_login_field('pwd', '$err_msg');
				});		
				jQuery('#ihc_login_form').on('submit', function(e){
					e.preventDefault();
					var u = jQuery('#ihc_login_form [name=log]').val();
					var p = jQuery('#ihc_login_form [name=pwd]').val();
					if (u!='' && p!=''){
						jQuery('#ihc_login_form').unbind('submit').submit();
					} else {
						ihc_check_login_field('log', '$err_msg');
						ihc_check_login_field('pwd', '$err_msg');
						return FALSE;
					}
				});		
			}
		);
	</script>";
			
	return $str;
}


function ihc_print_social_media_icons($type='login', $already_registered_sm=array(), $is_locker=FALSE){
	/*
	 * @param string (login, register, update), array, bool
	 * @return string
	 */

	//$current_url = IHC_PROTOCOL . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	$current_url = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$metas = ihc_return_meta_arr('social_media');
	
	$arr = array(
			"fb" => "Facebook",
			"tw" => "Twitter",
			"goo" => "Google",
			"in" => "LinkedIn",
			"vk" => "Vkontakte",
			"ig" => "Instagram",
			"tbr" => "Tumblr",
	);
	
	$str = '';
	foreach ($arr as $k=>$v){
		$data = ihc_check_social_status($k);
		$label = (empty($metas['ihc_sm_show_label'])) ? "" : '<span class="ihc-sm-item-label">'.$v.'</span>';
		
		if ($data['settings']=='Completed' && $data['active']){
			$extra_class = 'ihc-' . $k;
			$icon = '<i class="fa-ihc-sm fa-ihc-' . $k . '"></i>';
			if ($type=='login'){
				$href = IHC_URL . 'public/social_handler.php?sm_login=' . $k . '&ihc_current_url=' . urlencode($current_url);
				if (!empty($is_locker)){
					$href .= '&is_locker=1';
				}
				$str .= '<div class="ihc-sm-item ' . $extra_class . '"><a href="' . $href . '">' . $icon . $label . '</a></div>';				
			} else if ($type=='register'){
				$str .= '<div onClick="ihc_run_social_reg(\''.$k.'\');" class="ihc-sm-item ' . $extra_class . '">' . $icon . $label . '<div class="iump-clear"></div></div>';
			} else if ($type=='update'){
				$already_class = '';
				if ($already_registered_sm && in_array($k, $already_registered_sm)){
					$already_class = ' ihc-sm-already-reg';
					$str .= '<div class="ihc-sm-item ' . $extra_class . ' ' . $already_class . '"><a href="javascript:void(0)" onClick="ihc_remove_social(\'' . $k . '\');">' . $icon . $label . '<div class="iump-clear"></div></a></div>';	
				} else {
					$href = IHC_URL . 'public/social_handler.php?reg_ext_usr=' . $k . '&ihc_current_url=' . urlencode($current_url);
					$str .= '<div class="ihc-sm-item ' . $extra_class . '"><a href="' . $href . '">' . $icon . $label . '<div class="iump-clear"></div></a></div>';					
				}
			}
		}
	}
	if ($str){
		if ($type=='login'){
			$str = '<div>' . ihc_correct_text($metas['ihc_sm_top_content']) . '</div>' . $str . '<div>' . ihc_correct_text($metas['ihc_sm_bottom_content']) . '</div>';	
		}	
		$str = '<div class="ihc-sm-wrapp-fe ' . @$metas['ihc_sm_template'] . '">' . $str . '</div>';
		if (!empty($metas['ihc_sm_custom_css'])){
			$str = '<style>' . $metas['ihc_sm_custom_css'] . '</style>' . $str;
		}
	}
	return $str;
}

function ihc_print_links_login(){
	/*
	 * @param none
	 * @return string
	 */
	$str ='';
	$str .= '<div  class="impu-form-line-fr impu-form-links">';
				$pag_id = get_option('ihc_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="impu-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'ihc').'</a></div>';
				}

			
				$pag_id = get_option('ihc_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="impu-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'ihc').'</a></div>';
				}

		$str .= '</div>';
	return $str;
}

function ihc_get_level_by_id($id){
	/*
	 * @param int
	 * @return array|bool
	 */
	$data = get_option('ihc_levels');
	if ($data!==FALSE){
		foreach ($data as $k=>$v){
			if ((int)$k==(int)$id){
				return $v;
			}
		}
	}
	return FALSE;
}

function ihc_format_str_like_wp( $str ){
	/*
	 * @param string
	 * @return string
	 */
	$str = preg_replace("/\n\n+/", "\n\n", $str);
	$str_arr = preg_split('/\n\s*\n/', $str, -1, PREG_SPLIT_NO_EMPTY);
	$str = '';

	foreach ( $str_arr as $str_val ) {
		$str .= '<p>' . trim($str_val, "\n") . "</p>\n";
	}
	return $str;
}

function ihc_array_value_exists($haystack, $needle, $key){
	/*
	 * @param array, string, string
	 * @return string|int, bool 
	 */
	foreach ($haystack as $k=>$v){
		if ($v[$key]==$needle){
			return $k;
		}
	}
	return FALSE;
}

function ihc_is_array_value_multi_exists($haystack=array(), $needle='', $key=''){
	/*
	 * @param array, string, string
	 * @return int
	 */
	$c = 0;
	foreach ($haystack as $k=>$v){
		if ($v[$key]==$needle){
			$c++;
		}
	}
	return $c;
}

function ihc_array_key_recursive($arr, $key){
	/*
	 * @param array, string|int
	 * @return string|int, bool
	 */
	foreach ($arr as $k=>$v){
		if (array_key_exists($key, $v)) return $k;
	}
	return FALSE;
}


function ihc_correct_text($str, $wp_editor_content=false){
	/*
	 * @param string, bool
	 * @return string
	 */
	$str = stripcslashes(htmlspecialchars_decode($str));
	if ($wp_editor_content){
		return ihc_format_str_like_wp($str);
	}
	return $str;
}

///////////forms utility

function indeed_create_form_element($attr=array()){
	/*
	 * @param string
	 * @return string
	 */
	foreach (array('name', 'id', 'value', 'class', 'other_args', 'disabled', 'placeholder', 'multiple_values', 'user_id', 'sublabel') as $k){
		if (!isset($attr[$k])){
			$attr[$k] = '';
		}
	}
	
	$str = '';
	if (isset($attr['type']) && $attr['type']){
		switch ($attr['type']){
			case 'text':
			case 'conditional_text':
				$str = '<input type="text" name="'.$attr['name'].'" id="'.$attr['id'].'" class="'.$attr['class'].'" value="' . ihc_correct_text($attr['value']) . '" placeholder="'.$attr['placeholder'].'" '.$attr['other_args'].' '.$attr['disabled'].' />';
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}
				break;
		
			case 'number':
				foreach (array('max', 'min') as $k){
					if (!isset($attr[$k])){
						$attr[$k] = '';
					}
				}				
				$str = '<input type="number" name="'.$attr['name'].'" id="'.$attr['id'].'" class="'.$attr['class'].'" value="'.$attr['value'].'"  '.$attr['other_args'].' '.$attr['disabled'].' min="' . $attr['min'] . '" max="' . $attr['max'] . '" />';
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}				
				break;
		
			case 'textarea':
				$str = '<textarea name="'.$attr['name'].'" id="'.$attr['id'].'" class="iump-form-textarea '.$attr['class'].'" '.$attr['other_args'].' '.$attr['disabled'].' >' . ihc_correct_text($attr['value']) . '</textarea>';
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}				
				break;
		
			case 'password':
				$str = '<input type="password" name="'.$attr['name'].'" id="'.$attr['id'].'" class="'.$attr['class'].'" value="'.$attr['value'].'" placeholder="'.$attr['placeholder'].'" '.$attr['other_args'].' />';
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}				
				break;
		
			case 'hidden':
				$str = '<input type="hidden" name="'.$attr['name'].'" id="'.$attr['id'].'" class="'.$attr['class'].'" value="'.$attr['value'].'" '.$attr['other_args'].' />';
				break;
		
			case 'checkbox':
				$str = '';
				if ($attr['multiple_values']){
					$id = 'ihc_checkbox_parent_' . rand(1,1000);
					$str .= '<div class="iump-form-checkbox-wrapper" id="' . $id . '">';
					foreach ($attr['multiple_values'] as $v){
						if (is_array($attr['value'])){
							$checked = (in_array($v, $attr['value'])) ? 'checked' : '';
						} else {
							$checked = ($v==$attr['value']) ? 'checked' : '';
						}
						$str .= '<div class="iump-form-checkbox">';
						$str .= '<input type="checkbox" name="'.$attr['name'].'[]" id="'.$attr['id'].'" class="'.$attr['class'].'" value="' . ihc_correct_text($v) . '" '.$checked.' '.$attr['other_args'].' '.$attr['disabled'].'  />';
						$str .= ihc_correct_text($v);
						$str .= '</div>';
					}
					$str .= '</div>';
				}
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}
				break;
		
			case 'radio':
				$str = '';
				if ($attr['multiple_values']){
					$id = 'ihc_radio_parent_' . rand(1,1000);
					$str .= '<div class="iump-form-radiobox-wrapper" id="' . $id . '">';
					foreach ($attr['multiple_values'] as $v){
						$checked = ($v==$attr['value']) ? 'checked' : '';
						$str .= '<div class="iump-form-radiobox">';
						$str .= '<input type="radio" name="'.$attr['name'].'" id="'.$attr['id'].'" class="'.$attr['class'].'" value="' . ihc_correct_text($v) . '" '.$checked.' '.$attr['other_args'].' '.$attr['disabled'].'  />';
						$str .= ihc_correct_text($v);
						$str .= '</div>';
					}
					$str .= '</div>';
				}
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}
				break;
		
			case 'select':
				$str = '';		
				if ($attr['multiple_values']){
					$str .= '<select name="'.$attr['name'].'" id="'.$attr['id'].'" class="iump-form-select '.$attr['class'].'" '.$attr['other_args'].' '.$attr['disabled'].' >';
					if ($attr['multiple_values']){
						foreach ($attr['multiple_values'] as $k=>$v){
							$selected = ($k==$attr['value']) ? 'selected' : '';
							$str .= '<option value="'.$k.'" '.$selected.'>' . ihc_correct_text($v) . '</option>';
						}						
					}
					$str .= '</select>';
				}
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}
				break;
				
			case 'multi_select':
				$str = '';
				if ($attr['multiple_values']){
					$str .= '<select name="'.$attr['name'].'[]" id="'.$attr['id'].'" class="iump-form-multiselect '.$attr['class'].'" '.$attr['other_args'].' '.$attr['disabled'].' multiple>';
					foreach ($attr['multiple_values'] as $k=>$v){
						if (is_array($attr['value'])){
							$selected = (in_array($v, $attr['value'])) ? 'selected' : '';
						} else {
							$selected = ($v==$attr['value']) ? 'selected' : '';
						}
						$str .= '<option value="'.$k.'" '.$selected.'>' . ihc_correct_text($v) . '</option>';
					}
					$str .= '</select>';
				}	
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}			
				break;
		
			case 'submit':
				$str = '<input type="submit" value="' . ihc_correct_text($attr['value']) . '" name="'.$attr['name'].'" id="'.$attr['id'].'" class="'.$attr['class'].'" '.$attr['other_args'].' '.$attr['disabled'].' />';
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}				
				break;
				
			case 'date':
				if (empty($attr['class'])){
					$attr['class'] = 'ihc-date-field';
				}
				$str = '';
				
				global $ihc_jquery_ui_min_css;
				if (empty($ihc_jquery_ui_min_css)){
					$ihc_jquery_ui_min_css = TRUE;
					$str .= '<link rel="stylesheet" type="text/css" href="' . IHC_URL . 'admin/assets/css/jquery-ui.min.css"/>' ;	
				}
				
				if (empty($attr['callback'])){
					$attr['callback'] = '';
				}
				
				$str .= '<script>
					jQuery(document).ready(function() {
						var currentYear = new Date().getFullYear();
						jQuery(".'.$attr['class'].'").datepicker({
							dateFormat : "dd-mm-yy",
							changeMonth: true,
						    changeYear: true,
							yearRange: "1900:"+currentYear,
							onClose: function(r) {
								' . $attr['callback'] . '
							}
					});
				});
				</script>
				';
				$str .= '<input type="text" value="'.$attr['value'].'" name="'.$attr['name'].'" id="'.$attr['id'].'" class="iump-form-datepicker '.$attr['class'].'" '.$attr['other_args'].' '.$attr['disabled'].'   placeholder="'.$attr['placeholder'].'" />';
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}				
				break;	

			case 'file':
				$upload_settings = ihc_return_meta_arr('extra_settings');
				$max_size = $upload_settings['ihc_upload_max_size'] * 1000000; 
				$rand = rand(1,10000);
				$str .= '<div id="ihc_fileuploader_wrapp_' . $rand . '" class="ihc-wrapp-file-upload  ihc-wrapp-file-field" style=" vertical-align: text-top;">';
				$str .= '<div class="ihc-file-upload ihc-file-upload-button">Upload</div>
						<script>						
							jQuery(document).ready(function() {
								jQuery("#ihc_fileuploader_wrapp_' . $rand . ' .ihc-file-upload").uploadFile({
									onSelect: function (files) {		
											jQuery("#ihc_fileuploader_wrapp_' . $rand . ' .ajax-file-upload-container").css("display", "block");									
											var check_value = jQuery("#ihc_upload_hidden_'.$rand.'").val();
											if (check_value!="" ){
												alert("To add a new image please remove the previous one!");
												return false;
											}	
                							return true;
            						},
									url: "'.IHC_URL.'public/ajax-upload.php",
									fileName: "ihc_file",
									dragDrop: false,
									showFileCounter: false,
									showProgress: true,
									showFileSize: false,
									maxFileSize: ' . $max_size . ',
									allowedTypes: "' . $upload_settings['ihc_upload_extensions'] . '",
									onSuccess: function(a, response, b, c){					
										if (response){
											var obj = jQuery.parseJSON(response);	
											jQuery("#ihc_fileuploader_wrapp_' . $rand . ' .ihc-file-upload").prepend("<div onClick=\"ihc_delete_file_via_ajax("+obj.id+", -1, \'#ihc_fileuploader_wrapp_' . $rand . '\', \'' . $attr['name'] . '\', \'#ihc_upload_hidden_'.$rand.'\');\" class=\'ihc-delete-attachment-bttn\'>Remove</div>");
											switch (obj.type){
												case "image":
													jQuery("#ihc_fileuploader_wrapp_' . $rand . ' .ihc-file-upload").prepend("<img src="+obj.url+" class=\'ihc-member-photo\' /><div class=\'ihc-clear\'></div>");
												break;
												case "other":
													jQuery("#ihc_fileuploader_wrapp_' . $rand . ' .ihc-file-upload").prepend("<div class=ihc-icon-file-type></div><div class=ihc-file-name-uploaded>"+obj.name+"</div>");
												break;
											}
											jQuery("#ihc_upload_hidden_'.$rand.'").val(obj.id);
											setTimeout(function(){
												jQuery("#ihc_fileuploader_wrapp_' . $rand . ' .ajax-file-upload-container").css("display", "none");
											}, 3000);										
										}
									}
								});
							});
						</script>';
				if ($attr['value']){
					$attachment_type = ihc_get_attachment_details($attr['value'], 'extension');	
					$url = wp_get_attachment_url($attr['value']);				
					switch ($attachment_type){
						case 'jpg':
						case 'jpeg':
						case 'png':
						case 'gif':
							//print the picture
							$str .= '<img src="' . $url . '" class="ihc-member-photo" /><div class="ihc-clear"></div>';
							break;
						default:
							//default file type
							$str .= '<div class="ihc-icon-file-type"></div>';
							break;
					}
					$attachment_name = ihc_get_attachment_details($attr['value']);
					$str .= '<div class="ihc-file-name-uploaded"><a href="' . $url . '" target="_blank">' . $attachment_name . '</a></div>';
					$str .= '<div onClick=\'ihc_delete_file_via_ajax(' . $attr['value'] . ', '.$attr['user_id'].', "#ihc_fileuploader_wrapp_' . $rand . '", "' . $attr['name'] . '", "#ihc_upload_hidden_' . $rand . '");\' class="ihc-delete-attachment-bttn">Remove</div>';
				}
				$str .= '<input type="hidden" value="'.$attr['value'].'" name="' . $attr['name'] . '" id="ihc_upload_hidden_'.$rand.'" />';
				$str .= "</div>";
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}
				break;
				

			case 'upload_image':
				$upload_settings = ihc_return_meta_arr('extra_settings');
				$max_size = $upload_settings['ihc_avatar_max_size'] * 1000000;
				$rand = rand(1,10000);
				$str .= '<div id="ihc_fileuploader_wrapp_' . $rand . '" class="ihc-wrapp-file-upload" style=" vertical-align: text-top;">';
								$str .= '		<script>						
							jQuery(document).ready(function() {
								jQuery("#ihc_fileuploader_wrapp_' . $rand . ' .ihc-file-upload").uploadFile({
									onSelect: function (files) {			
											jQuery("#ihc_fileuploader_wrapp_' . $rand . ' .ajax-file-upload-container").css("display", "block");						
											var check_value = jQuery("#ihc_upload_hidden_'.$rand.'").val();
											if (check_value!="" ){
												alert("To add a new image please remove the previous one!");
												return false;
											}	
                							return true;
            						},
									url: "'.IHC_URL.'public/ajax-upload.php",
									allowedTypes: "jpg,png,jpeg,gif",
									fileName: "avatar",
									maxFileSize: ' . $max_size . ',
									dragDrop: false,
									showFileCounter: false,
									showProgress: true,
									onSuccess: function(a, response, b, c){
										if (response){
											var obj = jQuery.parseJSON(response);	
											jQuery("#ihc_upload_hidden_'.$rand.'").val(obj.id);
											jQuery("#ihc_fileuploader_wrapp_' . $rand . ' .ihc-file-upload").prepend("<div onClick=\"ihc_delete_file_via_ajax("+obj.id+", -1, \'#ihc_fileuploader_wrapp_' . $rand . '\', \'' . $attr['name'] . '\', \'#ihc_upload_hidden_'.$rand.'\');\" class=\'ihc-delete-attachment-bttn\'>Remove</div>");
											jQuery("#ihc_fileuploader_wrapp_' . $rand . ' .ihc-file-upload").prepend("<img src="+obj.url+" class=\'ihc-member-photo\' /><div class=\'ihc-clear\'></div>");	
											jQuery(".ihc-no-avatar").remove();
											setTimeout(function(){
												jQuery("#ihc_fileuploader_wrapp_' . $rand . ' .ajax-file-upload-container").css("display", "none");
											}, 3000);
										}
									}
								});
							});
						</script>';
				
				$str .= '<input type="hidden" value="'.$attr['value'].'" name="ihc_avatar"  id="ihc_upload_hidden_'.$rand.'" />';		
				
				if ($attr['value']){
					if (strpos($attr['value'], "http")===0){
						$url = $attr['value'];
					} else {
						$data = wp_get_attachment_image_src($attr['value']);
						if (!empty($data[0])){
							$url = $data[0];
						}
					}
					
					if (isset($url)){						
						$str .= '<img src="' . $url . '" class="ihc-member-photo" /><div class="ihc-clear"></div>';
						if (strpos($attr['value'], "http")===0){
							if (empty($attr['user_id'])){
								/// on register
								$user_id = -1;
							} else {
								$user_id = $attr['user_id'];
							}
							$str .= '<div onClick=\'ihc_delete_file_via_ajax("", ' . $user_id . ', "#ihc_fileuploader_wrapp_' . $rand . '", "' . $attr['name'] . '", "#ihc_upload_hidden_'.$rand.'" );\' class="ihc-delete-attachment-bttn">' . __("Remove", "ihc") . '</div>';							
						} else {
							$str .= '<div onClick=\'ihc_delete_file_via_ajax(' . $attr['value'] . ', '.$attr['user_id'].', "#ihc_fileuploader_wrapp_' . $rand . '", "' . $attr['name'] . '", "#ihc_upload_hidden_'.$rand.'" );\' class="ihc-delete-attachment-bttn">' . __("Remove", "ihc") . '</div>';							
						}
					}	
					$str .= '<div class="ihc-file-upload ihc-file-upload-button" style="display: none;">' . __("Upload", 'ihc') . '</div>';
				} else {
					$str .= '<div class="ihc-no-avatar ihc-member-photo"></div>';
					$str .= '<div class="ihc-file-upload ihc-file-upload-button" style="display: block;">' . __("Upload", 'ihc') . '</div>';
				}
					
				$str .= "</div>";	
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}			
				break;	

			case 'plain_text':
				$str = ihc_correct_text($attr['value']);
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}
				break;
				
			case 'unique_value_text':
				if (empty($attr['id'])){
					$attr['id'] = $attr['name'] . '_' . 'unique';
				}
				$str = '<input type="text" onBlur="ihc_check_unique_value_field(\'' . $attr['name'] . '\');" name="'.$attr['name'].'" id="'.$attr['id'].'" class="'.$attr['class'].'" value="' . ihc_correct_text($attr['value']) . '" placeholder="'.$attr['placeholder'].'" '.$attr['other_args'].' '.$attr['disabled'].' />';
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}
				break;
				
			case 'ihc_country':		
				if (empty($attr['id'])){
					$attr['id'] = $attr['name'] . '_field';
				}
				$countries = ihc_get_countries();
				$update_cart = 'ihc_update_cart();';
				if (isset($attr['form_type']) && $attr['form_type']=='edit'){
					$update_cart = '';
				}
				$str .= '<select name="' . $attr['name'] . '" id="' . $attr['id'] . '" onChange="ihc_update_state_field();' . $update_cart . '">'; /// onChange="ihc_update_tax_field();
				foreach ($countries as $k=>$v):
					$selected = ($attr['value']==$k) ? 'selected' : '';
					$str .= '<option value="' . $k . '" ' . $selected . '>' . $v . '</option>';
				endforeach;
				$str .= '</select>';
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}	
				$str .= '<ul id="ihc_countries_list_ul" style="display: none;">';
				
				$str .= '</ul>';
				
				$str .= '<script>
					jQuery("#' . $attr['id'] . '").select2({
					  placeholder: "Select Your Country",
					  allowClear: true
					});				
				</script>';
				break;
				
			case 'ihc_state':
				$str = '<input type="text" onBlur="ihc_update_cart();" name="' . $attr['name'] . '" id="' . $attr['id'] . '" class="' . $attr['class'] . '" value="' . ihc_correct_text($attr['value']) . '" placeholder="' . $attr['placeholder'] . '" ' . $attr['other_args'] . ' ' . $attr['disabled'] . ' />';
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}				
				break;
				
			/*
			case 'ihc_donation':
				$values = array(1, 2, 3, 5, 10);
				foreach ($values as $v){
					$str .= '<div>';
					$str .= '<input type="radio" value="' . $v . '" onClick="ihc_donation_value_update(this.value);" name="donation_radio"/>';
					$str .= '<label>' . $v . '</label>';
					$str .= '</div>';
				}
				$str .= '<div>';
				$str .= '<input type="number" value="" min="0" step="0.01" onKeyup="ihc_donation_value_update(this.value);" onChange="ihc_donation_value_update(this.value);" onBlur="ihc_donation_value_update(this.value);" />';
				$str .= '</div>';
				$str .= '<input type="hidden" value="" id="ihc_donation_input" name="ihc_donation" />';
				break;
			*/
			case 'ihc_invitation_code_field':
				$str = '<input type="text" onBlur="ihc_check_invitation_code();" name="ihc_invitation_code_field" id="ihc_invitation_code_field" class="'.$attr['class'].'" value="' . ihc_correct_text($attr['value']) . '" placeholder="'.$attr['placeholder'].'" '.$attr['other_args'].' '.$attr['disabled'].' />';
				if (!empty($attr['sublabel'])){
					$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($attr['sublabel']) . '</label>';
				}				
				break;
		}		
	}
	return $str;
}

function ihc_from_simple_array_to_k_v($arr){
	/*
	 * @param array
	 * @return array
	 */
	$return_arr = array();
	foreach ($arr as $v){
		$return_arr[$v] = $v;
	}
	return $return_arr;
}

function indeed_form_start($action=false, $method=false, $other_stuff=''){
	/*
	 * @param bool, bool, string
	 * @return string
	 */
	$str = '<form action="';
	if($action) $str .= $action;
	else $str .= '';
	$str .= '" method="';
	if($method) $str .= $method;
	else $str .= 'post';
	$str .= '" ';
	$str .= $other_stuff;
	$str .= '>';
	return $str;
}

function indeed_form_end(){
	/*
	 * @param none
	 * @return string
	 */
	return '</form>';
}

function ihc_reorder_arr($arr){
	/*
	 * @param array
	 * @return array
	 */
	if (isset($arr) && count($arr)>0 && $arr !== false){
		$new_arr = false;
		foreach ($arr as $k=>$v){
			$order = $v['order'];
			while (!empty($new_arr[$order])){
				$order++;
			}
			$new_arr[$order][$k] = $v;
		}
		if ($new_arr && count($new_arr)){
			ksort($new_arr);
			foreach ($new_arr as $k=>$v){
				$return_arr[key($v)] = $v[key($v)];
			}
			return $return_arr;	
		}	
	}
	return $arr;
}

function ihc_check_show($arr=array()){
	/*
	 * @param array
	 * @return array
	 */
	if ($arr!==FALSE && count($arr)>0){
		$new_arr = array();
		foreach ($arr as $k=>$v){
			if (isset($v['show_on'])){
				if($v['show_on'] == 1)
					$new_arr[$k] = $v;
			} else {
				$new_arr[$k] = $v;
			}
		}
		return $new_arr;					
	}
	return $arr;
}

function ihc_check_level_restricted_conditions($levels=array()){
	/*
	 * @param array
	 * @return array
	 */
	 $metas = ihc_return_meta_arr('level_subscription_plan_settings');
	 if (!empty($metas['ihc_level_subscription_plan_settings_enabled']) && $levels){
	 	 global $current_user;
		 $uid = (empty($current_user->ID)) ? 0 : $current_user->ID;
		 if (empty($uid)){
		 	 /// will check only for unreg 
		 	 foreach ($levels as $id=>$level){
		 	 	if (empty($metas['ihc_level_subscription_plan_settings_restr_levels']) || empty($metas['ihc_level_subscription_plan_settings_restr_levels'][$id])){
		 	 		continue;
		 	 	} else {
		 	 		/// CHECK IF MUST BLOCK THIS LEVEL
		 	 		if ($metas['ihc_level_subscription_plan_settings_condt'] && !empty($metas['ihc_level_subscription_plan_settings_condt'][$id])){
		 	 			$array_check = explode(',', $metas['ihc_level_subscription_plan_settings_condt'][$id]);		
						if (in_array('unreg', $array_check)){
							unset($levels[$id]);
						}	 			
		 	 		}
		 	 	}
		 	 }
		 } else {
			 $user_bought_something = Ihc_Db::does_this_user_bought_something($uid);
			 $user_levels = Ihc_Db::get_user_levels($uid);
			 
			 	 foreach ($levels as $id=>$level){
			 	 	if (empty($metas['ihc_level_subscription_plan_settings_restr_levels']) || empty($metas['ihc_level_subscription_plan_settings_restr_levels'][$id])){
			 	 		continue;
			 	 	} else {
			 	 		/// CHECK IF MUST BLOCK THIS LEVEL
			 	 		if ($metas['ihc_level_subscription_plan_settings_condt'] && !empty($metas['ihc_level_subscription_plan_settings_condt'][$id])){
			 	 			$array_check = explode(',', $metas['ihc_level_subscription_plan_settings_condt'][$id]);		
							if (!$user_bought_something && in_array('no_pay', $array_check)){
								unset($levels[$id]);
							}
							foreach ($user_levels as $current_level=>$current_level_data){
								if (in_array($current_level, $array_check)){
									unset($levels[$id]);
								}								
							}												 	 			
			 	 		}	 		
			 	 	}		 	 	
			 	 }			 	
			 			 			 
		 }
	 }
	 return $levels;
}

function ihc_return_cc_list($ips_cc_user, $ips_cc_pass){
	/*
	 * @param string, string
	 * @return array
	 */
	if (!class_exists('cc')){
		include_once IHC_PATH .'classes/email_services/constantcontact/class.cc.php';		
	}
	$list = array();
	$cc = new cc($ips_cc_user, $ips_cc_pass);
	$lists = $cc->get_lists('lists');
	if ($lists){
		foreach ((array) $lists as $v){
			$list[$v['id']] = array('name' => $v['Name']);
		}
	}
	return $list;
}


function ihc_get_all_post_types(){
	/*
	 * use this in front-end, returns all the custom post type available in db
	 * @param none
	 * @return array
	 */
	global $wpdb;
	$arr = array();
	$data = $wpdb->get_results('SELECT DISTINCT post_type FROM ' . $wpdb->prefix . 'posts WHERE post_status="publish";');
	if ($data && count($data)){
		foreach ($data as $obj){
			$arr[] = $obj->post_type;
		}
		$exclude = array('bp-email', 'edd_log', 'nav_menu_item', 'bp-email');
		foreach ($exclude as $e){
			if ($k=array_search($e, $arr)){
				unset($arr[$k]);
				unset($k);
			}	
		}
	}
	return $arr;
}

function ihc_get_post_types_be(){
	/*
	 * @param none
	 * @return all custom post type that are registered
	 * use this for back-end actions
	 */
	$args = array('public'=>true, '_builtin'=>false);
	$data = get_post_types($args);
	if (!function_exists('is_plugin_active')){
	 	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	if (is_plugin_active('download-monitor/download-monitor.php')){
		$data[] = 'dlm_download';
	}	
	return $data;
}


function ihc_get_post_id_by_cpt_name($custom_post_type='', $post_name=''){
	/*
	 * @param string, string
	 * @return int (id of post, >0 )
	 */
	global $wpdb;
	$table = $wpdb->prefix . 'posts';
	$q = $wpdb->prepare("SELECT ID FROM $table WHERE post_type=%s AND post_name=%s ", $custom_post_type, $post_name);
	$data = $wpdb->get_row($q);
	if (!empty($data->ID)){
		return $data->ID;
	}
	return FALSE;
}

function ihc_get_wp_roles_list(){
	/*
	 * @param none
	 * @return array with all wp roles available without administrator
	 */
	global $wp_roles;
	$roles = $wp_roles->get_names();
    if (!empty($roles)){
    	unset($roles['administrator']);// remove admin role from our list
    	return $roles;
    }
	return FALSE;
}

function ihc_get_multiply_time_value($time_type){
	/*
	 * @param string D,W,M,Y
	 * @return time in seconds
	 */
	$multiply = FALSE;
	switch ($time_type){
		case 'D':
			$multiply = 60*60*24;
		break;
		case 'W':
			$multiply = 60*60*24*7;
		break;
		case 'M':
			$multiply = 60*60*24*31;
		break;
		case 'Y':
			$multiply = 60*60*24*365;
		break;
	}
	return $multiply;
}

function ihc_delete_user_level_relation($l_id=FALSE, $u_id=FALSE){
	/*
	 * delete user meta level, delete relation from table ihc_user_levels
	 * @param level id and user id
	 * @return none
	 */
	if ($u_id && $l_id){	
		$levels_str = get_user_meta($u_id, 'ihc_user_levels', true);
		$levels_arr = explode(',', $levels_str);
		if (!is_array($l_id)){
			$lid_arr[] = $l_id;
		}
		$levels_arr = array_diff($levels_arr, $lid_arr);
		$levels_str = implode(',', $levels_arr);
		update_user_meta($u_id, 'ihc_user_levels', $levels_str);
		global $wpdb;
		$table_name = $wpdb->prefix . "ihc_user_levels";
		$wpdb->query('DELETE FROM ' . $table_name . ' WHERE user_id="'.$u_id.'" AND level_id="'.$l_id.'";');
		ihc_downgrade_levels_when_expire($u_id, $l_id);		
		
		do_action('ihc_action_after_subscription_delete', $u_id, $l_id);
	}
}

function ihc_update_user_level_expire($level_data, $l_id, $u_id){
	/*
	 * update expire level for a user with the right expire time
	 * use this only when user has made the payment
	 * @param:
	 * - array with level metas
	 * - level id int
	 * - user id int
	 * @return none
	 */
	global $wpdb;
	$table = $wpdb->prefix . 'ihc_user_levels';
	
	if (empty($level_data['access_type'])){
		$level_data['access_type'] = 'unlimited';
	}
	
	$current_time = time();
	//getting the current expire time, if it's exists. Old expire time will be current time
	$q = $wpdb->prepare("SELECT expire_time FROM $table WHERE user_id=%d AND level_id=%d ;", $u_id, $l_id);
	$data = $wpdb->get_row($q);
	if ($data && !empty($data->expire_time)){
		$expire_time = strtotime($data->expire_time);
		if ( $expire_time>0 && $expire_time>time() ){ /// level has not expired yet
			$current_time = $expire_time; 
		}
	}
	
	//set end time
	switch ($level_data['access_type']){
		case 'unlimited':
			$end_time = strtotime('+10 years', $current_time);//unlimited will be ten years
		break;
		case 'limited':
			if (!empty($level_data['access_limited_time_type']) && !empty($level_data['access_limited_time_value'])){
				$multiply = ihc_get_multiply_time_value($level_data['access_limited_time_type']);
				$end_time = $current_time + $multiply * $level_data['access_limited_time_value'];
			}
		break;
		case 'date_interval':
			if (!empty($level_data['access_interval_end'])){
				$end_time = strtotime($level_data['access_interval_end']);
			}
		break;
		case 'regular_period':
			if (!empty($level_data['access_regular_time_type']) && !empty($level_data['access_regular_time_value'])){
				$multiply = ihc_get_multiply_time_value($level_data['access_regular_time_type']);
				$end_time = $current_time + $multiply * $level_data['access_regular_time_value'];
			}
		break;
	}
	
	$update_time = date('Y-m-d H:i:s', time());
	$end_time = date('Y-m-d H:i:s', $end_time);
	$q = $wpdb->prepare("UPDATE $table SET update_time='$update_time', expire_time='$end_time', notification=0, status=1 WHERE user_id=%d AND level_id=%d ", $u_id, $l_id);
	$wpdb->query($q);
	do_action('ihc_action_after_subscription_activated', $u_id, $l_id);	
}

function ihc_set_level_trial_time_for_no_pay($level_data, $l_id, $u_id){
	/*
	 * USE THIS ONLY IN STRIPE, TO SET THE TRIAL TIME
	 * @param array, int, int
	 * @return none
	 */
	global $wpdb;
	$table = $wpdb->prefix . 'ihc_user_levels';
	$current_time = time();
	$q = $wpdb->prepare("SELECT expire_time FROM $table WHERE user_id=%d AND level_id=%d ;", $u_id, $l_id);
	$data = $wpdb->get_row($q);
	if ($data && !empty($data->expire_time)){
		$expire_time = strtotime($data->expire_time);
		if ($expire_time>0){
			$current_time = $expire_time; 
		}
	}
	if (!empty($level_data['access_trial_type'])){
		if ($level_data['access_trial_type']==1){
			$multiply = ihc_get_multiply_time_value($level_data['access_trial_time_type']);
			$time_to_add = $level_data['access_trial_time_value'];
		} else {
			///couple of circles
			$multiply = ihc_get_multiply_time_value($level_data['access_regular_time_type']);
			$time_to_add = $level_data['access_regular_time_value'];
		}
		$end_time = $current_time + $multiply * $time_to_add;
		$update_time = date('Y-m-d H:i:s', time());
		$end_time = date('Y-m-d H:i:s', $end_time);
		$q = $wpdb->prepare("UPDATE $table SET update_time='$update_time', expire_time='$end_time', notification=0, status=1 WHERE user_id=%d AND level_id=%d ", $u_id, $l_id);
		$wpdb->query($q);		
	}	
}

function ihc_get_start_expire_date_for_user_level($u_id, $l_id){
	/*
	 * @param int, int
	 * @return array
	 */
	global $wpdb;
	$table = $wpdb->prefix . 'ihc_user_levels';
	$q = $wpdb->prepare("SELECT expire_time, start_time FROM $table WHERE user_id=%d AND level_id=%d ", $u_id, $l_id);
	$data = $wpdb->get_row($q);
	$arr['start_time'] = (isset($data->start_time)) ? $data->start_time : FALSE;
	$arr['expire_time'] = (isset($data->expire_time)) ? $data->expire_time : FALSE;
	return $arr;
}

function ihc_set_time_for_user_level($u_id, $l_id, $start, $expire){
	/*
	 * @param user id, level id, start time , expire time
	 * @return none
	 */	
	global $wpdb;	
	$update_time = date('Y-m-d H:i:s', time());
	
	$table = $wpdb->prefix . 'ihc_user_levels';
	$where_condition = $wpdb->prepare("user_id=%d AND level_id=%d ", $u_id, $l_id);
	$q = "SELECT id FROM $table WHERE $where_condition;";
	$exists = $wpdb->get_row($q);
	if (isset($exists->id)){
		//it's gonna be an update
		$q = "UPDATE $table SET update_time='', start_time=";
		if (!$start){
			$q .= 'null';
		} else {
			$q .= "'" . $start . "'";
		}
		$q .= ", expire_time="; 
		if (!$expire){
			$q .= "null,";
		} else {
			$q .= "'" . $expire . "',";
		}
		$q .= " notification=0 ";
		$q .= " WHERE $where_condition;";		
	} else {
		//go create new row in db
		$q = $wpdb->prepare("INSERT INTO $table VALUES (null, %d, %d,", $u_id, $l_id);
		if (!$start){
			$q .= 'null';
		} else {
			$q .= "'" . $start . "'";
		}
		$q .=  ", '$update_time'";
		$q .= ", expire_time=";
		if (!$expire){
			$q .= "null";
		} else {
			$q .= "'" . $expire . "'";
		}		
		$q .= ", 0, 1)";
	}
	$wpdb->query($q);
}

function ihc_insert_update_transaction($u_id, $txn_id, $post_data, $dont_save_order=FALSE){
	/*
	 * @param user id, trascation id, post data from paypal
	 * @return none
	 */		
	//remove quotes from post data

	foreach ($post_data as $k=>$v){
		if (is_string($post_data[$k])){
			if (strpos($post_data[$k], "'")!==FALSE){
				$post_data[$k] = stripslashes($post_data[$k]);
				$post_data[$k] = str_replace("'", "", $post_data[$k]);
			} else if (strpos($post_data[$k], "\'")!==FALSE){
				$post_data[$k] = stripslashes($post_data[$k]);
				$post_data[$k] = str_replace("\'", "", $post_data[$k]);
			}			
		}
	}
	
	global $wpdb;
	$table = $wpdb->prefix . 'indeed_members_payments';
	$q = $wpdb->prepare("SELECT * FROM $table WHERE txn_id=%s;", $txn_id);
	$exists = $wpdb->get_row($q);
	if ($exists){
		/************** UPDATE ***************/
		$history = '';
		$q = $wpdb->prepare("SELECT history FROM $table WHERE txn_id=%s ;", $txn_id);
		$history_data = $wpdb->get_row($q);
		if ($history_data && isset($history_data->history)){
			//$history_data = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $history_data->history);
			@$history = unserialize($history_data->history);
		} else {
			$q = $wpdb->prepare("SELECT payment_data FROM $table WHERE txn_id=%s;", $txn_id);
			$history_data = $wpdb->get_row($q);
			if (isset($history_data->payment_data)){
				$temp = (array)json_decode($history_data->payment_data);
				if (isset($temp['custom'])) unset($temp['custom']);
				if (isset($temp['transaction_subject'])) unset($temp['transaction_subject']);
				$history[] = $temp;
			}
		}
		//remove custom from history
		$post_data_history = $post_data;
		if (isset($post_data_history['custom'])) unset($post_data_history['custom']);
		if (isset($post_data_history['transaction_subject'])) unset($post_data_history['transaction_subject']);
		$history[time()] = $post_data_history;
		$history_string = serialize($history);

		$q = $wpdb->prepare("UPDATE $table SET history=%s WHERE txn_id=%s ", $history_string, $txn_id);
		$wpdb->query($q);

		//////////update payment_data (last $_REQUEST )
		$post_data = json_encode($post_data);
		$q = $wpdb->prepare("UPDATE $table SET payment_data=%s WHERE txn_id=%s ", $post_data, $txn_id);
		$wpdb->query($q);

	} else {
		/************* insert ************/
				
		/////the history
		$post_data_history = $post_data;
		if (isset($post_data_history['custom'])) unset($post_data_history['custom']);
		if (isset($post_data_history['transaction_subject'])) unset($post_data_history['transaction_subject']);
		$history[time()] = $post_data_history;
		$history_str = serialize($history);

		////the payment data
		$post_data = json_encode($post_data);

		$q = $wpdb->prepare("INSERT INTO $table VALUES (null, %s, %d, %s, %s, null, NOW());", $txn_id, $u_id, $post_data, $history_str);
		$wpdb->query($q);
	}
	
	if ($dont_save_order){
		return;
	}
	/// ORDER
	require_once IHC_PATH . 'classes/Orders.class.php';
	$object = new Ump\Orders();
	$object->do_insert_update($txn_id);
}

function ihc_insert_update_order($uid=0, $lid=0, $amount_value=0, $status='pending', $payment_gateway='', $extra_fields=array()){
	/*
	 * @param int, int, float, string
	 * @return int
	 */	 
	if (!empty($uid) && isset($lid) && isset($amount_value)){
		require_once IHC_PATH . 'classes/Orders.class.php';
		$object = new Ump\Orders();
		$amount_type = get_option('ihc_currency');
		$order_id = $object->do_insert(array(
									'uid' => $uid,
									'lid' => $lid,
									'amount_type' => $amount_type,
									'amount' => $amount_value,
									'status' => $status,
									'ihc_payment_type' => $payment_gateway,
									'extra_fields' => $extra_fields,
		));
		return $order_id;
	}
}

function ihc_user_has_level($u_id, $l_id){
	/*
	 * test if user has a certain level
	 * @param user level
	 * @return bool
	 */
	$user_levels = get_user_meta($u_id, 'ihc_user_levels', true);
	if($user_levels){
		$levels = explode(',', $user_levels);
		if (isset($levels) && count($levels) && in_array($l_id, $levels)){
			$user_time = ihc_get_start_expire_date_for_user_level($u_id, $l_id);
			if(strtotime($user_time['expire_time']) > time())
				return TRUE;
		}
	}
	return FALSE;
}

function ihc_user_has_level_admin($uid, $lid){
	/*
	 * @param int, int
	 * @return bool
	 */
	
	global $wpdb;
	$data = $wpdb->get_row("SELECT id FROM " . $wpdb->prefix . "ihc_user_levels
								WHERE user_id='" . $uid . "'
								AND level_id='" . $lid . "';");
	if ($data!==FALSE && isset($data->id)){
		return TRUE;
	}
	return FALSE;
}

function ihc_insert_debug_payment_log($source, $data){
	/*
	 * insert into ihc_debug_payments
	 * @param source = type of payment service (paypall)
	 * data = the request from payment service
	 * @return none
	 */
	global $wpdb;
	$table = $wpdb->prefix . "ihc_debug_payments";
	$time = date('Y-m-d H:i:s', time());
	$data = serialize($data);
	$q = $wpdb->prepare("INSERT INTO $table VALUES(null, %s, %s, %s);", $source, $data, $time );
	$wpdb->query($q);
}

function ihc_send_user_notifications($u_id=FALSE, $notification_type='', $l_id=FALSE, $dynamic_data=array()){
	/*
	 * main function for notification module
	 * send e-mail to user
	 * @param:
	 * user id ($u_id) - int, 
	 * notification type ($notification_type) - string
	 * optional level id ($l_id) - int, -1 means all levels
	 * dynamic_data - array
	 * @return TRUE if mail was sent, FALSE otherwise
	 */
	global $wpdb;
	if ($u_id && $notification_type){

		//check if we have instances for this notification type [and for level]
		if ($l_id!==FALSE && $l_id>-1){
			$q = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ihc_notifications 
									WHERE 1=1 
									AND notification_type=%s
									AND level_id=%d
									ORDER BY id DESC LIMIT 1;", $notification_type, $l_id);
			$data = $wpdb->get_results($q);
		}
		
		if ($l_id===FALSE || $l_id==-1 || empty($data)){
			$q = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ihc_notifications
									WHERE 1=1
									AND notification_type=%s
									AND level_id='-1'
									ORDER BY id DESC LIMIT 1;", $notification_type);
			$data = $wpdb->get_results($q);			
		}
		if (!empty($data)){
			$subject = $data[0]->subject;
			$message = $data[0]->message;				
			
			$from_name = get_option('ihc_notification_name');
			if (!$from_name){
				$from_name = get_option("blogname");
			}
			
			//user levels
			$level_list_data = get_user_meta($u_id, 'ihc_user_levels', true);
			if (isset($level_list_data)){
				$level_list_data = explode(',', $level_list_data);
				foreach ($level_list_data as $id){
					$temp_level_data = ihc_get_level_by_id($id);
					$level_list_arr[] = $temp_level_data['label'];
				}
				if ($level_list_arr){
					$level_list = implode(',', $level_list_arr);
				}
			}
			
			//user data
			$u_data = get_userdata($u_id);
			$user_email = '';
			if ($u_data && !empty($u_data->data) && !empty($u_data->data->user_email)){
				$user_email = $u_data->data->user_email;	
			}
			//from email
			$from_email = get_option('ihc_notification_email_from');
			if (!$from_email){
				$from_email = get_option('admin_email');
			}
			$message = ihc_replace_constants($message, $u_id, $l_id, $l_id, $dynamic_data);
			$subject = ihc_replace_constants($subject, $u_id, $l_id, $l_id, $dynamic_data);
			
			$message = stripslashes(htmlspecialchars_decode(ihc_format_str_like_wp($message)));
			
			$message = apply_filters('ihc_send_notification_filter_message', $message, $u_id, $l_id, $notification_type);
			
			$message = "<html><head></head><body>" . $message . "</body></html>";
			
			if ($subject && $message && $user_email){
				
				$admin_case = array('admin_user_register', 
									'admin_user_expire_level', 
									'admin_before_user_expire_level', 
									'admin_user_payment', 
									'admin_user_profile_update',
									'ihc_subscription_activated_notification-admin',
									'ihc_order_placed_notification-admin',
									'ihc_delete_subscription_notification-admin',
				);
				if (in_array($notification_type, $admin_case)){
					/// SEND NOTIFICATION TO ADMIN, (we change the destination)
					$admin_email = get_option('ihc_notification_email_addresses');
					if (empty($admin_email)){
						$user_email = get_option('admin_email');				
					} else {
						$user_email = $admin_email;
					}
				} 
				if (!empty($from_email) && !empty($from_name)){
					$headers[] = "From: $from_name <$from_email>";						
				}			
				
				$headers[] = 'Content-Type: text/html; charset=UTF-8';
				$sent = wp_mail($user_email, $subject, $message, $headers);
				return $sent;
			}
		}
	}
	return FALSE;
}

function ihc_get_uid_lid_by_stripe($stripe_txn_id=''){
	/*
	 * @param transaction id - string
	 * @return array 
	 */
	global $wpdb;	
	$q = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix ."indeed_members_payments WHERE `txn_id`=%s ;", $stripe_txn_id);
	$db_data = $wpdb->get_row($q);
	$data = array();
	if ($db_data){
		if (isset($db_data->u_id)){
			$data['uid'] = $db_data->u_id;
		}
		if (isset($db_data->payment_data)){
			$data_db_json = json_decode($db_data->payment_data, TRUE);
			if (isset($data_db_json['level'])){
				$data['lid'] = $data_db_json['level'];
			}
			$data['payment_data'] = $data_db_json;			
		}
	}
	return $data;
}

function ihc_get_lid_uid_by_txn_id($txn_id=''){
	/*
	 * @param string
	 * @return array
	 */
	global $wpdb;	
	$table = $wpdb->prefix . "indeed_members_payments";
	$q = $wpdb->prepare("SELECT payment_data FROM $table WHERE txn_id=%s;", $txn_id);
	$data = $wpdb->get_row($q);
	if ($data && !empty($data->payment_data)){
		$temp = json_decode($data->payment_data, TRUE);
		return $temp;
	}
	return array();
}

function ihc_twocheckout_submit($u_id, $l_id, $code='', $ihc_country=FALSE){
	/*
	 * Redirect to 2checkout payment
	 * @param int, int, string, string
	 * @return none
	 */
	$level_data = get_option('ihc_levels');
	$amount = $level_data[$l_id]['price'];
	$currency = get_option('ihc_currency');
	$checkout_account_num = get_option('ihc_twocheckout_account_number');
	$custom_currency_code = get_option('ihc_custom_currency_code');
	if ($custom_currency_code){
		$currency = $custom_currency_code;
	}
	
	//========= DISCOUNT
	if ($code){
		$coupon_data = ihc_check_coupon($code, $l_id);
		if ($coupon_data){
			if (isset($level_data[$l_id]['access_type']) && $level_data[$l_id]['access_type']=='regular_period'){
				//discount on recurring payment
				if (empty($coupon_data['reccuring'])){
					//just one time
					$discount_once = -($amount - ihc_coupon_return_price_after_decrease($amount, $coupon_data));
				} else {
					//on every payment
					$amount = ihc_coupon_return_price_after_decrease($amount, $coupon_data);
				}
			} else {
				//discount on single payment
				$amount = ihc_coupon_return_price_after_decrease($amount, $coupon_data);
			}
		}
	}
	
	///TAXES
	$state = get_user_meta($u_id, 'ihc_state', TRUE);
	$country = ($ihc_country==FALSE) ? '' : $ihc_country;
	$taxes_data = ihc_get_taxes_for_amount_by_country($country, $state, $amount);
	if ($taxes_data && !empty($taxes_data['total'])){
		$taxes = $taxes_data['total'];
		$amount += $taxes;
	}	
	
	$li_0_name = (empty($level_data[$l_id]['label'])) ? 'Level ' . $l_id : $level_data[$l_id]['label'];	
	
	$params_arr = array(
			'sid' => $checkout_account_num,
			'mode' => '2CO',
			'pay_method' => 'CC',
			'li_0_type' => 'product',
			'li_0_name' => $li_0_name,
			'li_0_product_id' => $l_id,
			'li_0_quantity' => 1,
			'li_0_price' => $amount,
			'li_0_tangible' => 'N',
			'li_0_description' => json_encode(array("u_id" => $u_id, "l_id" => $l_id)),
			'currency_code' => $currency,
			'x_receipt_link_url' => admin_url("admin-ajax.php") . "?action=ihc_twocheckout_ins",//
			'purchase_step' => 'billing-information',
	);
	
	//====================== RECURRING
	if (isset($level_data[$l_id]['access_type']) && $level_data[$l_id]['access_type']=='regular_period'){
	
		switch ($level_data[$l_id]['access_regular_time_type']){
			case 'D':
				$weeks = $level_data[$l_id]['access_regular_time_value'] / 7;
				if ($weeks<1){
					$weeks = 1;
				}
				$reccurence_time = ceil($weeks) . ' Week';
				$billing = ceil($weeks) . ' Week';
				break;
			case 'W':
				$reccurence_time = $level_data[$l_id]['access_regular_time_value'] . ' Week';
				$billing = $level_data[$l_id]['billing_limit_num'] . ' Week';
				break;
			case 'M':
				$reccurence_time = $level_data[$l_id]['access_regular_time_value'] . ' Month';
				$billing = $level_data[$l_id]['billing_limit_num'] . ' Month';
				break;
			case 'Y':
				$reccurence_time = $level_data[$l_id]['access_regular_time_value'] . ' Year';
				$billing = $level_data[$l_id]['billing_limit_num'] . ' Year';
				break;
		}
		$params_arr['li_0_recurrence'] = $reccurence_time;//billing frequency. Ex. �1 Week� to bill order once a week. (Can use # Week, # Month, or # Year)
		$params_arr['li_0_duration'] = $billing;//how long to continue billing. Ex. �1 Year�, to continue billing for 1 year. (Forever or # Week, # Month, # Year)
	
		//trial for a single subscribe payment
		if (isset($level_data[$l_id]['access_trial_type']) && $level_data[$l_id]['access_trial_type']==2 && isset($level_data[$l_id]['access_trial_couple_cycles']) && $level_data[$l_id]['access_trial_couple_cycles']>0){
			////DISCOUNT
			$params_arr['li_0_startup_fee'] = $level_data[$l_id]['access_trial_price'] - $amount;
			if (!empty($discount_once)){
				//discount just once on reccuring with trial period
				$params_arr['li_0_startup_fee'] = $params_arr['li_0_startup_fee'] + $discount_once;
			}
		} else if (!empty($discount_once)){
			//discount just once on reccuring without trial period
			$params_arr['li_0_startup_fee'] = $discount_once;
		}
		
		/// TAXES
		if (isset($params_arr['li_0_startup_fee']) && !empty($ihc_country)){
			$state = get_user_meta($u_id, 'ihc_state', TRUE);
			$country = ($ihc_country==FALSE) ? '' : $ihc_country;
			$taxes_data = ihc_get_taxes_for_amount_by_country($country, $state, $params_arr['li_0_startup_fee']);
			if ($taxes_data && !empty($taxes_data['total'])){
				$taxes = $taxes_data['total'];
				$params_arr['li_0_startup_fee'] += $taxes;
			}	
		}			
	}
	
	$sandbox = get_option('ihc_twocheckout_sandbox');
	if ($sandbox){
		$base_url = "sandbox.2checkout.com";
		$params_arr['demo'] = 'Y';
	} else {
		$base_url = "www.2checkout.com";
	}
	
	$params_str = '';
	foreach ($params_arr as $k=>$v){
		if (empty($params_str)){
			$params_str = '?';
		} else {
			$params_str .= '&';
		}
		$params_str .= urlencode($k) . "=" . urlencode($v);
	}
	
	$redirect_url = 'https://' . $base_url . '/checkout/purchase' . $params_str;
	
	//logout user...
	wp_logout();
	wp_redirect( $redirect_url );
	exit();
}

function ihc_print_bank_transfer_order($u_id, $l_id){
	/*
	 * print the bank transfer message
	 * @param int, int, string, int
	 * @return string
	 */
	$msg = get_option('ihc_bank_transfer_message');
	if (!empty($_GET['cp'])){
		$discount_type = 'percentage';
		$discount_value = $_GET['cp'];
	} else if (!empty($_GET['cc'])) {
		$discount_type = 'flat';
		$discount_value = $_GET['cc'];
	}
	//get amount
	$level_data = ihc_get_level_by_id($l_id);
	$amount = $level_data['price'];	
	///DISCOUNT
	if (!empty($discount_type) && !empty($discount_value)){
		$amount = ihc_get_amount_after_discount_for_bt_show($discount_type, $discount_value, $amount);
	}
	
	///TAXES
	$state = (isset($_GET['ihc_state'])) ? $_GET['ihc_state'] : '';
	$country = isset($_GET['ihc_country']) ? $_GET['ihc_country'] : '';
	$taxes_data = ihc_get_taxes_for_amount_by_country($country, $state, $amount);
	if ($taxes_data && !empty($taxes_data['total'])){
		$taxes = $taxes_data['total'];
		$amount += $taxes;
	}
	
	$msg = str_replace('{amount}', $amount, $msg);
	
	$msg = ihc_replace_constants($msg, $u_id, $l_id, $l_id);

	//ihc_send_user_notifications($u_id, 'bank_transfer', $l_id);
	
	return '<div class="ihc-bank-transfer-msg" id="ihc_bt_success_msg">' . ihc_correct_text($msg) . '</div>';
}

function ihc_get_amount_after_discount_for_bt_show($discount_type='', $discount_value=0, $amount=0){
	/*
	 * @param string, int, string, int
	 * @return string
	 */
	if ($discount_type=='percentage'){
		$amount = $amount - ($amount*$discount_value/100);
	} else {
		$amount = $amount - $discount_value;
	}
	$amount = round($amount, 2);
	return $amount; 
}

function ihc_downgrade_levels_when_expire($uid, $lid){
	/*
	 * add after expire level for specified user
	 * @param user id, level id
	 * @return bool, true if succeed
	 */
	$level_data = ihc_get_level_by_id($lid);
	if (isset($level_data['afterexpire_level']) && $level_data['afterexpire_level']!=-1){
		$user_levels = get_user_meta($uid, 'ihc_user_levels', true);
		if ($user_levels!==FALSE && $user_levels!=''){
			$user_levels_arr = explode(',', $user_levels);
			if (!in_array($level_data['afterexpire_level'], $user_levels_arr)){
				$user_levels_arr[] = $level_data['afterexpire_level'];
			}
			$user_levels = implode(',', $user_levels_arr);
		} else {
			$user_levels = $level_data['afterexpire_level'];
		}
		$succees = ihc_handle_levels_assign($uid, $level_data['afterexpire_level']);//assign the new level expire time and stuff...
		if ($succees){
			update_user_meta($uid, 'ihc_user_levels', $user_levels);//assign the new level
			return TRUE;			
		}
	}
	return FALSE;
}

function ihc_handle_levels_assign($uid=FALSE, $lid=FALSE){
	/*
	 * Used in : buy new level ( with Authorize Reccuring or Stripe), social login 
	 * insert into db when user was start using this level,
	 * @param user id, level id
	 * @return bool, true if succeed
	 */	 
	if ($uid && $lid){
		$level_data = ihc_get_level_by_id($lid);//getting details about current level
		$current_time = time();
					
		if (empty($level_data['access_type'])){
			$level_data['access_type'] = 'unlimited';
		}
						
		//set start time
		if ( $level_data['access_type']=='date_interval' && !empty($level_data['access_interval_start']) ){
			$start_time = strtotime($level_data['access_interval_start']);
		} else {
			$start_time = $current_time;
		}
						
		//set end time
		if ($level_data['payment_type']=='payment'){
			//end time will be expired, updated when payment
			$end_time = '0000-00-00 00:00:00';
		} else {
				//it's free so we set the correct expire time
				switch ($level_data['access_type']){
					case 'unlimited':
						$end_time = strtotime('+10 years', $current_time);//unlimited will be ten years
						break;
					case 'limited':
						if (!empty($level_data['access_limited_time_type']) && !empty($level_data['access_limited_time_value'])){
							$multiply = ihc_get_multiply_time_value($level_data['access_limited_time_type']);
							$end_time = $current_time + $multiply * $level_data['access_limited_time_value'];
						}
						break;
					case 'date_interval':
						if (!empty($level_data['access_interval_end'])){
							$end_time = strtotime($level_data['access_interval_end']);
						}
						break;
					case 'regular_period':
						if (!empty($level_data['access_regular_time_type']) && !empty($level_data['access_regular_time_value'])){
							$multiply = ihc_get_multiply_time_value($level_data['access_regular_time_type']);
							$end_time = $current_time + $multiply * $level_data['access_regular_time_value'];
						}
						break;
				}//end of switch
				$end_time = date('Y-m-d H:i:s', $end_time);
			}
						
			$update_time = date('Y-m-d H:i:s', $current_time);
			$start_time = date('Y-m-d H:i:s', $start_time);
						
			global $wpdb;
			$table = $wpdb->prefix . 'ihc_user_levels';
			$q = $wpdb->prepare("SELECT * FROM $table WHERE user_id=%d AND level_id=%d", $uid, $lid);
			$exists = $wpdb->get_row($q);
			if (!empty($exists)){
				$q = $wpdb->prepare("DELETE FROM $table WHERE user_id=%d AND level_id=%d ;", $uid, $lid);
				$wpdb->query($q);//assure that pair user_id - level_id entry not exists
			}
			$q = $wpdb->prepare("INSERT INTO $table	VALUES(null, %d, %d, %s, %s, %s, 0, 1);", $uid, $lid, $start_time, $update_time, $end_time);
			$wpdb->query($q);
			do_action('ihc_new_subscription_action', $uid, $lid);
		return TRUE;
	}
	return FALSE;
}

function ihc_do_complete_level_assign_from_ap($uid=0, $lid=0){
	/*
	 * @param array
	 * @return boolean
	 */
	$user_levels = get_user_meta($uid, 'ihc_user_levels', true);
	if ($user_levels!==FALSE && $user_levels!=''){
		$user_levels_arr = explode(',', $user_levels);
		if (!in_array($lid, $user_levels_arr)){
			$user_levels_arr[] = $lid;
		}
		$user_levels = implode(',', $user_levels_arr);
	} else {
		$user_levels = $lid;
	}
	$succees = ihc_handle_levels_assign($uid, $lid);
	if ($succees){
		update_user_meta($uid, 'ihc_user_levels', $user_levels);//assign the new level
		return TRUE;
	}
	return FALSE; 
}

function ihc_make_csv_user_list(){
	/*
	 * generate csv file with all users
	 * @param none
	 * @return string, link to csv file or empty string
	 */
	global $wpdb;		
	$users_obj = new WP_User_Query(array(
			'meta_query' => array(
				array(
					'key' => $wpdb->get_blog_prefix() . 'capabilities',
					'value' => 'administrator',
					'compare' => 'NOT LIKE'
				)
			)
	));
	$users = $users_obj->results;

	if ($users){
		//if we have users
		$file_path = IHC_PATH . 'users.csv';
		$file_link = IHC_URL . 'users.csv';
		if (file_exists($file_path)){
			unlink($file_path);
		}
		$file_resource = fopen($file_path, 'w');
		
		$register_fields = ihc_get_user_reg_fields();
		foreach ($register_fields as $k=>$v){
			if ($v['name']=='pass1' || $v['name']=='pass2' || $v['name']=='tos' || $v['name']=='recaptcha' || $v['name']=='confirm_email' || $v['name']=='ihc_social_media'){
				unset($register_fields[$k]);
			} else {
				if (isset($v['native_wp']) && $v['native_wp']){
					$data[] = __($v['label'], 'ihc');
				} else {
					$data[] = $v['label'];
				}
			}			
		}
		$data[] = __('Level', 'ihc');
		$data[] = __('WP User Roles', 'ihc');
		$data[] = __('Join Date', 'ihc');
		fputcsv($file_resource, $data, ",");
		unset($data);
		
		foreach ($users as $user){
			foreach ($register_fields as $v){
				if (isset($user->data->{$v['name']})){
					$data[] = $user->data->{$v['name']};
				} else {
					$user_data = get_user_meta($user->data->ID, $v['name'], true);
					if ($user_data!==FALSE){
						if (is_array($user_data)){
							$data[] = implode(",", $user_data);
						} else {
							$data[] = $user_data;
						}
					} else {
						$data[] = ' ';
					}
				}	
			}
			$levels = get_user_meta($user->data->ID, 'ihc_user_levels', true);
			if (isset($levels)){
				$levels_arr = explode(",", $levels);
				foreach ($levels_arr as $lid){
					$current_level_data = ihc_get_level_by_id($lid);
					if (isset($current_level_data['label'])){
						$level_arr_to_write[] = $current_level_data['label'];
					} else {
						$level_arr_to_write[] = ' ';
					}			
				}
				if (isset($level_arr_to_write)){
					$write_str = implode(',', $level_arr_to_write);
					$data[] = $write_str;
					unset($write_str);
					unset($level_arr_to_write);
				} else {
					$data[] = " ";
				}
			} else {
				$data[] = " ";
			}
			
			$data[] = $user->roles[0];
			$data[] = $user->data->user_registered;
			fputcsv($file_resource, $data, ",");
			unset($data);
		}	
		fclose($file_resource);
		return $file_link;
	}
	return '';
}

function ihc_get_attachment_details($id, $return_type='name'){
	/*
	 * @param attachment id, what to return: name or extension
	 * @return string : 
	 */
	$attachment_data = wp_get_attachment_url($id);
	if (isset($attachment_data)){
		$attachment_arr = explode('/', $attachment_data);
		if (isset($attachment_arr)){
			end($attachment_arr);
			$attachment_name = $attachment_arr[key($attachment_arr)];
			if ($return_type=='name'){
				return $attachment_name;
			}
			$attachment_type = explode('.', $attachment_name);
			if (isset($attachment_type)){
				end($attachment_type);
				if (isset($attachment_type[key($attachment_type)])){
					return $attachment_type[key($attachment_type)];
				}				
			}		
		}
	}
	return 'Unknown';
}

function ihc_replace_constants($str = '', $u_id = FALSE, $current_level_id=FALSE, $l_id=FALSE, $dynamic_data = array()){
	/*
	 * @param $str - string where to replace,
	 * user id - int, 
	 * current level id - int, 
	 * level id - int, 
	 * dynamic_data must be an array ( {name of constant} => {value} )
	 * @return string
	 */
	if ($u_id){
		//$u_id = '';//from param
		//$l_id = ''; // from param
		$username = '';
		$first_name = '';
		$last_name = '';		
		$current_level = '';
		$level_expire_time = '';
		$level_list = '';
		$user_email = '';
		$account_page = '';
		$login_page = '';
		$blogname = '';
		$blogurl = '';		
		$level_name = '';
		$amount = '';
		$currency = '';
		$site_url = '';
		$current_date = date('Y-m-d H:i:s');
		
		//user levels
		$level_list_data = get_user_meta($u_id, 'ihc_user_levels', true);
		if (isset($level_list_data)){
			$level_list_data = explode(',', $level_list_data);
			foreach ($level_list_data as $id){
				$temp_level_data = ihc_get_level_by_id($id);
				$level_list_arr[] = $temp_level_data['label'];
			}
			if ($level_list_arr){
				$level_list = implode(',', $level_list_arr);
			}
		}

		//user data
		$u_data = get_userdata($u_id);
		@$user_email = $u_data->data->user_email;
		@$username = $u_data->data->user_login;
		$first_name = get_user_meta($u_id, 'first_name', true);
		$last_name = get_user_meta($u_id, 'last_name', true);
		$blogname = get_option("blogname");
		$blogurl = get_option("siteurl");
		$currency = get_option('ihc_currency');
		$site_url = get_option('siteurl');
		$user_registered = '';
		if ($u_data && !empty($u_data->data) && !empty($u_data->data->user_registered)){
			$user_registered = ihc_convert_date_to_us_format($u_data->data->user_registered);			
		}
		
		//current_level,current_level_expire_date
		if ($current_level_id!==FALSE){
			$current_level_data = ihc_get_level_by_id($current_level_id);
			$current_level = $current_level_data['label'];
			$time = ihc_get_start_expire_date_for_user_level($u_id, $current_level_id);
			$level_expire_time = $time['expire_time'];
		}

		//account page
		$account_page = get_option("ihc_general_user_page");
		if ($account_page){
			$account_page = get_permalink($account_page);
		}
		//login page
		$login_page = get_option("ihc_general_login_default_page");
		if ($login_page){
			$login_page = get_permalink($login_page);
		}

		if ($l_id!==FALSE){
			$level_data = ihc_get_level_by_id($l_id);
			$level_name = $level_data['label'];
			$amount = $level_data['price'];
		} else {
			$l_id = '';
		}
		
		/// ANOUNT FROM ORDER
		if (!empty($dynamic_data['order_id'])){
			require_once IHC_PATH . 'classes/Orders.class.php';
			$temp_object = new Ump\Orders();
			$temp_order_data = $temp_object->get_data($dynamic_data['order_id']);
			if ($temp_order_data && isset($temp_order_data['amount_value'])){
				$amount = $temp_order_data['amount_value'];
			}
			unset($temp_object);
			unset($temp_order_data);
		}
		
		/// AVATAR 
		$ihc_avatar = '';
		$avatar = get_user_meta($u_id, 'ihc_avatar', true);
		if (strpos($avatar, "http")===0){
			$avatar_url = $avatar;
		} else {
			$avatar_url = wp_get_attachment_url($avatar);
		}
		$avatar = (empty($avatar_url)) ? IHC_URL . 'assets/images/no-avatar.png' : $avatar_url;
		if (!empty($avatar)){
			$ihc_avatar = '<img src="' . $avatar . '" class=""/>';	
		}
		/// AVATAR
		
		/// FALG
		$flag = ihc_user_get_flag($u_id);	
		
		$replace = array(
				"{username}" => $username,
				"{first_name}" => $first_name,
				"{last_name}" => $last_name,
				"{user_id}" => $u_id,
				"{current_level}" => $current_level,
				"{current_level_expire_date}" => $level_expire_time,
				"{level_list}" => $level_list,
				"{user_email}" => $user_email,
				"{account_page}" => $account_page,
				"{login_page}" => $login_page,
				"{blogname}" => $blogname,
				"{blogurl}" => $blogurl,
				"{level_id}" => $l_id,
				"{level_name}" => $level_name,
				"{amount}" => $amount,
				"{currency}" => $currency,
				"{siteurl}" => $site_url,
				"{ihc_avatar}" => $ihc_avatar,
				"{current_date}" => $current_date,
				"{user_registered}" => $user_registered,
				"{flag}" => $flag,
		);
		
		foreach (ihc_get_custom_constant_fields() as $k=>$v){
			$replace[$k] = get_user_meta($u_id, $v, TRUE);
			if (is_array($replace[$k])){
				$replace[$k] = implode(',', $replace[$k]);
			}
		}
		
		/*
		if (!empty($dynamic_data['{verify_email_address_link}'])){
			$replace['{verify_email_address_link}'] = $dynamic_data['{verify_email_address_link}'];
		}
		
		if (!empty($dynamic_data['{NEW_PASSWORD}'])){
			$replace['{NEW_PASSWORD}'] = $dynamic_data['{NEW_PASSWORD}'];
		}
		*/
		foreach ($dynamic_data as $k=>$v){
			$replace[$k] = $v;
		}
		
		foreach ($replace as $k=>$v){
			$str = str_replace($k, $v, $str);
		}

	}
	return $str;
}

function ihc_user_get_flag($uid=0, $class='ihc-public-flag'){
	/*
	 * @param int (user id), string (class of image)
	 * @return string (image)
	 */
	$flag = get_user_meta($uid, 'ihc_country', true);
	if (empty($flag)){
		return '';
	} else {
		$countries = ihc_get_countries();
		$key = $flag;
		$flag = strtolower($flag);
		$country = $countries[strtoupper($key)];
		$title = (empty($country)) ? '' : $country;			
		return '<img src="' . IHC_URL . 'assets/flags/' . $flag . '.svg" class="' . $class . '" title="' . $title . '" />';
	}		 
}

function ihc_random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'){
	/*
	 * @param length - int, keyspace - string
	 * @return string
	 */
	$str = '';
	$max = mb_strlen($keyspace, '8bit') - 1;
	for ($i = 0; $i < $length; ++$i) {
		$str .= $keyspace[rand(0, $max)];
	}
	return $str;
}

function ihc_generate_alias_name($length=6, $check=array()){
	/*
	 * @param length, array
	 * @return string
	 */
	$keyspace = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$str = '';
	$max = mb_strlen($keyspace, '8bit') - 1;
	for ($i = 0; $i < $length; ++$i) {
		$str .= $keyspace[rand(0, $max)];
	}
	while (in_array($str, $check)){
		ihc_generate_alias_name($length, $check);
	}
	return $str;
}

function ihc_cancel_twocheckout_subscription($transaction_id){
	/*
	 * @param string
	 * @return boolean
	 */
	require_once IHC_PATH . 'classes/twocheckout/Twocheckout.php';
	//set API connection vars
	$api_user = get_option('ihc_twocheckout_api_user');
	$api_pass = get_option('ihc_twocheckout_api_pass');
	$api_private_key = get_option('ihc_twocheckout_private_key');
	$account_num = get_option('ihc_twocheckout_account_number');	
	$sandbox = get_option('ihc_twocheckout_sandbox');
	
	Twocheckout::sellerId($account_num);
	Twocheckout::privateKey($api_private_key);
	Twocheckout::username($api_user);
	Twocheckout::password($api_pass);
	Twocheckout::$verifySSL = false;

	$params = array();
	$params['sale_id'] = $transaction_id;
	if($sandbox){
		Twocheckout::sandbox(true);
		$params['demo'] = 'Y';
	} else {
		Twocheckout::sandbox(false);
	}
	try {
		$result = Twocheckout_Sale::stop( $params );
	} catch(Exception $e){
		
	}
	
	// Successfully cancelled
	if (isset($result['response_code']) && $result['response_code'] === 'OK') {
		return true;
	} else {
		//fail
		return false;
	}
}

function ihc_show_cancel_level_link($u_id, $l_id){
	/*
	 * @param user id, level id
	 * @return bool, true if we can show the cancel buntton
	 */
	$level_data = ihc_get_level_by_id($l_id);
	if (isset($level_data['access_type']) && $level_data['access_type']=='regular_period'){//only for reccurence
		global $wpdb;
		$data = $wpdb->get_row("SELECT status FROM " . $wpdb->prefix . "ihc_user_levels WHERE user_id='" . $u_id . "' AND level_id='" . $l_id . "';");
		if ($data && $data->status){
			return TRUE;
		}
	}
	return FALSE;
}

function ihc_cancel_level($u_id, $l_id){
	/*
	 * cancel subscription from payments services
	 * @param u_id - user id (int), l_id - level id (int)
	 * @return none
	 */
	$txn_id = '';
	$payment_type = '';
	global $wpdb;
	$table = $wpdb->prefix . "indeed_members_payments";
	$q = $wpdb->prepare("SELECT txn_id, payment_data FROM $table WHERE u_id=%d ORDER BY paydate DESC;", $u_id);
	$data = $wpdb->get_results($q);
	//we need to select last transaction that involved this level id
	foreach ($data as $obj){
		$arr = json_decode($obj->payment_data, TRUE);
		
		$completed = FALSE;
		if (!empty($arr['payment_status'])){
			$completed = TRUE;
		} else if (isset($arr['x_response_code']) && ($arr['x_response_code'] == 1)){
			$completed = TRUE;
		} else if (isset($arr['code']) && ($arr['code'] == 2)){
			$completed = TRUE;
		} else if (isset($arr['message']) && $arr['message']=='success'){
			$completed = TRUE;
		}
		
		if (!$completed){
			continue;	
		}
		
		if (isset($arr['ihc_payment_type'])){
			//in case we know the payment type
			$payment_type = $arr['ihc_payment_type'];
			switch ($arr['ihc_payment_type']){
				case 'paypal':
					$custom = json_decode(stripslashes($arr['custom']), TRUE);
					if (isset($custom['level_id']) && $custom['level_id']==$l_id){
						//it what we looking for
						$txn_id = $obj->txn_id;
						$payment_type = 'paypal';
						break 2;
					}
					break;
				case 'stripe':
				case 'twocheckout':
				case 'authorize':
					if (isset($arr['level']) && $arr['level']==$l_id){
						$txn_id = $obj->txn_id;
						break 2;
					}
					break;
			}//end of switch
		} else {
			//don't know from where the payment was made
			$payment_type = get_option('ihc_payment_selected');
			if (isset($arr['custom'])){
				$custom = json_decode($arr['custom'], TRUE);
				if ($custom['level_id']==$l_id){
					//it's paypal and it's the level we want
					$txn_id = $obj->txn_id;
					$payment_type = 'paypal';
					break;
				}
			} else if (isset($arr['level']) && $arr['level']==$l_id){
				$txn_id = $obj->txn_id;
			}
		}
		
	}//end of foreach

	if ($txn_id && $payment_type){
		//if we have the transaction id, payment type && user id we can go further
		switch ($payment_type){
			case 'paypal':
				$sandbox = get_option('ihc_paypal_sandbox');
				$alias = get_option('ihc_paypal_email');
				if ($sandbox){
					$url = "https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=" . urlencode($alias);
				} else {
					$url = "https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias=" . urlencode($alias);
				}
				wp_redirect($url);
				exit();
				break;
			case 'stripe':
				if (!class_exists('ihcStripe')){
					require_once IHC_PATH . 'classes/ihcStripe.class.php';					
				}
				$obj = new ihcStripe();
				$obj->cancel_subscription($txn_id);
				break;
			case 'twocheckout':				
				ihc_cancel_twocheckout_subscription($txn_id);
				break;
			case 'authorize':
				if (!class_exists('ihcAuthorizeNet')){
					require_once IHC_PATH . 'classes/ihcAuthorizeNet.class.php';					
				}
				$obj = new ihcAuthorizeNet();
				$unsubscribe = $obj->cancel_subscription($txn_id);
				break;
		}

		//after we cancel the subscription in payment service, we must modify the status in our db
		$table = $wpdb->prefix . "ihc_user_levels";
		$q = $wpdb->prepare("UPDATE $table SET status='0' WHERE user_id=%d AND level_id=%d;", $u_id, $l_id);
		$wpdb->query($q);
		
		do_action('ihc_action_after_cancel_subscription', $u_id, $l_id);
	}
}

function ihc_show_renew_level_link($l_id){
	/*
	 * @param level id
	 * @return bool, true if we must show to renew level link
	 */
	$level_data = ihc_get_level_by_id($l_id);
	if (isset($level_data['access_type']) && $level_data['access_type']=='limited'){
		return TRUE;
	}
	return FALSE;
}


function ihc_stripe_renew_script($form_id){
	/*
	 * @param string
	 * @return string
	 */
	$publishable_key = get_option('ihc_stripe_publishable_key');
	global $current_user;
	$uid = (!empty($current_user) && !empty($current_user->ID)) ? $current_user->ID : 0;
	$str ='';
	$str .= '<script src="https://checkout.stripe.com/checkout.js"></script>
	<script>
	var renew_stripe = StripeCheckout.configure({
		key: "' . $publishable_key . '",
		locale: "auto",
		token: function(response) {
			var input = jQuery("<input type=hidden name=stripeToken id=stripeToken />").val(response.id);
			var email = jQuery("<input type=hidden name=stripeEmail id=stripeEmail />").val(response.email);
			jQuery("' . $form_id . '").append(input);
			jQuery("' . $form_id . '").append(email);
			jQuery("' . $form_id . '").submit();
		}
	});
	
	function ihc_stripe_renew_payment(l_name, l_amount, lid){
		var l_amount = l_amount * 100;
		if (l_amount<50){
			l_amount = 50;
		}
		jQuery("#ihc_renew_level").val(lid);
		if (jQuery("#ihc_coupon").val()){
			jQuery.ajax({
						type : "post",
						url : "' . IHC_URL . 'public/custom-ajax.php",
						data : {
							    ihc_coupon: jQuery("#ihc_coupon").val(),
							    l_id: lid,
							    initial_price: l_amount
						},
						success: function (data) {
							if (data!=0){
								if (jQuery("#ihc_coupon").val()){
									jQuery("' . $form_id . '").append("<input type=hidden value=" + jQuery("#ihc_coupon").val() + " name=ihc_coupon />");
								}
								var obj = jQuery.parseJSON(data);
								if (typeof obj.price!="undefined"){
									var l_amount = obj.price;
									if (l_amount<50){
										l_amount = 50;
									}
									///								
										jQuery.ajax({
													type: "post",
													url: decodeURI(window.ihc_site_url)+"/wp-admin/admin-ajax.php",
										   	 		data: {
										   	 				action: "ihc_get_amount_plus_taxes_by_uid",
										               		uid: "' . $uid . '",	
										               		price: l_amount,								   	 					
										   	 		},
										   	 		success: function(data){
										   	 				if (data){
										   	 					var l_amount = data;
										   	 				} 
															renew_stripe.open({
																name: l_name,
																description: "Level "+lid,
																amount: l_amount,
															});								   	 					
										   	 		}
										});										
									///	
								}
							}
						}
			});
		} else {
			jQuery.ajax({
					type: "post",
					url: decodeURI(window.ihc_site_url)+"/wp-admin/admin-ajax.php",
		   	 		data: {
		   	 				action: "ihc_get_amount_plus_taxes_by_uid",
		               		uid: "' . $uid . '",	
		               		price: l_amount,								   	 					
		   	 		},
		   	 		success: function(data){
	   	 				if (data){
	   	 					var l_amount = data;
	  	 				} 
						renew_stripe.open({
							name: l_name,
							description: "Level "+lid,
							amount: l_amount,
						});							   	 					
					}
			});
		}
	}
	</script>';
	return $str;
}

function ihc_get_user_level_status_for_ac($u_id, $l_id){
	/*
	 * @param int, int
	 * @return string
	 */
	$status = __('Active', 'ihc');
	global $wpdb;
	$data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix ."ihc_user_levels WHERE user_id='$u_id' AND level_id='$l_id' ");
	if ($data){
		if ($data->status==0){
			$status =  __('Canceled', 'ihc');
		} else {
			$grace_period = get_option('ihc_grace_period');
			$expire_time_after_grace = strtotime($data->expire_time) + $grace_period * 24 * 60 * 60;
			if ($expire_time_after_grace<0){
				$status = __("Hold", 'ihc');
			} else if (time()>$expire_time_after_grace){
				$status = __("Expired", 'ihc');
			} else if (strtotime($data->start_time)>time()){
				$status = __("Inactive", 'ihc');
			}
		}	
	}	
	return $status;
}

function ihc_is_level_on_hold($uid=0, $lid=0){
	/*
	 * @param int, int
	 * @return boolean
	 */	
	$bool = FALSE;
	global $wpdb;
	$table = $wpdb->prefix . "ihc_user_levels";
	$data = $wpdb->get_row("SELECT * FROM $table WHERE user_id=$uid AND level_id=$lid;");
	if ($data){
		if ($data->status==0){
			return $bool;
		} else {
			$grace_period = get_option('ihc_grace_period');
			$expire_time_after_grace = strtotime($data->expire_time) + $grace_period * 24 * 60 * 60;
			if ($expire_time_after_grace<0){
				return TRUE;
			}
		} 
	}
	return $bool;
}

function ihc_set_level_status($u_id='', $l_id='', $status=''){
	/*
	 * @param: user id, level id, status
	 * status must be : 1 (in case the level can be renew) or 2 (in case of level it's renewed)
	 * @return none
	 */
	global $wpdb;
	$table = $wpdb->prefix . 'ihc_user_levels';
	$q = $wpdb->prepare("SELECT * FROM $table WHERE user_id=%d AND level_id=%d ;", $u_id, $l_id);
	$exists = $wpdb->get_row($q);
	if ($exists){
		$q = $wpdb->prepare("UPDATE $table SET status='$status' WHERE user_id=%d AND level_id=%d ;", $u_id, $l_id);
		$wpdb->query($q);
	}
}

function ihc_check_social_status($type){
	/*
	 * @param string name of social media
	 * @return array
	 */
	$return = array();
	$return['active'] = '';
	$return['status'] = 0;
	$return['settings'] = 'Uncompleted';
	switch ($type){
		case 'fb':
			$arr = ihc_return_meta_arr('fb');
			if (!empty($arr['ihc_fb_app_id']) && !empty($arr['ihc_fb_app_secret'])){
				$return['settings'] = 'Completed';
			}
			if (!empty($arr['ihc_fb_status'])){
				$return['status'] = 1;
				$return['active'] = 'fb-active';
			}
			break;
		case 'tw':
			$arr = ihc_return_meta_arr('tw');
			if (!empty($arr['ihc_tw_app_key']) && !empty($arr['ihc_tw_app_secret'])){
				$return['settings'] = 'Completed';
			}
			if (!empty($arr['ihc_tw_status'])){
				$return['status'] = 1;
				$return['active'] = 'tw-active';
			}
			break;
		case 'in':
			$arr = ihc_return_meta_arr('in');
			if (!empty($arr['ihc_in_app_key']) && !empty($arr['ihc_in_app_secret'])){
				$return['settings'] = 'Completed';
			}
			if (!empty($arr['ihc_in_status'])){
				$return['status'] = 1;
				$return['active'] = 'in-active';
			}				
			break;
		case 'tbr':
			$arr = ihc_return_meta_arr('tbr');
			if (!empty($arr['ihc_tbr_app_key']) && !empty($arr['ihc_tbr_app_secret'])){
				$return['settings'] = 'Completed';
			}
			if (!empty($arr['ihc_tbr_status'])){
				$return['status'] = 1;
				$return['active'] = 'tbr-active';
			}
			break;
		case 'ig':
			$arr = ihc_return_meta_arr('ig');
			if (!empty($arr['ihc_ig_app_id']) && !empty($arr['ihc_ig_app_secret'])){
				$return['settings'] = 'Completed';
			}
			if (!empty($arr['ihc_ig_status'])){
				$return['status'] = 1;
				$return['active'] = 'ig-active';
			}
			break;
		case 'vk':
			$arr = ihc_return_meta_arr('vk');
			if (!empty($arr['ihc_vk_app_id']) && !empty($arr['ihc_vk_app_secret'])){
				$return['settings'] = 'Completed';
			}
			if (!empty($arr['ihc_vk_status'])){
				$return['status'] = 1;
				$return['active'] = 'vk-active';
			}				
			break;
		case 'goo':
			$arr = ihc_return_meta_arr('goo');
			if (!empty($arr['ihc_goo_app_id']) && !empty($arr['ihc_goo_app_secret'])){
				$return['settings'] = 'Completed';
			}
			if (!empty($arr['ihc_goo_status'])){
				$return['status'] = 1;
				$return['active'] = 'goo-active';
			}				
			break;
	}
	return $return;
}

function ihc_generate_color_hex(){
	/*
	 * @param none
	 * @return string
	 */
	$colors =  array('#0a9fd8', '#38cbcb', '#27bebe', '#0bb586', '#94c523', '#6a3da3', '#f1505b', '#ee3733', '#f36510', '#f8ba01');
	return $colors[rand(0, (count($colors)-1) )];
}

//=================== COUPONS
function ihc_create_coupon($post_data=array()){
	/*
	 * @param post_data (array)
	 * @return boolean
	 */
	if ($post_data){
		global $wpdb;
		if (!empty($post_data['how_many_codes'])){
			// ============== MULTIPLE COUPONS ===============//
			$settings = serialize($post_data);
			$prefix = $post_data['code_prefix'];
			$prefix_length = strlen($post_data['code_prefix']);
			$length = $post_data['code_length'] - $prefix_length;
			$limit = $post_data['how_many_codes'];
			unset($post_data['how_many_codes']);
			unset($post_data['code_prefix']);
			unset($post_data['code_length']);
			if (empty($post_data['discount_value'])){
				return;
			}
			while ($limit){
				$code = ihc_random_str($length);
				$code = $prefix . $code;	
				$code = str_replace(' ', '', $code);
				$code = ihc_make_string_simple($code);
				$data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix ."ihc_coupons WHERE code='" . $code . "';");
				if ($data){
					continue;
				}				
				$wpdb->query("INSERT INTO " . $wpdb->prefix ."ihc_coupons VALUES(null, '" . $code ."', '" . $settings . "', 0, 1);");
				$limit--;				
			}			
		} else {
			//============== SINGLE COUPON ==================//
			if (empty($post_data['code']) || empty($post_data['discount_value'])){
				return FALSE;
			}
			//check if this code already exists
			$data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix ."ihc_coupons WHERE code='" . $post_data['code'] . "';");
			if ($data){
				return FALSE;
			}
			$code = str_replace(' ', '', $post_data['code']);
			$code = ihc_make_string_simple($code);
			unset($post_data['code']);
			if (isset($post_data['special_status'])){
				$status = $post_data['special_status'];
				unset($post_data['special_status']);
			} else {
				$status = 1;
			}
			$settings = serialize($post_data);
			$wpdb->query("INSERT INTO " . $wpdb->prefix ."ihc_coupons VALUES(null, '" . $code ."', '" . $settings . "', 0, $status);");	
			return TRUE;
		}
	}
}

function ihc_update_coupon($post_data=array()){
	/*
	 * @param post_data (array)
	 * @return none
	 */
	if ($post_data){
		if (empty($post_data['code']) || empty($post_data['discount_value'])){
			return FALSE;
		}		
		global $wpdb;
		$id = $post_data['id'];
		unset($post_data['id']);
		$data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix ."ihc_coupons WHERE id='" . $id . "';");
		if ($data){
			$code = str_replace(' ', '', $post_data['code']);
			$code = ihc_make_string_simple($post_data['code']);
			unset($post_data['code']);
			unset($post_data['id']);
			$settings = serialize($post_data);
			$wpdb->query("UPDATE " . $wpdb->prefix ."ihc_coupons
							SET code='" . $code . "', settings='" . $settings . "'
							WHERE id='".$id."';
			");
		}		
	}
}

function ihc_delete_coupon($id){
	/*
	 * @param id (int)
	 * @return none
	 */
	global $wpdb;
	$exists = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."ihc_coupons WHERE id='" . $id . "';");
	if ($exists){
		$wpdb->query("DELETE FROM ".$wpdb->prefix."ihc_coupons WHERE id='" . $id . "';");
	}
}

function ihc_submit_coupon($code=''){
	/*
	 * @param string
	 * @return boolean
	 */
	global $wpdb;
	//check if this code already exists
	$code = str_replace(' ', '', $code);
	$q = $wpdb->prepare("SELECT submited_coupons_count FROM " . $wpdb->prefix ."ihc_coupons WHERE code=%s ;", $code);
	$data = $wpdb->get_row($q);
	if (isset($data->submited_coupons_count)){
		$submited_coupons_count = (int)$data->submited_coupons_count;
		$submited_coupons_count++;
		$table = $wpdb->prefix ."ihc_coupons";
		$q = $wpdb->prepare("UPDATE $table
								SET submited_coupons_count=%d
								WHERE code=%s;", $submited_coupons_count, $code );
		$wpdb->query($q);
		
		/// 
		do_action('ump_coupon_code_submited', $code);
		///
		
		return TRUE;
	}
	return FALSE;
}

function ihc_get_coupon_by_code($code=''){
	/*
	 * @param string
	 * @return array
	 */
	$return_data = array();
	if ($code){
		global $wpdb;
		$code = str_replace(' ', '', $code);
		$q = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ihc_coupons	WHERE code=%s ;", $code);
		$data = $wpdb->get_row($q);
		if ($data){
			$return_data = unserialize($data->settings);
			$return_data['code'] = $data->code;
			$return_data['submited_coupons_count'] = $data->submited_coupons_count;			
		}
	}
	return $return_data;
}

function ihc_get_all_coupons(){
	/*
	 * @param none
	 * @return array
	 */
	$return_data = array();
	global $wpdb;
	$data = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "ihc_coupons WHERE status=1;"); 
	if ($data){
		foreach ($data as $obj){
			$return_data[$obj->id]['code'] = $obj->code;
			$return_data[$obj->id]['settings'] = unserialize($obj->settings);
			$return_data[$obj->id]['submited_coupons_count'] = $obj->submited_coupons_count;
		}
	}
	return $return_data;
}

function ihc_get_coupon_by_id($id=0){
	/*
	 * @param string
	 * @return array
	 */
	$arr = array();
	if ($id){
		global $wpdb;
		$data = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "ihc_coupons
									WHERE id='" . $id . "';");
		if ($data && isset($data->code) && isset($data->settings)){
			$arr = unserialize($data->settings);
			$arr['code'] = $data->code;			
		}
	} else {
		$arr = array(
						"code" => "",
						"discount_type" => "price",
						"discount_value" => '',
						"period_type" => "date_range",
						"repeat" => "",
						"target_level" => "",
						"reccuring" => "",
						"start_time" => '',
						"end_time" => '',
						"box_color" => ihc_generate_color_hex(),
						"description" => "",
					);
	}
	return $arr;	
}

function ihc_check_coupon($coupon='', $level_id=-1){
	/*
	 * @param coupon string, level id int
	 * @return array
	 */
	$empty = array();
	if (!$coupon || $level_id==-1){
		return $empty;
	}
	$coupon_data = ihc_get_coupon_by_code($coupon);
	if ($coupon_data){
		if (!empty($coupon_data['repeat']) && ($coupon_data['repeat']<=$coupon_data['submited_coupons_count'])){
			//out of repeat number
			return $empty;
		}
		if ($coupon_data['period_type']=='date_range' && !empty($coupon_data['start_time']) && !empty($coupon_data['end_time'])){
			//we must check the time
			$start_time = strtotime($coupon_data['start_time']);
			$end_time = strtotime($coupon_data['end_time']);
			$current_time = time();
			if ($start_time>$current_time){
				//not begin coupon time
				return $empty;
			}
			if ($current_time>$end_time){
				//out of date
				return $empty;
			}
		}
		if ($coupon_data['target_level']>-1){
			if ($coupon_data['target_level']!=$level_id){
				//it's not the target level
				return $empty;
			}
		}
		return array(	
						"discount_type" => $coupon_data['discount_type'],
						"discount_value" => $coupon_data['discount_value'],
						"reccuring" => $coupon_data['reccuring'],
						"code" => $coupon,
		);
	}
	return $empty;
}

function ihc_coupon_return_price_after_decrease($price=0, $coupon_data=array(), $update_coupon_count=TRUE){
	/*
	 * @param price int, coupon data array, update coupon count bool
	 * @return price int
	 */
	if ($price && $coupon_data){
		if ($coupon_data['discount_type']=='percentage'){
			$price = $price - ($price*$coupon_data['discount_value']/100);
		} else {
			$price = $price - $coupon_data['discount_value'];
		}
		$price = round($price, 2);
		if ($update_coupon_count){
			//lets update the coupon count in db
			ihc_submit_coupon($coupon_data['code']);			
		}
	}
	return $price;
}

function ihc_get_discount_value($price=0, $coupon_data=array()){
	/*
	 * @param int, int
	 * @return none
	 */
	if ($price && $coupon_data){
		if ($coupon_data['discount_type']=='percentage'){
			return ($price*$coupon_data['discount_value']/100);
		} else {
			return $coupon_data['discount_value'];
		}
	}	 
}


function ihc_dont_pay_after_discount($level_id, $coupon, $level_arr, $update_coupon_count=FALSE){
	/*
	 * if the price after discount if 0 will return TRUE
	 * @param level_id - int, coupon - string, level_arr - array, update_coupon_count - array
	 * @return boolean 
	 */
	if (!empty($coupon)){
		if (isset($level_arr['access_type']) && $level_arr['access_type']!='regular_period'){
			//not reccurence
			$coupon_data = ihc_check_coupon($coupon, $level_id);
			$level_arr['price'] = ihc_coupon_return_price_after_decrease($level_arr['price'], $coupon_data, FALSE);
			if ($level_arr['price']==0){
				if ($update_coupon_count){
					//update coupon count
					ihc_submit_coupon($coupon);					
				}
				return TRUE;
			}
		}		
	}
	return FALSE;
}

function ihc_get_redirect_link_by_label($name='', $uid=0){
	/*
	 * @param string, int (USER ID used for login first redirect, when current_user is not available)
	 * @return string
	 */
	if ($name=='#individual_page#'){
		if (empty($uid)){
			global $current_user;
			if (!empty($current_user->ID)){
				$uid = $current_user->ID;
			}
		}
		if (!empty($uid)){
			$individual_page = get_user_meta($uid, 'ihc_individual_page', TRUE);
			if ($individual_page){
				$redirect_to = get_permalink($individual_page);
				if ($redirect_to){
					return $redirect_to;
				}
			}
		}	
	} else {
		$data = get_option("ihc_custom_redirect_links_array");
		if (isset($data[$name])){
			return $data[$name];
		}		
	}
	return '';
}

function ihc_run_opt_in($email='', $target_opt_in=''){
	/*
	 * @param string
	 * @return none
	 */
	if (!$target_opt_in){
		$target_opt_in = get_option('ihc_register_opt-in-type');		
	}
	do_action('ihc_run_opt_in_action', $email, $target_opt_in);
	
	if ($target_opt_in && $email){
		if (!class_exists('IhcMailServices')){
			require_once IHC_PATH . 'classes/IhcMailServices.class.php';
		}
		$indeed_mail = new IhcMailServices();
		$indeed_mail->dir_path = IHC_PATH . 'classes';
		switch ($target_opt_in){
			case 'aweber':
				$awListOption = get_option('ihc_aweber_list');
				if ($awListOption){
					$aw_list = str_replace('awlist', '', $awListOption);
					$consumer_key = get_option( 'ihc_aweber_consumer_key' );
					$consumer_secret = get_option( 'ihc_aweber_consumer_secret' );
					$access_key = get_option( 'ihc_aweber_acces_key' );
					$access_secret = get_option( 'ihc_aweber_acces_secret' );						
					if ($consumer_key && $consumer_secret && $access_key && $access_secret){
						$return = $indeed_mail->indeed_aWebberSubscribe( $consumer_key, $consumer_secret, $access_key, $access_secret, $aw_list, $email );
					}
				}
				break;
	
			case 'email_list':
				$email_list = get_option('ihc_email_list');
				$email_list .= $email . ',';
				update_option('ihc_email_list', $email_list);
				break;
	
			case 'mailchimp':
				$mailchimp_api = get_option( 'ihc_mailchimp_api' );
				$mailchimp_id_list = get_option( 'ihc_mailchimp_id_list' );
				if ($mailchimp_api && $mailchimp_id_list){
					$indeed_mail->indeed_mailChimp( $mailchimp_api, $mailchimp_id_list, $email );
				}
				break;
	
			case 'get_response':
				$api_key = get_option('ihc_getResponse_api_key');
				$token = get_option('ihc_getResponse_token');
				if ($api_key && $token){
					$indeed_mail->indeed_getResponse( $api_key, $token, $email );
				}
				break;
	
			case 'campaign_monitor':
				$listId = get_option('ihc_cm_list_id');
				$apiID = get_option('ihc_cm_api_key');
				if ($listId && $apiID){
					$indeed_mail->indeed_campaignMonitor( $listId, $apiID, $email );
				}
				break;
	
			case 'icontact':
				$appId = get_option('ihc_icontact_appid');
				$apiPass = get_option('ihc_icontact_pass');
				$apiUser = get_option('ihc_icontact_user');
				$listId = get_option('ihc_icontact_list_id');
				if ($appId && $apiPass && $apiUser && $listId){
					$indeed_mail->indeed_iContact( $apiUser, $appId, $apiPass, $listId, $email );
				}
				break;
	
			case 'constant_contact':
				$apiUser = get_option('ihc_cc_user');
				$apiPass = get_option('ihc_cc_pass');
				$listId = get_option('ihc_cc_list');
				if ($apiUser && $apiPass && $listId){
					$indeed_mail->indeed_constantContact($apiUser, $apiPass, $listId, $email);
				}
				break;
	
			case 'wysija':
				$listID = get_option('ihc_wysija_list_id');
				if ($listID){
					$indeed_mail->indeed_wysija_subscribe( $listID, $email );
				}
				break;
	
			case 'mymail':
				$listID = get_option('ihc_mymail_list_id');
				if ($listID){
					$indeed_mail->indeed_myMailSubscribe( $listID, $email );
				}
				break;
	
			case 'madmimi':
				$username = get_option('ihc_madmimi_username');
				$api_key =  get_option('ihc_madmimi_apikey');
				$listName = get_option('ihc_madmimi_listname');
				if ($username && $api_key && $listName){
					$indeed_mail->indeed_madMimi($username, $api_key, $listName, $email);
				}
				break;
			case 'active_campaign':
				$api_url = get_option('ihc_active_campaign_apiurl');
				$api_key =  get_option('ihc_active_campaign_apikey');
				if ($api_url && $api_key){
					$indeed_mail->add_contanct_to_active_campaign($api_url, $api_key, $email, '', '');
				}				
				break;
		}
	}
}

function ihc_get_custom_constant_fields(){
	/*
	 * @param none
	 * @return array
	 */
	$data = get_option('ihc_user_fields');
	foreach ($data as $arr){
		$fields["{CUSTOM_FIELD_" . $arr['name'] ."}"] = $arr['name'];
	}
	$diff = array('ihc_social_media', 'ihc_coupon', 'recaptcha', 'tos', 'pass2', 'pass1', 'user_login', 'user_email', 'confirm_email', 'first_name', 'last_name', 'ihc_avatar');
	$fields = array_diff($fields, $diff);
	return $fields;
}

function ihc_update_stripe_subscriptions(){
	/*
	 * Update Stripe Transactions ID, run this just once on update plugin.
	 * @param none
	 * @return none
	 */
	global $wpdb;
	$data = $wpdb->get_results("SELECT id, txn_id, payment_data FROM " . $wpdb->prefix . "indeed_members_payments
									WHERE txn_id LIKE 'ch_%';");
	if (count($data)){

		//loading stripe libs
		require_once IHC_PATH . 'classes/stripe/init.php';
		$secret_key = get_option('ihc_stripe_secret_key');
		\Stripe\Stripe::setApiKey($secret_key);

		foreach ($data as $obj){
			$payment_data = json_decode($obj->payment_data);
			if (!empty($payment_data->customer)){
				$replace_txn_id = $payment_data->customer;
			} else {
				$stripe_obj = \Stripe\Charge::retrieve($obj->txn_id);
				if (!empty($stripe_obj->customer)){
					$replace_txn_id = $stripe_obj->customer;
				}
				unset($stripe_obj);
			}
			if (!empty($replace_txn_id)){
				$wpdb->query("UPDATE " . $wpdb->prefix . "indeed_members_payments
								SET txn_id='" . $replace_txn_id . "'
								WHERE id= '" . $obj->id . "';
						");
				unset($replace_txn_id);
			}
		}//end foreach
	}
}

function ihc_get_active_payments_services($only_keys=FALSE){
	/*
	 * @param none
	 * @return array
	 */
	$arr = array();
	if (!function_exists('ihc_check_payment_status')){
		require_once IHC_PATH . 'admin/includes/functions.php';
	}
	$gateways = array(
						'paypal' => 'PayPal', 
					  	'authorize' => 'Authorize', 
					  	'stripe' => 'Stripe', 
					  	'twocheckout' => '2Checkout', 
					 	'bank_transfer' => 'Bank Transfer',
						'braintree' => 'Braintree',		
						'payza' => 'Payza',		  
	);
	
	$gateways_without_labels = array();
	foreach ($gateways as $key=>$value){
		$order = get_option('ihc_' . $key . '_select_order');
		if ($order===FALSE){
			$order = array_search($key, array_keys($gateways));
		}
		while (!empty($gateways_without_labels[$order])){
			$order = $order+1;
		}
		$gateways_without_labels[$order] = $key;
	}
	ksort($gateways_without_labels);
	
	foreach ($gateways_without_labels as $k){
		$data = ihc_check_payment_status($k);
		if ($data['status'] && $data['settings']=='Completed'){
			if ($only_keys){
				$arr[] = $k;
			} else {
				$arr[$k] = $gateways[$k];
			}
		}
	}
	return $arr;
}

function ihc_get_active_payment_services(){
	/*
	 * @param none
	 * @return array
	 */
	 $array = array();
	 $gateways = array( 'paypal' => 'PayPal', 
					    'authorize' => 'Authorize', 
					    'stripe' => 'Stripe', 
					    'twocheckout' => '2Checkout', 
					    'bank_transfer' => 'Bank Transfer',
					    'braintree' => 'Braintree',
					    'payza' => 'Payza',
	 );
	 foreach ($gateways as $k=>$v){
		$data = ihc_check_payment_status($k);
		if ($data['status'] && $data['settings']=='Completed'){
			$array[$k] = $gateways[$k];
		}
	 }  
	 return $array;
			 	 
}

function ihc_is_level_reccuring($lid=-1){
	/*
	 * @param int
	 * @return bool
	 */
	if ($lid>-1){
		$level_data = ihc_get_level_by_id($lid);
		if (!empty($level_data['access_type']) && $level_data['access_type']=='regular_period'){
			return TRUE;
		}
	}
	return FALSE;
}

function ihc_print_payment_select($default_payment='', $field_data = array(), $payments_available, $is_reccurence=0, $required_field=FALSE){
	/*
	 * @param string, array, array, int, bool
	 * @return string
	 */
	
	$str = '';
	if (empty($field_data['theme'])){
		$field_data['theme'] = 'ihc-select-payment-theme-1';
	}
	$css_class = $field_data['theme'];
	$str .= '<div class="iump-form-line-register ' . $css_class . ' ' . @$field_data['class'] . '">';
	$str .= '<label class="iump-labels-register">';
	if ($required_field){
		$str .= '<span style="color: red;">*</span>';
	}
	if (!empty($field_data['label'])){
		$str .= ihc_correct_text($field_data['label']);
	} else {
		$str .= __('Select Payment Method', 'ihc');
	}
	$str .= '</label>';
	
	if ($field_data['theme']=='ihc-select-payment-theme-3') {
		$str .= '<select onChange="ihc_payment_gateway_update(this.value, ' . $is_reccurence . ');">';
	}
	
	foreach ($payments_available as $k => $v){

		$onclick = "ihc_payment_gateway_update('" . $k . "', " . $is_reccurence . ");";
		
		$label = get_option('ihc_' . $k . '_label');
		if (empty($label)){
			$label = $v;
		}
		
		if ($field_data['theme']=='ihc-select-payment-theme-1'){
			$selected = ($default_payment==$k) ? 'checked' : '';
			$str .= '<div class="iump-form-paybox"><input type="radio" name="ihc_payment_gateway_radio" value="' . $k . '" onClick="' . $onclick . '" ' . $selected . ' />' . ihc_correct_text($label) . '</div>';			
		} else if ($field_data['theme']=='ihc-select-payment-theme-2'){
			$onclick = "ihc_payment_select_icon('".$k."');" . $onclick;
			$class = ($default_payment==$k) ? 'ihc-payment-select-img-selected' : '';
			$str .= '<div class="iump-form-paybox" onClick="' . $onclick . '" class="ihc-payment-icon-wrap">';
			$str .= '<img src="' . IHC_URL . 'assets/images/'.$k.'.png" class="ihc-payment-icon ' . $class . '" id="ihc_payment_icon_' . $k . '"/>';
			$str .= '</div>';			
		} else if ($field_data['theme']=='ihc-select-payment-theme-3'){
			$selected = ($default_payment==$k) ? 'selected' : '';
			$str .= '<option value="' . $k . '" ' . $selected . '>' . ihc_correct_text($label) . '</option>';
		}
	}
	
	if ($field_data['theme']=='ihc-select-payment-theme-3') {
		$str .= '</select>';
	}
	if (!empty($field_data['sublabel'])){
		$str .= '<label class="iump-form-sublabel">' . ihc_correct_text($field_data['sublabel']) . '</label>';
	}
	$str .= '</div>';
	return $str;
}

function ihc_check_payment_available($type=''){
	/*
	 * check if a payment service it's enabled and has the required keys set
	 * @param string - type of payment
	 * @return bool
	 */
	if ($type){
		$payment_metas = ihc_return_meta_arr('payment_' . $type);
		switch ($type){
			case 'paypal':
				if (!empty($payment_metas['ihc_paypal_email']) && !empty($payment_metas['ihc_paypal_status'])){
					return TRUE;
				}
				break;
			case 'authorize':
				if (!empty($payment_metas['ihc_authorize_login_id']) && !empty($payment_metas['ihc_authorize_transaction_key']) && !empty($payment_metas['ihc_authorize_status'])){
					return TRUE;
				}
				break;
			case 'twocheckout':
				if (!empty($payment_metas['ihc_twocheckout_status']) && !empty($payment_metas['ihc_twocheckout_api_user'])
						&& !empty($payment_metas['ihc_twocheckout_api_pass']) && !empty($payment_metas['ihc_twocheckout_private_key'])
						&& !empty($payment_metas['ihc_twocheckout_account_number']) && !empty($payment_metas['ihc_twocheckout_secret_word'])){
					return TRUE;			
				}
				break;
			case 'bank_transfer':
				if (!empty($payment_metas['ihc_bank_transfer_status']) && !empty($payment_metas['ihc_bank_transfer_message'])){
					return TRUE;
				}
				break;
			case 'stripe':
				if (!empty($payment_metas['ihc_stripe_secret_key']) && !empty($payment_metas['ihc_stripe_publishable_key']) && !empty($payment_metas['ihc_stripe_status'])){
					return TRUE;
				}
				break;
			case 'braintree':
				if ($payment_metas['ihc_braintree_status'] == 1 && !empty($payment_metas['ihc_braintree_merchant_id']) && !empty($payment_metas['ihc_braintree_public_key']) && !empty($payment_metas['ihc_braintree_private_key'])){
					return TRUE;
				}					
				break;
			case 'payza':
				if (!empty($payment_metas['ihc_payza_status']) && !empty($payment_metas['ihc_payza_email'])){
					return TRUE;
				}				
				break;
		}
	}
	return FALSE;
}

function ihc_switch_role_for_user($uid=0){
	/*
	 * Switch User Role when Complete a Payment.
	 * @param int
	 * @return none
	 */
	$do_switch = get_option('ihc_automatically_switch_role');
	if ($do_switch && $uid){
		$data = get_userdata($uid);
		if ($data && isset($data->roles) && isset($data->roles[0]) && $data->roles[0]=='pending_user'){
			$role = get_option('ihc_automatically_new_role');
			if (empty($role)){
				$role = 'subscriber';
			}
			$arr['role'] = $role;
			$arr['ID'] = $uid;
			wp_update_user($arr);
		}
	}
}

function ihc_get_currencies_list($return='all'){
	/*
	 * @param string : all, basic, custom
	 * @return array
	 */
	$basic = array(
			'AUD' => 'Australian Dollar (A $)',
			'CAD' => 'Canadian Dollar (C $)',
			'EUR' => 'Euro (&#8364;)',
			'GBP' => 'British Pound (&#163;)',
			'JPY' => 'Japanese Yen (&#165;)',
			'USD' => 'U.S. Dollar ($)',
			'NZD' => 'New Zealand Dollar ($)',
			'CHF' => 'Swiss Franc',
			'HKD' => 'Hong Kong Dollar ($)',
			'SGD' => 'Singapore Dollar ($)',
			'SEK' => 'Swedish Krona',
			'DKK' => 'Danish Krone',
			'PLN' => 'Polish Zloty',
			'NOK' => 'Norwegian Krone',
			'HUF' => 'Hungarian Forint',
			'CZK' => 'Czech Koruna',
			'ILS' => 'Israeli New Shekel',
			'MXN' => 'Mexican Peso',
			'BRL' => 'Brazilian Real (only for Brazilian members)',
			'MYR' => 'Malaysian Ringgit (only for Malaysian members)',
			'PHP' => 'Philippine Peso',
			'TWD' => 'New Taiwan Dollar',
			'THB' => 'Thai Baht',
			'TRY' => 'Turkish Lira (only for Turkish members)',
			'RUB' => 'Russian Ruble',
	);
	$data = get_option('ihc_currencies_list');
	if ($return=='all'){
		if ($data!==FALSE && is_array($data)){
			return $basic+$data;
		}
		return $basic;
	} else if ($return=='basic'){
		return $basic;
	} else {
		return $data;
	}
}

function ihc_get_user_type(){
	/*
	 * @param none
	 * @return string
	 */
	$type = 'unreg';
	if (is_user_logged_in()){
		if (current_user_can('administrator')) return 'admin';
		//pending user
		global $current_user;
		if ($current_user){
			if (isset($current_user->roles[0]) && $current_user->roles[0]=='pending_user'){
				$type = 'pending';
			}else{
				$type = 'reg';
				$current_user = wp_get_current_user();
				$u_capability = get_user_meta($current_user->ID, 'ihc_user_levels', true);
				if ($u_capability!==FALSE && $u_capability!=''){
					$type = $u_capability;
				}
			}
		}
	}
	return $type;
}

function ihc_required_conditional_field_test($name='', $match_string=''){
	/*
	 * @param string, string
	 * @return string with error if it's case, empty string if it's ok
	 */
	$fields_meta = ihc_get_user_reg_fields();
	$key = ihc_array_value_exists($fields_meta, $name, 'name');
	if ($key!==FALSE && isset($fields_meta[$key]) && isset($fields_meta[$key]['type']) 
		&& $fields_meta[$key]['type']=='conditional_text' && !empty($fields_meta[$key]['conditional_text'])){
		if ($fields_meta[$key]['conditional_text']!=$match_string){
			return ihc_correct_text($fields_meta[$key]['error_message']);
		}
	}
	return '';
}

function ihc_get_public_register_fields($exclude_field=''){
	/*
	 * used only in register.php admin section, 
	 * @param string
	 * @return array
	 */
	$return = array();
	$fields_meta = ihc_get_user_reg_fields();
	foreach ($fields_meta as $arr){
		if ($arr['display_public_reg']>0 && !in_array($arr['type'], array('payment_select', 'social_media', 'upload_image', 'plain_text', 'file', 'capcha')) && $arr['name']!='tos'){
			if ($exclude_field && $exclude_field==$arr['name']){
				continue;
			}
			$return[$arr['name']] = $arr['name'];
		}
	}
	return $return;
}

function ihc_check_field_is_in_logic_conditional($field_name=''){
	/*
	 * check if this field it's mentionated in other fields conditions
	 * @param name of field
	 * @return boolean
	 */
	$fields_meta = ihc_get_user_reg_fields();
	$key = ihc_array_value_exists($fields_meta, $field_name, 'name');
	if ($key!==FALSE){
		if (!empty($fields_meta[$key]['conditional_logic_corresp_field']) && $fields_meta[$key]['conditional_logic_corresp_field']!=-1){
			return TRUE;
		}
	}
	return FALSE;
}

function ihc_check_envato_customer($code=''){
	/*
	 * @param stirng
	 * @return boolean
	 */
	if (!empty($code)){
		if (!class_exists('Envato_marketplace')){
			require_once IHC_PATH . 'classes/Envato_marketplace.class.php';
		}
		$api_key = 'z4dqvsth70g7qsr4f385fxjdt6wz9dfg';
		$user_name = 'azzaroco';
		$item_id = '12159253';
		$envato_object = new Envato_marketplaces($api_key);
		$buyer_verify = $envato_object->verify_purchase($user_name, $code);
		
		if ( isset($buyer_verify) && isset($buyer_verify->buyer)  && $buyer_verify->item_id==$item_id ){
			return TRUE;
		}	
	}	
	return FALSE;
}

function ihc_envato_licensing($code=''){
	/*
	 * @param string
	 * @return boolean
	 */
	$return = FALSE;
	if (ihc_check_envato_customer($code)){
		update_option('ihc_license_set', 1);
		$return = TRUE;
	} else {
		update_option('ihc_license_set', 0);
		$return = FALSE;
	}
	update_option('ihc_envato_code', $code);
	return $return;
}

function ihc_envato_check_license(){
	/*
	 * @param none
	 * @return bool
	 */
	$check = get_option('ihc_license_set');
	if ($check!==FALSE){
		if ($check==1)
			return TRUE;
		return FALSE;
	}
	return TRUE;
}

function ihc_inside_dashboard_error_license($global=FALSE){
	/*
	 * @param none
	 * @return string
	 */
	$url = get_admin_url() . 'admin.php?page=ihc_manage&tab=help';
	if (!IHCACTIVATEDMODE){
		if ($global) $class = 'error';
		else $class = 'ihc-error-global-dashboard-message';
		return "<div class='$class'>This is a Trial Version of <strong>Ultimate Membership Pro</strong> plugin. Please add your purchase code into Licence section to enable the Full Ultimate Membership Pro Version. Check your <a href='" . $url . "'>licence section</a>.</div>";
	}		
	return '';
}

function ihc_public_notify_trial_version(){
	/*
	 * @param none
	 * @return string
	 */
	$str = '';
	$str .= '<div class="ihc-public-trial-version">';
	$str .= "This is a Trial Version of <strong>Ultimate Membership Pro</strong> plugin. Please add your purchase code into Licence section to enable the Full Ultimate Membership Pro Version.";
	$str .= '</div>';
	return $str;
}

function ihc_make_string_simple($str=''){
	/*
	 * @param string
	 * @return string
	 */
	if (!empty($str)){
		$str = trim($str);
		$str = str_replace(' ', '_', $str);
		$str = preg_replace("/[^A-Za-z0-9_]/", '', $str);//remove all non-alphanumeric chars
	}
	return $str;
}

function ihc_return_transaction_amount_for_user_level($payment_history='', $payment_data=''){
	/*
	 * @param string, string
	 * @return float
	 */
	$count = 0;
	if (!empty($payment_history)){
		@$history_data = unserialize($payment_history);
		if ($history_data && is_array($history_data)){
			// calculating with reccuring payments from entire history
			foreach ($history_data as $arr){
				$amount = 0;
				if (isset($arr['amount'])){
					if (isset($arr['ihc_payment_type']) && !empty($arr['ihc_payment_type']) && $arr['ihc_payment_type']=='stripe' && ((empty($arr['type']) || $arr['type']!='charge.succeeded')) ){
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
		$history_not_available = FALSE;
	}
	if (!empty($history_not_available)){
		$amount = 0;
		if (isset($obj->payment_data)){
			$arr = json_decode($payment_data, TRUE);
			if (isset($arr['amount'])){
				$amount = (float)$arr['amount'];
			} else if (isset($arr['mc_gross'])){
				$amount = (float)$arr['mc_gross'];
			} else if (isset($arr['x_amount'])){
				$amount = (float)$arr['x_amount'];
			}
		}
		$count = $count + $amount;	
	}
	return $count;
}

function ihc_get_user_id_by_user_login($u_login=''){
	/*
	 * @param string
	 * @return int
	 */
	if (!empty($u_login)){
		global $wpdb;
		$q = $wpdb->prepare("SELECT ID FROM " . $wpdb->base_prefix . "users WHERE user_login=%s ;", $u_login);
		$data = $wpdb->get_row($q);
		if (!empty($data->ID)){
			return $data->ID;
		}
	}
	return 0;
}

function ihc_get_avatar_for_uid($uid){
	/*
	 * @param int
	 * @return string
	 */
	$avatar_url = IHC_URL . 'assets/images/no-avatar.png';
	if (!empty($uid)){
		$avatar = get_user_meta($uid, 'ihc_avatar', TRUE);
		if (!empty($avatar)){
			if (strpos($avatar, "http")===0){
				$avatar_url = $avatar;
			} else {
				$avatar_data = wp_get_attachment_image_src($avatar, 'full');
				if (!empty($avatar_data[0])){
					$avatar_url = $avatar_data[0];
				}
			}
		}		
	}
	return $avatar_url;
}

function ihc_get_admin_ids_list(){
	/*
	 * @param none
	 * @return array
	 */
	$ids = array();
	$data = get_users(array('role' => 'administrator'));
	if ($data && is_array($data)){
		foreach ($data as $user) {
			$ids[] = $user->ID;
		}		
	}
	return $ids;
}

function ihc_return_user_sm_profile_visit($uid=0){
	/*
	 * @param int
	 * @return string
	 */
	$str = '';
	if ($uid){
		$sm_base = array(
									'ihc_fb' => 'https://www.facebook.com/',/// profile.php?id=
									'ihc_tw' => 'https://twitter.com/intent/user?user_id=',
									'ihc_in' => 'https://www.linkedin.com/profile/view?id=',
									'ihc_tbr' => 'https://www.tumblr.com/blog/',
									'ihc_ig' => 'http://instagram.com/_u/',
									'ihc_vk' => 'http://vk.com/id',
									'ihc_goo' => 'https://plus.google.com/',	
		);
		foreach ($sm_base as $k=>$v){
			$data = get_user_meta($uid, $k, TRUE);
			if (!empty($data)){
				$class = str_replace('_', '-', $k);
				$str .= "<div class='ihc-account-page-sm-icon " . $class . "' style='display: inline-block;'>";
				$str .= "<a href='" . $v . $data . "'>";
				$str .= "<i class='fa-ihc-sm fa-" . $class . "'></i>";
				$str .= '</a>';
				$str .= "</div>";
			}
		}
	}
	if ($str){
		$str = "<div class='ihc-ap-sm-top-icons-wrap'>" . $str . "</div>";		
	}
	return $str;
}

function ihc_save_rewrite_rule_for_register_view_page($page_id=0){
	/*
	 * @param int
	 * @return none
	 */
	if ($page_id){
		$post_name = get_post_field('post_name', $page_id);
		if (!empty($post_name)){
			add_rewrite_rule("$post_name/([^/]+)/?", 'index.php?pagename=' . $post_name . '&ihc_name=$matches[1]', 'top');
			add_rewrite_rule("$post_name/([^/]+)/?",'index.php?page_id=' . $page_id . '&ihc_name=$matches[1]', 'top');
			flush_rewrite_rules();
		}
	}
}

function ihc_is_uap_active(){
	/*
	 * @param none
	 * @return boolean
	 */
	 if (!function_exists('is_plugin_active')){
	 	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	 }
	 if (file_exists(WP_CONTENT_DIR . '/plugins/indeed-affiliate-pro/indeed-affiliate-pro.php') && is_plugin_active('indeed-affiliate-pro/indeed-affiliate-pro.php')){
		if (get_option('uap_license_set')==1){
			return TRUE;
		}
	}
	return FALSE;
}

function ihc_get_payment_id_by_order_id($order_id=0){
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

function ihc_meta_value_exists($meta_key='', $meta_value=''){
	/*
	 * @param string
	 * @return boolean
	 */
	if ($meta_key && $meta_value){
		global $wpdb;
		$table = $wpdb->base_prefix . 'usermeta';
		$data = $wpdb->get_results("SELECT * FROM $table WHERE meta_value='$meta_value' AND meta_key='$meta_key';");
		if (!empty($data)){
			return TRUE;
		}	
	}
	return FALSE;
}

function ihc_save_metas_group($group='', $post_data=array()){
	/*
	 * @param string, array
	 * @return none
	 */
	$data = ihc_return_meta_arr($group, true);
	foreach ($data as $k=>$v){
		if (isset($post_data[$k])){
			$data_db = get_option($k);
			if ($data_db!==FALSE){
				update_option($k, $post_data[$k]);
			} else {
				add_option($k, $post_data[$k]);
			}
		}
	}	
}

function ihc_get_taxes_for_amount_by_country($country='', $state='', $amount=0){
	/*
	 * @param string, float || int
	 * @return array
	 */
	 $array = array();
	 if (!get_option('ihc_enable_taxes')){
	 	return $array;
	 }
	 $currency = get_option('ihc_currency');
	 if (!empty($country)){	
		 $data = Ihc_Db::get_taxes_by_country($country, $state);
		 if ($data){
			$array['total'] = 0;
			$array['currency'] = get_option("ihc_currency");			
			foreach ($data as $tax){
				$temp['label'] = $tax['label'];
				$temp['value'] = $tax['amount_value'] * $amount / 100;
				$temp['value'] = round($temp['value'], 2);
				$temp['print_value'] = ihc_format_price_and_currency($currency, $temp['value']);
				$array['items'][] = $temp;
				$array['total'] += $temp['value'];
			}
			$array['print_total'] = ihc_format_price_and_currency($currency, $array['total']);
			return $array;
		 } 	
	 }
	//use the defaults
	$taxes_settings = ihc_return_meta_arr('ihc_taxes_settings');
	if (!empty($taxes_settings['ihc_default_tax_label']) && !empty($taxes_settings['ihc_default_tax_value'])){
		$array['currency'] = get_option("ihc_currency");	
		$item['label'] = $taxes_settings['ihc_default_tax_label'];
		$item['value'] = $taxes_settings['ihc_default_tax_value'] * $amount / 100;
		$item['value'] = round($item['value'], 2);		
		$item['print_value'] = ihc_format_price_and_currency($currency, $item['value']);		
		$array['items'][] = $item;
		$array['total'] = $item['value'];
		$array['print_total'] = ihc_format_price_and_currency($currency, $array['total']);
	}	 
	return $array;
}

function ihc_convert_date_to_us_format($date=''){
	/*
	 * @param string
	 * @return string
	 */
	if ($date && $date!='-' && is_string($date)){
		@$date = strtotime($date); 
		//$format = 'F j, Y';
		$format = get_option('date_format');
		$return_date = date_i18n($format, $date);
		return $return_date;
	}
	return $date;
}
function ihc_get_user_orders_count($user_id=''){
	global $wpdb;
	
	$count = 0;
	$table = $wpdb->prefix . 'ihc_orders';
		$data = $wpdb->get_results("SELECT COUNT(id) AS count FROM $table WHERE uid='$user_id';");
		if (!empty($data)){
			$count = $data[0]->count;
		}	
	return $count;	

}

function ihc_timeunit_for_payza($type=''){
	/*
	 * @param string
	 * @return string
	 */
	 $unit = $type;
	 switch ($type){
		case 'D':
			$unit = 'Day';
			break;
		case 'W':
			$unit = 'Week';
			break;
		case 'M':
			$unit = 'Month';
			break;
		case 'Y':
			$unit = 'Year';
			break;
	}
	return $unit;
}

function insert_order_from_renew_level($uid=0, $lid=0, $ihc_coupon='', $ihc_country=FALSE, $payment_gateway='', $status=''){
	/*
	 * @param int, int, string, string, string, string
	 * @return none
	 */
	if (!empty($uid) && $lid!==FALSE){
		$extra_order_info = array();
		$levels = get_option('ihc_levels');
		$amount = $levels[$lid]['price'];
		if ($ihc_coupon){
			$coupon_data = ihc_check_coupon($ihc_coupon, $lid);
			$extra_order_info['discount_value'] = ihc_get_discount_value($amount, $coupon_data);
			$amount = ihc_coupon_return_price_after_decrease($amount, $coupon_data);
		}
		
		/// TAXES
		$state = get_user_meta($uid, 'ihc_state', TRUE);
		$country = ($ihc_country==FALSE) ? '' : $ihc_country;
		$taxes_data = ihc_get_taxes_for_amount_by_country($country, $state, $amount);
		if ($taxes_data && !empty($taxes_data['total'])){
			$amount += $taxes_data['total'];
			$extra_order_info['tax_value'] = $taxes_data['total'];
		}	
					
		if ($payment_gateway=='stripe' && $amount<0.50){
			$amount = 0.50;/// minimum for stripe.
		}
		$order_id = ihc_insert_update_order($uid, $lid, $amount, $status, $payment_gateway, $extra_order_info);		
		return $order_id;		
	}	
}

function ihc_user_level_first_time($uid=0, $lid=0){
	/*
	 * Return TRUE if user use this level for the first time.
	 * @param int, int
	 * @return
	 */
	global $wpdb;
	$table = $wpdb->prefix . 'ihc_user_levels';
	$current_time = time();
	$data = $wpdb->get_row("SELECT expire_time FROM $table WHERE user_id=$uid AND level_id=$lid;");
	if ($data && !empty($data->expire_time)){
		$time = strtotime($data->expire_time);
		if ($time<0){
			return TRUE;
		}
		return FALSE;
	}
	return TRUE;
}

function ihc_is_magic_feat_active($type=''){
	/*
	 * @param string
	 * @return boolean 
	 */
	 if ($type){
	 	switch ($type){
			case 'taxes':
				return get_option('ihc_enable_taxes');
				break;
			case 'bp_account_page':
				return get_option('ihc_bp_account_page_enable');
				break;
			case 'woo_account_page':
				return get_option('ihc_woo_account_page_enable');
				break;
			case 'membership_card':
				return get_option('ihc_membership_card_enable');
				break;
			case 'cheat_off':
				return get_option('ihc_cheat_off_enable');
				break;
			case 'invitation_code':
				return get_option('ihc_invitation_code_enable');
				break;
			case 'download_monitor_integration':
				return get_option('ihc_download_monitor_enabled');
				break;
			case 'register_lite':
				return get_option('ihc_register_lite_enabled');
				break;
			case 'individual_page':
				return get_option('ihc_individual_page_enabled');
				break;
			case 'level_restrict_payment':
				return get_option('ihc_level_restrict_payment_enabled');
				break;
			case 'level_subscription_plan_settings':
				return get_option('ihc_level_subscription_plan_settings_enabled');
				break;
			case 'gifts':
				return get_option('ihc_gifts_enabled');
				break;
			case 'login_level_redirect':
				return get_option('ihc_login_level_redirect_on');
				break;
			case 'wp_social_login':
				return get_option('ihc_wp_social_login_on');
				break;
			case 'list_access_posts':
				return get_option('ihc_list_access_posts_on');
				break;
			case 'invoices':
				return get_option('ihc_invoices_on');
				break;
			case 'woo_payment':
				return get_option('ihc_woo_payment_on');
				break;		
			case 'badges':
				return get_option('ihc_badges_on');
				break;
	 	}
	 }
	 return FALSE;
}

function get_terms_for_post_id($post_id=0){
	/*
	 * @param int
	 * @return array
	 */
	 $array = array();
	 if ($post_id){
	 	 global $wpdb;
	 	 $table = $wpdb->prefix . 'term_relationships';
		 $data = $wpdb->get_results("SELECT term_taxonomy_id FROM $table WHERE object_id=$post_id;");
		 if (!empty($data)){
		 	foreach ($data as $object){
		 		$array[] = $object->term_taxonomy_id;
		 	}
		 }
	 }
	 return $array;
}

function ihc_get_all_terms_with_names(){
	/*
	 * @param none
	 * @retunr array
	 */
	 $array = array();
	 global $wpdb;
	 $table = $wpdb->prefix . 'terms';
	 $table_2 = $wpdb->prefix . 'term_relationships';
	 $data = $wpdb->get_results("SELECT term_id, name FROM $table t1 INNER JOIN $table_2 t2 ON t2.term_taxonomy_id=t1.term_id;");
	 if (!empty($data)){
	 	foreach ($data as $object){
	 		$array[$object->term_id] = $object->name;
	 	}
		$exclude = array('settings-verify-email-change', 'groups-membership-request-accepted', 'groups-membership-request-rejected', 'friends-request',
		'core-user-registration', 'core-user-registration-with-blog', 
		);
		foreach ($exclude as $e){
			if ($k=array_search($e, $array)){
				unset($array[$k]);
				unset($k);
			}	
		}		
	 }
	 return $array;
}


function ihc_do_write_into_htaccess($extensions='mp3|mp4|avi|pdf|zip|rar|doc|gz|tar|docx|xls|xlsx|PDF'){
	/*
	 * @param none
	 * @return none
	 */	
	 $file = ABSPATH . '.htaccess';
	 if (file_exists($file) && is_writable($file)){
	 	/// READ FROM HTACCESS
		$data = file_get_contents($file);
		$resource = fopen($file, 'r');
		$data = fread($resource, filesize($file));
		fclose($resource);
		unset($resource);
		$path_to_check_file = WP_CONTENT_DIR . '/plugins/indeed-membership-pro/public/check_file_permissions.php';
		$string_to_write = '#BEGIN Ultimate Membership Pro Rules
	<IfModule mod_rewrite.c>
		RewriteCond %{REQUEST_URI} !^/(wp-content/themes|wp-content/plugins|wp-admin|wp-includes)
		RewriteCond %{REQUEST_URI} \.(' . $extensions . ')$
		RewriteRule . ' . $path_to_check_file . ' [L]
	</IfModule>
#END Ultimate Membership Pro Rules';
		if (strpos($data, $string_to_write)===FALSE){
			$data = $data . $string_to_write;
			$resource = fopen($file, 'w+');
			fwrite($resource, $data);/// WRITE THE NEW CONTENT
			fclose($resource);			
		}
	 }
}

function ihc_format_price_and_currency($currency='', $price_value=''){
	/*
	 * @param string, string
	 * @return string
	 */
	 $output = '';
	 $currency_custom_code = get_option('ihc_custom_currency_code');
	 if (!empty($currency_custom_code)){
	 	$currency = $currency_custom_code;
	 }
	 $rl = get_option('ihc_currency_position');
	 if ($rl=='left'){
	 	$output = $currency . $price_value;
	 } else {
	 	$output = $price_value . $currency;
	 }
	 return $output;
}

function ihc_get_levels_with_payment(){
	/*
	 * @param none
	 * @return array
	 */
	 $data = get_option('ihc_levels');
	 if ($data){
	 	foreach ($data as $key=>$array){
	 		if ($array['payment_type']=='free'){
	 			unset($data[$key]);
	 		}
	 	}
		return $data;
	 }
	 return array();
}

function ihc_get_state_field_str($country=''){
	/*
	 * @param string
	 * @return string
	 */
	$str = '';
	switch ($country){
		case 'US':
			include IHC_PATH . 'public/static_data.php';
			$str .= "<select class='iump-form-select ' name='ihc_state' onChange='ihc_update_cart();'>";
			foreach ($states['US'] as $prefix => $label){
				$str .= "<option value='$prefix'>$label</option>";
			}
			$str .= "</select>";			
			break;
		case 'CA':
			include IHC_PATH . 'public/static_data.php';
			$str .= "<select class='iump-form-select ' name='ihc_state' onChange='ihc_update_cart();'>";
			foreach ($states['CA'] as $prefix => $label){
				$str .= "<option value='$prefix'>$label</option>";
			}
			$str .= "</select>";
			break;
		default:
			$str .= "<input type='text' name='ihc_state' value='' class='' onBlur='ihc_update_cart();' />";
			break;
	}	
	return $str;
}
	
function ihc_do_show_hide_admin_bar_on_public(){
	/*
	 * @param none
	 * @return none
	 */
	 if (!current_user_can('administrator')){	
		if (is_user_logged_in()){
			/// ONLY REGISTERED USERS
			$uid = get_current_user_id();
			$user = new WP_User($uid);
			if ($user && !empty($user->roles) && !empty($user->roles[0]) && $user->roles[0]!='administrator'){
				$allowed_roles = get_option('ihc_dashboard_allowed_roles');
				if ($allowed_roles){
					$roles = explode(',', $allowed_roles);
					if ($roles && is_array($roles) && !in_array($user->roles[0], $roles)){	
						$hide = TRUE;
					}
				} else {
					$hide = TRUE;
				}
				if (!empty($hide)){
					show_admin_bar(FALSE);
				}
			}
		}
	}
}

if (!function_exists('indeed_debug_array')):
function indeed_debug_array($array=array()){
	/*
	 *  print the array into '<pre>' tags
	 * @param array
	 * @return none (echo)
	 */
	 echo '<pre>';
	 print_r($array);
	 echo '</pre>';
}
endif;

if (!function_exists('ihc_get_custom_field_label')):
function ihc_get_custom_field_label($slug=''){
	/*
	 * Return Label of custom register field by slug
	 * @param string
	 * @return string
	 */
	 $data = get_option('ihc_user_fields');
	 if ($data){
	 	 $key = ihc_array_value_exists($data, $slug, 'name');
		 if (isset($data[$key]) && isset($data[$key]['label'])){
		 	return $data[$key]['label'];
		 }
	 }
	 return '';
}
endif;

if (!function_exists('ihc_listing_user_get_filter_fields')):
function ihc_listing_user_get_filter_fields(){
	/*
	 * @param none
	 * @return array
	 */
  	 $return = array();	 
	 $data = get_option('ihc_user_fields');
	 $allow = array('select', 'multi_select', 'checkbox', 'radio', 'date', 'number', 'ihc_country');
	 if ($data){
	 	foreach ($data as $k=>$array){
	 		if (in_array($array['type'], $allow)){
	 			$return[$array['name']] = $array['label'];
	 		}
	 	}
	 }
	return $return;
}
endif;

if (!function_exists('ihc_register_field_get_type_by_slug')):
function ihc_register_field_get_type_by_slug($slug=''){
	/*
	 * @param string
	 * @return string
	 */
	 if ($slug){
	 	 $data = get_option('ihc_user_fields');
		 $key = ihc_array_value_exists($data, $slug, 'name');
		 if ($key!==FALSE && isset($data[$key])){
		 	return $data[$key]['type'];
		 }
	 }
}
endif;

if (!function_exists('ihc_make_level_expire_for_user')):
function ihc_make_level_expire_for_user($uid=0, $lid=0){
	/*
	 * @param int, int
	 * 2return none
	 */
	 if ($uid && $lid!==FALSE){
	 	 global $wpdb;
		 $table = $wpdb->prefix . 'ihc_user_levels';
		 $wpdb->query("UPDATE $table SET expire_time='0000-00-00 00:00:00', notification=0 WHERE user_id=$uid AND level_id=$lid;");
	 }
}
endif;

	

	