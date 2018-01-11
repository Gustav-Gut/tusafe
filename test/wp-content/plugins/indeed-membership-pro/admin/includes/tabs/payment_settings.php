<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['subtab'] =='paypal') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=paypal';?>"><?php _e('PayPal', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['subtab'] =='authorize') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=authorize';?>"><?php _e('Authorize', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['subtab'] =='stripe') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=stripe';?>"><?php _e('Stripe', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['subtab'] =='twocheckout') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=twocheckout';?>"><?php _e('2Checkout', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['subtab'] =='braintree') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=braintree';?>"><?php _e('Braintree', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['subtab'] =='payza') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=payza';?>"><?php _e('Payza', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['subtab'] =='bank_transfer') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=bank_transfer';?>"><?php _e('Bank transfer', 'ihc');?></a>
	<a class="ihc-subtab-menu-item" href="<?php echo $url.'&tab=general&subtab=pay_settings';?>"><?php _e('Payments Settings', 'ihc');?></a>
	<div class="ihc-clear"></div>
</div>
<?php 
echo ihc_inside_dashboard_error_license();

if (empty($_GET['subtab'])){
	//listing payment methods
	$pages = ihc_get_all_pages();//getting pages
	echo ihc_check_default_pages_set();//set default pages message
	echo ihc_check_payment_gateways();
	echo ihc_is_curl_enable();
	?>
	<div class="iump-page-title">Ultimate Membership Pro - 
		<span class="second-text">
			<?php _e('Payments Services', 'ihc');?>
		</span>
	</div>
	<div class="iump-payment-list-wrapper">
		<div class="iump-payment-box-wrap">
		<?php $pay_stat = ihc_check_payment_status('paypal'); ?>
		  <a href="<?php echo $url.'&tab='.$tab.'&subtab=paypal';?>">
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">PayPal</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		 </a>	
		</div>
		<div class="iump-payment-box-wrap">
		  <?php $pay_stat = ihc_check_payment_status('authorize'); ?>
		  <a href="<?php echo $url.'&tab='.$tab.'&subtab=authorize';?>">
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">Authorize.net</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		 </a>	
		</div>
		<div class="iump-payment-box-wrap">
		   <?php $pay_stat = ihc_check_payment_status('stripe'); ?>
		   <a href="<?php echo $url.'&tab='.$tab.'&subtab=stripe';?>"> 	
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">Stripe</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		   </a>	
		</div>
		<div class="iump-payment-box-wrap">
		   <?php $pay_stat = ihc_check_payment_status('twocheckout'); ?>
		   <a href="<?php echo $url.'&tab='.$tab.'&subtab=twocheckout';?>"> 	
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">2Checkout</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		   </a>	
		</div>
		<div class="iump-payment-box-wrap">
		   <?php $pay_stat = ihc_check_payment_status('bank_transfer'); ?>
		   <a href="<?php echo $url.'&tab='.$tab.'&subtab=bank_transfer';?>"> 	
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">Bank Transfer</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		   </a>	
		</div>
		<div class="iump-payment-box-wrap">
		   <?php $pay_stat = ihc_check_payment_status('braintree'); ?>
		   <a href="<?php echo $url.'&tab='.$tab.'&subtab=braintree';?>"> 	
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">Braintree</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		   </a>	
		</div>		
		<div class="iump-payment-box-wrap">
		   <?php $pay_stat = ihc_check_payment_status('payza'); ?>
		   <a href="<?php echo $url.'&tab='.$tab.'&subtab=payza';?>"> 	
			<div class="iump-payment-box <?php echo $pay_stat['active']; ?>">
				<div class="iump-payment-box-title">Payza</div>
				<div class="iump-payment-box-bottom">Settings: <span><?php echo $pay_stat['settings']; ?></span></div>
			</div>
		   </a>	
		</div>	
		<div class="ihc-clear"></div>								
	</div>
	<?php 
} else {
	switch ($_GET['subtab']){
		case 'paypal':
			ihc_save_update_metas('payment_paypal');//save update metas
			$meta_arr = ihc_return_meta_arr('payment_paypal');//getting metas
			$pages = ihc_get_all_pages();//getting pages
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();
			?>
			<div class="iump-page-title">Ultimate Membership Pro - 
				<span class="second-text">
					<?php _e('Payments Services', 'ihc');?>
				</span>
			</div>
			<form action="" method="post">
					<div class="ihc-stuffbox">
						<h3><?php _e('PayPal Activation:', 'ihc');?></h3>
						<div class="inside">		
							<div class="iump-form-line">
								<h4><?php _e('Once all Settings are properly done, Activate the Payment Getway for further use.', 'ihc');?> </h4>
								<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($meta_arr['ihc_paypal_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_paypal_status');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_paypal_status'];?>" name="ihc_paypal_status" id="ihc_paypal_status" /> 				
							</div>
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>			
						</div>	
					</div>
					<div class="ihc-stuffbox">
					
						<h3><?php _e('PayPal Settings:', 'ihc');?></h3>
						
						<div class="inside">
							<div class="iump-form-line">
								<label class="iump-labels"><?php _e('E-mail Address:', 'ihc');?></label> <input type="text" value="<?php echo $meta_arr['ihc_paypal_email'];?>" name="ihc_paypal_email" style="width: 300px;" />
							</div>
			
							<div class="iump-form-line iump-no-border">
								<label class="iump-labels"><?php _e('Enable Sandbox', 'ihc');?></label> <input type="checkbox" onClick="check_and_h(this, '#enable_sandbox');" <?php if($meta_arr['ihc_paypal_sandbox']) echo 'checked';?> />
								<input type="hidden" name="ihc_paypal_sandbox" value="<?php echo $meta_arr['ihc_paypal_sandbox'];?>" id="enable_sandbox" />
							</div>
							<div class="iump-form-line iump-special-line">
								<label class="iump-labels-special"><?php _e('Redirect Page after Payment:', 'ihc');?></label>
								<select name="ihc_paypal_return_page">
									<option value="-1" <?php if($meta_arr['ihc_paypal_return_page']==-1)echo 'selected';?> >...</option>
									<?php 
										if($pages){
											foreach($pages as $k=>$v){
												?>
													<option value="<?php echo $k;?>" <?php if ($meta_arr['ihc_paypal_return_page']==$k) echo 'selected';?> ><?php echo $v;?></option>
												<?php 
											}						
										}
									?>
								</select>
							</div>
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>				
						</div>
					</div>
					
					<div class="ihc-stuffbox">
						<h3><?php _e('Multi-Payment Selection:', 'ihc');?></h3>
						<div class="inside">
							<div class="iump-form-line iump-no-border">
								<label class="iump-labels"><?php _e('Label:', 'ihc');?></label>
								<input type="text" name="ihc_paypal_label" value="<?php echo $meta_arr['ihc_paypal_label'];?>" />
							</div>
							
							<div class="iump-form-line iump-no-border">
								<label class="iump-labels"><?php _e('Order:', 'ihc');?></label>
								<input type="number" min="1" name="ihc_paypal_select_order" value="<?php echo $meta_arr['ihc_paypal_select_order'];?>" />
							</div>						
																																
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>						
						</div>
					</div>						
					
			</form>
			<?php 		
		break;
		
		case 'stripe':
			ihc_save_update_metas('payment_stripe');//save update metas
			$meta_arr = ihc_return_meta_arr('payment_stripe');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();
			?>
			<div class="iump-page-title">Ultimate Membership Pro - 
				<span class="second-text">
					<?php _e('Payments Services', 'ihc');?>
				</span>
			</div>
			<form action="" method="post">
			<div class="ihc-stuffbox">
						<h3><?php _e('Stripe Activation:', 'ihc');?></h3>
						<div class="inside">		
							<div class="iump-form-line">
								<h4><?php _e('Once all Settings are properly done, Activate the Payment Getway for further use.', 'ihc');?> </h4>
								<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($meta_arr['ihc_stripe_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_stripe_status');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_stripe_status'];?>" name="ihc_stripe_status" id="ihc_stripe_status" /> 				
							</div>
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>			
						</div>	
					</div>
				<div class="ihc-stuffbox">				
					<h3><?php _e('Stripe Settings:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('Secret Key:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_stripe_secret_key'];?>" name="ihc_stripe_secret_key" style="width: 300px;" />
						</div>
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('Publishable Key:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_stripe_publishable_key'];?>" name="ihc_stripe_publishable_key" style="width: 300px;" />
						</div>	
						
						<div class="iump-form-line">
							<?php 
								$site_url = site_url();
								$site_url = trailingslashit($site_url);
								$notify_url = add_query_arg('ihc_action', 'stripe', $site_url);
								_e("<strong>Important:</strong> set your 'Webhook' to: ");
								echo '<strong>' . $notify_url . '</strong>'; /// admin_url("admin-ajax.php") . "?action=ihc_twocheckout_ins"									
							?>
						</div>
						
						<div style="font-size: 11px; color: #333; padding-left: 10px;">
							<ul class="ihc-info-list">
								<li><?php _e('1. Go to', 'ihc');?> <a href="http://stripe.com" target="_blank">http://stripe.com</a> <?php _e('and login with Username and password.', 'ihc');?></li>
								<li><?php _e('2. After that click on "Dashboard", and then select "Your account" - "Account settings".', 'ihc');?></li>
								<li><?php _e('3. A popup will appear and You must go to API Keys, here You will find the Secrete Key and	Publishable Key.', 'ihc');?></li>				
								<li><?php echo __("4. Set your Web Hook URL to: ", 'ihc') . '<strong>' . $notify_url . '</strong>';?></li>						
							</ul>							
						</div> 	
															
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>				
					</div>
				</div>
				
				<div class="ihc-stuffbox">
					<h3><?php _e('Multi-Payment Selection:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Label:', 'ihc');?></label>
							<input type="text" name="ihc_stripe_label" value="<?php echo $meta_arr['ihc_stripe_label'];?>" />
						</div>
						
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Order:', 'ihc');?></label>
							<input type="number" min="1" name="ihc_stripe_select_order" value="<?php echo $meta_arr['ihc_stripe_select_order'];?>" />
						</div>						
																															
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>						
					</div>
				</div>	
								
			</form>		
			<?php 
		break;
		
		case 'authorize':
			ihc_save_update_metas('payment_authorize');//save update metas
			$meta_arr = ihc_return_meta_arr('payment_authorize');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();
			?>
			<div class="iump-page-title">Ultimate Membership Pro - 
				<span class="second-text">
					<?php _e('Payments Services', 'ihc');?>
				</span>
			</div>
			<form action="" method="post">
			<div class="ihc-stuffbox">
						<h3><?php _e('Authorize.net Activation:', 'ihc');?></h3>
						<div class="inside">		
							<div class="iump-form-line">
								<h4><?php _e('Once all Settings are properly done, Activate the Payment Getway for further use.', 'ihc');?> </h4>
								<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($meta_arr['ihc_authorize_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_authorize_status');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_authorize_status'];?>" name="ihc_authorize_status" id="ihc_authorize_status" /> 				
							</div>
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>			
						</div>	
					</div>
				<div class="ihc-stuffbox">				
					<h3><?php _e('Authorize.net Settings:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('Login ID:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_authorize_login_id'];?>" name="ihc_authorize_login_id" style="width: 300px;" />
						</div>
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('Transaction Key:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_authorize_transaction_key'];?>" name="ihc_authorize_transaction_key" style="width: 300px;" />
						</div>	
						<div class="iump-form-line iump-no-border">
								<label class="iump-labels"><?php _e('Enable Sandbox', 'ihc');?></label> <input type="checkbox" onClick="check_and_h(this, '#enable_authorize_sandbox');" <?php if($meta_arr['ihc_authorize_sandbox']) echo 'checked';?> />
								<input type="hidden" name="ihc_authorize_sandbox" value="<?php echo $meta_arr['ihc_authorize_sandbox'];?>" id="enable_authorize_sandbox" />
						</div>
						
						<div class="iump-form-line">
							<?php 
								$site_url = site_url();
								$site_url = trailingslashit($site_url);
								$notify_url = add_query_arg('ihc_action', 'authorize', $site_url);		
								_e("<strong>Important:</strong> set your 'Silent Post URL' to: ");
								echo '<strong>' . $notify_url . '</strong>'; /// admin_url("admin-ajax.php") . "?action=ihc_twocheckout_ins"					
							?>
						</div>
						
						<div style="font-size: 11px; color: #333; padding-left: 10px;">
							<ul class="ihc-info-list">
								<li><?php _e('1. Go to', 'ihc');?> <a href="http://authorize.net" target="_blank">http://authorize.net</a> <?php echo __(' (or ', 'ihc');?> <a href="https://sandbox.authorize.net/" target="_blank">https://sandbox.authorize.net/</a> <?php echo __('if You want to use sandbox) and login with Username and password.', 'ihc');?></li>
								<li><?php _e('2. After that click on "Account". ', 'ihc');?></li>	
								<li><?php echo __("3. In 'Transaction Format Settings' You will find 'Silent Post URL', 'Response/Receipt URLs' and 'Relay Response'. Set them to : ", 'ihc'). '<strong>' . $notify_url . '</strong>';?></li>							
								<li><?php _e("4. In 'Security Settings' section You will find following link: 'API Credentials & Keys', click on it.", 'ihc');?></li>
								<li><?php _e("5. In this page You will find the 'Login ID' and 'Transaction Key'.", 'ihc');?></li>												
							</ul>		
						</div>	
			
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>				
					</div>
				</div>
				
				<div class="ihc-stuffbox">
					<h3><?php _e('Multi-Payment Selection:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Label:', 'ihc');?></label>
							<input type="text" name="ihc_authorize_label" value="<?php echo $meta_arr['ihc_authorize_label'];?>" />
						</div>
						
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Order:', 'ihc');?></label>
							<input type="number" min="1" name="ihc_authorize_select_order" value="<?php echo $meta_arr['ihc_authorize_select_order'];?>" />
						</div>						
																															
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>						
					</div>
				</div>					
				
			</form>		
			<?php 
		break;	
		
		case 'twocheckout':
			ihc_save_update_metas('payment_twocheckout');//save update metas
			$meta_arr = ihc_return_meta_arr('payment_twocheckout');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();
			?>
			<div class="iump-page-title">Ultimate Membership Pro - 
				<span class="second-text">
					<?php _e('2Checkout Services', 'ihc');?>
				</span>
			</div>
			<form action="" method="post">
				<div class="ihc-stuffbox">
					<h3><?php _e('2Checkout Activation:', 'ihc');?></h3>
					<div class="inside">		
						<div class="iump-form-line">
							<h4><?php _e('Once all Settings are properly done, Activate the Payment Getway for further use.', 'ihc');?> </h4>
							<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($meta_arr['ihc_twocheckout_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_twocheckout_status');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_twocheckout_status'];?>" name="ihc_twocheckout_status" id="ihc_twocheckout_status" /> 				
						</div>
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>			
					</div>	
				</div>
				<div class="ihc-stuffbox">
					<h3><?php _e('2Checkout Settings:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('API Username:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_api_user'];?>" name="ihc_twocheckout_api_user" style="width: 300px;" />
						</div>
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('API Password:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_api_pass'];?>" name="ihc_twocheckout_api_pass" style="width: 300px;" />
						</div>						
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('API Private Key:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_private_key'];?>" name="ihc_twocheckout_private_key" style="width: 300px;" />
						</div>	
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('Account Number:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_account_number'];?>" name="ihc_twocheckout_account_number" style="width: 300px;" />
						</div>	
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('Secret Word:', 'ihc');?></label> 
							<input type="text" value="<?php echo $meta_arr['ihc_twocheckout_secret_word'];?>" name="ihc_twocheckout_secret_word" style="width: 300px;" />
						</div>	
						<div class="iump-form-line">
							<label class="iump-labels"><?php _e('Enable Sandbox', 'ihc');?></label> <input type="checkbox" onClick="check_and_h(this, '#ihc_twocheckout_sandbox');" <?php if($meta_arr['ihc_twocheckout_sandbox']) echo 'checked';?> />
							<input type="hidden" name="ihc_twocheckout_sandbox" value="<?php echo $meta_arr['ihc_twocheckout_sandbox'];?>" id="ihc_twocheckout_sandbox" />
						</div>		
						<div class="iump-form-line">
							<?php 
								$site_url = site_url();
								$site_url = trailingslashit($site_url);
								$notify_url = add_query_arg('ihc_action', 'twocheckout', $site_url);								
								_e("<strong>Important:</strong> set your 'Web Hook URL'(ISN) and Your 'Approved URL' to: ");
								echo '<strong>' . $notify_url . '</strong>'; /// admin_url("admin-ajax.php") . "?action=ihc_twocheckout_ins"
							?>
						</div> 					
						
						<div style="font-size: 11px; color: #333; padding-left: 10px;">
							<ul class="ihc-info-list">
								<li><?php _e('1. Go to', 'ihc');?> <a href="http://authorize.net" target="_blank">http://authorize.net</a> <?php echo __(' (or ', 'ihc');?> <a href="https://sandbox.authorize.net/" target="_blank">https://sandbox.authorize.net/</a> <?php echo __('if You want to use sandbox) and login with Username and password.', 'ihc');?></li>	
								<li><?php _e("2. After You login go to 'Account' section and then click on 'Site Management'. Here You will find, at the bottom of page, the 'Secret Word'.", 'ihc');?>
								<li><?php echo __("3. In this section  You need also to set the 'Approved URL' at:", 'ihc') . $notify_url;?></li>
								<li><?php echo __("4. The 'Account Number' is beside Your Username in the top right of site.", 'ihc');?></li>
								<li><?php echo __("5. In 'API' You will find the 'Private Key'.", 'ihc');?></li>
								<li><?php echo __("6. 'API Username' and 'API Password' can be find or set in 'Account' -> 'User Management'.", 'ihc');?></li>
								<li><?php echo __("7. After You copy and paste all this keys You must set You INS (Instant Notification Settings) at: ", 'ihc') . '<strong>' . $notify_url . '</strong>' . " ." .  __("You can find this option on 'Webhooks' in live site or 'Notifications' in sandbox.", 'ihc');?></li>																			
							</ul>		
						</div>	
																															
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>	
					</div>			
				</div>
				
				<div class="ihc-stuffbox">
					<h3><?php _e('Multi-Payment Selection:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Label:', 'ihc');?></label>
							<input type="text" name="ihc_twocheckout_label" value="<?php echo $meta_arr['ihc_twocheckout_label'];?>" />
						</div>
						
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Order:', 'ihc');?></label>
							<input type="number" min="1" name="ihc_twocheckout_select_order" value="<?php echo $meta_arr['ihc_twocheckout_select_order'];?>" />
						</div>						
																															
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>						
					</div>
				</div>				
				
			</form>			
						
			<?php 
			break;
		case 'braintree':
			ihc_save_update_metas('payment_braintree');//save update metas
			$meta_arr = ihc_return_meta_arr('payment_braintree');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();
			?>
				<div class="iump-page-title">Ultimate Membership Pro - 
					<span class="second-text">
						<?php _e('Braintree Services', 'ihc');?>
					</span>
				</div>	
				<form action="" method="post">
					<div class="ihc-stuffbox">
						<h3><?php _e('Braintree Activation:', 'ihc');?></h3>						
						<div class="inside">		
							<div class="iump-form-line">
								<h4><?php _e('Once all Settings are properly done, Activate the Payment Getway for further use.', 'ihc');?> </h4>
								<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
									<?php $checked = ($meta_arr['ihc_braintree_status']) ? 'checked' : '';?>
									<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_braintree_status');" <?php echo $checked;?> />
									<div class="switch" style="display:inline-block;"></div>
								</label>
								<input type="hidden" value="<?php echo $meta_arr['ihc_braintree_status'];?>" name="ihc_braintree_status" id="ihc_braintree_status" /> 												
							</div>
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>								
						</div>
					</div>
					
						<div class="ihc-stuffbox">
							<h3><?php _e('Braintree Settings:', 'ihc');?></h3>
							<div class="inside">

								<div class="iump-form-line iump-no-border">
									<label class="iump-labels"><?php _e('Sandbox', 'ihc');?></label>
									<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
										<?php $checked = ($meta_arr['ihc_braintree_sandbox']) ? 'checked' : '';?>
										<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_braintree_sandbox');" <?php echo $checked;?> />
										<div class="switch" style="display:inline-block;"></div>
									</label>
									<input type="hidden" value="<?php echo $meta_arr['ihc_braintree_sandbox'];?>" name="ihc_braintree_sandbox" id="ihc_braintree_sandbox" /> 	
								</div>									
								
								<div class="iump-form-line iump-no-border">
									<label class="iump-labels"><?php _e('Merchant ID:', 'ihc');?></label>
									<input type="text" name="ihc_braintree_merchant_id" value="<?php echo $meta_arr['ihc_braintree_merchant_id'];?>" />
								</div>	
								
								<div class="iump-form-line iump-no-border">
									<label class="iump-labels"><?php _e('Public Key:', 'ihc');?></label>
									<input type="text" name="ihc_braintree_public_key" value="<?php echo $meta_arr['ihc_braintree_public_key'];?>" />
								</div>									
								
								<div class="iump-form-line iump-no-border">
									<label class="iump-labels"><?php _e('Private Key:', 'ihc');?></label>
									<input type="text" name="ihc_braintree_private_key" value="<?php echo $meta_arr['ihc_braintree_private_key'];?>" />
								</div>				
								<div class="iump-form-line">
									<?php 
										$site_url = site_url();
										$site_url = trailingslashit($site_url);
										$notify_url = add_query_arg('ihc_action', 'braintree', $site_url);										
										_e("<strong>Important:</strong> set your Webhook to: ");
										echo '<strong>' . $notify_url . '</strong>'; /// IHC_URL . 'braintree_webhook.php'
									?>
								</div> 	
								
								<div style="font-size: 11px; color: #333; padding-left: 10px;">
									<ul class="ihc-info-list">								
										<li><?php echo __("1. Go to ", 'ihc');?><a href="https://www.braintreepayments.com" target="_blank">https://www.braintreepayments.com</a> <?php echo __("(or ", 'ihc');?> <a href="https://www.braintreepayments.com/en-ro/sandbox" target="_blank">https://www.braintreepayments.com/en-ro/sandbox</a> <?php echo __(" if You want to use sandbox version) and login with Username and Password.", 'ihc');?></li>
										<li><?php echo __("2. After you login go to 'Account' section and select 'My User'. In this page click on 'View Authorizations'.", 'ihc');?></li>
										<li><?php echo __("3. In this page You will find the 'Public Key', 'Private Key' and 'Merchant ID'.", 'ihc');?></li>
										<li><?php echo __("4. After You copy and paste this keys You must set the webhook, to do that go to 'Settings' section and select 'Webhook'.", 'ihc');?></li>
										<li><?php echo __("5. Click on 'Create new Webhook' and in the next page check all subscription options and set the 'Destination URL' to ", 'ihc') . '<strong>' . $notify_url . '</strong>';?></li>  
									</ul>
								</div>
																																											
								<div class="ihc-wrapp-submit-bttn iump-submit-form">
									<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
								</div>						
							</div>
						</div>
				</form>				
				<?php						
			break;
		case 'bank_transfer':
			ihc_save_update_metas('payment_bank_transfer');//save update metas
			$meta_arr = ihc_return_meta_arr('payment_bank_transfer');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();
			?>
				<div class="iump-page-title">Ultimate Membership Pro - 
					<span class="second-text">
						<?php _e('Bank Transfer Services', 'ihc');?>
					</span>
				</div>		
			<form action="" method="post">
				<div class="ihc-stuffbox">
					<h3><?php _e('Bank Transfer Activation:', 'ihc');?></h3>
					<div class="inside">		
						<div class="iump-form-line">
							<h4><?php _e('Once all Settings are properly done, Activate the Payment Getway for further use.', 'ihc');?> </h4>
							<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($meta_arr['ihc_bank_transfer_status']) ? 'checked' : '';?>
								<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_bank_transfer_status');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" value="<?php echo $meta_arr['ihc_bank_transfer_status'];?>" name="ihc_bank_transfer_status" id="ihc_bank_transfer_status" /> 				
						</div>
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>			
					</div>	
				</div>
				<div class="ihc-stuffbox">
					<h3><?php _e('Bank Transfer Message:', 'ihc');?></h3>
					<div class="inside">
							<div style="padding-left: 5px; width: 70%;display:inline-block;">
								<?php wp_editor( $meta_arr['ihc_bank_transfer_message'], 'ihc_bank_transfer_message', array('textarea_name'=>'ihc_bank_transfer_message', 'quicktags'=>TRUE) );?>
							</div>
							<div style="width: 25%; display: inline-block; vertical-align: top;margin-left: 10px; color: #333;">
								<div>{siteurl}</div>
								<div>{username}</div>
								<div>{first_name}</div>
								<div>{last_name}</div>
								<div>{user_id}</div>
								<div>{level_id}</div>
								<div>{level_name}</div>
								<div>{amount}</div>
								<div>{currency}</div>
							</div>																							
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>	
					</div>			
				</div>
				
				<div class="ihc-stuffbox">
					<h3><?php _e('Bank Transfer Settings:', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Label:', 'ihc');?></label>
							<input type="text" name="ihc_bank_transfer_label" value="<?php echo $meta_arr['ihc_bank_transfer_label'];?>" />
						</div>
						
						<div class="iump-form-line iump-no-border">
							<label class="iump-labels"><?php _e('Order:', 'ihc');?></label>
							<input type="number" min="1" name="ihc_bank_transfer_select_order" value="<?php echo $meta_arr['ihc_bank_transfer_select_order'];?>" />
						</div>						
																															
						<div class="ihc-wrapp-submit-bttn iump-submit-form">
							<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
						</div>						
					</div>
				</div>
				
			</form>					
						
			<?php 		
			break;
		case 'payza':
			ihc_save_update_metas('payment_payza');//save update metas
			$meta_arr = ihc_return_meta_arr('payment_payza');//getting metas
			echo ihc_check_default_pages_set();//set default pages message
			echo ihc_check_payment_gateways();
			echo ihc_is_curl_enable();			
			?>
				<div class="iump-page-title">Ultimate Membership Pro - 
					<span class="second-text">
						<?php _e('Payza Services', 'ihc');?>
					</span>
				</div>	
				<form action="" method="post">
					<div class="ihc-stuffbox">
						<h3><?php _e('Payza Activation:', 'ihc');?></h3>						
						<div class="inside">		
							<div class="iump-form-line">
								<h4><?php _e('Once all Settings are properly done, Activate the Payment Getway for further use.', 'ihc');?> </h4>
								<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
									<?php $checked = ($meta_arr['ihc_payza_status']) ? 'checked' : '';?>
									<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_payza_status');" <?php echo $checked;?> />
									<div class="switch" style="display:inline-block;"></div>
								</label>
								<input type="hidden" value="<?php echo $meta_arr['ihc_payza_status'];?>" name="ihc_payza_status" id="ihc_payza_status" /> 												
							</div>
							<div class="ihc-wrapp-submit-bttn iump-submit-form">
								<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
							</div>								
						</div>
					   </div>
						<div class="ihc-stuffbox">
							<h3><?php _e('Payza Settings:', 'ihc');?></h3>
							<div class="inside">						
								
								<div class="iump-form-line iump-no-border">
									<label class="iump-labels"><?php _e('E-mail Address:', 'ihc');?></label>
									<input type="text" name="ihc_payza_email" value="<?php echo $meta_arr['ihc_payza_email'];?>" />
								</div>	
			
								<div class="iump-form-line">
									<?php 
										$site_url = site_url();
										$site_url = trailingslashit($site_url);
										$notify_url = add_query_arg('ihc_action', 'payza', $site_url);									
										_e("<strong>Important:</strong> set your Webhook to: ");
										echo '<strong>' . $notify_url . '</strong>'; /// IHC_URL . 'payza_webhook.php'
									?>
								</div> 	

								<div style="font-size: 11px; color: #333; padding-left: 10px;">
									<ul class="ihc-info-list">								
										<li><?php echo __('1. Go to ', 'ihc');?><a href="https://www.payza.com/" target="_blank">https://www.payza.com/</a> <?php echo __(' and login with Username and Password.', 'ihc');?></li>
										<li><?php echo __('2. After You login click on Business and select IPN Integration.', 'ihc');?></li>
										<li><?php echo __("3. In 'IPN Setup' section click on 'Set up your IPN now', in the next step you will have to write Your pin and then set the IPN to ", 'ihc') . '<strong>' . $notify_url . '</strong>';?></li> 																																																		
									</ul>
								</div>
								
								<div class="ihc-wrapp-submit-bttn iump-submit-form">
									<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
								</div>						
							</div>
						</div>				
						
					
				</form>					
			<?php
			break;
	}

}//end of switch
