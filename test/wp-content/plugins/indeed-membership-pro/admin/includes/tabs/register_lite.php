<?php
ihc_save_update_metas('register_lite');//save update metas
$data['metas'] = ihc_return_meta_arr('register_lite');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
$pages_arr = array(-1 => '...') + ihc_get_all_pages() + ihc_get_redirect_links_as_arr_for_select();
?>
<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php _e('Register Lite', 'ihc');?></h3>
		<div class="inside">
			
			<div class="iump-form-line">
				<h2><?php _e('Activate/Hold', 'ihc');?></h2>					
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['ihc_register_lite_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_register_lite_enabled');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_register_lite_enabled" value="<?php echo $data['metas']['ihc_register_lite_enabled'];?>" id="ihc_register_lite_enabled" /> 												
			</div>			
				
			<div class="iump-register-select-template" style="padding: 30px;">
				<?php 
					$templates = array('ihc-register-1'=>'(#1) '.__('Standard Theme', 'ihc'), 'ihc-register-2'=>'(#2) '.__('Basic Theme', 'ihc'), 'ihc-register-3'=>'(#3) '.__('Blue Box Theme', 'ihc'),
													'ihc-register-4'=>'(#4) '.__('PlaceHolder Theme', 'ihc'), 'ihc-register-5'=>'(#5) '.__('Strong Theme', 'ihc'), 'ihc-register-6'=>'(#6) '.__('Double Strong Theme', 'ihc'), 
													'ihc-register-7'=>'(#7) '.__('BackBox Theme', 'ihc'), 'ihc-register-8'=>'(#8) '.__('Simple Border Theme', 'ihc'), 'ihc-register-9'=>'(#9) '.__('Radius Theme', 'ihc'),
													'ihc-register-10'=>'(#10) '.__('BootStrap Theme', 'ihc'),'ihc-register-11'=>'(#11) '.__('Double Simple Border Theme', 'ihc'), 'ihc-register-12'=>'(#12) '.__('Dobule Radius Theme', 'ihc'),
													'ihc-register-13'=>'(#13) '.__('Double BootStrap Theme', 'ihc'));
				?>
				<?php _e('Register Template:', 'ihc');?>
				<select name="ihc_register_lite_template" id="ihc_register_lite_template" style="min-width:400px">
					<?php 
						foreach ($templates as $k=>$v){
						?>
							<option value="<?php echo $k;?>" <?php if ($k==$data['metas']['ihc_register_lite_template']) echo 'selected';?> >
								<?php echo $v;?>
							</option>
						<?php 	
						}
						?>
				</select>						
			</div>
		
			<div class="iump-form-line">						
				<h2><?php _e('WP Role', 'ihc');?></h2>
				<div style="font-weight:bold"><?php _e('Predefined Wordpress Role Assign to new Users:', 'ihc');?></div>
					<select name="ihc_register_lite_user_role">
					<?php 
						$roles = ihc_get_wp_roles_list();
						if ($roles){
							foreach ($roles as $k=>$v){
								$selected = ($data['metas']['ihc_register_lite_user_role']==$k) ? 'selected' : '';
								?>
									<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
								<?php 
							}	
						}
					?>
					</select>
					<p><?php _e('If "Pending" Role is set, the User is not able to Login until the Admin manually Approve it.', 'ihc');?></p>	
			</div>													
						<div class="iump-form-line">
							<h2><?php _e('Opt-In Subscription', 'ihc');?></h2>
							
								<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
									<?php $checked = ($data['metas']['ihc_register_lite_opt_in']) ? 'checked' : '';?>
									<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_register_lite_opt_in');" <?php echo $checked;?> />
									<div class="switch" style="display:inline-block;"></div>
								</label>
								<input type="hidden" name="ihc_register_lite_opt_in" value="<?php echo $data['metas']['ihc_register_lite_opt_in'];?>" id="ihc_register_lite_opt_in" /> 	
								<?php _e('Enable Opt-In', 'ihc');?>		
								<div style="margin-top:10px;">
								<div style="font-weight: bold;"><?php _e('Opt-In Destination:', 'ihc');?></div>
                                <select name="ihc_register_lite_opt_in_type">
                                    <?php
                                        $subscribe_types = array(
                                                                    'active_campaign' => 'Active Campaign',
                                                                    'aweber' => 'AWeber',
                                                                    'campaign_monitor' => 'CampaignMonitor',
                                                                    'constant_contact' => 'Constant Contact',
                                                                    'email_list' => __('E-mail List', 'ihc'),
                                                                    'get_response' => 'GetResponse',
                                                                    'icontact' => 'IContact',
                                                                    'madmimi' => 'Mad Mimi',
                                                                    'mailchimp' => 'MailChimp',
                                                                    'mymail' => 'MyMail',
                                                                    'wysija' => 'Wysija',
                                                                 );
                                        foreach ($subscribe_types as $k=>$v){
                                            $selected = ($data['metas']['ihc_register_lite_opt_in_type']==$k) ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo $k;?>" <?php echo $selected;?> ><?php 
                                                	echo $v;
                                                ?></option>
                                            <?php
                                        }
                                    ?>
                                </select>
							</div>					
							<p><?php _e('The User email address is sent to your OptIn Destination', 'ihc');?></p>
						</div>
						<div class="iump-form-line">
							<h2><?php _e('Double Email Verification', 'ihc');?></h2>
							<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
									<?php $checked = ($data['metas']['ihc_register_lite_double_email_verification']) ? 'checked' : '';?>
									<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_register_lite_double_email_verification');" <?php echo $checked;?> />
									<div class="switch" style="display:inline-block;"></div>
								</label>
								<input type="hidden" name="ihc_register_lite_double_email_verification" value="<?php echo $data['metas']['ihc_register_lite_double_email_verification'];?>" id="ihc_register_lite_double_email_verification" /> 	
								<?php _e('Double E-mail Verification', 'ihc');?>
												
							<p><?php _e('Be sure that your Notifications for <strong>Double Email Verification</strong> are properly set. Also, check Settings from General Options tab.', 'ihc');?> <a href="admin.php?page=ihc_manage&tab=general&subtab=double_email_verification" target="_blank">here</a></p>	
						</div>

			<div style="margin-bottom: 15px;">							
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['ihc_register_lite_auto_login']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_register_lite_auto_login');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_register_lite_auto_login" value="<?php echo $meta_arr['ihc_register_lite_auto_login'];?>" id="ihc_register_lite_auto_login" /> 	
				<?php _e('Auto Login after Registration', 'ihc');?>							
			</div>
			
			<div class="iump-form-line">
				<h2><?php _e('Custom Redirect:', 'ihc');?></h2>
				<div class="iump-form-line">
					<select name="ihc_register_lite_redirect" style="min-width:400px;">
						<?php foreach ($pages_arr as $post_id=>$title):?>
							<?php $selected = ($data['metas']['ihc_register_lite_redirect']==$post_id) ? 'selected' : '';?>
							<option value="<?php echo $post_id;?>" <?php echo $selected;?> ><?php echo $title;?></option>
						<?php endforeach;?>
					</select>
				</div>
			</div>
				
			<div class="iump-form-line">
				<h2><?php _e('Custom CSS', 'ihc');?></h2>	
				<textarea name="ihc_register_lite_custom_css" style="width: 100%; height: 150px;"><?php echo $data['metas']['ihc_register_lite_custom_css'];?></textarea>
			</div>	
	
				
			
			<h2><?php _e('Shortcode: ', 'ihc');?> </h2>		
			<div class="ihc-user-list-shortcode-wrapp">
				<div class="content-shortcode" style="padding:15px; text-align:center;">
					<span class="the-shortcode" style="font-size: 16px;">[ihc-register-lite]</span>
				</div>						
			</div>

											
			<div class="ihc-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>		
					
		</div>
	</div>
</form>