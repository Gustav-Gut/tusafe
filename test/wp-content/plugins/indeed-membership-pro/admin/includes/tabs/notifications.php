<?php 
echo ihc_inside_dashboard_error_license();
echo ihc_check_default_pages_set();
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
?>

<?php 
$notification_arr = array(
		'admin_user_register' => __('New Registered User', 'ihc'), // - Admin Notification
		'admin_before_user_expire_level' => __('First Alert Before Level Expire', 'ihc'),
		'admin_second_before_user_expire_level' => __('Second Alert Before Level Expire', 'ihc'),
		'admin_third_before_user_expire_level' => __('Third Alert Before Level Expire', 'ihc'),
		'admin_user_expire_level' => __('After Level Expired', 'ihc'),
		'admin_user_payment' => __('New Payment Completed', 'ihc'),
		'admin_user_profile_update' => __('User Profile Update', 'ihc'),
		
		'ihc_cancel_subscription_notification-admin' => __('When the Subscription was Canceled', 'ihc'),
		'ihc_delete_subscription_notification-admin' => __('When the Subscription was Deleted', 'ihc'),
		'ihc_order_placed_notification-admin' => __('New Order placed', 'ihc'),
		'ihc_new_subscription_assign_notification-admin' => __('New Subscription assign', 'ihc'),
		
		'register' => __('New Account', 'ihc'), //Register
		'register_lite_send_pass_to_user' => __('Register Lite - Send password to user', 'ihc'),
		'review_request' => __('New Account Review Request', 'ihc'), //register with pending
		'before_expire' => __('First Alert Before Level Expire', 'ihc'),
		'second_before_expire' => __('Second Alert Before Level Expire', 'ihc'),
		'third_before_expire' => __('Third Alert Before Level Expire', 'ihc'),
		'expire' => __('After Level Expired', 'ihc'),
		'email_check' => __('Double E-mail Verification Request', 'ihc'),
		'email_check_success' => __('Double E-mail Verification Validated', 'ihc'),
		'reset_password_process' => __('Reset Password Start Process', 'ihc'),
		'reset_password' => __('Reset Password Request', 'ihc'),
		'change_password' => __('Changed Password Inform', 'ihc'),
		'approve_account' => __('Approve Account'),
		'delete_account' => __('Deleted Account Inform', 'ihc'),
		'payment' => __('New Payment Completed', 'ihc'),
		'user_update' => __('User Profile Updates', 'ihc'),
		'bank_transfer' => __('Bank Transfer Payment Details', 'ihc'),
			
		'ihc_order_placed_notification-user' => __('Order placed', 'ihc'),
		'ihc_subscription_activated_notification' => __('Subscription Activated', 'ihc'),
		'ihc_delete_subscription_notification-user' => __('When the Subscription was Deleted', 'ihc'),
		'ihc_cancel_subscription_notification-user' => __('When the Subscription was Canceled', 'ihc'),			
);

