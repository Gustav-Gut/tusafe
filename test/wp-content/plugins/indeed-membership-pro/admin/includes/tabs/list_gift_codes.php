<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=gifts');?>"><?php _e('Settings', 'ihc');?></a>		
	<a class="ihc-subtab-menu-item" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=generated-gift-code');?>"><?php _e('Generated Membership Gift Codes', 'ihc');?></a>	
</div>
<?php
if (isset($_GET['delete_generated_code'])){
	Ihc_Db::do_delete_generated_gift_code($_GET['delete_generated_code']);
}
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();

$limit = 25;
$total = Ihc_Db::get_count_all_gift_codes();

$current_page = (empty($_GET['ihcdu_page'])) ? 1 : $_GET['ihcdu_page'];
if ($current_page>1){
	$offset = ( $current_page - 1 ) * $limit;
} else {
	$offset = 0;
}



require_once IHC_PATH . 'classes/Ihc_Pagination.class.php';
$url  = admin_url('admin.php?page=ihc_manage&tab=generated-gift-code');
$pagination_object = new Ihc_Pagination(array(
											'base_url' => $url,
											'param_name' => 'ihcdu_page',
											'total_items' => $total,
											'items_per_page' => $limit,
											'current_page' => $current_page,
));
$pagination = $pagination_object->output();
if ($offset + $limit>$total){
	$limit = $total - $offset;
} 
$data = Ihc_Db::get_all_gift_codes($limit, $offset);

$currency = get_option('ihc_currency');
$levels = get_option('ihc_levels');
$levels[-1]['label'] = __('All', 'ihc');
?>
<div class="iump-page-title">Ultimate Membership Pro - 
			<span class="second-text">
				<?php _e('MemberShip Codes', 'ihc');?>
			</span>
</div>
		
<?php if (!empty($data)):?>
<table class="wp-list-table widefat fixed tags" style="margin-top: 50px;">
	<thead>
		<tr>
			<th><?php _e('Username', 'ihc');?></th>
			<th><?php _e('Gift Code', 'ihc');?></th>
			<th><?php _e('Discount Value', 'ihc');?></th>
			<th><?php _e('Discount for Level', 'ihc');?></th>
			<th><?php _e('Gift Status', 'ihc');?></th>
			<th><?php _e('Action', 'ihc');?></th>
		</tr>
	</thead>
	<?php $i = 1;
		foreach ($data as $gift_id => $gift):?>
		<tr class="<?php if($i%2==0) echo 'alternate';?>">
			<td style="color: #21759b; font-weight:bold; width:120px;font-family: 'Oswald', arial, sans-serif !important;font-size: 14px;font-weight: 400;"><?php echo $gift['username'];?></td>
			<td><?php echo $gift['code'];?></td>
			<td><?php 
				if ($gift['discount_type']=='price'){
					echo ihc_format_price_and_currency($currency, $gift['discount_value']);
				} else {
					echo $gift['discount_value'] . '%';
				}
			?></td>
			<td>
				<div class="level-type-list ">
				<?php 
					$l = $gift['target_level'];
					if (isset($levels[$l]) && isset($levels[$l]['label'])){
						echo $levels[$l]['label'];		
					} 
				?>
				</div>
			</td>
			<td><?php 
				if ($gift['is_active']):
					_e('Unused', 'ihc');
				else :
					_e('Used', 'ihc');
				endif;	
			?></td>
			<td><a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=generated-gift-code&delete_generated_code=' . $gift_id);?>"><?php _e('Delete', 'ihc');?></a></td>
		</tr>
	<?php 
	$i++;
	endforeach;?>
</table>
<?php echo $pagination;?>
<?php else : ?>
	<h3><?php _e('No Gift Codes available!', 'ihc');?></h3>
<?php endif;?>