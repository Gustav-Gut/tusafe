<?php
ihc_save_update_metas('invoices');//save update metas
$data['metas'] = ihc_return_meta_arr('invoices');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();

?>
<form action="" method="post" id="invoice_form">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php _e('Invoices', 'ihc');?></h3>
		<div class="inside">
			
			<div class="iump-form-line">
				<h2><?php _e('Activate/Hold Invoices module', 'ihc');?></h2>
				<p><?php _e('Provides printable Invoices for each Order into Account Page or system Dashboard', 'ihc'); ?></p>					
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['ihc_invoices_on']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_invoices_on');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_invoices_on" value="<?php echo $data['metas']['ihc_invoices_on'];?>" id="ihc_invoices_on" /> 												
			</div>	
			<div class="iump-register-select-template" style="padding: 30px;margin-top: 30px;">
				<div class="iump-form-line">
					<h4><?php _e('Invoice Template', 'ihc');?></h4>	
					<select name="ihc_invoices_template" onChange="iump_admin_preview_invoice();"><?php
						foreach (array('iump-invoice-template-1'=>__('Template 1', 'ihc'), 'iump-invoice-template-2'=>__('Template 2', 'ihc')) as $k=>$v){
							$selected = ($data['metas']['ihc_invoices_template']==$k) ? 'selected' : '';
							?>
							<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
							<?php
						}	
					?></select>
				</div>	
				
				<div class="iump-form-line">
					<h4><?php _e('Invoice Logo', 'ihc');?></h4>				
					<input type="text" onblur="iump_admin_preview_invoice();" style="width: 70%;" name="ihc_invoices_logo" value="<?php echo $data['metas']['ihc_invoices_logo'];?>" onClick="open_media_up(this);" />	<i class="fa-ihc ihc-icon-remove-e iump-pointer" onClick="jQuery('[name=ihc_invoices_logo]').val('');"></i>
				</div>				
							
				<div class="iump-form-line">
					<h4><?php _e('Invoice main Title', 'ihc');?></h4>	
					<input type="text" onblur="iump_admin_preview_invoice();" name="ihc_invoices_title" value="<?php echo $data['metas']['ihc_invoices_title'];?>"/>					
				</div>			
			</div>
			
			<div class="iump-form-line">
			<h2><?php _e('Additional Invoice Details', 'ihc');?></h2>	
			</div>
			<div class="row" style="margin-left:0;">
				<div class="col-xs-5">
					<h4><?php _e('Company Field', 'ihc');?></h4>
					<div style="width:60%;">
						<?php wp_editor( $data['metas']['ihc_invoices_company_field'], 'ihc_invoices_company_field', array('textarea_name'=>'ihc_invoices_company_field', 'quicktags'=>TRUE) );?>	
					</div>
				</div>
				<div class="col-xs-7">
					<h4><?php _e('Bill to', 'ihc');?></h4>
					<div style=" width: 60%; margin-right:1%; display:inline-block;">
						<?php wp_editor( $data['metas']['ihc_invoices_bill_to'], 'ihc_invoices_bill_to', array('textarea_name'=>'ihc_invoices_bill_to', 'quicktags'=>TRUE) );?>	
					</div>	
					<div style="width: 19%; display: inline-block; vertical-align: top; color: #333;">
							<?php
								echo "<h4>".__('Standard Fields constants', 'ihc')."</h4>"; 
								$constants = array( 
													'{username}'=>'', 
													'{user_email}'=>'', 
													'{first_name}'=>'', 
													'{last_name}'=>'', 
													'{account_page}'=>'', 
													'{login_page}'=>'', 
													'{level_list}'=>'',
													'{blogname}'=>'', 
													'{blogurl}'=>'',  
													'{currency}'=>'', 
													'{amount}'=>'', 
													'{level_name}'=>'', 
													'{current_date}' => '', 
								);
								$extra_constants = ihc_get_custom_constant_fields();
								foreach ($constants as $k=>$v){
									?>
									<div><?php echo $k;?></div>
									<?php 	
								}
								?>
						</div>
						<div style="width: 19%; display: inline-block; vertical-align: top; color: #333;">		
								<?php
								echo "<h4>".__('Custom Fields constants', 'ihc')."</h4>";
								foreach ($extra_constants as $k=>$v){
									?>
									<div><?php echo $k;?></div>
									<?php 	
								}
							?>
					</div>						
				</div>
				<div class="ihc-clear"></div>
			</div>
			<div class="iump-form-line">
				<h2><?php _e('Footer Invoice Info', 'ihc');?></h2>						
			</div>
			<div style="width:60%; display:inline-block;">
				<?php wp_editor( $data['metas']['ihc_invoices_footer'], 'ihc_invoices_footer', array('textarea_name'=>'ihc_invoices_footer', 'quicktags'=>TRUE) );?>	
			</div>				
						
			<div class="iump-form-line">
				<h2><?php _e('Custom CSS', 'ihc');?></h2>	
				<textarea name="ihc_invoices_custom_css" onblur="iump_admin_preview_invoice();" style="width: 100%; height: 150px;"><?php echo $data['metas']['ihc_invoices_custom_css'];?></textarea>
			</div>			
			
							
			<div class="ihc-wrapp-submit-bttn iump-submit-form">
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large">
			</div>
						
		</div>
	</div>
</form>

<div class="ihc-stuffbox">
	<h3 class="ihc-h3"><?php _e('Preview', 'ihc');?></h3>
	<div class="inside" id="preview_container">
	</div>
</div>
<script>
	jQuery('document').ready(function(){
		iump_admin_preview_invoice();
	});
</script>
