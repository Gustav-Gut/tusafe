<?php 
echo ihc_inside_dashboard_error_license();
$is_uap_active = ihc_is_uap_active();
if ($is_uap_active):
?>
<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item" href="<?php echo $url.'&tab='.$tab.'&subtab=list';?>"><?php _e('Affiliates', 'ihc');?></a>
	<a class="ihc-subtab-menu-item" href="<?php echo $url.'&tab='.$tab.'&subtab=options';?>"><?php _e('Account Page', 'ihc');?></a>
	<div class="ihc-clear"></div>
</div>
<?php endif;?>

<div class="iump-wrapper">
		<div class="ihc-dashboard-title">
			Ultimate Membership Pro - 
			<span class="second-text">
				<?php _e('Affiliates', 'ihc');?>
			</span>
		</div>
		

		<?php if ($is_uap_active):?>
				<?php
				if (empty($_GET['subtab']) || $_GET['subtab']=='list'):
					////////////////////////////////////// LISTING ///////////////////////////////////////
				$limit = (isset($_REQUEST['ihc_limit'])) ? $_REQUEST['ihc_limit'] : 25;
				$start = 0;
				if(isset($_REQUEST['ihcdu_page'])){
					$pg = $_REQUEST['ihcdu_page'] - 1;
					$start = (int)$pg * $limit;
				}
				
				$filter_role = '';
				if(isset($_REQUEST['filter_role']))
					$filter_role = $_REQUEST['filter_role'];
				
				$orderby = 'registered';
				if(isset($_REQUEST['orderby_user']))
					$orderby = $_REQUEST['orderby_user'];
				
				$ordertype = 'DESC';
				if(isset($_REQUEST['ordertype_user']))
					$ordertype = $_REQUEST['ordertype_user'];
					
				$search_term = '';
				if(isset($_REQUEST['search_user']))
					$search_term = $_REQUEST['search_user'];
					
					global $wpdb;
					$current_time = time();
					if ($search_term != ''){
						function ihc_pre_user_query($user_query){
							$user_query->query_fields = 'DISTINCT ' . $user_query->query_fields;
						}
						add_action( 'pre_user_query', 'ihc_pre_user_query');
						global $wp_query;
						$users_obj = new WP_User_Query(array(
														    'role' => $filter_role,
															'meta_query' => array(
																'relation' => 'AND',
														        array(
														            'key' => $wpdb->get_blog_prefix() . 'capabilities',
														            'value' => 'administrator',
														            'compare' => 'NOT LIKE'
														        ),
																array(
																	'relation' => 'OR',
																	array(
																		'key'     => 'first_name',
																		'value'   => $search_term,
																		'compare' => 'LIKE'
																	),
																	array(
																		'key'     => 'last_name',
																		'value'   => $search_term,
																		'compare' => 'LIKE'
																	),
																	array(
																		'key' => 'nickname',
																		'value' => $search_term ,
																		'compare' => 'LIKE'
																	)
																)
														    ),
															'offset' => $start,
															'number' => $limit,
															'orderby' => $orderby, 
															'order' => $ordertype,
														));
						
						//////////////////PAGINATION
						$all_users = new WP_User_Query(array(
														    'role' => $filter_role,
															'meta_query' => array(
																'relation' => 'AND',
														        array(
														            'key' => $wpdb->get_blog_prefix() . 'capabilities',
														            'value' => 'administrator',
														            'compare' => 'NOT LIKE'
														        ),
																array(
																		'relation' => 'OR',
																		array(
																			'key'     => 'first_name',
																			'value'   => $search_term,
																			'compare' => 'LIKE'
																		),
																		array(
																			'key'     => 'last_name',
																			'value'   => $search_term,
																			'compare' => 'LIKE'
																		),
																		array(
																			'key' => 'nickname',
																			'value' => $search_term ,
																			'compare' => 'LIKE'
																		)
																	)
														    ),
															'count_total' => TRUE,
														));	
						
					} else {
						$users_obj = new WP_User_Query(array(
														    'role' => $filter_role,
															'meta_query' => array(
																array(
														            'key' => $wpdb->get_blog_prefix() . 'capabilities',
														            'value' => 'administrator',
														            'compare' => 'NOT LIKE'
														        )
																
														    ),
															'offset' => $start,
															'number' => $limit,
															'orderby' => $orderby, 
															'order' => $ordertype,
														));
						
						
						//////////////////PAGINATION
						$all_users = new WP_User_Query(array(
														    'role' => $filter_role,
														    'meta_query' => array(
																array(
														            'key' => $wpdb->get_blog_prefix() . 'capabilities',
														            'value' => 'administrator',
														            'compare' => 'NOT LIKE'
														        )
														    ),
															'orderby' => $orderby,
															'order' => $ordertype,
															'count_total' => TRUE,
						));
					}
					$users = $users_obj->results;
					$all_users = $all_users->results;	
					//SEARCH FILTER BY USER LEVELS
					if ($start==0) $current_page = 1;
					else $current_page = $_REQUEST['ihcdu_page'];
					
					require_once IHC_PATH . 'classes/Ihc_Pagination.class.php';
					$url  = admin_url('admin.php?page=ihc_manage&tab=affiliates&ihc_limit=' . $limit);
					$pagination_object = new Ihc_Pagination(array(
																'base_url' => $url,
																'param_name' => 'ihcdu_page',
																'total_items' => count($all_users),
																'items_per_page' => $limit,
																'current_page' => $current_page,
					));
					$pagination = $pagination_object->output();

					global $indeed_db;
					if (empty($indeed_db) && defined('UAP_PATH')){
						include UAP_PATH . 'classes/Uap_Db.class.php';
						$indeed_db = new Uap_Db;
					}
					
					$hidded = 'style="display:none;"';
					if (isset($_REQUEST['search_user']) || isset($_REQUEST['filter_role']) || isset($_REQUEST['ordertype_level']) || isset($_REQUEST['orderby_user']) || isset($_REQUEST['ordertype_user']) ) $hidded ='';
					
					?>
					<div class="ihc-special-buttons-users">
						<div class="ihc-special-button" onclick="ihc_show_hide('.ihc-filters-wrapper');"><i class="fa-ihc fa-export-csv"></i><?php _e('Add Filters', 'uap');?></div>
						<div class="ihc-clear"></div>
					</div>					
					<div class="ihc-filters-wrapper" <?php echo $hidded;?>>
						<form method="post" action="">
							<div class="row-fluid">
								<div class="span4">
									<div class="iump-form-line iump-no-border">
										<input name="search_user" type="text" value="<?php echo (isset($_REQUEST['search_user']) ? $_REQUEST['search_user'] : '') ?>" placeholder="<?php _e('Search by Name or Username', 'ihc');?>..."/>
									</div>
								</div>
								<div class="span2" style="padding:30px 10px 0 0;">
									<input type="submit" value="Search" name="search" class="button button-primary button-large">
								</div>
							</div>
						</form>
					</div>						
							<div style="margin: 10px 0px;">
								<div style="display: inline-block;float: left;" >
								</div>
								
								<div style="display: inline-block;float: right;margin-right:10px;">
									<strong><?php _e('Number of Users to Display:', 'ihc');?></strong> 
									<select name="ihc_limit" onChange="window.location = '<?php echo admin_url('admin.php');?>?page=ihc_manage&tab=affiliates&ihc_limit='+this.value;">
										<?php 
											foreach (array(5,25,50,100) as $v){
												?>
													<option value="<?php echo $v;?>" <?php if($limit==$v) echo 'selected';?> ><?php echo $v;?></option>
												<?php 
											}
										?>
									</select>
								</div>
								<?php echo $pagination;?>
								<div class="clear"></div>							
							</div>
												
						<table class="wp-list-table widefat fixed tags">
							<thead>
								<tr>						
									  <th class="manage-column">
											<?php _e('Username', 'ihc');?>
									  </th>								
									  <th class="manage-column">
											<?php _e('Name', 'ihc');?>
									  </th>
									  <th class="manage-column">
											<?php _e('E-mail', 'ihc');?>
									  </th>
									  <th><?php _e('Affiliate', 'uap');?></th>
									  <th><?php _e('Join Date', 'ihc');?></th>
								</tr>
							</thead>
							<tfoot>
								<tr>							
									  <th class="manage-column">
											<?php _e('Username', 'ihc');?>
									  </th>								
									  <th class="manage-column">
											<?php _e('Name', 'ihc');?>
									  </th>
									  <th class="manage-column">
											<?php _e('E-mail', 'ihc');?>
									  </th>
									  <th><?php _e('Affiliate', 'ihc');?></th>
									  <th><?php _e('Join Date', 'ihc');?></th>
								</tr>
							</tfoot>	
							  <?php 
							  		$i = 1;
							  		foreach ($users as $user){								  			
							  			?>
			    						   		<tr id="<?php echo "ihc_user_id_" . $user->data->ID;?>" class="<?php if($i%2==0) echo 'alternate';?>" onMouseOver="ihc_dh_selector('#user_tr_<?php echo $user->data->ID;?>', 1);" onMouseOut="ihc_dh_selector('#user_tr_<?php echo $user->data->ID;?>', 0);">
			    						   			<td>
														<?php echo $user->data->user_login;?>
			    						   			</td>
			    						   			<td style="color: #21759b; font-weight:bold; width:120px;font-family: 'Oswald', arial, sans-serif !important;font-size: 14px;font-weight: 400;">
			    						   				<?php 
			    						   					$first_name = get_user_meta($user->data->ID, 'first_name', true);
			    						   					$last_name = get_user_meta($user->data->ID, 'last_name', true);
			    						   					if ($first_name || $last_name){
			    						   						echo $first_name .' '.$last_name;
			    						   					} else {
			    						   						echo $user->data->user_nicename;
			    						   					}
			    						   				?>
			    						   			</td>
			    						   			<td>
			    						   				<?php echo $user->user_email;?>
			    						   			</td>
													<td>
														<div>
															<label class="iump_label_shiwtch-uap-affiliate">
																<?php
																	$uid = $user->data->ID; 
																	$checked = (!empty($indeed_db) && $indeed_db->is_user_affiliate_by_uid($uid)) ? 'checked' : '';
																?>
																<input type="checkbox" class="iump-switch" id="uap_checkbox_<?php echo $uid;?>" onClick="ihc_change_uap_affiliate(<?php echo $uid;?>);" <?php echo $checked;?>/>
																<div class="switch" style="display:inline-block;"></div>
															</label>			
														</div>																													
													</td>
			    						   			<td style="color: #396;">
			    						   				<?php 
			    						   					echo $user->user_registered;
			    						   				?>
			    						   			</td>
			    						   		</tr>
							  			<?php
							  			$i++; 
							  		}
							  ?>							
						</table>	
				<?php 
					else : 
						///////////////////////// OPTIONS 
						if (!empty($_POST['ihc_save'])){
							ihc_save_update_metas('affiliate_options');
						}
						$meta_arr = ihc_return_meta_arr('affiliate_options');
				?>
					<form method="post" action="">
						<div class="ihc-stuffbox">
							<h3><?php _e('Account Page - Affiliate Options', 'ihc');?></h3>
							<div class="inside">	
								<div>	
									<span class="iump-labels-onbutton"><?php _e('Show Tab', 'ihc');?></span>								
									<label class="iump_label_shiwtch iump-onbutton">
										<?php $checked = ($meta_arr['ihc_ap_show_aff_tab']) ? 'checked' : ''; ?>
										<input type="checkbox" class="iump-switch" onclick="iump_check_and_h(this, '#ihc_ap_show_aff_tab');" <?php echo $checked;?>>
										<div class="switch" style="display:inline-block;"></div>
									</label>
									<input type="hidden" name="ihc_ap_show_aff_tab" id="ihc_ap_show_aff_tab" value="<?php echo $meta_arr['ihc_ap_show_aff_tab'];?>" />									
								</div>
								<div>
									<span class="iump-labels-onbutton"><?php _e('Message', 'ihc');?></span>
									<div  class="iump-wp_editor" style="display: inline-block; vertical-align: top; width:79%;">
										<?php wp_editor(stripslashes($meta_arr['ihc_ap_aff_msg']), 'ihc_ap_aff_msg', array('textarea_name'=>'ihc_ap_aff_msg', 'editor_height'=>200));?>
									</div>									
								</div>
								<div><?php echo __("You can add 'Become Button' with the following shortcode: ", 'ihc') . '<b>[uap-user-become-affiliate]</b>';?></div>
								<div style="margin-top: 15px;">
									<input type="submit" value="Save" name="ihc_save" class="button button-primary button-large">
								</div>		
							</div>
						</div>						
					</form>
				<?php endif;?>
								
		<?php else:?>
		<div class="metabox-holder indeed">
		<div class="ihc-stuffbox" style="padding:20px 20px;">
			<h2>
				<label style=" font-size:16px; color:##B70B0B;">
					To get this section Available the <a href="http://codecanyon.net/item/ultimate-affiliate-pro-wordpress-plugin/16527729" target="_blank">Ultimate Affiliate Pro</a> system needs to be activated on your WordPress system.
				</label>
			</h2>		
			<p style="max-width:750px;"><strong>Ultimate Affiliate Pro</strong> is the newest and most completed Affiliate WordPress Plugin that allow you provide a premium platform for your Affiliates with different rewards and amounts based on Ranks or special Offers.
			<br/>You can turn on your Website into a REAL business and an income machine where you just need to sit down and let the others to work for you!
			</p>
			<p><a href="http://codecanyon.net/item/ultimate-affiliate-pro-wordpress-plugin/16527729?ref=azzaroco" target="_blank">
						<img src="<?php echo IHC_URL;?>admin/assets/images/uap-image-preview.jpg" style=" display: block;"/>
						</a>
			<br/><h2>You can find more details <a href="http://codecanyon.net/item/ultimate-affiliate-pro-wordpress-plugin/16527729" target="_blank">here</a></h2>
			</p>
			<div style="width:616px; padding-top:20px; margin-top:20px; border-top:1px solid #ddd;">
			<?php
			$url = 'http://codecanyon.net/item/ultimate-affiliate-pro-wordpress-plugin/16527729';
			$html = file_get_contents($url);
			
			$get1 = explode( '<div class="item-preview">' , $html );
			if (isset($get1[1])){
				$get2 = explode( '</div>' , $get1[1] );
			}		
			
			if (isset($get2[0])){
				preg_match_all('/<img.*?>/', $get2[0], $out);			
			}
			
			if (isset($out) && count($out) > 0){
				foreach($out as $value){
					echo '<div class="top-preview">'.$value[0].'</div>';
				}
			}
			
			$get3 = explode( '<div class="user-html">' , $html );
			$get4 = explode( '</div>' , $get3[1] );
			
			preg_match_all('/<img.*?>/', $get4[0], $images);
			if(isset($images) && count($images) > 0){
				foreach($images as $img){
					foreach($img as $value){
						if (strpos($value,'preview') === false && strpos($value,'button') === false) 
						echo '<a href="http://codecanyon.net/item/ultimate-affiliate-pro-wordpress-plugin/16527729" target="_blank">'.$value.'</a>';
					}
				}
			}
			?>
			</div>
		</div>
		</div>		
		<?php endif;?>
	<div class="ihc-clear"></div>
</div>
<?php 
