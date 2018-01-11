<?php 
//////////////////////////////LEVELS
function ihc_save_level($post_data=array(), $install=FALSE){
	/*
	 * @param array
	 * @return none
	 */
	if (isset($post_data['name']) && $post_data['name']!=''){
		$option_name = 'ihc_levels';
		$data = get_option($option_name);
		if (count($data)>=3 && (!defined('IHCACTIVATEDMODE') || !IHCACTIVATEDMODE)){
			if (!$install){
				echo '<div class="ihc-admin-err-level">' . __("You cannot add more than one level on Trial Version!", 'ihc') . '</div>';
			}
			return;
		}
		$arr = array(   
							'name'=>'', 
							'payment_type'=>'',
							'price'=>'',					
						    'label'=>'',
							'description'=>'',
							'price_text' => '',
							'order' => '',
							'access_type' => 'unlimited',
							'access_limited_time_type' => 'D',
							'access_limited_time_value' => '',
							'access_interval_start' => '',
							'access_interval_end' => '',
							'access_regular_time_type' => 'D',
							'access_regular_time_value' => '',
							'billing_type' => '',
							'billing_limit_num' => '2',
							'show_on' => '1',
							'afterexpire_level' => -1,
							'custom_role_level' => '-1',
							'start_date_content' => '0',
							'special_weekdays' => '',
							//trial
							'access_trial_time_value' => '',
							'access_trial_time_type' => 'D',
							'access_trial_price' => '',
							'access_trial_couple_cycles' => '',			
							'access_trial_type' => 1,
							///magic feat
							'badge_image_url' => '',
		);
		foreach ($arr as $k=>$v){
			$arr[$k] = (isset($post_data[$k])) ? $post_data[$k] : '';
		}
		
		//if it's not regular period type of level ... force billing_type to be bl_onetime
		if (isset($arr['access_type']) && $arr['access_type']!='regular_period'){				
			$arr['billing_type'] = 'bl_onetime';
		}
		
		$arr = apply_filters('ihc_save_level_filter', $arr);
		
		if ($data!==FALSE){
			if (isset($post_data['level_id']) && $post_data['level_id']!=''){
				//update level
				$id = $post_data['level_id'];
			} else {
				end($data);
				$id = key($data);
				$arr['name'] = ihc_make_string_simple($arr['name']);
				$id++;
			}
			$check = ihc_array_value_exists($data, $post_data['name'], 'name');
			if ($check!==FALSE && $check!=$id){
				if (!$install){
					echo '<div class="ihc-admin-err-level">' . __("A Level with this name ", 'ihc') . $post_data['name'] . __(" already exists! Please choose another name!", 'ihc') . '</div>';	
				}			
				return 0;
			} 
			$data[$id] = $arr;				
			update_option($option_name, $data);
			return $id;
		} else {
			//create the first level		
			$data[1] = $arr;
			update_option($option_name, $data);
			return 1;
		}
	}
}

function ihc_delete_level($lid=-1){
	/*
	 * delete LEVEL from wp_options, ihc_user_levels and user_meta 
	 * @param none
	 * @return none
	 */
	//delete level wp option
	$data = get_option('ihc_levels');
	foreach ($data as $k=>$v){
		if ($k==$lid){
			unset($data[$k]);
		}
	}
	update_option('ihc_levels', $data);
		
	//delete transactions 
	global $wpdb;
	$wpdb->query("DELETE FROM ".$wpdb->prefix."ihc_user_levels WHERE level_id=" . $lid);
	
	do_action('ihc_delete_level_action', $lid);
	
	//delete user-level
	$users = $wpdb->get_results("SELECT * FROM ".$wpdb->base_prefix."users;");
	foreach ($users as $u){
		$u_levels = get_user_meta($u->ID, 'ihc_user_levels', TRUE);
		if ($u_levels!==FALSE && $u_levels!=''){
			$u_levels_arr = explode(",", $u_levels);
			if ($u_levels_arr){
				foreach ($u_levels_arr as $k=>$u_lid){
					if ($u_lid==$lid){
						unset($u_levels_arr[$k]);
						$level_str = implode(',', $u_levels_arr);
						update_user_meta($u->ID, 'ihc_user_levels', $level_str);
						break;
					}
				}
			}
		}
	}
	
	$table = $wpdb->prefix . 'postmeta';
	$data = $wpdb->get_results("SELECT post_id, meta_value FROM $table WHERE meta_key='ihc_mb_who';");		
	if ($data){
		foreach ($data as $object){
			if ($object->meta_value){
				$post_levels = explode(',', $object->meta_value);
				if ($post_levels){
					foreach ($post_levels as $k=>$u_lid){
						if ($u_lid==$lid){
							unset($post_levels[$k]);
							$level_str = implode(',', $post_levels);
							$wpdb->query("UPDATE $table SET meta_value='$level_str' WHERE post_id='" . $object->post_id . "' AND meta_key='ihc_mb_who';");
							break;
						}
					}
				}				
			}		
		}
	}	
}


