<?php
/*
Plugin Name: Indeed Ultimate Membership Pro
Plugin URI: http://www.wpindeed.com/
Description: The most complete and easy to use Membership Plugin, ready to allow or restrict your content, Page for certain Users.
Version: 4.9
Author: indeed
Author URI: http://www.wpindeed.com
*/
///setting the paths
if (!defined('IHC_PATH')){
    define('IHC_PATH', plugin_dir_path(__FILE__));
}
if (!defined('IHC_URL')){
    define('IHC_URL', plugin_dir_url(__FILE__));
}
if (!defined('IHC_PROTOCOL')){
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&  $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
        define('IHC_PROTOCOL', 'https://');
    } else {
        define('IHC_PROTOCOL', 'http://');    
    }
}

update_option('ihc_license_set', 1);
update_option('ihc_envato_code', '2b35dec6-5674-314f-5d8d-9fe900dc03b8');

//LANGUAGES
add_action('init', 'ihc_load_language');
function ihc_load_language(){
    load_plugin_textdomain( 'ihc', false, dirname(plugin_basename(__FILE__)).'/languages/' );
}

require_once IHC_PATH . 'utilities.php';
require_once IHC_PATH . 'classes/Ihc_Db.class.php';
if (is_admin()){
    //go to admin  
    require_once IHC_PATH . 'admin/main.php';
} else {
    //go to public
    require_once IHC_PATH . 'public/main.php';
}
require_once IHC_PATH  . 'public/functions/ihc_countries.php';

/// Woo payment integration
require_once IHC_PATH . 'classes/IhcPaymentViaWoo.class.php';
$IhcPaymentViaWoo = new IhcPaymentViaWoo();


//on activating the plugin
function ihc_initiate_plugin(){
    /*
     * @param none
     * @return none
     */
    
    /// IF PHP >5.3 don't activate plugin
    if (defined('PHP_VERSION') && version_compare(PHP_VERSION, 5.3, '<')){
        deactivate_plugins(plugin_basename( __FILE__ ));
        die('Ultimate Membership Pro requires PHP version greater than 5.3, Your current PHP is v.' . PHP_VERSION . ' . Update Your PHP and try again!');
    }
    
    require_once IHC_PATH . 'classes/Ihc_Db.class.php';
    Ihc_Db::add_new_role();
    Ihc_Db::save_settings_into_db();
    Ihc_Db::create_tables();        
    Ihc_Db::create_notifications();
    Ihc_Db::create_default_pages();
    Ihc_Db::create_default_redirects();
    Ihc_Db::create_extra_redirects();    
    Ihc_Db::create_default_lockers();
    Ihc_Db::create_demo_levels();
}
register_activation_hook( __FILE__, 'ihc_initiate_plugin' );

add_action('init', 'ihc_check_plugin_version');
function ihc_check_plugin_version(){
    /*
     * @param none
     * @return none
     */
    $check = get_option('ihc_license_set');
    if ($check!==FALSE){
        if ($check==0)
            define('IHCACTIVATEDMODE', false);
        else
            define('IHCACTIVATEDMODE', true);
    } else {
        define('IHCACTIVATEDMODE', true);
    }
}

function ihc_admin_global_notice(){
    if (current_user_can('manage_options')){
        echo ihc_inside_dashboard_error_license(TRUE);    
    }
}
add_action('admin_notices', 'ihc_admin_global_notice');

