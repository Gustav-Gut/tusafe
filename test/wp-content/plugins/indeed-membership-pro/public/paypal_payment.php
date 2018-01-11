<?php 
	require '../../../../wp-load.php';
	
	$paypal_email = get_option('ihc_paypal_email');
	$currency = get_option('ihc_currency');
	$levels = get_option('ihc_levels');
	$sandbox = get_option('ihc_paypal_sandbox');
	$r_url = get_option('ihc_paypal_return_page');
	
	if(!$r_url || $r_url==-1){
		$r_url = get_option('page_on_front');
	}
	$r_url = get_permalink($r_url);
	if (!$r_url){
		$r_url = get_home_url();
	}
	
	if ($sandbox){
		$url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	} else{
		$url = 'https://www.paypal.com/cgi-bin/webscr';
	}
		
	$err = false;	
	//LEVEL
	if (isset($levels[$_GET['lid']])){
		$level_arr = $levels[$_GET['lid']];
		if ($level_arr['payment_type']=='free' || $level_arr['price']=='') $err = true;
	} else {
		$err = true;
	}
	// USER ID
	if (isset($_GET['uid']) && $_GET['uid']){
		$uid = $_GET['uid'];
	} else {
		$uid = get_current_user_id();
	}
	if (!$uid){
		$err = true;	
	}
		
	if ($err){
		////if level it's not available for some reason, go back to prev page
		header( 'location:'. $r_url );
		exit();
	} else {
		$custom_data = json_encode(array('user_id' => $uid, 'level_id' => $_GET['lid']));
	}
	
	//$notify_url = str_replace('public/', 'paypal_ipn.php', plugin_dir_url(__FILE__));
	$site_url = site_url();
	$site_url = trailingslashit($site_url);
	$notify_url = add_query_arg('ihc_action', 'paypal', $site_url);
	
	$reccurrence = FALSE;
	if (isset($level_arr['access_type']) && $level_arr['access_type']=='regular_period'){
		$reccurrence = TRUE;
	}
	

	$q = '?';
	if ($reccurrence){
		$q .= 'cmd=_xclick-subscriptions&';
	} else {
		$q .= 'cmd=_xclick&';
	}
	
	$q .= 'business=' . urlencode($paypal_email) . '&';
	$q .= 'item_name=' . urlencode($level_arr['name']) . '&';
	$q .= 'currency_code=' . $currency . '&';
	
	//coupons
	$coupon_data = array();
	if (!empty($_GET['ihc_coupon'])){
		$coupon_data = ihc_check_coupon($_GET['ihc_coupon'], $_GET['lid']);
	}
	
	if ($reccurrence){
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
			$country = (isset($_GET['ihc_country'])) ? $_GET['ihc_country'] : '';
			$state = (isset($_GET['ihc_state'])) ? $_GET['ihc_state'] : '';	
			$taxes_price = ihc_get_taxes_for_amount_by_country($country, $state, $level_arr['access_trial_price']);
			if ($taxes_price && !empty($taxes_price['total'])){
				$level_arr['access_trial_price'] += $taxes_price['total'];
			}			
			
			$q .= 'a1=' . urlencode($level_arr['access_trial_price']) . '&';//price
			if ($level_arr['access_trial_type']==1){
				//certain period
				$q .= 't1=' . urlencode($level_arr['access_trial_time_type']) . '&';//type of time
				$q .= 'p1=' . urlencode($level_arr['access_trial_time_value']) . '&';// time value				
			} else {
				//one subscription 
				$q .= 't1=' . $level_arr['access_regular_time_type'] . '&';//type of time
				$q .= 'p1=' . $level_arr['access_regular_time_value'] . '&';//time value			
			}
			$trial = TRUE;
		}
		//end of trial

		/// TAXES
		$country = (isset($_GET['ihc_country'])) ? $_GET['ihc_country'] : '';
		$state = (isset($_GET['ihc_state'])) ? $_GET['ihc_state'] : '';	
		$taxes_price = ihc_get_taxes_for_amount_by_country($country, $state, $level_arr['price']);
		if ($taxes_price && !empty($taxes_price['total'])){
			$level_arr['price'] += $taxes_price['total'];
		}			
 
		$q .= 'a3=' . urlencode($level_arr['price']) . '&';
		$q .= 't3=' . $level_arr['access_regular_time_type'] . '&';
		$q .= 'p3=' . $level_arr['access_regular_time_value'] . '&';
		$q .= 'src=1&';//set the rec
		if ($level_arr['billing_type']=='bl_ongoing'){
			//$rec = 52;
			$rec = 0;
		} else {
			if (isset($level_arr['billing_limit_num'])){
				$rec = (int)$level_arr['billing_limit_num'];
			} else {
				$rec = 52;
			}			
		}
		$q .= 'srt='.$rec.'&';//num of rec
		$q .= 'no_note=1&';
		if (!empty($trial)){
			$q .= 'modify=0&';
		} else {
			$q .= 'modify=1&';
		}
	} else {
		//====================== single payment
		
		//coupon
		if ($coupon_data){
			$level_arr['price'] = ihc_coupon_return_price_after_decrease($level_arr['price'], $coupon_data);
		}
		
		/// TAXES
		$country = (isset($_GET['ihc_country'])) ? $_GET['ihc_country'] : '';
		$state = (isset($_GET['ihc_state'])) ? $_GET['ihc_state'] : '';		
		$taxes_price = ihc_get_taxes_for_amount_by_country($country, $state, $level_arr['price']);
		if ($taxes_price && !empty($taxes_price['total'])){
			$level_arr['price'] += $taxes_price['total'];
		}						
		
		$q .= 'amount=' . urlencode($level_arr['price']) . '&';
		$q .= 'paymentaction=sale&';
	}	
	$q .= 'lc=EN_US&';
	$q .= 'return=' . urlencode($r_url) . '&';
	$q .= 'cancel_return=' . urlencode($r_url) . '&';
	$q .= 'notify_url=' . urlencode($notify_url) . '&';
	$q .= 'rm=2&';
	$q .= 'no_shipping=1&';
	$q .= 'custom=' . $custom_data;	

	header( 'location:' . $url . $q );
	exit();
	
	