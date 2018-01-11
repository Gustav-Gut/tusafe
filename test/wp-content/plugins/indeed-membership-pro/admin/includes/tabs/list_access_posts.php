<?php
ihc_save_update_metas('list_access_posts');//save update metas
$data['metas'] = ihc_return_meta_arr('list_access_posts');
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
$post_types = ihc_get_all_post_types();
$levels = get_option('ihc_levels');
?>
<form action="" method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php _e('List Access Posts', 'ihc');?></h3>
		<div class="inside">
			
			<div class="iump-form-line">
				<h2><?php _e('Activate/Hold List Access Posts', 'ihc');?></h2>
				<p><?php _e('Display all Posts that user can see base on his Subscriptions.', 'ihc'); ?></p>
				<label class="iump_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = empty($data['metas']['ihc_list_access_posts_on']) ? '' : 'checked';?>
					<input type="checkbox" class="iump-switch" onClick="iump_check_and_h(this, '#ihc_list_access_posts_on');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="ihc_list_access_posts_on" value="<?php echo $data['metas']['ihc_list_access_posts_on'];?>" id="ihc_list_access_posts_on" /> 												
			</div>
			
			

					

			<div class="iump-form-line">
				<h4><?php _e('Specific Levels', 'ihc');?></h4>	
				<p><?php _e('The user needs to have the Level(s) assigned and Active to see the available Posts into his List.', 'ihc');?></p>	
				<?php $excluded = explode(',', $data['metas']['ihc_list_access_posts_order_exclude_levels']);?>
				<?php foreach ($levels as $lid=>$larr):?>
					<div style="margin: 0px 4px; margin-right:12px; display: inline-block; vertical-align: top; font-weight:bold;">
						<?php $checked = (!in_array($lid, $excluded)) ? 'checked' : '';?>
						<input type="checkbox" <?php echo $checked;?> onClick="ihc_add_to_hidden_when_uncheck(this, '<?php echo $lid;?>', '#ihc_list_access_posts_order_exclude_levels');" /> <span style="vertical-align: bottom;"><?php echo $larr['label'];?></span>
					</div>									
				<?php endforeach;?>									
				<input type="hidden" name="ihc_list_access_posts_order_exclude_levels" id="ihc_list_access_posts_order_exclude_levels" value="<?php echo $data['metas']['ihc_list_access_posts_order_exclude_levels'];?>" />				
			</div>	
			
			<div class="iump-form-line">
				<h4><?php _e('Post Type', 'ihc');?></h4>
				<p><?php _e('Select to show up only specific Post types or all of them.', 'ihc');?></p>	
				<?php $post_type_in = explode(',', $data['metas']['ihc_list_access_posts_order_post_type']);?>
				<?php foreach ($post_types as $value):?>
					<div style="margin: 0px 4px; margin-right:12px; display: inline-block; vertical-align: top; font-weight:bold;">
						<?php $checked = (in_array($value, $post_type_in)) ? 'checked' : '';?>
						<input type="checkbox" <?php echo $checked;?> onClick="ihc_make_inputh_string(this, '<?php echo $value;?>', '#ihc_list_access_posts_order_post_type');" /> <span style="vertical-align: bottom;"><?php echo ucfirst($value);?></span>
					</div>									
				<?php endforeach;?>									
				<input type="hidden" name="ihc_list_access_posts_order_post_type" id="ihc_list_access_posts_order_post_type" value="<?php echo $data['metas']['ihc_list_access_posts_order_post_type'];?>" />				
			</div>			
			
			<div class="iump-register-select-template" style="padding: 30px;margin-top: 30px;">
				<div style="margin-bottom:25px;">
				<h4><?php _e('Template', 'ihc');?></h4>	
				<select name="ihc_list_access_posts_template"  style="min-width:500px"><?php
					foreach (array('iump-list-posts-template-1'=>__('Template 1', 'ihc'), 'iump-list-posts-template-2'=>__('Template 2', 'ihc')) as $k=>$v){
						$selected = ($data['metas']['ihc_list_access_posts_template']==$k) ? 'selected' : '';
						?>
						<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
						<?php
					}	
				?></select>
			</div>	
			<div style="margin:25px 0;">
				<h4><?php _e('Showcase Title', 'ihc');?></h4>	
				<input type="text" name="ihc_list_access_posts_title" value="<?php echo $data['metas']['ihc_list_access_posts_title'];?>"  style="min-width:500px; line-height: 30px;" />
			</div>
			
			<div style="margin:25px 0;">
				<h4><?php _e('Item Details', 'ihc');?></h4>	
				<?php $item_details = explode(',', $data['metas']['ihc_list_access_posts_item_details']);?>
				<?php
					$details_arr = array(
											'post_title' => __('Title', 'ihc'),
											'post_excerpt' => __('Excerpt', 'ihc'),
											'feature_image' => __('Feature Image', 'ihc'),
											'post_date' => __('Post Date', 'ihc'),
											'post_author' => __('Post Author', 'ihc'),
					);
				?>
				<?php foreach ($details_arr as $value => $label):?>
					<div style="margin: 0px 4px; margin-right:12px; display: inline-block; vertical-align: top; text-transform:capitalize;">
						<?php $checked = (in_array($value, $item_details)) ? 'checked' : '';?>
						<input type="checkbox" <?php echo $checked;?> onClick="ihc_make_inputh_string(this, '<?php echo $value;?>', '#ihc_list_access_posts_item_details');" /> <span style="vertical-align: bottom;">  <?php echo '  '.$label;?></span>
					</div>									
				<?php endforeach;?>									
				<input type="hidden" name="ihc_list_access_posts_item_details" id="ihc_list_access_posts_item_details" value="<?php echo $data['metas']['ihc_list_access_posts_item_details'];?>" />		
			</div>
			</div>
			
			<div class="iump-form-line">
				<h4><?php _e('No. of Posts per page', 'ihc');?></h4>	
				<input type="number" name="ihc_list_access_posts_per_page_value" value="<?php echo $data['metas']['ihc_list_access_posts_per_page_value'];?>" min="1" />
			</div>
						
			<div class="iump-form-line">
				<h4><?php _e('Max. No. of Posts', 'ihc');?></h4>	
				<input type="number" name="ihc_list_access_posts_order_limit" value="<?php echo $data['metas']['ihc_list_access_posts_order_limit'];?>" min="1" />
			</div>


			<div class="iump-form-line">
				<h4><?php _e('Posts Order by', 'ihc');?></h4>	
				<select name="ihc_list_access_posts_order_by"><?php
					foreach (array('post_title'=>__('Post Title', 'ihc'), 'post_date'=>__('Post Date', 'ihc')) as $k=>$v){
						$selected = ($data['metas']['ihc_list_access_posts_order_by']==$k) ? 'selected' : '';
						?>
						<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
						<?php
					}	
				?></select>
			</div>

			<div class="iump-form-line">
				<h4><?php _e('Posts Order Type', 'ihc');?></h4>	
				<select name="ihc_list_access_posts_order_type"><?php
					foreach (array('asc'=>__('ASC', 'ihc'), 'desc'=>__('DESC', 'ihc')) as $k=>$v){
						$selected = ($data['metas']['ihc_list_access_posts_order_type']==$k) ? 'selected' : '';
						?>
						<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
						<?php
					}	
				?></select>
			</div>
			
			<div class="iump-form-line">
				<h2><?php _e('Custom CSS', 'ihc');?></h2>	
				<textarea name="ihc_list_access_posts_custom_css" style="width: 100%; height: 150px;"><?php echo $data['metas']['ihc_list_access_posts_custom_css'];?></textarea>
			</div>

			<h2><?php _e('Shortcode: ', 'ihc');?></h2>												
			<div class="ihc-user-list-shortcode-wrapp">
				<div class="content-shortcode" style="padding:15px; text-align:center;">
					<span class="the-shortcode" style="font-size: 16px;">[ihc-list-all-access-posts]</span>
				</div>						
			</div>
				
			<div class="ihc-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>	
							
		</div>
	</div>
</form>
