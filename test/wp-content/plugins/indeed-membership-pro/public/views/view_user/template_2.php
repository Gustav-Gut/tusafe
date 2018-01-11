<div class="iump-view-user-wrapp-temp2 iump-color-scheme-<?php echo $data['color_scheme_class'];?>">
	<?php if ($data['color_scheme_class'] !=''){ ?>	
	<style>
		.iump-view-user-wrapp-temp2 .ihc-levels-wrapper .ihc-top-level-box{
			background-color:#<?php echo $data['color_scheme_class'];?>;
			border-color:#<?php echo $data['color_scheme_class'];?>;
			color:#fff;
		}
		.iump-view-user-wrapp-temp2 .ihc-levels-wrapper{
		
		}
		.iump-view-user-wrapp-temp2 .ihc-middle-side .iump-username{
			color:#<?php echo $data['color_scheme_class'];?>;
		}
		.iump-view-user-wrapp-temp2 .ihc-left-side .ihc-user-page-avatar img{
			border-color:#<?php echo $data['color_scheme_class'];?>;
		}
		.iump-view-user-wrapp-temp2 .ihc-levels-wrapper{
			background-color: transparent;
		}
	</style>
	<?php } ?>
	<?php if (empty($data['banner'])){ ?>
	<style>	
		.iump-view-user-wrapp-temp2 .ihc-user-page-top-ap-wrapper{
			padding-top:10px;
		}
		.iump-view-user-wrapp-temp2 .ihc-left-side{
			margin-bottom:0px;
		}
		.iump-view-user-wrapp-temp2 .ihc-left-side .ihc-user-page-details{
			top:0px;
		}
	</style>
	<?php } ?>
	<div class="ihc-user-page-top-ap-wrapper">
	<?php if (!empty($data['avatar'])):?>
		<div class="ihc-left-side">
			<div class="ihc-user-page-details">
				<div class="ihc-user-page-avatar"><img src="<?php echo $data['avatar'];?>" class="ihc-member-photo"></div>
			</div>
		</div>
	<?php endif;?>
	<div class="ihc-middle-side">
		<?php if (!empty($data['flag'])):?>
			<div class="iump-flag"><?php echo $data['flag'];?></div>
		<?php endif;?>	
		<?php if (!empty($data['name'])):?>
			<div class="iump-name"><?php echo $data['name'];?></div>
		<?php endif;?>
		<?php if (!empty($data['username'])):?>
			<div class="iump-username">- <?php echo $data['username'];?> -</div>
		<?php endif;?>
		
		<div class="iump-addiional-elements">
		
		<?php if (!empty($data['email'])):?>
			<span class="iump-element iump-email"><?php echo $data['email'];?></span>
		<?php endif;?>
		
		<?php if (!empty($data['since'])):?>
			<span class="iump-element iump-since"><?php echo __('Joined ', 'ihc');?><?php echo $data['since'];?></span>
		<?php endif;?>
		</div>
		
	</div>
	<div class="ihc-clear"></div>
	<?php if (!empty($data['banner'])):?>
	<div class="ihc-user-page-top-ap-background" style="background-image:url(<?php echo $data['banner'];?>);"></div>
	<?php endif;?>
	
	</div>
	<?php //echo "<pre>"; print_r($data); ?>
	<?php if (!empty($data['levels'])):?>
		<div class="ihc-levels-wrapper">
			<?php foreach ($data['levels'] as $lid => $level):?>
				<?php 
					$is_expired_class = '';
					if (isset($level['expire_time']) && time()>strtotime( $level['expire_time'] ) ){			    						   								
						$is_expired_class = 'ihc-expired-level';
					}
				?>							
				<?php if (!empty($data['badges_metas']['ihc_badges_on']) && !empty($level['badge_image_url'])):?>
					<div class="iump-badge-wrapper <?php echo $is_expired_class;?>"><img src="<?php echo $level['badge_image_url'];?>" class="iump-badge" title="<?php echo $level['label'];?>" /></div>
				<?php elseif (!empty($level['label'])):?>
					<div class="ihc-top-level-box <?php echo $is_expired_class;?>"><?php echo $level['label'];?></div>
				<?php endif;?>
			<?php endforeach;?>
		</div>
	<?php endif;?>

	

	<?php if (!empty($data['custom_fields'])):?>
		<div class="iump-user-fields-list"> 
			<?php foreach ($data['custom_fields'] as $label => $value):?>
				<?php if ($value!=''):?>
					<div class="iump-user-field"><div class="iump-label"><?php echo $label; ?></div> <div class="iump-value"> <?php echo $value;?> </div><div class="ihc-clear"></div></div>
					
				<?php endif;?>
			<?php endforeach;?>
		</div>
	<?php endif;?>
							
	<?php if (!empty($data['content'])):?>
		<div class="iump-additional-content">
			<?php echo $data['content'];?>
		</div>	
	<?php endif;?>
	
</div>
