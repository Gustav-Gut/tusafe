<?php 
	$posible_values = array('all'=> __('All', 'ihc'), 'reg'=> __('Registered Users', 'ihc'), 'unreg'=> __('Unregistered Users', 'ihc') );
	$levels = get_option('ihc_levels');
	if($levels){
		foreach($levels as $id=>$level){
			$posible_values[$id] = $level['name'];
		}
	}
	$pages = ihc_get_all_pages();//getting pages
?>
<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['subtab'] =='post_types' || !isset($_REQUEST['subtab'])) ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=post_types';?>"><?php _e('All Posts', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['subtab'] =='cats') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=cats';?>"><?php _e('All Categories', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['subtab'] =='files') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=files';?>"><?php _e('Specific Files', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['subtab'] =='entire_url') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=entire_url';?>"><?php _e('Specific URL', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($_REQUEST['subtab'] =='keyword') ? 'ihc-subtab-selected' : '';?>" href="<?php echo $url.'&tab='.$tab.'&subtab=keyword';?>"><?php _e('All URL (based on Keywords)', 'ihc');?></a>
	<div class="ihc-clear"></div>
</div>	

<?php 
	echo ihc_inside_dashboard_error_license();
	echo ihc_check_default_pages_set();//set default pages message
	echo ihc_check_payment_gateways();
	echo ihc_is_curl_enable();
?>
<div class="iump-page-title">Ultimate Membership Pro - 
							<span class="second-text">
								<?php _e('Lock Rules', 'ihc');?>
							</span>
