<?php if (!empty($coupon_field) || !empty($payment_select_string) || !empty($taxes)):?>
<div class="iump-subscription-page-top">
	<div class="iump-subscription-page-top-title">
		<?php echo __("Payment details", "ihc");?>
	</div>
	<div class="iump-subscription-page-top-fields <?php echo $register_template;?>">
		<?php if (!empty($coupon_field)):?>
			<div class='iump-form-line-register'>
				<label class='iump-labels-register'><?php echo __("Coupon Code", "ihc");?></label>
				<input type='text' id='ihc_coupon' class="iump-form-text"/>
			</div>	
		<?php endif;?>
		
		<?php if (!defined('IHC_HIDDEN_PAYMENT_PRINT')) define('IHC_HIDDEN_PAYMENT_PRINT', TRUE);?>
		
		<input type="hidden" name="ihc_payment_gateway" value="<?php echo $the_payment_type;?>" />
		
		<?php if (!empty($payment_select_string)):?>
			<?php echo $payment_select_string;?>
		<?php endif;?>

	<?php if (!empty($taxes)):?>
	<div class="iump-tax-rate-inform">
		<div class="iump-subscription-page-top-taxes-title"><?php echo __("Additional Taxes", "ihc");?></div>
		<div class="iump-level-details-register">
			<?php foreach ($taxes as $label => $value):?>
				<span class="iump-level-details-register-name"><?php echo $label;?></span>
				<span class="iump-level-details-register-price"><?php echo $value;?></span>	
				<div class="ihc-clear"></div>		
			<?php endforeach;?>
		</div>
	</div>
	<?php endif;?>
	</div>
</div>
<?php endif;?>