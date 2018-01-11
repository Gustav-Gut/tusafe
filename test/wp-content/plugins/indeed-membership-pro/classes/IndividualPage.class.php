<?php
if (!class_exists('IndividualPage')):
	
class IndividualPage{
	private static $metas = array();
	
	public function __construct($metas=array()){
		/*
		 * @param array
		 * @return none
		 */
		 if (!empty($metas)){
		 	self::$metas = $metas;
		 } else {
		 	self::$metas = ihc_return_meta_arr('individual_page');
		 }
	}
	
	public function generate_page_for_user($uid=0){
		/*
		 * @param int (user id)
		 * @return int (post id)
		 */
		 $post_id = 0;
		 if ($uid){
		 	$username = Ihc_Db::get_username_by_wpuid($uid);
			$parent = (self::$metas['ihc_individual_page_parent']==-1) ? 0 : self::$metas['ihc_individual_page_parent'];
			$content = stripslashes(self::$metas['ihc_individual_page_default_content']);
			$content = apply_filters('ihc_insert_individual_page_content', $content, $uid);
			$post_id = wp_insert_post(
									array(
											'post_content' => $content,
											'post_parent' => $parent,
											'post_type' => 'page',
											'post_status' => 'publish',
											'post_title' => 'IUMP Individual Page: ' . $username,
									)										
			);
			if ($post_id){
				add_post_meta($post_id, 'ihc_individual_page', $uid);
				update_user_meta($uid, 'ihc_individual_page', $post_id);
			}
		 }
		 return $post_id;
	}
	
	public function generate_pages_for_users($users=array()){
		/*
		 * @param array
		 * @return boolean
		 */
		 if ($users){
		 	foreach ($users as $uid){
		 		$this->generate_page_for_user($uid);
		 	}
			return TRUE;
		 }
		 return FALSE;
	}
	
}
	
endif;
