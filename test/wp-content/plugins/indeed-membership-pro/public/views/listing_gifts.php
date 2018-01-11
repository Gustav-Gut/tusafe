<table class="ihc-account-subscr-list">
	<thead>
		<tr>
			<th><?php _e('Gift Code', 'ihc');?></th>
			<th><?php _e('Discount Value', 'ihc');?></th>
			<th><?php _e('Discount for Level', 'ihc');?></th>
			<th><?php _e('Gift Status', 'ihc');?></th>
		</tr>
	</thead>
	<?php foreach ($gifts as $gift):?>
		<tr>
			<td><?php echo $gift['code'];?></td>
			<td><?php 
				if ($gift['discount_type']=='price'){
					echo ihc_format_price_and_currency($currency, $gift['discount_value']);
				} else {
					echo $gift['discount_value'] . '%';
				}
			?></td>
			<td>
				<?php 
					$l = $gift['target_level'];
					if (isset($levels[$l]) && isset($levels[$l]['label'])){
						echo $levels[$l]['label'];		
					}	
				?>
			</td>
			<td><?php 
				if ($gift['is_active']):
					_e('Unused', 'ihc');
				else :
					_e('Used', 'ihc');
				endif;	
			?></td>
		</tr>
	<?php endforeach;?>
</table>
