<?php 
if (!class_exists('UserAddEdit')){
	class UserAddEdit{
		protected $is_public = TRUE;
		protected $user_id = '';
		protected $type = 'create';//create or edit
		protected $action = '';// form action (url) 		
		protected $user_data = array();
		private $tos = false;
		private $captcha = false;
		protected $register_metas = array();
		protected $errors = false;
		protected $register_fields = '';
		private $disabled_submit_form = '';
		protected $register_template = 'ihc-register-1';
		private $bank_transfer_message = FALSE;
		private $display_type = 'display_admin';
		private $coupon = '';
		private $show_sm = FALSE;
		protected $print_errors = array();
		protected $required_fields = array();
		private $payment_gateway = '';
		private $current_level = -1;
		protected $global_css = '';
		protected $global_js = '';
		private $exception_fields = array();
		//private $donation_field = FALSE;
		private $taxes_enabled = FALSE;
		private $show_taxes = FALSE;
		private $payment_available_after_excluded_by_lid = array();
		private $rewrite_payment_gateway = TRUE;
		private $preview = FALSE;
		protected $order_id = 0;
		protected $shortcodes_attr = array();
		protected $set_password_auto = FALSE;
		protected $send_password_via_mail = FALSE;
		private $authorize_txn_id = FALSE;
		
		/////////
		public function __construct(){
			/*
			 * @param none
			 * @return none
			 */
			///////////////set payment gateway
			if (!empty($_REQUEST['ihc_payment_gateway'])){
				$this->payment_gateway = $_REQUEST['ihc_payment_gateway'];
				$this->rewrite_payment_gateway = FALSE;
				if (isset($_REQUEST['ihc_payment_gateway_radio'])){
					unset($_REQUEST['ihc_payment_gateway_radio']);
				}
			} else {
				//DEFAULT
				$this->payment_gateway = get_option('ihc_payment_selected');
			}
		}

		////////
		public function setVariable($arr=array()){
			/*
			 * set the input variables
			 * @param array
			 * @return none
			 */
			if(count($arr)){
				foreach($arr as $k=>$v){
					$this->$k = $v;
				}
			}
			if ($this->is_public){
				if ($this->type=='create'){
					$this->display_type = 'display_public_reg';
				} else {
					$this->display_type = 'display_public_ap';
				}
				/// TAXES
				$taxes_settings = ihc_return_meta_arr('ihc_taxes_settings');
				$this->taxes_enabled = $taxes_settings['ihc_enable_taxes'];
				$this->show_taxes = $taxes_settings['ihc_show_taxes'];				
			} else {
				$this->display_type = 'display_admin';
			}
			
			/////SET CURRENT LEVEL (lid can be in get)
			if ($this->type=='create' && !$this->disabled_submit_form){
				if (isset($this->shortcodes_attr['level']) && $this->shortcodes_attr['level']!==FALSE){
					$standard_level = $this->shortcodes_attr['level'];
				} else {
					$standard_level = get_option('ihc_register_new_user_level');					
				}
			} else {
				$standard_level = '';
			}
			if (isset($_REQUEST['lid']) || $standard_level){
				$this->current_level = isset($_REQUEST['lid']) ? $_REQUEST['lid'] : $standard_level;
			}
			
			if ($this->rewrite_payment_gateway && $this->current_level && ihc_is_magic_feat_active('level_restrict_payment')){
				/// set default payment type	
				$this->payment_gateway = Ihc_Db::get_default_payment_gateway_for_level($this->current_level, $this->payment_gateway);				
				$exclude_payments_available = Ihc_Db::get_excluded_payment_types_for_level_id($this->current_level);
				if ($exclude_payments_available){
					$exclude_array = explode(',', $exclude_payments_available);
					$this->payment_available_after_excluded_by_lid = ihc_get_active_payments_services();
					foreach ($exclude_array as $ek=>$ev){
						if (isset($this->payment_available_after_excluded_by_lid[$ev])){
							unset($this->payment_available_after_excluded_by_lid[$ev]);
						}
					}
					if (!in_array($this->payment_gateway, $this->payment_available_after_excluded_by_lid)){
						$this->payment_gateway = key($this->payment_available_after_excluded_by_lid);
					}
				}	
			}			
		}
		
		private function set_register_fields(){
			/*
			 * @param none
			 * @return none
			 */
			$this->register_fields = ihc_get_user_reg_fields();//register fields	
			ksort($this->register_fields);
			
			/// REMOVE USERNAME FROM EDIT
			if ($this->type=='edit'){
				$key = ihc_array_value_exists($this->register_fields, 'user_login', 'name');
				if ($key!==FALSE){
					unset($this->register_fields[$key]);
				}
			}
			
			
			/// REMOVE COUPUN FROM PUBLIC - EDIT, ADMIN - ADD NEW, ADMIN - EDIT
			if ($this->type!='create' || !$this->is_public){
				$key = ihc_array_value_exists($this->register_fields, 'ihc_coupon', 'name');
				if ($key!==FALSE){
					unset($this->register_fields[$key]);
				}				
			}
			
			$key = ihc_array_value_exists($this->register_fields, 'ihc_invitation_code_field', 'name');
			if ( $key!==FALSE && (!ihc_is_magic_feat_active('invitation_code') || !Ihc_Db::invitation_code_does_exist_codes() || !$this->is_public || $this->type!='create') ){
				/// remove invitation code
				unset($this->register_fields[$key]);
			}
			
		}
		
		/////////
		public function form(){
			/*
			 * @param none
			 * @return string
			 */
			
			/*extra fields that must be transalted:*/				 		
			   __("Confirm Password", 'ihc');
			   __("Last Name", 'ihc');
			   __("First Name", 'ihc');			 
			 /**/
			   
			$this->userdata();//settings the user data
			$this->set_register_fields();
			$level_data = ihc_get_level_by_id($this->current_level);
			
			$str = '';
			$i = 0;
			if ($this->register_template == 'ihc-register-6' || $this->register_template == 'ihc-register-11'
			|| $this->register_template == 'ihc-register-12' || $this->register_template == 'ihc-register-13'){
				$count_reg = $this->count_register_fields();
			}
			
			foreach ($this->register_fields as $v){
				if ($v[$this->display_type]>0){
					$i++;
					if ($this->is_public) {
						if ($this->register_template == 'ihc-register-6' || $this->register_template == 'ihc-register-11'
							|| $this->register_template == 'ihc-register-12' || $this->register_template == 'ihc-register-13'){
							if ($i == 1) $str .= '<div class="ihc-register-col">';
							if ($i-1 == ceil($count_reg/2)) $str .= '</div><div class="ihc-register-col">';
						}
					}
					// TEST CUSTOM FIELD - LEVELS RELATION
					if (isset($v['target_levels']) && $v['target_levels']!='' && $this->is_public){
						$field_target_levels = explode(',', $v['target_levels']);	
						if ($this->type=='create'){							
							if (!in_array($this->current_level, $field_target_levels)){
								continue;
							}							
						} else {
							/// do stuff
							$user_levels_temp = explode(',', $this->user_data['ihc_user_levels']);	
							if ($user_levels_temp){
								
								$k = 0;	
								foreach ($user_levels_temp as $lid_temp){
									if (in_array($lid_temp, $field_target_levels)){
										$k =1;
									}	
								}
								if($k == 0)
								  continue;
							}
							
						}
					}
					
					switch ($v['name']){
						case 'tos':
							if ($this->tos){
								$disp_tos = $this->print_tos($v);
								if ($disp_tos != ''){
							 		$str .= $disp_tos;
							 		$this->required_fields[] = array('name' => 'tos', 'type' => 'checkbox');
							 		if (!empty($this->print_errors['tos'])){
							 			$str .= '<div class="ihc-register-notice">' . $this->print_errors['tos'] . '</div>';
							 		}
								}
							}							
							break;
						case 'recaptcha':
							if ($this->captcha){
								$disp_captcha = $this->print_captcha($v);
								if ($disp_captcha != ''){
							 		$str .= $disp_captcha;
									if (!empty($this->print_errors['captcha'])){
										$str .= '<div class="ihc-register-notice">' . $this->print_errors['captcha'] . '</div>';
									}								
								}
							}
							break;
						case 'ihc_social_media':
							if ($this->type=='create' && $this->is_public){
								$str .= ihc_print_social_media_icons('register');
								$this->show_sm = TRUE;
							} else {
								continue;
							}
							break;
						case 'payment_select':							
							$payments_available = ihc_get_active_payments_services();
							if ($this->is_public && $this->type=='create' && $this->current_level!=-1 && $level_data['payment_type']=='payment'
								&& !empty($payments_available) && count($payments_available)>1){
								
								if (!empty($level_data['access_type']) && $level_data['access_type']=='regular_period'){
									$is_reccurence = 1;
								} else {
									$is_reccurence = 0;
								}
																
								$default_payment = (empty($this->payment_gateway)) ? get_option('ihc_payment_selected') : $this->payment_gateway;
								
								if (!empty($this->payment_available_after_excluded_by_lid)){
									$payments_available = $this->payment_available_after_excluded_by_lid;
									if (count($payments_available)<2){
										continue; /// single payment gateway
									}
								}
								
								$str .= ihc_print_payment_select($default_payment, $v, $payments_available, $is_reccurence, $v['req']);
								
								if (!empty($payments_available['stripe'])){
									$include_stripe = TRUE;
								}
								if (!empty($payments_available['authorize'])){
									$include_authorize = TRUE;
								}
								if (!empty($payments_available['braintree'])){
									$include_braintree = TRUE;
								}
																
								if (!empty($v['req'])){
									$this->required_fields[] = array('name' =>'ihc_payment_gateway', 'type'=>'hidden');
								}
							}							
							break; 
						default:
							if ($this->is_public) {
								
								//========== PUBLIC
								$str .= $this->print_fields($v);
							} else {
								//========== ADMIN
									
								$disabled = '';
								if ( $this->type=='edit' && $v['name']=='user_login'){
									$disabled = 'disabled';
								}
								//FORM FIELD
								$parent_id = 'ihc_reg_' . $v['type'] . '_' . rand(1,10000);
								$temp_type_class = 'iump-form-' . $v['type'];
								$str .= '<div class="iump-form-line-register ' . $temp_type_class . '" id="' . $parent_id . '">';
								$str .= '<label class="iump-labels-register">';
								if ($v['req']){
									$str .= '<span style="color: red;">*</span>';
								}
								if (isset($v['native_wp']) && $v['native_wp']){
									$str .= __($v['label'], 'ihc');
								} else {
									$str .= ihc_correct_text($v['label']);
								}
								$str .= '</label>';
							
								$val = '';
								if (isset($this->user_data[$v['name']])){
									$val = $this->user_data[$v['name']];
								}
								if (empty($val) && $v['type']=='plain_text'){ //maybe it's plain text
									$val = $v['plain_text_value'];
								}
									
								$multiple_values = FALSE;
								if (isset($v['values']) && $v['values']){
									//is checkbox, select or radio input field, so we have to include multiple+_values into indeed_create_form_elelemt
									$multiple_values = ihc_from_simple_array_to_k_v($v['values']);
								}
							
								if (empty($v['sublabel'])){
									$v['sublabel'] = '';
								}
							
								if (empty($v['class'])){
									$v['class'] = '';
								}
									
								$str .= indeed_create_form_element(array( 'type' => $v['type'], 'name' => $v['name'], 'value' => $val,
										'disabled' => $disabled, 'multiple_values' => $multiple_values,
										'user_id' => $this->user_id, 'sublabel' => $v['sublabel'], 'class' => $v['class'], 'form_type' => $this->type ));
								$str .= '</div>';
							}
							break;
					}//end of switch					
				}
			}
			
			if ($this->register_template == 'ihc-register-6' || $this->register_template == 'ihc-register-11'
				|| $this->register_template == 'ihc-register-12' || $this->register_template == 'ihc-register-13')	
			{ $str .= '</div>'; }
					
			if ($this->is_public && !$this->disabled_submit_form){		
				if (!empty($this->payment_gateway) && $this->type=='create'){
					$the_selected_payment = (ihc_check_payment_available($this->payment_gateway)) ? $this->payment_gateway : '';
					$str .= '<input type="hidden" name="ihc_payment_gateway" value="' . $the_selected_payment . '" />';
				}		
				/*******************************PUBLIC****************************/				
				//SPECIAL PAYMENTS (authorize, stripe)				
				if (isset($this->current_level) && $level_data['payment_type']=='payment'){
						if ( ($this->payment_gateway == 'authorize' || !empty($include_authorize)) && ihc_check_payment_available('authorize') && isset($level_data['access_type']) && $level_data['access_type']=='regular_period'){
							//AUTHORIZE RECCURING							
							if (!class_exists('ihcAuthorizeNet')){
								require_once IHC_PATH . 'classes/ihcAuthorizeNet.class.php';
							}							
							$auth_pay = new ihcAuthorizeNet();
							$display_authorize = ($this->payment_gateway=='authorize') ? 'block' : 'none';
							$str .= '<div id="ihc_authorize_r_fields" style="display: '.$display_authorize.'">';
								$str .= '<div class="ihc_payment_register_wrapper">';
									$str .= '<div class="ihc_payment_details">'.__('Authorize.Net details', 'ihc').'</div>';
									$str .=  $auth_pay->payment_fields();		
								$str .= '</div>';
							$str .= '</div>';
							if (!empty($this->print_errors) && !empty($this->print_errors['payment_gateway-authorize'])){
								$str .= '<div class="ihc-register-notice">' . __('An error has occurred during the payment process.', 'ihc') . '</div>';
							}
						} 						
						
						if (($this->payment_gateway=='stripe' || !empty($include_stripe)) && ihc_check_payment_available('stripe') ) {
							
							if ( isset($_POST['stripeEmail']) && isset($_POST['stripeToken']) ){
								//already have a token
								$str .= indeed_create_form_element(array('type'=>'hidden', 'name'=>'stripeToken', 'value' => $_POST['stripeToken'] ));
								$str .= indeed_create_form_element(array('type'=>'hidden', 'name'=>'stripeEmail', 'value' => $_POST['stripeEmail'] ));
							} else {
								if (!class_exists('ihcStripe')){
									require_once IHC_PATH . 'classes/ihcStripe.class.php';
								}								
								$payment_obj = new ihcStripe();
								$bind = ($this->payment_gateway=='stripe') ? TRUE : FALSE;
								$str .= $payment_obj->payment_fields($this->current_level, $bind);								
							}
						}

						if (($this->payment_gateway=='braintree' || !empty($include_braintree)) && ihc_check_payment_available('braintree')){
							require_once IHC_PATH . 'classes/Ihc_Braintree.class.php';
							$braintree = new Ihc_Braintree();
							$display_braintree = ($this->payment_gateway=='braintree') ? 'block' : 'none';
							$str .= '<div id="ihc_braintree_r_fields" style="display: ' . $display_braintree . '">';
								$str .= '<div class="ihc_payment_register_wrapper">';
									$str .= '<div class="ihc_payment_details">'.__('Braintree details', 'ihc').'</div>';
									$str .= $braintree->get_form();	
								$str .= '</div>';
							$str .= '</div>';		
						}
				}
				
				//ACTIONS
				if ($this->type=='edit'){
					$str .= indeed_create_form_element(array('type'=>'hidden', 'name'=>'ihcaction', 'value' => 'update' ));
				} else {
					$str .= indeed_create_form_element(array('type'=>'hidden', 'name'=>'ihcaction', 'value' => 'register' ));
				}
				//LEVELS				
				if ($this->current_level!=-1){
					$str .= indeed_create_form_element(array('type'=>'hidden', 'name'=>'lid', 'value' => $this->current_level ));
				}
			} else if (!$this->disabled_submit_form && !$this->is_public){
				/******************************** ADMIN ****************************/
				$str .= $this->print_wp_role();//select wp role
				$str .= $this->select_level();//select levels
				
				if ($this->user_id && $this->type=='edit'){//hide user id into the form for edit, only in admin
					$str .= indeed_create_form_element(array('type'=>'hidden', 'name'=>'user_id', 'value' => $this->user_id ));
					$str .= $this->print_level_expire_time_edit();///level expire time
				}		
				$str .= $this->print_overview_post_select();		
			}
			
			
			if ($this->register_template == 'ihc-register-7'){ $str .= '<div class="impu-temp7-row">'; }
			
			$str = apply_filters('ump_before_submit_form', $str);
			
			$str .= '<div class="iump-submit-form">';
			$bttn_submit_class = 'button button-primary button-large';
			if ($this->register_template!='ihc-register-1' && $this->preview){
				$bttn_submit_class = '';
			}
			if ($this->type=='create'){
				$str .= indeed_create_form_element(array('type'=>'submit', 'name'=>'Submit', 'value' => __('Register', 'ihc'), 
						'class' => $bttn_submit_class, 'id'=>'ihc_submit_bttn', 'disabled'=>$this->disabled_submit_form ));
			} else {
				$str .= indeed_create_form_element(array('type'=>'submit', 'name'=>'Update', 'value' => __('Save Changes', 'ihc'),
						 'class' => $bttn_submit_class, 'id'=>'ihc_submit_bttn', 'disabled'=>$this->disabled_submit_form ));
			}			
			$str .= '</div>';			
			
			if ($this->register_template == 'ihc-register-7'){ $str .= '</div>'; }
			
			$str .= $this->social_register_request_data();
			
			if (count($this->exception_fields)>0){
				$str .= '<input type="hidden" name="ihc_exceptionsfields" id="ihc_exceptionsfields" value="' . implode(',', $this->exception_fields) . '" />';
			}
			
			//wrapp it all in a form
			if ($this->type=='edit'){
				$form_detail = ' name="edituser" id="edituser" class="ihc-form-create-edit" enctype="multipart/form-data" ';
			} else {
				$form_detail = ' name="createuser" id="createuser" class="ihc-form-create-edit" enctype="multipart/form-data" ';
			}
			$str = indeed_form_start( $this->action, 'post', $form_detail) . $str . indeed_form_end();
			
			//SOCIAL LOGGER
			if ($this->is_public && $this->type=='create' && $this->show_sm){
				$str .= $this->ihc_social_form();		
			}	

			//MESSAGE ABOUT LEVEL 
			$str .= $this->add_level_details_on_register_form();
			
			//CSS
			$this->global_css .= get_option('ihc_register_custom_css'); //add custom css to global css
			$str = '<style>' . $this->global_css .  '</style>' . $str;
			
			//AJAX CHECK FIELDS VALUES (ONLY FOR PUBLIC REGISTER)
			if ($this->is_public && $this->type=='create'){
				$str .= '<script>';
				$str .= 'var req_fields_arr = [];';
				$str .= 'jQuery(document).ready(function(){';
				
				/// require fields
				foreach ($this->required_fields as $req_field){
					if (in_array($req_field['type'], array('text', 'textarea', 'number', 'password', 'conditional_text'))){
						$str .= 'jQuery(".ihc-form-create-edit [name='.$req_field['name'].']").on("blur", function(){
							ihc_register_check_via_ajax("'.$req_field['name'].'");
						});';						
					}
					
					$str .= 'req_fields_arr.push("' . $req_field['name'] . '");
					';
				}				
				
				$str .= 'jQuery(".ihc-form-create-edit").live("submit", function() {
							if (window.must_submit==1){
								return true;
							} else {
								ihc_register_check_via_ajax_rec(req_fields_arr);
								return false;							
							}
						});';
				$str .= '});';				
				$str .= $this->global_js;
				$str .= '</script>';
			}
			
			return $str;	
		}
		
		
		/////////
		public function userdata(){
			//setting $user_data for current user
			if ($this->user_id){
				/// EDIT
				$data = get_userdata($this->user_id);
				$user_fields = ihc_get_user_reg_fields();
				if ($data){
					foreach ($user_fields as $user_field){
						$name = $user_field['name'];
						if ($user_field['native_wp']==1){
							//native wp field, get value from get_userdata ( $data object )
							if (isset($data->$name) && $data->$name){
								$this->user_data[ $name ] = $data->$name;
							}
						} else {
							//custom field, get value from get_user_meta()
							$this->user_data[ $name ] = get_user_meta($this->user_id, $name, true);
						}
					}
				}
				//user wp role
				if (isset($data->roles[0])){
					$this->user_data['role'] = $data->roles[0];
				}
				///user levels
				$this->user_data['ihc_user_levels'] = get_user_meta($this->user_id, 'ihc_user_levels', true);
				
				//remove coupon data
				unset($this->user_data['ihc_coupon']);
			} else {
				/// CREATE
				$user_fields = ihc_get_user_reg_fields();
				global $ihc_stored_form_values;
				
					foreach ($user_fields as $user_field){
						$name = $user_field['name'];
						$this->user_data[$name] = '';	
						
						if ($this->is_public){
							if (isset($_POST[$name])){
								/// prev value from submit form
								$this->user_data[$name] = $_POST[$name];
							} else if (isset($ihc_stored_form_values[$name])){
								/// prev value from submit form, before register with sm
								$this->user_data[$name] = $ihc_stored_form_values[$name];
							}
						}

					}
				$this->user_data['ihc_user_levels'] = '';
				$this->user_data['role'] = '';
			}
		}

		private function print_wp_role(){
			$str = '';
			$str .= '<div class="iump-form-line-register iump-form-select">';
			$str .= '<label class="iump-labels-register">WP Role</label>';
			$str .= indeed_create_form_element(
													array(  'type' => 'select', 
															'name' => 'role', 
															'value' => $this->user_data['role'],
															'multiple_values' => ihc_get_wp_roles_list(),
															'class' => '' )
													);
			$str .= '</div>';
			return $str;
		}
		
		////////
		private function select_level(){
			$str = '';			
			$levels = get_option('ihc_levels');
			if ($levels && count($levels)){
				$level_select_options[-1] = '...';
				foreach ($levels as $k=>$v){
					$level_select_options[$k] = $v['name'];
				}
				$str .= '<div class="iump-form-line iump-special-line">';
				$str .= '<label class="iump-labels">'.__('Select Level:', 'ihc').' </label>';
					
				$args['type'] = 'select';
				$args['multiple_values'] = $level_select_options;
				$args['value'] = -1;
				$args['name'] = 'ihc_user_levels';
				if (!$this->is_public){
					unset($args['name']);
					$args['other_args'] = 'onChange="ihc_writeTagValue(this, \'#indeed-user-level-free-select\', \'#ihc-select-level-view-values\', \'ihc-level-select-v-\');"';
				}
				$str .= indeed_create_form_element($args);
				if (!$this->is_public){
					$str .= indeed_create_form_element(array('type'=>'hidden', 'name'=>'ihc_user_levels', 'id' => 'indeed-user-level-free-select', 'value' => $this->user_data['ihc_user_levels'] ));
					$str .= '<div id="ihc-select-level-view-values">';
					if ($this->user_data['ihc_user_levels']!='' && $this->user_data['ihc_user_levels']!=-1){
						$user_levels = explode(',', $this->user_data['ihc_user_levels']);				
						foreach ($user_levels as $v){
							$v = (int)$v;
							$temp_data = ihc_get_level_by_id($v);
							if ($temp_data){
								$str .= '<div id="ihc-level-select-v-'.$v.'" class="ihc-tag-item">'.$temp_data['name']
											. '<div class="ihc-remove-tag" onclick="ihcremoveTag('.$v.', \'#ihc-level-select-v-\', \'#indeed-user-level-free-select\');ihc_admin_delete_user_level_relationship(' . $v . ', ' . $this->user_id . ');" title="'.__('Removing tag', 'ihc').'">'
											. 'x</div>'
									  . '</div>';									
							}
						}
					}
					$str .= '</div>';
					$str .= '<div class="clear"></div>';
				}
				$str .= '</div>';
			}
			return $str;
		}
		
		private function check_for_conditional_logic($field_arr, $field_id){
			/*
			 * @param string, string
			 * @return none
			 */
			if (!empty($field_arr['conditional_logic_corresp_field']) && $field_arr['conditional_logic_corresp_field']!=-1){
				//so this field is correlated with another
				
				////Js ACTION
				$key = ihc_array_value_exists($this->register_fields, $field_arr['conditional_logic_corresp_field'], 'name');
				if ($key!==FALSE && !empty($this->register_fields[$key]['type'])){
					$show = ($field_arr['conditional_logic_show']=='yes') ? 1 : 0;
					
					switch ($this->register_fields[$key]['type']){
						case 'text':
						case 'textarea':
						case 'number':
						case 'password':
						case 'date':
						case 'conditional_text':
						case 'unique_value_text':
							$js_function = 'ihc_ajax_check_field_condition_onblur_onclick("' . $field_arr['conditional_logic_corresp_field'] . '", "#' . $field_id . '", "' . $field_arr['name'] . '", ' . $show . ');';
							$this->global_js .= '
								jQuery(".ihc-form-create-edit [name='.$field_arr['conditional_logic_corresp_field'].']").on("blur", function(){
									' . $js_function . '
								});
							';							
							break;
						case 'checkbox':
							$js_function = 'ihc_ajax_check_onClick_field_condition("' . $field_arr['conditional_logic_corresp_field'] . '", "#' . $field_id . '", "' . $field_arr['name'] . '", "checkbox", ' . $show . ');';
							$this->global_js .= '
								jQuery(".ihc-form-create-edit [name=\''.$field_arr['conditional_logic_corresp_field'].'[]\'], .ihc-form-create-edit [name='.$field_arr['conditional_logic_corresp_field'].']").on("click", function(){
									' . $js_function . '
								});
							';							
							break;
						case 'radio':
							$js_function = 'ihc_ajax_check_onClick_field_condition("' . $field_arr['conditional_logic_corresp_field'] . '", "#' . $field_id . '", "' . $field_arr['name'] . '", "radio", ' . $show . ');';
							$this->global_js .= '
								jQuery(".ihc-form-create-edit [name='.$field_arr['conditional_logic_corresp_field'].']").on("click", function(){
									' . $js_function . '
								});
							';							
							break;
						case 'select':
							$js_function = 'ihc_ajax_check_field_condition_onblur_onclick("' . $field_arr['conditional_logic_corresp_field'] . '", "#' . $field_id . '", "' . $field_arr['name'] . '", ' . $show . ');';
							$this->global_js .= '
								jQuery(".ihc-form-create-edit [name='.$field_arr['conditional_logic_corresp_field'].']").on("change", function(){
									' . $js_function . '
								});
							';
							break;
						case 'multi_select':
							$js_function = 'ihc_ajax_check_onChange_multiselect_field_condition("' . $field_arr['conditional_logic_corresp_field'] . '", "#' . $field_id . '", "' . $field_arr['name'] . '", ' . $show . ');';
							$this->global_js .= '
								jQuery(".ihc-form-create-edit [name=\''.$field_arr['conditional_logic_corresp_field'].'[]\']").on("change", function(){
									' . $js_function . '
								});
							';							
							break;
					}
					if (!empty($js_function)){
						$this->global_js .= 'jQuery(document).ready(function(){' . $js_function . '});';
					}
				}		

				//conditional logic & required => add new exception
				if ($field_arr['req']){
					$this->exception_fields[] = $field_arr['name'];
				}
				
				if ($field_arr['conditional_logic_show']=='yes'){
					//we must hide this field and show only when correlated field it's completed with desired value
					$this->global_css .= "#$field_id{display: none;}";
				}
			}
		}
		
		protected function print_fields($v=array()){		
			/*
			 * @param array
			 * @return string
			 */
			$str = '';
			$disabled = '';
			$placeholder = '';
			$callback = '';
			 if ( $this->type=='edit' && $v['name']=='user_login'){
			 	$disabled = 'disabled';
			 }
			 $parent_id = 'ihc_reg_' . $v['type'] . '_' . rand(1,10000);
			 
			 $this->check_for_conditional_logic($v, $parent_id);
			 
			 if ($v['type']=='date'){
			 	if (!empty($v['req']) || $v['type']=='conditional_text'){
			 		$callback = 'ihc_register_check_via_ajax("'.$v['name'].'");'; /// require field
			 		$this->required_fields[] = array('name' => $v['name'], 'type'=>$v['type']);
			 	}	
			 } else {
			 	if (!empty($v['req']) || $v['type']=='conditional_text'){
			 		$this->required_fields[] = array('name' => $v['name'], 'type'=>$v['type']);
			 	}	
				/*
				if ($v['type']=='ihc_donation'){
					$this->donation_field = TRUE;
				}
				*/		 	
			 }

			 switch ($this->register_template){
				 case 'ihc-register-3':
				 case 'ihc-register-8':
				 case 'ihc-register-9':
				  //////// FORM FIELD
				 	 $temp_type_class = 'iump-form-' . $v['type'];
					 $str .= '<div class="iump-form-line-register ' . $temp_type_class . '" id="' . $parent_id . '">';
					 if ($v['type'] == 'text' || $v['type'] == 'password'){
					 	if ($v['req']){
							 $placeholder .= '*';
						 }
						if (isset($v['native_wp']) && $v['native_wp']){
							$placeholder .= __($v['label'], 'ihc');
						 } else {
							$placeholder .= ihc_correct_text($v['label']);
						 }	
					 } else {
						 $str .= '<label class="iump-labels-register">';
						 if ($v['req']){
							 $str .= '<span style="color: red;">*</span>';
						 }
						 if (isset($v['native_wp']) && $v['native_wp']){
							$str .= __($v['label'], 'ihc');
						 } else {
						 	$str .= ihc_correct_text($v['label']);						
						 }						 
						 $str .= '</label>';
					 }
					 $val = '';
					 if (isset($this->user_data[$v['name']])){
					 	$val = $this->user_data[$v['name']];
					 }
			 		 if (empty($val) && $v['type']=='plain_text'){ //maybe it's plain text
					 	$val = $v['plain_text_value'];
					 }
					 
					 $multiple_values = FALSE;
					 if (isset($v['values']) && $v['values']){
					 	//is checkbox, select or radio input field, so we have to include multiple+_values into indeed_create_form_elelemt
					 	$multiple_values = ihc_from_simple_array_to_k_v($v['values']);
					 }	
					 			
					 if (empty($v['sublabel'])){
					 	$v['sublabel'] = '';
					 }
					 if (empty($v['class'])){
					 	$v['class'] = '';
					 }
					 					 				 	 
					 $str .= indeed_create_form_element(array(	'type'=>$v['type'], 'name'=>$v['name'], 'value' => $val, 
					 											'disabled' => $disabled, 'placeholder' => $placeholder, 'multiple_values'=>$multiple_values,
					 											'user_id'=>$this->user_id, 'sublabel' => $v['sublabel'], 'class' => $v['class'], 
					 											'callback' => $callback, 'form_type' => $this->type ));		
			 		 if (!empty($this->print_errors[$v['name']])){
					 	$str .= '<div class="ihc-register-notice">' . $this->print_errors[$v['name']] . '</div>';
					 }		 
					 $str .= '</div>';
				 break;
				 case 'ihc-register-4':
				  //////// FORM FIELD
				  $add_class = '';
					if ($v['type'] == 'select' || $v['type'] == 'multi_select' || $v['type'] == 'file' || $v['type'] == 'upload_image' || $v['type'] == 'ihc_country'){
						$add_class ='ihc-no-backs';
					}
					$temp_type_class = 'iump-form-' . $v['type'];
					 $str .= '<div class="iump-form-line-register '.$add_class.' ' . $temp_type_class . '" id="' . $parent_id . '">';
					 if ($v['type'] == 'text' || $v['type'] == 'password' || $v['type'] == 'unique_value_text' || $v['type'] == 'ihc_invitation_code_field' || $v['type'] == 'date'){
					 	if ($v['req']){
							 $placeholder .= '*';
						 }
						if (isset($v['native_wp']) && $v['native_wp']){
							$placeholder .= __($v['label'], 'ihc');
						 } else {
							$placeholder .= ihc_correct_text($v['label']);
						 }	
					 } else {
							 $str .= '<label class="iump-labels-register">';
							 if ($v['req']){
								 $str .= '<span style="color: red;">*</span>';
							 }
							 if (isset($v['native_wp']) && $v['native_wp']){
								$str .= __($v['label'], 'ihc');
							 } else {
								$str .= ihc_correct_text($v['label']);	
							 }						 
							 $str .= '</label>';
					 }
					 $val = '';
					 if (isset($this->user_data[$v['name']])){
					 	$val = $this->user_data[$v['name']];
					 }
			 		 if (empty($val) && $v['type']=='plain_text'){ //maybe it's plain text
					 	$val = $v['plain_text_value'];
					 }
					 
			 		 $multiple_values = FALSE;
					 if (isset($v['values']) && $v['values']){
					 	//is checkbox, select or radio input field, so we have to include multiple+_values into indeed_create_form_elelemt
					 	$multiple_values = ihc_from_simple_array_to_k_v($v['values']);
					 }		
					 
					 if (empty($v['sublabel'])){
					 	$v['sublabel'] = '';
					 }	
					 if (empty($v['class'])){
					 	$v['class'] = '';
					 }		
					 
					 $str .= indeed_create_form_element(array(  'type'=>$v['type'], 'name'=>$v['name'], 'value' => $val,
					 										    'disabled' => $disabled, 'placeholder' => $placeholder, 'multiple_values'=>$multiple_values,
					 											'user_id'=>$this->user_id, 'sublabel' => $v['sublabel'], 'class' => $v['class'], 
					 											'callback' => $callback, 'form_type' => $this->type ));
			 		 if (!empty($this->print_errors[$v['name']])){
					 	$str .= '<div class="ihc-register-notice">' . $this->print_errors[$v['name']] . '</div>';
					 }
					 $str .= '</div>';
				 break;
				 
				  case 'ihc-register-6':
				  	 //////// FORM FIELD
				  	 $temp_type_class = 'iump-form-' . $v['type'];
					 $str .= '<div class="iump-form-line-register ' . $temp_type_class . '" id="' . $parent_id . '">';
					 $str .= '<label class="iump-labels-register">';
					 if ($v['req']){
						 $str .= '<span style="color: red;">*</span>';
					 }
					 if (isset($v['native_wp']) && $v['native_wp']){
						$str .= __($v['label'], 'ihc');
					 } else {
						$str .= ihc_correct_text($v['label']);
					 }						 
					 $str .= '</label>';
		
					 $val = '';
					 if (isset($this->user_data[$v['name']])){
					 	$val = $this->user_data[$v['name']];
					 } 
			 		 if (empty($val) && $v['type']=='plain_text'){ //maybe it's plain text
					 	$val = $v['plain_text_value'];
					 }
					 
					 $multiple_values = FALSE;
					 if (isset($v['values']) && $v['values']){
					 	//is checkbox, select or radio input field, so we have to include multiple+_values into indeed_create_form_elelemt
					 	$multiple_values = ihc_from_simple_array_to_k_v($v['values']);
					 }	
					 
					 if (empty($v['sublabel'])){
					 	$v['sublabel'] = '';
					 }	

					 if (empty($v['class'])){
					 	$v['class'] = '';
					 }
					 
					 $str .= indeed_create_form_element(array(  'type'=>$v['type'], 'name'=>$v['name'], 'value' => $val,
					 										    'disabled' => $disabled, 'multiple_values'=>$multiple_values,
					 											'user_id'=>$this->user_id, 'sublabel' => $v['sublabel'], 'class' => $v['class'], 
					 											'callback' => $callback, 'form_type' => $this->type ));
					 if (!empty($this->print_errors[$v['name']])){
					 	$str .= '<div class="ihc-register-notice">' . $this->print_errors[$v['name']] . '</div>';
					 }
					 $str .= '</div>';
				 break;
				 
				 default:
					 //////// FORM FIELD
					 $temp_type_class = 'iump-form-' . $v['type'];
					 $str .= '<div class="iump-form-line-register ' . $temp_type_class . '" id="' . $parent_id . '">';
					 $str .= '<label class="iump-labels-register">';
					 if ($v['req']){
						 $str .= '<span style="color: red;">*</span>';
					 }
					 if (isset($v['native_wp']) && $v['native_wp']){
						$str .= __($v['label'], 'ihc');
					 } else {
					 	$str .= ihc_correct_text($v['label']);
					 }						 
					 $str .= '</label>';
		
					 $val = '';
					 if (isset($this->user_data[$v['name']])){
					 	$val = $this->user_data[$v['name']];
					 }
					 if (empty($val) && $v['type']=='plain_text'){ //maybe it's plain text
					 	$val = $v['plain_text_value'];
					 }
					 
					 $multiple_values = FALSE;
					 if (isset($v['values']) && $v['values']){
					 	//is checkbox, select or radio input field, so we have to include multiple+_values into indeed_create_form_elelemt
					 	$multiple_values = ihc_from_simple_array_to_k_v($v['values']);
					 }
					 
					 if (empty($v['sublabel'])){
					 	$v['sublabel'] = '';
					 }	

					 if (empty($v['class'])){
					 	$v['class'] = '';
					 }
					 
					 $str .= indeed_create_form_element(array(  'type'=>$v['type'], 'name'=>$v['name'], 'value' => $val,
					 										    'disabled' => $disabled, 'multiple_values'=>$multiple_values,
					 										    'user_id'=>$this->user_id, 'sublabel' => $v['sublabel'], 'class' => $v['class'], 
					 											'callback' => $callback, 'form_type' => $this->type ));
					 if (!empty($this->print_errors[$v['name']])){
					 	$str .= '<div class="ihc-register-notice">' . $this->print_errors[$v['name']] . '</div>';
					 }
					 $str .= '</div>';
				 break;
			 }
			return $str;	
		}
		///////
		private function print_tos($v=array()){
			/*
			 * @param array
			 * @return string
			 */
			$str = '';
			$tos_msg = get_option('ihc_register_terms_c');//getting tos message
			$tos_page_id = get_option('ihc_general_tos_page');
			$tos_link = get_permalink($tos_page_id);

			if ($tos_msg && $tos_page_id){
				$class = (empty($v['class'])) ? '' : $v['class'];
				$id = 'ihc_tos_field_parent_' . rand(1,1000);
				$str .= '<div class="ihc-tos-wrap" id="' . $id . '">';
				$str .= '<input type="checkbox" value="1" name="tos" class="' . $class . '" />';
				$str .= '<a href="'.$tos_link.'" target="_blank">' . $tos_msg . '</a>';
				$str .= '</div>';
			}									
			return $str;	
		}
		
		
		
		//////
		private function print_captcha($v=array()){
			/*
			 * @param array
			 * @return string
			 */			
			$str = '';
			$key = get_option('ihc_recaptcha_public');
			if ($key){
				$class = (empty($v['class'])) ? '' : $v['class'];
				$str .= '<div class="g-recaptcha-wrapper" class="' . $class . '">';
				$str .= '<div class="g-recaptcha" data-sitekey="' . $key . '"></div>';
				$str .= '<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=en"></script>';				
				$str .= '</div>';
			}	
			return $str;
		}
		
		private function add_level_details_on_register_form(){
			/*
			 * @param level id
			 * @return string
			 */
			if ($this->current_level>-1){ /// get_option("ihc_register_show_level_price") && 
				$this->global_js .= 'jQuery(document).ready(function(){ihc_update_cart();});';			
			}
		}
		
		private function print_level_expire_time_edit(){
			/*
			 * @param none, use variables from class
			 * @return string with input for each level expire
			 */
			
			$string = '';
			if (isset($this->user_data['ihc_user_levels']) && $this->user_data['ihc_user_levels']!=-1){
				
				$user_levels = explode(',', $this->user_data['ihc_user_levels']);
				$i = 1;
				foreach ($user_levels as $v){
					
					$v = (int)$v;
					$temp_data = ihc_get_level_by_id($v);
					if ($temp_data){
						$time = ihc_get_start_expire_date_for_user_level($this->user_id, $v);
						$placeholder['start_time'] = '';
						$placeholder['expire_time'] = '';
						if (!$time['start_time']){
							$placeholder['start_time'] = '----/--/----';
						}
						if (!$time['expire_time']){
							$placeholder['expire_time'] = '----/--/----';
						}
						
						if (!isset($temp_data['access_type'])){
							$temp_data['access_type'] = 'LifeTime';
						}
						$string .= '<tr class="'. ($i%2==0 ? 'alternate':'') .'" id="tr_level_user_' . $v . '_' . $this->user_id . '">' 
											. '<td style="color: #21759b; font-weight:bold; width:120px;font-family: "Oswald", arial, sans-serif !important;font-size: 14px;font-weight: 300;">' . $temp_data['name'] . '</td>'
											. '<td style="color: #888; font-weight:bold; width:120px;font-family: "Oswald", arial, sans-serif !important;font-size: 14px;font-weight: 300;">' . $temp_data['access_type'] . '</td>'
											. '<td>' . indeed_create_form_element( array('type'=>'text', 
																				'name'=>'start_time_levels['.$v.']', 
																				'class'=>'start_input_text', 
																				'value' => $time['start_time'],
																				'placeholder' => $placeholder['start_time'] 
																				)
											  )															
											. '</td>'											
											. '<td>' . indeed_create_form_element( array('type'=>'text', 
																				'name'=>'expire_levels['.$v.']', 
																				'class'=>'expire_input_text', 
																				'value' => $time['expire_time'],
																				'placeholder' => $placeholder['expire_time'] 
																				)
											  )
											. '</td>'
										. '</tr>';
					}
				$i++; 
				}
			}
			if ($string){
				$string = '<table class="wp-list-table widefat fixed tags ihc-manage-user-expire"><thead><tr><th>' . __('Level Name', 'ihc') . '</th><th>' . __('Acess Type', 'ihc') . '</th><th>' . __('Start Time', 'ihc') . '</th><th>' . __('Expire Time', 'ihc') . '</th></tr></thead><tbody>' . $string . '</tbody></table>';
			}
			return $string;
		}
		
		private function print_overview_post_select(){
			/*
			 * dropdown with all post 
			 * @param none
			 * @return string
			 */
			$str = '';
			$default_pages_arr = ihc_return_meta_arr('general-defaults');
			$default_pages_arr = array_diff_key($default_pages_arr, array(  'ihc_general_redirect_default_page'=>'', 
																			'ihc_general_logout_redirect'=>'', 
																			'ihc_general_register_redirect'=>'', 
																			'ihc_general_login_redirect'=>'' ));//let's exclude the redirect pages
			$args = array(
					'posts_per_page'   => 1000,
					'offset'           => 0,
					'orderby'          => 'date',
					'order'            => 'DESC',
					'post_type'        => array( 'post', 'page' ),
					'post_status'      => 'publish',
					'post__not_in'	   => $default_pages_arr,
			);
			
			$posts_array = get_posts( $args );
			$arr['-1'] = '...';
			foreach ($posts_array as $k=>$v){
				$arr[$v->ID] = $v->post_title;
			}
			$str .= '<div class="iump-form-line">';
			$str .= '<label class="iump-labels">' . __('Select Post For Account Page Overview:', 'ihc') . ' </label>';
			$args['type'] = 'select';
			$args['multiple_values'] = $arr;
			$value = get_user_meta($this->user_id, 'ihc_overview_post', true);
			$args['value'] = ($value!==FALSE) ? $value : '';
			$args['name'] = 'ihc_overview_post';
			$str .= indeed_create_form_element($args);
			$str .= '</div>';
			return $str;
		}
		
		private function update_level_expire(){
			/*
			 * used only in admin - edit user section
			 * @param none
			 * @return none
			 */
			if (!$this->is_public && $this->type=='edit'){
				if (isset($_POST['expire_levels']) && is_array($_POST['expire_levels'])){
					foreach ($_POST['expire_levels'] as $l_id=>$expire){
						$start = (isset($_POST['start_time_levels'][$l_id])) ? $_POST['start_time_levels'][$l_id] : '';
						ihc_set_time_for_user_level($this->user_id, $l_id, $start, $expire);
					}
				}
			}
		}
		
		///////
		public function save_update_user(){
			/*
			 * @param none
			 * @return none
			 */
			 $this->do_action_before_insert_user();
			 
			//settings the user data, in case of new user, set the array only with keys
			$this->userdata();
			
			// set the meta register array, values selected in dashboard, register tab
			$this->register_metas = array_merge(ihc_return_meta_arr('register'), ihc_return_meta_arr('register-msg'), ihc_return_meta_arr('register-custom-fields'));
				
			//register fields, function available in utilities.php, 
			//return something $arr[] = array($this->display_type=>'', 'name'=>'', 'label'=>'', 'type'=>'', 'order'=>'', 'native_wp' => '', 'req' => '' );
			$this->set_register_fields();					
			$this->register_with_social();			
			$this->check_username();
			$this->check_password();
			$this->check_email();
			$this->check_tos();
			$this->check_captcha();
			$this->set_roles();
			$this->set_coupon();
			$this->check_invitation_code();
						 			
			//ADD exceptions
			if (!empty($_POST['ihc_exceptionsfields'])){
				$this->exception_fields = explode(',', $_POST['ihc_exceptionsfields']);
			}
			
			$custom_meta_user = FALSE;
			if ($this->type=='create'){
				$custom_meta_user['indeed_user'] = 1;
			}
			if (!$this->is_public){
				$custom_meta_user['ihc_overview_post'] = $_POST['ihc_overview_post'];
			}
			
			foreach ($this->register_fields as $value){
				$name = $value['name'];
				if (isset($_POST[$name]) && $name!='ihc_payment_gateway'){
					if ($this->is_public && !empty($value['req']) && $_POST[$name]=='' && !in_array($name, $this->exception_fields)){
						$this->errors[$name] = $this->register_metas['ihc_register_err_req_fields'];//add the error message
					}
					if (!empty($value['native_wp'])){
						 //wp standard info
						 $this->fields[$name] = $_POST[$name];
					} else {
						 //custom field
						 $custom_meta_user[$name] = $_POST[$name];
						 $this->ihc_is_req_conditional_field($value);//conditional required field
					}
				} else if ($value['type']=='unique_value_text'){
					if (isset($_POST[$name]) && ihc_meta_value_exists($name, $_POST[$name])){
						$this->errors[$name] = __('Error', 'uap');
					}
				}
			}
					
			//PAY CHECK
			$paid = 0;
			if (!empty($this->payment_gateway) && ihc_check_payment_available($this->payment_gateway) && isset($_POST['lid'])){//ihcpay
				do {
					
					//======================== if price after discount is 0
					$level_data = ihc_get_level_by_id($_POST['lid']);
					if (ihc_dont_pay_after_discount($_POST['lid'], $this->coupon, $level_data)){
						//will continue in set_levels() method
						break;
					}
					//========================	
					if ($level_data['payment_type']!='payment'){ /// extra check for braintree
						break; /// free level
					}			
					
					switch ($this->payment_gateway){//ihcpay
						case 'authorize':
							/*************** AUTHORIZE *****************/
							if (!ihc_is_level_reccuring($_POST['lid'])){
								break;
							}
							global $ihc_pay_error;
							$pay_errors = '';
							foreach ($_POST as $key => $vals){
								$exp_key = explode('_', $key);
								if ($exp_key[0] == 'ihcpay'){
									if (empty($_POST[$key])){										
										$ihc_pay_error['not_empty'] = $this->register_metas['ihc_register_err_req_fields'];
										$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];										
										$pay_errors = 1;
									}
								}
							}
							
							/// EXTRA TESTINT FOR EXPIRATION DATE & CARD NUMBER
							if ($pay_errors==''){
								///TESTING EXPIRATION DATE
								if (preg_match("/[^0-9]/", $_POST['ihcpay_card_expire'])){
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['wrong_expiration'] = __('Please enter the expiration date in the MMYY format', 'ihc');
									$pay_errors = 1;										
								}	
								$current_year_a = date('y', time());
								$current_month_a = date('m', time()); 
								$temp_string = (string)$_POST['ihcpay_card_expire'];
								$post_month = $temp_string[0] . $temp_string[1];
								$post_month = (int)$post_month;
								$post_year = $temp_string[2] . $temp_string[3]; 
								$post_year = (int)$post_year;
								$expiration_length = strlen($_POST['ihcpay_card_expire']);
								if ($expiration_length!=4){
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['wrong_expiration'] = __('Please enter the expiration date in the MMYY format', 'ihc');
									$pay_errors = 1;										
								}
								if ($post_month>12){
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['wrong_expiration'] = __('Please enter the expiration date in the MMYY format', 'ihc');
									$pay_errors = 1;										
								}
								if ($post_year<$current_year_a){
									/// ERROR ON YEAR (TOO OLD)									
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['wrong_expiration'] = __('Please enter the expiration date in the MMYY format', 'ihc');
									$pay_errors = 1;											
								} else if ($post_year==$current_year_a){
									/// TESTING THE MONTH
									if ($post_month<$current_month_a){
										$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
										$ihc_pay_error['wrong_expiration'] = __('Please enter the expiration date in the MMYY format', 'ihc');
										$pay_errors = 1;										
									}
								}
								
								/// TESTING CARD NUMBER
								if (preg_match("/[^0-9]/", $_POST['ihcpay_card_number'])){
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['invalid_card'] = __('Invalid card number', 'ihc');
									$pay_errors = 1;										
								}
								$card_number_length = strlen($_POST['ihcpay_card_number']);
								if ($card_number_length<13 || $card_number_length>16){
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['invalid_card'] = __('Invalid card number', 'ihc');
									$pay_errors = 1;									
								}
								/// FIRST NAME
								if (preg_match("/[^A-Za-z_]/", $_POST['ihcpay_first_name'])){
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['invalid_first_name'] = __('Name field can only contain letters', 'ihc');
									$pay_errors = 1;											
								}
								/// LAST NAME
								if (preg_match("/[^A-Za-z_]/", $_POST['ihcpay_last_name'])){
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['invalid_last_name'] = __('Name field can only contain letters', 'ihc');
									$pay_errors = 1;											
								}														
							}							
							
							if ($pay_errors == '' && !$this->errors){
								if (!class_exists('ihcAuthorizeNet')){
									require_once IHC_PATH . 'classes/ihcAuthorizeNet.class.php';
								}								
								$auth_pay = new ihcAuthorizeNet();
								$charge = $auth_pay->charge($_POST);
								if ($charge){
									$pay_result = $auth_pay->subscribe($_POST);
									if ($pay_result['code'] == 2){
										$paid = 1;
										$trans_id = $pay_result['trans_id'];
										$this->authorize_txn_id = $trans_id;
										$trans_info = $pay_result;
										$trans_info['ihc_payment_type'] = 'authorize';
									} else {
										$this->errors[] = $pay_result['message'];
									}
								} else {
									/// error 
									$this->errors['payment_gateway-authorize'] = TRUE;
								}
							}
							break;
						case 'stripe':
							/*************** STRIPE *****************/
							if (isset($_POST['stripeToken'])) {
								if (!$this->errors){
									if (!class_exists('ihcStripe')){
										require_once IHC_PATH . 'classes/ihcStripe.class.php';
									}									
									$payment_obj = new ihcStripe();
									$pay_result = $payment_obj->charge($_POST);
									if ($pay_result['message'] == "pending") {//if ($pay_result['message'] == "success") {
										$paid = 1;
										$trans_id = $pay_result['trans_id'];
										$trans_info = $pay_result;
										$trans_info['ihc_payment_type'] = 'stripe';
									}
									unset($_POST['stripeToken']);
								}
							}
							break;
						case 'braintree':
							/*************** BRAINTREE *****************/
							$pay_errors = '';
							foreach ($_POST as $key => $vals){
								if (strpos($key, 'ihc_braintree')===0){
									if (empty($_POST[$key])){
										global $ihc_pay_error;
										$ihc_pay_error['braintree']['not_empty'] = $this->register_metas['ihc_register_err_req_fields'];
										$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];										
									}
								}
							}
							
							/// EXTRA TESTINT FOR EXPIRATION DATE & CARD NUMBER
							if ($pay_errors==''){
								///TESTING EXPIRATION DATE
								if (preg_match("/[^0-9]/", $_POST['ihc_braintree_card_expire_year'])){
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['braintree']['wrong_expiration'] = __('Please enter the expiration date in the MMYY format', 'ihc');									
								}	
								$current_year_a = date('y', time());
								$current_month_a = date('m', time()); 
								$post_month = (int)$_POST['ihc_braintree_card_expire_month'];
								$post_year = (int)$_POST['ihc_braintree_card_expire_year'];
								if ($post_month>12){
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['braintree']['wrong_expiration'] = __('Wrong month', 'ihc');	
								}
								if ($post_year<$current_year_a){
									/// ERROR ON YEAR (TOO OLD)									
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['braintree']['wrong_expiration'] = __('Wrong year', 'ihc');	
								} else if ($post_year==$current_year_a){
									/// TESTING THE MONTH
									if ($post_month<$current_month_a){
										$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
										$ihc_pay_error['braintree']['wrong_expiration'] = __('Wrong month', 'ihc');							
									}
								}
								
								/// TESTING CARD NUMBER
								if (preg_match("/[^0-9]/", $_POST['ihc_braintree_card_number'])){
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['braintree']['invalid_card'] = __('Invalid card number', 'ihc');										
								}
								$card_number_length = strlen($_POST['ihc_braintree_card_number']);
								if ($card_number_length<13 || $card_number_length>16){
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['braintree']['invalid_card'] = __('Invalid card number', 'ihc');								
								}
								/// FIRST NAME
								if (preg_match("/[^A-Za-z_]/", $_POST['ihc_braintree_first_name'])){
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['braintree']['invalid_first_name'] = __('Name field can only contain letters', 'ihc');											
								}
								/// LAST NAME
								if (preg_match("/[^A-Za-z_]/", $_POST['ihc_braintree_last_name'])){
									$this->errors[] = $this->register_metas['ihc_register_err_req_fields'];
									$ihc_pay_error['braintree']['invalid_last_name'] = __('Name field can only contain letters', 'ihc');											
								}														
							}								
							break;
					}					
				} while (FALSE);
			}

			$this->errors = apply_filters('ump_before_printing_errors', $this->errors);	
			if ($this->errors){
				 //print the error and exit
				 $this->return_errors();
				 return FALSE;
			}			
			
			//=========================== SAVE / UPDATE
			//wp native user
			if ($this->type=='create'){
				//add new user
				if ($this->set_password_auto || ihc_is_magic_feat_active('register_lite')){
					$this->set_password_and_username();
				}
				$this->fields = apply_filters('ump_before_register_new_user', $this->fields);
				$this->user_id = wp_insert_user($this->fields);
				do_action('ump_on_register_action', $this->user_id);
				///Wp Admin Dashboard Notification
				//Ihc_Db::increment_dashboard_notification('users');
			} else {
				//update user
				$this->fields = apply_filters('ump_before_update_user', $this->fields);
				$this->fields['ID'] = $this->user_id;
				wp_update_user($this->fields);
				do_action('ump_on_update_action', $this->user_id);
			}
			
			$this->do_opt_in();
			$this->double_email_verification();
			
			//custom user meta
			if ($custom_meta_user){
				foreach ($custom_meta_user as $k=>$v){		
					update_user_meta($this->user_id, $k, $v);
					do_action('ihc_user_save_custom_field', $this->user_id, $k, $v);
				}
			}
			
			//auto login
			if (isset($this->shortcodes_attr['autologin']) && $this->shortcodes_attr['autologin']!==FALSE){
				$this->register_metas['ihc_register_auto_login'] = $this->shortcodes_attr['autologin'];
			}
			if ($this->is_public && $this->type=='create' && 
					!empty($this->register_metas['ihc_register_auto_login']) && !empty($this->register_metas['ihc_register_new_user_role']) 
					&& $this->register_metas['ihc_register_new_user_role']!='pending_user'){
				wp_set_auth_cookie($this->user_id);
			}
			
			$this->save_coupon();//save coupon if used
			$this->notify_admin();
			$this->notify_user();
			$this->set_levels();//USER LEVELS	
			$this->update_level_expire();//only for admin
			$this->do_individual_page();
			
			if ($this->send_password_via_mail){
				$this->notify_user_send_password();
			}
						
			//PAY SAVE
			if($paid == 1){						
				$dont_save_order = TRUE;				
				ihc_insert_update_transaction($this->user_id, $trans_id, $trans_info, $dont_save_order);
				//only authorize && stripe		
				if ($this->payment_gateway=='authorize'){//ihcpay
					//only authorize with reccuring
					$level_data = ihc_get_level_by_id($_POST['lid']);
					if (isset($level_data['access_type']) && $level_data['access_type']=='regular_period'){
						ihc_update_user_level_expire($level_data, $_POST['lid'], $this->user_id);
						ihc_switch_role_for_user($this->user_id);						
						$this->insert_the_order('Completed');///Save Order 
					}
				}
			}
			
			if ($this->is_public){
				$this->succes_message();//this will redirect
			}				
		}

		private function set_password_and_username(){
			/*
			 * @param none
			 * @return none
			 */
			if (empty($this->fields['user_login'])){
			 	$this->fields['user_login'] = @$_POST['user_email'];				
			}
			if (empty($this->fields['user_pass'])){
			 	$this->fields['user_pass'] = wp_generate_password(10);			
				$this->send_password_via_mail = TRUE;	
			}		 
		}
		
		///handle password
		private function check_password(){
			if ($this->is_public && $this->type=='create' && !isset($_POST['pass1'])){
				$this->set_password_auto = TRUE;
				return;
			}
						
			if(($this->type=='edit' && !empty($_POST['pass1'])) || $this->type=='create' ){
				///// only for create new user or in case that current user has selected a new password (edit)
				
				//check the strength
				if ($this->register_metas['ihc_register_pass_options']==2){
					//characters and digits
					if (!preg_match('/[a-z]/', $_POST['pass1'])){
						$this->errors['pass1'] = $this->register_metas['ihc_register_pass_letter_digits_msg'];
					}						
					if (!preg_match('/[0-9]/', $_POST['pass1'])){
						$this->errors['pass1'] = $this->register_metas['ihc_register_pass_letter_digits_msg'];
					}	
				} elseif ($this->register_metas['ihc_register_pass_options']==3){
					//characters, digits and one Uppercase letter
					if (!preg_match('/[a-z]/', $_POST['pass1'])){
						$this->errors['pass1'] = $this->register_metas['ihc_register_pass_let_dig_up_let_msg'];
					}						
					if (!preg_match('/[0-9]/', $_POST['pass1'])){
						$this->errors['pass1'] = $this->register_metas['ihc_register_pass_let_dig_up_let_msg'];
					}
					if (!preg_match('/[A-Z]/', $_POST['pass1'])){
						$this->errors['pass1'] = $this->register_metas['ihc_register_pass_let_dig_up_let_msg'];
					}
				}
				
				//check the length of password
				if($this->register_metas['ihc_register_pass_min_length']!=0){
					if(strlen($_POST['pass1'])<$this->register_metas['ihc_register_pass_min_length']){
						$this->errors['pass1'] = str_replace( '{X}', $this->register_metas['ihc_register_pass_min_length'], $this->register_metas['ihc_register_pass_min_char_msg'] );
					}
				}
				if(isset($_POST['pass2'])){
					if($_POST['pass1']!=$_POST['pass2']){
						$this->errors['pass2'] = $this->register_metas['ihc_register_pass_not_match_msg'];
					}
				}
				//PASSWORD
				$this->fields['user_pass'] = $_POST['pass1'];
			}
			$pass1 = ihc_array_value_exists($this->register_fields, 'pass1', 'name');
			unset($this->register_fields[$pass1]);
			$pass2 = ihc_array_value_exists($this->register_fields, 'pass2', 'name');
			unset($this->register_fields[$pass2]);
		}
		
		///check email
		protected function check_email(){
			if (!is_email( $_POST['user_email'])) {
				$this->errors['user_email'] = $this->register_metas['ihc_register_invalid_email_msg'];
			}
			if (isset($_POST['confirm_email'])){
				if ($_POST['confirm_email']!=$_POST['user_email']){
					$this->errors['user_email'] = $this->register_metas['ihc_register_emails_not_match_msg'];
				}
			}	
			if (email_exists( $_POST['user_email'])){
				if ($this->type=='create' || ($this->type=='edit' && email_exists( $_POST['user_email'])!=$this->user_id  ) ){
					$this->errors['user_email'] = $this->register_metas['ihc_register_email_is_taken_msg'];
				}					
			}
		}
		
		//check username
		private function check_username(){
			//only for create
			if (ihc_is_magic_feat_active('register_lite') || !isset($_POST['user_login'])){ /// &&
				return;
			}
			
			$username = (isset($_POST['user_login'])) ? $_POST['user_login'] : '';
			
			if ($this->type=='create'){
				if (!validate_username($username)) {
					$this->errors['user_login'] = $this->register_metas['ihc_register_error_username_msg'];
				}
				if (username_exists($username)) {
					$this->errors['user_login'] = $this->register_metas['ihc_register_username_taken_msg'];
				}				
			}
		}
		
		private function check_invitation_code(){
			/*
			 * @param none
			 * @return none
			 */
			 $field_key = ihc_array_value_exists($this->register_fields, 'ihc_invitation_code_field', 'name');
			 $check_invite_code = FALSE;
			 if ($field_key!==FALSE && !empty($this->register_fields[$field_key]) && !empty($this->register_fields[$field_key]['display_public_reg'])){
			 	$check_invite_code = TRUE;
			 }
			 if ($this->type=='create' && $check_invite_code && get_option('ihc_invitation_code_enable')){
			 	if (empty($_POST['ihc_invitation_code_field']) || !Ihc_Db::invitation_code_check($_POST['ihc_invitation_code_field'])){
			 		$err_msg = get_option('ihc_invitation_code_err_msg');
					if (!$err_msg){
				 		$this->errors['ihc_invitation_code_field'] = __('Your Invitation Code is wrong.', 'ihc');						
					} else {
				 		$this->errors['ihc_invitation_code_field'] = $err_msg;	
					}					
			 	} else {
			 		Ihc_Db::invitation_code_increment_submited_value($_POST['ihc_invitation_code_field']);
					unset($_POST['ihc_invitation_code_field']);
					if (isset($this->register_fields[$field_key])){
						unset($this->register_fields[$field_key]);
					}
			 	}
			 }
		}
		
		///////// TERMS AND CONDITIONS CHECKBOX CHECK
		private function check_tos(){
			//check if tos was printed
			$tos_page_id = get_option('ihc_general_tos_page');
			$tos_msg = get_option('ihc_register_terms_c');//getting tos message
			if (!$tos_page_id || !$tos_msg){
				$tos = ihc_array_value_exists($this->register_fields, 'tos', 'name');
				unset($this->register_fields[$tos]);
				return;
			}
			

			if ($this->tos && $this->type=='create'){
				$tos = ihc_array_value_exists($this->register_fields, 'tos', 'name');
				if ($tos!==FALSE && $this->register_fields[$tos][$this->display_type]){
					unset($this->register_fields[$tos]);
					if (!isset($_POST['tos']) || $_POST['tos']!=1){
						$this->errors['tos'] = get_option('ihc_register_err_tos');
					}
				}
			} else {
				$tos = ihc_array_value_exists($this->register_fields, 'tos', 'name');
				if ($tos!==FALSE) unset($this->register_fields[$tos]);
			}
		}
		
		//////////// CAPTCHA
		private function check_captcha(){
			if ($this->type=='create' && $this->captcha){
				$key = ihc_array_value_exists($this->register_fields, 'recaptcha', 'name');
				if ($key!==FALSE && isset($this->register_fields[$key]) && isset($this->register_fields[$key]['target_levels'])){
					///not available for current level
					$target_levels = explode(',', $this->register_fields[$key]['target_levels']);
					if (!in_array($this->current_level, $target_levels)){
						return;
					}
				}
				
				//check if capcha key is set
				$captcha_key = get_option('ihc_recaptcha_public');
				if (!$captcha_key){
					$captcha = ihc_array_value_exists($this->register_fields, 'recaptcha', 'name');
					unset($this->register_fields[$captcha]);
					return;
				}
				
				$captcha = ihc_array_value_exists($this->register_fields, 'recaptcha', 'name');
				if ($captcha!==FALSE && $this->register_fields[$captcha][$this->display_type]){
					$captha_err = get_option('ihc_register_err_recaptcha');
					unset($this->register_fields[$captcha]);
					if (isset($_POST['g-recaptcha-response'])){					
						$secret = get_option('ihc_recaptcha_private');
						if ($secret){
							if (!class_exists('ReCaptcha')){
								include_once IHC_PATH . 'classes/ReCaptcha/ReCaptcha.php';								
							}
							$recaptcha = new ReCaptcha($secret);
							$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
							if (!$resp->isSuccess()){
								$this->errors['captcha'] = $captha_err;
							}
						} else {
							$this->errors['captcha'] = $captha_err;
						}
					} else {
						$this->errors['captcha'] = $captha_err;
					}
				}
			}
		}
		
		private function ihc_is_req_conditional_field($field_meta=array()){
			/*
			 * @param array
			 * @return none
			 */
			if (!empty($field_meta['type']) && $field_meta['type']=='conditional_text' && $this->is_public){
				$field_name = $field_meta['name'];
				if ($field_meta['conditional_text']!=$_POST[$field_name]){
					if (!empty($field_meta['error_message'])){
						$this->errors[$field_name] = ihc_correct_text($field_meta['error_message']);
					} else {
						$this->errors[$field_name] = __("Error");
					}
				}
			}
		}
		
		////WP ROLE
		private function set_roles(){
			//role
			if ($this->is_public && $this->type=='create'){
				// special role for this level?
				if (isset($_POST['lid'])){
					$level_data = ihc_get_level_by_id($_POST['lid']);
					if (isset($level_data['custom_role_level']) && $level_data['custom_role_level']!=-1 && $level_data['custom_role_level']){
						$this->fields['role'] = $level_data['custom_role_level'];
						return;
					}					
				}
				
				/// CUSTOM ROLE FROM SHORTCODE
				if (isset($this->shortcodes_attr['role']) && $this->shortcodes_attr['role'] !== FALSE){
					$this->register_metas['ihc_register_new_user_role'] = $this->shortcodes_attr['role'];
				}
				
				if (isset($this->register_metas['ihc_register_new_user_role'])){
					$this->fields['role'] = $this->register_metas['ihc_register_new_user_role'];
				} else {
					$this->fields['role'] = 'subscriber';
				}	
			} else if (!$this->is_public){
				if (isset($_POST['role'])){
					$this->fields['role'] = $_POST['role'];
				}
			}
		}
		
		public function update_level($url_return=''){
			/*
			 * used only in public section (ihc_acquire_new_level() in IHC_PATH/functions.php), for add new levels to user
			 * @param none 
			 * @return none
			 */			 
			if ($this->is_public){
				$lid = (isset($_REQUEST['lid'])) ? $_REQUEST['lid'] : -1;
				$lid = (int)$lid; /// let's be sure this is a number
				/****************** PUBLIC ******************/
				if (isset($lid) && $lid!=='none' && $lid>-1){ //'lid' can be none in some older versions of plugin
					$user_levels = get_user_meta($this->user_id, 'ihc_user_levels', true);
					if ($user_levels){
						$user_levels_arr = explode(',', $user_levels);
						if (!in_array($lid, $user_levels_arr)){
							$user_levels_arr[] = $lid;
						}
						$user_levels = implode(',', $user_levels_arr);
					} else {
						$user_levels = $lid;
					}					
					$level_data = ihc_get_level_by_id($lid);
					
					if ($level_data['payment_type']=='payment'){
						ihc_send_user_notifications($this->user_id, 'user_update');//[$l_id]
						ihc_send_user_notifications($this->user_id, 'admin_user_profile_update');/// notification to admin
						
						//======================== if price after discount is 0
						if (ihc_dont_pay_after_discount($lid, $this->coupon, $level_data, TRUE)){
							$this->handle_levels_assign($lid);
							update_user_meta($this->user_id, 'ihc_user_levels', $user_levels);
							ihc_update_user_level_expire($level_data, $lid, $this->user_id);
							if ($url_return){
								wp_redirect($url_return);
								exit();
							} else {
								return;
							}
						}
						//========================
						
						switch ($this->payment_gateway){
							case 'paypal':
								$this->handle_levels_assign($lid);
								update_user_meta($this->user_id, 'ihc_user_levels', $user_levels);
								
								//REDIRECT
								if ( ihc_check_payment_available('paypal') ){
									$href = IHC_URL . 'public/paypal_payment.php?lid=' . $lid . '&uid='.$this->user_id;
									if ($this->coupon){
										$href .= '&ihc_coupon=' . $this->coupon;
									}
									if ($this->taxes_enabled){
										$ihc_country = get_user_meta($this->user_id, 'ihc_country', TRUE);
										$href .= '&ihc_country=' . $ihc_country; 
										$state = get_user_meta($this->user_id, 'ihc_state', TRUE);
										$href .= '&ihc_state=' . $state; 
									}
									//
									$this->insert_the_order();
									
									wp_redirect($href);
									exit();									
								} else {
									$redirect_back = TRUE;
								}
								
								break;
							case 'authorize':								
								//redirect to payment
								if (isset($level_data['access_type']) && $level_data['access_type']=='regular_period'){
									/////// AUTHORIZE RECCURING
									if ( ihc_check_payment_available('authorize') ){
										$url_return = add_query_arg( 'ihc_authorize_fields', '1', $url_return );
										$url_return = add_query_arg( 'lid', $lid, $url_return );
										if ($this->coupon){
											$url_return = add_query_arg( 'ihc_coupon', $this->coupon, $url_return );
										}
										if ($this->taxes_enabled){
											$ihc_country = get_user_meta($this->user_id, 'ihc_country', TRUE);
											$url_return = add_query_arg('ihc_country', $ihc_country, $url_return);
											$state = get_user_meta($this->user_id, 'ihc_state', TRUE);
											$url_return = add_query_arg('ihc_state', $state, $url_return);											
										}									
										//
										$this->insert_the_order();
									
										wp_redirect($url_return);
										exit();
									} else {
										$redirect_back = TRUE;
									}
								} else {
									/////// AUTHORIZE SIMPLE
									$this->handle_levels_assign($lid);
									update_user_meta($this->user_id, 'ihc_user_levels', $user_levels);
									
									//REDIRECT
									if ( ihc_check_payment_available('authorize') ){
										$href = IHC_URL . 'public/authorize_payment.php?lid=' . $lid . '&uid=' . $this->user_id;
										if ($this->coupon){
											$href .= '&ihc_coupon=' . $this->coupon;
										}
										if (!empty($_POST['ihc_country'])){
											$href .= '&ihc_country=' . $_POST['ihc_country']; 
										}	
										if ($this->taxes_enabled){
											$ihc_country = get_user_meta($this->user_id, 'ihc_country', TRUE);
											$href .= '&ihc_country=' . $ihc_country; 
											$state = get_user_meta($this->user_id, 'ihc_state', TRUE);
											$href .= '&ihc_state=' . $state; 											
										}																			
										$this->insert_the_order();
										wp_redirect($href);
										exit();										
									} else {
										$redirect_back = TRUE;
									}							
								}
								break;
							case 'twocheckout':	
								$this->handle_levels_assign($lid);
								update_user_meta($this->user_id, 'ihc_user_levels', $user_levels);
								if ( ihc_check_payment_available('twocheckout') ){	
									$this->insert_the_order();
									if ($this->taxes_enabled){
										$ihc_country = get_user_meta($this->user_id, 'ihc_country', TRUE);
									} else {
										$ihc_country = FALSE;
									}					
									ihc_twocheckout_submit($this->user_id, $lid, $this->coupon, $ihc_country);//function available in utilities.php
								} else {
									$redirect_back = TRUE;
								}								
								break;
							case 'bank_transfer':
								$this->handle_levels_assign($lid);
								update_user_meta($this->user_id, 'ihc_user_levels', $user_levels);
								if ( ihc_check_payment_available('bank_transfer') ){
									if ($url_return){
										$url = $url_return;								
										$bt_params = array( 'ihc_success_bt' => true,
															'ihc_lid' => $lid,
															'ihc_uid' => $this->user_id,
										);
										if ($this->coupon){
											$coupon_data = ihc_check_coupon($this->coupon, $lid);
											if ($coupon_data){
												if ($coupon_data['discount_type']=='percentage'){
													$bt_params['cp'] = $coupon_data['discount_value'];
												} else {
													$bt_params['cc'] = $coupon_data['discount_value'];
												}
												ihc_submit_coupon($this->coupon);
											}
										}
										
										//country
										if ($this->taxes_enabled){
											$ihc_country = get_user_meta($this->user_id, 'ihc_country', TRUE);
											$bt_params['ihc_country'] = $ihc_country; 
											$state = get_user_meta($this->user_id, 'ihc_state', TRUE);
											$bt_params['ihc_state'] = $state;
										}			
																			
										$this->insert_the_order();
										$dynamic_data = array('order_id'=>$this->order_id);
										ihc_send_user_notifications($this->user_id, 'bank_transfer', $lid, $dynamic_data);
											
										$url = add_query_arg($bt_params, $url);
										$url .= '#ihc_bt_success_msg';
										
										wp_redirect($url);
										exit();
									}	
								} else {
									$redirect_back = TRUE;
								}								
								break;
							case 'stripe':
								if ( ihc_check_payment_available('stripe') ){
									$page = get_option('ihc_general_user_page');
									$url = get_permalink($page);
									$url = add_query_arg('ihc_ap_menu', 'profile', $url);
									$url = add_query_arg('lid', $lid, $url);
									if ($this->taxes_enabled){
										$ihc_country = get_user_meta($this->user_id, 'ihc_country', TRUE);
										$url = add_query_arg('ihc_country', $ihc_country, $url);
										$state = get_user_meta($this->user_id, 'ihc_state', TRUE);
										$url = add_query_arg('ihc_state', $state, $url);
									}										
									$this->insert_the_order();
									wp_redirect($url);
									exit();									
								} else {
									$redirect_back = TRUE;
								}
								break;
							
							case 'braintree':								
								if (ihc_check_payment_available('braintree')){
									$url_return = add_query_arg('ihc_braintree_fields', '1', $url_return);
									$url_return = add_query_arg('lid', $lid, $url_return);
									if ($this->coupon){
										$url_return = add_query_arg('ihc_coupon', $this->coupon, $url_return);
									}
									if ($this->taxes_enabled){
										$ihc_country = get_user_meta($this->user_id, 'ihc_country', TRUE);
										$url_return = add_query_arg('ihc_country', $ihc_country, $url_return);
										$state = get_user_meta($this->user_id, 'ihc_state', TRUE);
										$url_return = add_query_arg('ihc_state', $state, $url_return);
									}		
									wp_redirect($url_return);
									exit();
								} else {
									$redirect_back = TRUE;
								}		
								break;		
													
							case 'payza':
								$this->handle_levels_assign($lid);
								update_user_meta($this->user_id, 'ihc_user_levels', $user_levels);
								
								//REDIRECT
								if ( ihc_check_payment_available('payza') ){
									$href = IHC_URL . 'public/payza_payment.php?lid=' . $lid . '&uid='.$this->user_id;
									if ($this->coupon){
										$href .= '&ihc_coupon=' . $this->coupon;
									}
									if ($this->taxes_enabled){
										$ihc_country = get_user_meta($this->user_id, 'ihc_country', TRUE);
										$href .= '&ihc_country=' . $ihc_country; 
										$state = get_user_meta($this->user_id, 'ihc_state', TRUE);
										$href .= '&ihc_state=' . $state; 
									}
									//
									$this->insert_the_order();
									
									wp_redirect($href);
									exit();									
								} else {
									$redirect_back = TRUE;
								}								
								break;		
													
							default:
								wp_redirect($url_return);
								exit();
								break;
						}//end switch	
						if (!empty($redirect_back)){
							wp_redirect($url_return);
							exit();
						}						
					} else {
						/****************** FREE LEVEL ******************/
						$this->handle_levels_assign($lid);
						update_user_meta($this->user_id, 'ihc_user_levels', $user_levels);
						if ($url_return){
							wp_redirect($url_return);
							exit();
						}
					}
				}
			}			
		}
		
		///LEVELs
		private function set_levels(){
			if ($this->is_public){
				/****************** PUBLIC ******************/
				if (isset($_POST['lid']) && $_POST['lid']!=='none' && $_POST['lid']>-1){ //'lid' can be none in a older version
					$user_levels = get_user_meta($this->user_id, 'ihc_user_levels', true);
					if ($user_levels!==FALSE && $user_levels!=''){
						$user_levels_arr = explode(',', $user_levels);
						if (!in_array($_POST['lid'], $user_levels_arr)){
							$user_levels_arr[] = $_POST['lid'];
						}
						$user_levels = implode(',', $user_levels_arr);
					} else {
						$user_levels = $_POST['lid'];
					}		

					$this->handle_levels_assign($_POST['lid']);
					update_user_meta($this->user_id, 'ihc_user_levels', $user_levels);
					$level_data = ihc_get_level_by_id($_POST['lid']);
					
					//======================== if price after discount is 0
					if (ihc_dont_pay_after_discount($_POST['lid'], $this->coupon, $level_data, TRUE)){
						ihc_update_user_level_expire($level_data, $_POST['lid'], $this->user_id);
						return;
					}
					//========================		
								
					if ($level_data['payment_type']=='payment'){		
						switch ($this->payment_gateway){
							case 'paypal':
								if ( ihc_check_payment_available('paypal') ){
									/// SAVE THE ORDER
									$this->insert_the_order();
									
									//redirect to payment
									$href = IHC_URL . 'public/paypal_payment.php?lid=' . $_POST['lid'] . '&uid=' . $this->user_id;
									if ($this->coupon){
										$href .= '&ihc_coupon=' . $this->coupon;
									}
									if (!empty($_POST['ihc_country']) && $this->taxes_enabled){
										$href .= '&ihc_country=' . $_POST['ihc_country']; 
										$state = get_user_meta($this->user_id, 'ihc_state', TRUE);
										$href .= '&ihc_state=' . $state; 											
									}										
									wp_redirect($href);
									exit();
								}	
								break;
							case 'authorize':
								if (isset($level_data['access_type']) && $level_data['access_type']=='regular_period'){											
								} else {								
									if ( ihc_check_payment_available('authorize') ){
										/// SAVE THE ORDER
										$this->insert_the_order();
											
										$href = IHC_URL . 'public/authorize_payment.php?lid='.$_POST['lid'].'&uid='.$this->user_id;
										if ($this->coupon){
											$href .= '&ihc_coupon=' . $this->coupon;
										}
										if (!empty($_POST['ihc_country']) && $this->taxes_enabled){
											$href .= '&ihc_country=' . $_POST['ihc_country']; 
											$state = get_user_meta($this->user_id, 'ihc_state', TRUE);
											$href .= '&ihc_state=' . $state; 												
										}										
										wp_redirect($href);
									 	exit();
									}
								}
								break;
							case 'twocheckout':
								if ( ihc_check_payment_available('twocheckout') ){
									/// SAVE THE ORDER
									$this->insert_the_order();	
									if (!empty($_POST['ihc_country']) && $this->taxes_enabled){
										$ihc_country = $_POST['ihc_country']; 
									} else {
										$ihc_country = FALSE;
									}																					
									ihc_twocheckout_submit($this->user_id, $_POST['lid'], $this->coupon, $ihc_country);//function available in utilities.php
								}
								break;
							case 'bank_transfer':
								if ( ihc_check_payment_available('bank_transfer') ){
									/// SAVE THE ORDER
									$this->insert_the_order();		
									$dynamic_data = array('order_id'=>$this->order_id);
									ihc_send_user_notifications($this->user_id, 'bank_transfer', $_POST['lid'], $dynamic_data);													
									$this->bank_transfer_message = TRUE;
								}
								break;
							case 'stripe':
								$this->insert_the_order();
								break;
							case 'braintree':
								if (ihc_check_payment_available('braintree')){
									$post_data = $_POST;
									$post_data['uid'] = $this->user_id;
									$post_data['lid'] = $_POST['lid'];
									$post_data['ihc_coupon'] = $this->coupon;
									if (!empty($post_data['ihc_country']) && !$this->taxes_enabled){
										unset($post_data['ihc_country']);											
									}
									require_once IHC_PATH . 'classes/Ihc_Braintree.class.php';
									$braintree = new Ihc_Braintree();									
									$braintree->do_charge($post_data);									
								}								
								break;
							case 'payza':
								if ( ihc_check_payment_available('payza') ){
									/// SAVE THE ORDER
									$this->insert_the_order();
									
									//redirect to payment
									$href = IHC_URL . 'public/payza_payment.php?lid=' . $_POST['lid'] . '&uid=' . $this->user_id;
									if ($this->coupon){
										$href .= '&ihc_coupon=' . $this->coupon;
									}
									if ($this->taxes_enabled && !empty($_POST['ihc_country'])){
										$href .= '&ihc_country=' . $_POST['ihc_country']; 
										$state = get_user_meta($this->user_id, 'ihc_state', TRUE);
										$href .= '&ihc_state=' . $state;										
									}										
									wp_redirect($href);
									exit();
								}	
								break;								
						}				
					}
				}
			} else {
				/*************** ADMIN ********************/
				if (isset($_POST['ihc_user_levels'])){				
					$the_level = $_POST['ihc_user_levels'];
					if ($_POST['ihc_user_levels']==-1 || $_POST['ihc_user_levels']===''){
						$the_level = FALSE;
					}
					$this->handle_levels_assign($the_level);
					update_user_meta($this->user_id, 'ihc_user_levels', $the_level);
				}			
			}
		}
		
		private function insert_the_order($status='pending'){
			/*
			 * @param none
			 * @return none
			 */
			$lid = (empty($_POST['lid'])) ? FALSE : $_POST['lid'];
			if ($lid===FALSE){
				$lid = (empty($_GET['lid'])) ? FALSE : $_GET['lid'];
			}
			
			if (!empty($this->user_id) && $lid!==FALSE){
				$levels = get_option('ihc_levels');
				$amount = $levels[$lid]['price'];
				$extra_order_info = array();
				
				/// 
				if (!empty($this->authorize_txn_id)){
					$extra_order_info['txn_id'] = $this->authorize_txn_id;
				}
								
				if (isset($levels[$lid]['access_type']) && $levels[$lid]['access_type']=='regular_period'){
					if (isset($levels[$lid]['access_trial_price']) && $levels[$lid]['access_trial_price']!=''){
						/// AMOUNT FOR TRIAL
						$amount = $levels[$lid]['access_trial_price'];
						$extra_order_info['is_trial'] = TRUE;
					}
				}
								
				if ($this->coupon){
					$coupon_data = ihc_check_coupon($this->coupon, $lid);
					if ($coupon_data){
						$extra_order_info['discount_value'] = ihc_get_discount_value($amount, $coupon_data);
						$amount = ihc_coupon_return_price_after_decrease($amount, $coupon_data);						
					}
				}
				/// TAXES
				if (!empty($_POST['ihc_country'])){
					$ihc_country = $_POST['ihc_country'];
				} else {
					$ihc_country = get_user_meta($this->user_id, 'ihc_country', TRUE);
				}
				if (!empty($_POST['ihc_state'])){
					$state = $_POST['ihc_state'];
				} else {
					$state = get_user_meta($this->user_id, 'ihc_state', TRUE);
					if ($state==FALSE) $state = '';
				}			
				
				$country = ($ihc_country==FALSE) ? '' : $ihc_country;
				$taxes_data = ihc_get_taxes_for_amount_by_country($country, $state, $amount);
				if ($taxes_data && !empty($taxes_data['total'])){						
					$amount = $amount + $taxes_data['total'];
					$extra_order_info['tax_value'] = $taxes_data['total'];
				}					
				
				if ($this->payment_gateway=='stripe' && $amount<0.50){
					$amount = 0.50;/// minimum for stripe.
				}
				
				$this->order_id = ihc_insert_update_order($this->user_id, $lid, $amount, $status, $this->payment_gateway, $extra_order_info);				
			}
		}		
		
		private function handle_levels_assign($request_levels){			
			/*
			 * insert into db when user was start using this level, 
			 * if it's free level will assign the expire date, 
			 * if not paypal ipn will set the expire time
			 * @param string with all level ids separated by comma
			 * @return none
			 */			
			if ($request_levels!=-1 && $request_levels!==FALSE){				
				$current_levels = explode(',', $request_levels);

				if (count($current_levels)){
					
					//$old_levels = get_user_meta($this->user_id, 'ihc_user_levels', true);
					foreach ($current_levels as $lid){
						if (isset($lid) && $lid!='' ){ //&& strpos($old_levels, $lid)===FALSE
							//we got a new level to assign
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
							if ($this->is_public &&	$level_data['payment_type']!='free'){
								//end time will be expired, updated when payment
								$end_time = '0000-00-00 00:00:00';
							} else {
								//it's admin or free so we set the correct expire time
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
								$end_time = date('Y-m-d H:i:s', $end_time);
							}
							
							$update_time = date('Y-m-d H:i:s', $current_time);
							$start_time = date('Y-m-d H:i:s', $start_time);
							
							global $wpdb;
							$table = $wpdb->prefix . 'ihc_user_levels';
							$exists = $wpdb->get_row('SELECT * FROM ' . $table . ' WHERE user_id="' . $this->user_id . '" AND level_id="' . $lid . '";');
							if (!empty($exists)){
								$wpdb->query('DELETE FROM ' . $table .' WHERE user_id="' . $this->user_id . '" AND level_id="' . $lid . '";');//assure that pair user_id - level_id entry is not exists
							}					
							$wpdb->query('INSERT INTO ' . $table . '
												VALUES(null, "' . $this->user_id . '", "' . $lid . '", "' . $start_time . '", "' . $update_time . '", "' . $end_time . '", 0, 1);');				
							
							do_action('ihc_new_subscription_action', $this->user_id, $lid);
							
						}
					}
				}
			}
		}//end of handle_levels_assign()
		
		
		///notify admin email
		protected function notify_admin(){
			/*
			 * REGISTER SEND EMAIL - send email to admin when someone is register
			 * @param none
			 * @return none
			 */
			if ('create'==$this->type && $this->is_public && $this->register_metas['ihc_register_admin_notify']){
				$lid = (empty($_POST['lid'])) ? '' : $_POST['lid'];
				ihc_send_user_notifications($this->user_id, 'admin_user_register', $lid);
			}			
		}
		
		protected function notify_user(){
			/*
			 * @param none
			 * @return none
			 */			
			if ($this->is_public){				
				//email notification to user
				if ($this->type=='create'){
					if (!empty($this->register_metas['ihc_register_new_user_role']) && $this->register_metas['ihc_register_new_user_role']=='pending_user'){
						//PENDING NOTIFICATION
						ihc_send_user_notifications($this->user_id, 'review_request', @$_POST['lid']);//[$l_id]
					} else {
						//REGISTER NOTIFICATION
						ihc_send_user_notifications($this->user_id, 'register', @$_POST['lid']);//[$l_id]
					}					
				} else {
					ihc_send_user_notifications($this->user_id, 'user_update');/// notification to user
					ihc_send_user_notifications($this->user_id, 'admin_user_profile_update');/// notification to admin					
				}		
			}	
		}		
	
		protected function notify_user_send_password(){
			/*
			 * @param none
			 * @return none
			 */
			 ihc_send_user_notifications($this->user_id, 'register_lite_send_pass_to_user', FALSE, array('{NEW_PASSWORD}' => $this->fields['user_pass']));
		}
			
		///// RETURN ERROR
		protected function return_errors(){
			/*
			 * set the global variable with the error string
			 */
			if (!empty($this->errors)){
				global $ihc_error_register;				
				$ihc_error_register = $this->errors;				
			}
		}
		
		private function count_register_fields(){
			$count = 0;
			foreach ($this->register_fields as $v){
				if ($v[$this->display_type] > 0){
					$count++;
				}
			}
			return $count;
		}
		
		protected function succes_message(){
			/*
			 * @param none
			 * @return none
			 */			
			if ($this->type=='create'){
				$q_arg = 'create_message';				
			} else {
				$q_arg = 'update_message';						
			}		
						
			$redirect = get_option('ihc_general_register_redirect');
			if ($redirect && $redirect!=-1 && $this->type=='create'){
				//custom redirect
				$url = get_permalink($redirect);
				if (!$url){				
					$url = ihc_get_redirect_link_by_label($redirect, $this->user_id);
					if (strpos($url, IHC_PROTOCOL . $_SERVER['HTTP_HOST'] )!==0){ /// $_SERVER['SERVER_NAME'] 
						//if it's a external custom redirect we don't want to add extra params in url, so let's redirect from here
						wp_redirect($url);
						exit();						
					}
				}
			}
			
			if (empty($url)){
				$url = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; /// $_SERVER['SERVER_NAME'] 
			}		
			
			if ($this->bank_transfer_message){
				/// bt redirect only to same page
				$bt_params = array( 'ihc_register' => $q_arg,
									'ihcbt' => 'true',
									'ihc_lid' => $_POST['lid'],
									'ihc_uid' => $this->user_id
				);
				if ($this->coupon){
					$coupon_data = ihc_check_coupon($this->coupon, $_POST['lid']);
					if ($coupon_data){
						if ($coupon_data['discount_type']=='percentage'){
							$bt_params['cp'] = $coupon_data['discount_value'];
						} else {
							$bt_params['cc'] = $coupon_data['discount_value'];
						}
						ihc_submit_coupon($this->coupon);
					}
				}
				
				//country
				if (!empty($_POST['ihc_country'])){
					$bt_params['ihc_country'] = $_POST['ihc_country']; 
				}
				if (!empty($_POST['ihc_state'])){
					$bt_params['ihc_state'] = $_POST['ihc_state']; 
				}										
				$url = add_query_arg($bt_params, $url);
			} else {
				$url = add_query_arg( array( 'ihc_register' => $q_arg ), $url );
			}
			
			if (!empty($bt_params)){
				$url .= '#ihc_bt_success_msg';
			}
			
			wp_redirect($url);
			exit();
		}
		
		
		protected function do_opt_in(){
			/*
			 * @param none
			 * @return none
			 */
			if (isset($this->shortcodes_attr['double_email']) && $this->shortcodes_attr['double_email']!==FALSE){
				$double_email_verification = $this->shortcodes_attr['double_email']; 
			} else {
				$double_email_verification = get_option('ihc_register_double_email_verification');				
			}
			if (get_option('ihc_register_opt-in') && $this->type=='create' && empty($double_email_verification)){
				//If Opt In it's enable, put the email address somewhere
				// Not available when double email verification it's enabled
				ihc_run_opt_in($_POST['user_email']);
			}
		}//end of do_opt_in()
		
		
		protected function double_email_verification(){
			/*
			 * @param none
			 * @return none
			 */
			if (isset($this->shortcodes_attr['double_email']) && $this->shortcodes_attr['double_email']!==FALSE){
				$double_email_verification = $this->shortcodes_attr['double_email'];
			} else {
				$double_email_verification = get_option('ihc_register_double_email_verification');				
			}
			
			if ($this->is_public && $this->type=='create' && !empty($double_email_verification) ){
				$hash = ihc_random_str(10);
				//put the hash into user option
				update_user_meta($this->user_id, 'ihc_activation_code', $hash);
				//set ihc_verification_status @ -1
				update_user_meta($this->user_id, 'ihc_verification_status', -1);
				
				///$activation_url_w_hash = IHC_URL . 'user_activation.php?uid=' . $this->user_id . '&ihc_code=' . $hash;
				$activation_url_w_hash = site_url();
				$activation_url_w_hash = add_query_arg('ihc_action', 'user_activation', $activation_url_w_hash);	
				$activation_url_w_hash = add_query_arg('uid', $this->user_id, $activation_url_w_hash);	
				$activation_url_w_hash = add_query_arg('ihc_code', $hash, $activation_url_w_hash);
				
				//send a nice notification
				ihc_send_user_notifications($this->user_id, 'email_check', @$_POST['lid'], array('{verify_email_address_link}'=>$activation_url_w_hash));
			}
		}
		
			
		////SOCIAL MEDIA
		private function social_register_request_data(){
			$str = '';
			if ($this->is_public){
				if (!empty($_GET['ihc_fb'])){
					$ihc_register_sm_value = 'fb';
					$ihc_sm_value = $_GET['ihc_fb'];
					$ihc_sm_name = 'ihc_fb';
				} else if (!empty($_GET['ihc_tw'])){
					$ihc_register_sm_value = 'tw';
					$ihc_sm_value = $_GET['ihc_tw'];
					$ihc_sm_name = 'ihc_tw';						
				} else if (!empty($_GET['ihc_in'])){
					$ihc_register_sm_value = 'in';
					$ihc_sm_value = $_GET['ihc_in'];
					$ihc_sm_name = 'ihc_in';									
				} else if (!empty($_GET['ihc_tbr'])){
					$ihc_register_sm_value = 'tbr';
					$ihc_sm_value = $_GET['ihc_tbr'];
					$ihc_sm_name = 'ihc_tbr';									
				} else if (!empty($_GET['ihc_ig'])){
					$ihc_register_sm_value = 'ig';
					$ihc_sm_value = $_GET['ihc_ig'];
					$ihc_sm_name = 'ihc_ig';									
				} else if (!empty($_GET['ihc_vk'])){
					$ihc_register_sm_value = 'vk';
					$ihc_sm_value = $_GET['ihc_vk'];
					$ihc_sm_name = 'ihc_vk';									
				} else if (!empty($_GET['ihc_goo'])){
					$ihc_register_sm_value = 'goo';
					$ihc_sm_value = $_GET['ihc_goo'];
					$ihc_sm_name = 'ihc_goo';									
				}
				if (!empty($ihc_register_sm_value) && !empty($ihc_sm_value) && !empty($ihc_sm_name)){
					$str .= indeed_create_form_element(array('name'=>'ihc_sm_register', 'value'=>$ihc_register_sm_value, 'type'=>'hidden'));
					$str .= indeed_create_form_element(array('name'=>$ihc_sm_name, 'value'=>$ihc_sm_value, 'type'=>'hidden'));
				}
			}
			return $str;
		}
		
		private function register_with_social(){
			/*
			 * test if user was register with social. If true generate a password if it's not set
			 * @param none
			 * @return none
			 */
			if ($this->is_public){
				if (isset($_POST['ihc_sm_register'])){
					//generate password if it's not set
					if (empty($_POST['pass1'])){
						$password = wp_generate_password();
						$_POST['pass1'] = $password;
						$_POST['pass2'] = $password;
					}
					
					//add social key to current register_fields array
					$name = 'ihc_' . $_POST['ihc_sm_register'];
					$this->register_fields[] = array('name' => $name);

				}
			}
		}//end of register_with_social
		
		private function ihc_social_form(){
			/*
			 * @param none
			 * @return string
			 */
			 $url = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; /// $_SERVER['SERVER_NAME'] 
			 $str = '<form method="post" action="' . IHC_URL . 'public/social_handler.php" id="ihc_social_login_form">';
			 $str .= '<input type=hidden name=ihc_current_url value=' . urlencode($url) . ' />';
			 $str .= '<input type=hidden name=sm_register value=1 />';
			 $str .= '</form>';
			 return $str;
		}

		////COUPONS
		
		public function set_coupon($coupon=''){
			/*
			 * @param none
			 * @return none
			 */
			$this->coupon = (isset($_POST['ihc_coupon'])) ? $_POST['ihc_coupon'] : $coupon;
			if ($this->coupon){
				$this->coupon = str_replace(' ', '', $this->coupon);
				if (!empty($this->register_fields)){
					$ihc_coupon = ihc_array_value_exists($this->register_fields, 'ihc_coupon', 'name');
					if (isset($ihc_coupon) && $ihc_coupon!==FALSE){
						unset($this->register_fields[$ihc_coupon]);
					}					
				}
			}
		}
		
		public function save_coupon(){
			if ($this->coupon && $this->user_id){
				$user_coupons = get_user_meta($this->user_id, 'ihc_coupon', TRUE);
				$user_coupons[] = $this->coupon;
				update_user_meta($this->user_id, 'ihc_coupon', $user_coupons);
			}
		}
		
		
		protected function do_action_before_insert_user(){
			/*
			 * @param none
			 * @return none
			 */
			if ($this->is_public && $this->type=='create'){
				do_action('ump_before_insert_user');
			}			 
		}
		
		protected function do_individual_page(){
			/*
			 * @param none
			 * @return none
			 */
			if ($this->type=='create' && ihc_is_magic_feat_active('individual_page')){ ///  && $this->is_public
				if (!class_exists('IndividualPage')){
					include_once IHC_PATH . 'classes/IndividualPage.class.php';
				}
				$object = new IndividualPage();
				$object->generate_page_for_user($this->user_id);
			}
		}		
		
	}//end of class UserAddEdit
}
