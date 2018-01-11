<?php 
if (empty($ext_menu) || !isset($ext_menu)){
	$ind_menu = '';
}else{
	$ind_menu = $ext_menu;
}
unset($ext_menu);
require_once 'IndeedFeedSystem.class.php';
$obj = new IndeedFeedSystem($ind_menu);