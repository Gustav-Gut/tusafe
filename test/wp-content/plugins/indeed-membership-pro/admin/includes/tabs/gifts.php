<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=gifts');?>"><?php _e('Settings', 'ihc');?></a>		
	<a class="ihc-subtab-menu-item" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=generated-gift-code');?>"><?php _e('Generated Membership Gift Codes', 'ihc');?></a>	
</div>
<?php
ihc_save_update_metas('gifts');//save update metas
$data = ihc_return_meta_arr('gifts');
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
?>
<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php _e('Membership Gifts', 'ihc');?></h3>
		<div class="inside">
			
			<div class="iump-form-line">
				<h2><?php _e('Activate/Hold Membership Gifts', 'ihc');?></h2>
				<p style="max-width:70%;"><?php _e('Provide to your Customers a way to buy Gifts with current Levels', 'ihc');?></p>
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = empty($data['ihc_gifts_enabled']) ? '' : 'checked';?>
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_gifts_enabled');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_gifts_enabled" value="<?php echo $data['ihc_gifts_enabled'];?>" id="ihc_gifts_enabled" /> 												
			</div>

			<div class="iump-form-line">
				<h2><?php _e('Additional Settings', 'ihc');?></h2>
				<br/>
				<h5><?php _e('Give User Gift on every recurring level payment', 'ihc');?></h5>
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = empty($data['ihc_gifts_user_get_multiple_on_recurring']) ? '' : 'checked';?>
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_gifts_user_get_multiple_on_recurring');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_gifts_user_get_multiple_on_recurring" value="<?php echo $data['ihc_gifts_user_get_multiple_on_recurring'];?>" id="ihc_gifts_user_get_multiple_on_recurring" /> 												
			</div>
				
			<div class="ihc-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>	
							
		</div>
	</div>
</form>

<?php
if (!empty($_POST['ihc_save_gift'])){
	Ihc_Db::gifts_do_save($_POST);
} else if (isset($_GET['do_delete'])){
	Ihc_Db::gifts_do_delete($_GET['do_delete']);
}
$data = Ihc_Db::gift_get_all_items();
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
$levels = get_option('ihc_levels');
$levels[-1]['label'] = __('All', 'ihc');
$currency = get_option('ihc_currency');
?>
<div style="margin-bottom: 10px;">
	<a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=add_new_gift');?>" class="indeed-add-new-like-wp"><i class="fa-ihc fa-add-ihc"></i><?php _e('Add new Gift Offer', 'ihc');?></a>	
</div>

<?php if (!empty($data)):?>
	<div style="margin-right:20px;">
	<table class="wp-list-table widefat fixed tags">
		<thead>
			<tr>
				<th class="manage-column"><?php _e('Awarded Level', 'ihc');?></th>
				<th class="manage-column"><?php _e('Discount Value', 'ihc');?></th>
				<th class="manage-column"><?php _e('Target Level', 'ihc');?></th>
				<th class="manage-column"><?php _e('Action', 'ihc');?></th>
			</tr>
		</thead>
		<?php  $i = 1;
			foreach ($data as $id => $array):?>
			<tr class="<?php if($i%2==0) echo 'alternate';?>">
				<td style="color: #21759b; font-weight:bold; width:120px;font-family: 'Oswald', arial, sans-serif !important;font-size: 14px;font-weight: 400;"><?php 
					$l = $array['lid'];
					if (isset($levels[$l]) && isset($levels[$l]['label'])){
						echo $levels[$l]['label'];		
					}
				?></td>
				<td>
					<?php 
						if ($array['discount_type']=='price'){
							echo ihc_format_price_and_currency($currency, $array['discount_value']);
						} else {
							echo $array['discount_value'] . '%';
						}
					?>
				</td>
				<td>
					<div class="level-type-list ">
					<?php 
					$l = $array['target_level'];
					if (isset($levels[$l]) && isset($levels[$l]['label'])){
						echo $levels[$l]['label'];		
					}			
					?>
					</div>
				</td>
				<td>
					<a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=add_new_gift&id=' . $id);?>"><?php _e('Edit', 'ihc');?></a> |
					<a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=gifts&do_delete=' . $id);?>"><?php _e('Delete', 'ihc');?></a>
				</td>
			</tr>
		<?php 
		$i++;
		endforeach;?>
	</table>
	</div>
<?php endif;?>
