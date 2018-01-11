<?php
if (!empty($_GET['delete'])){
	Ihc_Db::delete_order($_GET['delete']);	
}

if (!empty($_POST['submit_new_payment'])){
	unset($_POST['submit_new_payment']);
	$array = $_POST;
	if (empty($array['txn_id'])){
		/// set txn_id
		$array['txn_id'] = $_POST['uid'] . '_' . $_POST['order_id'] . '_' . time();		
	}
	$array['message'] = 'success';

	/// THIS PIECe OF CODE ACT AS AN IPN SERVICE.
	$level_data = ihc_get_level_by_id($_POST['level']);
	if (ihc_user_level_first_time($_POST['uid'], $_POST['level'])){
		/// CHECK FOR TRIAL
		ihc_set_level_trial_time_for_no_pay($level_data, $_POST['level'], $_POST['uid']);
	}
	ihc_update_user_level_expire($level_data, $_POST['level'], $_POST['uid']);	
					
	ihc_send_user_notifications($_POST['uid'], 'payment', $_POST['level']);//send notification to user
	ihc_send_user_notifications($_POST['uid'], 'admin_user_payment', $_POST['level']);//send notification to admin
	ihc_switch_role_for_user($_POST['uid']);	
	ihc_insert_update_transaction($_POST['uid'], $array['txn_id'], $array);
	unset($array);
}
$uid = (isset($_GET['uid'])) ? $_GET['uid'] : 0;

	$data['total_items'] = Ihc_Db::get_count_orders($uid);
	if ($data['total_items']){
		$url = admin_url('admin.php?page=ihc_manage&tab=orders');
		$limit = 25;
		$current_page = (empty($_GET['ihc_payments_list_p'])) ? 1 : $_GET['ihc_payments_list_p'];
		if ($current_page>1){
			$offset = ( $current_page - 1 ) * $limit;
		} else {
			$offset = 0;
		}
		if ($offset + $limit>$data['total_items']){
			$limit = $data['total_items'] - $offset;
		}
		//$limit = 25;
		include_once IHC_PATH . 'classes/Ihc_Pagination.class.php';
		$pagination = new Ihc_Pagination(array(
												'base_url' => $url,
												'param_name' => 'ihc_payments_list_p',
												'total_items' => $data['total_items'],
												'items_per_page' => $limit,
												'current_page' => $current_page,
		));
		$data['pagination'] = $pagination->output();
		$data['orders'] = Ihc_Db::get_all_order($limit, $offset, $uid);		
	}
	$data['view_transaction_base_link'] = admin_url('admin.php?page=ihc_manage&tab=payments&details_id=');
	$data['add_new_transaction_by_order_id_link'] = admin_url('admin.php?page=ihc_manage&tab=new_transaction&order_id=');

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
	
	$show_invoices = (ihc_is_magic_feat_active('invoices')) ? TRUE : FALSE;
?>
<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['tab'] =='orders') ? 'ihc-subtab-selected' : '';?>" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=orders');?>"><?php _e('Orders', 'ihc');?></a>	
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['tab'] =='payments') ? 'ihc-subtab-selected' : '';?>" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=payments');?>"><?php _e('Transactions', 'ihc');?></a>
	<div class="ihc-clear"></div>
</div>
<?php 
echo ihc_inside_dashboard_error_license();
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();

?>
<div class="iump-page-title">Ultimate Membership Pro - 
	<span class="second-text"><?php _e('Orders List', 'ihc');?></span>
</div>

<?php if (!empty($data['orders'])):?>
	<?php echo $data['pagination'];?>
