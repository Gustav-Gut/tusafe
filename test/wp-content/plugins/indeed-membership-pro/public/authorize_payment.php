<?php 
	require '../../../../wp-load.php';
	
	$loginID = get_option('ihc_authorize_login_id');
	$transactionKey = get_option('ihc_authorize_transaction_key');
	$currency = get_option('ihc_currency');
	$levels = get_option('ihc_levels');
	$sandbox = get_option('ihc_authorize_sandbox');

	$r_url = get_home_url();

	if ($sandbox){
		$url = 'https://test.authorize.net/gateway/transact.dll';
	} else{
		$url = 'https://secure.authorize.net/gateway/transact.dll';
	}
	$relay_url = str_replace('public/', 'authorize_response.php', plugin_dir_url(__FILE__));
		
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
	}
	
	$reccurrence = FALSE;
	if (isset($level_arr['access_type']) && $level_arr['access_type']=='regular_period'){
		$reccurrence = TRUE;
	}
	if ($reccurrence){
		///redirect to prev page
		header( 'location:'. $r_url );
		exit();		
		
	} else {
		/// COUPON
		if (!empty($_GET['ihc_coupon'])){
			$coupon_data = ihc_check_coupon($_GET['ihc_coupon'], $_GET['lid']);
			$level_arr['price'] = ihc_coupon_return_price_after_decrease($level_arr['price'], $coupon_data);
		}
		$amount = urlencode($level_arr['price']);
		///TAXES
		$state = (isset($_GET['ihc_state'])) ? $_GET['ihc_state'] : '';
		$country = isset($_GET['ihc_country']) ? $_GET['ihc_country'] : '';
		$taxes_data = ihc_get_taxes_for_amount_by_country($country, $state, $amount);
		if ($taxes_data && !empty($taxes_data['total'])){
			$taxes = $taxes_data['total'];
			$amount += $taxes;
		}
		
		$description 	= $level_arr['label'];
		$label 			= $level_arr['label'];
		// an invoice is generated using the date and time
		$invoice	= date('YmdHis');
		// a sequence number is randomly generated
		$sequence	= rand(1, 1000);
		// a timestamp is generated
		$timeStamp	= time();
		$testMode		= "false";
		
		if( phpversion() >= '5.1.2' )
			{ $fingerprint = hash_hmac("md5", $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^" . $currency, $transactionKey); }
		else 
			{ $fingerprint = bin2hex(mhash(MHASH_MD5, $loginID . "^" . $sequence . "^" . $timeStamp . "^" . $amount . "^". $currency, $transactionKey)); }
		
		/// <input type="hidden" name="x_relay_response" value="FALSE" />	
		/// <input type="hidden" name="x_type" value="AUTH_ONLY" />	
		?>
		<form method="post" action="<?php echo $url;?>" id="authorize_form">
			<input type="hidden" name="x_login" value="<?php echo $loginID;?>" />		
			<input type="hidden" name="x_amount" value="<?php echo $amount;?>" />		
			<input type="hidden" name="x_currency_code" value="<?php echo $currency;?>" />		
			<input type="hidden" name="x_type" value="AUTH_CAPTURE" />		
			<input type="hidden" name="x_description" value="<?php echo $description;?>" />		
			<input type="hidden" name="x_invoice_num" value="<?php echo $invoice;?>" />		
			<input type="hidden" name="x_fp_sequence" value="<?php echo $sequence;?>" />		
			<input type="hidden" name="x_fp_timestamp" value="<?php echo $timeStamp;?>" />		
			<input type="hidden" name="x_fp_hash" value="<?php echo $fingerprint;?>" />		
			<input type="hidden" name="x_relay_response" value="FALSE" />		
			<input type="hidden" name="x_relay_url" value="<?php echo $relay_url;?>" />		
			<input type="hidden" name="x_cust_id" value="<?php echo $uid;?>" />		
			<input type="hidden" name="x_po_num" value="<?php echo $_GET['lid'];?>" />		
			<input type="hidden" name="x_test_request" value="<?php echo $testMode;?>" />	
			<input type="hidden" name="x_show_form" value="PAYMENT_FORM" />		
		</form>	
		<script>
			document.forms[0].submit();
		</script>	
		<?php
		 
		/*
		$q = '?';
		$q .= 'x_login=' . $loginID . '&';
		$q .= 'x_amount=' . $amount . '&';
		$q .= 'x_currency_code=' . $currency . '&';
		$q .= 'x_type="AUTH_ONLY"&';
		$q .= 'x_description=' . $description . '&';
		$q .= 'x_invoice_num=' . $invoice . '&';
		$q .= 'x_fp_sequence=' . $sequence . '&';
		$q .= 'x_fp_timestamp=' . $timeStamp . '&';
		$q .= 'x_fp_hash=' . $fingerprint . '&';
		$q .= 'x_relay_response="TRUE"&';
		$q .= 'x_relay_url=' . $relay_url . '&';
		$q .= 'x_cust_id=' . $uid . '&';
		$q .= 'x_po_num=' . $_GET['lid'] . '&';
		$q .= 'x_test_request=' . $testMode . '&';
		$q .= 'x_show_form=PAYMENT_FORM';
		
		header( 'location:' . $url . $q );
		exit();
		*/
	}

/*	
header( 'location:'. $r_url );
exit();
*/



	
	