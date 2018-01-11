<?php 
namespace Ump{
	class Transactions{
		private $transaction_id;
		private $transaction_data;
		
		public function __construct($transaction_id=''){
			/*
			 * @param string
			 * @return none
			 */
			if ($transaction_id){
				$this->transaction_id = $transaction_id;
			}
		}
		
		public function get_data(){
			/*
			 * @param none
			 * @return array
			 */
			if ($this->transaction_id){
				///getting data from db
				global $wpdb;
				$table = $wpdb->prefix . 'indeed_members_payments';
				$q = $wpdb->prepare("SELECT * FROM $table WHERE txn_id=%s", $this->transaction_id);
				$data = $wpdb->get_row($q);
				if ($data){					
					$this->transaction_data = json_decode($data->payment_data, TRUE);
					$array['amount'] = $this->get_transaction_amount_value();
					$array['amount_type'] = $this->get_transaction_amount_type();
					$array['status'] = $this->get_transaction_status();
					$array['date'] = $data->paydate;
					$array['uid'] = $data->u_id;
					$array['lid'] = $this->get_level_id();
					$array['transaction_id'] = $data->id;
					$array['payment_type'] = (empty($this->transaction_data['ihc_payment_type'])) ? '' : $this->transaction_data['ihc_payment_type'];
					return $array;
				}
			} else {
				return array();
			}
		}
		
		private function get_level_id(){
			/*
			 * @param none
			 * @return int
			 */
			if (isset($this->transaction_data['level'])){
				return $this->transaction_data['level'];
			} else if (isset($this->transaction_data['custom'])){
				$data = stripslashes($this->transaction_data['custom']);
				$data = json_decode($data, true);
				return (empty($data['level_id'])) ? '' : $data['level_id'];
			} else if (isset($this->transaction_data['x_po_num'])){
				return $this->transaction_data['x_po_num'];
			} else if (isset($this->transaction_data['lid'])){
				return $this->transaction_data['lid'];
			}
			return '';
		}
		
		private function get_transaction_amount_value(){
			/*
			 * @param none
			 * @return number
			 */
			if (isset($this->transaction_data['mc_gross'])){
				return $this->transaction_data['mc_gross'];
			} else if (isset($this->transaction_data['x_amount'])){
				return $this->transaction_data['x_amount'];				
			} else if (isset($this->transaction_data['amount'])){
				return $this->transaction_data['amount'];
			} else if(isset($this->transaction_data['total'])){
				return $this->transaction_data['total'];
			}
			return 0;
		}
		
		private function get_transaction_amount_type(){
			/*
			 * @param none
			 * @return string
			 */
			if (isset($this->transaction_data['mc_currency'])){
				return $this->transaction_data['mc_currency'];
			} else if (isset($this->transaction_data['x_currency_code'])){
				return $this->transaction_data['x_currency_code'];
			} else if (isset($this->transaction_data['currency'])){
				return $this->transaction_data['currency'];
			} else if(isset($this->transaction_data['currency_code'])){
				return $this->transaction_data['currency_code'];
			}
			return '';
		}
		
		private function get_transaction_status(){
			/*
			 * @param none
			 * @return string
			 */
			if (!empty($this->transaction_data['payment_status'])){
				return $this->transaction_data['payment_status'];
			} else if (isset($this->transaction_data['x_response_code']) && ($this->transaction_data['x_response_code']==1)){
				return "Completed";
			} else if (isset($this->transaction_data['code']) && ($this->transaction_data['code']== 2)){
				return "Completed";
			} else if(isset($this->transaction_data['message']) && $this->transaction_data['message']=='success'){
				return "Completed";
			} else if(isset($this->transaction_data['ap_status']) && ($this->transaction_data['ap_status']=='Success' || $this->transaction_data['ap_status']=='Subscription-Payment-Success')){
				/// PAYZA
				return "Completed";
			}
			return '';
		}
		
	}
}