<table class="wp-list-table widefat fixed tags" style="margin-top:20px;">
	<thead>
		<tr style="height: 45px; background-color: #666;">	
			<th class="manage-column" style="color:#fff;">
				<span><?php _e('ID', 'ihc');?></span>
			</th>	
			<th class="manage-column" style="color:#fff;">
				<span><?php _e('Code', 'ihc');?></span>
			</th>																	
			<th class="manage-column" style="color:#fff;">
				<span><?php _e('Customer', 'ihc');?></span>
			</th>						
			<th class="manage-column" style="color:#fff;">
				<span><?php _e('Items', 'ihc');?></span>
			</th>
			<th class="manage-column" style="color:#fff;">
				<span><?php _e('Total Amount', 'ihc');?></span>
			</th>	
			<th class="manage-column" style="color:#fff;">
				<span><?php _e('Payment Type', 'ihc');?></span>
			</th>							 			
			<th class="manage-column" style="color:#fff;">
				<span><?php _e('Date', 'ihc');?></span>
			</th>					
			<th class="manage-column" style="color:#fff;">
				<span><?php _e('Transaction', 'ihc');?></span>
			</th>		
			<?php if ($show_invoices):?>
				<th class="manage-column" style="color:#fff;">
					<span><?php _e('Invoices', 'ihc');?></span>
				</th>					
			<?php endif;?>				  	
			<th class="manage-column" style="color:#fff;">
				<span><?php _e('Status', 'ihc');?></span>
			</th>						  	
			<th class="manage-column" style="color:#fff;">
				<span><?php _e('Actions', 'ihc');?></span>
			</th>										  
		</tr>
	</thead>
	<tfoot>
		<tr>	
			<th class="manage-column">
				<span><?php _e('ID', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php _e('Code', 'ihc');?></span>
			</th>																	
			<th class="manage-column">
				<span><?php _e('Customer', 'ihc');?></span>
			</th>						
			<th class="manage-column">
				<span><?php _e('Items', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php _e('Total Amount', 'ihc');?></span>
			</th>		
			<th class="manage-column">
				<span><?php _e('Payment Type', 'ihc');?></span>
			</th>					 			
			<th class="manage-column">
				<span><?php _e('Date', 'ihc');?></span>
			</th>					
			<th class="manage-column">
				<span><?php _e('Transaction', 'ihc');?></span>
			</th>	
			<?php if ($show_invoices):?>
				<th class="manage-column" style="color:#fff;">
					<span><?php _e('Invoice', 'ihc');?></span>
				</th>					
			<?php endif;?>									  	
			<th class="manage-column">
				<span><?php _e('Status', 'ihc');?></span>
			</th>						  	
			<th class="manage-column">
				<span><?php _e('Actions', 'ihc');?></span>
			</th>				  
		</tr>
	</tfoot>

	<?php 
	$i = 1;
	foreach ($data['orders'] as $array):?>
		<tr  class="<?php if($i%2==0) echo 'alternate';?>">
			<td><?php echo $array['id'];?></td>
			<td><?php  
				if (!empty($array['metas']['code'])){
					echo $array['metas']['code'];
				} else {
					echo '-';
				}
			?></td>
			<td><span style="color: #21759b; font-weight:bold;"><?php echo $array['user'];?></span></td>
			<td><div class="level-type-list"><?php echo $array['level'];?></div></td>
			<td><span class="level-payment-list"><?php echo $array['amount_value'] . ' ' . $array['amount_type'];?></span></td>
			<td><?php 
				if (empty($array['metas']['ihc_payment_type'])):
					echo '-';
				else:
					if (!empty($array['metas']['ihc_payment_type'])){
						$gateway_key = $array['metas']['ihc_payment_type'];
						echo $payment_gateways[$gateway_key]; 
					}					
				endif;	
			?></td>
			<td><?php echo $array['create_date'];?></td>
			<td><?php 
					if (empty($array['transaction_id'])):
						?>
							<a href="<?php echo $data['add_new_transaction_by_order_id_link'] . $array['id'];?>"><?php _e('Add New', 'uap');?></a>							
						<?php
					else :
						?>
							<a href="<?php echo $data['view_transaction_base_link'] . $array['transaction_id'];?>"><?php _e('View', 'uap');?></a>
						<?php						
					endif;	
			?></td>
			<?php if ($show_invoices):?>
				<td><i class="fa-ihc fa-invoice-preview-ihc iump-pointer" onClick="iump_generate_invoice(<?php echo $array['id'];?>);"></i></td>				
			<?php endif;?>				
			<td style="font-family: 'Oswald', arial, sans-serif !important; font-weight:400;"><?php echo ucfirst($array['status']);?></td>
			<td class="column" style="width:80px; text-align:center;">
				<a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=orders&delete=') . $array['id'];?>">
					<i class="fa-ihc ihc-icon-remove-e"></i>
				</a>
			</td>			
		</tr>
	<?php
		$i++; 
	 endforeach;?>

</table>

<?php endif;?>