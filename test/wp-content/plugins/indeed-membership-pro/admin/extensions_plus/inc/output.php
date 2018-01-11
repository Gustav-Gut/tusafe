<link rel="stylesheet" href="<?php echo $this->dir_url . 'files/style.css';?>" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo $this->dir_url . 'files/font-awesome.css';?>" type="text/css" media="all" />
<script src="<?php echo $this->dir_url . '/files/admin.js';?>"></script>
<div class="indeed-addons-main-title">
	<div class="indeed-addons-main-title-left">
		<h3><i class="fa-ifs ifs-busket"></i>Extensions Plus</h3>
		<span>exclusive Envato Market items</span>
	</div>
	<div class="indeed-addons-main-title-right">
		<form method="post" action="" id="indeed_form">
			<input type="hidden" name="do_update" value="1"/>
			<div onClick="jQuery('#indeed_form').submit();"><i class="fa-ifs ifs-refresh"></i> Refresh</div>
		</form>
	</div>
	<div class="ind-clear"></div>
</div>
<?php 
	if (empty($items)){
		?>
		<h3>No Data Available! Please try again later!</h3>
		<?php 
		return;
	}
?>
<div class="indeed-addons-top">
	<ul class="indeed-addons-categories-list" data-current-tag="">
		<?php 
			if (!empty($cats) && is_array($cats) && count($cats)){
				foreach ($cats as $cat){
					$cat = array_merge($this->return_default_array_key('cats'), $cat);
					if (empty($cat['slug']) || empty($cat['label'])) continue;
					?>
						<li onClick="ihc_select_items_by_cat('<?php echo $cat['slug'];?>', this);"><a href="#"><?php echo $cat['label'];?></a></li>
					<?php 
				}
			}
		?>
		<div class="ind-clear"></div>
	</ul>
	<div class="indeed-addons-search">
	<input type="text" placeholder="Search Items" onKeyUp="indeed_search_value(this.value);"/>
	</div>
	<div class="ind-clear"></div>
</div>



<div id="all" class="indeed-items-wrapper">

<?php 
	/// TOP DESCRIPTION
	if (isset($settings['global_description'])){
		?>
		<p id="indeed_global_description" class="indeed-description"><?php echo $settings['global_description'];?></p>
		<?php 
	}
	if (!empty($cats) && is_array($cats) && count($cats)){
		foreach ($cats as $cat){
			$cat = array_merge($this->return_default_array_key('cats'), $cat);
			if (empty($cat['slug']) || empty($cat['description'])) continue;
			?>
				<p style="display: none;" class="indeed-description" id="indeed_<?php echo $cat['slug'];?>_description"><?php echo stripslashes($cat['description']);?></p>
			<?php  			
		}
	}
?>

<?php 
	if (count($items)){
		foreach ($items as $item){
			$item = array_merge($this->return_default_array_key('items'), $item);
			$installed = (in_array($item['item_name'], self::$installed_items)) ? TRUE : FALSE;
			$keywords = $item['keywords'];
			$keywords[] = $item['title'];
			$keywords = implode(', ', $keywords);			
			?>
			<div data-keywords="<?php echo $keywords;?>" data-category="<?php echo $item['category'];?>" class="indeed-item-wrapp">
			  <div class="indeed-item">
			  	<?php if (isset($item['new']) && $item['new'] == 1){ ?>
					<div class="indeed-addon-ribbon-new"></div>
				<?php }?>
				<?php if (isset($item['updated']) && $item['updated'] == 1){ ?>
					<div class="indeed-addon-ribbon-updated"></div>
				<?php }?>
			 	<?php if (!empty($item['image'])){ ?>
			  	<div class="indeed-addon-img-wrapper">
					<?php if (!empty($item['envato_link']))?>
					<a href="<?php echo $item['envato_link']?>" target="_blank">
						<img src="<?php echo $item['image'];?>" class="indeed-addon-img-preview" />
						<div class="indeed-addon-img-cover"><i class="fa-ifs ifs-frw"></i></div>
					<?php if (!empty($item['envato_link']))?>
					</a>		
				</div>
				<?php }?>
				<div class="indeed-item-inner">
					<?php if (!empty($item['title'])){ ?>
						<div class="indeed-addon-title"><?php echo $item['title']?></div>
					<?php } ?>
					<?php if (!empty($item['author_name'])){?>
						<div class="indeed-addon-author"><a href="<?php if (!empty($item['author_link'])) echo $item['author_link']; else echo '#';?>" target="_blank">by <?php echo $item['author_name']?></a></div>
					<?php }?>
					<div class="indeed-addon-short-description">
					<?php 
						$item_desc = '';
						if (!empty($item['short_description'])){ $item_desc = $item['short_description']; }
							elseif(!empty($item['long_description'])) {$item_desc = $item['long_description'];}
					?>
					<?php echo substr($item_desc,0,133);?>...<a href="" class="indeed-addon-more">Learn more</a>
					
					</div>
					<?php if (!empty($item['envato_link'])){?>
					<div class="indeed-addon-envato-link">
						<a class="button" href="<?php echo $item['envato_link']?>" target="_blank">check on <strong>Envato</strong></a>
					</div>
					<?php }?>
					<?php if (!empty($item['demo_link'])){?>
					<div class="indeed-addon-demo-link">
						<?php if ($installed){?>
						<div class="button button-secondary disabled" style="">Installed</div>
						<?php } else { ?>
						<a class="button button-primary" href="<?php echo $item['demo_link']?>" target="_blank">Try Demo</a>
						<?php } ?>
					</div>
					<?php }?>
					<div class="ind-clear"></div>
					<?php if (!empty($keywords)){?>
					<div class="indeed-addon-keywords"><?php echo $keywords?></div>
					<?php }?>
				</div>
				<div class="indeed-addon-bottom-side">
					<div class="ind-side-left">
						<div class="indeed-addon-purchase-button"><a href="<?php if (!empty($item['envato_link'])) echo $item['envato_link']; else echo '#';?>"><span>Get it now</span> 
						<?php if (isset($item['price'])) echo 'from $' . $item['price'];?></a></div>
					</div>
					<div class="ind-side-right">
						<strong>Exclusive</strong> available on Envato Market
					</div>		
					<div class="ind-clear"></div>
				</div>
			  </div>
			</div>
			<?php 
		}
	}
?>
	<div class="ind-clear"></div>
</div>
