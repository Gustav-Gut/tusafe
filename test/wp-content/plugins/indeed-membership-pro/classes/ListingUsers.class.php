<?php 
if (!class_exists('ListingUsers')){
	class ListingUsers{
		private $args = array();
		private $total_pages = 0;
		private $users = array();
		private $div_parent_id = '';
		private $li_width = '';
		private $user_fields = array();
		private $total_users;
		private $single_item_template = '';
		private $general_settings = array();
		private $link_user_page = '';
		private $fields_label = array();
		private $permalink_type = '';
		private $filter_form_fields = array();
		private $search_get_filter = array();
		
		public function __construct($input=array()){
			/*
			 * @param array
			 * @return none
			 */
			if (empty($input)){
				return;
			} else {
				$this->args = $input;
				$this->general_settings = ihc_return_meta_arr('listing_users');	
				$link_user_page = get_option('ihc_general_register_view_user');
				if (!empty($link_user_page)){
					$link_user_page = get_permalink($link_user_page);			
					if (!empty($link_user_page)){
						$this->link_user_page = $link_user_page;
					}
					$this->permalink_type = get_option('permalink_structure');
				}
			}
		}
		
		public function run(){
			/*
			 * @param none
			 * @return string
			 */
			if (empty($this->args)){				
				return;
			}

			$output = '';
			$html = '';
			$js = '';
			$css = '';
			$js_after_html = '';
			$pagination = '';
			$search_bar = '';
			$search_filter = '';
			
			if (empty($this->args['entries_per_page'])) $this->args['entries_per_page'] = 25;
			$search_by = empty($this->args['search_by']) ? '' : $this->args['search_by'];
			$search_q = empty($_GET['ihc_search_u']) ? '' : $_GET['ihc_search_u']; 
			
			////// FILTER BY LEVELs
			if (!empty($this->args['filter_by_level']) && !empty($this->args['levels_in'])){
				if (strpos($this->args['levels_in'], ',')!==FALSE){
					$inner_join_levels = explode(',', $this->args['levels_in']);
				} else {
					$inner_join_levels = array($this->args['levels_in']);
				}
			} else {
				$inner_join_levels = array();
			}
			
			////////// ORDER
			$order_by = $this->args['order_by'];
			if ($order_by=='random'){
				$order_by = '';
			}
			$order_type = $this->args['order_type'];
			
			$this->set_filter_form_fields();
			
			//// FILTER
			if (!empty($_GET['filter'])){
				foreach ($_GET as $get_key=>$get_value){
					if (isset($this->filter_form_fields[$get_key]) && $_GET[$get_key]!=''){
						if (is_array($get_value)){
							if (isset($get_value[0]) && $get_value[0]!=''){
								$this->search_get_filter[$get_key] = $get_value;								
							}
						} else {
							$this->search_get_filter[$get_key] = $get_value;
						}
					}
				}
			}		
			
			//////////TOTAL USERS
			$this->total_users = $this->get_users($order_by, $order_type, -1, -1, TRUE, $inner_join_levels, $search_by, $search_q);
			if ($this->total_users>$this->args['num_of_entries']){
				$this->total_users = $this->args['num_of_entries'];
			}
			
			//limit && offset
			if (empty($this->args['slider_set'])){
				//// NO SLIDER + PAGINATION
				if (!empty($this->args['current_page'])){
					$current_page = $this->args['current_page'];
					$offset = ( $current_page - 1 ) * $this->args['entries_per_page']; //start from
				} else {
					$offset = 0;
				}
				$limit = $this->args['entries_per_page'];
				if ($offset + $limit>$this->total_users){
					$limit = $this->total_users - $offset;
				}			
			} else {
				////SLIDER
				$offset = 0;
				$limit = $this->args['num_of_entries'];				
			}
			
			///GETTING USER IDS
			$user_ids = $this->get_users($order_by, $order_type, (int)$offset, (int)$limit, FALSE, $inner_join_levels, $search_by, $search_q);
			
			if (!empty($user_ids)){
				/// we have users				
				$this->set_users_data($user_ids);////SET USERS DATA				
				$this->single_item_template = IHC_PATH . 'public/listing_users/themes/' . $this->args['theme'] . "/index.php";				
				///SET FIELDS LABEL
				$this->set_fields_label();
								
				if (!empty($this->users) && file_exists($this->single_item_template)){
					$html .= $this->create_the_html();
					$js .= $this->create_the_js();
					$css .= $this->create_the_css();
					$js_after_html .= $this->create_the_js_after_html();
				}				
				
				/// PAGINATION
				if (empty($this->args['slider_set']) && $this->args['entries_per_page']<$this->total_users){
					///adding pagination
					$pagination .= $this->print_pagination();
				}			
				if (empty($this->args['pagination_pos'])){
					$this->args['pagination_pos'] = 'top';
				}
				switch ($this->args['pagination_pos']){
					case 'top':
						$html = $pagination . $html;
						break;
					case 'bottom':
						$html = $html . $pagination;
						break;
					case 'both':
						$html = $pagination . $html . $pagination;
						break;
				}								
			}

			/// SEARCH BAR
			if (!empty($this->args['show_search'])){
				$search_bar .= '<form action="" method="get">';
				$search_bar .= '<div class="ihc-search-bar-wrapper">';
				$search_bar .= '<div class="ihc-input-pre"><i class="fa-ihc fa-srch-ihc"></i></div>';
				$get_val = empty($_GET['ihc_search_u']) ? '' : $_GET['ihc_search_u'];
				$search_bar .= '<input type="text" name="ihc_search_u" value="" class="ihc-search-bar" placeholder="'.__('Search for...','ihc').'" />';
				$search_bar .= '</div>';
				$search_bar .= '</form>';
			}						

			/// SHOW FILTER
			if (!empty($this->args['show_search_filter']) && !empty($this->args['search_filter_items'])){
				$search_filter = $this->print_filter_form();
				$html = '<div class="iump-listing-users-pre-wrapp">' . $search_bar . $html . '</div>';
			} else {
				$html = $search_bar . $html;
			}
			
			$output = $css . $js . $search_filter . $html . $js_after_html;
			return $output;
		}
		
		private function set_users_data($user_ids){
			/*
			 * @param array
			 * @return none
			 */
			$this->user_fields = explode(',', $this->args['user_fields']);
			if ($this->args['order_by']=='random'){
				shuffle($user_ids);
			}
			foreach ($user_ids as $k=>$id){
				foreach ($this->user_fields as $field){
					if (empty($users[$id][$field])){
						$user_data = get_userdata($id);
						if (isset($user_data->$field)){
							$this->users[$id][$field] = $user_data->$field;
						} else {
							@$this->users[$id][$field] = get_user_meta($id, $field, TRUE);
						}
					}
				}
			}
		}
		
		private function set_fields_label(){
			/*
			 * @param none
			 * @return none
			 */
			$fields_data = ihc_get_user_reg_fields();
			foreach ($this->user_fields as $field){
				$key = ihc_array_value_exists($fields_data, $field, 'name');
				if ($key!==FALSE && !empty($fields_data[$key]) && !empty($fields_data[$key]['label'])){
					$this->fields_label[$field] = $fields_data[$key]['label'];
				}				
			}
		}
		
		private function get_users($order_by, $order_type, $offset=-1, $limit=-1, $count=FALSE, $inner_join_levels=array(), $search_by='', $search_q=''){
			/*
			 * GETTING USERS FROM DB, COUNT USERS FROM DB
			 * @param: string, string, int, int, boolean, array, string, string
			 * @return array
			 */
			global $wpdb;
			$data = ihc_get_admin_ids_list();
			$not_in = implode(',', $data);
			
			$q = 'SELECT';
			if ($count){
				if (!empty($inner_join_levels)){
					$q .= " COUNT(DISTINCT b.user_id) as count_val";
				} else {
					$q .= " COUNT(DISTINCT c.user_id) as count_val";
				}
			} else {
				if (!empty($inner_join_levels)){
					$q .= " DISTINCT b.user_id as user_id";
				} else {
					$q .= " DISTINCT c.user_id as user_id";
				}
			}
			$q .= " FROM " . $wpdb->base_prefix ."users as a";
			if (!empty($inner_join_levels)){
				$q .= " INNER JOIN " . $wpdb->prefix . "ihc_user_levels as b";
				$q .= " ON a.ID=b.user_id";
			}	
			
			$q .= " INNER JOIN " . $wpdb->base_prefix . "usermeta as c on a.ID=c.user_id";
			
			/// FILTER
			if (!empty($this->search_get_filter)){
				$alias_array = array();
				foreach ($this->search_get_filter as $filter_key=>$filter_value){
					$alias = ihc_generate_alias_name(7, $alias_array);
					$alias_array[$filter_key] = $alias;				
					$q .= " INNER JOIN " . $wpdb->base_prefix . "usermeta as $alias on a.ID=$alias.user_id";	
				}
			}
			/// FILTER
			
			$q .= " WHERE 1=1";
			if (!empty($inner_join_levels)){
				$q .= " AND (";
				for ($i=0; $i<count($inner_join_levels); $i++){
					if ($i>0){
						$q .= " OR";
					}
					$q .= " b.level_id='" . $inner_join_levels[$i] . "'";
				}
				$q .= ") ";
				$q .= " AND b.start_time<NOW()";
				$q .= " AND b.expire_time>NOW()";		
			}
			
			//EXCLUDE ADMINISTRATORS
			if (!empty($not_in)){
				$q .= " AND a.ID NOT IN ('" . $not_in . "')";
			}
			
			if ($search_q && $search_by){
				$q .= " AND (";
				$search_fields = explode(',', $search_by);
				$mail_in = array_search('user_email', $search_fields);
				if ($mail_in!==FALSE && isset($search_fields[$mail_in])){
					$q .= " (a.user_email LIKE '%$search_q%') ";
					unset($search_fields[$mail_in]);
				}
				if (!empty($search_fields)){
					if ($mail_in!==FALSE){
						$q .= " OR ";
					}
					$fields_str = '';
					foreach ($search_fields as $field_val){
						if ($fields_str){
							$fields_str .= ",";
						}
						$fields_str .= "'$field_val'";
					}
					$q .= " (c.meta_key IN ($fields_str) AND c.meta_value LIKE '%$search_q%') ";
				}
				//$q .= " AND ( c.meta_key='$search_by' AND c.meta_value LIKE '%$search_q%' )"; /// old version
				$q .= ")";
			}

			/// EXCLUDE PENDING
			if (!empty($this->args['exclude_pending'])){
				$capabilities = $wpdb->prefix . 'capabilities';
				$q .= " AND ( c.meta_key='$capabilities' AND CAST(c.meta_value as CHAR) NOT LIKE '\"%pending_user%\"' ) ";
			}
			
			/// FILTER
			if (!empty($this->search_get_filter)){
				foreach ($this->search_get_filter as $filter_key=>$filter_value){
					$alias = $alias_array[$filter_key];					
					if (is_array($filter_value) && count($filter_value)==2){						
						$q .= "	AND $alias.meta_key='$filter_key' ";
						$q .= " AND CAST($alias.meta_value as INTEGER) BETWEEN '{$filter_value[0]}' AND '{$filter_value[1]}' ";	
					} else {
						$q .= "	AND $alias.meta_key='$filter_key' ";
						$q .= " AND $alias.meta_value='$filter_value' ";						
					}
				}
			}
			/// FILTER			
			
			if ($order_type && $order_by){
				$q .= " ORDER BY a." . $order_by . " " . $order_type;		
			}
		
		
			if ($limit>-1 && $offset>-1){
				$q .= " LIMIT " . $limit . " OFFSET " . $offset;
			}

			$data = $wpdb->get_results($q);
		
			if ($count){
				if (isset($data[0]) && isset($data[0]->count_val)){
					return $data[0]->count_val;
				} 
				return 0;
			} else {
				$return = array();
				if ($data && is_array($data)){
					foreach ($data as $obj){
						$return[] = $obj->user_id;	
					}			
				}
				return $return;
			}
			return $data;
		}
		
		private function create_the_js_after_html(){
			/*
			 * @param
			 * @return string
			 */
			$str = '';
			if (!empty($this->args['slider_set'])){
				$total_pages = count($this->users) / $this->args['items_per_slide'];
					
				if ($total_pages>1){
					$navigation = (empty($this->args['nav_button'])) ? 'false' : 'true';
					$bullets = (empty($this->args['bullets'])) ? 'false' : 'true';
					if (empty($this->args['autoplay'])){
						$autoplay = 'false';
						$autoplayTimeout = 5000;
					} else {
						$autoplay = 'true';
						$autoplayTimeout = $this->args['speed'];
					}
					$autoheight = (empty($this->args['autoheight'])) ? 'false' : 'true';
					$stop_hover = (empty($this->args['stop_hover'])) ? 'false' : 'true';
					$loop = (empty($this->args['loop'])) ? 'false' : 'true';
					$responsive = (empty($this->args['responsive'])) ? 'false' : 'true';
					$lazy_load = (empty($this->args['lazy_load'])) ? 'false' : 'true';
					$animation_in = (($this->args['animation_in'])=='none') ? 'false' : "'{$this->args['animation_in']}'";
					$animation_out = (($this->args['animation_out'])=='none') ? 'false' : "'{$this->args['animation_out']}'";
					$slide_pagination_speed = $this->args['pagination_speed'];
						
					$str .= "<script>
												jQuery(document).ready(function() {
													var owl = jQuery('#" . $this->div_parent_id . "');
													owl.owlihcCarousel({
															items : 1,
															mouseDrag: true,
															touchDrag: true,
													
															autoHeight: $autoheight,
													
															animateOut: $animation_out,
															animateIn: $animation_in,
													
															lazyLoad : $lazy_load,
															loop: $loop,
													
															autoplay : $autoplay,
															autoplayTimeout: $autoplayTimeout,
															autoplayHoverPause: $stop_hover,
															autoplaySpeed: $slide_pagination_speed,
													
															nav : $navigation,
															navSpeed : $slide_pagination_speed,
															navText: [ '', '' ],
													
															dots: $bullets,
															dotsSpeed : $slide_pagination_speed,
													
															responsiveClass: $responsive,
															responsive:{
																0:{
																	nav:false
																},
																450:{
																	nav : $navigation
																}
															}
													});	
												});
					</script>";
				}
			}
			return $str;
		}
	
		private function create_the_css(){
			/*
			 * @param none
			 * @return string
			 */
			//add the themes and the rest of CSS here...
			$str = '';			
			if (!empty($this->args['slider_set']) && !defined('IHC_SLIDER_LOAD_CSS')){
				///// SLIDER CSS
				$str .= '<link rel="stylesheet" type="text/css" href="' . IHC_URL . 'public/listing_users/assets/css/owl.carousel.css">';
				$str .= '<link rel="stylesheet" type="text/css" href="' . IHC_URL . 'public/listing_users/assets/css/owl.theme.css">';
				$str .= '<link rel="stylesheet" type="text/css" href="' . IHC_URL . 'public/listing_users/assets/css/owl.transitions.css">';
				define('IHC_SLIDER_LOAD_CSS', TRUE);
			}
			if (!empty($this->args['theme'])){
				///// THEME
				$str .= '<link rel="stylesheet" type="text/css" href="' . IHC_URL . 'public/listing_users/themes/' . $this->args['theme'] . '/style.css">';
			}
			if (!defined('IHC_COLOR_CSS_FILE')){
				////// COLOR EXTERNAL CSS
				$str .= '<link rel="stylesheet" type="text/css" href="' . IHC_URL . 'public/listing_users/assets/css/layouts.css">';
				define('IHC_COLOR_CSS_FILE', TRUE);
			}			
			$str .= '<style>';
			///// SLIDER COLORS
			if (!empty($this->args['color_scheme']) && !empty($this->args['slider_set'])){
				$str .= '
							.style_'.$this->args['color_scheme'].' .owl-ihc-theme .owl-ihc-dots .owl-ihc-dot.active span, .style_'.$this->args['color_scheme'].'  .owl-ihc-theme .owl-ihc-dots .owl-ihc-dot:hover span { background: #'.$this->args['color_scheme'].' !important; }
							.style_'.$this->args['color_scheme'].' .pag-theme1 .owl-ihc-theme .owl-ihc-nav [class*="owl-ihc-"]:hover{ background-color: #'.$this->args['color_scheme'].'; }
							.style_'.$this->args['color_scheme'].' .pag-theme2 .owl-ihc-theme .owl-ihc-nav [class*="owl-ihc-"]:hover{ color: #'.$this->args['color_scheme'].'; }
							.style_'.$this->args['color_scheme'].' .pag-theme3 .owl-ihc-theme .owl-ihc-nav [class*="owl-ihc-"]:hover{ background-color: #'.$this->args['color_scheme'].';}
						';
			}		
			////// ALIGN CENTER
			if (!empty($this->args['align_center'])) {
				$str .= '#'.$this->div_parent_id.' ul{text-align: center;}';
			}
			///// CUSTOM CSS
			if (!empty($this->general_settings['ihc_listing_users_custom_css'])){
				$str .= stripslashes($this->general_settings['ihc_listing_users_custom_css']);
			}
			//// RESPONSIVE 
			if (!empty($this->general_settings['ihc_listing_users_responsive_small'])){
				$width = 100 / $this->general_settings['ihc_listing_users_responsive_small'];
				$str .= '
						@media only screen and (max-width: 479px){
							#' . $this->div_parent_id . ' ul li{
								width: calc(' . $width . '% - 1px) !important;
							}
						}
				';				
			}
			if (!empty($this->general_settings['ihc_listing_users_responsive_medium'])){
				$width = 100 / $this->general_settings['ihc_listing_users_responsive_medium'];
				$str .= '
						@media only screen and (min-width: 480px) and (max-width: 767px){
							#' . $this->div_parent_id . ' ul li{
								width: calc(' . $width . '% - 1px) !important;
							}
						}
				';				
			}
			if (!empty($this->general_settings['ihc_listing_users_responsive_large'])){
				$width = 100 / $this->general_settings['ihc_listing_users_responsive_large'];
				$str .= '
						@media only screen and (min-width: 768px) and (max-width: 959px){
							#' . $this->div_parent_id . ' ul li{
								width: calc(' . $width . '% - 1px) !important;
							}
						}
				';				
			}
			$str .= '</style>';
			return $str;		
		}	

		private function create_the_js(){
			/*
			 * @param
			 * @return string
			 */
			$str = '';
			if (!empty($this->args['slider_set']) && !defined('IHC_SLIDER_LOAD_JS')){
				$str .= '<script src="' . IHC_URL . 'public/listing_users/assets/js/owl.carousel.js" ></script>';
				define('IHC_SLIDER_LOAD_JS', TRUE);
			}				
			return $str;
		}
		
		private function print_pagination(){
			/*
			 * @param none
			 * @return string
			 */
			$str = '';
			$current_page = (empty($this->args['current_page'])) ? 1 : $this->args['current_page'];
			$this->total_pages = ceil($this->total_users/$this->args['entries_per_page']);
			///$url = IHC_PROTOCOL . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			$url = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$str = '';
			
			if ($this->total_pages<=5){
				//show all the links
				for ($i=1; $i<=$this->total_pages; $i++){
					$show_links[] = $i;
				}
			} else {
				// we want to show only first, last, and the first neighbors of current page
				$show_links = array(1, $this->total_pages, $current_page, $current_page+1, $current_page-1);
			}
			
			for ($i=1; $i<=$this->total_pages; $i++){
				if (in_array($i, $show_links)){
					$href = (defined('IS_PREVIEW')) ? '#' : add_query_arg('ihcUserList_p', $i, $url);
					$selected = ($current_page==$i) ? '-selected' : '';
					$str .= "<a href='$href' class='ihc-user-list-pagination-item" . $selected . "'>" . $i . '</a>';		
					$dots_on = TRUE;
				} else {
					if (!empty($dots_on)){
						$str .= '<span class="ihc-user-list-pagination-item-break">...</span>';
						$dots_on = FALSE;
					}
				}
			}
			/// Back link
			if ($current_page>1){
				$prev_page = $current_page - 1;
				$href = (defined('IS_PREVIEW')) ? '#' : add_query_arg('ihcUserList_p', $prev_page, $url);
				$str = "<a href='" . $href . "' class='ihc-user-list-pagination-item'> < </a>" . $str;
			}
			///Forward link
			if ($current_page<$this->total_pages){
				$next_page = $current_page + 1;
				$href = (defined('IS_PREVIEW')) ? '#' : add_query_arg('ihcUserList_p', $next_page, $url);
				$str = $str . "<a href='" . $href . "' class='ihc-user-list-pagination-item'> > </a>";
			}
						
			//Wrappers
			$str = "<div class='ihc-user-list-pagination'>" . $str . "</div><div class='ihc-clear'></div>";
			return $str;
		}

		private function create_the_html(){
			/*
			 * @param
			 * @return string
			 */
			$str = '';
			$total_items = count($this->users);
			$items_per_slide = (empty($this->args['slider_set'])) ? $total_items : $this->args['items_per_slide'];
			
			include $this->single_item_template;
			if (empty($list_item_template)){
				return '';
			}
			
			$this->li_width = 'calc(' . 100/$this->args['columns'] . '% - 1px)';
			$i = 1;
			$breaker_div = 1;
			$new_div = 1;
			$color_class = (empty($this->args['color_scheme'])) ? 'style_0a9fd8' : 'style_' . $this->args['color_scheme'];
			$parent_class = (empty($this->args['slider_set'])) ? 'ihc-content-user-list' : 'ihc-carousel-view';//carousel_view
			$num = rand(1, 10000);
			$this->div_parent_id = 'indeed_carousel_view_widget_' . $num;
			$arrow_wrapp_id = 'wrapp_arrows_widget_' . $num;
			$ul_id = 'ihc_list_users_ul_' . rand(1, 10000);
				
			///// WRAPPERS
			$extra_class = (empty($this->args['pagination_theme'])) ? '' : $this->args['pagination_theme'];
			$str .= "<div class='' id='ihc_public_list_users_" . rand(1, 10000) . "'>";
			$str .= "<div class='$color_class'>";
			$str .= "<div class='" . $this->args['theme'] . " " . $extra_class . "'>";
			$str .= "<div class='ihc-wrapp-list-users'>";
			$str .= "<div class='$parent_class' id='$this->div_parent_id' >";
			
			////// ITEMS
			foreach ($this->users as $uid=>$arr){
				if (!empty($new_div)){
					$div_id = $ul_id . '_' . $breaker_div;
					$str .= "<ul id='$div_id' class=''>"; /////ADDING THE UL
				}
			
				$str .= $this->print_item($uid, $list_item_template, $socials_arr);///// PRINT SINGLE ITEM
			
				if ($i % $items_per_slide==0 || $i==$total_items){
					$breaker_div++;
					$new_div = 1;
					$str .= "<div class='ihc-clear'></div></ul>";
				} else {
					$new_div = 0;
				}
				$i++;
			}
				
			///// CLOSE WRAPPERS
			$str .= '</div>'; /// end of $parent_class
			$str .= '</div>'; /// end of ihc-wrapp-list-users
			$str .= '</div>'; /// end of $args['theme'] . " " . $args['pagination_theme']
			$str .= '</div>'; /// end of $color_class
			$str .= '</div>'; //// end of ihc_public_list_users_
			
			return $str;
		}

		private function print_item($uid, $template, $socials_arr){
			/*
			 * SINGLE ITEM
			 * @param int, string, array
			 * @return string
			 */
			$fields = $this->user_fields;
			
			$str = '';
			$str .= "<li style='width: $this->li_width' >";
			
			//AVATAR
			$this->users[$uid]['ihc_avatar'] = ihc_get_avatar_for_uid($uid);
			
			///STANDARD FIELDS
			$standard_fields = array(
										"user_login" => "IHC_USERNAME",
										"first_name" => "IHC_FIRST_NAME",
										"last_name" => "IHC_LAST_NAME",
										"user_email" => "IHC_EMAIL",
										"ihc_avatar" => "IHC_AVATAR",
 			);

			foreach ($standard_fields as $k=>$v){
				$data = '';
				if (in_array($k, $fields)){
					$data = $this->users[$uid][$k];
				}
				$template = str_replace($v, $data, $template);
				$key = array_search($k, $fields);
				if ($key!==FALSE){
					unset($fields[$key]);					
				}
			}

			///SOCIAL MEDIA STUFF
			if (in_array('ihc_sm', $fields)){
				$key = array_search('ihc_sm', $fields);
				unset($fields[$key]);
				$social_media_string = '';
				$sm_arr = array(
						'ihc_fb' => 'FB',
						'ihc_tw' => 'TW',
						'ihc_in' => 'LIN',
						'ihc_tbr' => 'TBR',
						'ihc_ig' => 'INS',
						'ihc_vk' => 'VK',
						'ihc_goo' => 'GP',
				);
				$sm_base = array(
									'ihc_fb' => 'https://www.facebook.com/',///old version was : profile.php?id=
									'ihc_tw' => 'https://twitter.com/intent/user?user_id=',
									'ihc_in' => 'https://www.linkedin.com/profile/view?id=',
									'ihc_tbr' => 'https://www.tumblr.com/blog/',
									'ihc_ig' => 'http://instagram.com/_u/',
									'ihc_vk' => 'http://vk.com/id',
									'ihc_goo' => 'https://plus.google.com/',									
								);
				foreach ($sm_arr as $k=>$v){
					$data = get_user_meta($uid, $k, TRUE);
					if (!empty($data)){
						$data = $sm_base[$k] . $data;
						$social_media_string .= str_replace($v, $data, $socials_arr[$k]);
					}
				}
				$template = str_replace("IHC_SOCIAL_MEDIA", $social_media_string, $template);
			}
			
			/// SOME EXTRA FIELDS
			
			$extra_fields = '';
			if ($fields){				
				foreach ($fields as $value){
					$extra_fields_str = '';
					if (!empty($this->users[$uid][$value])){
						if (!empty($this->args['include_fields_label']) && !empty($this->fields_label[$value])){
							$extra_fields_str .= '<span class="ihc-user-list-label">' . $this->fields_label[$value] . ' </span>';
							$extra_fields_str .= '<span class="ihc-user-list-label-result">';
						}else{
							$extra_fields_str .= '<span class="ihc-user-list-result">';
						}
						if (is_array($this->users[$uid][$value])){
							$extra_fields_str .= implode(',', $this->users[$uid][$value]);
						} else {
							$extra_fields_str .= $this->users[$uid][$value];
						}
						$extra_fields_str .= '</span>';
						$extra_fields_str .= '<div class="ihc-clear"></div>';
						if (!empty($extra_fields_str)){
							$extra_fields .= '<div class="member-extra-single-field">' . $extra_fields_str . '</div>';
						}					
					}					
				}
			}
			$template = str_replace('IHC_EXTRA_FIELDS', $extra_fields, $template);

			/// LINK TO USER PAGE
			$link = '#';
			if (!empty($this->args['inside_page']) && !empty($this->link_user_page)){
				$target_blank = (empty($this->general_settings['ihc_listing_users_target_blank'])) ? '' : 'target="_blank"';
				if (empty($this->users[$uid]['user_login'])){
					$username = Ihc_Db::get_username_by_wpuid($uid);		
					$username = urlencode($username);	
				} else {
					$username = urlencode($this->users[$uid]['user_login']);					
				}
								  
				if ($this->permalink_type){
					$link = trailingslashit(trailingslashit($this->link_user_page) . $username );
				} else {
					$link = add_query_arg('ihc_name', $username, $this->link_user_page);
				}
											
				$link = ' href="' . $link . '" ' . $target_blank;			
			}
			$template = str_replace("#POST_LINK#", $link, $template);
			
			$str .= $template;
			$str .= '</li>';
			return $str;
		}

		private function set_filter_form_fields(){
			/*
			 * @param none
			 * @return string
			 */
			 if (isset($this->args['search_filter_items'])){
				 $fields = explode(',', $this->args['search_filter_items']);
				 $output = '';
				 global $post;
				 $base_url = get_permalink(@$post->ID);
				 
				 if ($fields){
				 	foreach ($fields as $field){
				 		$temporary_array['type'] = ihc_register_field_get_type_by_slug($field);
						$temporary_array['label'] = ihc_get_custom_field_label($field);	
						$temporary_array['name'] = $field;
						$temporary_array['values'] = $this->get_register_field_possible_values($field, $temporary_array['type']);
						$this->filter_form_fields[$field] = $temporary_array;
			 		}
				 }			 	
			 }
	
		}

		private function print_filter_form(){
			/*
			 * @param none
			 * @return string
			 */
			 $output = '';
			 global $post;
			 $base_url = get_permalink(@$post->ID);

			 ob_start();
			 require IHC_PATH . 'public/views/listing_users-filter.php';
			 $output = ob_get_contents();
			 ob_end_clean();
			 return $output;	
		}
		
		private function get_register_field_possible_values($field_slug='', $type=''){
			/*
			 * @param string, string
			 * @return array
			 */
			 $array = array();
			 if ($field_slug){
			 	 global $wpdb;
				 $table = $wpdb->base_prefix . 'usermeta';
				 switch ($type){
					case 'ihc_country':
					case 'multiselect':
					case 'select':
					case 'radio':
						$data = $wpdb->get_results("SELECT DISTINCT meta_value FROM $table WHERE meta_key='$field_slug' ORDER BY umeta_id DESC;");
						if ($data){
						 	 foreach ($data as $object){
						 	 	if (isset($object->meta_value)){
							 	 	$array[] = $object->meta_value;				 	 		
						 	 	}
						 	 }
						}
						break;
					case 'number':
						$data = $wpdb->get_row("SELECT CAST(meta_value AS INTEGER) as min FROM $table WHERE meta_key='$field_slug' AND meta_value!='' ORDER BY CAST(meta_value AS INTEGER) ASC LIMIT 1;");
						if (isset($data->min)){
							$array['min'] = $data->min;
						}
						$data = $wpdb->get_row("SELECT CAST(meta_value AS INTEGER) as max FROM $table WHERE meta_key='$field_slug' AND meta_value!='' ORDER BY CAST(meta_value AS INTEGER) DESC LIMIT 1;");	
						if (isset($data->max)){
							$array['max'] = $data->max;
						}					
						break;
					case 'date':
						$data = $wpdb->get_row("SELECT meta_value FROM $table WHERE meta_key='$field_slug' AND meta_value!='' ORDER BY meta_value ASC LIMIT 1;");	
						if (isset($data->meta_value)){
							$array['min'] = $data->meta_value;
						}
						$data = $wpdb->get_row("SELECT meta_value FROM $table WHERE meta_key='$field_slug' AND meta_value!='' ORDER BY meta_value DESC LIMIT 1;");	
						if (isset($data->meta_value)){
							$array['max'] = $data->meta_value;
						}
						break;
				}
			 }
			 return $array;
		}				
		
	}
}