<?php
if (empty($no_load)){
	require_once '../../../wp-load.php';
	require_once 'utilities.php';	
}

ini_set('display_errors','on');

//insert this request into debug payments table
if (get_option('ihc_debug_payments_db')){
	ihc_insert_debug_payment_log('paypal', $_POST);
}

//file_put_contents( IHC_PATH . "paypal_log.log", serialize($_POST), FILE_APPEND );

if ( ( isset($_POST['payment_status']) || isset($_POST['txn_type']) ) && isset($_POST['custom']) ){

	$debug = FALSE;	
	$path = str_replace('paypal_ipn.php', '', __FILE__);
	$log_file = $path . 'paypal.log';
	$raw_post_data = file_get_contents('php://input');
	$raw_post_array = explode('&', $raw_post_data);
	$myPost = array();
	foreach ($raw_post_array as $keyval) {
		$keyval = explode ('=', $keyval);
		if (count($keyval) == 2)
			$myPost[$keyval[0]] = urldecode($keyval[1]);
	}
	// read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-validate';
	if (function_exists('get_magic_quotes_gpc')) {
		$get_magic_quotes_exists = true;
	}
	foreach ($myPost as $key => $value) {
		if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
			$value = urlencode(stripslashes($value));
		} else {
			$value = urlencode($value);
		}
		$req .= "&$key=$value";
	}
	// Post IPN data back to PayPal to validate the IPN data is genuine
	// Without this step anyone can fake IPN data
	$sandbox = get_option('ihc_paypal_sandbox');
	if ($sandbox){
		$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	} else {
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
	}
	
	$ch = curl_init($paypal_url);
	if ($ch == FALSE) {
		
		if ($debug) {
			error_log(date('[Y-m-d H:i e] '). "No CURL Enabled on this server ", 3, $log_file);
		}
		echo "No CURL Enabled on this server ";
		
		exit();
	}
	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	if ($debug) {
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
	}
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close', 'User-Agent: membership-pro'));
	$res = curl_exec($ch);
	if (curl_errno($ch) != 0){ // cURL error
		if ($debug) {
			error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, $log_file);
		}
		curl_close($ch);
		
		echo date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch);
		
		exit;
	} else {
		//Log the entire HTTP response if debug is switched on.
		if ($debug) {
			error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, $log_file );
			error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, $log_file);
		}
		curl_close($ch);
	}
	// Inspect IPN validation result and act accordingly
	// Split response headers and payload, a better way for strcmp
	$tokens = explode("\r\n\r\n", trim($res));
	$res = trim(end($tokens));	
	
	if (strcmp ($res, "VERIFIED") == 0) {	
		if (isset($_POST['custom'])){
			$data = stripslashes($_POST['custom']);
			$data = json_decode($data, true);
			$level_data = ihc_get_level_by_id($data['level_id']);//getting details about current level
		}
		
		if (isset($_POST['payment_status'])){
			switch ($_POST['payment_status']){
				case 'Processed':
				case 'Completed':
					//payment made, put the right expire time
					$approved = TRUE;
					//if (get_option('ihc_paypal_email')!=$_POST['receiver_email']){
					//	$approved = FALSE;
					//}
					//if ($_POST['mc_currency']!=get_option('ihc_currency') ){
					//	$approved = FALSE;
					//}			
			
					if ($approved){
						ihc_update_user_level_expire($level_data, $data['level_id'], $data['user_id']);						
						ihc_send_user_notifications($data['user_id'], 'payment', $data['level_id']);//send notification to user
						ihc_send_user_notifications($data['user_id'], 'admin_user_payment', $data['level_id']);//send notification to admin
						ihc_switch_role_for_user($data['user_id']);
					}
				break;
				case 'Pending':
			
				break;
				case 'Reversed':
				case 'Refunded':
				case 'Denied':
					if (!function_exists('ihc_is_user_level_expired')){
						require_once IHC_PATH . 'public/functions.php';
					}
					$expired = ihc_is_user_level_expired($data['user_id'], $data['level_id'], FALSE, TRUE);
					if ($expired){
						//it's expired and we must delete user - level relationship
						ihc_delete_user_level_relation($data['level_id'], $data['user_id']);
					}					
				break;
			}
			if (isset($_POST['txn_id'])){
				//set payment type
				$_POST['ihc_payment_type'] = 'paypal';
				//record transation				
				
				ihc_insert_update_transaction($data['user_id'], $_POST['txn_id'], $_POST);
			}			
		}

		switch ($_POST['txn_type']) {
			case 'web_accept':
			case 'subscr_payment':
				
			break;
			
			case 'subscr_signup':
			case 'subscr_modify':
			
			break;
			
			case 'recurring_payment_profile_canceled':
			case 'recurring_payment_suspended':
			case 'recurring_payment_suspended_due_to_max_failed_payment':
			case 'recurring_payment_failed':
				if (!function_exists('ihc_is_user_level_expired')){
					require_once IHC_PATH . 'public/functions.php';
				}
				$expired = ihc_is_user_level_expired($data['user_id'], $data['level_id'], FALSE, TRUE);
				if ($expired){			
					//it's expired and we must delete user - level relationship
					ihc_delete_user_level_relation($data['level_id'], $data['user_id']);
				}
			break;
		}
	} else if (strcmp ($res, "INVALID") == 0) {
		///problems with connection
		if ($debug){
			error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, $log_file);
		}
		echo date('[Y-m-d H:i e] '). "Invalid IPN: $req";
	}
} else {
	echo '============= Ultimate Membership Pro - PAYPAL IPN ============= ';
	echo '<br/><br/>No Payments details sent. Come later';	

	//non PayPal tries to access this file
	//header('Status: 404 Not Found');
	//exit();	
}