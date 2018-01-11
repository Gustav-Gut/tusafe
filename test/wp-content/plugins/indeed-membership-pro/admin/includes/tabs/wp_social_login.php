<?php
if (defined('WORDPRESS_SOCIAL_LOGIN_ABS_PATH')){
	$is_set = TRUE;
}

ihc_save_update_metas('wp_social_login');//save update metas
$data['metas'] = ihc_return_meta_arr('wp_social_login');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
$pages_arr = array(-1=>'...') + ihc_get_all_pages() + ihc_get_redirect_links_as_arr_for_select();
if (empty($data['metas']['ihc_wp_social_login_redirect_page'])){
	$data['metas']['ihc_wp_social_login_redirect_page'] = get_option('ihc_general_user_page');
}
?>
<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php _e('Wp Social Login Integration', 'ihc');?></h3>
		
		<div class="inside">
			
			<?php if (empty($is_set)):?>
				<?php echo __("Wp Social Login it's not active on Your system. You can find ", 'ihc') . '<a href="https://wordpress.org/plugins/wordpress-social-login/" target="_blank">' . __('here', 'ihc') . '.</a>';?>
			<?php else:?>
			
				<div class="iump-form-line">
					<h2><?php _e('Activate/Hold', 'ihc');?></h2>					
					<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
						<?php $checked = ($data['metas']['ihc_wp_social_login_on']) ? 'checked' : '';?>
						<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_wp_social_login_on');" <?php echo $checked;?> />
						<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="ihc_wp_social_login_on" value="<?php echo $data['metas']['ihc_wp_social_login_on'];?>" id="ihc_wp_social_login_on" /> 												
				</div>	
				
				<div class="iump-form-line">
					<h2><?php _e('Login/Register Redirect', 'ihc');?></h2>
					<div class="iump-form-line">
						<select name="ihc_wp_social_login_redirect_page">
							<?php foreach ($pages_arr as $post_id=>$title):?>
								<?php $selected = ($data['metas']['ihc_wp_social_login_redirect_page']==$post_id) ? 'selected' : '';?>
								<option value="<?php echo $post_id;?>" <?php echo $selected;?> ><?php echo $title;?></option>
							<?php endforeach;?>
						</select>
					</div>					
				</div>
				
				<div class="iump-form-line">				
					<h2><?php _e('WP Role', 'ihc');?></h2>
					<div style="font-weight:bold"><?php _e('Predefined Wordpress Role Assign to new Users:', 'ihc');?></div>
					<select name="ihc_wp_social_login_default_role">
					<?php 
						if (empty($data['metas']['ihc_wp_social_login_default_role'])){
							$data['metas']['ihc_wp_social_login_default_role'] = get_option('ihc_register_new_user_role');
						}
						$roles = ihc_get_wp_roles_list();
						if ($roles){
							foreach ($roles as $k=>$v){
								$selected = ($data['metas']['ihc_wp_social_login_default_role']==$k) ? 'selected' : '';
								?>
									<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
								<?php 
							}	
						}
					?>
					</select>							
				</div>	
	
				<div class="iump-form-line">
					<?php 
						if (empty($data['metas']['ihc_wp_social_login_default_level'])){
							$data['metas']['ihc_wp_social_login_default_level'] = get_option('ihc_register_new_user_level');										
						}
					?>					
					<div style="font-weight:bold"><?php _e('Level assigned to new User', 'ihc');?></div>
					<select name="ihc_wp_social_login_default_level">
						<option value="-1" <?php if ($data['metas']['ihc_wp_social_login_default_level']==-1) echo 'selected';?> ><?php _e('None', 'ihc');?></option>
						<?php 
							$levels = get_option('ihc_levels');
							if ($levels && count($levels)){
								foreach ($levels as $id=>$v){
								?>
									<option value="<?php echo $id;?>" <?php if ($data['metas']['ihc_wp_social_login_default_level']==$id) echo 'selected';?> ><?php echo $v['name'];?></option>
								<?php 
								}
							}
						?>
					</select>						
				</div>						

				
				<h4>Wordpress Social Login - Shortocode:</h4>
				<div class="ihc-user-list-shortcode-wrapp">	
					<div class="content-shortcode" style="padding:15px; text-align:center;">						
						<span class="the-shortcode" style="font-size: 16px;">[wordpress_social_login]</span>
					</div>						
				</div>

				<div>
					<a href="<?php echo admin_url('options-general.php?page=wordpress-social-login');?>"><?php _e('Wordpress Social Login - Settings', 'ihc');?></a>
				</div>
																													
				<div class="ihc-submit-form" style="margin-top: 20px;"> 
					<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
				</div>		
					
			<?php endif;?>
							
		</div>		
	</div>
</form>