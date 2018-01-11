<?php
$levels = get_option('ihc_levels');
//ihc_save_update_metas('level_restrict_payment');//save update metas
if (!empty($_POST['ihc_save'])){
	update_option('ihc_level_subscription_plan_settings_enabled', $_POST['ihc_level_subscription_plan_settings_enabled']);
	update_option('ihc_show_renew_link', $_POST['ihc_show_renew_link']);
	update_option('ihc_show_delete_link', $_POST['ihc_show_delete_link']);
	/// RESTRICT LEVELs
	$restrict_arr = array();
	$conditions = array();
	foreach ($levels as $id=>$level){
		$restrict_arr[$id] = (isset($_POST['ihc_level_subscription_plan_settings_restr_levels'][$id])) ? $_POST['ihc_level_subscription_plan_settings_restr_levels'][$id] : '';
		$conditions[$id] = (isset($_POST['ihc_level_subscription_plan_settings_condt'][$id])) ? $_POST['ihc_level_subscription_plan_settings_condt'][$id] : '';
	}
	update_option('ihc_level_subscription_plan_settings_restr_levels', $restrict_arr);	
	update_option('ihc_level_subscription_plan_settings_condt', $conditions);
}
$data['metas'] = ihc_return_meta_arr('level_subscription_plan_settings');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
?>
<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php _e('Level - Subscription Plan Display Details', 'ihc');?></h3>
		<div class="inside">
			
			<div class="iump-form-line">
				<h2><?php _e('Activate/Hold this feature', 'ihc');?></h2>
				<p><?php _e('Restrict a Level to be bought if the User didnt bought a Level yet or has already bought specific Levels. The Module will take of the active Level from Subscription Plan if a restriction for that Level was set.', 'ihc');?></p>							
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['ihc_level_subscription_plan_settings_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_level_subscription_plan_settings_enabled');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_level_subscription_plan_settings_enabled" value="<?php echo $data['metas']['ihc_level_subscription_plan_settings_enabled'];?>" id="ihc_level_subscription_plan_settings_enabled" /> 												
				<p style="font-weight:bold;"><?php _e('Important: the Level have to be default set to Show into Subscriptions Plan to be managed during this filter.', 'ihc');?></p>	
				
			</div>	
			
				<div class="iump-form-line">
					<label> <?php _e("Show Level Renew Link:", 'ihc');?></label>
					<div>
						<label class="iump_label_shiwtch" >
							<?php $checked = ($data['metas']['ihc_show_renew_link']) ? 'checked' : '';?>
							<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_show_renew_link');" <?php echo $checked;?> />
							<div class="switch" style="display:inline-block;"></div>
						</label>
						<input type="hidden" name="ihc_show_renew_link" value="<?php echo $data['metas']['ihc_show_renew_link'];?>" id="ihc_show_renew_link" /> 				
					</div>
				</div>	

				<div class="iump-form-line">
					<label> <?php _e("Show Level Delete Link:", 'ihc');?></label>
					<div>
						<label class="iump_label_shiwtch">
							<?php $checked = ($data['metas']['ihc_show_delete_link']) ? 'checked' : '';?>
							<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_show_delete_link');" <?php echo $checked;?> />
							<div class="switch" style="display:inline-block;"></div>
						</label>
						<input type="hidden" name="ihc_show_delete_link" value="<?php echo $data['metas']['ihc_show_delete_link'];?>" id="ihc_show_delete_link" /> 		
					</div>
				</div>			
			
			<div class="ihc-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>		
					
		</div>	
	</div>
	
	<?php if ($levels):?>
		<?php foreach ($levels as $id=>$level):?>
			<div class="ihc-stuffbox">
				
					<h3 class="ihc-h3"><?php echo __('Level: ', 'ihc') . $level['label'];?></h3>
				<div class="inside">									
					<div class="iump-form-line">
						<label><?php _e('Activate Restriction for this level', 'ihc');?></label>
						<div>							
							<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = (empty($data['metas']['ihc_level_subscription_plan_settings_restr_levels'][$id])) ? '' : 'checked';?>
								<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '<?php echo '#ihc_level_subscription_plan_settings_restr_levels'.$id;?>');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<?php $hidden_value = (empty($data['metas']['ihc_level_subscription_plan_settings_restr_levels'][$id])) ? 0 : $data['metas']['ihc_level_subscription_plan_settings_restr_levels'][$id];?>
							<input type="hidden" name="ihc_level_subscription_plan_settings_restr_levels[<?php echo $id;?>]" value="<?php echo $hidden_value;?>" id="<?php echo 'ihc_level_subscription_plan_settings_restr_levels'.$id;?>" /> 												
						</div>
					</div>		

					<?php 
						$hidden_value = (empty($data['metas']['ihc_level_subscription_plan_settings_condt'][$id])) ? '' : $data['metas']['ihc_level_subscription_plan_settings_condt'][$id];
						$hidden_arr = array();
						if (!empty($hidden_value)){
							$hidden_arr = explode(',', $hidden_value);
						}
					?>					
					<div class="iump-form-line">
						<h4><?php _e('User never bought:', 'ihc');?></h4>
						<div>
							<?php $checked = (in_array('unreg', $hidden_arr)) ? 'checked' : '';?>
							<input type="checkbox" <?php echo $checked;?> onClick="ihc_make_inputh_string(this, 'unreg', '<?php echo '#level' . $id . 'cond';?>');" /><span style="font-weight:bold; vertical-align:bottom;"> <?php _e('UnRegistered Users', 'ihc');?></span>
						</div>		
						<div>
							<?php $checked = (in_array('no_pay', $hidden_arr)) ? 'checked' : '';?>
							<input type="checkbox" <?php echo $checked;?> onClick="ihc_make_inputh_string(this, 'no_pay', '<?php echo '#level' . $id . 'cond';?>');" /><span style="font-weight:bold; vertical-align:bottom;"> <?php _e('Registered Users with no payment made', 'ihc');?></span>
						</div>		
						<h4 style="margin-top: 24px;"><?php _e('User already bought: ', 'ihc');?></h4>
						<?php foreach ($levels as $lid=>$larr):?>
							<?php $spanclass = ($lid==$id) ? 'ihc-magic-feat-bold-span' : '';?> 
							<div style="margin: 0px 4px; margin-right:12px; display: inline-block; vertical-align: top; font-weight:bold;">
								<?php $checked = (in_array($lid, $hidden_arr)) ? 'checked' : '';?>
								<input type="checkbox" <?php echo $checked;?> onClick="ihc_make_inputh_string(this, '<?php echo $lid;?>', '<?php echo '#level' . $id . 'cond';?>');" /> <span style="vertical-align: bottom;" class="<?php echo $spanclass;?>"><?php echo $larr['label'];?></span>
							</div>									
						<?php endforeach;?>									
						<input type="hidden" name="ihc_level_subscription_plan_settings_condt[<?php echo $id;?>]" id="<?php echo 'level' . $id . 'cond';?>" value="<?php echo $hidden_value;?>" />
					</div>		
								
					<div class="ihc-submit-form" style="margin-top: 20px;"> 
						<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
					</div>						
				</div>
			</div>								
		<?php endforeach;?>				
	<?php endif;?>	
	
</form>