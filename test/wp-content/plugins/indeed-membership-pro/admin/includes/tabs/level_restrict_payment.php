<?php
$levels = get_option('ihc_levels');
//ihc_save_update_metas('level_restrict_payment');//save update metas
if (!empty($_POST['ihc_save'])){
	update_option('ihc_level_restrict_payment_enabled', $_POST['ihc_level_restrict_payment_enabled']);
	$ihc_level_restrict_payment_values = array();
	$ihc_levels_default_payments = array();
	foreach ($levels as $id=>$level){
		$ihc_level_restrict_payment_values[$id] = (isset($_POST['ihc_level_restrict_payment_values'][$id])) ? $_POST['ihc_level_restrict_payment_values'][$id] : '';
		$ihc_levels_default_payments[$id] = (isset($_POST['ihc_levels_default_payments'][$id])) ? $_POST['ihc_levels_default_payments'][$id] : '';
	}
	update_option('ihc_level_restrict_payment_values', $ihc_level_restrict_payment_values);
	update_option('ihc_levels_default_payments', $ihc_levels_default_payments);
}
$data['metas'] = ihc_return_meta_arr('level_restrict_payment');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
$default_payment = get_option('ihc_payment_selected');
$payments = ihc_get_active_payments_services();
?>
<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php _e('Level - Payment Gateways restriction', 'ihc');?></h3>
		<div class="inside">
			
			<div class="iump-form-line">
				<h2><?php _e('Activate/Hold', 'ihc');?></h2>
				<p><?php _e('Restrict for each Level to be paid only with specific Payment Gateways. For example, You can provide Bank Transfer payment option only for specific Levels or for identical Level but with a higher price.', 'ihc');?></p>							
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['ihc_level_restrict_payment_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_level_restrict_payment_enabled');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_level_restrict_payment_enabled" value="<?php echo $data['metas']['ihc_level_restrict_payment_enabled'];?>" id="ihc_level_restrict_payment_enabled" /> 												
			</div>	
			
			<div class="ihc-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>				
			
		</div>	
	</div>
			
			<?php if ($levels):?>
				<?php foreach ($levels as $id=>$level):?>
					<?php 
						/// ONLY PAID LEVELS
						if ($level['payment_type']=='free'){
							continue;
						}
					
						if (!empty($data['metas']['ihc_levels_default_payments'][$id])){
							$default_payment_for_level = $data['metas']['ihc_levels_default_payments'][$id];  	
						} else {
							$default_payment_for_level = -1;
						}
						$temp_payments = $payments;
						unset($temp_payments[$default_payment]);
						$current_default_label = $payments[$default_payment];
					?>
					
		<div class="ihc-stuffbox">
				<h3 class="ihc-h3"><?php echo __('Level: ', 'ihc') . $level['label'];?></h3>
			<div class="inside">									
				<div class="iump-form-line">
					<h2></h2>
					<div>
						<h4><?php _e('Default Payment:', 'ihc');?></h4>
						<select name="ihc_levels_default_payments[<?php echo $id;?>]">
							<option value="-1" <?php if ($k==-1) echo 'selected';?> ><?php echo __('Current Default Payment ', 'ihc') . '(' . $current_default_label . ')';?></option>
							<?php foreach ($temp_payments as $k=>$v):?>
								<?php $selected = ($k==$default_payment_for_level) ? 'selected' : '';?>
								<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
							<?php endforeach;?>
						</select>
					</div>
				</div>		
				<div class="iump-form-line">
					<?php 						
						if (isset($data['metas']['ihc_level_restrict_payment_values'][$id])){
							$excluded_values = $data['metas']['ihc_level_restrict_payment_values'][$id];
							$excluded_values_array = explode(',', $excluded_values);
						} else {
							$excluded_values = '';
							$excluded_values_array = array();
						}				
					?>
						<h4><?php _e('Payments Available:', 'ihc');?></h4>
						<?php foreach ($payments as $k=>$v):?>
							<?php $checked = (!in_array($k, $excluded_values_array)) ? 'checked' : '';?>
							<div class="ihc-inline-block-item">
								<input type="checkbox" onClick="ihc_add_to_hidden_when_uncheck(this, '<?php echo $k;?>', '<?php echo '#' . $id . 'excludedforlevel';?>');" <?php echo $checked;?> />	
								<img src="<?php echo IHC_URL . 'assets/images/'.$k.'.png';?>" class="ihc-payment-icon ihc-payment-select-img-selected" style="margin:0 14px 0 5px;   width: auto !important; height:35px;" />							
							</div>
						<?php endforeach;?>
						<input type="hidden" name="ihc_level_restrict_payment_values[<?php echo $id;?>]" value="<?php echo $excluded_values;?>" id="<?php echo $id . 'excludedforlevel';?>"/>
				</div>	
																				
				<div class="ihc-submit-form" style="margin-top: 20px;"> 
					<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
				</div>						
			</div>
		</div>								
				<?php endforeach;?>				
			<?php endif;?>
</form>