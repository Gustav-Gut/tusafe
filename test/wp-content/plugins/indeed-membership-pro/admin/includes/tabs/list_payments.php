<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['tab'] =='orders') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab=orders';?>"><?php _e('Orders', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['tab'] =='payments') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab=payments';?>"><?php _e('Transactions', 'ihc');?></a>
	<div class="ihc-clear"></div>
</div>
<?php 
echo ihc_inside_dashboard_error_license();
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();

	 
if (isset($_REQUEST['delete'])){
	ihc_delete_payment_entry($_REQUEST['delete']);
}

$payment_gateways = array( 
					'paypal' => 'PayPal', 
			        'authorize' => 'Authorize', 
				    'stripe' => 'Stripe', 
				    'twocheckout' => '2Checkout', 
				    'bank_transfer' => 'Bank Transfer',
				    'braintree' => 'Braintree',
				    'payza' => 'Payza',
				    'woocommerce' => 'WooCommerce',
);

global $wpdb;
$table_name = $wpdb->prefix . 'indeed_members_payments';

if (isset($_REQUEST['details_id'])){
	?>
	<div class="ihc-sortable-off" style="float:none; margin-bottom:15px;">
		<a style="text-decoration:none;" href="<?php echo $url.'&tab=payments';?>"><?php _e('Back to Payment List', 'ihc');?></a>
	</div>
	<div class="ihc-stuffbox">
	<h3><?php _e('Payment Details', 'ihc');?></h3>
	<div class="inside">
	<?php 	
	$data = $wpdb->get_row( 'SELECT * FROM '.$table_name.' WHERE id='.$_REQUEST['details_id'].' ;' );
	
	if (!empty($data->history)){
		//print the history
		$dat = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $data->history);
		$dat = unserialize($dat);		
		if (isset($dat) && is_array($dat)){
			foreach ($dat as $k=>$transaction_history_arr){
				if (is_string($transaction_history_arr)){
					//is json
					$json = stripslashes($transaction_history_arr);
					if ($k){
						echo '<h4>' . date('Y-m-d H:i:s', $k) .'</h4>';
					}
					$arr = (array)json_decode($json, true);
					foreach ($arr as $key=>$value){
						echo $key.': '.$value.'<br/>';
					}
				} else {	
					//is an array
					if ($k>0){ 
						echo '<h4>' . date('Y-m-d H:i:s', $k) .'</h4>';
					}
					foreach ($transaction_history_arr as $key=>$value){
						echo $key.' : '.$value.'<br/>';
					}
				}
			}
		}
	} else if(!empty($data->payment_data)) {
		//insert history
		$arr = (array)json_decode($data->payment_data);
		unset($arr['custom']);
		unset($arr['transaction_subject']);
		if (!empty($arr->paydate)){
			$arr_key = strtotime($arr->paydate);
		} else {
			$arr_key = 0;
		}
		$history[$arr_key] = json_encode($arr);
		$history_str = serialize($history);
		$wpdb->query("UPDATE " . $table_name . " SET history='" . addcslashes($history_str, "'") . "' WHERE id='" . $_REQUEST['details_id'] . "' ;" );
	}
	?>
	</div>
	</div>
