<div class="ihc-ap-wrap">
	<?php if (!empty($data['title'])):?>
		<h3><?php echo do_shortcode($data['title']);?></h3>
	<?php endif;?>
	<?php if (!empty($data['content'])):?>
		<p><?php echo do_shortcode($data['content']);?></p>
	<?php endif;?>
	<?php echo ihc_print_social_media_icons('update', $this->users_sm);?>	
</div>

