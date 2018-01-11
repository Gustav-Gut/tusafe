<?php $excluded_from_cancel = array('payza', 'braintree', 'bank_transfer');?>
<div class="ihc-ap-wrap">
	<?php if (!empty($data['title'])):?>
		<h3><?php echo do_shortcode($data['title']);?></h3>
	<?php endif;?>
	<?php if (!empty($data['content'])):?>
		<p><?php echo do_shortcode($data['content']);?></p>
	<?php endif;?>

	<div class="iump-account-content-title"><?php _e('Subscription Details', 'ihc');?></div>
	
	<?php
			if ($levels_str!='' && $data['show_table']){
				$levels_arr = explode(',', $levels_str);
				?>
				<table class="ihc-account-subscr-list">
					<thead>
						<tr> 
							<td style="padding-left: 15px;"><?php _e("Level Name", 'ihc');?></td> 
							<td><?php _e("Status", 'ihc');?></td>
							<td><?php _e("Expire Time", 'ihc');?></td>
							<td><?php _e("Access", 'ihc');?></td>
							<td style="text-align:center;"><?php _e("Recurrent", 'ihc');?></td>
							<td style="text-align:right;"><?php _e("Amount", 'ihc');?></td>
						</tr>
					</thead>
				<?php
				$i = 0;
				$show_meta_links = ihc_return_meta_arr('level_subscription_plan_settings');
				$check_renew = (empty($show_meta_links['ihc_show_renew_link'])) ? 0 : $show_meta_links['ihc_show_renew_link'];
				$show_delete = (empty($show_meta_links['ihc_show_delete_link'])) ? 0 : $show_meta_links['ihc_show_delete_link'];
				$show_renew = TRUE;
				
				foreach ($levels_arr as $level_id){
					$time_data = ihc_get_start_expire_date_for_user_level($this->current_user->ID, $level_id);
					if (strtotime($time_data['expire_time'])>time()){
						$expire = $time_data['expire_time'];
					} else if (strtotime($time_data['expire_time'])<0) {
						$expire = __('--', 'ihc');//not active yet
					} else {
						$expire = __('Expired', 'ihc');
					}
					$show_cancel = ihc_show_cancel_level_link($this->current_user->ID, $level_id);

					if ($check_renew){
						$show_renew = ihc_show_renew_level_link($level_id);						
					}
					$payment_type = get_option('ihc_payment_selected');
					
					$level_data = ihc_get_level_by_id($level_id);
					if (empty($level_data)){
						continue;
					}
					$hidden_div = 'ihc_ap_subscription_l_' . $i;
					$status = ihc_get_user_level_status_for_ac($this->current_user->ID, $level_id);
					$payment_type_for_this_level = Ihc_Db::get_payment_tyoe_by_userId_levelId($this->current_user->ID, $level_id);
					?>
					<tr onMouseOver="ihc_dh_selector('<?php echo '#' . $hidden_div;?>', 1);" onMouseOut="ihc_dh_selector('<?php echo '#' . $hidden_div;?>', 0);">
						<td  class="ihc-level-name-wrapp" style="text-align:left;"><span class="ihc-level-name"><?php echo $level_data['label'];?></span>
							<div style="visibility: hidden;" id="<?php echo $hidden_div;?>">
								<?php
									if ($show_renew){
										$include_stripe_script = TRUE;		
										$renew_label = __('Renew', 'ihc');
										$time_arr = ihc_get_start_expire_date_for_user_level($this->current_user->ID, $level_id);
										if (isset($time_arr['expire_time']) && $time_arr['expire_time']=='0000-00-00 00:00:00'){
											//it's for the first time
											$renew_label = __('Finish payment', 'ihc');
										}				
										?>
										<span style="cursor: pointer;" onClick="ihc_renew_function('#ihc_renew_level', '#ihc_form_ap_subscription_page', <?php echo $level_id;?>, '<?php echo $level_data['label'];?>',  '<?php echo $level_data['price'];?>');"><?php echo $renew_label;?></span> |
										<?php
									} else if (ihc_is_level_on_hold($this->current_user->ID, $level_id)){
										?>
										<span style="cursor: pointer;" onClick="ihc_renew_function('#ihc_renew_level', '#ihc_form_ap_subscription_page', <?php echo $level_id;?>, '<?php echo $level_data['label'];?>',  '<?php echo $level_data['price'];?>');"><?php echo __('Finish payment', 'ihc');?></span> |
										<?php										
									} else {
										///finish payment
										$include_stripe_script = TRUE;		
										$time_arr = ihc_get_start_expire_date_for_user_level($this->current_user->ID, $level_id);
										if (isset($time_arr['expire_time']) && ($time_arr['expire_time']=='0000-00-00 00:00:00' || ($time_arr['expire_time']==FALSE && $time_arr['start_time']==FALSE))){
											//it's for the first time
											$renew_label = __('Finish payment', 'ihc');
											?>
											<span style="cursor: pointer;" onClick="ihc_renew_function('#ihc_renew_level', '#ihc_form_ap_subscription_page', <?php echo $level_id;?>, '<?php echo $level_data['label'];?>',  '<?php echo $level_data['price'];?>');"><?php echo $renew_label;?></span> |
											<?php												
										}													
									}				
									if ($show_cancel){									
										if ($payment_type_for_this_level && !in_array($payment_type_for_this_level, $excluded_from_cancel)):
										?>	
											<span style="color: red;cursor: pointer;" onClick="ihc_set_form_i('#ihc_cancel_level', '#ihc_form_ap_subscription_page', <?php echo $level_id;?>);"><?php _e('Cancel', 'ihc');?></span> |
										<?php
										endif;
									}

								?>
								<?php if ($show_delete):?>
								<span style="color: red;cursor: pointer;" onClick="ihc_set_form_i('#ihc_delete_level', '#ihc_form_ap_subscription_page', <?php echo $level_id;?>, 1);"><?php _e('Delete', 'ihc');?></span> 
								<?php endif;?>
							</div>
						</td>
					<td class="ihc_account_level_status"><?php echo $status;?></td>
					<?php
					if ($expire && $expire!='--' && $expire!=__('Expired', 'ihc')){
						?><td><?php echo date("F j, Y", strtotime($expire));?></td><?php
					} else {
						?><td>--</td><?php
					}					
					$paid_type = $level_data['payment_type'];
					if ($paid_type == 'payment') $paid_type = __('Paid', 'ihc');
					else $paid_type = __('Free', 'ihc');
					?><td style="text-transform: capitalize;"><?php echo $paid_type;?></td><?php
					$reccurence = '--';
					$r = array(  
								 'bl_onetime' => __('No', 'ihc'),
								 'bl_ongoing'=>__('Yes', 'ihc'), 
								 'bl_limited'=> __('Limited', 'ihc'),
					);
					if (!empty($level_data['billing_type']) && !empty($r[$level_data['billing_type']])){
						$reccurence = $r[$level_data['billing_type']];
					}
					?><td style="text-align:center;"><?php echo $reccurence;?></td><?php
					if ($level_data['price'] && $level_data['payment_type']=='payment'){
						$currency = get_option('ihc_currency');
						//$price = $level_data['price'] . ' ' . $currency;
						$price = ihc_format_price_and_currency($currency, $level_data['price']);
					} else {
						$price = '--';
					}
					?><td style="color:#222; text-align:right; padding-right:10px;"><?php echo $price;?></td>
					</tr><?php
					$i++;
				}
				$default_payment = get_option('ihc_payment_selected');
				?></table>
					<form id="ihc_form_ap_subscription_page" name="ihc_ap_subscription_page" method="post" >				
						<input type="hidden" name="ihc_delete_level" value="" id="ihc_delete_level" />
						<input type="hidden" name="ihc_cancel_level" value="" id="ihc_cancel_level" />
						<input type="hidden" name="ihc_renew_level" value="" id="ihc_renew_level" />
						<input type="hidden" name="ihcaction" value="renew_cancel_delete_level_ap" />
				<?php
				$the_payment_type = ( ihc_check_payment_available($default_payment) ) ? $default_payment : '';
				if (!defined('IHC_HIDDEN_PAYMENT_PRINT')) define('IHC_HIDDEN_PAYMENT_PRINT', TRUE);
					?><input type="hidden" value="<?php echo $the_payment_type;?>" name="ihc_payment_gateway" />
				</form><?php
				
				if (($payment_type=='stripe' || !empty($include_stripe)) && !empty($include_stripe_script)){
					echo ihc_stripe_renew_script('#ihc_form_ap_subscription_page');
				}			
			}
		if ($data['show_subscription_plan']){
			echo ihc_user_select_level();	/// FALSE, FALSE
		}		
	?>	
	
</div>
