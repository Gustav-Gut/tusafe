<?php 
if (empty($no_load)){
	require_once ABSPATH . 'wp-load.php';
}

if (get_option('ihc_debug_payments_db')){
	ihc_insert_debug_payment_log('twocheckout', $_POST);
}

//set payment type
$_POST['ihc_payment_type'] = 'twocheckout';


if (isset($_POST['message_type'])){
	//we've got md5_hash
	if (isset($_POST['md5_hash'])) {
		# Validate the Hash
		$hashSecretWord = get_option('ihc_twocheckout_secret_word'); # Input your secret word
		$hashSid = $_POST['vendor_id'];
		$hashOrder = $_POST['sale_id'];
		$hashInvoice = $_POST['invoice_id'];
		$StringToHash = strtoupper(md5($hashOrder . $hashSid . $hashInvoice . $secretWord));
	
		if ($StringToHash == $_POST['md5_hash']) {
			$data = json_decode(stripslashes($_POST['li_0_description']), TRUE);
			switch ($_POST['message_type']) {
				case 'ORDER_CREATED':
				case 'RECURRING_INSTALLMENT_SUCCESS':
					# Do something when sale passes fraud review.
					if (isset($data['u_id']) && isset($data['l_id'])){
						$_POST['level'] = $data['l_id'];
						$_POST['message'] = 'success';
						$level_data = ihc_get_level_by_id($data['l_id']);//getting details about current level
						ihc_update_user_level_expire($level_data, $data['l_id'], $data['u_id']);
						ihc_insert_update_transaction($data['u_id'], $_POST['sale_id'], $_POST);
						ihc_switch_role_for_user($data['u_id']);
					}
					break;
				case 'RECURRING_INSTALLMENT_FAILED':
					# Do something when sale fails fraud review.
					if (!function_exists('ihc_is_user_level_expired')){
						require_once IHC_PATH . 'public/functions.php';
					}
					$expired = ihc_is_user_level_expired($data['u_id'], $data['l_id'], FALSE, TRUE);
					if ($expired){
						//delete user - level relationship
						ihc_delete_user_level_relation($data['l_id'], $data['u_id']);
					}					
					break;
			}			
		}
	}	
} else if (isset($_POST['key'])){
	$hashSecretWord = get_option('ihc_twocheckout_secret_word'); # Input your secret word
	$hashSid = get_option('ihc_twocheckout_account_number'); #Input your seller ID (2Checkout account number)
	$hashTotal = $_POST['total']; //Sale total to validate against

	if (!empty($_POST['demo']) && $_POST['demo']=='Y'){
		$hashOrder = 1;
	} else {
		$hashOrder = $_POST['order_number'];
	}		
	
	$StringToHash = strtoupper(md5($hashSecretWord . $hashSid . $hashOrder . $hashTotal));
	if ($StringToHash == $_POST['key']) {
		$data = json_decode(stripslashes($_POST['li_0_description']), TRUE);
		if (isset($data['u_id']) && isset($data['l_id'])){
			$_POST['level'] = $data['l_id'];
			$_POST['message'] = 'success';
			$level_data = ihc_get_level_by_id($data['l_id']);//getting details about current level
			ihc_update_user_level_expire($level_data, $data['l_id'], $data['u_id']);
			ihc_insert_update_transaction($data['u_id'], $_POST['order_number'], $_POST);
			ihc_switch_role_for_user($data['u_id']);
		}
	}
}
//debug
//file_put_contents( "twocheckout_log.log", json_encode($_POST), FILE_APPEND | LOCK_EX );

wp_redirect(get_home_url());
exit();