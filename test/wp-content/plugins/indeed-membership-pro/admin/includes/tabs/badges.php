<?php
ihc_save_update_metas('badges');//save update metas
$data['metas'] = ihc_return_meta_arr('badges');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
if (!empty($_POST['badge_image_url'])){
	$levels_data = get_option('ihc_levels');
	foreach ($_POST['badge_image_url'] as $id=>$value){
		$levels_data[$id]['badge_image_url'] = $value;
	}
	update_option('ihc_levels', $levels_data);
}
$levels = get_option('ihc_levels');
?>
<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php _e('Membership Level Badges', 'ihc');?></h3>
		<div class="inside">
			
			<div class="iump-form-line">
				<h2><?php _e('Activate/Hold Memberhsip Badges', 'ihc');?></h2>
				<p><?php _e('Add custom badge for each Level for a better approach. Be sure that you Add images with a proper size and ratio and for each Level.', 'ihc');?></p>
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['ihc_badges_on']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_badges_on');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_badges_on" value="<?php echo $data['metas']['ihc_badges_on'];?>" id="ihc_badges_on" /> 												
			</div>					

			<div class="iump-form-line">
				<h2><?php _e('Custom CSS', 'ihc');?></h2>	
				<textarea name="ihc_badge_custom_css" style="width: 100%; height: 150px;"><?php echo $data['metas']['ihc_badge_custom_css'];?></textarea>
			</div>
														
			<div class="ihc-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>		
					
		</div>
	</div>				


<?php if ($levels):?>
	<div class="ihc-stuffbox">
		<table class="wp-list-table widefat fixed tags">
			<thead style="background: #f1f4f8 !important;    border-bottom: 1px solid #ccc;box-shadow: inset 0px -5px 10px 2px rgba(0,0,0,0.03);
    line-height: 1.4;">
				<tr>
					<td style="width: 20%;font-weight:bold;font-family: 'Oswald', arial, sans-serif !important;padding: 16px 12px;"><?php _e('Level', 'ihc');?></td>
					<td style="width: 20%;font-weight:bold;font-family: 'Oswald', arial, sans-serif !important;padding: 16px 12px;"><?php _e('Image', 'ihc');?></td>
					<td style="width: 60%;font-weight:bold;font-family: 'Oswald', arial, sans-serif !important;padding: 16px 12px;"><?php _e('Image URL', 'ihc');?></td>
				</tr>						
			</thead>
			<tbody class="uap-alternate">	
		<?php foreach ($levels as $id => $level):?>
			<tr>
				<td style="vertical-align: middle;"><span  class="uap-list-affiliates-name-label"><?php echo $level['label'];?></span></td>
				<?php if (empty($level['badge_image_url'])) $level['badge_image_url'] = '';?>
				<td>
					<?php $display = (empty($level['badge_image_url'])) ? 'none' : 'block';?>
					<img src="<?php echo $level['badge_image_url'];?>" style="width: 75px; display: <?php echo $display;?>;" id="<?php echo 'img_level' . $id;?>"/>
				</td>
				<td style="vertical-align: middle;">				
					<input type="text" class="form-control" onclick="open_media_up(this, '<?php echo '#img_level' . $id;?>');" value="<?php echo $level['badge_image_url'];?>" name="<?php echo "badge_image_url[$id]";?>" id="<?php echo 'badge_image_url'.$id;?>" style="width: 90%;display: inline; float:none; min-width:500px;">
					<i class="fa-ihc ihc-icon-remove-e" onclick="jQuery('<?php echo '#badge_image_url'.$id;?>').val('');jQuery('<?php echo '#img_level' . $id;?>').css('display', 'none');" title="Remove Badge"></i>				
				</td>
			</tr>
		<?php endforeach;?>
			</tbody>
		</table>
	</div>
<?php endif;?>

</form>