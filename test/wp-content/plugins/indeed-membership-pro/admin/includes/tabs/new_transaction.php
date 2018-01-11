<?php 
	$order_id = (empty($_GET['order_id'])) ? 0 : $_GET['order_id'];
	$data = Ihc_Db::get_order_data_by_id($order_id);
	if ($data):
		$subtab = admin_url('admin.php?page=ihc_manage&tab=orders');
?>
<form action="<?php echo $subtab;?>" method="post">
	<div class="ihc-stuffbox">
		<h3><?php _e('Add New Transaction', 'ihc');?></h3>
		<div class="inside">
			
			<div class="row" style="margin-left:0px;">
				<div class="col-xs-5">
					<div class="input-group" style="margin:30px 0 5px 0;">
					<span class="input-group-addon ihc-special-input-label" id="basic-addon1" style="min-width:170px; text-align:right;"><?php _e('Customer Username:', 'ihc');?></span>
					<input type="text" class="form-control" name="" disabled="disabled" value="<?php echo $data['user'];?>" />
					</div>
				</div>
			</div>
			<div class="row" style="margin-left:0px;">
				<div class="col-xs-5">
					<div class="input-group" style="margin:30px 0 5px 0;">
					<span class="input-group-addon ihc-special-input-label" id="basic-addon1" style="min-width:170px; text-align:right;"><?php _e('Transaction Item:', 'ihc');?></span>
					<input type="text" class="form-control" name="" disabled="disabled" value="<?php echo $data['level'];?>" />
					</div>
				</div>
			</div>
			<div class="row" style="margin-left:0px;">
				<div class="col-xs-5">
					<div class="input-group" style="margin:30px 0 5px 0;">
					<span class="input-group-addon ihc-special-input-label" id="basic-addon1" style="min-width:170px; text-align:right;"><?php _e('Total Amount:', 'ihc');?></span>
					<input type="text" class="form-control" name="" disabled="disabled" value="<?php echo $data['amount_value'];?>" />
					<div class="input-group-addon"><?php echo $data['amount_type'];?></div>
					</div>
				</div>
			</div>
			<?php if (empty($data['metas']['ihc_payment_type']) || 1==1):?>
			<div class="row" style="margin-left:0px;">
				<div class="col-xs-5">
				<h4><?php echo __('Payment Method', 'ihc');?></h4>
				<select name="ihc_payment_type" class="form-control m-bot15">
					<?php
						$payments = ihc_get_active_payment_services();
						if ($payments):
							foreach ($payments as $k=>$v):
								$selected = ($k=='bank_transfer') ? 'selected' : '';
								?>
								<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
								<?php
							endforeach;
						endif;
					?>
				</select>
				</div>
			</div>
			<?php else:?>
				
			<div class="row" style="margin-left:0px;">
				<div class="col-xs-5">
					<div class="input-group" style="margin:30px 0 5px 0;">
					<span class="input-group-addon ihc-special-input-label" id="basic-addon1" style="min-width:170px; text-align:right;"><?php _e('Payment Service:', 'ihc');?></span>
					<input type="text" class="form-control" name="" disabled="disabled" style="text-transform: capitalize; font-weight:bold;" value="<?php echo $data['metas']['ihc_payment_type'];?>" />
					</div>
				</div>
			</div>
			<input type="hidden" value="<?php echo $data['metas']['ihc_payment_type'];?>" name="ihc_payment_type" />
			<?php endif;?>
			<div class="row" style="margin-left:0px;">
				<div class="col-xs-5">
					<h2><?php echo __('Details: ', 'ihc');?></h2>
					<p><?php echo __('Provide some particular details about this Transaction manually set ', 'ihc');?></p>
					<textarea name="details" style="width: 100%; height: 150px;"></textarea>
				</div>
			</div>
			
			
			<?php if (!empty($data['metas']['txn_id'])) :?>
				<input type="hidden" value="<?php echo $data['metas']['txn_id'];?>" name="txn_id" />
			<?php endif;?>
			<input type="hidden" value="<?php echo $data['uid'];?>" name="uid" />
			<input type="hidden" value="<?php echo $data['lid'];?>" name="level" />
			<input type="hidden" value="<?php echo $order_id;?>" name="order_id" /> 
			<input type="hidden" value="<?php echo $data['amount_value'];?>" name="amount" />
			<input type="hidden" value="<?php echo $data['amount_type'];?>" name="currency" />
			
			<div style="margin-top: 15px;" class="ihc-wrapp-submit-bttn">
				<input type="submit" value="<?php _e('Add Transaction', 'ihc');?>" name="submit_new_payment" class="button button-primary button-large" />
			</div>				
		</div>
		
	</div>
</form>	
<?php else:?>
	<div><?php _e('No details available!', 'uap');?></div>
<?php endif;?>