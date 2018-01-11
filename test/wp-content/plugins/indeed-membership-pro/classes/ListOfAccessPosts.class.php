<?php
if (!class_exists('ListOfAccessPosts')):
	
class ListOfAccessPosts{
	private $levels = array();
	private $levels_conditions = '';
	private $metas = array();
	private $post_types_in = "'post','page'";
	
	public function __construct($levels=array(), $metas=array()){
		/*
		 * @param array
		 * @return none
		 */
		$this->levels = $levels;
		$this->metas = $metas;
	}
	
	public function output(){
		/*
		 * @param none
		 * @return string
		 */
		if (!empty($this->metas['ihc_list_access_posts_per_page_value'])){
			$limit = $this->metas['ihc_list_access_posts_per_page_value'];
		} else {
			$limit = 25;			
		}

		$total = $this->get_count();
		$current_page = (empty($_GET['ihcdu_page'])) ? 1 : $_GET['ihcdu_page'];
		if ($current_page>1){
			$offset = ( $current_page - 1 ) * $limit;
		} else {
			$offset = 0;
		}
		require_once IHC_PATH . 'classes/Ihc_Pagination.class.php';
		///$base_url = IHC_PROTOCOL . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		$base_url = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$base_url = remove_query_arg('ihcdu_page', $base_url);
		$pagination_object = new Ihc_Pagination(array(
													'base_url' => $base_url,
													'param_name' => 'ihcdu_page',
													'total_items' => $total,
													'items_per_page' => $limit,
													'current_page' => $current_page,
		));
		$data['pagination'] = $pagination_object->output();
		if ($offset + $limit>$total){
			$limit = $total - $offset;
		} 

		if ($this->metas['ihc_list_access_posts_order_limit']<$limit){
			$limit = $this->metas['ihc_list_access_posts_order_limit'];
		}
		$data['metas'] = $this->metas;
		$data['items'] = $this->get_data($limit, $offset);

		ob_start();
		require IHC_PATH . 'public/views/list_access_posts.php';
		$output = ob_get_contents();
		ob_end_clean();
		return $output;						 		
	}
	
	private function get_count(){
		/*
		 * @param int
		 * @return int
		 */
		global $wpdb;
		$posts = $wpdb->prefix . 'posts';
		$postmeta = $wpdb->prefix . 'postmeta';	 
	
		$this->set_level_conditions();
		$limit = '';
		if (!empty($this->metas['ihc_list_access_posts_order_limit'])){
			$limit = ' LIMIT ' . $this->metas['ihc_list_access_posts_order_limit'];
		}
		$this->set_post_types();
		$q = "
				SELECT COUNT(DISTINCT(b.ID)) as count_value
				FROM $postmeta a
				INNER JOIN $posts b
				ON a.post_id=b.ID
				INNER JOIN $postmeta c	
				ON c.post_id=a.post_id
				WHERE 
				( b.post_type IN ({$this->post_types_in}) )
				AND
				( a.meta_key='ihc_mb_type' AND a.meta_value='show' )
				AND 
				( c.meta_key='ihc_mb_who' AND {$this->levels_conditions} )
				$limit
		";
		$data = $wpdb->get_row($q);
		if ($data && isset($data->count_value)){
			return $data->count_value;
		}
		return 0;
	}	

