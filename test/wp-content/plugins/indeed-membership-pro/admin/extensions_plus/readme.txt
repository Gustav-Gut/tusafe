Plugin Name: Extensions Plus

Plugin URI: http://market.wpindeed.com/
Description: 
Extensions Plus - Premium WordPress Plugins, Themes and AddOns

Version: 1.0

Author: wpindeed

Author URI: http://market.wpindeed.com/



===WORKFLOW====

a. Items are feeded via API from market.wpindeed.com

b. Extensions Plus will makes automatically updates on every 12 hours. 

c. If the Dashboard Notification is dismissed, it will show up again after 48 hours.




===INSTALL====

1. Just install the zip file as any regular Plugin


2. If You want to use this into another Plugin or Theme you need to: 
	
a. Copy the externsion_plus folder into your main Plugin/Theme folder
	
b. Copy the next two lines
 
	

For Plugins: 
		
Add next lines into your main plugin file
		
$ext_menu = 'YOUR_MENU_SLUG';
		
include_once plugin_dir_path(__FILE__) . 'extensions_plus/index.php';

	

For Themes: 
		
Add next lines into functions.php file:
		
$ext_menu = 'YOUR_MENU_SLUG';// if you have one
		
include_once get_template_directory() . '/extensions_plus/index.php';