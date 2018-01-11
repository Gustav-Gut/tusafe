<?php
///save
if (isset($_POST['ihc_save'])){
	/// SAVE TAX
	Ihc_Db::save_tax($_POST);	
} else if (!empty($_GET['delete'])){
	/// DELETE TAX
	Ihc_Db::delete_tax($_GET['delete']);
} else if (!empty($_POST['save_settings'])){
	///update settings
	ihc_save_metas_group('ihc_taxes_settings', $_POST);//save update metas
}
$data['items'] = Ihc_Db::get_all_taxes();
$data['countries'] = ihc_get_countries();
$data['metas'] = ihc_return_meta_arr('ihc_taxes_settings');
?>

<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3><?php _e('Taxes Settings', 'ihc');?></h3>
		<div class="inside">	
			<div class="iump-form-line">
				<h2><?php _e('Activate/Hold Taxes', 'ihc');?></h2>
				<p style="margin-top:0px;"><?php _e('You can activate this option to take in place into your Membership system.', 'ihc');?></p>				
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['ihc_enable_taxes']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onclick="iump_check_and_h(this, '#ihc_enable_taxes');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_enable_taxes" value="<?php echo $data['metas']['ihc_enable_taxes'];?>" id="ihc_enable_taxes" /> 									
			</div>
			<div class="iump-form-line">
				<h2><?php _e('Show Taxes details', 'ihc');?></h2>
				<p><?php _e('Display Tax details and amount inside the Register process', 'ihc');?></p>			
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['ihc_show_taxes']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onclick="iump_check_and_h(this, '#ihc_show_taxes');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_show_taxes" value="<?php echo $data['metas']['ihc_show_taxes'];?>" id="ihc_show_taxes" /> 									
				<br/>
				<div class="row" style="margin-left:0px;">
				<div class="col-xs-5">
					<div class="input-group" style="margin:30px 0 15px 0;">
						<span class="input-group-addon" id="basic-addon1"><?php _e('Label:', 'ihc');?></span>
						<input type="text" class="form-control" name="ihc_default_tax_label"value="<?php echo $data['metas']['ihc_default_tax_label'];?>" />
					</div>
				</div>
				</div>	
			 </div>
			 <div class="iump-form-line">	
				<h2><?php _e('General Tax Value', 'ihc');?></h2>
				<p><?php _e('Set a default value that will take in place when none custom Value is set related on a specific Country', 'ihc');?></p>	
				<div class="row" style="margin-left:0px;">
				<div class="col-xs-5">
					<div class="input-group" style="margin:30px 0 15px 0;">
						<span class="input-group-addon" id="basic-addon1"><?php _e('Default Tax Value:', 'ihc');?></span>
						<input type="number" class="form-control" min="0" step="0.01" name="ihc_default_tax_value" value="<?php echo $data['metas']['ihc_default_tax_value'];?>" />
						<div class="input-group-addon">%</div>
					</div>
				</div>
				</div>
			</div>				
			
			<div style="margin-top: 15px;">
				<input type="submit" value="Save" name="save_settings" class="button button-primary button-large">
			</div>							
		</div>
	</div>	
</form>

<a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=add_edit_taxes');?>" class="indeed-add-new-like-wp"><i class="fa-ihc fa-add-ihc"></i><?php _e('Add New Tax', 'ihc');?></a>

<?php
if ($data['items']):
	?>
	<div style="width: 100%; margin-top: 20px;">
		<table class="wp-list-table widefat fixed tags">
			<thead>
				<tr>
					<th class="manage-column"><?php _e('Country', 'ihc');?></th>
					<th class="manage-column"><?php _e('State', 'ihc');?></th>					
					<th class="manage-column"><?php _e('Label', 'ihc');?></th>		
					<th class="manage-column"><?php _e('Tax Value (%)', 'ihc');?></th>
				</tr>			
			</thead>
			<tbody>
				<?php foreach ($data['items'] as $item):?>
					<tr onMouseOver="ihc_dh_selector('#tax_tr_<?php echo $item['id'];?>', 1);" onMouseOut="ihc_dh_selector('#tax_tr_<?php echo $item['id'];?>', 0);">
						<td><?php echo $data['countries'][$item['country_code']];?>
							<div style="visibility: hidden;" id="tax_tr_<?php echo $item['id'];?>">
								<a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=add_edit_taxes&edit=') . $item['id'];?>"><?php _e('Edit', 'ihc');?></a> 
								| 
								<a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=taxes&delete=') . $item['id'];?>" style="color: red;"><?php _e('Delete', 'ihc');?></a>
							</div>			
						</td>
						<td><?php 
							if (!empty($item['state_code'])){
								echo $item['state_code'];						
							} else {
								echo '-';
							}
						?></td>
						<td><?php echo $item['label'];?></td>
						<td><?php echo $item['amount_value'];?></td>
					</tr>
				<?php endforeach;?>			
			</tbody>
		</table>
	</div>
	<?php
else :
	?>
	<div><?php _e('No Taxes yet!', 'ihc');?></div>
	<?php
endif;

