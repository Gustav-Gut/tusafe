<?php
if (!class_exists('Ihc_Braintree')):
	
class Ihc_Braintree{
		
	public function __contruct(){}
	
	public function do_charge($input=array()){
		/*
		 * @param array
		 * @return array
		 */

		if (!isset($input['lid'])){
			return;
		}
		$levels = get_option('ihc_levels');
		if (!isset($levels[$input['lid']])){
			return;
		}
		$level_arr = $levels[$input['lid']];
		$amount = $level_arr['price'];
	
		$reccurrence = FALSE;
		if (isset($level_arr['access_type']) && $level_arr['access_type']=='regular_period'){
			$reccurrence = TRUE;
		}
		$coupon_data = array();
		if (!empty($_GET['ihc_coupon'])){
			$coupon_data = ihc_check_coupon($_GET['ihc_coupon'], $_GET['lid']);
		}		
		
		if ($reccurrence){
			////////////////////// RECCURING
			//coupon on reccurence
			if ($coupon_data){
				if (!empty($coupon_data['reccuring'])){
					//everytime the price will be reduced
					$amount = ihc_coupon_return_price_after_decrease($amount, $coupon_data);
					if (isset($level_arr['access_trial_price'])){
						$level_arr['access_trial_price'] = ihc_coupon_return_price_after_decrease($level_arr['access_trial_price'], $coupon_data, FALSE); 
					}
				} else {
					//only one time
					if (!empty($level_arr['access_trial_price'])){
						$level_arr['access_trial_price'] = ihc_coupon_return_price_after_decrease($level_arr['access_trial_price'], $coupon_data);
					} else {
						$level_arr['access_trial_price'] = ihc_coupon_return_price_after_decrease($level_arr['price'], $coupon_data);
					}
				}
			}
			//coupon on reccurence
			
			//trial block
			if (!empty($level_arr['access_trial_type']) && isset($level_arr['access_trial_price']) && $level_arr['access_trial_price']!=''){
				/// TAXES
				$country = isset($_GET['ihc_country']) ? $_GET['ihc_country'] : '';
				$state = (isset($_GET['ihc_state'])) ? $_GET['ihc_state'] : '';
				$taxes_price = ihc_get_taxes_for_amount_by_country($country, $state, $level_arr['access_trial_price']);
				if ($taxes_price && !empty($taxes_price['total'])){
					$level_arr['access_trial_price'] += $taxes_price['total'];
				}				
		
				$subscription_data['trialPeriod'] = TRUE;
				if ($level_arr['access_trial_type']==1){
					//certain period
					$unit = 'day';
					switch ($level_arr['access_trial_time_type']){
						case 'D':
							$unit = 'day';
							$trial_value = $level_arr['access_trial_time_value'];
							break;
						case 'W':
							$unit = 'day';
							$trial_value = $level_arr['access_trial_time_value'] * 7;
							break;
						case 'M':
							$unit = 'month';
							$trial_value = $level_arr['access_trial_time_value'];
							break;
						case 'Y':
							$unit = 'month';
							$trial_value = $level_arr['access_trial_time_value'] * 12;
							break;
					}
					$subscription_data['trialDuration'] = $trial_value;
					$subscription_data['trialDurationUnit'] = $unit;				
				} else {
					//couple of circles 
					$subscription_data['trialDurationUnit'] = $level_arr['access_regular_time_type'];
					$subscription_data['trialDuration'] = $level_arr['access_regular_time_value'] * $level_arr['access_trial_couple_cycles'];			
				}					
			}
			//end of trial
			
			/// TAXES
			$country = isset($_GET['ihc_country']) ? $_GET['ihc_country'] : '';
			$state = (isset($_GET['ihc_state'])) ? $_GET['ihc_state'] : '';
			$taxes_price = ihc_get_taxes_for_amount_by_country($country, $state, $level_arr['price']);
			if ($taxes_price && !empty($taxes_price['total'])){
				$level_arr['price'] += $taxes_price['total'];
			}
			/// TAXES		
			
			if (isset($level_arr['billing_limit_num'])){
				$rec = (int)$level_arr['billing_limit_num'];
			} else {
				$rec = 100;
			}				
			$subscription_data['numberOfBillingCycles'] = $rec;
			
		} else {
			///////////////////// SINGLE PAYMENT
			/// COUPON
			if ($coupon_data){
				$amount = ihc_coupon_return_price_after_decrease($amount, $coupon_data);
			}
			/// TAXES
			$state = (isset($input['ihc_state'])) ? $input['ihc_state'] : '';
			$country = isset($input['ihc_country']) ? $input['ihc_country'] : '';
			$taxes_price = ihc_get_taxes_for_amount_by_country($country, $state, $amount);
			if ($taxes_price && !empty($taxes_price['total'])){
				$amount += $taxes_price['total'];
			}			
	
		}
	
		$this->do_auth(); ///include braintree libs
		
		if (isset($input['ihc_braintree_card_expire_month']) && isset($input['ihc_braintree_card_expire_year'])){
			$expire = $input['ihc_braintree_card_expire_month'] . '/' . $input['ihc_braintree_card_expire_year'];			
		} else {
			$expire = '';
		}
		$customer_arr = array(
							'firstName' => $input['ihc_braintree_first_name'],
							'lastName' => $input['ihc_braintree_last_name'],
							'creditCard' => array(
								'number' => $input['ihc_braintree_card_number'],
								'expirationDate' => $expire,
								'cvv' => $input['ihc_braintree_cvv'],
								'cardholderName' => $input['ihc_braintree_first_name'] . ' ' . $input['ihc_braintree_last_name'],
							),
							//'customFields' => json_encode(array('lid'=>$input['lid'], 'uid'=>$input['uid'])),
		);
		$result = Braintree_Customer::create($customer_arr);
		
		if (empty($subscription_data)){
			/// SINGLE PAYMENT
			if (!empty($result->customer) && !empty($result->customer->id)){
				$response = Braintree_Transaction::sale(array(
				  	'amount' => $amount,
				  	'customerId' => $result->customer->id			  
				));		
				$transaction_id = $response->transaction->id;///store this id
			}
			if ($response->success){
				$transaction_status = 'pending';
				$response = Braintree_Transaction::submitForSettlement($transaction_id);
				if ($response->success){
					$transaction_status = 'success';
				}			
			} else {
				$transaction_status = 'error';
			}		
		} else {
			/// RECURRING
			$subscription_data['paymentMethodToken'] = $result->customer->creditCards[0]->token;	
			$subscription_data['planId'] = $level_arr['name'];
			$subscription_data['price'] = $amount;
			$subscription_result = Braintree_Subscription::create($subscription_data);							
			if ($subscription_result->success){
				if (isset($subscription_result->subscription) && isset($subscription_result->subscription->id)){
					$transaction_id = $subscription_result->subscription->id;
					$transaction_status = 'pending';			
				}
			} else {
				$transaction_status = 'error';
			}			
		}
		
		/// SAVE TRANSACTION, UPDATE LID 
		if (isset($transaction_id)){
			$dont_save_order = TRUE;	
			$currency = get_option('ihc_currency');
			
			$transaction_info = array(
										'lid' => $input['lid'],
										'uid' => $input['uid'],
										'ihc_payment_type' => 'braintree',
										'amount' => $amount,
										'message' => $transaction_status,
										'currency' => $currency,
										'item_name' => $level_arr['name'],
			);
			ihc_insert_update_transaction($input['uid'], $transaction_id, $transaction_info); /// will save the order too
							
			/// SET LEVEL EXPIRe FOR SINGLE PAYMENT
			if ('success'==$transaction_status && empty($subscription_data)){
				/// set level expire for non recurring levels
				ihc_update_user_level_expire($level_arr, $input['lid'], $input['uid']);///update lid time
				ihc_switch_role_for_user($input['uid']);
			} else if (!empty($subscription_data['trialDuration'])){
				/// SET LEVEL EXPIRE FOR RECURRINg WITH TRIAL PERIOD
				ihc_set_level_trial_time_for_no_pay($level_arr, $input['lid'], $input['uid']);
			}
			return TRUE;
		}
	}

