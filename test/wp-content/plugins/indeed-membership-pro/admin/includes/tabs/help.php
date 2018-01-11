<?php $disabled = (ihc_is_curl_enable()) ? 'disabled' : '';?>
<div style="width: 97%">
	<div class="ihc-dashboard-title">
		Ultimate Membership Pro - 
		<span class="second-text">
			<?php _e('Help Section', 'ihc');?>
		</span>
	</div>
	
	
	<div class="metabox-holder indeed">
		<div class="ihc-stuffbox">
			<?php 
				if (isset($_REQUEST['ihc_save_licensing_code']) && isset($_REQUEST['ihc_licensing_code'])){
					$proceed = ihc_envato_licensing($_REQUEST['ihc_licensing_code']);
				}
				$envato_code = get_option('ihc_envato_code');
			?>
			<h3>
				<label style=" font-size:16px;">
					<?php _e('Activate Ultimate Membership Pro', 'ihc');?>
				</label>
			</h3>
			<form method="post" action="">
				<div class="inside">
					<?php if ($disabled):?>
						<div class="iump-form-line iump-no-border" style="font-weight: bold; color: red;"><?php _e("cURL is disabled. You need to enable if for further activation request.")?></div>
					<?php endif;?>
					<div class="iump-form-line iump-no-border" style="width:10%; float:left; box-sizing:border-box; text-align:right; font-weight:bold;">
						<label for="tag-name" class="iump-labels" style="text-align: left;"><?php _e('Purchase Code', 'ihc');?></label>
					</div>	
					<div class="iump-form-line iump-no-border" style="width:70%; float:left; box-sizing:border-box;">	
						<input name="ihc_licensing_code" type="text" value="<?php echo $envato_code;?>" style="width:100%;"/>
					</div>
					<div class="ihc-stuffbox-submit-wrap iump-submit-form" style="width:20%; float:right; box-sizing:border-box; text-align:center;">
						
						<input type="submit" value="<?php _e('Activate', 'ihc');?>" name="ihc_save_licensing_code" <?php echo $disabled;?> class="button button-primary button-large" />
					</div>
					<div class="ihc-clear"></div>
					<div class="ihc-license-status"><?php 
						if (isset($proceed)){
							if ($proceed){
								?>
								<div class="ihc-dashboard-valid-license-code"><?php _e("You've activated the Ultimate Membership Pro plugin!")?></div>
								<?php 
							} else {
								?>
								<div class="ihc-dashboard-err-license-code"><?php _e("You have entered an invalid purchase code or the Envato API could be down for a moment.")?></div>
								<?php 	
							}
						}
					?></div>
					<div style="padding:0 60px;">
					<p>A valid purchase code Activate the Full Version of<strong> Ultimate Memership Pro</strong> plugin and provides access on support system. A purchase code can only be used for <strong>ONE</strong> Ultimate Membership Pro for WordPress installation on <strong>ONE</strong> WordPress site at a time. If you previosly activated your purchase code on another website, then you have to get a <a href="http://codecanyon.net/item/ultimate-membership-pro-wordpress-plugin/12159253?ref=azzaroco" target="_blank">new Licence</a>.</p>
					<h4>Where can I find my Purchase Code?</h4>
					<a href="http://codecanyon.net/item/ultimate-membership-pro-wordpress-plugin/12159253?ref=azzaroco" target="_blank">
						<img src="<?php echo IHC_URL;?>admin/assets/images/purchase_code.jpg" style="margin: 0 auto; display: block;"/>
						</a>
					</div>	
				</div>
			</form>		
		</div>
	</div>
	
<div class="metabox-holder indeed">
	<div class="ihc-stuffbox">
		<h3>
			<label style="text-transform: uppercase; font-size:16px;">
				<?php _e('Contact Support', 'ihc');?>
			</label>
		</h3>
		<div class="inside">
			<div class="submit" style="float:left; width:80%;">
				<?php _e('In order to contact Indeed support team you need to create a ticket providing all the necessary details via our support system:', 'ihc');?> support.wpindeed.com
			</div>
			<div class="submit" style="float:left; width:20%; text-align:center;">
				<a href="http://support.wpindeed.com/open.php?topicId=0" target="_blank" class="button button-primary button-large"> <?php _e('Submit Ticket', 'ihc');?></a>
			</div>
			<div class="clear"></div>
		</div>
	</div>

	<div class="ihc-stuffbox">
		<h3>
			<label style="text-transform: uppercase; font-size:16px;">
		    	<?php _e('Documentation', 'ihc');?>
		    </label>
		</h3>
		<div class="inside">
			<iframe src="https://null24.net/" width="100%" height="1000px" ></iframe>
		</div>
	</div>	
</div>
</div>
<?php 
