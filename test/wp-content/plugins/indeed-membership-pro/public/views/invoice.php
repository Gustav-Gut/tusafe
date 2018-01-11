<script>
		var printhisopt = {
			importCSS: true,
            importStyle: true,
            loadCSS: "<?php echo IHC_URL . 'assets/css/style.css';?>",
         	debug: false,
        	printContainer: true, 
        	pageTitle: "",
        	removeInline: true,
        	printDelay: 333,
        	header: null,
        	formValues: false,
        };
</script>

<div class="iump-invoice-bttn-wrapp">
	<div class="iump-popup-print-bttn" onClick="jQuery('<?php echo '#' . $data['wrapp_id'];?>').printThis(printhisopt);"><?php _e('Print Invoice', 'ihc');?></div>	
</div>


<div class="iump-invoice-wrapp <?php echo $data['ihc_invoices_template'];?>" id="<?php echo $data['wrapp_id'];?>" >
	<div class="iump-invoice-logo"><img src="<?php echo $data['ihc_invoices_logo'];?>" /></div>
	<div class="iump-invoice-title"><?php echo $data['ihc_invoices_title'];?></div>
	<div class="ihc-clear"></div>
	<div class="iump-invoice-company-field"><?php echo $data['ihc_invoices_company_field'];?></div>	
	<div class="iump-invoice-invoice-code">
		<?php if (!empty($data['order_details']['code'])):?>
			<div><b><?php _e('Invoice ID:', 'ihc');?></b> <?php echo $data['order_details']['code'];?></div>
		<?php endif;?>
		<?php if (!empty($data['order_details']['txn_id'])):?>
			<div><b><?php _e('Transaction ID:', 'ihc');?></b> <?php echo $data['order_details']['txn_id'];?></div>
		<?php endif;?>
		<?php if (!empty($data['order_details']['create_date'])):?>
			<div><b><?php _e('Date:', 'ihc');?></b> <?php echo $data['order_details']['create_date'];?></div>
		<?php endif;?>
	</div>
	<div class="ihc-clear"></div>
	<div class="iump-invoice-client-details"><?php echo $data['ihc_invoices_bill_to'];?></div>	
	<div class="ihc-clear"></div>
	
	<div class="iump-invoice-list-details">
		<table>
			<thead>
				<tr>
					<td width="5%">#</td>
					<td width="75%"><?php _e('Description', 'ihc');?></td>
					<td width="20%"><?php _e('Amount', 'ihc');?></td>
				</tr>
			</thead>
			<tbody>
				<?php $i = 1; ?>
				<?php if (!empty($data['level_price']) && !empty($data['level_label'])):?>
					<tr <?php echo ($i%2==0) ? 'class="alternate"' : ''; ?> >
						<td><?php echo $i;$i++;?></td>
						<td><?php echo $data['level_label'];?></td>
						<td><?php echo $data['level_price'];?></td>
					</tr>
				<?php endif;?>
				<?php if (!empty($data['total_discount'])):?>
					<tr <?php echo ($i%2==0) ? 'class="alternate"' : ''; ?> >
						<td><?php echo $i;$i++;?></td>
						<td><?php _e('Total Discount:', 'ihc');?></td>
						<td><?php echo $data['total_discount'];?></td>
					</tr>
				<?php endif;?>	
				<?php if (!empty($data['total_taxes'])):?>
					<tr <?php echo ($i%2==0) ? 'class="alternate"' : ''; ?> >
						<td><?php echo $i;$i++;?></td>
						<td><?php _e('Total Taxes:', 'ihc');?></td>
						<td><?php echo $data['total_taxes'];?></td>
					</tr>
				<?php endif;?>	
				<?php if (!empty($data['total_amount'])):?>
				<?php if($i < 4){ 
						do{ ?>
						<tr <?php echo ($i%2==0) ? 'class="alternate"' : ''; ?> >
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>	
						<?php
							$i++;
						}while($i < 5);
					}
				?>
					<tr class="ihc-invoice-total">
						<td></td>
						<td align="right";><?php _e('Total Amount:', 'ihc');?></td>
						<td><?php echo $data['total_amount'];?></td>
					</tr>
				<?php endif;?>												
			</tbody>
		</table>
	</div>
	
	<?php if (!empty($data['ihc_invoices_footer'])):?>
		<div class="iump-invoice-footer"><?php echo $data['ihc_invoices_footer'];?></div>
	<?php endif;?>
	
</div>




