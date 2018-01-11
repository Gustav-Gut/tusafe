<?php if (!empty($data['style'])):?>
	<style><?php echo $data['style'];?></style>
<?php endif;?>

<div class="ihc-ap-wrap">
	<?php if (!empty($data['title'])):?>
		<h3><?php echo do_shortcode($data['title']);?></h3>
	<?php endif;?>
	<?php if (!empty($data['content'])):?>
		<p><?php echo do_shortcode($data['content']);?></p>
	<?php endif;?>

	<div class="iump-user-page-wrapper ihc_userpage_template_1">
		<div class="iump-user-page-box">
			<div class="iump-user-page-box-title"><?php _e('Update Profile', 'ihc');?></div>
			<div class="iump-register-form <?php echo $data['template'];?>"><?php echo $data['form'];?></div>
		</div>
	</div>

</div>