<div class="ihc-mobile-bttn-wrapp"><i class="ihc-mobile-bttn"></i></div>
<div class="ihc-ap-menu">
	<?php if ($data['menu']):?>
		<?php foreach ($data['menu'] as $k => $array):?>
			<div class="<?php echo $array['class'];?>"><a href="<?php echo $array['url'];?>"><i class="<?php echo 'fa-ihc fa-' . $k . '-account-ihc';?>"></i><?php echo $array['title'];?></a></div>
		<?php endforeach;?>
	<?php endif;?>
	<div class="ihc-clear"></div>
</div>