function ihc_send_notification_before_after_expire(){
    /*
     * @param none
     * @return none
     */
    global $wpdb;
    /// FIRST BEFORE EXPIRE
    $before_expire = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "ihc_notifications` WHERE `notification_type`='before_expire' ORDER BY id DESC LIMIT 1;");
    if ($before_expire){
        //we have notification before expire
        $days = get_option("ihc_notification_before_time");
        if (!$days){
            $days = 5;
        }
        $time_diff = $days*24*60*60;
        $u_ids = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."ihc_user_levels`
                                        WHERE 1=1
                                        AND notification=0
                                        AND UNIX_TIMESTAMP(expire_time)<(UNIX_TIMESTAMP(NOW())+".$time_diff.")
        ;");        
        if ($u_ids){
            foreach ($u_ids as $u_data){
                $sent = ihc_send_user_notifications($u_data->user_id, 'before_expire', $u_data->level_id);
                ihc_send_user_notifications($u_data->user_id, 'admin_before_user_expire_level', $u_data->level_id);/// SEND NOTIFICATION TO ADMIN                
                if ($sent){
                    $wpdb->query("UPDATE `".$wpdb->prefix."ihc_user_levels` SET notification='1' WHERE `id`='".$u_data->id."'; ");
                }
            }
        }
    }    
    
    /// SECOND BEFORE EXPIRE
    $before_expire = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "ihc_notifications` WHERE `notification_type`='second_before_expire' ORDER BY id DESC LIMIT 1;");
    if ($before_expire){
        //we have notification before expire
        $days = get_option("ihc_notification_before_time");
        if (!$days){
            $days = 3;
        }
        $time_diff = $days*24*60*60;
        $table = $wpdb->prefix . "ihc_user_levels";
        $u_ids = $wpdb->get_results("SELECT * FROM $table
                                        WHERE 1=1
                                        AND notification=1
                                        AND UNIX_TIMESTAMP(expire_time)<(UNIX_TIMESTAMP(NOW())+".$time_diff.")
        ;");        
        if ($u_ids){
            foreach ($u_ids as $u_data){
                $uid = $u_data->user_id;
                $sent = ihc_send_user_notifications($uid, 'second_before_expire', $u_data->level_id);    
                ihc_send_user_notifications($uid, 'admin_second_before_user_expire_level', $u_data->level_id);            
                if ($sent){
                    $wpdb->query("UPDATE $table SET notification='-1' WHERE id='$uid';");
                }
            }
        }
    }    
    
    /// THIRD BEFORE EXPIRE
    $before_expire = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "ihc_notifications` WHERE `notification_type`='third_before_expire' ORDER BY id DESC LIMIT 1;");
    if ($before_expire){
        //we have notification before expire
        $days = get_option("ihc_notification_before_time");
        if (!$days){
            $days = 1;
        }
        $time_diff = $days*24*60*60;
        $table = $wpdb->prefix . "ihc_user_levels";
        $u_ids = $wpdb->get_results("SELECT * FROM $table
                                        WHERE 1=1
                                        AND notification=-1
                                        AND UNIX_TIMESTAMP(expire_time)<(UNIX_TIMESTAMP(NOW())+".$time_diff.")
        ;");        
        if ($u_ids){
            foreach ($u_ids as $u_data){
                $uid = $u_data->user_id;
                $sent = ihc_send_user_notifications($uid, 'third_before_expire', $u_data->level_id);    
                ihc_send_user_notifications($uid, 'admin_third_before_user_expire_level', $u_data->level_id);            
                if ($sent){
                    $wpdb->query("UPDATE $table SET notification='-2' WHERE id='$uid';");
                }
            }
        }
    }
    
    $expire = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "ihc_notifications` WHERE `notification_type`='expire' ORDER BY id DESC LIMIT 1;");    
    if ($expire){
        $u_ids = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."ihc_user_levels`
                                        WHERE 1=1
                                        AND notification<>2
                                        AND DATE(expire_time)=DATE(NOW())
                                    ;");
        if ($u_ids){
            foreach ($u_ids as $u_data){
                $sent = ihc_send_user_notifications($u_data->user_id, 'expire', $u_data->level_id);
                ihc_send_user_notifications($u_data->user_id, 'admin_user_expire_level', $u_data->level_id);/// SEND NOTIFICATION TO ADMIN    
                
                if ($sent){                                    
                    $wpdb->query("UPDATE `".$wpdb->prefix."ihc_user_levels` SET notification='2' WHERE `id`='".$u_data->id."'; ");
                }
            }
        }        
    }
}

add_action( 'ihc_notifications_job', 'ihc_send_notification_before_after_expire', 82 );


////downgrade level
function ihc_check_if_level_expire_downgrade(){
    /*
     * main function for "add another level after expire current level"
     * @param none
     * @return none
     */
    global $wpdb;
    $grace_period = get_option('ihc_grace_period');
    $q = "SELECT * FROM `" . $wpdb->prefix . "ihc_user_levels`
            WHERE 1=1
            AND DATE(expire_time)<=DATE(NOW())
            AND DATE(expire_time)>DATE('0000-00-00 00:00:00')";
    $u_ids = $wpdb->get_results($q);    
    if ($u_ids){
        foreach ($u_ids as $u_data){
            if ($grace_period){
                $expire_time_after_grace = strtotime($u_data->expire_time) + $grace_period * 24 * 60 * 60;
                if ($expire_time_after_grace>time()){
                    continue;
                }
            }
            if (isset($u_data->level_id) && isset($u_data->user_id)){
                $added = ihc_downgrade_levels_when_expire($u_data->user_id, $u_data->level_id);
                if ($added){
                    //ihc_delete_user_level_relation($u_data->level_id, $u_data->user_id);//remove the older level
                }
            }
        }
    }
}
add_action( 'ihc_check_level_downgrade', 'ihc_check_if_level_expire_downgrade', 83);

