<?php
if (!class_exists('Ihc_Gifts')):
	
class Ihc_Gifts{
	
	public function __construct($uid=0, $lid=0){
		/*
		 * @param none
		 * @return none
		 */
		 if ($lid!='' && $uid){
		 	 $gifts_available = Ihc_Db::gift_get_all_items($lid);
			 if ($gifts_available){
			 	$user_meta_value = get_user_meta($uid, 'ihc_gifts', TRUE);
				
			 	if (ihc_is_level_reccuring($lid) && $user_meta_value){
			 		/// extra check
			 		if (ihc_array_value_exists($user_meta_value, $lid, 'lid')){
			 			/// for this level user already got one gift
			 			$check = ihc_return_meta_arr('gifts');
			 			if (empty($check['ihc_gifts_user_get_multiple_on_recurring'])){
			 				return;
			 			}
			 		}
			 	}
				 
			 	foreach ($gifts_available as $gift_id => $gift_metas){
			 		$code = $this->generate_coupon_code($gift_metas, $uid);
					if ($code){
						///store gift code into user meta
						$temp = array(
										'code' => $code,
										'lid' => $lid,
										'gift_id' => $gift_id,
						);
						$user_meta_value[] = $temp;
					}
			 	}
				update_user_meta($uid, 'ihc_gifts', $user_meta_value);
			 }
		 }
	}
	
	private function generate_coupon_code($meta=array(), $uid){
		/*
		 * @param array, int
		 * @return string
		 */
		 $code = ihc_random_str(10);
		 $data = array(
		 				'code' => $code,
		 				'description' => __('Gift', 'ihc'),
		 				'period_type' => 'unlimited',
		 				'discount_type' => $meta['discount_type'],
		 				'discount_value' => $meta['discount_value'],
		 				'repeat' => 1,
		 				'target_level' => $meta['target_level'],
		 				'reccuring' => $meta['reccuring'],
		 				'special_status' => 2, /// set the status at 2 (gift code)
		 				'uid' => $uid,
		 );
		 ihc_create_coupon($data);
		 return $code;
	}
	
}	
	
endif;