if (isset($_GET['edit_notification']) || isset($_GET['add_notification'])){
	//add/edit

	$notification_id = (isset($_GET['edit_notification'])) ? @$_GET['edit_notification'] : FALSE;
	$meta_arr = ihc_get_notification_metas($notification_id);
	?>
	<form method="post" action="<?php echo $url.'&tab=notifications';?>">
		<?php 
			if ($notification_id){
				?>
				<input type="hidden" name="notification_id" value="<?php echo $notification_id;?>" />
				<?php 	
			} else {
				?>
				<script>
					jQuery(document).ready(function(){
						ihc_change_notification_template();
					});
				</script>
				<?php 	
			}
		?>
		<div class="ihc-stuffbox">
			<h3><?php _e('Add new Notification', 'ihc');?></h3>
			<div class="inside">
				<div class="iump-form-line">
					<label class="iump-labels-special"><?php _e('Action:', 'ihc');?></label>
					<select name="notification_type" id="notification_type" onChange="ihc_change_notification_template();ihc_notification_level_only_for();">
						<?php 
							foreach ($notification_arr as $k=>$v){
								//Manually set optGroups
								switch($k){
									case 'admin_user_register':
											echo ' <optgroup label="' . __('----------Admin Notifications----------', 'ihc') . '"> </optgroup>';
											echo ' <optgroup label="' . __('Register Process', 'ihc') . '">';
										break;
									case 'register':
													echo ' <optgroup label="' . __('----------Users Notifications----------', 'ihc') . '"> </optgroup>';
													echo ' <optgroup label="Register Process">';
													break;
									
									case 'email_check':
													echo ' <optgroup label="Double Email Verification">';
													break;	
									case 'before_expire':
									case 'admin_before_user_expire_level':
													echo ' <optgroup label="Level Expire">';
													break;									
									case 'admin_user_payment':
													echo ' <optgroup label="User Actions">';
													break;				
									case 'reset_password_process':
													echo ' <optgroup label="Password">';
													break;	
									case 'approve_account':
										echo ' <optgroup label="Admin Actions">'; 			
										break;
									case 'payment':
										echo ' <optgroup label="User Actions">'; 			
										break;									
								}
								?>
								<option value="<?php echo $k;?>" <?php if ($meta_arr['notification_type']==$k) echo 'selected';?>><?php echo $v;?></option>
								<?php 
								switch($k){
									case 'ihc_new_subscription_assign_notification-admin':
									case 'review_request':
									case 'email_check_success':
									case 'expire':	
									case 'admin_user_expire_level':
									case 'change_password':	
									case 'delete_account':
									case 'admin_user_profile_update':
									case 'ihc_cancel_subscription_notification-user':
									//case 'bank_transfer':	
										echo ' </optgroup>';
										break;								
								}	
							}
						?>
					</select>			
				</div>
				<div class="iump-form-line">
					<label class="iump-labels-special"><?php _e('Level:', 'ihc');?></label>

					<select name="level_id">
						<option value="-1" <?php if ($meta_arr['level_id']==-1) echo 'selected';?>>All</option>
						<?php 
						$levels = get_option('ihc_levels');
						if ($levels && count($levels)){
							foreach ($levels as $k=>$v){								
								?>
									<option value="<?php echo $k;?>" <?php if ($meta_arr['level_id']==$k) echo 'selected';?>><?php echo $v['name'];?></option>
								<?php 
							}
						}
						?>
					</select>	
					<div style="color: #999;font-size: 10px; font-style: italic;"><?php 
						echo __('Available only for:', 'ihc')
							. ', ' . $notification_arr['register']
							. ', ' . $notification_arr['review_request']
							. ', ' . $notification_arr['before_expire']
							. ', ' . $notification_arr['expire']
							. ', ' . $notification_arr['payment']
							. ', ' . $notification_arr['bank_transfer']
							. ', ' . $notification_arr['admin_user_register']
							. ', ' . $notification_arr['admin_user_expire_level']
							. ', ' . $notification_arr['admin_before_user_expire_level']
							. ', ' . $notification_arr['admin_user_payment']
							. '.';						
					;?></div>		
				</div>	
				<div class="iump-form-line">
					<label class="iump-labels-special"><?php _e('Subject:', 'ihc');?></label>
					<input type="text" name="subject" value="<?php echo $meta_arr['subject'];?>" style="width: 450px;" id="notification_subject" />
				</div>
				<div class="iump-form-line" style="padding: 10px 0px 0px 5px;">
					<label class="iump-labels-special"><?php _e('Message:', 'ihc');?></label>
				</div>
				<div style="padding-left: 5px; width: 70%;display:inline-block;">
					<?php wp_editor( $meta_arr['message'], 'ihc_message', array('textarea_name'=>'message', 'quicktags'=>TRUE) );?>
				</div>					
				<div style="width: 25%; display: inline-block; vertical-align: top;margin-left: 10px; color: #333;">
					<?php 
						$constants = array( 
											'{username}'=>'', 
											'{user_email}'=>'', 
											'{first_name}'=>'', 
											'{last_name}'=>'', 
											'{account_page}'=>'', 
											'{login_page}'=>'', 
											'{current_level}'=>'', 
											'{current_level_expire_date}'=>'', 
											'{level_list}'=>'',
											'{blogname}'=>'', 
											'{blogurl}'=>'', 
											'{verify_email_address_link}'=>'', 
											'{NEW_PASSWORD}'=>'',
											'{currency}'=>'', 
											'{amount}'=>'', 
											'{level_name}'=>'', 
											'{password_reset_link}' => '',
											'{current_date}' => '', 
						);
						$extra_constants = ihc_get_custom_constant_fields();
						foreach ($constants as $k=>$v){
							?>
							<div><?php echo $k;?></div>
							<?php 	
						}
						echo "<h4>".__('Custom Fields constants', 'ihc')."</h4>";
						foreach ($extra_constants as $k=>$v){
							?>
							<div><?php echo $k;?></div>
							<?php 	
						}
					?>
				</div>				
						
				<div style="margin-top: 15px;">
					<input type="submit" value="<?php if ($notification_id){_e('Update', 'ihc');} else{_e('Add New', 'ihc');}?>" name="ihc_save" class="button button-primary button-large">
				</div>				
			</div>	
		</div>
	</form>	
	<?php 
} else {
	//listing
	if (isset($_POST['ihc_save'])){
		ihc_save_notification_metas($_POST);
	} else if (isset($_POST['delete_notification_by_id'])){
		ihc_delete_notification($_POST['delete_notification_by_id']);
	}
	$data = ihc_get_all_notification_available();
		?>
		<div class="iump-wrapper">
		<div class="iump-page-title">Ultimate Membership Pro - 
							<span class="second-text">
								<?php _e('Notifications', 'ihc');?>
							</span>
						</div>
			<a href="<?php echo $url.'&tab=notifications&add_notification=true';?>" class="indeed-add-new-like-wp"><i class="fa-ihc fa-add-ihc"></i><?php _e('Add New Notification', 'ihc');?></a>
			<span class="ihc-top-message"><?php _e('...create your notification Templates!', 'uap');?></span>
			<a href="javascript:void(0)" class="button button-primary button-large" style="display:inline-block; float:right;" onClick="ihc_check_email_server();"><?php _e('Check Mail Server', 'ihc');?></a>
			<div class="ihc-clear"></div>
			<?php 
			if ($data){
			?>
				<form id="delete_notification" method="post" action=""><input type="hidden" value="" id="delete_notification_by_id" name="delete_notification_by_id"/></form>
				<div class="ihc-sortable-table-wrapp" style="margin: 20px 20px 20px 0px;" >
					<table class="wp-list-table widefat fixed tags" id="ihc-levels-table">
						<thead>
							<tr>	
								<th class="manage-column"><?php _e('Subject', 'ihc');?></th>
								<th class="manage-column"><?php _e('Action', 'ihc');?></th>
								<th class="manage-column"><?php _e('Goes to', 'ihc');?></th>
								<th class="manage-column"><?php _e('Target Levels', 'ihc');?></th>
							</tr>
						</thead>
											  
						<tfoot>
							<tr>	
								<th class="manage-column"><?php _e('Subject', 'ihc');?></th>
								<th class="manage-column"><?php _e('Action', 'ihc');?></th>
								<th class="manage-column"><?php _e('Goes to', 'ihc');?></th>
								<th class="manage-column"><?php _e('Target Levels', 'ihc');?></th>
							</tr>	
						</tfoot>
								
						<tbody class="ui-sortable">
							<?php 
								$admin_actions = array(
														'admin_user_register',
														'admin_before_user_expire_level',
														'admin_user_expire_level',
														'admin_user_payment',
														'admin_user_profile_update',									
								);
								foreach ($data as $item){
								?>
								<tr onmouseover="ihc_dh_selector('#notify_tr_<?php echo $item->id;?>', 1);" onmouseout="ihc_dh_selector('#notify_tr_<?php echo $item->id;?>', 0);">
									<td><?php 
										if (strlen($item->subject)>100){
											echo substr($item->subject, 0, 100) . ' ...';
										} else {
											echo $item->subject;
										}
										
										?>
										<div style="visibility: hidden;" id="notify_tr_<?php echo $item->id;?>">
											<a href="<?php echo $url.'&tab=notifications&edit_notification='.$item->id;?>"><?php _e('Edit', 'ihc');?></a> | 
											<span onClick="jQuery('#delete_notification_by_id').val(<?php echo $item->id;?>); jQuery('#delete_notification').submit();" style="color: red;cursor: pointer;"><?php _e('Delete', 'ihc');?></span>
										</div>
									</td>		
									<td style="color: #21759b; font-weight:bold;font-family: 'Oswald', arial, sans-serif !important;font-size: 14px;font-weight: 400;"><?php 
										echo $notification_arr[$item->notification_type];
									?></td>
									<td><?php 
										if (in_array($item->notification_type, $admin_actions)){
											echo 'Admin';
										} else {
											echo 'User';
										}
									?></td>
									<td><?php 
										if ($item->level_id==-1){
											echo 'All';
										} else {
											$level_data = ihc_get_level_by_id($item->level_id);
											echo $level_data['name'];
										}
									?></td>
								</tr>									
							<?php 	
								}
							?>	
						</tbody>
					</table>			
				</div>
				<?php 
				}
				?>
			
		</div>	
<script>
	
	jQuery(document).ready(function(){
		ihc_notification_level_only_for();
	});
	
</script>						
<?php 
}
