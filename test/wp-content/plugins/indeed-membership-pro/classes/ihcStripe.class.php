<?php 
if(!class_exists('ihcStripe')){
	class ihcStripe{
		private $publishable_key = FALSE;
		private $secret_key = FALSE;
		private $level_data = array();
		private $currency = 'USD';
		
		public function __construct(){
			//set keys
			$this->publishable_key = get_option('ihc_stripe_publishable_key'); 
			$this->secret_key = get_option('ihc_stripe_secret_key');
			$this->level_data = get_option('ihc_levels');
			$this->currency = get_option('ihc_currency');
			
			//load stripe libs
			require_once IHC_PATH . 'classes/stripe/init.php';
			\Stripe\Stripe::setApiKey($this->secret_key);
		}
		
		public function payment_fields($level_id, $bind=TRUE){
			if (isset($this->level_data[$level_id])){
				$amount = $this->level_data[$level_id]['price']*100;
				if ($amount<50){
					$amount = 50;
				}
				$str = '
				<script src="https://checkout.stripe.com/checkout.js"></script>
				<script>
				var iump_stripe = StripeCheckout.configure({
					key: "' . $this->publishable_key . '",
					locale: "auto",
					token: function(response) {
						var input = jQuery("<input type=hidden name=stripeToken id=stripeToken />").val(response.id);
						var email = jQuery("<input type=hidden name=stripeEmail id=stripeEmail />").val(response.email);
						jQuery(".ihc-form-create-edit").append(input);
						jQuery(".ihc-form-create-edit").append(email);
						jQuery(".ihc-form-create-edit").submit();
					}
				});
				';

				if ($bind){
					$str .= '
								jQuery(document).ready(function(){			
										
									jQuery("#ihc_submit_bttn").bind("click", function(e){
										e.preventDefault();
										if (jQuery("#stripeToken").val() && jQuery("#stripeEmail").val()){
											jQuery(".ihc-form-create-edit").submit();
											return true;
										}
										var p = jQuery("#iumpfinalglobalp").val();
										p = p * 100;
										if (p<50){
											p = 50;
										}
										iump_stripe.open({
														name: jQuery("#iumpfinalglobal_ll").val(),
														description: jQuery("#iumpfinalglobal_ll").val(),
														amount: p,
														currency: jQuery("#iumpfinalglobalc").val(),															
										});		
									});						
									
								});					
							';					
				}
				
				$str .= "</script>";

				return $str;				
			}
		}
		
		public function charge($post_data, $insert_order=FALSE){
			/*
			 * @param array
			 * @return array
			 */
			if (isset($this->level_data[$post_data['lid']])){
				$order_extra_metas = array();
				$reccurrence = FALSE;
				if (isset($this->level_data[$post_data['lid']]['access_type']) && $this->level_data[$post_data['lid']]['access_type']=='regular_period'){
					$reccurrence = TRUE;
				}
								
				//DISCOUNT
				if (!empty($post_data['ihc_coupon'])){
					$coupon_data = ihc_check_coupon($post_data['ihc_coupon'], $post_data['lid']);
					if ($coupon_data && (!empty($coupon_data['reccuring']) || !$reccurrence)){
						//available only for single payment or discount on all reccuring payments
						$order_extra_metas['discount_value'] = ihc_get_discount_value($this->level_data[$post_data['lid']]['price'], $coupon_data);
						$this->level_data[$post_data['lid']]['price'] = ihc_coupon_return_price_after_decrease($this->level_data[$post_data['lid']]['price'], $coupon_data);
					}
					
				}
				
				$amount = $this->level_data[$post_data['lid']]['price'];
	
				$amount = $amount * 100;
				if ($amount<50){
					$amount = 50;// 0.50 cents minimum amount for stripe transactions
				}
				
				/// TAXES
				$state = (isset($post_data['ihc_state'])) ? $post_data['ihc_state'] : '';
				$country = isset($post_data['ihc_country']) ? $post_data['ihc_country'] : '';
				$taxes_data = ihc_get_taxes_for_amount_by_country($country, $state, $amount);
				if ($taxes_data && !empty($taxes_data['total'])){
					$amount += $taxes_data['total'];
					$order_extra_metas['tax_value'] = $taxes_data['total'];
				}	
									
				$amount = round($amount);
				
				$customer_arr = array(
						'email' => $post_data['stripeEmail'],
						'card'  => $post_data['stripeToken'],
				);

				
				if ($reccurrence){
					$ihc_plan_code = 'ihc_plan_' . rand(1,10000);
					switch ($this->level_data[$post_data['lid']]['access_regular_time_type']){
						case 'D':
							$this->level_data[$post_data['lid']]['access_regular_time_type'] = 'day';
							break;
						case 'W':
							$this->level_data[$post_data['lid']]['access_regular_time_type'] = 'week';
							break;
						case 'M':
							$this->level_data[$post_data['lid']]['access_regular_time_type'] = 'month';
							break;
						case 'Y':
							$this->level_data[$post_data['lid']]['access_regular_time_type'] = 'year';
							break;
					}

					///trial
					$trial_period_days = 0;
					if (!empty($this->level_data[$post_data['lid']]['access_trial_type'])){
						if ($this->level_data[$post_data['lid']]['access_trial_type']==1 && isset($this->level_data[$post_data['lid']]['access_trial_time_value']) 
								&& $this->level_data[$post_data['lid']]['access_trial_time_value'] !=''){
							switch ($this->level_data[$post_data['lid']]['access_trial_time_type']){
								case 'D':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_trial_time_value'];
									break;
								case 'W':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_trial_time_value'] * 7; 
									break;
								case 'M':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_trial_time_value'] * 31;
									break;
								case 'Y':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_trial_time_value'] * 365;
									break;
							}
						} else if ($this->level_data[$post_data['lid']]['access_trial_type']==2 && isset($this->level_data[$post_data['lid']]['access_trial_couple_cycles']) 
									&& $this->level_data[$post_data['lid']]['access_trial_couple_cycles']!=''){
							switch ($this->level_data[$post_data['lid']]['access_regular_time_type']){
								case 'day':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value'] * $this->level_data[$post_data['lid']]['access_trial_couple_cycles'];
									break;
								case 'week':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value'] * $this->level_data[$post_data['lid']]['access_trial_couple_cycles'] * 7;
									break;
								case 'month':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value'] * $this->level_data[$post_data['lid']]['access_trial_couple_cycles'] * 31;
									break;
								case 'year':
									$trial_period_days = $this->level_data[$post_data['lid']]['access_regular_time_value'] * $this->level_data[$post_data['lid']]['access_trial_couple_cycles'] * 365;
									break;								
							}							
						}
					}
					//end of trial
					$plan = array(
							"amount" => $amount,
							"interval_count" => $this->level_data[$post_data['lid']]['access_regular_time_value'],
							"interval" => $this->level_data[$post_data['lid']]['access_regular_time_type'],							
							"name" => "Reccuring for " . $post_data['lid'],
							"currency" => $this->currency,
							"id" => $ihc_plan_code,	
							//"trial_period_days" => $trial_period_days,						
					);
					
					if (!empty($trial_period_days)){
						$plan['trial_period_days'] = $trial_period_days;
					}
					
					$return_data_plan = \Stripe\Plan::create($plan);
					$customer_arr['plan'] = $ihc_plan_code;
				}//end of reccurence
				
				$customer = \Stripe\Customer::create($customer_arr);
					
				
				$sub_id = '';
				if ($reccurrence){
					/// RECCURRING PAYMENT
					$plan = \Stripe\Plan::retrieve($ihc_plan_code);			
					$plan->delete();//delete the plan					
					if ( isset($customer->subscriptions->data[0]->id)){
						$sub_id = $customer->subscriptions->data[0]->id;
					}	
					
					/// DONATION, DOUBLE CHARGE
					/*
					if (!empty($donation_amount)){
						$charge = \Stripe\Charge::create(array(
							'customer' => $customer->id,
							'amount'   => $donation_amount,
							'currency' => $this->currency,
							'description' => serialize(array('is_donation'=>TRUE)),
						));		
					}
					*/
					/// DONATION
					
				} else {
					/// SINGLE PAYMENT
					$charge = \Stripe\Charge::create(array(
							'customer' => $customer->id,
							'amount'   => $amount,
							'currency' => $this->currency,
					));				
				}
				
				$amount = $amount/100;
				$response_return = array(
						'amount' => urlencode($amount),
						'currency' => $this->currency,
						'level' => $post_data['lid'],
						'item_name' => $this->level_data[$post_data['lid']]['name'],
						'customer' => $customer->id,
				);
				if ($sub_id){
					$response_return['subscription'] = $sub_id;
				}
					
				if (!empty($insert_order)){
					ihc_insert_update_order($post_data['uid'], $post_data['lid'], $amount, 'pending', 'stripe', $order_extra_metas);		
				}	
					
				if ($reccurrence && isset($customer->id)){
					//$response_return['message'] = "success";
					$response_return['message'] = "pending";
					$response_return['trans_id'] = $customer->id;
				} else if (!empty($charge) && $charge->paid) {
					//$response_return['message'] = "success";
					$response_return['message'] = "pending";
					$response_return['trans_id'] = $charge->customer;
				} else {
					$response_return['message'] = "error";
				}				
				
				return $response_return;
			}
		}
		
		public function cancel_subscription($transaction_id=''){
			/*
			 * @param string (txn_id)
			 * @return none
			 */	
			if ($transaction_id){
				global $wpdb;
				$data = $wpdb->get_row("SELECT payment_data FROM " . $wpdb->prefix . "indeed_members_payments WHERE txn_id='" . $transaction_id . "';");
				$arr = json_decode($data->payment_data, TRUE);
				if (!empty($arr['customer']) && !empty($arr['subscription'])){
					$customer = \Stripe\Customer::retrieve($arr['customer']);
					if ($customer){

						if (isset($customer->subscriptions) && !empty($customer->subscriptions->data)){
							foreach ($customer->subscriptions->data as $k=>$temp_obj){									
								if (!empty($temp_obj->id) && $temp_obj->id==$arr['subscription']){
									$it_exists = TRUE;
									break;
								}
							}
							if ($it_exists){
								@$subscription = $customer->subscriptions->retrieve($arr['subscription']);
								if (!empty($subscription)){
									try {
										@$value = $subscription->cancel();							
									} catch (Stripe\Error\InvalidRequest $e){}
									return $value;	
								}									
							}
						}
												
					}			
				} 					
			}		
		}

	}//end of class ihcStripe
	
}