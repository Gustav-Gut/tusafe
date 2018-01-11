<?php
if (!class_exists('Ihc_Custom_BP_Endpoint.class.php')):
	
	class Ihc_Custom_BP_Endpoint{
		private $metas = array();
		
		public function __construct(){
			/*
			 * ALL THE ACTIONS & FILTERS
			 * @param none
			 * @return none
			 */
			$this->metas = ihc_return_meta_arr('ihc_bp');//getting metas	 
			if (empty($this->metas['ihc_bp_account_page_enable'])){
				return;
			}			 			
			add_action('bp_setup_nav', array($this, 'do_setup_bp_nav'), 99);
		}
		
		public function do_setup_bp_nav(){
			/*
			 * @param 
			 * @return
			 */
			global $current_user;			
			if (empty($current_user) || empty($current_user->ID)){
				return;
			}
						 
			global $bp;

			bp_core_new_nav_item( array(
					'name' => $this->metas['ihc_bp_account_page_name'],
					'slug' => 'ihc',
					'position' => $this->metas['ihc_bp_account_page_position'],
					'show_for_displayed_user' => false,
					'screen_function' => 'ihc_bp_content_action',
					'item_css_id' => 'ihc',
					'default_subnav_slug' => 'ihc'
				) 
			);
			bp_core_new_subnav_item( array(
					'name' => __('Ultimate Membership Pro', 'ihc'),
					'slug' => 'ihc',
					'show_for_displayed_user' => false, 
					'parent_url' => trailingslashit( bp_displayed_user_domain() . 'ihc'),
					'parent_slug' => 'ihc',
					'position' => $this->metas['ihc_bp_account_page_position'],
					'screen_function' => array($this, 'ihc_bp_content_action'),
					'item_css_id' => 'ihc',
					'user_has_access' => bp_is_my_profile()					
				)
			);		 
		}
		
		
		public function ihc_bp_content_action(){
			/*
			 * @param none
			 * @return none
			 */
			 add_action('bp_template_content', array($this, 'ihc_bp_do_the_content'));
			 bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
		}
		
		public function ihc_bp_do_the_content(){
			/*
			 * @param none
			 * @return 
			 */
			echo do_shortcode('[ihc-user-page]');
		}
		
	}
	
endif;
