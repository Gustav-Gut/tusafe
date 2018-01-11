<?php 
if (!class_exists('ihcAccountPage')){
	class ihcAccountPage{
		private $url = '';
		private $current_user = array();
		private $settings = array();
		private $tab = '';
		private $users_sm = array();
		private $show_tabs = array();
		private $is_affiliate_on = FALSE;
		private $base_url = '';
		
		public function __construct(){
			$account_page = get_option('ihc_general_user_page');
			if ($account_page!==FALSE && $account_page>-1){
				$this->url = get_permalink($account_page);
				$this->base_url = $this->url;
			} else {
				$this->url = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];//$_SERVER['SERVER_NAME']
				$this->base_url = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			}
			
			$remove_get_attr = array(	
										'ihcdologout', 
										'ihc_ap_menu', 
										'ihc_list_item', 
										'ihc_braintree_fields', 
										'lid',
										'ihcbt',
										'ihc_lid',
										'ihc_uid',
										'ihc_success_bt',
										'ihc_bt_success_msg',
										'ihc_state',
										'ihc_country',
										'ihc_register',
										'ihcUserList_p',
										'ihc_name',										
			);
			foreach ($remove_get_attr as $key){
				if (!empty($_GET[$key])){
					$this->base_url = remove_query_arg($key, $this->base_url);
				}
			}
					
			$this->current_user = wp_get_current_user();
			$this->settings = ihc_return_meta_arr('account_page');
			$this->is_affiliate_on = ihc_is_uap_active();
		}
		
		public function print_page($tab){	
			/*
			 * @param string 
			 * @return string
			 */
			$this->tab = $tab;			
			$str = '';
			$str .= $this->print_header();
			$str .= $this->print_tabs();
			/// CONTENT
			switch ($this->tab){
				case 'profile':
					$str .= $this->account_details_page();
					break;
				case 'transactions':
					$str .= $this->transactions_page();
					break;
				case 'subscription':
					$str .= $this->subscription_page();
					break;
				case 'social':
				case 'social_plus':
					$str .= $this->social_page();
					break;
				case 'affiliate':
					$str .= $this->affiliate_page();
					break;
				case 'help';
					$str .= $this->print_help();
					break;
				case 'orders';
					$str .= $this->print_orders();
					break;		
				case 'membeship_gifts';
					$str .= $this->print_membeship_gifts();
					break;		
				case 'membership_cards';
					$str .= $this->print_membership_cards();
					break;													
				case 'overview':
				default :					
					$str .= $this->overview_page();
					break;								
			}			
			/// CONTENT
			$str .= $this->print_footer();	
			return $str;
		}
		
		private function print_header(){
			/*
			 * print the top section with photo and welcome message
			 * @param none
			 * @return string
			 */
			$output = '';
			$data['custom_css'] = '';
			if (!empty($this->settings['ihc_account_page_custom_css'])){
				$data['custom_css'] = $this->settings['ihc_account_page_custom_css'];
			}			
			$show_avatar = $this->settings['ihc_ap_edit_show_avatar'];
			if ($show_avatar){
				$avatar = get_user_meta($this->current_user->ID, 'ihc_avatar', true);
				if (strpos($avatar, "http")===0){
					$avatar_url = $avatar;
				} else {
					$avatar_url = wp_get_attachment_url($avatar);
				}	
				if ($avatar_url){
					$data['avatar'] = $avatar_url;
				} else {
					$data['avatar'] = IHC_URL . 'assets/images/no-avatar.png';
				}		
			}
			$first_name = get_user_meta($this->current_user->ID, 'first_name', true);
			$last_name = get_user_meta($this->current_user->ID, 'last_name', true);
			
			if (!empty($this->settings['ihc_ap_welcome_msg'])){
				$this->settings['ihc_ap_welcome_msg'] = ihc_format_str_like_wp($this->settings['ihc_ap_welcome_msg']);
				$this->settings['ihc_ap_welcome_msg'] = htmlspecialchars_decode($this->settings['ihc_ap_welcome_msg']);
				$this->settings['ihc_ap_welcome_msg'] = stripslashes($this->settings['ihc_ap_welcome_msg']);					
				$data['welcome_message'] = ihc_replace_constants($this->settings['ihc_ap_welcome_msg'], $this->current_user->ID);
			}
			$data['sm'] = $this->print_sm_icons_for_current_user();
			
			if (!empty($this->settings['ihc_ap_edit_show_level'])){
				$data['levels'] = array();
				$level_list_data = get_user_meta($this->current_user->ID, 'ihc_user_levels', true);
				if (isset($level_list_data) && $level_list_data!=''){
					$level_list_data = explode(',', $level_list_data);
					if ($level_list_data){
						foreach ($level_list_data as $id){
							$data['levels'][$id] = ihc_get_level_by_id($id);
						}					
					}
				}							
			}
			
			$data['badges_metas'] = ihc_return_meta_arr('badges');
			if (!empty($data['badges_metas']) && !empty($data['badges_metas']['ihc_badges_on']) && !empty($data['badges_metas']['ihc_badge_custom_css'])){
				$data['custom_css'] .= $data['badges_metas']['ihc_badge_custom_css'];
			}
			
			ob_start();
			require IHC_PATH . 'public/views/account_page-header.php';
			$output = ob_get_contents();
			ob_end_clean();
			return $output;			
		}

		private function print_footer(){
			/*
			 * @param none
			 * @return string
			 */
		 	$output = '';
			$data['content'] = (isset($this->settings['ihc_ap_footer_msg'])) ? ihc_replace_constants($this->settings['ihc_ap_footer_msg'], $this->current_user->ID) : '';			
			$data['content'] = $this->clean_text($data['content']);
									
			ob_start();
			require IHC_PATH . 'public/views/account_page-footer.php';
			$output = ob_get_contents();
			ob_end_clean();
			return $output;					 
		}
		
		private function print_tabs(){
			/*
			 * print the top menu with available tabs
			 * @param none
			 * @return string
			 */

			$output = '';
			$available_tabs = array(
									'overview'=>__('Overview', 'ihc'),
									'profile'=>__('Profile', 'ihc'),
									'subscription'=>__('Subscription', 'ihc'),
									'social' => __('Social Plus', 'ihc'),
									'orders' => __('Orders', 'ihc'),
									'transactions'=>__('Transactions', 'ihc'),	
									'membeship_gifts' => __('Membership Gifts', 'ihc'),
									'membership_cards' => __('Membership Cards', 'ihc'),
									'help' => __('Help', 'ihc'),							
			);
					
			if (!ihc_is_magic_feat_active('gifts')){
				unset($available_tabs['membeship_gifts']);
			}
					
			if (!ihc_is_magic_feat_active('membership_card')){
				unset($available_tabs['membership_cards']);
			}
									
			$this->show_tabs = explode(',', $this->settings['ihc_ap_tabs']);			
			foreach ($available_tabs as $k=>$v){
				if (in_array($k, $this->show_tabs)){
					if (!empty($this->settings['ihc_ap_' . $k . '_menu_label'])){
						$data['menu'][$k]['title'] = $this->settings['ihc_ap_' . $k . '_menu_label'];
					} else {
						$data['menu'][$k]['title'] = $v;						
					}
					$data['menu'][$k]['class'] = 'ihc-ap-menu-item'; 
					$data['menu'][$k]['class'] .= ($k==$this->tab) ? ' ihc-ap-menu-item-selected' : '';		
					$data['menu'][$k]['url'] = add_query_arg( 'ihc_ap_menu', $k, $this->base_url );				
				}
			}
			
			if ($this->is_affiliate_on && get_option('ihc_ap_show_aff_tab')){
				$data['menu']['affiliate']['title'] = __('Affiliate', 'ihc');
				$data['menu']['affiliate']['class'] = 'ihc-account-affiliate-link ihc-ap-menu-item'; 
				$data['menu']['affiliate']['class'] .= ('affiliate'==$this->tab) ? ' ihc-ap-menu-item-selected' : '';					
				global $indeed_db;
				if (empty($indeed_db) && defined('UAP_PATH')){
					include UAP_PATH . 'classes/Uap_Db.class.php';
					$indeed_db = new Uap_Db;
				}
				$is_affiliate = $indeed_db->is_user_affiliate_by_uid($this->current_user->ID);
				if ($is_affiliate){
					$pid = get_option('uap_general_user_page');
					$data['menu']['affiliate']['url'] = get_permalink($pid);
				} else {
					$data['menu']['affiliate']['url'] = add_query_arg( 'ihc_ap_menu', 'affiliate', $this->base_url );
				}	
			}

			if (in_array('logout', $this->show_tabs)){
				if (!empty($this->settings['ihc_ap_logout_menu_label'])){
					$data['menu']['logout']['title'] = $this->settings['ihc_ap_logout_menu_label'];
				} else {
					$data['menu']['logout']['title'] = 'LogOut';						
				}
				$data['menu']['logout']['class'] = 'ihc-ap-menu-item'; 
				$data['menu']['logout']['url'] = $this->base_url;
				$data['menu']['logout']['url'] = add_query_arg('ihcdologout', 1, $data['menu']['logout']['url']);
			}

			ob_start();
			require IHC_PATH . 'public/views/account_page-tabs.php';
			$output = ob_get_contents();
			ob_end_clean();			
			return $output;			
		}
		
		private function overview_page(){
			/*
			 * OVerview Page
			 * @param none
			 * @return string
			 */
			$output = '';
			$data['content'] = '';
			$data['title'] = '';
			$post_overview = get_user_meta($this->current_user->ID, 'ihc_overview_post', true);
			if ($post_overview && $post_overview!=-1){
				//print the post for user
				$post = get_post($post_overview);
				if (!empty($post) && !empty($post->post_content)){
					$data['content'] = $post->post_content;					
				}
			} else {
				//predifined message
				$this->settings['ihc_ap_overview_msg'] = ihc_format_str_like_wp($this->settings['ihc_ap_overview_msg']);
				$this->settings['ihc_ap_overview_msg'] = ihc_correct_text($this->settings['ihc_ap_overview_msg']);
				$data['content'] = $this->settings['ihc_ap_overview_msg'];	
			}
			$data['content'] = ihc_replace_constants($data['content'], $this->current_user->ID);
			$data['title'] = (isset($this->settings['ihc_ap_overview_title'])) ? ihc_replace_constants($this->settings['ihc_ap_overview_title'], $this->current_user->ID) : '';
			$data['content'] = $this->clean_text($data['content']);
			$data['title'] = $this->clean_text($data['title']);
									
			ob_start();
			require IHC_PATH . 'public/views/account_page-overview.php';
			$output = ob_get_contents();
			ob_end_clean();				
			return $output;
		}

		private function affiliate_page(){
			/*
			 * @param none
			 * @return string
			 */
			 return do_shortcode(get_option('ihc_ap_aff_msg'));
		}
		

		private function account_details_page(){
			/*
			 * 
			 * @param none
			 * @return string
			 */
			$data['template'] = get_option('ihc_register_template');
			$data['style'] = get_option('ihc_register_custom_css');
			$current_user = wp_get_current_user();
			
			global $ihc_error_register;
			if (empty($ihc_error_register)){
				$ihc_error_register = array();
			}
			
			if (!class_exists('UserAddEdit')){
				require_once IHC_PATH . 'classes/UserAddEdit.class.php';				
			}
			$obj_form = new UserAddEdit();
			$args = array(
							'user_id' => $current_user->ID,
							'type' => 'edit',
							'tos' => false,
							'captcha' => false,
							'select_level' => false,
							'action' => '',
							'is_public' => true,
							'register_template' => $data['template'],
							'print_errors' => $ihc_error_register
						);
			$obj_form->setVariable($args);
			$data['form'] = $obj_form->form();
			$data['content'] = (isset($this->settings['ihc_ap_profile_msg'])) ? ihc_replace_constants($this->settings['ihc_ap_profile_msg'], $this->current_user->ID) : '';
			$data['title'] = (isset($this->settings['ihc_ap_profile_title'])) ? ihc_replace_constants($this->settings['ihc_ap_profile_title'], $this->current_user->ID) : '';
			$data['content'] = $this->clean_text($data['content']);
			$data['title'] = $this->clean_text($data['title']);
												
			ob_start();
			require IHC_PATH . 'public/views/account_page-account_details_page.php';
			$output = ob_get_contents();
			ob_end_clean();				
			return $output;
		}
		
		private function transactions_page(){
			/*
			 * transactions
			 * @param none
			 * @return string
			 */
	
			$data['content'] = (isset($this->settings['ihc_ap_transactions_msg'])) ? ihc_replace_constants($this->settings['ihc_ap_transactions_msg'], $this->current_user->ID) : '';
			$data['title'] = (isset($this->settings['ihc_ap_transactions_title'])) ? ihc_replace_constants($this->settings['ihc_ap_transactions_title'], $this->current_user->ID) : '';
			$data['content'] = $this->clean_text($data['content']);
			$data['title'] = $this->clean_text($data['title']);
									
			/*
			global $wpdb;
			$data['db'] = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "indeed_members_payments WHERE `u_id`='" . $this->current_user->ID . "';");
			*/
			$data['total_items'] = Ihc_Db::transactions_get_total_for_user($this->current_user->ID);

			if ($data['total_items']){
				$url = $this->base_url;
				$url = add_query_arg('ihc_ap_menu', 'transactions', $url);	
				$limit = 25;
				$current_page = (empty($_GET['ihcp'])) ? 1 : $_GET['ihcp'];
				if ($current_page>1){
					$offset = ( $current_page - 1 ) * $limit;
				} else {
					$offset = 0;
				}
				if ($offset + $limit>$data['total_items']){
					$limit = $data['total_items'] - $offset;
				}
				include_once IHC_PATH . 'classes/Ihc_Pagination.class.php';
				$pagination = new Ihc_Pagination(array(
														'base_url' => $url,
														'param_name' => 'ihcp',
														'total_items' => $data['total_items'],
														'items_per_page' => $limit,
														'current_page' => $current_page,
				));
				$data['pagination'] = $pagination->output();
				$data['items'] = Ihc_Db::transaction_get_items_for_user($limit, $offset, $this->current_user->ID);		
			}	
		
			ob_start();
			require IHC_PATH . 'public/views/account_page-transactions.php';
			$output = ob_get_contents();
			ob_end_clean();				
			return $output;
		}
		
		private function subscription_page(){
			/*
			 * @param none
			 * @return string
			 */	 
			$data['content'] = (isset($this->settings['ihc_ap_subscription_msg'])) ? ihc_replace_constants($this->settings['ihc_ap_subscription_msg'], $this->current_user->ID) : '';
			$data['title'] = (isset($this->settings['ihc_ap_subscription_title'])) ? ihc_replace_constants($this->settings['ihc_ap_subscription_title'], $this->current_user->ID) : '';
			$data['content'] = $this->clean_text($data['content']);
			$data['title'] = $this->clean_text($data['title']);
									
			$data['show_table'] = 1;
			if (isset($this->settings['ihc_ap_subscription_table_enable']) && $this->settings['ihc_ap_subscription_table_enable']==0){
				$data['show_table'] = 0;
			}
			$data['show_subscription_plan'] = 1;
			if (isset($this->settings['ihc_ap_subscription_plan_enable']) && $this->settings['ihc_ap_subscription_plan_enable']==0){
				$data['show_subscription_plan'] = 0;
				
				/// subscription plan check stuff
				if (!empty($_POST['stripeToken']) && (empty($_GET['ihc_register']) || $_GET['ihc_register']!='create_message') ){
					/// STRIPE PAYMENT
					ihc_pay_new_lid_with_stripe($_POST);//available in functions.php
					unset($_POST['stripeToken']);
				} else if (isset($_GET['ihc_success_bt'])){
					/// BT PAYMENT
					add_filter('the_content', 'ihc_filter_print_bank_transfer_message', 79, 1);
				} else if (!empty($_GET['ihc_authorize_fields']) && !empty($_GET['lid'])){
					/// AUTHORIZE RECCURING PAYMENT
					add_filter('the_content', 'ihc_filter_reccuring_authorize_payment', 81, 1);
				}				
				/// subscription plan check stuff
				
			}			
			
			$levels_str = get_user_meta($this->current_user->ID, 'ihc_user_levels', true);
			$fields = get_option('ihc_user_fields');				
			////PRINT SELECT PAYMENT
			$key = ihc_array_value_exists($fields, 'payment_select', 'name');
			$print_payment_select = (empty($fields[$key]['display_public_ap'])) ? FALSE : TRUE;
			///INCLUDE STRIPE JS SCRIPT?
			if (in_array('stripe', ihc_get_active_payments_services(TRUE)) && $print_payment_select){
				$include_stripe = TRUE;
			}
						
			global $wpdb;
			ob_start();
			require IHC_PATH . 'public/views/account_page-subscription_page.php';
			$output = ob_get_contents();
			ob_end_clean();				
			return $output;		
		}
		
		private function social_page(){
			/*
			 * @param none
			 * @return string
			 */
			$output = '';
			if (!empty($this->settings['ihc_ap_social_plus_message'])){
				/// old var
				$this->settings['ihc_ap_social_msg'] = $this->settings['ihc_ap_social_plus_message'];
			}
			$data['content'] = ihc_replace_constants(@$this->settings['ihc_ap_social_msg'], $this->current_user->ID);
			$data['title'] = (isset($this->settings['ihc_ap_social_title'])) ? $this->settings['ihc_ap_social_title'] : '';
			$data['content'] = $this->clean_text($data['content']);
			$data['title'] = $this->clean_text($data['title']);
						
			ob_start();
			require IHC_PATH . 'public/views/account_page-social_plus.php';
			$output = ob_get_contents();
			ob_end_clean();				
			return $output;		
		}
		
		private function print_sm_icons_for_current_user(){
			/*
			 * @param none
			 * @return string
			 */
			$arr = array(
					"fb" => "Facebook",
					"tw" => "Twitter",
					"in" => "LinkedIn",
					"goo" => "Google",
					"vk" => "Vkontakte",
					"ig" => "Instagram",
					"tbr" => "Tumblr"
			);
			$str = '';
			foreach ($arr as $k=>$v){
				$data = get_user_meta($this->current_user->ID, 'ihc_' . $k, true);
				if (!empty($data)){
					$this->users_sm[] = $k; 
					$str .= '<div class="ihc-account-page-sm-icon ihc-'.$k.'" style="display: inline-block;"><i class="fa-ihc-sm fa-ihc-'.$k.'"></i></div>';
				}
			}	
			if ($str){
				$str = '<div class="ihc-ap-sm-top-icons-wrap">' . $str . '</div>';
			}
			return $str;		
		}
		
		private function print_help(){
			/*
			 * @param none
			 * @return string
			 */
			$data['content'] = (isset($this->settings['ihc_ap_help_msg'])) ? ihc_replace_constants($this->settings['ihc_ap_help_msg'], $this->current_user->ID) : '';
			$data['title'] = (isset($this->settings['ihc_ap_help_title'])) ? ihc_replace_constants($this->settings['ihc_ap_help_title'], $this->current_user->ID) : '';			 
			$data['content'] = $this->clean_text($data['content']);
			$data['title'] = $this->clean_text($data['title']);			
			ob_start();
			require IHC_PATH . 'public/views/account_page-help.php';
			$output = ob_get_contents();
			ob_end_clean();				
			return $output;					 
		}
		
		private function print_orders(){
			/*
			 * @param none
			 * @return string
			 */
			$output = '';
			if ($this->current_user && isset($this->current_user->ID)){
				$payment_gateways = array( 
								'paypal' => 'PayPal', 
						       	'authorize' => 'Authorize', 
							   	'stripe' => 'Stripe', 
							   	'twocheckout' => '2Checkout', 
							   	'bank_transfer' => 'Bank Transfer',
							   	'braintree' => 'Braintree',
							   	'payza' => 'Payza',
				);		
				
				$data['total_items'] = Ihc_Db::get_count_orders($this->current_user->ID);
				if ($data['total_items']){
					$url = $this->base_url;
					$url = add_query_arg('ihc_ap_menu', 'orders', $url);	
					$limit = 25;
					$current_page = (empty($_GET['ihcp'])) ? 1 : $_GET['ihcp'];
					if ($current_page>1){
						$offset = ( $current_page - 1 ) * $limit;
					} else {
						$offset = 0;
					}
					if ($offset + $limit>$data['total_items']){
						$limit = $data['total_items'] - $offset;
					}
					include_once IHC_PATH . 'classes/Ihc_Pagination.class.php';
					$pagination = new Ihc_Pagination(array(
															'base_url' => $url,
															'param_name' => 'ihcp',
															'total_items' => $data['total_items'],
															'items_per_page' => $limit,
															'current_page' => $current_page,
					));
					$data['pagination'] = $pagination->output();
					$data['orders'] = Ihc_Db::get_all_order($limit, $offset, $this->current_user->ID);		
				}
						
				$data['content'] = (isset($this->settings['ihc_ap_orders_msg'])) ? ihc_replace_constants($this->settings['ihc_ap_orders_msg'], $this->current_user->ID) : '';
				$data['title'] = (isset($this->settings['ihc_ap_orders_title'])) ? ihc_replace_constants($this->settings['ihc_ap_orders_title'], $this->current_user->ID) : '';			 
				$data['content'] = $this->clean_text($data['content']);
				$data['title'] = $this->clean_text($data['title']);		
				$data['show_invoices'] = (ihc_is_magic_feat_active('invoices')) ? TRUE : FALSE;		
				ob_start();
				require IHC_PATH . 'public/views/account_page-orders.php';
				$output = ob_get_contents();
				ob_end_clean();					
			}			
			return $output;				
		}

		private function print_membeship_gifts(){
			/*
			 * @param none
			 * @return string
			 */
			$data['content'] = (isset($this->settings['ihc_ap_membeship_gifts_msg'])) ? ihc_replace_constants($this->settings['ihc_ap_membeship_gifts_msg'], $this->current_user->ID) : '';
			$data['title'] = (isset($this->settings['ihc_ap_membeship_gifts_title'])) ? ihc_replace_constants($this->settings['ihc_ap_membeship_gifts_title'], $this->current_user->ID) : '';			 
			$data['content'] = $this->clean_text($data['content']);
			$data['title'] = $this->clean_text($data['title']);			
			ob_start();
			require IHC_PATH . 'public/views/account_page-help.php';
			$output = ob_get_contents();
			ob_end_clean();				
			return $output;				
		}
		
		private function print_membership_cards(){
			/*
			 * @param none
			 * @return string
			 */
			$data['content'] = (isset($this->settings['ihc_ap_membership_cards_msg'])) ? ihc_replace_constants($this->settings['ihc_ap_membership_cards_msg'], $this->current_user->ID) : '';
			$data['title'] = (isset($this->settings['ihc_ap_membership_cards_title'])) ? ihc_replace_constants($this->settings['ihc_ap_membership_cards_title'], $this->current_user->ID) : '';			 
			$data['content'] = $this->clean_text($data['content']);
			$data['title'] = $this->clean_text($data['title']);
			ob_start();
			require IHC_PATH . 'public/views/account_page-help.php';
			$output = ob_get_contents();
			ob_end_clean();	
			return $output;					
		}

		private function clean_text($string=''){
			/*
			 * @param string
			 * @return string
			 */
			 return stripslashes($string);
		}
		
	}//end of class ihcAccountPage
}