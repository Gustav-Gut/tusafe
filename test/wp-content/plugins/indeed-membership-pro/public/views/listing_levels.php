<?php if (!empty($data['levels'])):?>
	<?php if (!empty($data['custom_css'])):?>
		<style><?php echo $data['custom_css'];?></style>
	<?php endif;?>
	<div class="iump-wrapp-listing-levels">
		<?php foreach ($data['levels'] as $level):?>
			<?php $is_expired_class = (empty($level['is_expired'])) ? '' : 'ihc-expired-level';?>
			<?php if (!empty($attr['badges']) && !empty($level['badge_image_url'])):?>
				<div class="iump-badge-wrapper <?php echo $is_expired_class;?>">
					<img src="<?php echo $level['badge_image_url'];?>" class="iump-badge" title="<?php echo $level['label'];?>" />
				</div>
			<?php else:?>
				<div class="iump-listing-levels-label  <?php echo $is_expired_class;?>"><?php echo $level['label'];?></div>
			<?php endif;?>
		<?php endforeach;?>		
	</div>
<?php endif;?>
