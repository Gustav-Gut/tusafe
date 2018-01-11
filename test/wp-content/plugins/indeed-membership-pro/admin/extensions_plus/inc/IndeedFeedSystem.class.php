<?php 
if (!class_exists('IndeedFeedSystem')){
	class IndeedFeedSystem{
		
		private static $notification = FALSE;
		private static $current_notification = array();
		private static $installed_items_run = FALSE;
		private static $installed_items = array();
		private static $global_menu = FALSE;
		private $url = 'http://market.wpindeed.com/api';
		private $option_name_update_time = 'indeed_feed_data_update_timestamp';
		private $update_time_interval = 43200;//twelve hours
		private $pause_time = 172800;//forty eight hours
		private $main_data = array(); //used in notification
		private $dir_path = '';
		private $dir_url = '';
		private $menu_style = 'color:#fff; background: #d54e21; padding: 4px 16px 4px 13px; margin: 0 0 0 -13px; width: 100%; display: block;    box-sizing: content-box;';
		private $main_menu_style = 'color: #fff; background: #d54e21;display: block; padding: 3px;';
		private $admin_url = '';
		private static $created_menu_parent_slug_arr=array();
		private $option_name_data = 'indeed_feed_data'; /// settings, notifications and cats
		private $option_name_items = 'indeed_feed_data-items'; /// extensions
		
		public function __construct($menu_parent_slug=''){
			/*
			 * @param string
			 * @return none
			 */
			if (is_admin()){
				//prevent print same menu multiple times
				if (!empty(self::$created_menu_parent_slug_arr) && $menu_parent_slug && in_array($menu_parent_slug, self::$created_menu_parent_slug_arr)){
					return;
				} else {
					self::$created_menu_parent_slug_arr[] = $menu_parent_slug;
				}				
				
				if (empty($menu_parent_slug)){
					//no submenu, create menu parent
					if (self::$global_menu){
						return;//already exists
					}
					self::$global_menu = TRUE;
					add_action('admin_menu', array($this, 'add_menu'), 70 );
					$this->admin_url = admin_url('admin.php?page=indeed_extensions_plus');
					add_action('admin_notices', array($this, 'print_global_style'));
				} else {
					$this->menu_parent = $menu_parent_slug;	
					add_action('admin_menu', array($this, 'add_submenu'), 99);
					$this->admin_url = admin_url('admin.php?page=addons-' . $this->menu_parent);
				}
				$this->dir_path =  plugin_dir_path(__FILE__);
				$this->dir_url = plugin_dir_url(__FILE__);
				
				
				add_action('wp_ajax_indeed_update_notify_date_show', array($this, 'update_notify_date_show'));//close notify
				
				//set the installed items
				if (!self::$installed_items_run){
					self::$installed_items_run = TRUE;
					$this->set_installed_items();
					$this->set_global_settings();
				}
				
				//Notifications
				if (!self::$notification && (empty($_GET['page']) || (strpos($_GET['page'], 'addons-')!==0) && $_GET['page']!='indeed_extensions_plus' ) ){///this module must run only once				
					self::$notification = TRUE;
					if ($this->check_notify_date_show()){
						$this->check_notifications();
						//add_action('admin_notices', array($this, 'do_notification'));						
					}
				}
			}
		}
		
		public function add_menu(){
			/*
			 * MAIN MENU
			 * @param none
			 * @return none
			 */	
			add_menu_page('Extensions Plus', '<span style=" ' . $this->main_menu_style . ' ">Extensions Plus</span>', 'manage_options',	'indeed_extensions_plus', array($this, 'output') , 'dashicons-cart');
		}
		
		public function add_submenu(){
			/*
			 * SUBMENU TO PLUGIN/THEME
			 * @param none
			 * @return none
			 */
			add_submenu_page( $this->menu_parent, 'AddOns', '<span style=" ' . $this->menu_style . ' ">Extensions Plus</span>', 'manage_options', 'addons-' . $this->menu_parent, array($this, 'output') );
		}
		
		public function output(){
			/*
			 * @param none
			 * @return string
			 */
			if (!empty($_REQUEST['do_update'])){
				//manually update
				$data = $this->do_update();
			} else {
				$data = $this->get_data();
			}
			
			$items = $this->get_items();		
			
			if (!empty($data) && is_array($data) && !empty($items) && !empty($data['settings']) && !empty($data['cats'])){
				$items = $this->do_reorder($items);
				$items = $this->remove_inactive($items);
				$cats = $this->do_reorder($data['cats']);				
				$cats = $this->remove_inactive($cats);
				$settings = array_merge($this->return_default_array_key('settings'), $data['settings']);	
				include_once $this->dir_path . 'output.php';
			}
			include_once $this->dir_path . 'output.php';
		}
		
		private function get_data(){
			/*
			 * @param none
			 * @return string
			 */	
			////UPDATE
			$last_update = get_option($this->option_name_update_time);
			if (empty($last_update)){
				return $this->do_update();
			} else if (time()>$last_update+$this->update_time_interval){
				return $this->do_update();
			}
			
			//// GET FROM CACHE 
			$cache = get_option($this->option_name_data);
			if (empty($cache)){
				return $this->do_update();//NO CACHE AVAILABLE
			} else {
				return $cache;
			}			
		}
		
		private function get_items(){
			/*
			 * @param none
			 * @return array
			 */
			$items = get_option($this->option_name_items);
			return $items;
		}
		
		private function do_update(){
			/*
			 * @param none
			 * @return string
			 */
			@$response = wp_remote_get( $this->url );
			if (wp_remote_retrieve_response_code($response)==200 && !is_wp_error($response) && is_array($response) && !empty($response['body'])){
				@$data = json_decode(@$response['body'], TRUE);				
				if (!empty($data) && is_array($data) && !empty($data['items']) ){
					/*
					 * 	&& !empty($data['cats']) && !empty($data['settings']) && !empty($data['notifications']) 
					 */	
					// STORE THE RESULT
					update_option($this->option_name_items, $data['items']);
					unset($data['items']);
					update_option($this->option_name_data, $data);
					update_option($this->option_name_update_time, time());
					// AND RETURN DATA
					return $data;
				}
				//RETURN PREVIOUS DATA
				$data = get_option($this->option_name_data);
				if (!empty($data) && is_array($data)){
					return $data;
				}
				return '';//nothing to return
			}
		}
		
		private function set_global_settings(){
			/*
			 * @param none
			 * @return none
			 */
			$data = get_option($this->option_name_data);
			
			if ($data!==FALSE && is_array($data) && isset($data['settings'])){
				$this->main_data = $data;
				$data['settings'] = array_merge($this->return_default_array_key('settings'), $data['settings']);
				if (isset($data['settings']['update_time_interval'])){
					$this->update_time_interval = $data['settings']['update_time_interval'];
				}
				if (isset($data['settings']['pause_time'])){
					$this->pause_time = $data['settings']['pause_time'];
				}
			}
		}
		
		private function return_default_array_key($type=''){
			/*
			 * @param string
			 * @return array
			 */
			$arr = array();
			if ($type){
				switch ($type){
					case 'items':
						$arr = array(
									'category' => 'plugins',
									'image' => '',
									'title' => '',
									'item_name' => '',
									'short_description' => '',
									'price' => '',
									'envato_link' => '',
									'demo_link' => '',
									'keywords' => '',
									'author_name' => '',
									'author_link' => '',
									'long_description' => '',
									'other_images' => array(),
									'order' => 0,
									'status' => 0,
									'new' => 0,
									'updated' => 0,
						);
						break;
					case 'notifications':
						$arr = array(
									'type' => 'plugins',
									'item_name' => '',
									'message' => '',
						);
						break;
					case 'cats':
						$arr = array(
									'slug' => '',
									'label' => '',
									'order' => 0,
									'status' => 0,
									'description' => '',
						);
						break;
					case 'settings':
						$arr = array(
									'global_description' => 'Plugins extend and expand the functionality of WordPress. You may automatically install plugins from the WordPress Plugin Directory or upload a plugin in .zip format via this page.',
									'update_time_interval' => 43200,//twelve hours
									'pause_time' => 172800,//forty eight hours
						);
						break;
				}				
			}
			return $arr;
		}
		
		private function do_reorder($arr=array()){
			/*
			 * @param array
			 * @return array
			 */
			if (is_array($arr) && count($arr)){
				foreach ($arr as $inside_array){
					if (isset($inside_array['order'])){
						$key = $inside_array['order'];
						if (isset($return_array[$key])){
							$new_key = $key++;
							$total_count = count($return_array);
							for ($i=$total_count-1; $i>=$new_key; $i--){
								if (isset($return_array[$i])){
									$return_array[$i+1] = $return_array[$i];
								}
							}
							$return_array[$new_key] = $inside_array;
						} else {
							$return_array[$key] = $inside_array;
						}
					} else {
						$return_array[] = $inside_array;
					}
				}
				ksort($return_array);
				return $return_array;
			}
			return $arr;
		}
		
		private function remove_inactive($arr=array()){
			/*
			 * @param array
			 * @return array
			 */
			if (is_array($arr) && count($arr)){
				foreach ($arr as $key=>$inside_array){
					if (!empty($inside_array['status'])){
						$return_array[$key] = $inside_array;
					}
				}
				if (!empty($return_array)){
					return $return_array;					
				}
			}
			return $arr;
		}
		
		private function check_notifications(){
			/*
			 * Run Only Once
			 * @param none
			 * @return none
			 */
			if (!empty($this->main_data)){
				$data = $this->main_data;
			} else {
				$data = get_option($this->option_name_data);				
			}
			if (!empty($data['notifications']) && is_array($data['notifications'])){
				//run notifications
				$last_notification = get_option('indeed_last_notification_key_used');
				if ($last_notification===FALSE){
					$last_notification = 0;
				}
				$arr = $data['notifications'];
				if (isset($arr[$last_notification])) unset($arr[$last_notification]);
				end($arr);
				$last_key = key($arr);				
				$i = $last_notification + 1;
				while ($arr){
					if ($i>$last_key){
						$i = 0;
					}
					if ( isset($arr[$i]) && isset($arr[$i]['item_name']) && !in_array($arr[$i]['item_name'], self::$installed_items)){						
						self::$current_notification['index'] = $i;
						self::$current_notification['message'] = $data['notifications'][$i]['message'];
						return;
					}
					if (isset($arr[$i])) unset($arr[$i]);
					$i++;
				}

				if (isset($data['notifications'][$last_notification]) && isset($data['notifications'][$last_notification]['item_name']) 
					&& !in_array($data['notifications'][$last_notification]['item_name'], self::$installed_items)){
						self::$current_notification['index'] = $last_notification;
						self::$current_notification['message'] = $data['notifications'][$last_notification]['message'];
				}
			}
		}
		
		public function do_notification(){
			/*
			 * NOTIFICATION ACTION
			 * @param none
			 * @return string
			 */
			if (!empty(self::$current_notification) && !empty(self::$current_notification['message']) && isset(self::$current_notification['index'])){
				update_option('indeed_last_notification_key_used', self::$current_notification['index']);
				
				$output = '';
				$output .= '<link rel="stylesheet" href="' . $this->dir_url . 'files/font-awesome.css" type="text/css" media="all" />';
				$output .= '<script>';
				$output .= 'function indeed_close_notf_div(){
							   	jQuery.ajax({
							        type : "post",
							        url : "' . get_site_url() . '/wp-admin/admin-ajax.php",
							        data : {
							                   action: "indeed_update_notify_date_show",
							        },
							        success: function (r){jQuery(\'#indeed_main_notify\').fadeOut();}
							   });
							}';
				$output .= '</script>';
				$output .= '<div class="updated" id="indeed_main_notify" style="padding: 15px 10px;">';
				$output .= '<div style="display: inline-block;max-width: 80%;">' . self::$current_notification['message'] . '</div>';
				$output .= '<div class="button" style="float:right;    margin-top: -5px; vertical-align: top;text-align:right;cursor: pointer;margin-left: 10px;" onClick="indeed_close_notf_div();"><i class="fa-ifs ifs-close"></i> Dismiss this message</div>';
				$output .= '<a class="button button-primary" style="float:right;    margin-right: 5px;    margin-top: -5px;" href="' . $this->admin_url  . '"><i class="fa-ifs ifs-busket-small"></i>Check all Externsion Plus Items</a>';
				$output .= '</div>';
				echo $output;				
			}
			return '';
		}		
		
		private function set_installed_items(){
			/*
			 * Run Only Once
			 * @param none
			 * @return none
			 */
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$plugins = get_plugins();
			foreach ($plugins as $k=>$arr){
				self::$installed_items[] = $arr['Name'];
			}
			
			$themes = wp_get_themes();
			foreach ($themes as $name=>$theme){
				self::$installed_items[] = $name;				
			}
		}
		
		
		private function check_notify_date_show(){
			/*
			 * @param none
			 * @return boolean
			 */
			$check = get_option('indeed_notf_last_print');
			if ($check){
				if ($check+$this->pause_time<time()){
					update_option('indeed_notf_last_print', 0);
					return TRUE;
				}
				return FALSE;
			}
			return TRUE;
		}
		
		public function update_notify_date_show(){
			/*
			 * @param none
			 * @return none
			 */
			update_option('indeed_notf_last_print', time());
			echo 1;
			die();
		}
		
		
		public function print_global_style(){
			/*
			 * @param none
			 * @return none
			 */
			$output = '';
			$output .= '<style>';
			$output .= '#toplevel_page_indeed_extensions_plus .dashicons-cart{padding: 3px 0px;}';
			$output .= '#toplevel_page_indeed_extensions_plus,#toplevel_page_indeed_extensions_plus a.menu-top{background: #d54e21 !important;}';
			$output .= '#toplevel_page_indeed_extensions_plus:hover{color: #FFF !important; background-color: #d54e21 !important;}';
			$output .= '#toplevel_page_indeed_extensions_plus:hover wp-menu-image:before{color: #FFF !important;}'; 
			$output .= '#toplevel_page_indeed_extensions_plus div.wp-menu-image:before{color: #FFF !important;}'; 			
			$output .= '</style>';
			echo $output;
		}
		
	}//end of class
}//end of class exists