</div>
<form method="post" action="" id="block_url_form">
	<?php 
		$subtab = isset($_REQUEST['subtab']) ? $_REQUEST['subtab'] : 'post_types';
		switch ($subtab):
			case 'entire_url':
				ihc_save_block_urls();//save/update block url
				ihc_delete_block_urls();//delete block url
			?>
			<div class="ihc-stuffbox">
				<h3><?php _e('Add new Restriction', 'ihc');?></h3>
				<div class="inside">
				
						<div class="iump-form-line">
							<label class="iump-labels-special"><?php _e('Full URL:', 'ihc');?></label>
							<input type="text" value="" name="ihc_block_url_entire-url" style="width: 500px;"/>				
						</div>
						
						<div class="iump-form-line iump-special-line">
							
							<div class="iump-form-line">
								<?php
									$type_values = array(
															'block' => __('Block Only', 'ihc'), 
															'show' => __('Show Only', 'ihc'),
									);
								?>
								<label class="iump-labels-special"><?php _e('Restrict type:', 'ihc');?></label>
								<select name="block_or_show">
									<?php foreach ($type_values as $k=>$v):?>
										<option value="<?php echo $k;?>"><?php echo $v;?></option>		
									<?php endforeach;?>
								</select>		
							</div>								
							
							<div class="iump-form-line">
								<label class="iump-labels-special"><?php _e('Target Users:', 'ihc');?></label>
								<select id="ihc-change-target-user-set" onChange="ihc_writeTagValue(this, '#ihc_block_url_entire-target_users', '#ihc_tags_field1', 'ihc_select_tag_' );" style="width: auto;">
									<option value="-1" selected>...</option>
									<?php 
										foreach($posible_values as $k=>$v){
										?>
											<option value="<?php echo $k;?>"><?php echo $v;?></option>	
										<?php 
										}
									?>
								</select>	
								<input type="hidden" value="" name="ihc_block_url_entire-target_users" id="ihc_block_url_entire-target_users" />
								<div id="ihc_tags_field1"></div>		
							</div>
						</div>
						
						<div class="iump-form-line">
							<label class="iump-labels-special"><?php _e('Redirect to:', 'ihc');?></label> 
							<select name="ihc_block_url_entire-redirect">
								<option value="-1" selected >...</option>
								<?php 
									$pages = $pages + ihc_get_redirect_links_as_arr_for_select();
									if ($pages){
										foreach ($pages as $k=>$v){
											?>
												<option value="<?php echo $k;?>" ><?php echo $v;?></option>
											<?php 
										}						
									}
								?>
							</select>				
						</div>
				
					<input type="hidden" value="" name="delete_block_url" id="delete_block_url" />
					
					<div style="margin-top: 15px;">
						<input type="submit" value="<?php _e('Add New', 'ihc');?>" name="ihc_save_block_url" class="button button-primary button-large" />
					</div>				
				</div>	
			</div>	
			<?php 
				$data = get_option('ihc_block_url_entire');
				if ($data && count($data)){
					?>
					<div class="ihc-dashboard-form-wrap">
					<table class="wp-list-table widefat fixed tags" style="margin-bottom: 20px;">
						<thead>
						<tr>
							<th class="manage-column"><?php _e('Target URL', 'ihc');?></th>
							<th class="manage-column"><?php _e('Type', 'ihc');?></th>
							<th class="manage-column"><?php _e('Target Users', 'ihc');?></th>
							<th class="manage-column"><?php _e('Redirect To', 'ihc');?></th>
							<th class="manage-column" style="width:80px;  text-align: center;"><?php _e('Delete', 'ihc');?></th>
						</tr>
						</thead>
					<?php 
						$i = 1;
						foreach ($data as $key=>$arr){
						?>
						<tr class="<?php if ($i%2==0) echo 'alternate';?>">
							<td><?php echo $arr['url'];?></td>
							<td><?php 
								$print_type = isset($arr['block_or_show']) ? $arr['block_or_show'] : 'block';
								echo ucfirst($print_type);	
							?></td>							
							<td>							
								<?php
									if ($arr['target_users']){
										$levels = explode(',', $arr['target_users']);
										if ($levels && count($levels)){
											$extra_class = ($print_type=='block') ? 'ihc-expired-level' : '';
											foreach ($levels as $val){
												$print_type_user = '';
												if ($val!='reg' && $val!='unreg' && $val!='all'){
													$temp_data = ihc_get_level_by_id($val);
													if (!empty($temp_data['name'])){
														$print_type_user = $temp_data['name'];	
													}										
												} else {
													$print_type_user = $val;	
												}
												if (empty($print_type_user)){
													$print_type_user = __('Deleted Level', 'ihc');
												}
												?>
												<div class="level-type-list <?php echo $extra_class;?>"><?php echo $print_type_user;?></div>
												<?php
											}									
										}								
									}
								?>
							</td>
							<td style="color: #21759b; font-weight:bold;">
								<?php 
									if ($arr['redirect']!=-1){
										$redirect_link = ihc_get_redirect_link_by_label($arr['redirect']);
										if ($redirect_link){
											echo $redirect_link;
										} else {
											echo get_the_title($arr['redirect']);
										}										
									} else {									
										echo '-';						
									}
								?>
							</td>
							<td align="center">
								<i class="fa-ihc ihc-icon-remove-e" style="cursor:pointer;" onClick="jQuery('#delete_block_url').val('<?php echo $key;?>');jQuery('#block_url_form').submit();"></i>
							</td>
						</tr>
						<?php 
						}
						?>
					</table>
					</div>
					<?php 
				}
			break;
		case 'keyword':
				ihc_save_block_urls();//save/update block url
				ihc_delete_block_urls();//delete block url			
			?>
				<div class="ihc-stuffbox">
					<h3><?php _e('Add new Restriction', 'ihc');?></h3>
					<div class="inside">
						
							<div class="iump-form-line">
								<label class="iump-labels-special"><?php _e('Keyword:', 'ihc');?></label>
								<input type="text" value="" name="ihc_block_url_word-url" />				
							</div>
			
							<div class="iump-form-line iump-special-line">
								<div class="iump-form-line">
									<?php
										$type_values = array(
																'block' => __('Block Only', 'ihc'), 
																'show' => __('Show Only', 'ihc'),
										);
									?>
									<label class="iump-labels-special"><?php _e('Restrict type:', 'ihc');?></label>
									<select name="block_or_show">
										<?php foreach ($type_values as $k=>$v):?>
											<option value="<?php echo $k;?>"><?php echo $v;?></option>		
										<?php endforeach;?>
									</select>		
								</div>									
								
								<div class="iump-form-line">
									<label class="iump-labels-special"><?php _e('Target Users:', 'ihc');?></label>
									<select id="ihc-change-target-user-set-regex" onChange="ihc_writeTagValue(this, '#ihc_block_url_word-target_users', '#ihc_tags_field2', 'ihc_select_tag_regex_' );" style="width: auto;">
										<option value="-1" selected>...</option>
										<?php 
											foreach($posible_values as $k=>$v){
											?>
												<option value="<?php echo $k;?>"><?php echo $v;?></option>	
											<?php 
											}
										?>
									</select>	
									<input type="hidden" value="" name="ihc_block_url_word-target_users" id="ihc_block_url_word-target_users" />
									<div id="ihc_tags_field2"></div>	
								</div>		
							</div>
							
							<div class="iump-form-line">
								<label class="iump-labels-special"><?php _e('Redirect to:', 'ihc');?></label> 
								<select name="ihc_block_url_word-redirect">
									<option value="-1" selected >...</option>
									<?php 
										$pages = $pages + ihc_get_redirect_links_as_arr_for_select();									
										if ($pages){
											foreach($pages as $k=>$v){
												?>
													<option value="<?php echo $k;?>"><?php echo $v;?></option>
												<?php 
											}						
										}
									?>
								</select>	
							</div>	
							<input type="hidden" value="" name="delete_block_regex" id="delete_block_regex" />		
						<div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Add New', 'ihc');?>" name="ihc_save_block_url" class="button button-primary button-large" />
						</div>		
					</div>
				</div>		
		<?php 				
				$data = get_option('ihc_block_url_word');
				if ($data && count($data)){
					?>
						<div class="ihc-dashboard-form-wrap">
						<table class="wp-list-table widefat fixed tags" style="margin-top: 20px" >
							<thead>
							<tr>
								<th class="manage-column"><?php _e('Target URL That Contains', 'ihc');?></th>
								<th class="manage-column"><?php _e('Type', 'ihc');?></th>
								<th class="manage-column"><?php _e('Target Users', 'ihc');?></th>
								<th class="manage-column"><?php _e('Redirect To', 'ihc');?></th>
								<th class="manage-column" style="width:80px;  text-align: center;"><?php _e('Delete', 'ihc');?></th>
							</tr>
							</thead>
						<?php 
							$i = 1;
							foreach ($data as $key=>$arr){
							?>
								<tr class="<?php if ($i%2==0) echo 'alternate';?>">								
									<td><?php echo $arr['url'];?></td>
									<td><?php 
										$print_type = isset($arr['block_or_show']) ? $arr['block_or_show'] : 'block';
										echo ucfirst($print_type);	
									?></td>										
									<td>
										<?php
											if ($arr['target_users']){
												$levels = explode(',', $arr['target_users']);
												if ($levels && count($levels)){
													$extra_class = ($print_type=='block') ? 'ihc-expired-level' : '';
													foreach ($levels as $val){
														$print_type_user = '';
														if ($val!='reg' && $val!='unreg' && $val!='all'){
															$temp_data = ihc_get_level_by_id($val);
															if (!empty($temp_data['name'])){
																$print_type_user = $temp_data['name'];
															}																										
														} else {
															$print_type_user = $val;
														}
														if (empty($print_type_user)){
															$print_type_user = __('Deleted Level', 'ihc');
														}														
														?>
														<div class="level-type-list <?php echo $extra_class;?>"><?php echo $print_type_user;?></div>
														<?php
													}									
												}								
											}
										?>
									</td>
									<td style="color: #21759b; font-weight:bold;">
										<?php 
											if ($arr['redirect']!=-1){
												$redirect_link = ihc_get_redirect_link_by_label($arr['redirect']);
												if ($redirect_link){
													echo $redirect_link;
												} else {
													echo get_the_title($arr['redirect']);
												}
											} else {
												echo '-';
											}
										?>
									</td>
									<td align="center">
										<i class="fa-ihc ihc-icon-remove-e" style="cursor:pointer;" onClick="jQuery('#delete_block_regex').val('<?php echo $key;?>');jQuery('#block_url_form').submit();"></i>
									</td>
								</tr>
							<?php 
							}
							?>
						</table>
						</div>
			<?php 
				}
			break;
		case 'post_types':
			
			if (isset($_POST['delete_block']) && $_POST['delete_block']!=''){
				/// ======================== DELETE
				ihc_delete_block_group('ihc_block_posts_by_type', $_POST['delete_block']);	
			}
			if (!empty($_POST['ihc_save'])){
				/// ========================= ADD NEW
				unset($_POST['ihc_save']);
				ihc_save_block_group('ihc_block_posts_by_type', $_POST, $_POST['post_type']);
			}
			?>
			<form method="post" action="">
				<div class="ihc-stuffbox">
					<h3><?php _e('Block All Posts By Type', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<label class="iump-labels-special"><?php _e('Post Type:', 'ihc');?></label> 
							<select name="post_type">
							<?php
								global $wp_post_types;
								$post_types = ihc_get_all_post_types();
								foreach ($post_types as $key):
									if (isset($wp_post_types[$key])){
										$obj = $wp_post_types[$key];
										$label =  $obj->labels->name;										
									} else {
										$label = ucfirst($key);
									}
							?>
								<option value="<?php echo $key;?>"><?php echo $label . ' (' . $key . ')';?></option>
							<?php
								endforeach;
							?>								
							</select>
						</div>
						
						<div class="iump-form-line">
							<label><?php _e('Except: ', 'ihc');?></label>
							<input type="text" name="except" value="" /> 
							<p><i><?php _e('Write post IDs separated by comma. ex.: 30, 55, 102');?></i></p>
						</div>						
						
						<div class="iump-form-line iump-special-line">
							<div class="iump-form-line">
								<?php
									$type_values = array(
															'block' => __('Block Only', 'ihc'), 
															'show' => __('Show Only', 'ihc'),
									);
								?>
								<label class="iump-labels-special"><?php _e('Restrict type:', 'ihc');?></label>
								<select name="block_or_show">
									<?php foreach ($type_values as $k=>$v):?>
										<option value="<?php echo $k;?>"><?php echo $v;?></option>		
									<?php endforeach;?>
								</select>									
							</div>

							<div class="iump-form-line">
								<label class="iump-labels-special"><?php _e('Target Users:', 'ihc');?></label>
								<select id="ihc-change-target-user-set-regex" onChange="ihc_writeTagValue(this, '#target_users', '#ihc_tags_field2', 'ihc_select_tag_regex_' );" style="width: auto;">
									<option value="-1" selected>...</option>
									<?php 
										foreach($posible_values as $k=>$v){
										?>
											<option value="<?php echo $k;?>"><?php echo $v;?></option>	
										<?php 
										}
									?>
								</select>	
								<input type="hidden" value="" name="target_users" id="target_users" />
								<div id="ihc_tags_field2"></div>									
							</div>
		
						</div>					

						
						<div class="iump-form-line">
							<label class="iump-labels-special"><?php _e('Redirect to:', 'ihc');?></label> 
							<select name="redirect">
								<option value="-1" selected >...</option>
								<?php 
									$pages = $pages + ihc_get_redirect_links_as_arr_for_select();									
									if ($pages){
										foreach($pages as $k=>$v){
											?>
												<option value="<?php echo $k;?>"><?php echo $v;?></option>
										<?php 
										}						
									}
								?>
							</select>	
						</div>	
														
						<div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Add New', 'ihc');?>" name="ihc_save" class="button button-primary button-large">
						</div>							
					</div>
				</div>	
				
			</form>					
			<?php 				
				$data = get_option('ihc_block_posts_by_type');				
				if ($data && count($data)){
					?>
						<form method="post" action="" id="delete_block_form">
							<input type="hidden" value="" name="delete_block" id="delete_block" />	
						</form>
						<div class="ihc-dashboard-form-wrap">
						<table class="wp-list-table widefat fixed tags" style="margin-top: 20px" >
							<thead>
							<tr>
								<th class="manage-column"><?php _e('Target Post Type', 'ihc');?></th>
								<th class="manage-column"><?php _e('Type', 'ihc');?></th>
								<th class="manage-column"><?php _e('Target Users', 'ihc');?></th>
								<th class="manage-column"><?php _e('Except', 'ihc');?></th>
								<th class="manage-column"><?php _e('Redirect To', 'ihc');?></th>
								<th class="manage-column" style="width:80px;  text-align: center;"><?php _e('Delete', 'ihc');?></th>
							</tr>
							</thead>
						<?php 
							$i = 1;
							foreach ($data as $key=>$arr){
							?>
								<tr class="<?php if ($i%2==0) echo 'alternate';?>">
									<td><?php echo $arr['post_type'];?></td>
									<td><?php 
										$print_type = isset($arr['block_or_show']) ? $arr['block_or_show'] : 'block';
										echo ucfirst($print_type);	
									?></td>									
									<td>
										<?php
											if ($arr['target_users']){
												$levels = explode(',', $arr['target_users']);
												if ($levels && count($levels)){
													$extra_class = ($print_type=='block') ? 'ihc-expired-level' : '';
													foreach ($levels as $val){
														$print_type_user = '';
														if ($val!='reg' && $val!='unreg' && $val!='all'){
															$temp_data = ihc_get_level_by_id($val);
															if (!empty($temp_data['name'])){
																$print_type_user = $temp_data['name'];
															}																										
														} else {
															$print_type_user = $val;	
														}
														if (empty($print_type_user)){
															$print_type_user = __('Deleted Level', 'ihc');
														}
														?>
														<div class="level-type-list <?php echo $extra_class;?>"><?php echo $print_type_user;?></div>
														<?php																												
													}							
												}								
											}
										?>
									</td>
									<td><?php 
										if (empty($arr['except'])){
											echo '-';
										} else {
											echo $arr['except'];	
										}
									?></td>
									<td style="color: #21759b; font-weight:bold;">
										<?php 
											if ($arr['redirect']!=-1){
												$redirect_link = ihc_get_redirect_link_by_label($arr['redirect']);
												if ($redirect_link){
													echo $redirect_link;
												} else {
													echo get_the_title($arr['redirect']);
												}
											} else {
												echo '-';
											}
										?>
									</td>
									<td align="center">
										<i class="fa-ihc ihc-icon-remove-e" style="cursor:pointer;" onClick="jQuery('#delete_block').val('<?php echo $key;?>');jQuery('#delete_block_form').submit();"></i>
									</td>
								</tr>
							<?php 
							}
							?>
						</table>
						</div>						
		<?php }
		break;
	case 'cats':
			if (isset($_POST['delete_block']) && $_POST['delete_block']!=''){
				/// ======================== DELETE
				ihc_delete_block_group('ihc_block_cats_by_name', $_POST['delete_block']);	
			}
			if (!empty($_POST['ihc_save'])){
				/// ========================= ADD NEW
				unset($_POST['ihc_save']);
				ihc_save_block_group('ihc_block_cats_by_name', $_POST, $_POST['cat_id']);
			}
			?>
			<form method="post" action="">
				<div class="ihc-stuffbox">
					<h3><?php _e('Block All Posts By Category Name', 'ihc');?></h3>
					<div class="inside">							
						<div class="iump-form-line">
							<label class="iump-labels-special"><?php _e('Category:', 'ihc');?></label> 
							<select name="cat_id">
							<?php
								$terms = ihc_get_all_terms_with_names();
								foreach ($terms as $key=>$label):
							?>
								<option value="<?php echo $key;?>"><?php echo $label;?></option>
							<?php
								endforeach;
							?>								
							</select>
						</div>
						
						<div class="iump-form-line">
							<label><?php _e('Except: ', 'ihc');?></label>
							<input type="text" name="except" value="" /> 
							<p><i><?php _e('Write post IDs separated by comma. ex.: 30, 55, 102');?></i></p>
						</div>
												
						<div class="iump-form-line iump-special-line">
							<div class="iump-form-line">
								<?php
									$type_values = array(
															'block' => __('Block Only', 'ihc'), 
															'show' => __('Show Only', 'ihc'),
									);
								?>
								<label class="iump-labels-special"><?php _e('Restrict type:', 'ihc');?></label>
								<select name="block_or_show">
									<?php foreach ($type_values as $k=>$v):?>
										<option value="<?php echo $k;?>"><?php echo $v;?></option>		
									<?php endforeach;?>
								</select>		
							</div>									
							<div class="iump-form-line">
								<label class="iump-labels-special"><?php _e('Target Users:', 'ihc');?></label>
								<select id="ihc-change-target-user-set-regex" onChange="ihc_writeTagValue(this, '#target_users', '#ihc_tags_field2', 'ihc_select_tag_regex_' );" style="width: auto;">
									<option value="-1" selected>...</option>
									<?php 
										foreach($posible_values as $k=>$v){
										?>
											<option value="<?php echo $k;?>"><?php echo $v;?></option>	
										<?php 
										}
									?>
								</select>	
								<input type="hidden" value="" name="target_users" id="target_users" />
								<div id="ihc_tags_field2"></div>		
							</div>	
						</div>			
						
						<div class="iump-form-line">
							<label class="iump-labels-special"><?php _e('Redirect to:', 'ihc');?></label> 
							<select name="redirect">
								<option value="-1" selected >...</option>
								<?php 
									$pages = $pages + ihc_get_redirect_links_as_arr_for_select();									
									if ($pages){
										foreach($pages as $k=>$v){
											?>
												<option value="<?php echo $k;?>"><?php echo $v;?></option>
										<?php 
										}						
									}
								?>
							</select>	
						</div>	
														
						<div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Add New', 'ihc');?>" name="ihc_save" class="button button-primary button-large">
						</div>							
					</div>
				</div>	
				
			</form>					
			<?php 				
				$data = get_option('ihc_block_cats_by_name');				
				if ($data && count($data)){
					?>
						<form method="post" action="" id="delete_block_form">
							<input type="hidden" value="" name="delete_block" id="delete_block" />	
						</form>
						<div class="ihc-dashboard-form-wrap">
						<table class="wp-list-table widefat fixed tags" style="margin-top: 20px" >
							<thead>
							<tr>
								<th class="manage-column"><?php _e('Target Category Name', 'ihc');?></th>
								<th class="manage-column"><?php _e('Type', 'ihc');?></th>
								<th class="manage-column"><?php _e('Target Users', 'ihc');?></th>
								<th class="manage-column"><?php _e('Except', 'ihc');?></th>
								<th class="manage-column"><?php _e('Redirect To', 'ihc');?></th>
								<th class="manage-column" style="width:80px;  text-align: center;"><?php _e('Delete', 'ihc');?></th>
							</tr>
							</thead>
						<?php 
							$i = 1;
							foreach ($data as $key=>$arr){
							?>
								<tr class="<?php if ($i%2==0) echo 'alternate';?>">								
									<td><?php 
										$k = $arr['cat_id'];
										if (!empty($terms[$k])){
											echo $terms[$k];
										}
									?></td>
									<td><?php 
										$print_type = isset($arr['block_or_show']) ? $arr['block_or_show'] : 'block';
										echo ucfirst($print_type);	
									?></td>										
									<td>
										<?php
											if ($arr['target_users']){
												$levels = explode(',', $arr['target_users']);
												if ($levels && count($levels)){
													$extra_class = ($print_type=='block') ? 'ihc-expired-level' : '';
													foreach ($levels as $val){
														$print_type_user = '';
														if ($val!='reg' && $val!='unreg' && $val!='all'){
															$temp_data = ihc_get_level_by_id($val);
															if (!empty($temp_data['name'])){
																$print_type_user = $temp_data['name'];
															}																										
														} else {
															$print_type_user = $val;
														}
														if (empty($print_type_user)){
															$print_type_user = __('Deleted Level', 'ihc');
														}														
														?>
														<div class="level-type-list <?php echo $extra_class;?>"><?php echo $print_type_user;?></div>
														<?php
													}									
												}								
											}
										?>
									</td>
									<td><?php 
										if (empty($arr['except'])){
											echo '-';
										} else {
											echo $arr['except'];	
										}
									?></td>
									<td style="color: #21759b; font-weight:bold;">
										<?php 
											if ($arr['redirect']!=-1){
												$redirect_link = ihc_get_redirect_link_by_label($arr['redirect']);
												if ($redirect_link){
													echo $redirect_link;
												} else {
													echo get_the_title($arr['redirect']);
												}
											} else {
												echo '-';
											}
										?>
									</td>
									<td align="center">
										<i class="fa-ihc ihc-icon-remove-e" style="cursor:pointer;" onClick="jQuery('#delete_block').val('<?php echo $key;?>');jQuery('#delete_block_form').submit();"></i>
									</td>
								</tr>
							<?php 
							}
							?>
						</table>
						</div>						
		<?php }		
		break;
	case 'files':
			if (isset($_POST['delete_block']) && $_POST['delete_block']!=''){
				/// ======================== DELETE
				ihc_delete_block_group('ihc_block_files_by_url', $_POST['delete_block']);	
			}
			if (!empty($_POST['ihc_save'])){
				/// ========================= ADD NEW
				unset($_POST['ihc_save']);
				ihc_save_block_group('ihc_block_files_by_url', $_POST, $_POST['file_url']);
				ihc_do_write_into_htaccess();
			}
			?>
			<form method="post" action="">
				<div class="ihc-stuffbox">
					<h3><?php _e('Block Files By URL', 'ihc');?></h3>
					<div class="inside">
								
						<div class="iump-form-line">
							<label class="iump-labels-special"><?php _e('File URL Path:', 'ihc');?></label> 
							<input type="text" name="file_url" value="" style="width: 75%;"/>
						</div>
						
						<div class="iump-form-line iump-special-line">
							
							<div class="iump-form-line">
								<?php
									$type_values = array(
															'block' => __('Block Only', 'ihc'), 
															'show' => __('Show Only', 'ihc'),
									);
								?>
								<label class="iump-labels-special"><?php _e('Restrict type:', 'ihc');?></label>
								<select name="block_or_show">
									<?php foreach ($type_values as $k=>$v):?>
										<option value="<?php echo $k;?>"><?php echo $v;?></option>		
									<?php endforeach;?>
								</select>		
							</div>								
							
							<div class="iump-form-line">
								<label class="iump-labels-special"><?php _e('Target Users:', 'ihc');?></label>
								<select id="ihc-change-target-user-set-regex" onChange="ihc_writeTagValue(this, '#target_users', '#ihc_tags_field2', 'ihc_select_tag_regex_' );" style="width: auto;">
									<option value="-1" selected>...</option>
									<?php 
										foreach($posible_values as $k=>$v){
										?>
											<option value="<?php echo $k;?>"><?php echo $v;?></option>	
										<?php 
										}
									?>
								</select>	
								<input type="hidden" value="" name="target_users" id="target_users" />
								<div id="ihc_tags_field2"></div>	
							</div>
									
						</div>					
						
						<div class="iump-form-line">
							<label class="iump-labels-special"><?php _e('Redirect to:', 'ihc');?></label> 
							<select name="redirect">
								<option value="-1" selected >...</option>
								<?php 
									$pages = $pages + ihc_get_redirect_links_as_arr_for_select();									
									if ($pages){
										foreach($pages as $k=>$v){
											?>
												<option value="<?php echo $k;?>"><?php echo $v;?></option>
										<?php 
										}						
									}
								?>
							</select>	
						</div>	
														
						<div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Add New', 'ihc');?>" name="ihc_save" class="button button-primary button-large">
						</div>							
					</div>
				</div>	
				
			</form>					
			<?php 				
				$data = get_option('ihc_block_files_by_url');				
				if ($data && count($data)){
					?>
						<form method="post" action="" id="delete_block_form">
							<input type="hidden" value="" name="delete_block" id="delete_block" />	
						</form>
						<div class="ihc-dashboard-form-wrap">
						<table class="wp-list-table widefat fixed tags" style="margin-top: 20px" >
							<thead>
							<tr>
								<th class="manage-column"><?php _e('Target File URL', 'ihc');?></th>
								<th class="manage-column"><?php _e('Type', 'ihc');?></th>
								<th class="manage-column"><?php _e('Target Users', 'ihc');?></th>
								<th class="manage-column"><?php _e('Redirect To', 'ihc');?></th>
								<th class="manage-column" style="width:80px;  text-align: center;"><?php _e('Delete', 'ihc');?></th>
							</tr>
							</thead>
						<?php 
							$i = 1;
							foreach ($data as $key=>$arr){
							?>
								<tr class="<?php if ($i%2==0) echo 'alternate';?>">
									<td><?php echo $arr['file_url'];?></td>
									<td><?php 
										$print_type = isset($arr['block_or_show']) ? $arr['block_or_show'] : 'block';
										echo ucfirst($print_type);	
									?></td>									
									<td>
										<?php
											if ($arr['target_users']){
												$levels = explode(',', $arr['target_users']);
												if ($levels && count($levels)){
													$extra_class = ($print_type=='block') ? 'ihc-expired-level' : '';
													foreach ($levels as $val){
														$print_type_user = '';
														if ($val!='reg' && $val!='unreg' && $val!='all'){
															$temp_data = ihc_get_level_by_id($val);
															if (!empty($temp_data['name'])){
																$print_type_user = $temp_data['name'];
															}																										
														} else {
															$print_type_user = $val;	
														}
														if (empty($print_type_user)){
															$print_type_user = __('Deleted Level', 'ihc');
														}														
														?>
														<div class="level-type-list <?php echo $extra_class;?>"><?php echo $print_type_user;?></div>
														<?php
													}									
												}								
											}
										?>
									</td>
									<td style="color: #21759b; font-weight:bold;">
										<?php 
											if ($arr['redirect']!=-1){
												$redirect_link = ihc_get_redirect_link_by_label($arr['redirect']);
												if ($redirect_link){
													echo $redirect_link;
												} else {
													echo get_the_title($arr['redirect']);
												}
											} else {
												echo '-';
											}
										?>
									</td>
									<td align="center">
										<i class="fa-ihc ihc-icon-remove-e" style="cursor:pointer;" onClick="jQuery('#delete_block').val('<?php echo $key;?>');jQuery('#delete_block_form').submit();"></i>
									</td>
								</tr>
							<?php 
							}
							?>
						</table>
						</div>						
			<?php }			
		break;
endswitch;