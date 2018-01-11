<?php
$id = empty($_GET['edit']) ? 0 : $_GET['edit'];
$data['metas'] = Ihc_Db::get_tax($id);
require_once IHC_PATH . 'public/functions/ihc_countries.php';
$data['countries'] = ihc_get_countries();
?>
	<form method="post" action="<?php echo admin_url('admin.php?page=ihc_manage&tab=taxes');?>">
		<input type="hidden" name="id" value="<?php echo $data['metas']['id'];?>" />
		<input type="hidden" name="status" value="<?php echo $data['metas']['status'];?>" />
		<div class="ihc-stuffbox">
			<h3><?php _e('Add/Edit Tax', 'ihc');?></h3>
			<div class="inside">
								
				<div class="iump-form-line">
					<label class="iump-labels-special"><?php _e('Label:', 'ihc');?></label>
					<input type="text" name="label" value="<?php echo $data['metas']['label'];?>" />
				</div>

				<div class="iump-form-line">
					<label class="iump-labels-special"><?php _e('Description:', 'ihc');?></label>
					<textarea name="description"><?php echo $data['metas']['description'];?></textarea>
				</div>			
										
				<div class="iump-form-line">
					<label class="iump-labels-special"><?php _e('Country:', 'ihc');?></label>
					<select name="country_code" id="country_field">
						<?php foreach ($data['countries'] as $k=>$v):?>
							<option value="<?php echo $k;?>" <?php if ($data['metas']['country_code']==$k) echo 'selected';?> ><?php echo $v;?></option>
						<?php endforeach;?>
					</select>			
				</div>
										
				<div class="iump-form-line">
					<label class="iump-labels-special"><?php _e('State:', 'ihc');?></label>
					<input type="text" name="state_code" value="<?php echo $data['metas']['state_code'];?>" />	
				</div>
								
				<div class="iump-form-line">
					<label class="iump-labels-special"><?php _e('Tax Value:', 'ihc');?></label>
					<input type="number" name="amount_value" value="<?php echo $data['metas']['amount_value'];?>" min="0" step="0.01" /> %
				</div>

				<div style="margin-top: 15px;">
					<input type="submit" value="<?php if ($id){_e('Update', 'ihc');} else{_e('Add New', 'ihc');}?>" name="ihc_save" class="button button-primary button-large">
				</div>				
			</div>	
		</div>
	</form>	
	
<script>
jQuery("#country_field").select2({
	placeholder: "Select Your Country",
	allowClear: true
});				
</script>
				
