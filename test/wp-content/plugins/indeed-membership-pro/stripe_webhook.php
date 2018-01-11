<?php
if (empty($no_load)){
	require_once '../../../wp-load.php';	
	require_once 'utilities.php';
}
require_once 'classes/stripe/init.php';

ini_set('display_errors','on');

$publishable_key = get_option('ihc_stripe_publishable_key');
$secret_key = get_option('ihc_stripe_secret_key');
if (empty($secret_key)){
	/// out
	die();
}
\Stripe\Stripe::setApiKey($secret_key);

$body = @file_get_contents('php://input');
$event_arr = json_decode($body, TRUE);

$event_arr['ihc_payment_type'] = 'stripe';//set payment type

//insert this request into debug payments table
if (get_option('ihc_debug_payments_db')){
	ihc_insert_debug_payment_log('stripe', $event_arr );
}
if(isset($event_arr['id'])){
	$event = \Stripe\Event::retrieve($event_arr['id']);
} else {
	echo '============= Ultimate Membership Pro - STRIPE WEBHOOK ============= ';
	echo '<br/><br/>No Event sent. Come later';	
	die();
}
if (is_array($event_arr) && is_array($event_arr['data']) && is_array($event_arr['data']['object']) && isset($event_arr['data']['object']['customer'])){
	$the_key = $event_arr['data']['object']['customer'];
} else {
	$the_key = '';
}
		
if ($event && isset($event->data->object->id) && $the_key){
	
	$data = ihc_get_uid_lid_by_stripe($the_key);
	if (count($data)>0 && isset($data['uid']) && isset($data['lid'])){
		//we have user id and level id for processing
				
		if ($event->type=='charge.succeeded'){
			////// PAYMENT MADE	
			$event_arr['level'] = $data['lid'];
			$level_data = ihc_get_level_by_id($data['lid']);//getting details about current level
			ihc_update_user_level_expire($level_data, $data['lid'], $data['uid']);
			ihc_switch_role_for_user($data['uid']);
			
			$data_body = json_decode($body, TRUE);
			unset($data_body['data']); 
			$data_db = array_merge($data['payment_data'], $data_body);
			$data_db['message'] = 'success';
			ihc_insert_update_transaction($data['uid'], $the_key, $data_db);	
			
			//send notification to user
			ihc_send_user_notifications($data['uid'], 'payment', $data['lid']);
			ihc_send_user_notifications($data['uid'], 'admin_user_payment', $data['lid']);//send notification to admin
		} else if ($event->type=='customer.subscription.created' && $event->data->object->status=="trialing"){
			////// TRIAL
			$event_arr['level'] = $data['lid'];
			$level_data = ihc_get_level_by_id($data['lid']);//getting details about current level
			ihc_set_level_trial_time_for_no_pay($level_data, $data['lid'], $data['uid']);
			ihc_switch_role_for_user($data['uid']);			
			$data_body = json_decode($body, TRUE);
			unset($data_body['data']); 
			$data_db = array_merge($data['payment_data'], $data_body);		
			$data_db['message'] = 'success';	
			ihc_insert_update_transaction($data['uid'], $the_key, $data_db);			
			ihc_send_user_notifications($data['uid'], 'payment', $data['lid']);//send notification to user
			ihc_send_user_notifications($data['uid'], 'admin_user_payment', $data['lid']);//send notification to admin			
		} else if ($event->type=='invoiceitem.deleted') {
			//suspend the account?
			require_once IHC_PATH . 'public/functions.php';
			$expired = ihc_is_user_level_expired($data['uid'], $data['lid'], FALSE, TRUE);
			if ($expired){
				//delete user - level relationship
				ihc_delete_user_level_relation($data['lid'], $data['uid']);
			}
		}
	}
}

