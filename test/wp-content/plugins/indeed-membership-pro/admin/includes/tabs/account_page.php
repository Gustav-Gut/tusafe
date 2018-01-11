<?php 
echo ihc_inside_dashboard_error_license();
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
if (isset($_POST['ihc_save'])){
	//update/save
	ihc_save_update_metas('account_page');
}
$meta_arr = ihc_return_meta_arr('account_page');

?>
<div class="iump-page-title">Ultimate Membership Pro - 
							<span class="second-text">
								<?php _e('Account Page', 'ihc');?>
							</span>
						</div>
			<div class="ihc-stuffbox">
				<div class="impu-shortcode-display">
					[ihc-user-page]
				</div>
			</div>		
<div class="metabox-holder indeed">			
<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3><?php _e('Top Section:', 'ihc');?></h3>
		<div class="inside">
		
			<div class="iump-register-select-template" style="padding:20px 0 35px 20px;">
				<?php _e('Select Template:', 'ihc');?>
				<select name="ihc_ap_top_template"  style="min-width:300px; margin-left:10px;"><?php 
					$themes = array(
											'ihc-ap-top-theme-1' => '(#1) '.__('Basic Full Background Theme', 'ihc'),
											'ihc-ap-top-theme-2' => '(#2) '.__('Square Top Image Theme', 'ihc'),
											'ihc-ap-top-theme-3' => '(#3) '.__('Rounded Big Image Theme', 'ihc'),	
					);
					foreach ($themes as $k=>$v){
						?>
						<option value="<?php echo $k;?>" <?php if ($meta_arr['ihc_ap_top_template']==$k) echo 'selected';?> ><?php echo $v;?></option>
						<?php 
					}
				?></select>
			</div>	
			
			<div class="inside">
			
				
				<div>
					
					<label class="iump_label_shiwtch iump-onbutton">
						<?php $checked = ($meta_arr['ihc_ap_edit_show_avatar']) ? 'checked' : '';?>
						<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_ap_edit_show_avatar');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" value="<?php echo $meta_arr['ihc_ap_edit_show_avatar'];?>" name="ihc_ap_edit_show_avatar" id="ihc_ap_edit_show_avatar" /> 						
					<label><?php _e('Show Avatar Image:', 'ihc');?></label>
				</div>
				
				<div>
					<label class="iump_label_shiwtch iump-onbutton">
						<?php $checked = ($meta_arr['ihc_ap_edit_show_level']) ? 'checked' : '';?>
						<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_ap_edit_show_level');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" value="<?php echo $meta_arr['ihc_ap_edit_show_level'];?>" name="ihc_ap_edit_show_level" id="ihc_ap_edit_show_level" /> 
					<label><?php _e('Show Level:', 'ihc');?></label>				
				</div>
				<br/>
				<h4><?php _e('Welcome Message:', 'ihc');?></h4>
				<div class="iump-wp_editor" style="float:left; width: 60%;">
				<?php wp_editor(stripslashes($meta_arr['ihc_ap_welcome_msg']), 'ihc_ap_welcome_msg', array('textarea_name'=>'ihc_ap_welcome_msg', 'editor_height'=>200));?>
				</div>
				<div style="width: 19%; display: inline-block; vertical-align: top;margin-left: 10px; color: #333;">
				<h4><?php _e('Regular constants', 'uap');?></h4>
					<?php 
						$constants = array( '{username}'=>'', 
											'{user_email}'=>'', 
											'{first_name}'=>'', 
											'{last_name}'=>'', 
											'{account_page}'=>'', 
											'{login_page}'=>'', 
											'{level_list}'=>'',	
											'{blogname}'=>'', 
											'{blogurl}'=>'',
											'{ihc_avatar}' => '',
											'{current_date}' => '',
											'{user_registered}' => '',
											'{flag}' => '',
						);
						$extra_constants = ihc_get_custom_constant_fields();
						foreach ($constants as $k=>$v){
							?>
							<div><?php echo $k;?></div>
							<?php 	
						}
						?>
						</div>
						<div style="width: 19%; display: inline-block; vertical-align: top; margin-left: 10px; color: #333;">
							<h4><?php _e('Custom Fields constants', 'uap');?></h4>
						<?php 
						foreach ($extra_constants as $k=>$v){
							?>
							<div><?php echo $k;?></div>
							<?php 	
						}
					?>
				</div>				
				<div class="ihc-clear"></div>
				

				<div class="input-group">
					<h2><?php _e('Background/Banner Image:', 'ihc');?></h2>
					<p><?php _e('The Cover or Background Image, based on what Theme have you chosen', 'ihc');?></p>						
					<label class="iump_label_shiwtch  iump-onbutton">
						<?php if (!isset($meta_arr['ihc_ap_edit_background'])) $meta_arr['ihc_ap_edit_background'] = 1; ?>
						<?php $checked = ($meta_arr['ihc_ap_edit_background']==1) ? 'checked' : '';?>
						<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_ap_edit_background');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
					</label>	
					<input type="hidden" name="ihc_ap_edit_background" value="<?php echo $meta_arr['ihc_ap_edit_background'];?>" id="ihc_ap_edit_background"/>					
					
						
				<div class="form-group" style="margin:20px 0 10px 10px">
					<input type="text" class="form-control" onClick="open_media_up(this);" value="<?php  echo $meta_arr['ihc_ap_top_background_image'];?>" name="ihc_ap_top_background_image" id="ihc_ap_top_background_image" style="width: 90%;display: inline; float:none; min-width:500px;"/>
					<i class="fa-ihc ihc-icon-remove-e" onclick="jQuery('#ihc_ap_top_background_image').val('');" title="<?php _e('Remove Background Image', 'ihc');?>"></i>
				</div>		
				</div>	
				<div class="ihc-wrapp-submit-bttn">
					<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large"  style="min-width:50px;" />
				</div>	
						
			</div>	
		</div>			
	</div>
	
	<div class="ihc-stuffbox">
		<h3><?php _e('Content Section:', 'ihc');?></h3>
			  <div class="inside">
			  	
				<div class="iump-register-select-template" style="padding:20px 0 35px 20px;">
					<?php _e('Select Template:', 'ihc');?>
					<select name="ihc_ap_theme"  style="min-width:300px; margin-left:10px;"><?php 
						$themes = array(
												'ihc-ap-theme-1' => '(#1) '.__('Blue New Theme', 'ihc'),
												'ihc-ap-theme-2' => '(#2) '.__('Dark Theme', 'ihc'),	
						);
						foreach ($themes as $k=>$v){
							?>
							<option value="<?php echo $k;?>" <?php if ($meta_arr['ihc_ap_theme']==$k) echo 'selected';?> ><?php echo $v;?></option>
							<?php 
						}
					?></select>
				</div>			  	

				<h2 style="font-size:22px;"><?php _e('Menu Tabs:', 'ihc');?></h2>
				<?php 
					$available_tabs = array(
											'overview'=>__('Overview', 'ihc'),
											'profile'=>__('Profile', 'ihc'),											
											'subscription'=>__('Subscription', 'ihc'),
											'social'=>__('Social Plus', 'ihc'),
											'orders' => __('Orders', 'ihc'),
											'transactions'=>__('Transactions', 'ihc'),
											'membeship_gifts' => __('Membership Gifts', 'ihc'),
											'membership_cards' => __('Membership Cards', 'ihc'),
											'help' => __('Help', 'ihc'),
											'logout' => __('LogOut', 'ihc'),
					);
					if (!ihc_is_magic_feat_active('gifts')){
						unset($available_tabs['membeship_gifts']);
					}
					if (!ihc_is_magic_feat_active('membership_card')){
						unset($available_tabs['membership_cards']);
					}
					$tabs = explode(',', $meta_arr['ihc_ap_tabs']);
					?>
						<div class="ihc-ap-tabs-list">
							<?php foreach ($available_tabs as $k=>$v):?>						
								<div class="ihc-ap-tabs-list-item" onClick="ihc_ap_make_visible('<?php echo $k;?>', this);" id="<?php echo 'ihc_tab-' . $k;?>"><?php echo $v;?></div>	
							<?php endforeach;?>
							<div class="ihc-clear"></div>
						</div>				
					<?php

					foreach ($available_tabs as $k=>$v){
						?>
						
							<div class="ihc-ap-tabs-settings-item" id="<?php echo 'ihc_tab_item_' . $k;?>" style="margin-top:20px;">
								<h4><?php echo $v;?></h4>
								<div style="margin: 7px 0px;">
									<span class="iump-labels-onbutton" style="  min-width:100px;"><?php _e('Activate the Tab:', 'uap');?></span>
									<label class="iump_label_shiwtch  iump-onbutton">
										<?php $checked = (in_array($k, $tabs)) ? 'checked' : '';?>
										<input type="checkbox" class="iump-switch" onClick="ihc_make_inputh_string(this, '<?php echo $k;?>', '#ihc_ap_tabs');" <?php echo $checked;?> />
										<div class="switch" style="display:inline-block;"></div>
									</label>						
								</div>								
									
									<?php 
										if (empty($meta_arr['ihc_ap_' . $k . '_menu_label'])){
											$meta_arr['ihc_ap_' . $k . '_menu_label'] = '';
										}
									?>
									<div class="input-group" style="max-width:40%;">
										<span class="input-group-addon" id="basic-addon1"><?php _e('Menu Label', 'uap');?></span>
										<input type="text" class="form-control" placeholder="" value="<?php echo $meta_arr['ihc_ap_' . $k . '_menu_label'];?>" name="<?php echo 'ihc_ap_' . $k . '_menu_label';?>">
									</div>			
									
									<?php if (isset($meta_arr['ihc_ap_' . $k . '_title'])):?>												
										<div class="input-group" style="max-width:40%;">
											<span class="input-group-addon" id="basic-addon1"><?php _e('Title', 'uap');?></span>
											<input type="text" class="form-control" placeholder="" value="<?php echo $meta_arr['ihc_ap_' . $k . '_title'];?>" name="<?php echo 'ihc_ap_' . $k . '_title';?>">
										</div>
									<?php endif;?>
									
									<?php if (isset($meta_arr['ihc_ap_' . $k . '_msg'])):?>
										<div style="margin-top:20px;">
											<div style="width: 60%; display: inline-block; vertical-align: top; box-sizing:border-box;"><?php 
												wp_editor(stripslashes($meta_arr['ihc_ap_' . $k . '_msg']), 'uap_tab_' . $k . '_msg', array('textarea_name' => 'ihc_ap_' . $k . '_msg', 'editor_height'=>200));
											?></div>	
											<div style="width: 19%; display: inline-block; vertical-align: top; padding-left: 10px; box-sizing:border-box; color: #333;">
												<?php 
													echo "<h4>" . __('Regular constants', 'ihc') . "</h4>";
													foreach ($constants as $key=>$val){
														?>
														<div><?php echo $key;?></div>
														<?php 	
													}
											?>
											</div>
											<div style="width: 19%; display: inline-block; vertical-align: top; padding-left: 10px; box-sizing:border-box; color: #333;">
												<?php
													echo "<h4>".__('Custom Fields constants', 'ihc')."</h4>";
													foreach ($extra_constants as $key=>$val){
														?>
														<div><?php echo $key;?></div>
														<?php 	
													}
												?>
											</div>																
										</div>
									<?php endif;?>
									
									<?php if ($k=='subscription'):?>
										<div style="margin: 7px 12px;">
											<span class="iump-labels-onbutton" style="  min-width:100px;"><?php _e('Display Subscription Details Table:', 'uap');?></span>
											<label class="iump_label_shiwtch  iump-onbutton">
												<?php if (!isset($meta_arr['ihc_ap_subscription_table_enable'])) $meta_arr['ihc_ap_subscription_table_enable'] = 1; ?>
												<?php $checked = ($meta_arr['ihc_ap_subscription_table_enable']==1) ? 'checked' : '';?>
												<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_ap_subscription_table_enable');" <?php echo $checked;?> />
												<div class="switch" style="display:inline-block;"></div>
											</label>	
											<input type="hidden" name="ihc_ap_subscription_table_enable" value="<?php echo $meta_arr['ihc_ap_subscription_table_enable'];?>" id="ihc_ap_subscription_table_enable"/>					
										</div>	
										<div style="margin: 7px 12px;">
											<span class="iump-labels-onbutton" style="  min-width:100px;"><?php _e('Display Subscription Plan:', 'uap');?></span>
											<label class="iump_label_shiwtch  iump-onbutton">
												<?php if (!isset($meta_arr['ihc_ap_subscription_plan_enable'])) $meta_arr['ihc_ap_subscription_plan_enable'] = 1; ?>
												<?php $checked = ($meta_arr['ihc_ap_subscription_plan_enable']==1) ? 'checked' : '';?>
												<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_ap_subscription_plan_enable');" <?php echo $checked;?> />
												<div class="switch" style="display:inline-block;"></div>
											</label>
											<input type="hidden" name="ihc_ap_subscription_plan_enable" value="<?php echo $meta_arr['ihc_ap_subscription_plan_enable'];?>" id="ihc_ap_subscription_plan_enable"/>							
										</div>																					
									<?php endif;?>

							</div>		
											
						<?php
						
						////

					}
				?>
					<input type="hidden" value="<?php echo $meta_arr['ihc_ap_tabs'];?>" id="ihc_ap_tabs" name="ihc_ap_tabs" />
	
					<div class="ihc-wrapp-submit-bttn">
						<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large"  style="min-width:50px;" />
					</div>					
				
			   </div>
									

	</div>
	
	<div class="ihc-stuffbox">
		<h3><?php _e('Footer Section:', 'ihc');?></h3>
		<div class="inside">
			<h2><?php _e('Footer Content:', 'ihc');?></h2>
			<div style="margin-top:20px;">
				<div style="width: 60%; display: inline-block; vertical-align: top; box-sizing:border-box;"><?php 
					wp_editor(stripslashes($meta_arr['ihc_ap_footer_msg']), 'ihc_ap_footer_msg', array('textarea_name' => 'ihc_ap_footer_msg', 'editor_height'=>200));
				?></div>	
				<div style="width: 19%; display: inline-block; vertical-align: top; padding-left: 10px; box-sizing:border-box; color: #333;">
					<?php 
						echo "<h4>" . __('Regular constants', 'ihc') . "</h4>";
						foreach ($constants as $k=>$v){
						?>
							<div><?php echo $k;?></div>
						<?php 	
						}
					?>
				</div>
				<div style="width: 19%; display: inline-block; vertical-align: top; padding-left: 10px; box-sizing:border-box; color: #333;">
					<?php
						echo "<h4>".__('Custom Fields constants', 'ihc')."</h4>";
						foreach ($extra_constants as $k=>$v){
						?>
							<div><?php echo $k;?></div>
						<?php 	
						}
					?>
				</div>																
			</div>	
			<div class="ihc-wrapp-submit-bttn">
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large"  style="min-width:50px;" />
			</div>						
		</div>	
	</div>	
	
	<div class="ihc-stuffbox">
		<h3><?php _e('Additional Settings:', 'ihc');?></h3>
		<div class="inside">
			<div class="iump-form-line">
				<h2><?php _e('Custom CSS:', 'ihc');?></h2>
				<textarea id="ihc_account_page_custom_css"  name="ihc_account_page_custom_css" class="ihc-dashboard-textarea-full"  style="max-width:80%;"><?php echo $meta_arr['ihc_account_page_custom_css'];?></textarea>
			</div>				
			<div class="ihc-wrapp-submit-bttn">
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large"  style="min-width:50px;" />
			</div>				
		</div>
	</div>
		
</form>
</div>
<script>
jQuery(document).ready(function(){
	ihc_ap_make_visible('overview', '#uap_tab-overview');
});
</script>