<?php
ihc_save_update_metas('ihc_membership_card');//save update metas
$data['metas'] = ihc_return_meta_arr('ihc_membership_card');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
$levels = get_option('ihc_levels');

?>
<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php _e('Membership Card', 'ihc');?></h3>
		<div class="inside">
			
			<div class="iump-form-line">
				<h2><?php _e('Activate/Hold', 'ihc');?></h2>					
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['ihc_membership_card_enable']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_membership_card_enable');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_membership_card_enable" value="<?php echo $data['metas']['ihc_membership_card_enable'];?>" id="ihc_membership_card_enable" /> 												
			</div>			

			<div class="iump-form-line">
				<h2><?php _e('Template', 'ihc');?></h2>	
				<select name="ihc_membership_card_template"><?php
					$templates = array(
										'ihc-membership-card-1' => __('Template 1', 'ihc'),
										'ihc-membership-card-2' => __('Template 2', 'ihc'),
										'ihc-membership-card-3' => __('Template 3', 'ihc'),
					);
					foreach ($templates as $k=>$v):
						?>
						<option value="<?php echo $k;?>" <?php if ($k==$data['metas']['ihc_membership_card_template']) echo 'selected'; ?> ><?php echo $v;?></option>
						<?php
					endforeach;
				?></select>				
			</div>	

			<div class="iump-form-line">
				<h2><?php _e('Card Size', 'ihc');?></h2>	
				<select name="ihc_membership_card_size"><?php
					$templates = array(
										'ihc-membership-card-small' => __('Small', 'ihc'),
										'ihc-membership-card-medium' => __('Medium', 'ihc'),
										'ihc-membership-card-large' => __('Large', 'ihc'),
					);
					foreach ($templates as $k=>$v):
						?>
						<option value="<?php echo $k;?>" <?php if ($k==$data['metas']['ihc_membership_card_size']) echo 'selected';?> ><?php echo $v;?></option>
						<?php
					endforeach;
				?></select>				
			</div>
			
			<div class="row" style="margin-left:0px;">
			<div class="col-xs-5">			
			<div class="iump-form-line">
				<h2><?php _e('Image', 'ihc');?></h2>				
				<input type="text" style="width: 100%;" name="ihc_membership_card_image" value="<?php echo $data['metas']['ihc_membership_card_image'];?>" onClick="open_media_up(this);" />		
			</div>				
			</div>	
			</div>
			
			<div class="row" style="margin-left:0px;">
						<div class="col-xs-3">
				<div class="iump-form-line">		
				<h2><?php _e('Member Since', 'ihc');?></h2>					
						<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
							<?php $checked = ($data['metas']['ihc_membership_member_since_enable']) ? 'checked' : '';?>
							<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_membership_member_since_enable');" <?php echo $checked;?> />
							<div class="switch" style="display:inline-block;"></div>
						</label>
						<input type="hidden" name="ihc_membership_member_since_enable" value="<?php echo $data['metas']['ihc_membership_member_since_enable'];?>" id="ihc_membership_member_since_enable" /> 	
						<div class="input-group" style="margin:30px 0 5px 0;">
							<span class="input-group-addon" id="basic-addon1"><?php _e('Member since label:', 'ihc');?></span>
							<input type="text" class="form-control" name="ihc_membership_member_since_label" value="<?php echo $data['metas']['ihc_membership_member_since_label'];?>" />
						</div>	
				</div>
			</div>	
			</div>	
			
			<div class="row" style="margin-left:0px;">
						<div class="col-xs-3">
			<div class="iump-form-line">
				<h2><?php _e('Level', 'ihc');?></h2>	
						<div class="input-group" style="margin:30px 0 5px 0;">
							<span class="input-group-addon" id="basic-addon1"><?php _e('Label:', 'ihc');?></span>
							<input type="text" class="form-control" name="ihc_membership_member_level_label" value="<?php echo $data['metas']['ihc_membership_member_level_label'];?>" />
						</div>		
			</div>	
			</div>	
			</div>
			
			<div class="row" style="margin-left:0px;">
						<div class="col-xs-3">
			<div class="iump-form-line">
				<h2><?php _e('Level Expire', 'ihc');?></h2>									
							<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['ihc_membership_member_level_expire']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_membership_member_level_expire');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="ihc_membership_member_level_expire" value="<?php echo $data['metas']['ihc_membership_member_level_expire'];?>" id="ihc_membership_member_level_expire" /> 	
							<div class="input-group" style="margin:30px 0 5px 0;">
								<span class="input-group-addon" id="basic-addon1"><?php _e('Label:', 'ihc');?></span>
								<input type="text" class="form-control" name="ihc_membership_member_level_expire_label" value="<?php echo $data['metas']['ihc_membership_member_level_expire_label'];?>">
							</div>	
			</div>										
			</div>	
			</div>
				
			<div class="iump-form-line">
				<h2><?php _e('Custom CSS', 'ihc');?></h2>	
				<textarea name="ihc_membership_card_custom_css" style="width: 100%; height: 150px;"><?php echo $data['metas']['ihc_membership_card_custom_css'];?></textarea>
			</div>	
			
			<?php if (!empty($levels)):?>		
				<div class="iump-form-line">
					<h2><?php _e('Show only for: ', 'ihc');?></h2>
					<?php $exclude_vals = explode(',', $data['metas']['ihc_membership_card_exclude_levels']);?>
					<?php foreach ($levels as $lid=>$level_arr):?>
					<div style="margin: 0px 4px; margin-right:12px; display: inline-block; vertical-align: top; font-weight:bold;">
						<?php $checked = (in_array($lid, $exclude_vals)) ? '' : 'checked';?>
						<input type="checkbox" <?php echo $checked;?> onClick="ihc_add_to_hidden_when_uncheck(this, '<?php echo $lid;?>', '#ihc_membership_card_exclude_levels');" /> <?php echo $level_arr['label'];?>
					</div>								
					<?php endforeach;?>
				</div>		
			<?php endif;?>
			<input type="hidden" name="ihc_membership_card_exclude_levels" value="<?php echo $data['metas']['ihc_membership_card_exclude_levels'];?>" id="ihc_membership_card_exclude_levels" />
					
			<div class="ihc-user-list-shortcode-wrapp">
				<div class="content-shortcode">
					<span class="the-shortcode" style="font-size: 16px;"><?php _e('Shortcode: ', 'ihc');?> [ihc-membership-card]</span>
				</div>						
			</div>

											
			<div class="ihc-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>		
					
		</div>
	</div>
</form>