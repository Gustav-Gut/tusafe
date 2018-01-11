<?php
if (empty($no_load)){
	require_once '../../../wp-load.php';
	require_once 'utilities.php';
}

if (!empty($_POST['token'])){
	define("IPN_V2_HANDLER", "https://secure.payza.com/ipn2.ashx");
	define("TOKEN_IDENTIFIER", "token=");
	$token = TOKEN_IDENTIFIER . urlencode($_POST['token']);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, IPN_V2_HANDLER);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $token);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch);
	curl_close($ch);
	
	if (strlen($response)>0){
		$response_decoded = urldecode($response);
		if ("INVALID TOKEN"==$response_decoded){
            exit;
        } else {
			$response_array = explode("&", $response_decoded);
			$ipn_data = array();
			foreach ($response_array as $value){
                $temp = explode("=", $value);
                $ipn_data[$temp[0]] = $temp[1];
            }
			if (!empty($ipn_data['apc_1'])){
				$custom_data = json_decode($ipn_data['apc_1'], TRUE);				
			} else {
				/// initialize the array, just to avoid errors
				$custom_data = array('user_id' => '', 'level_id' => '');
			}
			
			switch ($ipn_data['ap_status']){
				case 'Success':
				case 'Subscription-Payment-Success':
					$level_data = ihc_get_level_by_id($custom_data['level_id']);//getting details about current level
					ihc_update_user_level_expire($level_data, $custom_data['level_id'], $custom_data['user_id']);						
					ihc_send_user_notifications($custom_data['user_id'], 'payment', $custom_data['level_id']);//send notification to user
					ihc_send_user_notifications($custom_data['user_id'], 'admin_user_payment', $custom_data['level_id']);//send notification to admin
					ihc_switch_role_for_user($custom_data['user_id']);					
					break;
				case 'Subscription-Canceled':
				case 'Subscription-Payment-Failed':
				case 'Subscription-Expired':
				case 'Refunded':	
					if (!function_exists('ihc_is_user_level_expired')){
						require_once IHC_PATH . 'public/functions.php';
					}
					$expired = ihc_is_user_level_expired($custom_data['user_id'], $custom_data['level_id'], FALSE, TRUE);
					if ($expired){			
						//it's expired and we must delete user - level relationship
						ihc_delete_user_level_relation($custom_data['level_id'], $custom_data['user_id']);
					}					
					break;
			}
			if ($ipn_data['ap_status']=='Subscription-Expired'){
				exit(); 
			}
			$ipn_data['uid'] = @$custom_data['user_id'];
			$ipn_data['lid'] = @$custom_data['level_id'];
			$ipn_data['amount'] = @$ipn_data['ap_amount'];
			$ipn_data['ihc_payment_type'] = 'payza';
			$ipn_data['currency'] = $ipn_data['ap_currency'];
			ihc_insert_update_transaction($ipn_data['uid'], $ipn_data['ap_referencenumber'], $ipn_data);
			
			/// file_put_contents(IHC_PATH . 'log.log', serialize($ipn_data), FILE_APPEND);
		}
	}
}
