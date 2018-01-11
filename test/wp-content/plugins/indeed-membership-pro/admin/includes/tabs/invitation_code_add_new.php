<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=invitation_code-add_new');?>"><?php _e('Add Single Invitation Code', 'ihc');?></a>
	<a class="ihc-subtab-menu-item" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=invitation_code-add_new&multiple=true');?>"><?php _e('Add Bulk Invitation Codes', 'ihc');?></a>	
	<a class="ihc-subtab-menu-item" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=invitation_code');?>"><?php _e('Manage Invitation Codes', 'ihc');?></a>	
	<div class="ihc-clear"></div>
</div>
<?php
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
?>
		<div class="iump-page-title"><?php  _e("Invitation Code", 'ihc');?></div>
			<form method="post" action="<?php echo admin_url('admin.php?page=ihc_manage&tab=invitation_code');?>">
				<div class="ihc-stuffbox">
					<h3><?php _e("Add New", 'ihc');?></h3>
					<div class="inside">
						<?php 
							if (!empty($_GET['multiple'])){
								//////////////// MULTIPLE COUPONS ////////////
								?>
								<div class="iump-form-line">
									<label class="iump-labels-special"><?php _e("Code prefix", 'ihc');?></label>
									<input type="text" value="" name="code_prefix" />
								</div>
								<div class="iump-form-line">
									<label class="iump-labels-special"><?php _e("Length", 'ihc');?></label>
									<input type="number" min="2" value="10" name="code_length" />
								</div>
								<div class="iump-form-line">
									<label class="iump-labels-special"><?php _e("Number of Codes", 'ihc');?></label>
									<input type="number" min="2" value="2" name="how_many_codes" />
								</div>																								
								<?php 	
							} else {
								/////////////// ONE /////////////
								?>
								<div class="iump-form-line">
									<label class="iump-labels-special"><?php _e("Code", 'ihc');?></label>
									<input type="text" value="" name="code" id="ihc_the_coupon_code" /> <span style="font-size: 11px;color: #fff; padding: 6px 9px;-webkit-border-radius: 3px;box-radius: 3px;    background-color: rgba(240, 80, 80, 0.8);cursor: pointer;" onClick="ihc_generate_code();"><?php _e("Generate Code", "ihc");?></span>
								</div>								
								<?php 
							}
						?>

						<div class="iump-form-line">
							<label class="iump-labels-special"><?php _e("Repeat", 'ihc');?></label>
							<input type="number"  min="1" value="1" name="repeat"/>
						</div>	

						<div class="ihc-wrapp-submit-bttn">
							<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="add_new" class="button button-primary button-large" />
						</div>																												

					</div>
				</div>
			</form>
		</div>