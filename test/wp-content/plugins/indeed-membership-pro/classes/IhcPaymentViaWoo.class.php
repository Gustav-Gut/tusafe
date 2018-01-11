<?php
if (!class_exists('IhcPaymentViaWoo')):
	
class IhcPaymentViaWoo{
	
	public function __construct(){
		/*
		 * @param none
		 * @return none
		 */
		if (!ihc_is_magic_feat_active('woo_payment')){
			return; /// OUT 
		}
		
		/// ADMIN
		add_filter('woocommerce_product_data_tabs', array($this, 'add_product_tab'));
		add_action('woocommerce_product_data_panels', array($this, 'product_tab_html')); 
		add_action('woocommerce_process_product_meta_simple', array($this, 'admin_save_iump'));
		add_action('woocommerce_process_product_meta_grouped', array($this, 'admin_save_iump'));
		add_action('woocommerce_process_product_meta_external', array($this, 'admin_save_iump'));
		add_action('woocommerce_process_product_meta_variable', array($this, 'admin_save_iump'));
		
		/// PUBLIC
		add_action('woocommerce_checkout_order_processed', array($this, 'create_order')); /// INSERT ORDER, LEVEL
		add_action('woocommerce_order_status_completed', array($this, 'make_level_active')); /// ACTIVATE LEVEL
		/// MAKE LEVEL EXPIRE
		add_action('woocommerce_order_status_pending_to_cancelled', array($this, 'make_level_expire'));
		add_action('woocommerce_order_status_pending_to_failed', array($this, 'make_level_expire'));
		add_action('woocommerce_order_status_completed_to_refunded', array($this, 'make_level_expire'));
		add_action('woocommerce_order_status_completed_to_cancelled', array($this, 'make_level_expire'));
		add_action('woocommerce_order_status_processing_to_refunded', array($this, 'make_level_expire'));
		add_action('woocommerce_order_status_processing_to_cancelled', array($this, 'make_level_expire'));	
		add_action('woocommerce_order_status_on-hold_to_refunded', array($this, 'make_level_expire'));
		add_action('wc-on-hold_to_trash', array($this, 'make_level_expire'));
		add_action('wc-processing_to_trash', array($this, 'make_level_expire'));
		add_action('wc-completed_to_trash', array($this, 'make_level_expire'));
	}
	
	public function add_product_tab($product_tabs=array()){
		/*
		 * @param array
		 * @return array
		 */
		$product_tabs['iump'] = array(
									'label'  => __('Ultimate Membership Pro Options', 'ihc'),
									'target' => 'iump_options',
									'class'  => array('hide_if_grouped'),
		);
		return $product_tabs;
	}
	
	public function product_tab_html(){
		/*
		 * @param none
		 * @return string
		 */
		global $woocommerce, $post;
		$levels = get_option('ihc_levels');
		@$current_value = get_post_meta(@$post->ID, 'iump_woo_product_level_relation', TRUE);
		?>
		<div id="iump_options" class="panel woocommerce_options_panel options_group" >
			<p><?php _e('Link this product to a Ultimate Membership Pro Level', 'ihc');?></p>
			<p class="form-field">
			<label><?php _e('Target Level', 'ihc');?></label>
			<select name="iump_woo_product_level_relation">
				<option value="-1" selected><?php _e('None', 'ihc');?></option>
				<?php 
				foreach ($levels as $id => $level):?>
					<?php $selected = ($current_value==$id) ? 'selected' : '';?>
				<option value="<?php echo $id;?>" <?php echo $selected;?> ><?php echo $level['label'];?></option>
				<?
				endforeach;	
			?></select>
			</p>
		</div>
		<?php
	}
	
	public function admin_save_iump($post_id=0){
		/*
		 * @param int
		 * @return none
		 */
		 if ($post_id && isset($_POST['iump_woo_product_level_relation']) && $_POST['iump_woo_product_level_relation']!=-1){
		 	update_post_meta($post_id, 'iump_woo_product_level_relation', $_POST['iump_woo_product_level_relation']);
		 }
	}

