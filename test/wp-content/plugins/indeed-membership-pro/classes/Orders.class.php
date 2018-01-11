<?php 
namespace Ump{
	class Orders{
		
		public function __construct(){}
		
		public function do_insert($data=array(), $automated_payment=1){
			/*
			 * @param array, int (1 - simple payment, 2 - reccuring)
			 * @return int
			 */
			$data = apply_filters('ihc_order_insert_data', $data);
			if (!empty($data['uid']) && isset($data['lid']) && isset($data['amount'])){
				if (empty($data['status'])){
					$data['status'] = 'pending';
				}
				global $wpdb;
				$table = $wpdb->prefix . 'ihc_orders';				
				$q = $wpdb->prepare("INSERT INTO $table VALUES (null, %d, %d, %s, %s, %d, %s, NOW());", 
											$data['uid'], $data['lid'], $data['amount_type'], $data['amount'], $automated_payment, $data['status']);
				$wpdb->query($q);
				$id = $wpdb->insert_id;
				do_action('ump_payment_check', $id, 'insert');
				
				/// SAVE METAS
				if (isset($data['txn_id'])){
					\Ihc_Db::save_udate_order_meta($id, 'txn_id', $data['txn_id']);					
				}
				if (isset($data['ihc_payment_type'])){
					\Ihc_Db::save_udate_order_meta($id, 'ihc_payment_type', $data['ihc_payment_type']);					
				}
				
				if (!empty($data['extra_fields'])){
					if (!empty($data['extra_fields']['is_trial'])){
						\Ihc_Db::save_udate_order_meta($id, 'is_trial', TRUE);		
					}
				}
				
				///TAX VALUE
				if (!empty($data['extra_fields'])){
					if (!empty($data['extra_fields']['tax_value'])){
						\Ihc_Db::save_udate_order_meta($id, 'tax_value', $data['extra_fields']['tax_value']);		
					}
				}

				///DISCOUNT VALUE
				if (!empty($data['extra_fields'])){
					if (!empty($data['extra_fields']['discount_value'])){
						\Ihc_Db::save_udate_order_meta($id, 'discount_value', $data['extra_fields']['discount_value']);		
					}
				}
				
				
				///only for authorize recurring
				if (!empty($data['extra_fields']['txn_id'])){
					/// update transactions
					$this->update_transaction_table($data['extra_fields']['txn_id'], $id);
					/// update order metas
					\Ihc_Db::save_udate_order_meta($id, 'txn_id', $data['extra_fields']['txn_id']);							
				}
				
				
				// INSERT ORDER INVOICE CODE
				$prefix = get_option('ihc_order_prefix_code');
				if (empty($prefix)){
					$prefix = 'iump';
				}
				$the_code = $id;
				while (strlen($the_code)<6){
					$the_code = '0' . $the_code;
				}
				$the_code = $prefix . $the_code;
				\Ihc_Db::save_udate_order_meta($id, 'code', $the_code);
					
				///Wp Admin Dashboard Notification
				\Ihc_Db::increment_dashboard_notification('orders');
					
				do_action('ihc_action_after_order_placed', @$data['uid'], @$data['lid']);	
								
				return $id;
			}
		}
		
		public function do_insert_update($txn_id=0){
			/*
			 * @param int
			 * @return none
			 */
			 
			if ($txn_id){
				require_once IHC_PATH . 'classes/Transactions.class.php';
				$object = new Transactions($txn_id);
				$data = $object->get_data();
				
				global $wpdb;
				$table = $wpdb->prefix . 'ihc_orders';

				/// SEARCH BY AMOUNT LEVEL AND UID		
				$q = $wpdb->prepare("SELECT * FROM $table
											WHERE
											uid=%d
											AND lid=%d
											AND amount_value=%s
											AND status='pending'
											ORDER BY create_date DESC
											LIMIT 1
				", $data['uid'], $data['lid'], $data['amount']);
				$query_result = $wpdb->get_row($q);
				if (!empty($query_result) && !empty($query_result->id)){
					$order_id = @$query_result->id;									
				}		
				
				if (empty($order_id)){
					/****************** INSERT **************/
					$automated_payment = ($this->is_recuring_payment($data)) ? 2 : 1;/// CHECK if it's reccuring payment
					$the_id = $this->do_insert($data, $automated_payment);
				} else {
					/***************** SIMPLE UPDATE **************/
					if (!empty($data['status'])){
						$the_id = $order_id;
						$q = $wpdb->prepare("UPDATE $table SET status=%s WHERE id=%d", $data['status'], $the_id);
						$wpdb->query($q);
						do_action('ump_payment_check', $the_id, 'update');						
					}
				}	

				if (!empty($the_id)){
					/// update transactions
					$this->update_transaction_table($txn_id, $the_id);
					/// update order metas
					\Ihc_Db::save_udate_order_meta($the_id, 'txn_id', $txn_id);							
				}
			}			
		}
		
		public function get_data($order_id=0){
			/*
			 * @param none
			 * @return array
			 */
			if ($order_id){
				global $wpdb;
				$table = $wpdb->prefix . 'ihc_orders';
				$data = $wpdb->get_row("SELECT * FROM $table WHERE id='" . $order_id . "';");
				if (!empty($data)){
					return (array)$data;
				} else {
					return array();
				}
			}
		}
		
		private function update_transaction_table($txn_id='', $id=0){
			/*
			 * @param string, int
			 * @return none
			 */
			if ($txn_id && $id){
				global $wpdb;
				$table = $wpdb->prefix . 'indeed_members_payments';
				$data = $wpdb->get_row("SELECT orders FROM $table WHERE txn_id='$txn_id';");
				if ($data && !empty($data->orders)){
					@$ids = unserialize($data->orders);
				}
				$ids[] = (int)$id;
				$ids = serialize($ids);
				$made = $wpdb->query("UPDATE $table SET orders='$ids' WHERE txn_id='$txn_id';");				
			}
		}
		
		private function is_recuring_payment($data=array()){
			/*
			 * @param array
			 * @return boolean
			 */
			global $wpdb;
			$table = $wpdb->prefix . 'ihc_orders';
			$q = $wpdb->prepare("SELECT id FROM $table
									WHERE
									uid=%d
									AND lid=%d
									AND automated_payment=1
									AND status='Completed'
									", $data['uid'], $data['lid']
			);
			$query_result = $wpdb->get_row($q);
			if (isset($query_result->id)){
				return TRUE;
			}
			return FALSE;
		}
		
		
		public function get_metas($order_id=0){
			/*
			 * @param int
			 * @return array
			 */
			if ($order_id){
				global $wpdb;
				$table = $wpdb->prefix . 'ihc_orders_meta';
				$data = $wpdb->get_results("SELECT meta_key, meta_value FROM $table WHERE order_id='$order_id';");
				if (!empty($data)){
					$array = array();
					foreach ($data as $object){
						$array[$object->meta_key] = $object->meta_value;
					}
					return $array;
				} else {
					return array();
				}
			}			 
		}		
				
	}
}