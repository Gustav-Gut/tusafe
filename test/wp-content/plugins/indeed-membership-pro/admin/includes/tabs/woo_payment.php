<?php
ihc_save_update_metas('woo_payment');//save update metas
$data['metas'] = ihc_return_meta_arr('woo_payment');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
$data['items'] = Ihc_Db::get_woo_product_level_relations();
?>
<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php _e('WooCommerce Payment Integration', 'ihc');?></h3>
		<div class="inside">
			
			<div class="iump-form-line">
					<h2><?php _e('Activate/Hold WooCommerce Payment Integration', 'ihc');?></h2>
					<p style="max-width:800px;"><?php _e('Link A WooCommerce Product with a Membership Level from Product Edit Page. Once an Order with that Product is created into Woo, a new UMP Order is created and the Subscription is managed relating on that order status.', 'ihc');?></p>
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['ihc_woo_payment_on']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_woo_payment_on');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_woo_payment_on" value="<?php echo $data['metas']['ihc_woo_payment_on'];?>" id="ihc_woo_payment_on" /> 												
				<p style=" font-size:110%; font-weight:bold;"><?php _e('The user will have an Active Level only when the WooCommerce Order will be set as Completed (manually or automatically).', 'ihc');?></p>
			</div>					
											
			<div class="ihc-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>		
					
		</div>
	</div>
				
		<?php if (!empty($data['items'])):?>
			<div class="ihc-stuffbox">	
				<table class="wp-list-table widefat fixed tags">
					<thead style="background: #f1f4f8 !important;    border-bottom: 1px solid #ccc;box-shadow: inset 0px -5px 10px 2px rgba(0,0,0,0.03);
    line-height: 1.4;">
						<tr>
							<td style="font-weight:400;font-family: 'Oswald', arial, sans-serif !important;padding: 16px 12px;"><?php _e('Ultimate Membership Pro Level', 'ihc');?></td>
							<td style="font-weight:400;font-family: 'Oswald', arial, sans-serif !important;padding: 16px 12px;"><?php _e('WooCommerce Product', 'ihc');?></td>
						</tr>						
					</thead>
					<tbody class="uap-alternate">
					<?php foreach ($data['items'] as $array):?>
					<tr>
						<td><span  class="uap-list-affiliates-name-label"><a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=levels&edit_level=' . $array['level_id']);?>" target="_blank"><?php echo $array['level_label'];?></a></span></td>
						<td><a href="<?php echo admin_url('post.php?post=' . $array['product_id'] . '&action=edit');?>" target="_blank"><?php echo $array['product_label'];?></a></td>
					</tr>						
					<?php endforeach;?>						
					</tbody>
				</table>
			</div>
		<?php endif;?>	
				
</form>