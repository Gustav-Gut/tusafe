<?php
ihc_save_update_metas('ihc_cheat_off');//save update metas
$data['metas'] = ihc_return_meta_arr('ihc_cheat_off');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
?>
<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php _e('Cheat Off', 'ihc');?></h3>
		<div class="inside">
			
			<div class="iump-form-line">
				<h2><?php _e('Activate/Hold', 'ihc');?></h2>					
				<p><?php _e('Avoid sharing their login credentials by your customers and keep only one user logged one time. If a new user Logs in using the same credentials, the previous one will be Logged Out and redirected to a Warning Page.', 'ihc');?></p>
					
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['ihc_cheat_off_enable']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_cheat_off_enable');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_cheat_off_enable" value="<?php echo $data['metas']['ihc_cheat_off_enable'];?>" id="ihc_cheat_off_enable" /> 												
			</div>	
			
			<div class="iump-form-line">
				<h2><?php _e('Cookie Expire Time', 'ihc');?></h2>					
				<input type="number" name="ihc_cheat_off_cookie_time" value="<?php echo $data['metas']['ihc_cheat_off_cookie_time'];?>" min="1"  /> <?php _e('Days', 'ihc');?> 												
			</div>	
					
			<?php
			$pages = ihc_get_all_pages() + ihc_get_redirect_links_as_arr_for_select();
			?>		
			<div class="iump-form-line">
				<label class="iump-labels-special"><?php _e('Warning Redirect:', 'ihc');?></label>
				<select name="ihc_cheat_off_redirect">
					<option value="-1" <?php if($data['metas']['ihc_cheat_off_redirect']==-1)echo 'selected';?> >...</option>
					<?php 
						if ($pages){
							foreach ($pages as $k=>$v){
							?>
								<option value="<?php echo $k;?>" <?php if($data['metas']['ihc_cheat_off_redirect']==$k)echo 'selected';?> ><?php echo $v;?></option>
							<?php 
							}						
						}
					?>
				</select>
			</div>
																				
			<div class="ihc-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>		
					
		</div>
	</div>
</form>