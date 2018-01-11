<?php
if (!class_exists('Ihc_Custom_Woo_Endpoint')):
	 
class Ihc_Custom_Woo_Endpoint{
	private $name = 'ihc';
	private $metas = array();
	
	public function __construct(){
		/*
		 * @param none
		 * @return none
		 */			
		$this->metas = ihc_return_meta_arr('ihc_woo');//getting metas
		if (empty($this->metas['ihc_woo_account_page_enable'])){
			return;
		}
		//add_action('init',array( $this, 'flush_rules'));
		add_action('init', array( $this, 'add_endpoints'));	
		
		//add_filter('query_vars', array( $this, 'add_query_vars'), 0); 			
		add_filter('the_title', array( $this, 'endpoint_title' ));
		add_filter('woocommerce_account_menu_items', array($this, 'new_menu_items'));
		add_action('woocommerce_account_ihc_endpoint', array($this, 'content'));
	}

	public function add_endpoints(){
		/*
		 * @param none
		 * @return none
		 */		
		add_rewrite_endpoint($this->name, EP_ROOT | EP_PAGES );
	}
	
	public function flush_rules(){
		/*
		 * @param none
		 * @return none
		 */		
		flush_rewrite_rules();
	}
	
	public function add_query_vars($vars=array()){
		/*
		 * @param array
		 * @return array
		 */			
		$vars[] = $this->name;
		return $vars;
	}

	public function endpoint_title($title=''){
		/*
		 * @param string
		 * @return string
		 */			
		global $wp_query;	
		if (isset($wp_query->query_vars[$this->name]) && ! is_admin() && is_main_query() && in_the_loop() && is_account_page()){
			$title = $this->metas['ihc_woo_account_page_name'];
		}
		return $title;
	}

	public function new_menu_items($items=array()){
		/*
		 * @param array
		 * @return array
		 */
		 if ($this->name && isset($this->metas['ihc_woo_account_page_name']) ){
		 	 $position = $this->metas['ihc_woo_account_page_menu_position'];
		 	 $reorder[$position] = array($this->name, $this->metas['ihc_woo_account_page_name']);
			 
			 $i = 1;
			 foreach ($items as $key=>$value){
			 	 while (isset($reorder[$i])){
			 	 	$i++;
			 	 }
				 $reorder[$i] = array($key, $value);
			 }

			 ksort($reorder);
			 $return_array = array();
			 foreach ($reorder as $array){
			 	if (isset($array[0]) && isset($array[1])){
				 	$return_array[$array[0]] = $array[1];		 		
			 	}
			 }
			 return $return_array;		 	
		 }
		return $items;
	}

	public function content(){
		/*
		 * @param none
		 * @return string
		 */		
		echo do_shortcode('[ihc-user-page]');		 	
	}
	
}

endif;

