<?php
if (!empty($_POST['new_currency_code']) && !empty($_POST['new_currency_name'])){
	$data = get_option('ihc_currencies_list');
	if (empty($data[$_POST['new_currency_code']])){
		$data[$_POST['new_currency_code']] = $_POST['new_currency_name'];	
	}
	update_option('ihc_currencies_list', $data);
}
$basic_currencies = ihc_get_currencies_list('custom');
?>
<form action="" method="post">
		<div class="ihc-stuffbox">
			<h3><?php _e('Add new Currency', 'ihc');?></h3>
			<div class="inside">		
			<h2><?php _e('Custom Currency', 'ihc');?></h2>
		<p style="margin-top:0px;"><?php _e('Add new currencies beside the predefined list based on custom Symbols', 'ihc');?></p>
			
				<div class="iump-form-line">
					<label class="iump-labels-special"><?php _e('Code:', 'ihc');?></label>
					<input type="test" value="" name="new_currency_code" />
					<p><?php _e('Insert a valid Currency Code, ex: ', 'ihc');?><span style="font-weight:bold;"><?php _e('USD, EUR, CAD.', 'ihc');?></span></p>
				</div>
				<div class="iump-form-line">
					<label class="iump-labels-special"><?php _e('Name:', 'ihc');?></label>
					<input type="text" value="" name="new_currency_name" />
				</div>
				<div class="ihc-wrapp-submit-bttn iump-submit-form">
					<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
				</div>							
			</div>
		</div>
		<?php if ($basic_currencies!==FALSE && count($basic_currencies)>0){?>
		<div class="ihc-dashboard-form-wrap">
			<table class="wp-list-table widefat fixed tags" style="margin-bottom: 20px;">
				<thead>
					<tr>
						<th class="manage-column">Code</th>
						<th class="manage-column">Name</th>
						<th class="manage-column" style="width:80px; text-align: center;">Delete</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					foreach ($basic_currencies as $code=>$name){
					?>
					<tr id="ihc_div_<?php echo $code;?>">
						<td><?php echo $code;?></td>
						<td><?php echo $name;?></td>
						<td style="text-align: center;"><i class="fa-ihc ihc-icon-remove-e" onClick="ihc_remove_currency('<?php echo $code;?>');" style="cursor: pointer;"></i></td>
					</tr>						
					<?php 
					}
					?>
				</tbody>
			</table>				
		</div>
	<?php }?>			
</form>