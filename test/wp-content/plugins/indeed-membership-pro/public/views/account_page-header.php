<?php if (!empty($data['custom_css'])):?>
	<style><?php echo $data['custom_css'];?></style>
<?php endif;?>

<div class="ihc-account-page-wrapp" id="ihc_account_page_wrapp">

	<?php 
		$top_style='';
		if (empty($this->settings['ihc_ap_edit_background']) && ($this->settings['ihc_ap_top_template'] == 'ihc-ap-top-theme-2' || $this->settings['ihc_ap_top_template'] == 'ihc-ap-top-theme-3' )){
			$top_style .='style="padding-top:75px;"'; 			
		}
	?>
			
		<div class="ihc-user-page-top-ap-wrapper <?php echo (!empty($this->settings['ihc_ap_top_template']) ? $this->settings['ihc_ap_top_template'] : '');?>" <?php echo $top_style;?> >
		  
		  	<div class="ihc-left-side">
				<div class="ihc-user-page-details">
					<?php if (!empty($data['avatar'])):?>
						<div class="ihc-user-page-avatar"><img src="<?php echo $data['avatar'];?>" class="ihc-member-photo"/></div>
					<?php endif;?>
				</div>
			</div>
			
			<div class="ihc-middle-side">	
				<div class="ihc-account-page-top-mess">
					<?php if (!empty($data['welcome_message'])):?>
						<?php echo do_shortcode($data['welcome_message']);?>
					<?php else:?>
						<div class="iump-user-page-mess"><?php echo __('Welcome', 'ihc');?>,</div>
						<div class="iump-user-page-name"><?php echo $first_name . ' ' . $last_name;?></div>
						<div class="iump-user-page-email"><?php echo $this->current_user->user_email;?></div>
					<?php endif;?>		
				</div>	
				<?php if (!empty($data['levels'])):?>
					<div class="ihc-top-levels">						
						<?php foreach ($data['levels'] as $lid => $level):?>
							<?php 
				    			$time_arr = ihc_get_start_expire_date_for_user_level($this->current_user->ID, $lid);
						    	$is_expired_class = '';
								if (isset($time_arr['expire_time']) && time()>strtotime( $time_arr['expire_time'] ) ){			    						   								
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
				<?php if (!empty($data['sm'])):?>
					<div class="ihc-ap-top-sm">
						<?php echo $data['sm'];?>						
					</div>
				<?php endif;?>				
			</div>
			
			<div class="ihc-clear"></div>
				<?php 
					if (!empty($this->settings['ihc_ap_edit_background'])):
		  				$bk_style = '';
			 		if (!empty($this->settings['ihc_ap_top_background_image'])):
			 			$bk_style = 'style="background-image:url('.$this->settings['ihc_ap_top_background_image'].');"';	
			 		endif;
			 	?>
		  	<div class="ihc-user-page-top-ap-background" <?php echo $bk_style;?>></div>
		  <?php endif;?>
		  
		</div>	
		<div class="ihc-user-page-content-wrapper  <?php echo @$this->settings['ihc_ap_theme'];?>">