function ihc_run_check_verify_email_status(){
    /*
     * Search for users that not verified their email address, and delete them if it's time.
     * @param none
     * @return none
     */
    $time_limit = (int)get_option('ihc_double_email_delete_user_not_verified');
    if ($time_limit>-1){
        $time_limit = $time_limit * 24 * 60 * 60;
        global $wpdb;
        $data = $wpdb->get_results("SELECT user_id FROM " . $wpdb->prefix . "usermeta
                                        WHERE meta_key='ihc_verification_status'
                                        AND meta_value='-1';");
        if (!empty($data)){        
            foreach ($data as $k=>$v){
                if (!empty($v->user_id)){
                    $time_data = $wpdb->get_row("SELECT user_registered FROM " . $wpdb->prefix . "users
                            WHERE ID='" . $v->user_id . "';");
                    if (!empty($time_data->user_registered)){
                        $time_to_delete = strtotime($time_data->user_registered)+$time_limit;
                        if ( $time_to_delete < time() ){
                            //delete user
                            wp_delete_user( $v->user_id );
                            $wpdb->query("DELETE FROM " . $wpdb->prefix . "ihc_user_levels WHERE user_id='" . $v->user_id . "';");
                            //send notification
                            ihc_send_user_notifications($v->user_id, 'delete_account');
                        }
                    }
                }
            }
        }        
    }
}
add_action( 'ihc_check_verify_email_status', 'ihc_run_check_verify_email_status', 84);

//2checkout ajax ins
add_action('wp_ajax_ihc_twocheckout_ins', 'twocheckout_ins_ihc');
add_action('wp_ajax_nopriv_ihc_twocheckout_ins', 'twocheckout_ins_ihc');
function twocheckout_ins_ihc(){
    require_once IHC_PATH . "twocheckout_ins.php";
    exit;
}


//delete attachment ajax
add_action('wp_ajax_nopriv_ihc_delete_attachment_ajax_action', 'ihc_delete_attachment_ajax_action');
add_action('wp_ajax_ihc_delete_attachment_ajax_action', 'ihc_delete_attachment_ajax_action');
function ihc_delete_attachment_ajax_action(){
    if (!empty($_REQUEST['attachemnt_id'])){
        wp_delete_attachment( $_REQUEST['attachemnt_id'], TRUE );
    }
    if ($_REQUEST['user_id']!=-1 && isset($_REQUEST['field_name'])){
        update_user_meta($_REQUEST['user_id'], $_REQUEST['field_name'], '');
    }
    echo 1;
    die();
}

add_action("wp_ajax_nopriv_ihc_check_coupon_code_via_ajax", "ihc_check_coupon_code_via_ajax");
add_action('wp_ajax_ihc_check_coupon_code_via_ajax', 'ihc_check_coupon_code_via_ajax');
function ihc_check_coupon_code_via_ajax(){
    /*
     * RETURN VALUE AFTER COUPON DISCOUNT AND ADDING TAXES
     * use this only for stripe
     * @param none
     * @return array or int 0
     */
    if (!empty($_REQUEST['code']) && !empty($_REQUEST['lid'])){
        $coupon_data = ihc_check_coupon($_REQUEST['code'], $_REQUEST['lid']);
        if ($coupon_data){
            $level_data = ihc_get_level_by_id($_REQUEST['lid']);
            $reccurence = FALSE;
            if (!empty($level_data['access_type']) && $level_data['access_type']=='regular_period'){
                $reccurence = TRUE;
            }
            if ($level_data['price'] && $coupon_data && (!empty($coupon_data['reccuring']) || !$reccurence) ){
                if ($coupon_data['discount_type']=='percentage'){
                    $price = $level_data['price'] - ($level_data['price']*$coupon_data['discount_value']/100);
                } else {
                    $price = $level_data['price'] - $coupon_data['discount_value'];
                }
                $price = $price * 100;
                $price = round($price, 2);

                echo json_encode(array('price'=>$price));
                die();
            }
        }
    }
    echo 0;
    die();
}

add_filter('send_password_change_email', 'ihc_update_passowrd_filter', 99, 2);
function ihc_update_passowrd_filter($return, $user_data){
    /*
     * send custom e-mail notification when user change his password
     * @param return - boolean, $user_data - array
     * @return boolean
     */
    if (isset($user_data['ID']) && $return){
        $sent_mail = ihc_send_user_notifications($user_data['ID'], 'change_password');
        if ($sent_mail){
            return FALSE;
        }
    }
    return $return;
}


add_action("wp_ajax_nopriv_ihc_check_reg_field_ajax", "ihc_check_reg_field_ajax");
add_action('wp_ajax_ihc_check_reg_field_ajax', 'ihc_check_reg_field_ajax');
function ihc_check_reg_field_ajax(){
    $register_msg = ihc_return_meta_arr('register-msg');
    if (isset($_REQUEST['type']) && isset($_REQUEST['value'])){        
        echo ihc_check_value_field($_REQUEST['type'], $_REQUEST['value'], $_REQUEST['second_value'], $register_msg);
    } else if (isset($_REQUEST['fields_obj'])){
        $arr = $_REQUEST['fields_obj'];
        foreach ($arr as $k=>$v){
            $return_arr[] = array( 'type' => $v['type'], 'value' => ihc_check_value_field($v['type'], $v['value'], $v['second_value'], $register_msg) );
        }
        echo json_encode($return_arr);
    }
    die();
}

function ihc_check_value_field($type='', $value='', $val2='', $register_msg=array()){
    if (isset($value) && $value!=''){
        switch ($type){
            case 'user_login':
                if (!validate_username($value)){
                    $return = $register_msg['ihc_register_error_username_msg'];
                }
                if (username_exists($value)) {
                    $return = $register_msg['ihc_register_username_taken_msg'];
                }
                break;
            case 'user_email':
                if (!is_email($value)) {
                    $return = $register_msg['ihc_register_invalid_email_msg'];
                }
                if (email_exists($value)){
                    $return = $register_msg['ihc_register_email_is_taken_msg'];
                }
                break;
            case 'confirm_email':
                if ($value==$val2){
                    $return = 1;
                } else {
                    $return = $register_msg['ihc_register_emails_not_match_msg'];
                }
                break;
            case 'pass1':
                $register_metas = ihc_return_meta_arr('register');
                if ($register_metas['ihc_register_pass_options']==2){
                    //characters and digits
                    if (!preg_match('/[a-z]/', $value)){
                        $return = $register_msg['ihc_register_pass_letter_digits_msg'];
                    }
                    if (!preg_match('/[0-9]/', $value)){
                        $return = $register_msg['ihc_register_pass_letter_digits_msg'];
                    }
                } else if ($register_metas['ihc_register_pass_options']==3){
                    //characters, digits and one Uppercase letter
                    if (!preg_match('/[a-z]/', $value)){
                        $return = $register_msg['ihc_register_pass_let_dig_up_let_msg'];
                    }
                    if (!preg_match('/[0-9]/', $value)){
                        $return = $register_msg['ihc_register_pass_let_dig_up_let_msg'];
                    }
                    if (!preg_match('/[A-Z]/', $value)){
                        $return = $register_msg['ihc_register_pass_let_dig_up_let_msg'];
                    }
                }
                //check the length of password
                if($register_metas['ihc_register_pass_min_length']!=0){
                    if (strlen($value)<$register_metas['ihc_register_pass_min_length']){
                        $return = str_replace( '{X}', $register_metas['ihc_register_pass_min_length'], $register_msg['ihc_register_pass_min_char_msg'] );
                    }
                }
                break;
            case 'pass2':
                if ($value==$val2){
                    $return = 1;
                } else {
                    $return = $register_msg['ihc_register_pass_not_match_msg'];
                }
                break;
            case 'tos':
                if ($value==1){
                    $return = 1;
                } else {
                    $return = $register_msg['ihc_register_err_tos'];
                }
                break;
            
            default:
                //required conditional field
                $check = ihc_required_conditional_field_test($type, $value);
                if ($check){
                    $return = $check;
                } else {
                    $return = 1;
                }
                break;
        }
        if (empty($return)){
            $return = 1;
        }
        return $return;
    } else {
        $check = ihc_required_conditional_field_test($type, $value);//Check for required conditional field
        if ($check){
            return $check;
        } else {
            return $register_msg['ihc_register_err_req_fields'];    
        }
    }    
}

add_action("wp_ajax_nopriv_ihc_check_logic_condition_value", "ihc_check_logic_condition_value");
add_action('wp_ajax_ihc_check_logic_condition_value', 'ihc_check_logic_condition_value');
function ihc_check_logic_condition_value(){
    /*
     * @param none
     * @return none (print 1 the test was passed, 0 otherwise)
     */
    if (isset($_REQUEST['val']) && isset($_REQUEST['field'])){
        $fields_meta = ihc_get_user_reg_fields();
        $key = ihc_array_value_exists($fields_meta, $_REQUEST['field'], 'name');
        if ($key!==FALSE){
            if (isset($fields_meta[$key]['conditional_logic_corresp_field_value'])){
                if ($fields_meta[$key]['conditional_logic_cond_type']=='has'){
                    //has value
                    if ($fields_meta[$key]['conditional_logic_corresp_field_value']==$_REQUEST['val']){
                        echo 1;
                        die();                        
                    }                    
                } else {
                    //contain value
                    if (strpos($_REQUEST['val'], $fields_meta[$key]['conditional_logic_corresp_field_value'])!==FALSE){
                        echo 1;
                        die();
                    }
                }        
            }
        }
    }
    echo 0;
    die();
}

add_action("wp_ajax_nopriv_ihc_check_lid_price", "ihc_check_lid_price");
add_action('wp_ajax_ihc_check_lid_price', 'ihc_check_lid_price');
function ihc_check_lid_price(){
    if (isset($_REQUEST['level_id'])){
        $data = ihc_get_level_by_id($_REQUEST['level_id']);
        if ($data['payment_type']=='free'){
            echo 1;
            die();
        }
    }
    echo 0;
    die();
}

add_action("wp_ajax_nopriv_ihc_check_unique_value_field_register", "ihc_check_unique_value_field_register");
add_action('wp_ajax_ihc_check_unique_value_field_register', 'ihc_check_unique_value_field_register');
function ihc_check_unique_value_field_register(){
    /*
     * @param none
     * @return none
     */
    $meta_key = (empty($_REQUEST['meta_key'])) ? '' : $_REQUEST['meta_key'];
    $meta_value = (empty($_REQUEST['meta_value'])) ? '' : $_REQUEST['meta_value'];

    if (ihc_meta_value_exists($meta_key, $meta_value)){
        echo 'Error';
        die();
    }
    echo 1;
    die();    
}

add_action("wp_ajax_nopriv_ihc_check_invitation_code_via_ajax", "ihc_check_invitation_code_via_ajax");
add_action('wp_ajax_ihc_check_invitation_code_via_ajax', 'ihc_check_invitation_code_via_ajax');
function ihc_check_invitation_code_via_ajax(){
    /*
     * @param none
     * @return none
     */
    if (empty($_REQUEST['c']) || !Ihc_Db::invitation_code_check($_REQUEST['c'])){
        $err_msg = get_option('ihc_invitation_code_err_msg');
        if (!$err_msg){
            echo __('Your Invitation Code is wrong.', 'ihc');                        
        } else {
            echo $err_msg;    
        }
        die();
    }
    echo 1;
    die();    
}

add_action("wp_ajax_nopriv_ihc_get_amount_plus_taxes", "ihc_get_amount_plus_taxes");
add_action('wp_ajax_ihc_get_amount_plus_taxes', 'ihc_get_amount_plus_taxes');
function ihc_get_amount_plus_taxes(){
    /*
     * @param none
     * @return none
     */
    if (!empty($_REQUEST['price'])){
        $price = $_REQUEST['price'];
        $state = (isset($_REQUEST['state'])) ? $_REQUEST['state'] : '';
        $country = isset($_REQUEST['country']) ? $_REQUEST['country'] : '';
        $taxes_data = ihc_get_taxes_for_amount_by_country($country, $state, $price);
        if ($taxes_data && !empty($taxes_data['total'])){
            $price += $taxes_data['total'];
            $price = round($price);
        }
        echo $price;
        die();         
    }
    echo @$_REQUEST['price'];
    die();
}

add_action("wp_ajax_nopriv_ihc_get_amount_plus_taxes_by_uid", "ihc_get_amount_plus_taxes_by_uid");
add_action('wp_ajax_ihc_get_amount_plus_taxes_by_uid', 'ihc_get_amount_plus_taxes_by_uid');
function ihc_get_amount_plus_taxes_by_uid(){
    /*
     * @param none
     * @return none
     */
     if (!empty($_REQUEST['uid']) && !empty($_REQUEST['price'])){
         $price = $_REQUEST['price'];
         $ihc_country = get_user_meta($_REQUEST['uid'], 'ihc_country', TRUE);
         $state = get_user_meta($_REQUEST['uid'], 'ihc_state', TRUE);
        $taxes_data = ihc_get_taxes_for_amount_by_country($ihc_country, $state, $price);
        if ($taxes_data && !empty($taxes_data['total'])){
            $price += $taxes_data['total'];
            $price = round($price);
        }
         echo $price;
        die();                  
     }
     echo @$_REQUEST['price'];
     die();
}

add_action("wp_ajax_nopriv_ihc_get_cart_via_ajax", "ihc_get_cart_via_ajax");
add_action('wp_ajax_ihc_get_cart_via_ajax', 'ihc_get_cart_via_ajax');
function ihc_get_cart_via_ajax(){
    /*
     * @param none
     * @return none
     */
    $currency = get_option("ihc_currency");
     $data['template'] = '';
    
     $level_data = ihc_get_level_by_id(@$_REQUEST['lid']);
    $data['level_label'] = ihc_correct_text($level_data['label']);
    @$data['final_price'] = $level_data['price'];
    
    /// LEVEL PRICE
    if ($level_data['payment_type']=='payment'){
        $data['level_price'] = ihc_format_price_and_currency($currency, $data['final_price']);
    } else {
        $data['level_price'] = __("Free", "ihc");
    }    
    
    /// COUPON
    if (!empty($_REQUEST['coupon'])){
        $coupon_data = ihc_check_coupon($_REQUEST['coupon'], $_REQUEST['lid']);
        if ($coupon_data){
            $data['discount_value'] = ihc_get_discount_value($data['final_price'], $coupon_data);    
            $data['final_price'] = ihc_coupon_return_price_after_decrease($data['final_price'], $coupon_data);    
            $data['discount_value']     = '-' . ihc_format_price_and_currency($currency, $data['discount_value']);        
        }
    }
    
    /// TAXES
    if (!empty($data['final_price']) && ihc_is_magic_feat_active('taxes')){
        $country = empty($_REQUEST['country']) ? '' : $_REQUEST['country'];
        $state = (isset($_REQUEST['state'])) ? $_REQUEST['state'] : '';
        $taxes = ihc_get_taxes_for_amount_by_country($country, $state, $data['final_price']);
        /// view tax value
        $total_taxes = $taxes['total'];
        $data['total_taxes'] = $taxes['print_total'];
        $data['taxes_details'] = $taxes['items'];
        $data['print_taxes'] = get_option('ihc_show_taxes');
    }        
    
    /// FINAL PRICE
    if (isset($total_taxes)){
        $data['final_price'] += $total_taxes;
    }
    if (isset($data['final_price'])){
        $data['price_number'] = $data['final_price'];
        $data['final_price'] = ihc_format_price_and_currency($currency, $data['final_price']);        
    }
    
    $data['show_full_cart'] = get_option("ihc_register_show_level_price");
    
    ob_start();
    require IHC_PATH . 'public/views/cart.php';
    $str = ob_get_contents();
    ob_end_clean();
    echo $str;
    die();        
}    
    
    /// WooCommerce
    require_once IHC_PATH . 'classes/Ihc_Custom_Woo_Endpoint.class.php';
    $woo_object = new Ihc_Custom_Woo_Endpoint();
    /// BuddyPress
    require_once IHC_PATH . 'classes/Ihc_Custom_BP_Endpoint.class.php';
    $bp_object = new Ihc_Custom_BP_Endpoint();
    
add_action('admin_bar_menu', 'ihc_add_custom_admin_bar_item', 998);
function ihc_add_custom_admin_bar_item(){
    /*
     * @param none
     * @return none
     */
    global $wp_admin_bar, $wpdb;
    if (!is_super_admin() || !is_admin_bar_showing()){
        return;
    }    
    if (!empty($_GET['page']) && $_GET['page']=='ihc_manage' && !empty($_GET['tab'])){
        switch ($_GET['tab']){
            case 'users':
                Ihc_Db::reset_dashboard_notification('users');
                break;
            case 'orders':
                Ihc_Db::reset_dashboard_notification('orders');                    
                break;
        }                
    }
    ?>
    <style>
        .ihc-top-bar-count{
                    display: inline-block !important;
                    vertical-align: top !important;
                    padding: 2px 7px !important;
                    background-color: #d54e21 !important;
                    color: #fff !important;
                    font-size: 9px !important;
                    line-height: 17px !important;
                    font-weight: 600 !important;
                    margin: 5px !important;
                    vertical-align: top !important;
                    -webkit-border-radius: 10px !important;
                    border-radius: 10px !important;
                    z-index: 26 !important;                    
        }
    </style>
    <?php

    if (!is_super_admin() || !is_admin_bar_showing() || get_option('ihc_admin_workflow_dashboard_notifications')==0){
        return;
    }    
    $new_users = Ihc_Db::get_dashboard_notification_value('users');
    $new_orders = Ihc_Db::get_dashboard_notification_value('orders');                

    $wp_admin_bar->add_menu(array(
                'id'    => 'ihc_users',
                'title' => '<span class="ihc-top-bar-count">' . $new_users . '</span>' . __('New Users', 'ihc'),
                'href'  => admin_url('admin.php?page=ihc_manage&tab=users'),
                'meta'  => array('class' => 'ihc-top-notf-admin-menu-bar'),
    ));
    $wp_admin_bar->add_menu(array(
                'id'    => 'ihc_orders',
                'title' => '<span class="ihc-top-bar-count">' . $new_orders . '</span>' . __('New Orders', 'ihc'),
                'href'  => admin_url('admin.php?page=ihc_manage&tab=orders'),
                'meta'  => array('class' => 'ihc-top-notf-admin-menu-bar'),
    ));        
}    
    
add_action('admin_bar_menu', 'ihc_add_custom_top_menu_dashboard', 997);
function ihc_add_custom_top_menu_dashboard(){
    /*
     * =============== DASHBOARD TOP MENU =================
     * @param none
     * @return none
     */        
    global $wp_admin_bar;
    if (!is_super_admin() || !is_admin_bar_showing()){
        return;
    }
    
    /// PARENT
    $wp_admin_bar->add_menu(array(
                'id'    => 'ihc_dashboard_menu',
                'title' => 'Ultimate Membership Pro',
                'href'  => '#',
                'meta'  => array(),
    ));    
    ///ITEMS
    $wp_admin_bar->add_menu(array('parent'=>'ihc_dashboard_menu', 'id'=>'ihc_dashboard_menu_pages', 'title'=>__('Membership Pages', 'ihc'), 'href'=>'#', 'meta'=>array()));
    $wp_admin_bar->add_menu(array('parent'=>'ihc_dashboard_menu', 'id'=>'ihc_dashboard_menu_showcases', 'title'=>__('Showcases', 'ihc'), 'href'=>'#', 'meta'=>array()));
    $wp_admin_bar->add_menu(array('parent'=>'ihc_dashboard_menu', 'id'=>'ihc_dashboard_menu_payment_gateways', 'title'=>__('Payment Gateways', 'ihc'), 'href'=>'#', 'meta'=>array()));
    $wp_admin_bar->add_menu(array('parent'=>'ihc_dashboard_menu', 'id'=>'ihc_dashboard_menu_levels', 'title'=>__('Levels', 'ihc'), 'href'=>admin_url('admin.php?page=ihc_manage&tab=levels'), 'meta'=>array()));
    $wp_admin_bar->add_menu(array('parent'=>'ihc_dashboard_menu', 'id'=>'ihc_dashboard_menu_notifications', 'title'=>__('Notifications', 'ihc'), 'href'=>admin_url('admin.php?page=ihc_manage&tab=notifications'), 'meta'=>array()));
    $wp_admin_bar->add_menu(array('parent'=>'ihc_dashboard_menu', 'id'=>'ihc_dashboard_menu_shortcodes', 'title'=>__('Shortcodes', 'ihc'), 'href'=>admin_url('admin.php?page=ihc_manage&tab=user_shortcodes'), 'meta'=>array()));
    
    /// SHOWCASES
    $wp_admin_bar->add_menu(array('parent'=>'ihc_dashboard_menu_showcases', 'id'=>'ihc_dashboard_menu_showcases_rf', 'title'=>__('Register Form', 'ihc'), 'href'=>admin_url('admin.php?page=ihc_manage&tab=register'), 'meta'=>array()));
    $wp_admin_bar->add_menu(array('parent'=>'ihc_dashboard_menu_showcases', 'id'=>'ihc_dashboard_menu_showcases_lf', 'title'=>__('Login Form', 'ihc'), 'href'=>admin_url('admin.php?page=ihc_manage&tab=login'), 'meta'=>array()));
    $wp_admin_bar->add_menu(array('parent'=>'ihc_dashboard_menu_showcases', 'id'=>'ihc_dashboard_menu_showcases_sp', 'title'=>__('Subscription Plan', 'ihc'), 'href'=>admin_url('admin.php?page=ihc_manage&tab=subscription_plan'), 'meta'=>array()));
    $wp_admin_bar->add_menu(array('parent'=>'ihc_dashboard_menu_showcases', 'id'=>'ihc_dashboard_menu_showcases_ap', 'title'=>__('Account Page', 'ihc'), 'href'=>admin_url('admin.php?page=ihc_manage&tab=account_page'), 'meta'=>array()));
    $wp_admin_bar->add_menu(array('parent'=>'ihc_dashboard_menu_showcases', 'id'=>'ihc_dashboard_menu_showcases_lu', 'title'=>__('Members List', 'ihc'), 'href'=>admin_url('admin.php?page=ihc_manage&tab=listing_users'), 'meta'=>array()));            
    
    /// PAYMENT GATEWAYS
    $gateways = ihc_get_active_payments_services();
    foreach ($gateways as $k=>$v){
        $wp_admin_bar->add_menu(array('parent'=>'ihc_dashboard_menu_payment_gateways', 'id'=>'ihc_dashboard_menu_gateway_' . $k, 'title'=>$v, 'href'=>admin_url('admin.php?page=ihc_manage&tab=payment_settings&subtab=' . $k), 'meta'=>array()));        
    }

    /// DEFAULT PAGES
    $array = array(
                    'ihc_general_login_default_page' => __('Login', 'ihc'),
                    'ihc_general_register_default_page' => __('Register', 'ihc'),
                    'ihc_subscription_plan_page' => __('Subscription Plan', 'ihc'),
                    'ihc_general_lost_pass_page' => __('Lost Password', 'ihc'),
                    'ihc_general_logout_page' => __('LogOut', 'ihc'),
                    'ihc_general_user_page' => __('User Account Page', 'ihc'),
                    'ihc_general_tos_page' => __('TOS', 'ihc'),
    );
    foreach ($array as $k=>$v){
        $page = get_option($k);
        $permalink = get_permalink($page);
        if ($permalink){
            $wp_admin_bar->add_menu(array('parent'=>'ihc_dashboard_menu_pages', 'id'=>'ihc_dashboard_menu_pages_' . $k, 'title'=>$v, 'href'=>$permalink, 'meta'=>array('target'=>'_blank')));                
        }
    }    
}

//// ACTIONS

add_action('ihc_action_after_cancel_subscription', 'ihc_send_notf_after_cancel_subscription', 1, 2);
function ihc_send_notf_after_cancel_subscription($uid=0, $lid=0){
    /*
     * @param int, int
     * @return none
     */
     ///CANCEL SUBSCRIPTION USER
     ihc_send_user_notifications($uid, 'ihc_cancel_subscription_notification-user', $lid);
     ///CANCEL SUBSCRIPTION Admin
     ihc_send_user_notifications($uid, 'ihc_cancel_subscription_notification-admin', $lid);    
}

add_action('ihc_action_after_subscription_delete', 'ihc_send_notf_after_delete_subscription', 1, 2);
function ihc_send_notf_after_delete_subscription($uid=0, $lid=0){
    /*
     * @param int, int
     * @return none
     */
     ///DELETE SUBSCRIPTION USER
     ihc_send_user_notifications($uid, 'ihc_delete_subscription_notification-user', $lid);
     ///DELETE SUBSCRIPTION Admin
     ihc_send_user_notifications($uid, 'ihc_delete_subscription_notification-admin', $lid);    
}

add_action('ihc_action_after_order_placed', 'ihc_send_notf_after_order_placed', 1, 2);
function ihc_send_notf_after_order_placed($uid=0, $lid=0){
    /*
     * @param int, int
     * @return none
     */
     ///ORDER PLACED USER
     ihc_send_user_notifications($uid, 'ihc_order_placed_notification-user', $lid);
     ///ORDER PLACED Admin
     ihc_send_user_notifications($uid, 'ihc_order_placed_notification-admin', $lid);         
}

add_action('ihc_action_after_subscription_activated', 'ihc_send_notf_after_subscription_activated', 1, 2);
function ihc_send_notf_after_subscription_activated($uid=0, $lid=0){
    /*
     * @param int, int
     * @reutnr none
     */
     /// send notification to user
     ihc_send_user_notifications($uid, 'ihc_subscription_activated_notification', $lid);
    
     /// give a gift
     if (ihc_is_magic_feat_active('gifts')){
         require_once IHC_PATH . 'classes/Ihc_Gifts.class.php';
         $gift_object = new Ihc_Gifts($uid, $lid);
     }
}

add_action('ihc_new_subscription_action', 'ihc_send_notf_on_new_subscription', 1, 2);
function ihc_send_notf_on_new_subscription($uid=0, $lid=0){
    /*
     * @param int, int
     * @reutnr none
     */
     ihc_send_user_notifications($uid, 'ihc_new_subscription_assign_notification-admin', $lid);    
}

 
add_action('wsl_hook_process_login_before_wp_safe_redirect', 'ihc_wp_social_login_do_redirect', 99, 0);
function ihc_wp_social_login_do_redirect(){
    /*
     * @param none
     * @return none, will do redirect if it's case
     */
    if (ihc_is_magic_feat_active('wp_social_login')){
        $redirect = get_option('ihc_wp_social_login_redirect_page');
        if ($redirect && $redirect!=-1){
            $url = get_permalink($redirect);
            if (!empty($url)){
                wp_safe_redirect($url);
                die();        
            }
        }
    }    
}

add_action('wsl_hook_process_login_after_wp_insert_user', 'ihc_wp_social_login_after_register_action', 99, 3);
function ihc_wp_social_login_after_register_action($user_id=0, $provider='', $hybridauth_user_profile=''){
     /*
     * @param none
     * @return none
     */
     if ($user_id){
         if (ihc_is_magic_feat_active('wp_social_login')){
             /// STORE AVATAR
             if (!empty($hybridauth_user_profile) && !empty($hybridauth_user_profile->photoURL)){
                 update_user_meta($user_id, 'ihc_avatar', $hybridauth_user_profile->photoURL);
             }
            
            ///ROLE
            $role = get_option('ihc_wp_social_login_default_role');
            if ($role){
                $u = new WP_User($user_id);
                $u->set_role($role);
            }
            
            /// LEVEL
            $lid = get_option('ihc_wp_social_login_default_level');
            if ($lid!='' && $lid!=-1){
                ihc_handle_levels_assign($user_id, $lid);                
            }                
        }
     }
}

/// PAYMENT GATE
function ihc_gate_add_query_vars_filter($vars=array()){
    /*
     * @param array
     * @return array
     */
    $vars[] = "ihc_action";
    $vars[] = "ihc_name";
    $vars[] = "ihc";
    return $vars;
}
add_filter('query_vars', 'ihc_gate_add_query_vars_filter', 99);


add_action('pre_get_posts', 'ihc_payment_gate_check', 999);
function ihc_payment_gate_check(){
    /*
     * @param string
     * @return none
     */
    if (!empty($_GET['ihc_action'])){
        $ihc_action = $_GET['ihc_action'];
    } else {
        $ihc_action = get_query_var('ihc_action');
    }    
     if (!empty($ihc_action)){
         $no_load = TRUE;
         switch ($ihc_action){
            case 'paypal':
                require_once IHC_PATH . 'paypal_ipn.php';
                break;
            case 'stripe':
                require_once IHC_PATH . 'stripe_webhook.php';
                break;
            case 'twocheckout':
                require_once IHC_PATH . 'twocheckout_ins.php';
                break;
            case 'authorize':
                require_once IHC_PATH . 'authorie_responso.php';
                break;
            case 'braintree':
                require_once IHC_PATH . 'braintree_webhook.php';
                break;
            case 'payza':
                require_once IHC_PATH . 'payza_webhook.php';
                break;
            case 'arrive':
                require_once IHC_PATH . 'arrive.php';
                break;
            case 'user_activation':
                require_once IHC_PATH . 'user_activation.php';
                break;    
            default:
                $home = get_home_url();
                wp_safe_redirect($home);
                exit;                
                break;
         }
     }
}


add_action("wp_ajax_nopriv_ihc_check_coupon_status_via_ajax", "ihc_check_coupon_status_via_ajax");
add_action('wp_ajax_ihc_check_coupon_status_via_ajax', 'ihc_check_coupon_status_via_ajax');
function ihc_check_coupon_status_via_ajax(){
    /*
     * @param none
     * @return none
     */
    $data['is_active'] = 0;
    $data['success_msg'] = __('Coupon applied successfully.', 'ihc');
    $data['err_msg'] = __('Coupon code is not valid.', 'ihc');
    if (!empty($_REQUEST['c']) && isset($_REQUEST['l'])){
        $check = ihc_check_coupon($_REQUEST['c'], $_REQUEST['l']);
        if (empty($check)){
            $data['is_active'] = 0;
        } else {
            $data['is_active'] = 1;
        }
    }
    echo json_encode($data);
    die();
}


add_action("wp_ajax_nopriv_ihc_get_ihc_state_field", "ihc_get_ihc_state_field");
add_action('wp_ajax_ihc_get_ihc_state_field', 'ihc_get_ihc_state_field');
function ihc_get_ihc_state_field(){
    /*
     * @param none
     * @return string
     */    
    if (isset($_REQUEST['country'])){
        echo ihc_get_state_field_str($_REQUEST['country']);
    }
    die();
}

add_action("wp_ajax_nopriv_ihc_remove_sm_from_user", "ihc_remove_sm_from_user");
add_action('wp_ajax_ihc_remove_sm_from_user', 'ihc_remove_sm_from_user');
function ihc_remove_sm_from_user(){
    /*
     * @param none
     * @return string
     */    
    if (isset($_REQUEST['type'])){
        global $current_user;
        if (isset($current_user->ID)){
            delete_user_meta($current_user->ID, 'ihc_' . $_REQUEST['type'] );
        }
    }
    die();
}

add_action("wp_ajax_nopriv_ihc_generate_invoice", "ihc_generate_invoice");
add_action('wp_ajax_ihc_generate_invoice', 'ihc_generate_invoice');
function ihc_generate_invoice(){
    /*
     * @param none
     * @return string
     */    
    if (isset($_REQUEST['order_id'])){
        if (is_admin()){
            /// is secure so get the uid from order table
            $uid = Ihc_Db::get_uid_by_order_id($_REQUEST['order_id']);
            $check = TRUE;
        } else {
            global $current_user;
            $uid = (isset($current_user->ID)) ? $current_user->ID : 0;                    
            $check = Ihc_Db::is_order_id_for_uid($uid, $_REQUEST['order_id']);    /// Security check        
        }
                
        if ($check && $uid){
            require_once IHC_PATH . 'classes/Ihc_Invoice.class.php';
            $object = new Ihc_Invoice($uid, $_REQUEST['order_id']);
            echo $object->output(TRUE);            
        }        
    }
    die();
}

add_action('user_register', 'ihc_increment_dashboard_user_notification', 1, 1);
function ihc_increment_dashboard_user_notification($uid=0){
    /*
     * @param int
     * @return none
     */
     Ihc_Db::increment_dashboard_notification('users');
}