<?php
} else {
	///list all payments
?>
<div class="iump-page-title">Ultimate Membership Pro - 
							<span class="second-text">
								<?php _e('Transations List', 'ihc');?>
							</span>
						</div>
<?php	
$count_total_items = $wpdb->get_row("SELECT COUNT(*) as c FROM $table_name;");
$total_items = (empty($count_total_items->c)) ? 0 : $count_total_items->c;
$url = admin_url('admin.php?page=ihc_manage&tab=payments');
$limit = 25;
$current_page = (empty($_GET['ihc_payments_list_p'])) ? 1 : $_GET['ihc_payments_list_p'];
if ($current_page>1){
	$offset = ( $current_page - 1 ) * $limit;
} else {
	$offset = 0;
}
if ($offset + $limit>$total_items){
	$limit = $total_items - $offset;
}
$limit = 25;
include_once IHC_PATH . 'classes/Ihc_Pagination.class.php';
$pagination = new Ihc_Pagination(array(
										'base_url' => $url,
										'param_name' => 'ihc_payments_list_p',
										'total_items' => $total_items,
										'items_per_page' => $limit,
										'current_page' => $current_page,
));
$pagination_str = $pagination->output();

$data_db = $wpdb->get_results("SELECT * FROM $table_name ORDER BY paydate DESC LIMIT $limit OFFSET $offset;");



	if ($data_db && count($data_db)){
		?>
		<?php echo $pagination_str;?>
							<table class="wp-list-table widefat fixed tags">
								  <thead>
									<tr>	
										  <th class="manage-column">
											  <span>
												<?php _e('Username', 'ihc');?>
											  </span>
										  </th>													
										  <!--th class="manage-column">
											  <span>
												<?php _e('Name', 'ihc');?>
											  </span>
										  </th-->	
										  <!--th class="manage-column">
											  <span>
												<?php _e('E-mail', 'ihc');?>
											  </span>
										  </th-->											  
										  <th style="width:30%;">
											  <span>
												<?php _e('Orders', 'ihc');?>
											  </span>									  
										  </th>
										  <th>
											  <span>
												<?php _e('Amount', 'ihc');?>
											  </span>									  
										  </th>	
										  <th>
											  <span>
												<?php _e('Payment Type', 'ihc');?>
											  </span>									  
										  </th>											  
										  <th>
										  	  <span>
										  		<?php _e('Status', 'ihc');?>
										  	  </span>
										  </th>	
										  <th class="manage-column">
											  <span>
												<?php _e('Details', 'ihc');?>
											  </span>
										  </th>											  								  
										  <th class="manage-column">
											  <span>
												<?php _e('Date', 'ihc');?>
											  </span>
										  </th>		
										  <th class="manage-column" style="width:80px; text-align:center;">
											  <span>
												<?php _e('Delete', 'ihc');?>
											  </span>
										  </th>											  										  								  								  
								    </tr>
								  </thead>
								  
								  <tfoot>
									<tr>	
										  <th class="manage-column">
											  <span>
													<?php _e('Username', 'ihc');?>
											  </span>
										  </th>															
										  <!--th class="manage-column">
											  <span>
													<?php _e('Name', 'ihc');?>
											  </span>
										  </th-->
										  <!--th class="manage-column">
											  <span>
													<?php _e('E-mail', 'ihc');?>
											  </span>
										  </th-->											  
										  <th>
											  <span>
													<?php _e('Orders', 'ihc');?>
											  </span>									  
										  </th>
										  <th>
											  <span>
													<?php _e('Amount', 'ihc');?>
											  </span>									  
										  </th>	
										  <th>
											  <span>
												<?php _e('Payment Type', 'ihc');?>
											  </span>									  
										  </th>											  
										  <th>
										  		<span>
										  			<?php _e('Status', 'ihc');?>
										  		</span>
										  </th>											  										  
										  <th class="manage-column">
											  <span>
													<?php _e('Details', 'ihc');?>
											  </span>
										  </th>		
										  <th class="manage-column">
											  <span>
													<?php _e('Date', 'ihc');?>
											  </span>
										  </th>	
										  <th class="manage-column" style="width:80px; text-align:center;">
											  <span>
													<?php _e('Delete', 'ihc');?>
											  </span>
										  </th>		
								    </tr>
								  </tfoot>
								  <tbody>
										<?php 
										foreach ($data_db as $arr){
											$data = json_decode($arr->payment_data);
											
											$user_info = get_userdata($arr->u_id);
											?>
												<tr>
													  <td class="manage-column">
													  	<span style="color: #21759b; font-weight:bold;">
													  	<?php 
													  		if (isset( $user_info->data->user_login) &&  $user_info->data->user_login){
													  			echo $user_info->data->user_login;
													  		}
													  	?>
														</span>
													  </td>
													  <!--td class="manage-column">
														  <span style="color: #21759b; font-weight:bold;">
															<?php																
																$first_name = get_user_meta($arr->u_id, 'first_name', true);
																$last_name = get_user_meta($arr->u_id, 'last_name', true);
																if ($first_name || $last_name){
																	echo $first_name .' '.$last_name;
																} else {
																	if (isset($user_info->user_nicename)){
																		echo $user_info->user_nicename;
																	} else {
																		echo '<span style="color: red;">' . __('Deleted User', 'ihc') . '</span>';	
																	}
																}
																
															?>
														  </span>
													  </td-->
													  <!--td class="manage-column">
													  	<?php 
													  		if (isset($user_info->data->user_email) && $user_info->data->user_email){
													  			echo $user_info->data->user_email;
													  		}
													  	?>
													  </td-->
													  <td class="manage-column">
													  	<?php 
														  	/*if (isset($data->level)){
														  		//2checkout
														  		$level_data_arr = ihc_get_level_by_id($data->level);
														  		echo $level_data_arr['label'];
														  	} else if (isset($data->item_name)){
													  			echo $data->item_name;
													  		} else if (isset($data->x_description)){
																echo $data->x_description;
															} else if (isset($data->lid)){
														  		$level_data_arr = ihc_get_level_by_id($data->lid);
														  		echo $level_data_arr['label'];																
															} else {
													  			echo '-';	
													  		}*/
															$tx_orders = array();
															$tx_orders = unserialize($arr->orders);
															if(!empty($tx_orders) && is_array($tx_orders) && count($tx_orders)>0){
																foreach($tx_orders as $order){
																	$order_code = '-';
																	$order_code = Ihc_Db::get_order_meta($order, 'code');
																	
																	echo '<div class="level-type-list  ihc-expired-level">' . $order_code . '</div>';
																} 
															} else {
																/// No order
																echo '<div class="level-type-list  ihc-expired-level">-</div>';
															}
													  	?>
													  
													  </td>
													  <td class="manage-column">
													  	<span class="level-payment-list">
														<?php
															if (isset($data->mc_gross) && isset($data->mc_currency)){
																echo $data->mc_gross . ' ' .$data->mc_currency;
															} else if (isset($data->x_amount)){
																echo $data->x_amount;
																if(isset($data->x_currency_code)){
																	echo ' '.$data->x_currency_code;
																}
															} else if (isset($data->amount) && isset($data->currency)){
																echo $data->amount . ' ' .$data->currency;;
															} else if(isset($data->total)){
																echo $data->total . ' ' . $data->currency_code;
															} else {
																echo '-';	
															}
														?>
													  	</span>
													  </td>
													  <td><?php 
													  		if (!empty($data->ihc_payment_type)){
													  			$gateway_key = $data->ihc_payment_type;
													  			echo $payment_gateways[$gateway_key]; 
													  		}
													  ?></td>
													  <td class="manage-column" style="font-family: 'Oswald', arial, sans-serif !important; font-weight:400;">
													  	<?php 
													  		if (!empty($data->payment_status)){
													  			$pay_sts = $data->payment_status;
													  		} else if (isset($data->x_response_code) && ($data->x_response_code == 1)){
															  	$pay_sts = __('Confirmed', 'ihc');
															} else if (isset($data->code) && ($data->code == 2)){
															  	$pay_sts = __('Confirmed', 'ihc');
															} else if(isset($data->message) && $data->message=='success'){
																$pay_sts = __('Confirmed', 'ihc');
															} else if (isset($data->ap_status) && ($data->ap_status=='Success' || $data->ap_status=='Subscription-Payment-Success')){
																$pay_sts = __('Confirmed', 'ihc');
															} else {
																$pay_sts = '-';	
															}
															if ($pay_sts=='pending'){
																$pay_sts = __('Pending', 'ihc');
															}
															echo $pay_sts;													  		
													  	?>
													  </td>													  
													  <td class="manage-column">
														  <span>
															<a href="<?php echo $url.'&tab=payments&details_id='.$arr->id;?>"><?php _e('View Details', 'ihc');?></a>
														  </span>
													  </td>		
													  <td class="manage-column">
														  <span>
															<?php echo $arr->paydate;?>
														  </span>
													  </td>			
												      <td class="column" style="width:80px; text-align:center;">
															<a href="<?php echo $url.'&tab=payments&delete='.$arr->id;?>">
																<i class="fa-ihc ihc-icon-remove-e"></i>
															</a>
												      </td>								
												</tr>											
											<?php 
										}
										?>								  
								  </tbody>
						</table>
		<?php 
	} else {
		?>
		<div class="ihc-warning-message"> <?php _e('No Payments Available to show up!', 'ihc');?></div> 
		<?php 
	}
} // end of list all payments
