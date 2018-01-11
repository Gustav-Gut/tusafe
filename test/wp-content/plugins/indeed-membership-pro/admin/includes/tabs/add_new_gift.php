<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=gifts');?>"><?php _e('Gifts', 'ihc');?></a>
	<a class="ihc-subtab-menu-item" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=add_new_gift');?>"><?php _e('Add new Gift', 'ihc');?></a>		
	<a class="ihc-subtab-menu-item" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=generated-gift-code');?>"><?php _e('Generated Gift Codes', 'ihc');?></a>	
</div>
<?php
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
$data['id'] = (isset($_GET['id'])) ? $_GET['id'] : 0;
$data['metas'] = Ihc_Db::gift_templates_get_metas($data['id']);
$levels = ihc_get_levels_with_payment();

?>

<div class="iump-page-title"><?php  _e("Gifts", 'ihc');?></div>
<form method="post" action="<?php echo admin_url('admin.php?page=ihc_manage&tab=gifts');?>">
	<div class="ihc-stuffbox">
		<?php if (!empty($_GET['id'])){?>
		<h3><?php _e("Edit", 'ihc');?></h3>
		<?php } else { ?>
		<h3><?php _e("Add New", 'ihc');?></h3>
		<?php } ?>
		<input type="hidden" name="id" value="<?php echo $data['id'];?>" />		
		<div class="inside">					
			
			<div class="iump-form-line">
				<label class="iump-labels-special"><?php _e("Awarded Level", 'ihc');?></label>
				<select name="lid"><?php 
					if ($levels && count($levels)){
						$levels_arr[-1] = __("All", 'ihc');
						foreach ($levels as $k=>$v){
							$levels_arr[$k] = $v['name'];
						}
					}
					foreach ($levels_arr as $k=>$v){
						$selected = ($data['metas']['lid']==$k) ? 'selected' : '';
						?>
						<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
					<?php 	
						}							
					?>
				</select>							
			</div>	
			
			<div class="iump-form-line">
				<label class="iump-labels-special"><?php _e("Type of discount", 'ihc');?></label>
				<select name="discount_type" onChange="ihc_discount_type(this.value);"><?php 
					$arr = array(
									'price' => __('Price', 'ihc'), 
									'percentage' => __('Percentage (%)', 'ihc'),
					);
					foreach ($arr as $k=>$v){
						$selected = ($data['metas']['discount_type']==$k) ? 'selected' : '';
						?>
						<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
						<?php 	
					}
				?></select>
			</div>
			
			<div class="iump-form-line">
				<label class="iump-labels-special"><?php _e("Discount Value", 'ihc');?></label>
				<input type="number" step="0.01" value="<?php echo $data['metas']['discount_value'];?>" name="discount_value"/> 
				<span id="discount_currency" style="display: <?php if ($data['metas']['discount_type']=='price') echo 'inline'; else echo 'none';?>"><?php echo get_option('ihc_currency');?></span>
				<span id="discount_percentage" style="display: <?php if ($data['metas']['discount_type']=='percentage') echo 'inline'; else echo 'none';?>">%</span>
			</div>

			<div class="iump-form-line">
				<label class="iump-labels-special"><?php _e("Target Level", 'ihc');?></label>
				<select name="target_level"><?php 
					if ($levels && count($levels)){
						$levels_arr[-1] = __("All", 'ihc');
						foreach ($levels as $k=>$v){
							$levels_arr[$k] = $v['name'];
						}
					}
					foreach ($levels_arr as $k=>$v){
						$selected = ($data['metas']['target_level']==$k) ? 'selected' : '';
						?>
						<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
					<?php 	
						}							
					?>
				</select>							
			</div>	

			<div class="iump-form-line">
				<label class="iump-labels-special"><?php _e("On Levels with Billing Recurrence apply the Discount:", 'ihc');?></label>
				<select name="reccuring"><?php 
					$arr = array( 0 => __("Just Once", 'ihc'), 
								  1 => __("Forever", 'ihc'),
					);
					foreach ($arr as $k=>$v){
						$selected = ($data['metas']['reccuring']==$k) ? 'selected' : '';
						?>
							<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
						<?php 	
					}							
				?></select>							
			</div>
												
			<div class="ihc-wrapp-submit-bttn">
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save_gift" class="button button-primary button-large" />
			</div>
																															
		</div>
	</div>
</form>