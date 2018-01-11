<?php
$levels = get_option('ihc_levels');
ihc_save_update_metas('download_monitor_integration');//save update metas
if (isset($_POST['ihc_save'])){
	if ($levels){
		$array = array();
		foreach ($levels as $id=>$level){
			if (isset($_POST['level_' . $id])){
				$array['level_' . $id ] = $_POST['level_' . $id ];				
			}
		}
		update_option('ihc_download_monitor_values', $array);
	}
}
$data['metas'] = ihc_return_meta_arr('download_monitor_integration');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();

?>
<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3><?php _e('Download Monitor Integration', 'ihc');?></h3>
		<div class="inside">			
			<div class="iump-form-line">
				<h2><?php _e('Activate/Hold', 'ihc');?></h2>
				<p style="margin-top:0px;"><?php _e('Limit number of Downloads per Files or per User related on current User Subscription/Level', 'ihc');?></p>
				
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['ihc_download_monitor_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_download_monitor_enabled');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_download_monitor_enabled" value="<?php echo $data['metas']['ihc_download_monitor_enabled'];?>" id="ihc_download_monitor_enabled" /> 																
			<p style="font-weight:bold;">Requires <a href="https://wordpress.org/plugins/download-monitor/" target="_blank">Download Monitor</a> Plugin installed and active.</p>
			</div>
			
			<div class="iump-form-line">
				<h2><?php _e('Limit Type', 'ihc');?></h2>
				<select name="ihc_download_monitor_limit_type">
					<option value="files" <?php if ($data['metas']['ihc_download_monitor_limit_type']=='files') echo 'selected';?> ><?php _e('Downloaded Files', 'ihc');?></option>
					<option value="downloads" <?php if ($data['metas']['ihc_download_monitor_limit_type']=='downloads') echo 'selected';?> ><?php _e('Total Downloads', 'ihc');?></option>
				</select>				
			</div>			
	
			<div class="iump-form-line">
				<?php if (!empty($levels)):?>
					<h2><?php _e('Levels limits', 'ihc');?></h2>										
					<?php foreach ($levels as $id => $level):?>
					<div class="row" style="margin-left:0px;">
						<div class="col-xs-5">
							<div class="input-group" style="margin:0px 0 15px 0;">
								<span class="input-group-addon" id="basic-addon1"><?php echo $level['label'];?></span>
								<?php 
									$value = '';
									if (isset($data['metas']['ihc_download_monitor_values']['level_' . $id ])){
										$value = $data['metas']['ihc_download_monitor_values']['level_' . $id ];
									}
								?>
								<input type="number" class="form-control" value="<?php echo $value;?>" name="<?php echo 'level_' . $id;?>" min="0" />
							</div>
						</div>
					</div>	
						
					<?php endforeach;?>
				<?php endif;?>
			</div>
			<div class="ihc-wrapp-submit-bttn iump-submit-form">
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large">
			</div>							
		</div>
	</div>				
</form>

<?php