	private function get_data($limit=30, $offset=0){
		/*
		 * @param int
		 * @return int
		 */		
		$array = array();
		$order_by = $this->metas['ihc_list_access_posts_order_by'];
		$order_type = $this->metas['ihc_list_access_posts_order_type'];		
		global $wpdb;
		$posts = $wpdb->prefix . 'posts';
		$postmeta = $wpdb->prefix . 'postmeta';
		
		$select_fields = '';
		if (!empty($this->metas['ihc_list_access_posts_item_details'])){
			$select_array = explode(',', $this->metas['ihc_list_access_posts_item_details']);
			if (in_array('post_title', $select_array)){
				$select_fields .= ', b.post_title as title';		
			}
			if (in_array('post_excerpt', $select_array)){
				$select_fields .= ', b.post_excerpt as post_excerpt';		
			}	
			if (in_array('feature_image', $select_array)){
				$get_image = TRUE;
			}					
			if (in_array('post_date', $select_array)){
				$select_fields .= ', b.post_date as post_date';	
			}
			if (in_array('post_author', $select_array)){
				$select_fields .= ', b.post_author as post_author';	
			}							
		}
		
		$q = "
		SELECT DISTINCT(a.post_id) as id $select_fields
			FROM $postmeta a
			INNER JOIN $posts b
			ON a.post_id=b.ID
			INNER JOIN $postmeta c	
			ON c.post_id=a.post_id
			WHERE 
			( b.post_type IN ({$this->post_types_in}) )
			AND
			( a.meta_key='ihc_mb_type' AND a.meta_value='show' )
			AND 
			( c.meta_key='ihc_mb_who' AND {$this->levels_conditions} )
			GROUP BY id 
			ORDER BY b.$order_by $order_type
			LIMIT $limit OFFSET $offset
		";		
		$db_result = $wpdb->get_results($q);

		foreach ($db_result as $db_object){
			$temp = (array)$db_object;
			$temp['drip_content_conditions'] = $this->get_drip_content_conditions($temp['id']);
			$temp['permalink'] = get_permalink($temp['id']);
			if (!empty($get_image)){
				$temp['feature_image'] = wp_get_attachment_image_src(get_post_thumbnail_id($temp['id']),'single-post-thumbnail');
				if (!empty($temp['feature_image']) && !empty($temp['feature_image'][0])){
					$temp['feature_image'] = $temp['feature_image'][0];
				}
			}
			if (!empty($temp['post_author'])){
				$temp_user = get_userdata($temp['post_author']);
				if (!empty($temp_user->first_name) && !empty($temp_user->last_name)){
					$temp['post_author'] = $temp_user->first_name . ' ' . $temp_user->last_name;
				} else if ($temp_user->user_nicename){
					$temp['post_author'] = $temp_user->user_nicename;
				} else {
					$temp['post_author'] = '';
				}
			}
			if (!empty($temp['post_date'])){
				$temp['post_date'] = ihc_convert_date_to_us_format($temp['post_date']);
			}
			$array[$temp['id']] = $temp;
		}

		return $array;
	}
	
	private function set_level_conditions(){
		/*
		 * @param none
		 * @return none
		 */
		if (count($this->levels)==0){
			$this->levels_conditions = " FIND_IN_SET('reg', c.meta_value) ";
		} else if (count($this->levels)==1){
			$cond_lid = (isset($this->levels[0])) ? $this->levels[0] : '';
			$this->levels_conditions = " FIND_IN_SET($cond_lid, c.meta_value) ";
		} else {
			$this->levels_conditions .= " ( ";
			foreach ($this->levels as $lid){
				if (!empty($or)){
					$this->levels_conditions .= " OR ";
				}
				$this->levels_conditions .= " FIND_IN_SET($lid, c.meta_value) ";
				$or = TRUE;
			}
			$this->levels_conditions .= " ) ";
		}		 
	}
	
	private function set_post_types(){
		/*
		 * @param none
		 * @return none
		 */
		if (!empty($this->metas['ihc_list_access_posts_order_post_type'])){
			$str = '';
			$this->metas['ihc_list_access_posts_order_post_type'] = explode(',', $this->metas['ihc_list_access_posts_order_post_type']);
			foreach ($this->metas['ihc_list_access_posts_order_post_type'] as $value){
				if ($str){
					$str .= ",";
				}
				$str .= "'$value'";
			}
			$this->post_types_in = $str;
		}
	}
	
	private function get_drip_content_conditions($post_id=0){
		/*
		 * @param int
		 * @return array
		 */
		 $array = array();
		 global $wpdb;
		 $table = $wpdb->prefix . 'postmeta';
		 $data = $wpdb->get_results("SELECT meta_key, meta_value 
		 								FROM $table 
		 								WHERE post_id='$post_id' 
		 								AND meta_key IN 
		 								(
		 								 'ihc_drip_content', 
		 								 'ihc_drip_start_type', 
		 								 'ihc_drip_end_type', 
		 								 'ihc_drip_start_numeric_type',
		 								 'ihc_drip_start_numeric_value',
		 								 'ihc_drip_end_numeric_type',
		 								 'ihc_drip_end_numeric_value',
		 								 'ihc_drip_start_certain_date',
		 								 'ihc_drip_end_certain_date'
		 								 );"
		);
		if ($data){
			foreach ($data as $obj){
				$array[$obj->meta_key] = $obj->meta_value;
			}
		}
		return $array;
	}
	
}	
	
endif;
