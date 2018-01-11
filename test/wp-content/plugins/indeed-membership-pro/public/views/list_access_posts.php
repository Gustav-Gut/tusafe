<?php if (!empty($data['metas']['ihc_list_access_posts_custom_css'])):?>
	<style>
		<?php echo $data['metas']['ihc_list_access_posts_custom_css'];?>
	</style>
<?php endif;?>
<div class="iump-list-access-posts-wrapp <?php echo $data['metas']['ihc_list_access_posts_template'];?>">
	
	<?php if (!empty($data['metas']['ihc_list_access_posts_title'])):?>
		<div class="iump-list-access-posts-title"><h2><?php echo $data['metas']['ihc_list_access_posts_title'];?></h2></div>
	<?php endif;?>

	<?php if (!empty($data['items'])):?>
		<?php foreach ($data['items'] as $item):?>
			<div class="iump-list-access-posts-item-wrapp">
				<?php if (!empty($item['feature_image'])):?>
					<div class="iump-list-access-posts-the-feature-image">
					     <a href="<?php echo $item['permalink'];?>">
							<img src="<?php echo $item['feature_image'];?>" />
						</a>
					</div>					
				<?php endif;?>
				<div class="iump-list-access-posts-item-content">
					<?php if (!empty($item['title'])):?>
					  <div class="iump-list-title">
						<a href="<?php echo $item['permalink'];?>" class="iump-permalink">
							<?php echo $item['title'];?>
						</a>
					   </div>
					<?php endif;?>
					<div class="iump-list-details">
					<?php if (!empty($item['post_date'])):?>
						<div class="iump-list-access-posts-date">
						 <?php _e('Posted', 'ihc');?> 
						  <a href="<?php echo $item['permalink'];?>">	
							<?php echo $item['post_date'];?>
						  </a>	
						</div>	
					<?php endif;?>						
					<?php if (!empty($item['post_author'])):?>
						<div class="iump-list-access-posts-author">
					     <?php _e('By', 'ihc');?> 
						 <a href="<?php echo $item['permalink'];?>">	
							<?php echo $item['post_author'];?>
						</a>	
						</div>	
					<?php endif;?>
					<span class="ihc-clear"></span>	
					</div>
					<?php if (!empty($item['post_excerpt'])):?>
						<div class="iump-list-access-posts-the-excerpt">
							<?php echo $item['post_excerpt'];?>
						</div>	
					<?php endif;?>																	
				</div>
				<div class="ihc-clear"></div>	
			</div>
		<?php endforeach;?>	
		<?php if (!empty($data['pagination'])):?>
			<?php echo $data['pagination'];?>	
		<?php endif;?>
	<?php endif;?>
	
</div>
