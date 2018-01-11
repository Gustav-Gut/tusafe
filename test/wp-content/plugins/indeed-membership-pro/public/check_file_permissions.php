<?php 
require_once '../../../../wp-load.php';
require_once '../utilities.php';

$user_type = ihc_get_user_type();
$current_url = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; /// $_SERVER['SERVER_NAME'] 

if ($user_type=='admin'){
	//admin can view anything
	$show = TRUE;
}
if (strpos($current_url, 'indeed-membership-pro')!==FALSE){
	/// links inside plugin must work everytime!
	$show = TRUE;
}

$block_data = get_option('ihc_block_files_by_url');
if (empty($show) && !empty($block_data)){
	foreach ($block_data as $key=>$array){
		if ($current_url==$array['file_url']){
			/// TARGET USERS
			$target_users = FALSE;
			if (!empty($array['target_users']) && $array['target_users']!=-1){
				$target_users = explode(',', $array['target_users']);
			}
			
			$block_or_show = (isset($array['block_or_show'])) ? $array['block_or_show'] : 'block';
			
			$block = ihc_test_if_must_block($block_or_show, $user_type, $target_users, '');//test if user must be block			
			if ($block){
				if (!empty($array['redirect'])){
					$redirect_link = get_permalink($array['redirect']);
				}
				if (empty($redirect_link)){
					$redirect_link = get_home_url();	
				}
				break;
			}									
		}
	}
}

if (!empty($redirect_link)){
	/// REDIRECT
	wp_redirect($redirect_link);
	exit();
} else {
	/// SHOW 
	$base_url = home_url();
	$file = str_replace($base_url, '', $current_url);
	if (strpos($file, '/')===0){
		$file = substr($file, 1); /// remove first '/' from path
	}
	$file = ABSPATH . $file;
	$file = urldecode($file);
	if (file_exists($file)){
		$data = file_get_contents($file);
		$file_info = wp_check_filetype($file);
		if (!empty($data) && $file_info && !empty($file_info['type'])){
			header('Content-Type: ' . $file_info['type'] );
			echo $data;
			exit();			
		}		
	}
}


/// just for safe redirect home if something goes wrong
$redirect_link = get_home_url();	
wp_redirect($redirect_link);
exit();