	private function do_auth(){
		/*
		 * @param none
		 * @return none
		 */
		require_once IHC_PATH . 'classes/braintree/lib/Braintree.php';
		$meta = ihc_return_meta_arr('payment_braintree');
		if ($meta['ihc_braintree_sandbox']){
			Braintree_Configuration::environment('sandbox');			
		} else {
			Braintree_Configuration::environment('production');			
		}
		
		Braintree_Configuration::merchantId($meta['ihc_braintree_merchant_id']); // 'y8dbqs3rtqnqyprm'
		Braintree_Configuration::publicKey($meta['ihc_braintree_public_key']); //'ggmr6zdvbsdqjc7q'
		Braintree_Configuration::privateKey($meta['ihc_braintree_private_key']); // '6a2eccd5f8f3c4defaec5fe6ec2e2fb0'				
	}
	
	public function get_form(){
		/*
		 * @param none
		 * @return string
		 */
		$str = '';
		$months = array();
		for ($i=1; $i<13; $i++){
			$months[$i] = $i;
		}
		$y = date("Y");
		$payment_fields = array(
								1 => array(
											'name' => 'ihc_braintree_card_number',
											'type' => 'number',
											'label' => 'Card Number',
								),
								2 => array(
											'name' => 'ihc_braintree_card_expire_month',
											'type' => 'select',
											'label' => 'Expiration Month',
											'multiple_values' => $months,
											'value' => '',
								),
								3 => array(
											'name' => 'ihc_braintree_card_expire_year',
											'type' => 'number',
											'label' => 'Expiration Year',
											'min' => $y,
											'max' => 2099,
											'value' => $y,
								),								
								4 => array(
											'name' => 'ihc_braintree_cvv',
											'type' => 'number',
											'label' => 'CVV',
											'max' => 9999,
											'min' => 1,
								),											
								5 => array(
											'name' => 'ihc_braintree_first_name',
											'type' => 'text',
											'label' => 'First Name',
								),										
								6 => array(
											'name' => 'ihc_braintree_last_name',
											'type' => 'text',
											'label' => 'Last Name',
								),	
		);
		foreach ($payment_fields as $v){
				$str .= '<div class="iump-form-line-register">';
				$str .= '<label class="iump-labels-register">';
				$str .= '<span style="color: red;">*</span>';
				$str .= $v['label'];
				$str .= '</label>';
				
				$post_submited_value = (isset($_POST[$v['name']])) ? $_POST[$v['name']] : '';	
				$temp_arr = $v;
				$temp_arr['value'] = $post_submited_value;			
				$str .= indeed_create_form_element($temp_arr);
				if (isset($v['sublabel']) && $v['sublabel'] != '')
					$str .= '<span class="iump-sublabel-register">'.$v['sublabel'].'</span>';
				$str .= '</div>';	 
				unset($temp_arr);
		}

		global $ihc_pay_error;
		if (!empty($ihc_pay_error['braintree'])){
			if (!empty($ihc_pay_error['braintree']['not_empty'])){
				$str .= '<div class="ihc-register-notice">' . $ihc_pay_error['braintree']['not_empty'] . '</div>';		
			}
			if (!empty($ihc_pay_error['braintree']['wrong_expiration'])){
				$str .= '<div class="ihc-register-notice">' . $ihc_pay_error['braintree']['wrong_expiration'] . '</div>';		
			}
			if (!empty($ihc_pay_error['braintree']['invalid_card'])){
				$str .= '<div class="ihc-register-notice">' . $ihc_pay_error['braintree']['invalid_card'] . '</div>';		
			}
			if (!empty($ihc_pay_error['braintree']['invalid_first_name'])){
				$str .= '<div class="ihc-register-notice">' . $ihc_pay_error['braintree']['invalid_first_name'] . '</div>';		
			}
			if (!empty($ihc_pay_error['braintree']['invalid_last_name'])){
				$str .= '<div class="ihc-register-notice">' . $ihc_pay_error['braintree']['invalid_last_name'] . '</div>';		
			}														
		}		
		return $str;		 
	}

	
}

endif;