	public function create_order($order_id=0){
		/*
		 * @param int
		 * @return none
		 */
		 if ($order_id){
		 	$order = new WC_Order($order_id);
			$items = $order->get_items();
			$amount = 0;
			$extra_order_info = array();
			$uid = $order->get_user_id();
			$total_discount = $order->get_total_discount();
			$total_amount = $order->get_total();
			
			if ($uid){
			 	foreach ($items as $item){
			 		$lid = get_post_meta($item['product_id'], 'iump_woo_product_level_relation', TRUE);
			 		if ($lid!==FALSE && $lid!=-1 && $lid!=''){
						$amount = round($item['line_total'], 3);
						if (!empty($item['line_tax'])){
							$extra_order_info['tax_value'] = round($item['line_tax'], 3);
							$amount += $extra_order_info['tax_value'];
						}						
						$extra_order_info['txn_id'] = 'woocommerce_order_' . $order_id . '_' . $lid;
						if (!empty($total_discount)){
							$level_discount = $this->calculate_discount_per_level($total_discount, $total_amount, $amount);
							if ($level_discount){
								$extra_order_info['discount_value'] = $level_discount;
							}
						}
						
						/// save user id, level id relation
						if (Ihc_Db::user_has_level($uid, $lid)){
							if (!Ihc_Db::is_user_level_active($uid, $lid)){
								ihc_do_complete_level_assign_from_ap($uid, $lid);
							}
							/// else = user already has this level and it's active
						} else {						
							ihc_do_complete_level_assign_from_ap($uid, $lid);						
						}
						///ORDER
						ihc_insert_update_order($uid, $lid, $amount, 'pending', 'woocommerce', $extra_order_info);
			 		}
			 	}
			}
		 }
	}

	public function make_level_active($order_id=0){
		/*
		 * @param int
		 * @return none
		 */	
		if ($order_id){
		 	$order = new WC_Order($order_id);
			$items = $order->get_items();	
			$uid = $order->get_user_id();
			
			if ($uid){
			 	foreach ($items as $item){
			 		$lid = get_post_meta($item['product_id'], 'iump_woo_product_level_relation', TRUE);
			 		if ($lid!==FALSE && $lid!=-1 && $lid!=''){
			 			$product_id = $item['product_id'];					
						$level_data = ihc_get_level_by_id($lid);					
						ihc_update_user_level_expire($level_data, $lid, $uid);
						ihc_send_user_notifications($uid, 'payment', $lid);//send notification to user
						ihc_send_user_notifications($uid, 'admin_user_payment', $lid);//send notification to admin
						ihc_switch_role_for_user($uid);	
						$txn_id = 'woocommerce_order_' . $order_id . '_' . $lid;
						$ihc_order_id = Ihc_Db::get_order_id_by_meta_value_and_meta_type('txn_id', $txn_id);
						if ($ihc_order_id){
							$data = Ihc_Db::get_order_data_by_id($ihc_order_id);
							$array = array(
											'txn_id' => $txn_id,
											'uid' => $data['uid'],
											'level' => $data['lid'],
											'order_id' => $ihc_order_id,
											'amount' => $data['amount_value'],
											'currency' => $data['amount_type'],
											'ihc_payment_type' => 'woocommerce',
											'message' => 'success',
											'details' => '',
							);					
							ihc_insert_update_transaction($uid, $txn_id, $array);									
						}
			 		}
			 	}				
			}				
		}		 	
	}

	public function make_level_expire($order_id=0){
		/*
		 * @param int
		 * @return none
		 */
		if ($order_id){
		 	$order = new WC_Order($order_id);
			$items = $order->get_items();	
			$uid = $order->get_user_id();			
			if ($uid){
			 	foreach ($items as $item){
			 		$lid = get_post_meta($item['product_id'], 'iump_woo_product_level_relation', TRUE);
			 		if ($lid!==FALSE && $lid!=-1 && $lid!=''){
			 			$product_id = $item['product_id'];					
						$new_status = $order->get_status();
						$txn_id = 'woocommerce_order_' . $order_id . '_' . $lid;	
						$ihc_order_id = Ihc_Db::get_order_id_by_meta_value_and_meta_type('txn_id', $txn_id);
						Ihc_Db::update_order_status($ihc_order_id, $new_status);
						Ihc_Db::update_transaction_status($txn_id, $new_status);
						ihc_make_level_expire_for_user($uid, $lid);
			 		}
			 	}				
			}				
		}		 
	}
	
	private function calculate_discount_per_level($total_discount=0, $total_amount=0, $level_amount=0){
		/*
		 * @param float, float
		 * @return float
		 */
		 if ($total_discount && $total_amount && $level_amount){
		 	$discount_percent_per_level = 100 * $level_amount / $total_amount;
			if ($discount_percent_per_level){
				$discount_per_level = $discount_percent_per_level * $total_discount / 100;
				if ($discount_per_level){
					return round($discount_per_level, 2);
				}
			}
		 }
		 return 0;
	}
	
}
	
endif;
