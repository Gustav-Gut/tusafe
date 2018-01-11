<?php
ihc_save_update_metas('ihc_woo');//save update metas
$data['metas'] = ihc_return_meta_arr('ihc_woo');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
?>
<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php _e('WooCommerce Account Page', 'ihc');?></h3>
		<div class="inside">
			
			<div class="iump-form-line">
					<h2><?php _e('Activate/Hold', 'ihc');?></h2>
					<p><?php _e('Fully integrate User Account with their WooCommerce MyAccount. Once is activated a new Tab into Woo MyAccount menu will show up.', 'ihc');?></p>
					
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['ihc_woo_account_page_enable']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_woo_account_page_enable');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_woo_account_page_enable" value="<?php echo $data['metas']['ihc_woo_account_page_enable'];?>" id="ihc_woo_account_page_enable" /> 												
			</div>			
				
			<div class="iump-form-line">
				<div class="row" style="margin-left:0px;">
				<div class="col-xs-5">
					<div class="input-group" style="margin:30px 0 5px 0;">
					<span class="input-group-addon" id="basic-addon1"><?php _e('Menu Label', 'ihc');?></span>
					<input type="text" class="form-control" name="ihc_woo_account_page_name" value="<?php echo $data['metas']['ihc_woo_account_page_name'];?>" />
					</div>
				</div>
				</div>
			</div>	
			
			<div class="iump-form-line">
				<div class="row" style="margin-left:0px;">
				<div class="col-xs-5">
					<div class="input-group" style="margin:0px 0 5px 0;">
					<span class="input-group-addon" id="basic-addon1"><?php _e('Menu Position', 'ihc');?></span>
					<input type="number" class="form-control" name="ihc_woo_account_page_menu_position" value="<?php echo $data['metas']['ihc_woo_account_page_menu_position'];?>" min=1 />
					</div>
				</div>
				</div>
			</div>				
			
											
			<div class="ihc-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>		
					
		</div>
	</div>
</form>