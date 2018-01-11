<?php
require_once '../../../../wp-load.php';
require_once IHC_PATH . 'utilities.php';
	
$gateway_metas = ihc_return_meta_arr('payment_payza');
$currency = get_option('ihc_currency');
$levels = get_option('ihc_levels');
	
$return_url = get_option('page_on_front');
$return_url = get_permalink($return_url);
if (!$return_url){
	$return_url = get_home_url();
}

$error = false;	
//LEVEL
if (isset($levels[$_GET['lid']])){
	$level_arr = $levels[$_GET['lid']];
	if ($level_arr['payment_type']=='free' || empty($level_arr['price'])){
		$error = true;
	} 
} else {
	$error = true;
}
// USER ID
if (isset($_GET['uid']) && $_GET['uid']){
	$uid = $_GET['uid'];
} else {
	$uid = get_current_user_id();
}
if (empty($uid)){
	$error = true;	
}
		
if ($error){
	////if level it's not available for some reason, go back to prev page
	header( 'location:' . $return_url );
	exit();
}

$notify_url = str_replace('public/', 'paypal_ipn.php', plugin_dir_url(__FILE__));
$reccurrence = FALSE;
if (isset($level_arr['access_type']) && $level_arr['access_type']=='regular_period'){
	$reccurrence = TRUE;
}


$q = 'ap_merchant=' . urlencode($gateway_metas['ihc_payza_email']);
$q .= '&ap_itemname=' . urlencode($level_arr['label']);
$q .= '&ap_currency=' . $currency;
$q .= '&ap_returnurl=' . urlencode($return_url);
$q .= '&ap_cancelurl=' . urlencode($return_url);			
$q .= '&ap_quantity=1';
$q .= '&ap_itemcode=' . urlencode($level_arr['name']);
$q .= '&apc_1=' . json_encode(array('user_id' => $uid, 'level_id' => $_GET['lid']));


//coupons
$coupon_data = array();
if (!empty($_GET['ihc_coupon'])){
	$coupon_data = ihc_check_coupon($_GET['ihc_coupon'], $_GET['lid']);
}



/// NOW LETS SET THE AMOUNT
	if (isset($level_arr['access_type']) && $level_arr['access_type']=='regular_period'){
		$q .= '&ap_purchasetype=subscription';
		//====================RECCURENCE
		//coupon on reccurence
		if ($coupon_data){
			if (!empty($coupon_data['reccuring'])){
				//everytime the price will be reduced
				$level_arr['price'] = ihc_coupon_return_price_after_decrease($level_arr['price'], $coupon_data);
				if (isset($level_arr['access_trial_price'])){
					$level_arr['access_trial_price'] = ihc_coupon_return_price_after_decrease($level_arr['access_trial_price'], $coupon_data, FALSE); 
				}
			} else {
				//only one time
				if (!empty($level_arr['access_trial_price'])){
					$level_arr['access_trial_price'] = ihc_coupon_return_price_after_decrease($level_arr['access_trial_price'], $coupon_data);
				} else {
					$level_arr['access_trial_price'] = ihc_coupon_return_price_after_decrease($level_arr['price'], $coupon_data);
					$level_arr['access_trial_type'] = 2;
				}
				if (empty($level_arr['access_trial_type'])){
					$level_arr['access_trial_type'] = 2;
				}
			}
		}
				
		//trial block
		if (!empty($level_arr['access_trial_type']) && isset($level_arr['access_trial_price']) && $level_arr['access_trial_price']!=''){
			/// TAXES
			$state = (isset($_GET['ihc_state'])) ? $_GET['ihc_state'] : '';
			$country = isset($_GET['ihc_country']) ? $_GET['ihc_country'] : '';
			$taxes_price = ihc_get_taxes_for_amount_by_country($country, $state, $level_arr['access_trial_price']);
			if ($taxes_price && !empty($taxes_price['total'])){
				$level_arr['access_trial_price'] += $taxes_price['total'];
			}
		
			$q .= '&ap_trialamount=' . urlencode($level_arr['access_trial_price']);//price
			if ($level_arr['access_trial_type']==1){
				//certain period
				$unit = ihc_timeunit_for_payza($level_arr['access_trial_time_type']);
				$q .= '&ap_trialtimeunit=' . $unit;//type of time
				$q .= '&ap_trialperiodlength=' . urlencode($level_arr['access_trial_time_value']);// time value				
			} else {
				//one subscription 	
				$unit = ihc_timeunit_for_payza($level_arr['access_regular_time_type']);
				$q .= '&ap_trialtimeunit=' . $unit;//type of time
				$q .= '&ap_trialperiodlength=' . ($level_arr['access_regular_time_value'] * $level_arr['access_trial_couple_cycles']);//time value		
			}
			$trial = TRUE;
		}
		//end of trial

		/// TAXES
		$state = (isset($_GET['ihc_state'])) ? $_GET['ihc_state'] : '';
		$country = isset($_GET['ihc_country']) ? $_GET['ihc_country'] : '';
		$taxes_price = ihc_get_taxes_for_amount_by_country($country, $state, $level_arr['price']);
		if ($taxes_price && !empty($taxes_price['total'])){
			$level_arr['price'] += $taxes_price['total'];
		}				
		
		$unit = ihc_timeunit_for_payza($level_arr['access_regular_time_type']);
 
		$q .= '&ap_amount=' . urlencode($level_arr['price']);
		$q .= '&ap_timeunit=' . $unit;
		$q .= '&ap_periodlength=' . $level_arr['access_regular_time_value'];
		if ($level_arr['billing_type']=='bl_ongoing'){
			$rec = 0;///no limit
		} else {
			if (isset($level_arr['billing_limit_num'])){
				$rec = (int)$level_arr['billing_limit_num'];
			} else {
				$rec = 0;///no limit
			}			
		}
		$q .= '&ap_periodcount=' . $rec;//num of rec

	} else {
		//====================== single payment
		$q .= '&ap_purchasetype=service';		
		//coupon
		if ($coupon_data){
			$level_arr['price'] = ihc_coupon_return_price_after_decrease($level_arr['price'], $coupon_data);
		}
		/// TAXES
		$state = (isset($_GET['ihc_state'])) ? $_GET['ihc_state'] : '';
		$country = isset($_GET['ihc_country']) ? $_GET['ihc_country'] : '';
		$taxes_price = ihc_get_taxes_for_amount_by_country($country, $state, $level_arr['price']);
		if ($taxes_price && !empty($taxes_price['total'])){
			$level_arr['price'] += $taxes_price['total'];
		}			

		$q .= '&ap_amount=' . urlencode($level_arr['price']);
	}	

/// REDIRECT TO PAYZA
$url = 'https://secure.payza.com/checkout' . '?' . $q;
header("Location: " . $url );
exit;
