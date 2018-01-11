<?php
/*
Plugin Name: Video Share VOD
Plugin URI: http://www.videosharevod.com
Description: <strong>Video Share / Video on Demand (VOD)</strong> plugin enables users to share videos and others to watch on demand. Allows publishing archived VideoWhisper Live Streaming broadcasts and recorded videochat streams.
Version: 1.7.1
Author: VideoWhisper.com
Author URI: http://www.videowhisper.com/
Contributors: videowhisper, VideoWhisper.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!class_exists("VWvideoShare"))
{
	class VWvideoShare {

		function VWvideoShare() { //constructor

		}

		static function install() {
			// do not generate any output here
			VWvideoShare::setupOptions();
			VWvideoShare::video_post();
			flush_rewrite_rules();
		}

		//! Supported extensions
		function extensions_video()
		{
			return array('3gp', '3g2', 'avi', 'f4v', 'flv', 'm2v', 'm4p', 'm4v', 'mp2', 'mkv', 'mov', 'mp4', 'mpg', 'mpe', 'mpeg', 'mpv', 'mwv', 'ogv', 'ogg', 'rm', 'rmvb', 'svi','ts', 'qt', 'vob', 'webm', 'wmv');
		}

		function extensions_flash()
		{
			return array('flv', 'mp4', 'f4v', 'm4v');
		}

		// Register Custom Post Type
		function video_post() {


			$options = get_option('VWvideoShareOptions');

			//only if missing
			if (post_type_exists($options['custom_post'])) return;

			$labels = array(
				'name'                => _x( 'Videos', 'Post Type General Name', 'video-share-vod' ),
				'singular_name'       => _x( 'Video', 'Post Type Singular Name', 'video-share-vod' ),
				'menu_name'           => __( 'Videos', 'video-share-vod' ),
				'parent_item_colon'   => __( 'Parent Video:', 'video-share-vod' ),
				'all_items'           => __( 'All Videos', 'video-share-vod' ),
				'view_item'           => __( 'View Video', 'video-share-vod' ),
				'add_new_item'        => __( 'Add New Video', 'video-share-vod' ),
				'add_new'             => __( 'New Video', 'video-share-vod' ),
				'edit_item'           => __( 'Edit Video', 'video-share-vod' ),
				'update_item'         => __( 'Update Video', 'video-share-vod' ),
				'search_items'        => __( 'Search Videos', 'video-share-vod' ),
				'not_found'           => __( 'No Videos found', 'video-share-vod' ),
				'not_found_in_trash'  => __( 'No Videos found in Trash', 'video-share-vod' ),
			);

			$args = array(
				'label'               => __( 'video', 'video-share-vod' ),
				'description'         => __( 'Video Videos', 'video-share-vod' ),
				'labels'              => $labels,
				'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', ),
				'taxonomies'          => array( 'category', 'post_tag' ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 5,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'map_meta_cap'        => true,
				'menu_icon' => 'dashicons-video-alt3',
				'capability_type'     => 'post',
				'capabilities' => array(
					'create_posts' => false
				)
			);
			register_post_type( $options['custom_post'], $args );

			// Add new taxonomy, make it hierarchical (like categories)
			$labels = array(
				'name'              => _x( 'Playlists', 'taxonomy general name' ),
				'singular_name'     => _x( 'Playlist', 'taxonomy singular name' ),
				'search_items'      => __( 'Search Playlists', 'video-share-vod' ),
				'all_items'         => __( 'All Playlists', 'video-share-vod' ),
				'parent_item'       => __( 'Parent Playlist' , 'video-share-vod'),
				'parent_item_colon' => __( 'Parent Playlist:', 'video-share-vod' ),
				'edit_item'         => __( 'Edit Playlist' , 'video-share-vod'),
				'update_item'       => __( 'Update Playlist', 'video-share-vod' ),
				'add_new_item'      => __( 'Add New Playlist' , 'video-share-vod'),
				'new_item_name'     => __( 'New Playlist Name' , 'video-share-vod'),
				'menu_name'         => __( 'Playlists' , 'video-share-vod'),
			);

			$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'         => true,
				'rewrite'           => array( 'slug' => $options['custom_taxonomy']),
			);
			register_taxonomy( $options['custom_taxonomy'], array( $options['custom_post'] ), $args );

			if ($options['tvshows'])
			{
				$labels = array(
					'name'                => _x( 'TV Shows', 'Post Type General Name', 'video-share-vod' ),
					'singular_name'       => _x( 'TV Show', 'Post Type Singular Name', 'video-share-vod' ),
					'menu_name'           => __( 'TV Shows', 'video-share-vod' ),
					'parent_item_colon'   => __( 'Parent TV Show:', 'video-share-vod' ),
					'all_items'           => __( 'All TV Shows', 'video-share-vod' ),
					'view_item'           => __( 'View TV Show', 'video-share-vod' ),
					'add_new_item'        => __( 'Add New TV Show', 'video-share-vod' ),
					'add_new'             => __( 'New TV Show', 'video-share-vod' ),
					'edit_item'           => __( 'Edit TV Show', 'video-share-vod' ),
					'update_item'         => __( 'Update TV Show', 'video-share-vod' ),
					'search_items'        => __( 'Search TV Show', 'video-share-vod' ),
					'not_found'           => __( 'No TV Shows found', 'video-share-vod' ),
					'not_found_in_trash'  => __( 'No TV Shows found in Trash', 'video-share-vod' ),
				);

				$args = array(
					'label'               => __( 'TV show', 'video-share-vod' ),
					'description'         => __( 'TV Shows', 'video-share-vod' ),
					'labels'              => $labels,
					'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', ),
					'taxonomies'          => array( 'category', 'post_tag' ),
					'hierarchical'        => false,
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'show_in_nav_menus'   => true,
					'show_in_admin_bar'   => true,
					'menu_position'       => 5,
					'can_export'          => true,
					'has_archive'         => true,
					'exclude_from_search' => false,
					'publicly_queryable'  => true,
					'menu_icon' => 'dashicons-format-video',
					'capability_type'     => 'post',
				);
				register_post_type( $options['tvshows_slug'], $args );

			}

			//extra rules
			add_rewrite_rule( 'crossdomain.xml$', 'index.php?vsv_crossdomain=1', 'top' );

			//without index.php use $1 instead of $matches[1]
			add_rewrite_rule( '^mbr\/([a-z\-]+)\/([0-9]+)\.([0-9a-z]+)$', 'wp-admin/admin-ajax.php?action=vwvs_mbr&protocol=$1&type=$3&id=$2', 'top');

		}

		function query_vars( $query_vars ){

			// array of recognized query vars
			$query_vars[] = 'vsv_crossdomain';
			/*
			$query_vars[] = 'protocol';
			$query_vars[] = 'id';
			$query_vars[] = 'type';
			$query_vars[] = 'action';
*/
			return $query_vars;
		}

		function parse_request( &$wp )
		{

			if ( array_key_exists( 'vsv_crossdomain', $wp->query_vars ) ) {
				$options = get_option('VWvideoShareOptions');
				echo html_entity_decode(stripslashes($options['crossdomain_xml']));
				exit();
			}
		}

		function cleanVideo($post_id, $clean = 'source', $confirm = 0)
		{
			//cleans certain files for video (source, hls)

			$options = get_option('VWvideoShareOptions');
			if (get_post_type( $post_id ) != $options['custom_post']) return 0;

			switch ($clean)
			{
			case 'source':
				//delete source video
				$videoPath = get_post_meta($post_id, 'video-source-file', true);

				if (!file_exists($videoPath)) return 0;

				$space += filesize($videoPath);

				if ($confirm) unlink($videoPath);

				break;

			case 'hls':
				//delete all generated video files
				$videoAdaptive = get_post_meta($post_id, 'video-adaptive', true);
				if ($videoAdaptive) $videoAlts = $videoAdaptive;
				else $videoAlts = array();

				foreach ($videoAlts as $alt)
				{

					//clean segmentation
					if ($alt['hls']) if (strstr($alt['hls'], $options['uploadsPath']))
						{
							$space += VWvideoShare::sizeTree($alt['hls']);
							if ($confirm) if (is_dir($alt['hls'])) VWvideoShare::delTree($alt['hls']);
						}
				}


				break;

			}

			//recalculate video space
			VWvideoShare::spaceVideo($post_id);

			return $space;


		}


		function delTree($dir) {
			$files = array_diff(scandir($dir), array('.','..'));
			foreach ($files as $file) {
				(is_dir("$dir/$file")) ? VWvideoShare::delTree("$dir/$file") : unlink("$dir/$file");
			}
			return rmdir($dir);
		}


		function video_delete($video_id)
		{
			$options = get_option('VWvideoShareOptions');
			if (get_post_type( $video_id ) != $options['custom_post']) return;

			//delete source video
			$videoPath = get_post_meta($video_id, 'video-source-file', true);
			if (file_exists($videoPath)) unlink($videoPath);

			//delete all generated video files
			$videoAdaptive = get_post_meta($video_id, 'video-adaptive', true);
			if ($videoAdaptive) $videoAlts = $videoAdaptive;
			else $videoAlts = array();



			foreach ($videoAlts as $alt)
			{
				if (file_exists($alt['file'])) unlink($alt['file']);

				//clean segmentation
				if ($alt['hls']) if (strstr($alt['hls'], $options['uploadsPath']))
					{
						if (file_exists($alt['hls']))
						{
							$files = glob($alt['hls'] . '/*'); // get all file names
							foreach($files as $file)
								{ // iterate files
								if(is_file($file))
									unlink($file); // delete file
							}
						}

						if (is_dir($alt['hls'])) VWvideoShare::delTree($alt['hls']);
						else unlink($alt['hls']);
					}
			}

		}

		//! Feature Pages and Menus
		function setupPages()
		{
			$options = get_option('VWvideoShareOptions');
			if ($options['disableSetupPages']) return;

			$pages = array(
				'videowhisper_videos' => 'Videos',
				'videowhisper_upload' => 'Upload',
				'videowhisper_recorder' => 'Record',
			);

			//create a menu and add pages
			$menu_name = 'VideoWhisper';
			$menu_exists = wp_get_nav_menu_object( $menu_name );
			if (!$menu_exists) $menu_id = wp_create_nav_menu($menu_name);

			//create pages if not created or existant
			foreach ($pages as $key => $value)
			{
				$pid = $options['p_'.$key];
				$page = get_post($pid);
				if (!$page) $pid = 0;

				if (!$pid)
				{
					global $user_ID;
					$page = array();
					$page['post_type']    = 'page';
					$page['post_content'] = '['.$key.']';
					$page['post_parent']  = 0;
					$page['post_author']  = $user_ID;
					$page['post_status']  = 'publish';
					$page['post_title']   = $value;
					$page['comment_status'] = 'closed';

					$pid = wp_insert_post ($page);

					$options['p_'.$key] = $pid;
					$link = get_permalink( $pid);

					if ($menu_id) wp_update_nav_menu_item($menu_id, 0, array(
								'menu-item-title' =>  $value,
								'menu-item-url' => $link,
								'menu-item-status' => 'publish'));

				}

			}

			update_option('VWvideoShareOptions', $options);
		}


		function adminMenu()
		{
			$options = get_option('VWvideoShareOptions');

			add_menu_page('Video Share VOD', 'Video Share VOD', 'manage_options', 'video-share', array('VWvideoShare', 'adminOptions'), 'dashicons-video-alt3',81);
			add_submenu_page("video-share", "Video Share VOD", "Options", 'manage_options', "video-share", array('VWvideoShare', 'adminOptions'));
			add_submenu_page("video-share", "Upload", "Upload", 'manage_options', "video-share-upload", array('VWvideoShare', 'adminUpload'));
			add_submenu_page("video-share", "Import", "Import", 'manage_options', "video-share-import", array('VWvideoShare', 'adminImport'));

			if (class_exists("VWliveStreaming")) add_submenu_page('video-share', 'Live Streaming', 'Live Streaming', 'manage_options', 'video-share-ls', array('VWvideoShare', 'adminLiveStreaming'));
			add_submenu_page("video-share", "Manage Videos", "Manage Videos", 'manage_options', "video-manage", array('VWvideoShare', 'adminManage'));
			add_submenu_page("video-share", "Statistics & Space", "Statistics & Space", 'manage_options', "video-stats", array('VWvideoShare', 'adminStats'));
			add_submenu_page("video-share", "Documentation", "Documentation", 'manage_options', "video-share-docs", array('VWvideoShare', 'adminDocs'));

		}


		//! cron

		function cron_schedules( $schedules ) {
			$schedules['min5'] = array(
				'interval' => 5*60,
				'display' => __( 'Once every five minutes' )
			);
			return $schedules;
		}


		function setup_schedule() {
			if ( ! wp_next_scheduled( 'cron_5min_event') )
			{
				wp_schedule_event( time(), 'min5', 'cron_5min_event');
			}
		}


		function init()
		{
			$options = get_option('VWvideoShareOptions');

			//translations
			load_plugin_textdomain('video-share-vod', false, dirname(plugin_basename(__FILE__)) .'/languages');

			add_action( 'wp_enqueue_scripts', array('VWvideoShare','scripts') );

			//prevent wp from adding <p> that breaks JS
			remove_filter ('the_content',  'wpautop');

			//move wpautop filter to BEFORE shortcode is processed
			add_filter( 'the_content', 'wpautop' , 1);

			//then clean AFTER shortcode
			add_filter( 'the_content', 'shortcode_unautop', 100 );

			/* Fire our meta box setup function on the post editor screen. */
			add_action( 'load-post.php', array('VWvideoShare', 'post_meta_boxes_setup' ) );
			add_action( 'load-post-new.php', array( 'VWvideoShare', 'post_meta_boxes_setup' ) );

			//listings
			add_filter('pre_get_posts', array('VWvideoShare','pre_get_posts'));

			add_filter('manage_' .$options['custom_post']. '_posts_columns', array( 'VWvideoShare', 'columns_head_video') , 10);
			add_filter( 'manage_edit-' .$options['custom_post']. '_sortable_columns', array('VWvideoShare', 'columns_register_sortable') );
			add_filter( 'request', array('VWvideoShare', 'duration_column_orderby') );
			add_action('manage_' .$options['custom_post']. '_posts_custom_column', array( 'VWvideoShare', 'columns_content_video') , 10, 2);
			add_filter( 'parse_query', array( 'VWvideoShare', 'parse_query') );

			add_action( 'before_delete_post',  array( 'VWvideoShare','video_delete') );

			//add_filter( 'category_description', 'category_description' );

			//video post page
			add_filter( "the_content", array('VWvideoShare','video_page'));
			// add_filter( "the_content", array('VWvideoShare','playlist_page'));

			if (class_exists("VWliveStreaming"))  if ($options['vwls_channel']) add_filter( "the_content", array('VWvideoShare','channel_page'));

				add_filter( "the_content", array('VWvideoShare','tvshow_page'));

			//! shortcodes
			add_shortcode('videowhisper_player', array( 'VWvideoShare', 'shortcode_player'));
			add_shortcode('videowhisper_videos', array( 'VWvideoShare', 'shortcode_videos'));
			add_shortcode('videowhisper_upload', array( 'VWvideoShare', 'shortcode_upload'));
			add_shortcode('videowhisper_preview', array( 'VWvideoShare', 'shortcode_preview'));
			add_shortcode('videowhisper_player_html', array( 'VWvideoShare', 'shortcode_player_html'));
			add_shortcode('videowhisper_import', array( 'VWvideoShare', 'shortcode_import'));
			add_shortcode('videowhisper_playlist', array( 'VWvideoShare', 'shortcode_playlist'));
			add_shortcode('videowhisper_embed_code', array( 'VWvideoShare', 'shortcode_embed_code'));

			add_shortcode('videowhisper_postvideos', array( 'VWvideoShare', 'videowhisper_postvideos'));
			add_shortcode('videowhisper_postvideos_process', array( 'VWvideoShare', 'videowhisper_postvideos_process'));

			//! widgets
			wp_register_sidebar_widget( 'videowhisper_videos', 'Videos',  array( 'VWvideoShare', 'widget_videos'), array('description' => 'List videos and updates using AJAX.') );
			wp_register_widget_control( 'videowhisper_videos', 'videowhisper_videos', array( 'VWvideoShare', 'widget_videos_options') );


			//! ajax
			//ajax videos
			add_action( 'wp_ajax_vwvs_videos', array('VWvideoShare','vwvs_videos'));
			add_action( 'wp_ajax_nopriv_vwvs_videos', array('VWvideoShare','vwvs_videos'));

			//ajax tools
			add_action( 'wp_ajax_vwvs_playlist_m3u', array('VWvideoShare','vwvs_playlist_m3u'));
			add_action( 'wp_ajax_nopriv_vwvs_playlist_m3u', array('VWvideoShare','vwvs_playlist_m3u'));

			add_action( 'wp_ajax_vwvs_embed', array('VWvideoShare','vwvs_embed'));
			add_action( 'wp_ajax_nopriv_vwvs_embed', array('VWvideoShare','vwvs_embed'));

			add_action( 'wp_ajax_vwvs_mbr', array('VWvideoShare','vwvs_mbr'));
			add_action( 'wp_ajax_nopriv_vwvs_mbr', array('VWvideoShare','vwvs_mbr'));


			add_filter('query_vars', array('VWvideoShare','query_vars'));


			//upload videos
			add_action( 'wp_ajax_vwvs_upload', array('VWvideoShare','vwvs_upload'));

			//Live Streaming support
			if (class_exists("VWliveStreaming")) if ($options['vwls_playlist'])
				{
					add_filter('vw_ls_manage_channel', array('VWvideoShare', 'vw_ls_manage_channel' ), 10, 2);
					add_filter('vw_ls_manage_channels_head', array('VWvideoShare', 'vw_ls_manage_channels_head' ));
				}


			//check db and update if necessary
			/*
			$vw_db_version = "0.0";

			$installed_ver = get_option( "vwvs_db_version" );
			if( $installed_ver != $vw_db_version )
			{
				$tab_formats = $wpdb->prefix . "vwvs_formats";
				$tab_process = $wpdb->prefix . "vwvs_process";

				global $wpdb;
				$wpdb->flush();
				$sql = "";

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
				if (!$installed_ver) add_option("vwvs_db_version", $vw_db_version);
				else update_option( "vwvs_db_version", $vw_db_version );
			}
			*/


		}


		function archive_template( $archive_template ) {
			global $post;

			$options = get_option('VWvideoShareOptions');

			if ( get_query_var( 'taxonomy' ) != $options['custom_taxonomy'] ) return $archive_template;

			if ($options['playlistTemplate'] == '+plugin')
			{
				$archive_template_new = dirname( __FILE__ ) . '/taxonomy-playlist.php';
				if (file_exists($archive_template_new)) return $archive_template_new;
			}

			$archive_template_new = get_template_directory() . '/' . $options['playlistTemplate'];
			if (file_exists($archive_template_new)) return $archive_template_new;
			else return $archive_template;
		}


		/*
		function category_description( $desc, $cat_id )
		{
			  $desc = 'Description: ' . $desc;
			  return $desc;
		}
*/

		/*
		function playlist_page($content)
		{
			if (!is_post_type_archive('playlist')) return $content;

			$addCode = 'Playlist [videowhisper_playlist videos=""]' . post_type_archive_title();

			return $addCode . $content;
		}
*/

		//! Widgets

		function widgetSetupOptions()
		{
			$widgetOptions = array(
				'title' => '',
				'perpage'=> '6',
				'perrow' => '',
				'playlist' => '',
				'order_by' => '',
				'category_id' => '',
				'select_category' => '1',
				'select_order' => '1',
				'select_page' => '1',
				'include_css' => '0'

			);

			$options = get_option('VWvideoShareWidgetOptions');

			if (!empty($options)) {
				foreach ($options as $key => $option)
					$widgetOptions[$key] = $option;
			}

			update_option('VWvideoShareWidgetOptions', $widgetOptions);

			return $widgetOptions;
		}

		function widget_videos_options($args=array(), $params=array())
		{

			$options = VWvideoShare::widgetSetupOptions();

			if (isset($_POST))
			{
				foreach ($options as $key => $value)
					if (isset($_POST[$key])) $options[$key] = trim($_POST[$key]);
					update_option('VWvideoShareWidgetOptions', $options);
			}
?>

	<?php _e('Title','video-share-vod'); ?>:<br />
	<input type="text" class="widefat" name="title" value="<?php echo stripslashes($options['title']); ?>" />
	<br /><br />

	<?php _e('Playlist','video-share-vod'); ?>:<br />
	<input type="text" class="widefat" name="playlist" value="<?php echo stripslashes($options['playlist']); ?>" />
	<br /><br />

	<?php _e('Category ID','video-share-vod'); ?>:<br />
	<input type="text" class="widefat" name="category_id" value="<?php echo stripslashes($options['category_id']); ?>" />
	<br /><br />

 <?php _e('Order By','video-share-vod'); ?>:<br />
	<select name="order_by" id="order_by">
  <option value="post_date" <?php echo $options['order_by']=='post_date'?"selected":""?>><?php _e('Video Date','video-share-vod'); ?></option>
    <option value="video-views" <?php echo $options['order_by']=='video-views'?"selected":""?>><?php _e('Views','video-share-vod'); ?></option>
    <option value="video-lastview" <?php echo $options['order_by']=='video-lastview'?"selected":""?>><?php _e('Recently Watched','video-share-vod'); ?></option>
</select><br /><br />

	<?php _e('Videos per Page','video-share-vod'); ?>:<br />
	<input type="text" class="widefat" name="perpage" value="<?php echo stripslashes($options['perpage']); ?>" />
	<br /><br />

	<?php _e('Videos per Row','video-share-vod'); ?>:<br />
	<input type="text" class="widefat" name="perrow" value="<?php echo stripslashes($options['perrow']); ?>" />
	<br /><br />

 <?php _e('Category Selector','video-share-vod'); ?>:<br />
	<select name="select_category" id="select_category">
  <option value="1" <?php echo $options['select_category']?"selected":""?>>Yes</option>
  <option value="0" <?php echo $options['select_category']?"":"selected"?>>No</option>
</select><br /><br />

 <?php _e('Order Selector','video-share-vod'); ?>:<br />
	<select name="select_order" id="select_order">
  <option value="1" <?php echo $options['select_order']?"selected":""?>>Yes</option>
  <option value="0" <?php echo $options['select_order']?"":"selected"?>>No</option>
</select><br /><br />

	<?php _e('Page Selector','video-share-vod'); ?>:<br />
	<select name="select_page" id="select_page">
  <option value="1" <?php echo $options['select_page']?"selected":""?>>Yes</option>
  <option value="0" <?php echo $options['select_page']?"":"selected"?>>No</option>
</select><br /><br />

	<?php _e('Include CSS','video-share-vod'); ?>:<br />
	<select name="include_css" id="include_css">
  <option value="1" <?php echo $options['include_css']?"selected":""?>>Yes</option>
  <option value="0" <?php echo $options['include_css']?"":"selected"?>>No</option>
</select><br /><br />
	<?php
		}

		function widget_videos($args=array(), $params=array())
		{

			$options = get_option('VWvideoShareWidgetOptions');

			echo stripslashes($args['before_widget']);

			echo stripslashes($args['before_title']);
			echo stripslashes($options['title']);
			echo stripslashes($args['after_title']);

			echo do_shortcode('[videowhisper_videos playlist="' . $options['playlist'] . '" category_id="' . $options['category_id'] . '" order_by="' . $options['order_by'] . '" perpage="' . $options['perpage'] . '" perrow="' . $options['perrow'] . '" select_category="' . $options['select_category'] . '" select_order="' . $options['select_order'] . '" select_page="' . $options['select_page'] . '" include_css="' . $options['include_css'] . '"]');

			echo stripslashes($args['after_widget']);
		}

		//! Post Listings
		public static function pre_get_posts($query)
		{
			/*
			//add channels to post listings
			if(is_category() || is_tag() || is_archive())
			{

				if (is_admin()) return $query;

				$query_type = get_query_var('post_type');

				if ($query_type)
				{
					if (!is_array($query_type)) $query_type = array($query_type);

					if (is_array($query_type))
						if (in_array('post', $query_type) && !in_array( $options['custom_post'], $query_type))
							$query_type[] =  $options['custom_post'];

				}
				else  //default
					{
					$query_type = array('post',  $options['custom_post']);
				}

				$query->set('post_type', $query_type);
			}
*/
			return $query;
		}


		//! AJAX implementation

		function scripts()
		{
			wp_enqueue_script("jquery");
		}


		function vwvs_mbr()
		{

			//mbr/$protocol/$id.$type

			$video_id = intval($_GET['id']);
			$type = sanitize_file_name($_GET['type']);

			$protocol = sanitize_file_name($_GET['protocol']);
			if (!$protocol) $protocol = 'http';

			if (!$video_id)
			{
				echo 'Missing video id!';
				var_dump($_GET);
				exit;
			}

			$video = get_post($video_id);
			if (!$video)
			{
				echo 'Missing video!';
				exit;
			}

			$options = get_option('VWvideoShareOptions');

			$mbr = array();


			//retrieve mp4 variants (conversions)
			$videoAdaptive = get_post_meta($video_id, 'video-adaptive', true);


			$hasHigh = 0;
			if ($videoAdaptive) if (is_array($videoAdaptive))
					foreach ($videoAdaptive as $alt)
					{
						if ($alt['extension'] == 'mp4') $mbr[] = $alt;
						if ($alt['id'] == 'high') $hasHigh = 1;
					}

				//add original if mp4 and high format is not available
				if (!count($mbr) && $options['originalBackup'])
				{
					$videoPath = get_post_meta($video_id, 'video-source-file', true);
					$ext = strtolower(pathinfo($videoPath, PATHINFO_EXTENSION));
					if (in_array($ext, array('mp4')))
					{
						$src['file'] = $videoPath;
						$src['bitrate'] = get_post_meta($video_id, 'video-bitrate', true);
						$src['width'] = get_post_meta($video_id, 'video-width', true);
						$src['height'] = get_post_meta($video_id, 'video-height', true);
						$src['extension'] = $ext;
						$src['type'] = 'video/mp4';
						$mbr[] = $src;
					}
				}

			//var_dump($mbr);

			$videoDuration = get_post_meta($video_id, 'video-duration', true);

			$nl = "\r\n";

			switch ($type)
			{
			case 'f4m':
				$htmlCode = '<?xml version="1.0" encoding="utf-8"?>
<manifest xmlns="http://ns.adobe.com/f4m/1.0">
     <mimeType>video/mp4</mimeType>
     <duration>' . $videoDuration . '</duration>' . $nl;


				switch ($protocol)
				{
				case 'http':
					$htmlCode .='<id>Progressive Download</id>
     <streamType>recorded</streamType>
	 <deliveryType>progressive</deliveryType>' . $nl;
					foreach ($mbr as $alt)
						$htmlCode .= '<media url="' . VWvideoShare::path2url($alt['file']) . '" bitrate="' . $alt['bitrate'] . '" width="' . $alt['width'] . '" height="' . $alt['height'] . '" />' . $nl;
					break;

				case 'rtmp':
					$htmlCode .='<id>Dynamic Streaming</id>
	<baseURL>' . $options['rtmpServer'] . '</baseURL>' . $nl;
					foreach ($mbr as $alt)
						$htmlCode .= '<media url="mp4:' . VWvideoShare::path2stream($alt['file']) . '" bitrate="' . $alt['bitrate'] . '" width="' . $alt['width'] . '" height="' . $alt['height'] . '" />
';
					break;

				}


				$htmlCode .= '</manifest>';
				break;

			case 'm3u8':
				switch ($protocol)
				{
				case 'hls':
					$htmlCode .='#EXTM3U
';
					foreach ($mbr as $alt)
					{
						$htmlCode .= '#EXT-X-STREAM-INF:PROGRAM-ID=1, BANDWIDTH=' . $alt['bitrate'] . $nl;

						$indexULR = '';
						if ($alt['hls'])
						{
							//static HLS conversion available
							$indexULR = VWvideoShare::path2url($alt['hls']) . '/index.m3u8';
						}
						elseif ($options['hlsServer'])
						{
							//use HLS server
							$stream = VWvideoShare::path2stream($alt['file']);
							$stream = 'mp4:' . $stream;
							$indexULR = $options['hlsServer'] . '_definst_/' . $stream . '/playlist.m3u8';
						}

						if ($indexULR) $htmlCode .= $indexULR . $nl;

					}
					break;
				}
				break;


			}

			ob_clean();
			echo $htmlCode;
			die;
		}



		function vwvs_embed()
		{

			header( "Content-Type: application/javascript" );

			$playlist = sanitize_file_name($_GET['playlist']);


			if ($playlist)
			{
				$htmlCode = VWvideoShare::shortcode_playlist(array('name'=> $playlist, 'embed'=>0));
				$htmlCode = preg_replace("/\r?\n/", "\\n", addslashes($htmlCode));
			}

			ob_clean();
			if ($htmlCode) echo 'document.write("'. $htmlCode . '");';
			die;


		}

		function vwvs_playlist_m3u()
		{
			$options = get_option('VWvideoShareOptions');

			$playlist = sanitize_file_name($_GET['playlist']);

			$listCode = '#EXTM3U';


			if ($playlist)
			{
				$args=array(
					'post_type' =>  $options['custom_post'],
					'post_status' => 'publish',
					'posts_per_page' => 100,
					'order'            => 'DESC',
					'orderby' => 'post_date',
					$options['custom_taxonomy'] => $playlist
				);

				$postslist = get_posts( $args );

				if (count($postslist)>0)
					foreach ($postslist as $item)
					{
						$listCode .= "\r\n" . VWvideoShare::path2url(VWvideoShare::videoPath($item->ID));
					}
			}

			ob_clean();
			echo $listCode;
			die;

		}


		//! Videos AJAX handler

		function vwvs_videos()
		{
			$options = get_option('VWvideoShareOptions');

			$perPage = (int) $_GET['pp'];
			if (!$perPage) $perPage = $options['perPage'];

			$playlist = sanitize_file_name($_GET['playlist']);

			$id = sanitize_file_name($_GET['id']);

			$category = (int) $_GET['cat'];

			$page = (int) $_GET['p'];
			$offset = $page * $perPage;

			$perRow = (int) $_GET['pr'];
			if (!$perRow) $perRow = $options['perRow'];

			//order
			$order_by = sanitize_file_name($_GET['ob']);
			if (!$order_by) $order_by = 'post_date';

			//options
			$selectCategory = (int) $_GET['sc'];
			$selectOrder = (int) $_GET['so'];
			$selectPage = (int) $_GET['sp'];

			//query
			$args=array(
				'post_type' =>  $options['custom_post'],
				'post_status' => 'publish',
				'posts_per_page' => $perPage,
				'offset'           => $offset,
				'order'            => 'DESC',
			);

			if ($order_by != 'post_date')
			{
				$args['orderby'] = 'meta_value_num';
				$args['meta_key'] = $order_by;
			}
			else
			{
				$args['orderby'] = 'post_date';
			}

			if ($playlist)  $args['playlist'] = $playlist;
			if ($category)  $args['category'] = $category;

			$postslist = get_posts( $args );

			ob_clean();
			//output

			//var_dump ($args);
			//echo $order_by;
			$ajaxurl = admin_url() . 'admin-ajax.php?action=vwvs_videos&pp=' . $perPage .  '&pr=' .$perRow. '&playlist=' . urlencode($playlist) . '&sc=' . $selectCategory . '&so=' . $selectOrder . '&sp=' . $selectPage .  '&id=' . $id;

			$ajaxurlP = $ajaxurl . '&p='.$page;
			$ajaxurlC = $ajaxurl . '&cat=' . $category ;
			$ajaxurlO = $ajaxurl . '&ob='. $order_by;
			$ajaxurlCO = $ajaxurl . '&cat=' . $category . '&ob='.$order_by ;

			//options
			echo '<div class="videowhisperListOptions">';
			if ($selectCategory)
			{
				echo '<div class="videowhisperDropdown">' . wp_dropdown_categories('show_count=1&echo=0&name=category' . $id . '&hide_empty=1&class=videowhisperSelect&show_option_all=' . __('All', 'video-share-vod') . '&selected=' . $category).'</div>';
				echo '<script>var category' . $id . ' = document.getElementById("category' . $id . '"); 			category' . $id . '.onchange = function(){aurl' . $id . '=\'' . $ajaxurlO.'&cat=\'+ this.value; loadVideos' . $id . '(\'Loading category...\')}
			</script>';
			}

			if ($selectOrder)
			{
				echo '<div class="videowhisperDropdown"><select class="videowhisperSelect" id="order_by' . $id . '" name="order_by' . $id . '" onchange="aurl' . $id . '=\'' . $ajaxurlC.'&ob=\'+ this.value; loadVideos' . $id . '(\'Ordering videos...\')">';
				echo '<option value="">' . __('Order By', 'video-share-vod') . ':</option>';
				echo '<option value="post_date"' . ($order_by == 'post_date'?' selected':'') . '>' . __('Video Date', 'video-share-vod') . '</option>';
				echo '<option value="video-views"' . ($order_by == 'video-views'?' selected':'') . '>' . __('Views', 'video-share-vod') . '</option>';
				echo '<option value="video-lastview"' . ($order_by == 'video-lastview'?' selected':'') . '>' . __('Watched Recently', 'video-share-vod') . '</option>';
				echo '</select></div>';
			}
			echo '</div>';


			//list
			if (count($postslist)>0)
			{
				$k = 0;
				foreach ( $postslist as $item )
				{
					if ($perRow) if ($k) if ($k % $perRow == 0) echo '<br>';

							$videoDuration = get_post_meta($item->ID, 'video-duration', true);
						$imagePath = get_post_meta($item->ID, 'video-thumbnail', true);

					$views = get_post_meta($item->ID, 'video-views', true) ;
					if (!$views) $views = 0;

					$duration = VWvideoShare::humanDuration($videoDuration);
					$age = VWvideoShare::humanAge(time() - strtotime($item->post_date));


					$height = get_post_meta($item->ID, 'video-height', true) ;

					$info = '' . __('Title', 'video-share-vod') . ': ' . $item->post_title . "\r\n" . __('Duration', 'video-share-vod') . ': ' . $duration . "\r\n" . __('Age', 'video-share-vod') . ': ' . $age . "\r\n" . __('Views', 'video-share-vod') . ": " . $views;
					$views .= ' ' . __('views', 'video-share-vod');

					echo '<div class="videowhisperVideo">';
					echo '<a href="' . get_permalink($item->ID) . '" title="' . $info . '"><div class="videowhisperVideoTitle">' . $item->post_title. '</div></a>';
					echo '<div class="videowhisperVideoDuration">' . $duration . '</div>';
					echo '<div class="videowhisperVideoDate">' . $age . '</div>';
					echo '<div class="videowhisperVideoViews">' . $views . '</div>';
					echo '<div class="videowhisperVideoResolution">' . $height . 'p</div>';


					if (!$imagePath || !file_exists($imagePath)) //video thumbnail?
						{
						$imagePath = plugin_dir_path( __FILE__ ) . 'no_video.png';
						VWvideoShare::updatePostThumbnail($item->ID);
					}
					else //what about featured image?
						{
						$post_thumbnail_id = get_post_thumbnail_id($item->ID);
						if ($post_thumbnail_id) $post_featured_image = wp_get_attachment_image_src($post_thumbnail_id, 'featured_preview') ;

						if (!$post_featured_image) VWvideoShare::updatePostThumbnail($item->ID);
					}



					echo '<a href="' . get_permalink($item->ID) . '" title="' . $info . '"><IMG src="' . VWvideoShare::path2url($imagePath) . $noCache .'" width="' . $options['thumbWidth'] . 'px" height="' . $options['thumbHeight'] . 'px" ALT="' . $info . '"></a>';

					echo '</div>
					';

					$k++;
				}

			} else echo __("No videos.",'video-share-vod');

			//pagination
			if ($selectPage)
			{
				echo "<BR>";
				if ($page>0) echo ' <a class="button g-btn type_secondary mk-button dark-color  mk-shortcode two-dimension" href="JavaScript: void()" onclick="aurl' . $id . '=\'' . $ajaxurlCO.'&p='.($page-1). '\'; loadVideos' . $id . '(\'Loading previous page...\');">' . __('Previous', 'video-share-vod') . '</a> ';
				echo '<a class="button g-btn type_secondary mk-button dark-color  mk-shortcode two-dimension" href="#"> ' . __('Page', 'video-share-vod') . ' ' . ($page+1) . ' </a>' ;
				if (count($postslist) >= $perPage) echo ' <a class="button g-btn type_secondary mk-button dark-color  mk-shortcode two-dimension" href="JavaScript: void()" onclick="aurl' . $id . '=\'' . $ajaxurlCO.'&p='.($page+1). '\'; loadVideos' . $id . '(\'Loading next page...\');">' . __('Next', 'video-share-vod') . '</a> ';
			}
			//output end
			die;

		}
		// !Shortcodes

		function shortcode_videos($atts)
		{

			$options = get_option('VWvideoShareOptions');

			$atts = shortcode_atts(
				array(
					'perpage'=> $options['perPage'],
					'perrow' => '',
					'playlist' => '',
					'order_by' => '',
					'category_id' => '',
					'select_category' => '1',
					'select_order' => '1',
					'select_page' => '1',
					'include_css' => '1',
					'id' => ''
				),
				$atts, 'videowhisper_videos');


			$id = $atts['id'];
			if (!$id) $id = uniqid();

			$ajaxurl = admin_url() . 'admin-ajax.php?action=vwvs_videos&pp=' . $atts['perpage'] . '&pr=' . $atts['perrow'] . '&playlist=' . urlencode($atts['playlist']) . '&ob=' . $atts['order_by'] . '&cat=' . $atts['category_id'] . '&sc=' . $atts['select_category'] . '&so=' . $atts['select_order'] . '&sp=' . $atts['select_page']. '&id=' .$id;

			$htmlCode = <<<HTMLCODE
<script type="text/javascript">
var aurl$id = '$ajaxurl';
var \$j = jQuery.noConflict();
var loader$id;

	function loadVideos$id(message){

	if (message)
	if (message.length > 0)
	{
	  \$j("#videowhisperVideos$id").html(message);
	}

		if (loader$id) loader$id.abort();

		loader$id = \$j.ajax({
			url: aurl$id,
			success: function(data) {
				\$j("#videowhisperVideos$id").html(data);
			}
		});
	}


	\$j(function(){
		loadVideos$id();
		setInterval("loadVideos$id('')", 60000);
	});

</script>

<div id="videowhisperVideos$id">
    Loading Videos...
</div>

HTMLCODE;

			if ($atts['include_css']) $htmlCode .= html_entity_decode(stripslashes($options['customCSS']));

			return $htmlCode;
		}

		function shortcode_import($atts)
		{
			global $current_user;

			get_currentuserinfo();

			if (!is_user_logged_in())
			{
				return __('Login is required to import videos!', 'video-share-vod');

			}

			$options = get_option( 'VWvideoShareOptions' );
			if (!VWvideoShare::hasPriviledge($options['shareList'])) return __('You do not have permissions to share videos!', 'video-share-vod');

			$atts = shortcode_atts(array('category' => '', 'playlist' => '', 'owner' => '', 'path' => '', 'prefix' => '', 'tag' => '', 'description' => ''), $atts, 'videowhisper_import');

			if (!$atts['path']) return 'videowhisper_import: Path required!';

			if (!file_exists($atts['path'])) return 'videowhisper_import: Path not found!';

			if ($atts['category']) $categories = '<input type="hidden" name="category" id="category" value="'.$atts['category'].'"/>';
			else $categories = '<label for="category">' . __('Category', 'video-share-vod') . ': </label><div class="videowhisperDropdown">' . wp_dropdown_categories('show_count=1&echo=0&name=category&hide_empty=0&class=videowhisperSelect').'</div>';

			if ($atts['playlist']) $playlists = '<br><label for="playlist">' . __('Playlist', 'video-share-vod') . ': </label>' .$atts['playlist'] . '<input type="hidden" name="playlist" id="playlist" value="'.$atts['playlist'].'"/>';
			elseif ( current_user_can('edit_posts') ) $playlists = '<br><label for="playlist">Playlist(s): </label> <br> <input size="48" maxlength="64" type="text" name="playlist" id="playlist" value="' . $current_user->display_name .'"/> ' . __('(comma separated)', 'video-share-vod');
			else $playlists = '<br><label for="playlist">' . __('Playlist', 'video-share-vod') . ': </label> ' . $current_user->display_name .' <input type="hidden" name="playlist" id="playlist" value="' . $current_user->display_name .'"/> ';

			if ($atts['owner']) $owners = '<input type="hidden" name="owner" id="owner" value="'.$atts['owner'].'"/>';
			else
				$owners = '<input type="hidden" name="owner" id="owner" value="'.$current_user->ID.'"/>';

			if ($atts['tag'] != '_none' )
				if ($atts['tag']) $tags = '<br><label for="playlist">' . __('Tags', 'video-share-vod') . ': </label>' .$atts['tag'] . '<input type="hidden" name="tag" id="tag" value="'.$atts['tag'].'"/>';
				else $tags = '<br><label for="tag">' . __('Tag(s)', 'video-share-vod') . ': </label> <br> <input size="48" maxlength="64" type="text" name="tag" id="tag" value=""/> (comma separated)';

				if ($atts['description'] != '_none' )
					if ($atts['description']) $descriptions = '<br><label for="description">' . __('Description', 'video-share-vod') . ': </label>' .$atts['description'] . '<input type="hidden" name="description" id="description" value="'.$atts['description'].'"/>';
					else $descriptions = '<br><label for="description">' . __('Description', 'video-share-vod') . ': </label> <br> <input size="48" maxlength="256" type="text" name="description" id="description" value=""/>';


					$url  =  get_permalink();

				$htmlCode .= '<h3>' . __('Import Videos', 'video-share-vod') . '</h3>' . $atts['path'] . $atts['prefix'];

			$htmlCode .=  '<form action="' . $url . '" method="post">';

			$htmlCode .= $categories;
			$htmlCode .= $playlists;
			$htmlCode .= $tags;
			$htmlCode .= $descriptions;
			$htmlCode .= $owners;

			$htmlCode .= '<br>' . VWvideoShare::importFilesSelect( $atts['prefix'], VWvideoShare::extensions_video(), $atts['path']);

			$htmlCode .= '<INPUT class="button button-primary" TYPE="submit" name="import" id="import" value="Import">';

			$htmlCode .= ' <INPUT class="button button-primary" TYPE="submit" name="delete" id="delete" value="Delete">';

			$htmlCode .= '</form>';

			$htmlCode .= html_entity_decode(stripslashes($options['customCSS']));

			return $htmlCode;
		}

		function shortcode_upload($atts)
		{

			global $current_user;

			get_currentuserinfo();

			if (!is_user_logged_in())
			{
				return __('Login is required to upload videos!', 'video-share-vod');
			}

			$options = get_option( 'VWvideoShareOptions' );
			if (!VWvideoShare::hasPriviledge($options['shareList'])) return __('You do not have permissions to share videos!', 'video-share-vod');


			$atts = shortcode_atts(array('category' => '', 'playlist' => '', 'owner' => '', 'tag' => '', 'description' => ''), $atts, 'videowhisper_upload');

			$ajaxurl = admin_url() . 'admin-ajax.php?action=vwvs_upload';

			if ($atts['category']) $categories = '<input type="hidden" name="category" id="category" value="'.$atts['category'].'"/>';
			else $categories = '<label for="category">' . __('Category', 'video-share-vod') . ': </label><div class="videowhisperDropdown">' . wp_dropdown_categories('show_count=1&echo=0&name=category&hide_empty=0&class=videowhisperSelect').'</div>';

			if ($atts['playlist']) $playlists = '<label for="playlist">' . __('Playlist', 'video-share-vod') . ': </label>' .$atts['playlist'] . '<input type="hidden" name="playlist" id="playlist" value="'.$atts['playlist'].'"/>';
			elseif ( current_user_can('edit_users') ) $playlists = '<br><label for="playlist">' . __('Playlist(s)', 'video-share-vod') . ': </label> <br> <input size="48" maxlength="64" type="text" name="playlist" id="playlist" value="' . $current_user->display_name .'" class="text-input"/> (comma separated)';
			else $playlists = '<label for="playlist">' . __('Playlist', 'video-share-vod') . ': </label> ' . $current_user->display_name .' <input type="hidden" name="playlist" id="playlist" value="' . $current_user->display_name .'"/> ';

			if ($atts['owner']) $owners = '<input type="hidden" name="owner" id="owner" value="'.$atts['owner'].'"/>';
			else $owners = '<input type="hidden" name="owner" id="owner" value="'.$current_user->ID.'"/>';

			if ($atts['tag'] != '_none' )
				if ($atts['tag']) $tags = '<br><label for="playlist">' . __('Tags', 'video-share-vod') . ': </label>' .$atts['tag'] . '<input type="hidden" name="tag" id="tag" value="'.$atts['tag'].'"/>';
				else $tags = '<br><label for="tag">' . __('Tag(s)', 'video-share-vod') . ': </label> <br> <input size="48" maxlength="64" type="text" name="tag" id="tag" value="" class="text-input"/> (comma separated)';

				if ($atts['description'] != '_none' )
					if ($atts['description']) $descriptions = '<br><label for="description">' . __('Description', 'video-share-vod') . ': </label>' .$atts['description'] . '<input type="hidden" name="description" id="description" value="'.$atts['description'].'"/>';
					else $descriptions = '<br><label for="description">' . __('Description', 'video-share-vod') . ': </label> <br> <input size="48" maxlength="256" type="text" name="description" id="description" value="" class="text-input"/>';



					$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
				$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
			$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
			$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");

			if ($iPhone || $iPad || $iPod || $Android) $mobile = true; else $mobile = false;

			if ($mobile)
			{
				$mobiles = 'capture="camcorder"';
				$accepts = 'accept="video/*;capture=camcorder"';
				$multiples = '';
				$filedrags = '';
			}
			else
			{
				$mobiles = '';
				$accepts = 'accept="video/*"';
				$multiples = 'multiple="multiple"';
				$filedrags = '<div id="filedrag">' . __('or Drag & Drop files to this upload area<br>(select rest of options first)', 'video-share-vod') . '</div>';
			}

			wp_enqueue_script( 'vwvs-upload', plugin_dir_url(  __FILE__ ) . '/upload.js');

			$submits = '<div id="submitbutton">
	<button class="videowhisperButton g-btn type_green small" type="submit" name="upload" id="upload">' . __('Upload Files', 'video-share-vod') . '</button>';

			$htmlCode .= <<<EOHTML
<form id="upload" action="$ajaxurl" method="POST" enctype="multipart/form-data">

<fieldset>
$categories
$playlists
$tags
$descriptions
$owners
<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="128000000" />
EOHTML;

			$htmlCode .= '<legend><h3>' . __('Video Upload', 'video-share-vod') . '</h3></legend><div> <label for="fileselect">' . __('Videos to upload', 'video-share-vod') . ': </label>';

			$htmlCode .= <<<EOHTML
	<br><input class="videowhisperButton g-btn type_midnight small" type="file" id="fileselect" name="fileselect[]" $mobiles $multiples $accepts />
$filedrags
$submits
</div>
EOHTML;

			$htmlCode .= <<<EOHTML
<div id="progress"></div>

</fieldset>
</form>

<STYLE>

#filedrag
{
 height: 100px;
 border: 1px solid #AAA;
 border-radius: 9px;
 color: #AAA;
 background: #243;
 padding: 4px;
 margin: 4px;
 text-align:center;
}

#progress
{
padding: 4px;
margin: 4px;
}

#progress div {
	position: relative;
	background: #555;
	-moz-border-radius: 9px;
	-webkit-border-radius: 9px;
	border-radius: 9px;

	padding: 4px;
	margin: 4px;

	color: #DDD;

}

#progress div > span {
	display: block;
	height: 20px;

	   -webkit-border-top-right-radius: 4px;
	-webkit-border-bottom-right-radius: 4px;
	       -moz-border-radius-topright: 4px;
	    -moz-border-radius-bottomright: 4px;
	           border-top-right-radius: 4px;
	        border-bottom-right-radius: 4px;
	    -webkit-border-top-left-radius: 4px;
	 -webkit-border-bottom-left-radius: 4px;
	        -moz-border-radius-topleft: 4px;
	     -moz-border-radius-bottomleft: 4px;
	            border-top-left-radius: 4px;
	         border-bottom-left-radius: 4px;

	background-color: rgb(43,194,83);

	background-image:
	   -webkit-gradient(linear, 0 0, 100% 100%,
	      color-stop(.25, rgba(255, 255, 255, .2)),
	      color-stop(.25, transparent), color-stop(.5, transparent),
	      color-stop(.5, rgba(255, 255, 255, .2)),
	      color-stop(.75, rgba(255, 255, 255, .2)),
	      color-stop(.75, transparent), to(transparent)
	   );

	background-image:
		-webkit-linear-gradient(
		  -45deg,
	      rgba(255, 255, 255, .2) 25%,
	      transparent 25%,
	      transparent 50%,
	      rgba(255, 255, 255, .2) 50%,
	      rgba(255, 255, 255, .2) 75%,
	      transparent 75%,
	      transparent
	   );

	background-image:
		-moz-linear-gradient(
		  -45deg,
	      rgba(255, 255, 255, .2) 25%,
	      transparent 25%,
	      transparent 50%,
	      rgba(255, 255, 255, .2) 50%,
	      rgba(255, 255, 255, .2) 75%,
	      transparent 75%,
	      transparent
	   );

	background-image:
		-ms-linear-gradient(
		  -45deg,
	      rgba(255, 255, 255, .2) 25%,
	      transparent 25%,
	      transparent 50%,
	      rgba(255, 255, 255, .2) 50%,
	      rgba(255, 255, 255, .2) 75%,
	      transparent 75%,
	      transparent
	   );

	background-image:
		-o-linear-gradient(
		  -45deg,
	      rgba(255, 255, 255, .2) 25%,
	      transparent 25%,
	      transparent 50%,
	      rgba(255, 255, 255, .2) 50%,
	      rgba(255, 255, 255, .2) 75%,
	      transparent 75%,
	      transparent
	   );

	position: relative;
	overflow: hidden;
}

#progress div.success
{
    color: #DDD;
	background: #3C6243 none 0 0 no-repeat;
}

#progress div.failed
{
 	color: #DDD;
	background: #682C38 none 0 0 no-repeat;
}
</STYLE>
EOHTML;

			$htmlCode .= html_entity_decode(stripslashes($options['customCSS']));

			return $htmlCode;

		}

		function vwvs_upload()
		{

			global $current_user;
			get_currentuserinfo();

			if (!is_user_logged_in())
			{
				echo 'Login required!';
				exit;
			}

			$owner = $_SERVER['HTTP_X_OWNER'] ? intval($_SERVER['HTTP_X_OWNER']) : intval($_POST['owner']);

			if ($owner && ! current_user_can('edit_users') && $owner != $current_user->ID )
			{
				echo 'Only admin can upload for others!';
				exit;
			}
			if (!$owner) $owner = $current_user->ID;


			$playlist = $_SERVER['HTTP_X_PLAYLIST'] ? $_SERVER['HTTP_X_PLAYLIST'] :$_POST['playlist'];

			//if csv sanitize as array
			if (strpos($playlist, ',') !== FALSE)
			{
				$playlists = explode(',', $playlist);
				foreach ($playlists as $key => $value) $playlists[$key] = sanitize_file_name(trim($value));
				$playlist = $playlists;
			}

			if (!$playlist)
			{
				echo 'Playlist required!';
				exit;
			}

			$category = $_SERVER['HTTP_X_CATEGORY'] ? sanitize_file_name($_SERVER['HTTP_X_CATEGORY']) : sanitize_file_name($_POST['category']);


			$tag = $_SERVER['HTTP_X_TAG'] ? $_SERVER['HTTP_X_TAG'] :$_POST['tag'];

			//if csv sanitize as array
			if (strpos($tag, ',') !== FALSE)
			{
				$tags = explode(',', $tag);
				foreach ($tags as $key => $value) $tags[$key] = sanitize_file_name(trim($value));
				$tag = $tags;
			}


			$description = sanitize_text_field( $_SERVER['HTTP_X_DESCRIPTION'] ? $_SERVER['HTTP_X_DESCRIPTION'] :$_POST['description'] );

			$options = get_option( 'VWvideoShareOptions' );

			$dir = $options['uploadsPath'];
			if (!file_exists($dir)) mkdir($dir);

			$dir .= '/uploads';
			if (!file_exists($dir)) mkdir($dir);

			$dir .= '/';


			ob_clean();
			$fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);

			function generateName($fn)
			{
				$ext = strtolower(pathinfo($fn, PATHINFO_EXTENSION));

				if (!in_array($ext, VWvideoShare::extensions_video() ))
				{
					echo 'Extension not allowed!';
					exit;
				}

				//unpredictable name
				return md5(uniqid($fn, true))  . '.' . $ext;
			}

			$path = '';

			if ($fn)
			{
				// AJAX call
				file_put_contents($path = $dir . generateName($fn), file_get_contents('php://input') );
				$el = array_shift(explode(".", $fn));
				$title = ucwords(str_replace('-', ' ', sanitize_file_name($el) ));

				echo VWvideoShare::importFile($path, $title, $owner, $playlist, $category, $tag, $description);

				//echo "Video was uploaded.";
			}
			else
			{
				// form submit
				$files = $_FILES['fileselect'];

				if ($files['error']) if (is_array($files['error']))
						foreach ($files['error'] as $id => $err)
						{
							if ($err == UPLOAD_ERR_OK) {
								$fn = $files['name'][$id];
								move_uploaded_file( $files['tmp_name'][$id], $path = $dir . generateName($fn) );
								$title = ucwords(str_replace('-', ' ', sanitize_file_name(array_shift(explode(".", $fn)))));

								echo VWvideoShare::importFile($path, $title, $owner, $playlist, $category) . '<br>';

								//echo "Video was uploaded.";
							}
						}

			}


			die;
		}

		function shortcode_preview($atts)
		{
			$atts = shortcode_atts(array('video' => '0'), $atts, 'shortcode_preview');

			$video_id = intval($atts['video']);
			if (!$video_id) return 'shortcode_preview: Missing video id!';

			$video = get_post($video_id);
			if (!$video) return 'shortcode_preview: Video #'. $video_id . ' not found!';

			$options = get_option( 'VWvideoShareOptions' );

			//res
			$vWidth = get_post_meta($video_id, 'video-width', true);
			$vHeight = get_post_meta($video_id, 'video-height', true);
			if (!$vWidth) $vWidth = $options['thumbWidth'];
			if (!$vHeight) $vHeight = $options['thumbHeight'];

			//snap
			$imagePath = get_post_meta($video_id, 'video-snapshot', true);
			if ($imagePath)
				if (file_exists($imagePath))
					$imageURL = VWvideoShare::path2url($imagePath);
				else VWvideoShare::updatePostThumbnail($update_id);

				if (!$imagePath) $imageURL = VWvideoShare::path2url(plugin_dir_path( __FILE__ ) . 'no_video.png');
				$video_url = get_permalink($video_id);
			$htmlCode = "<a href='$video_url'><IMG SRC='$imageURL' width='$vWidth' height='$vHeight'></a>";

			return $htmlCode;
		}

		function shortcode_playlist($atts)
		{
			$atts = shortcode_atts(
				array(
					'name' => '',
					'videos' => '',
					'embed' => '1',
				), $atts, 'videowhisper_playlist');


			if (!$atts['name'] && !$atts['videos']) return 'No playlist or video list specified!';

			$options = get_option( 'VWvideoShareOptions' );

			if ($atts['embed'])
				if (VWvideoShare::hasPriviledge($options['embedList'])) $showEmbed=1;
				else $showEmbed = 0;
				else $showEmbed = 0;


				$player = $option['playlist_player'];
			if (!$player) $player = 'video-js';

			switch ($player)
			{
			case 'strobe':

				$playlist_m3u = admin_url() . 'admin-ajax.php?action=vwvs_playlist_m3u&playlist=' . urlencode($atts['name']);

				$player_url = plugin_dir_url(__FILE__) . 'strobe/StrobeMediaPlayback.swf';
				$flashvars ='src=' .$playlist_m3u. '&autoPlay=false';

				$htmlCode .= '<object class="videoPlayer" width="480" height="360" type="application/x-shockwave-flash" data="' . $player_url . '"> <param name="movie" value="' . $player_url . '" /><param name="flashvars" value="' .$flashvars . '" /><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="wmode" value="direct" /></object>';

				// $dfrt56 .= $htmlCode;
				$embedCode .= '<BR><a href="'.$playlist_m3u . '">Playlist M3U</a>';

				$htmlCode .= '<br><h5>Embed Flash Playlist HTML Code (Copy and Paste to your Page)</h5>';
				$htmlCode .= htmlspecialchars($embedCode);
				break;


			case 'video-js':

				if ($atts['name'] && !$atts['videos'])
				{
					if (!taxonomy_exists($options['custom_taxonomy'])) $htmlCode .= 'Error: Taxonomy does not exist: '. $options['custom_taxonomy'];

					$args = array(
						'post_type' => $options['custom_post'],
						'post_status' => 'publish',
						'posts_per_page' => 100,
						'order'            => 'DESC',
						'orderby' => 'post_date',
						$options['custom_taxonomy'] => strtolower($atts['name']),
						'tax_query' => array(
							'taxonomy' => $options['custom_taxonomy'],
							'field'    => 'name',
							'terms'    =>  $atts['name'],
						),

					);
					//var_dump($args);
					$id = preg_replace("/[^A-Za-z0-9]/", '', $atts['name']);

					$postslist = get_posts( $args );
					//var_dump($postslist);

					if (count($postslist)>0)
						foreach ($postslist as $item)
						{
							$listCode .= ($listCode?",\r\n":'');
							$listCode .= '{title:"'.$item->post_title.'", ';

							$poster =  VWvideoShare::path2url(get_post_meta($item->ID, 'video-thumbnail', true));
							$listCode .= 'poster:"'.$poster.'", ';

							$source = VWvideoShare::path2url(VWvideoShare::videoPath($item->ID));
							$listCode .= 'src: ["'.$source.'"] ';
							$listCode .= '}';
						}
					else $htmlCode .= 'No published videos found for playlist "' . $atts['name']. '", taxonomy "'.$options['custom_taxonomy'].'"';
				}

				wp_enqueue_style( 'video-js', plugin_dir_url(__FILE__) .'video-js/video-js.min.css');
				wp_enqueue_script('video-js', plugin_dir_url(__FILE__) .'video-js/video.js');
				wp_enqueue_script('video-js4', plugin_dir_url(__FILE__) .'video-js/4/videojs-playlists.min.js',  array( 'video-js'));

				$VideoWidth = $options['playlistVideoWidth'];
				$ListWidth = $options['playlistListWidth'];

				$buttons = '<a id="prev" title="' . __('Previous video', 'video-share-vod') . '" href="#">' . __('Previous', 'video-share-vod') . '</a><a id="next" title="' . __('Next video', 'video-share-vod') . '" href="#">' . __('Next', 'video-share-vod') . '</a>';

				$htmlCode .= <<<EOCODE
<div class="video-holder centered">
        <video id="video_$id" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="none" width="$VideoWidth" height="540" data-setup='' poster="">
        </video>
        <div class="playlist-components">
            <div class="playlist_$id">
                <ul></ul>
            </div>
            <div class="button-holder">
$buttons
            </div>
        </div>
    </div>

<style>
.video-holder {
    background: #1b1b1b;
    padding: 10px;
}

.centered {
  margin-left: auto;
  margin-right: auto;
  width: auto;
  background: #333;
}

.playlist-components {
    height: 540px;
}

.video-js, .playlist-components {
    display: inline-block;
    vertical-align: top;
    margin-left: auto;
}
.button-holder {
    padding: 10px;
    height: 36px;
}

.playlist_$id {
    height: 490px;
    width: ${ListWidth}px;
    overflow-y: auto;
    color: #c0c0c0;
    display: block;
    margin: 0;
    padding: 1px 0 0 0;
    position: relative;
    background: -moz-linear-gradient(top,#000 0,#212121 19%,#212121 100%);
    background: -webkit-gradient(linear,left top,left bottom,color-stop(0%,#000),color-stop(19%,#212121),color-stop(100%,#212121));
    background: -o-linear-gradient(top,#000 0,#212121 19%,#212121 100%);
    background: -ms-linear-gradient(top,#000 0,#212121 19%,#212121 100%);
    background: linear-gradient(to bottom,#000 0,#212121 19%,#212121 100%);
    box-shadow: 0 1px 1px #1a1a1a inset,0px 1px 1px #454545;
    border: 1px solid #1a1a18;
}
#next {
    float: right;
}
#prev {
    float: left;
}

#prev, #next {
    cursor: pointer;
}

.playlist_$id ul {
    padding: 0;
    margin: 0;
    list-style: none;
}

.playlist_$id ul li {
    padding: 10px;
    border-bottom: 1px solid #000;
    cursor: pointer;
}
.playlist_$id ul li.active {
    background-color: #4f4f4f;
    border-color: #4f4f4f;
    color: #FFF;
}
.playlist_$id ul li:hover {
    border-color: #353535;
    background: #353535;
}


.playlist_$id .poster, .playlist_$id .title  {
    display: inline-block;
    vertical-align: middle;
}
 .playlist_$id .number{
    padding-right: 10px;
}
.playlist_$id .poster img {
    width: 64px;
}
.playlist_$id .title {
    padding-left: 10px;
}
</style>
<script>
var \$jQnC = jQuery.noConflict();
\$jQnC(document).ready(function()
{

  var videos_$id = [
    $listCode
  ];

  var videowhisperPlaylist_$id= {
    init : function(){
      this.els = {};
      this.cacheElements();
      this.initVideo();
      this.createListOfVideos();
      this.bindEvents();
      this.overwriteConsole();
    },
    overwriteConsole : function(){
    },
    log : function(string){
    },
    cacheElements : function(){
      this.els.playlist_$id = \$jQnC('div.playlist_$id > ul');
      this.els.next = \$jQnC('#next');
      this.els.prev = \$jQnC('#prev');
      this.els.log = \$jQnC('div.panels > pre');
    },
    initVideo : function(){
      this.player = videojs('video_$id');
      this.player.playList(videos_$id);
    },
    createListOfVideos : function(){
      var html = '';
      for (var i = 0, len = this.player.pl.videos.length; i < len; i++){
        html += '<li data-videoplaylist="'+ i +'">'+
                  '<span class="number">' + (i + 1) + '</span>'+
                  '<span class="poster"><img src="'+ videos_${id}[i].poster +'"></span>' +
                  '<span class="title">'+ videos_${id}[i].title +'</span>' +
                '</li>';
      }
      this.els.playlist_$id.empty().html(html);
      this.updateActiveVideo();
    },
    updateActiveVideo : function(){
      var activeIndex = this.player.pl.current;

      this.els.playlist_$id.find('li').removeClass('active');
      this.els.playlist_$id.find('li[data-videoplaylist="' + activeIndex +'"]').addClass('active');
    },
    bindEvents : function(){
      var self = this;
      this.els.playlist_$id.find('li').on('click', \$jQnC.proxy(this.selectVideo,this));
      this.els.next.on('click', \$jQnC.proxy(this.nextOrPrev,this));
      this.els.prev.on('click', \$jQnC.proxy(this.nextOrPrev,this));

      this.player.on('next', function(e){
        self.updateActiveVideo.apply(self);
      });

      this.player.on('prev', function(e){
        self.updateActiveVideo.apply(self);
      });

      this.player.on('lastVideoEnded', function(e){

      });
    },

    nextOrPrev : function(e){
      var clicked = \$jQnC(e.target);
      this.player[clicked.attr('id')]();
    },

    selectVideo : function(e){
      var clicked = e.target.nodeName === 'LI' ? \$jQnC(e.target) : \$jQnC(e.target).closest('li');

      if (!clicked.hasClass('active')){
        var videoIndex = clicked.data('videoplaylist');
        this.player.playList(videoIndex);
        this.updateActiveVideo();
      }
    }
  };

  videowhisperPlaylist_$id.init();

});
</script>
EOCODE;


				if ($showEmbed)
				{
					$embedCode .= '<link rel="stylesheet" type="text/css" href="'.plugin_dir_url(__FILE__) . 'video-js/video-js.min.css' . '">';
					$embedCode .= "\r\n" . '<script src="' . plugin_dir_url(__FILE__) .'video-js/video.js' . '" type="text/javascript"></script>';
					$embedCode .= "\r\n" . '<script src="' . plugin_dir_url(__FILE__) .'video-js/4/videojs-playlists.min.js' . '" type="text/javascript"></script>';

					$embedCode .= "\r\n\r\n" . '<script src="' . admin_url() .'admin-ajax.php?action=vwvs_embed&playlist=' . urlencode($atts['name']) . '" type="text/javascript"></script>';


					$embedCode .= "\r\n\r\n". '<BR><a href="'.admin_url() . 'admin-ajax.php?action=vwvs_playlist_m3u&playlist=' . urlencode($atts['name']) . '">Playlist (M3U)</a>';


					$htmlCode .= "\r\n\r\n" . VWvideoShare::embedCode($embedCode, 'Embed Playlist HTML Code', 'Copy and Paste to your Page');
				}

				break;
			}

			return $htmlCode;

		}

		function embedCode($embedCode, $title, $instructions)
		{
			$htmlCode .= '<br><h5>'.$title.'</h5>';
			$htmlCode .= '<textarea style="width:90%; height: 160px">';
			$htmlCode .= '<script src="'.includes_url().'js/jquery/jquery.js" type="text/javascript"></script>'. "\r\n\r\n";
			$htmlCode .= htmlspecialchars($embedCode);
			$htmlCode .= '</textarea>';
			$htmlCode .= '<br>'.$instructions;
			return  $htmlCode;
		}

		function adVAST($id)
		{

			$options = get_option( 'VWvideoShareOptions' );

			//Ads enabled?
			$showAds = $options['adsGlobal'];

			//video exception playlists
			if ($id)
			{
				$lists = wp_get_post_terms(  $id, $options['custom_taxonomy'], array( 'fields' => 'names' ) );
				if (is_array($lists))
					foreach ($lists as $playlist)
					{
						if (strtolower($playlist) == 'sponsored') $showAds= true;
						if (strtolower($playlist) == 'adfree') $showAds= false;
					}

			}

			//no ads for premium users
			if ($showAds) if (VWvideoShare::hasPriviledge($options['premiumList'])) $showAds= false;


				if (!$showAds) return '';
				else return $options['vast'];

		}

		function shortcode_embed_code($atts)
		{
			$options = get_option( 'VWvideoShareOptions' );

			$atts = shortcode_atts(
				array(
					'poster' => '',
					'width' => $options['thumbWidth'],
					'height' => $options['thumbHeight'],
					'poster' => $options['thumbHeight'],
					'source' => '',
					'source_type' => '',
					'id' => '0',
					'fallback' => 'You must have a HTML5 capable browser to watch this video. Read more about video sharing solutions and players on <a href="http://videosharevod.com/">Video Share VOD</a> website.'
				), $atts, 'videowhisper_embed_code');

			$player = $options['embed_player'];
			if (!$player) $player = 'native';


			switch ($player)
			{
			case 'native':

				if ($atts['poster']) $posterProp = ' poster="' . $atts['poster'] . '"';
				else $posterProp ='';

				$embedCode .= "\r\n" . '<video width="' . $atts['width'] . '" height="' . $atts['height'] . '"  preload="metadata" autobuffer controls="controls"' . $posterProp . '>';
				$embedCode .= "\r\n" . ' <source src="' . $atts['source'] . '" type="' . $atts['source_type'] . '">';
				$embedCode .= "\r\n" . '</video>';
				$embedCode .= "\r\n" . "\r\n" . '<br><a href="' . $atts['source'] . '">' . __('Download Video File', 'video-share-vod') . '</a> (' . __('right click and Save As..', 'video-share-vod') . ')';
				break;
			}

			return VWvideoShare::embedCode($embedCode, __('Embed Video HTML Code','video-share-vod'), __('Copy and Paste to your Page','video-share-vod'));
		}


		function shortcode_player_html($atts)
		{
			$options = get_option( 'VWvideoShareOptions' );

			$atts = shortcode_atts(
				array(
					'poster' => '',
					'width' => $options['thumbWidth'],
					'height' => $options['thumbHeight'],
					'poster' => $options['thumbHeight'],
					'source_alt' => '',
					'source' => '',
					'source_type' => '',
					'source_alt_type' => '',
					'player' => '',
					'id' => '0',
					'fallback' => 'You must have a HTML5 capable browser to watch this video. Read more about video sharing solutions and players on <a href="http://videosharevod.com/">Video Share VOD Script</a> website.'
				), $atts, 'videowhisper_player_html');

			if (!$atts['player']) $player = $options['html5_player'];
			else $player = $atts['player'];

			if ($_GET['player_html'] && $options['allowDebug']) $player = sanitize_file_name($_GET['player_html']);


			if (!$player) $player = 'video-js';

			switch ($player)
			{
			case 'native':

				if ($atts['poster']) $posterProp = ' poster="' . $atts['poster'] . '"';
				else $posterProp ='';

				$htmlCode .='<video width="' . $atts['width'] . '" height="' . $atts['height'] . '"  preload="metadata" autobuffer controls="controls"' . $posterProp . '>';

				$htmlCode .=' <source src="' . $atts['source'] . '" type="' . $atts['source_type'] . '">';

				$htmlCode .='<div class="fallback"> <p>' . $atts['fallback'] . '</p></div> </video>';

				break;

			case 'wordpress':
				$htmlCode .= do_shortcode('[video src="' . $atts['source'] . '" poster="' . $atts['poster'] . '" width="' . $atts['width'] . '" height="' . $atts['height'] . '"]');
				break;


			case 'video-js':

				wp_enqueue_script('video-js', plugin_dir_url(__FILE__) .'video-js/video.js');
				wp_enqueue_style( 'video-js', plugin_dir_url(__FILE__) .'video-js/video-js.min.css');

				$vast = VWvideoShare::adVAST($atts['id']);

				$id = 'vwVid' . $atts['id'];

				$videojsParams = '';
				$videojsCalls = '';

				$videojsCSS = 1;

				$htmlCode .= '<script>var $j = jQuery.noConflict();
				$j(document).ready(function(){ videojs.options.flash.swf = "' . plugin_dir_url(__FILE__) .'video-js/video-js.swf' . '";});</script>';

				//source alternatives (mbr)
				if ($atts['source_alt'])
				{
					wp_enqueue_script('video-js6', plugin_dir_url(__FILE__) .'video-js/6/videojs-media-sources.js', array( 'video-js') );

					wp_enqueue_script('video-js7', plugin_dir_url(__FILE__) .'video-js/7/videojs-hls.js', array( 'video-js', 'video-js6') );

					// segment handling
					wp_enqueue_script('video-js7-1', plugin_dir_url(__FILE__) .'video-js/7/flv-tag.js', array( 'video-js', 'video-js7') );
					wp_enqueue_script('video-js7-2', plugin_dir_url(__FILE__) .'video-js/7/exp-golomb.js', array( 'video-js', 'video-js7') );
					wp_enqueue_script('video-js7-3', plugin_dir_url(__FILE__) .'video-js/7/h264-stream.js', array( 'video-js', 'video-js7') );
					wp_enqueue_script('video-js7-4', plugin_dir_url(__FILE__) .'video-js/7/aac-stream.js', array( 'video-js', 'video-js7') );
					wp_enqueue_script('video-js7-5', plugin_dir_url(__FILE__) .'video-js/7/segment-parser.js', array( 'video-js', 'video-js7') );

					//m3u8 handling
					wp_enqueue_script('video-js7-6', plugin_dir_url(__FILE__) .'video-js/7/stream.js', array( 'video-js', 'video-js7') );
					wp_enqueue_script('video-js7-7', plugin_dir_url(__FILE__) .'video-js/7/m3u8/m3u8-parser.js', array( 'video-js', 'video-js7') );
					wp_enqueue_script('video-js7-8', plugin_dir_url(__FILE__) .'video-js/7/playlist-loader.js', array( 'video-js', 'video-js7') );

					//MBR plugin
					wp_enqueue_script('video-js8-1', plugin_dir_url(__FILE__) .'video-js/8/videojs-mbr-menu-button.js', array( 'video-js', 'video-js6', 'video-js7') );
					wp_enqueue_script('video-js8', plugin_dir_url(__FILE__) .'video-js/8/videojs-mbr.js', array( 'video-js', 'video-js6', 'video-js7') );

					wp_enqueue_style( 'video-js9', plugin_dir_url(__FILE__) .'video-js/8/videojs-mbr.css');
					$videojsCSS = 0;

					$videojsParams .= "techOrder: ['hls']";
					$videojsCalls .= $id . '.mbr({autoSwitch:false});';
					$videojsCalls .= $id . '.controlBar.show();';

					$atts['source'] = $atts['source_alt'];
					$atts['source_type'] = $atts['source_alt_type'];
				}

				$htmlCode .= '<script>
					(function($) {})( jQuery );
					$j(document).ready(function(){
					var ' . $id . ' = videojs("' . $id . '", {' . $videojsParams . '});';


				if ($vast)
					if ($options['vastLib'] == 'vast')
					{
						wp_enqueue_script('video-js1', plugin_dir_url(__FILE__) .'video-js/1/vast-client.js');

						wp_enqueue_script('video-js2', plugin_dir_url(__FILE__) .'video-js/2/videojs.ads.js', array( 'video-js') );
						wp_enqueue_style( 'video-js2', plugin_dir_url(__FILE__) .'video-js/2/videojs.ads.css');


						wp_enqueue_script('video-js3', plugin_dir_url(__FILE__) .'video-js/3/videojs.vast.js', array( 'video-js', 'video-js1', 'video-js2') );
						wp_enqueue_style( 'video-js3', plugin_dir_url(__FILE__) .'video-js/3/videojs.vast.css');

						$videojsCalls .= $id . '.ads();';
						$videojsCalls .= $id . '.vast({ url: \'' . $options['vast'] . '\' })';
					}
				else
				{

					wp_enqueue_script('video-js2', plugin_dir_url(__FILE__) .'video-js/2/videojs.ads.js', array( 'video-js') );
					wp_enqueue_style( 'video-js2', plugin_dir_url(__FILE__) .'video-js/2/videojs.ads.css');

					wp_enqueue_script('ima3', 'http://imasdk.googleapis.com/js/sdkloader/ima3.js');


					wp_enqueue_script('video-js5', plugin_dir_url(__FILE__) .'video-js/5/videojs.ima.js', array( 'video-js', 'ima3'));
					wp_enqueue_style( 'video-js5', plugin_dir_url(__FILE__) .'video-js/5/videojs.ima.css');


					$videojsCalls .=  $id . '.ima({ id: \'' .$id. '\', adTagUrl: \'' . $options['vast'] . '\' });';
					$videojsCalls .=  $id . '.ima.requestAds();';
				}

				$htmlCode .= $videojsCalls;

				$htmlCode .= '});</script>';


				if ($atts['poster']) $posterProp = ' poster="' . $atts['poster'] . '"';
				else $posterProp ='';

				$htmlCode .= '<video id="' . $id . '" class="video-js vjs-default-skin vjs-big-play-centered"  controls="controls" preload="metadata" width="' . $atts['width'] . '" height="' . $atts['height'] . '"' . $posterProp . ' data-setup="{}">';

				$htmlCode .=' <source src="' . $atts['source'] . '" type="' . $atts['source_type'] . '">';

				$htmlCode .='<div class="fallback"> <p>' . $atts['fallback'] . '</p></div> </video>';

				break;

			default:
				$htmlCode .= 'Player not found:' . $player;
			}

			return $htmlCode;
		}

		function videowhisper_postvideos($atts)
		{


			$options = get_option( 'VWvideoShareOptions' );

			$atts = shortcode_atts(
				array(
					'post' => '',
				), $atts, 'videowhisper_postvideos');

			if (!$atts['post']) return 'No post id was specified, to manage post associated videos.';

			if ($_GET['playlist_upload']) $htmlCode .= '<A class="videowhisperButton g-btn type_blue" href="'.remove_query_arg('playlist_upload').'">Done Uploading Videos</A>';
			else
			{

				$htmlCode .= '<div class="w-actionbox color_alternate"><h3>Manage Videos</h3>';

				$channel = get_post( $atts['post'] );
				$htmlCode .= '<p>Available '.$channel->post_title.' videos: ' . VWvideoShare::importFilesCount( $channel->post_title, VWvideoShare::extensions_flash(), $options['vwls_archive_path']) .'</p>';

				$link  = add_query_arg( array( 'playlist_import' => $channel->post_title), get_permalink() );
				$link2  = add_query_arg( array( 'playlist_upload' => $channel->post_title), get_permalink() );

				$htmlCode .= ' <a class="videowhisperButton g-btn type_blue" href="' .$link.'">Import</a> ';
				$htmlCode .= ' <a class="videowhisperButton g-btn type_green" href="' .$link2.'">Upload</a> ';
				$htmlCode .= '</div>';
			}

			$htmlCode .= '<h4>Channel Videos</h4>';

			$htmlCode .= do_shortcode('[videowhisper_videos perpage="4" playlist="'.$channel->post_name.'"]');


			return $htmlCode;
		}

		function videowhisper_postvideos_process($atts)
		{

			$atts = shortcode_atts(
				array(
					'post' => '',
					'post_type' => '',
				), $atts, 'videowhisper_postvideos_process');

			VWvideoShare::importFilesClean();

			$htmlCode = '';

			if ($channel_upload = sanitize_file_name($_GET['playlist_upload']))
			{
				$htmlCode .= do_shortcode('[videowhisper_upload playlist="'.$channel_upload.'"]');
			}

			if ($channel_name = sanitize_file_name($_GET['playlist_import']))
			{

				$options = get_option( 'VWvideoShareOptions' );

				$url  = add_query_arg( array( 'playlist_import' => $channel_name), get_permalink() );


				$htmlCode .=  '<form id="videowhisperImport" name="videowhisperImport" action="' . $url . '" method="post">';

				$htmlCode .= "<h3>Import <b>" . $channel_name . "</b> Videos to Playlist</h3>";

				$htmlCode .= VWvideoShare::importFilesSelect( $channel_name, VWvideoShare::extensions_flash(), $options['vwls_archive_path']);

				$htmlCode .=  '<input type="hidden" name="playlist" id="playlist" value="' . $channel_name . '">';

				//same category as post
				if ($atts['post']) $postID = $atts['post'];
				else
					{ //search by name
					global $wpdb;
					if ($atts['post_type']) $cfilter = "AND post_type='" . $atts['post_type'] . "'";
					$postID = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $channel_name . "' $cfilter LIMIT 0,1" );
				}

				if ($postID)
				{
					$cats = wp_get_post_categories( $postID);
					if (count($cats)) $category = array_pop($cats);
					$htmlCode .=  '<input type="hidden" name="category" id="category" value="' . $category . '">';
				}

				$htmlCode .=   '<INPUT class="videowhisperButton g-btn type_primary button button-primary" TYPE="submit" name="import" id="import" value="Import">';

				$htmlCode .=  ' <INPUT class="videowhisperButton g-btn type_primary button button-primary" TYPE="submit" name="delete" id="delete" value="Delete">';

				$htmlCode .=  '</form>';
			}

			return $htmlCode;
		}


		//!permission functions

		//if any key matches any listing
		public static function inList($keys, $data)
		{
			if (!$keys) return 0;

			$list = explode(",", strtolower(trim($data)));

			foreach ($keys as $key)
				foreach ($list as $listing)
					if ( strtolower(trim($key)) == trim($listing) ) return 1;

					return 0;
		}

		public static function hasPriviledge($csv)
		{
			//determines if user is in csv list (role, id, email)

			if (strpos($csv,'Guest') !== false) return 1;



			if (is_user_logged_in())
			{
				global $current_user;
				get_currentuserinfo();

				//access keys : roles, #id, email
				if ($current_user)
				{
					$userkeys = $current_user->roles;
					$userkeys[] = $current_user->ID;
					$userkeys[] = $current_user->user_email;
				}

				if (VWvideoShare::inList($userkeys, $csv)) return 1;
			}

			return 0;
		}

		function hasRole($role)
		{
			if (!is_user_logged_in()) return false;

			global $current_user;
			get_currentuserinfo();

			$role = strtolower($role);

			if (in_array($role, $current_user->roles)) return true;
			else return false;
		}

		function getRoles()
		{
			if (!is_user_logged_in()) return 'None';

			global $current_user;
			get_currentuserinfo();

			return implode(", ", $current_user->roles);
		}

		function poweredBy()
		{


			$options = get_option('VWvideoShareOptions');

			$state = 'block' ;
			if (!$options['videowhisper']) $state = 'none';

			return '<div id="VideoWhisper" style="display: ' . $state . ';"><p>Published with VideoWhisper <a href="http://videosharevod.com/">Video Share VOD</a>.</p></div>';
		}

		//get video path
		function videoPath($video_id, $type = 'auto')
		{

			$options = get_option('VWvideoShareOptions');

			if ($type == 'auto')
			{
				$isMobile = (bool)preg_match('#\b(ip(hone|od|ad)|android|opera m(ob|in)i|windows (phone|ce)|blackberry|tablet|s(ymbian|eries60|amsung)|p(laybook|alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]|mobile|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT'] );

				if ($isMobile) $type = 'html5-mobile';
				else $type='html5';
			}


			$videoPath = get_post_meta($video_id, 'video-source-file', true);
			$ext = pathinfo($videoPath, PATHINFO_EXTENSION);

			switch ($type)
			{
			case 'html5':


				//use conversion - high first
				$videoAdaptive = get_post_meta($video_id, 'video-adaptive', true);
				if ($videoAdaptive) $videoAlts = $videoAdaptive;
				else $videoAlts = array();

				foreach (array('high', 'mobile') as $frm)
					if ($alt = $videoAlts[$frm])
						if (file_exists($alt['file']))
						{
							return $alt['file'];

						}

					if ($options['originalBackup'])
						if (in_array($ext, array('mp4')))
						{
							return $videoPath;
						}

					break;

			case 'html5-mobile':

				//use conversion - mobile first
				$videoAdaptive = get_post_meta($video_id, 'video-adaptive', true);
				if ($videoAdaptive) $videoAlts = $videoAdaptive;
				else $videoAlts = array();

				foreach (array('mobile', 'high') as $frm)
					if ($alt = $videoAlts[$frm])
						if (file_exists($alt['file']))
						{
							return $alt['file'];

						}

					if ($options['originalBackup'])
						if (in_array($ext, array('mp4')))
						{
							return $videoPath;
						}

					break;

			case 'flash':

				//use conversion
				$videoAdaptive = get_post_meta($video_id, 'video-adaptive', true);
				if ($videoAdaptive) $videoAlts = $videoAdaptive;
				else $videoAlts = array();

				foreach (array('high', 'mobile') as $frm)
					if ($alt = $videoAlts[$frm])
						if (file_exists($alt['file']))
						{
							return $alt['file'];

						}
					return;

				if ($options['originalBackup'])
					if (in_array($ext, VWvideoShare::extensions_flash()))
					{
						return $videoPath;
					}

				break;
			}


		}

		function shortcode_player($atts)
		{

			$atts = shortcode_atts(array('video' => '0', 'embed' => '1', 'player' => '',  'width' => ''), $atts, 'videowhisper_player');

			$video_id = intval($atts['video']);
			if (!$video_id) return 'shortcode_player: Missing video id!';

			$video = get_post($video_id);
			if (!$video) return 'shortcode_player: Video #'. $video_id . ' not found!';

			$pWidth = intval($atts['width']); //force width

			$options = get_option( 'VWvideoShareOptions' );

			//VOD
			$deny = '';

			//global
			if (!VWvideoShare::hasPriviledge($options['watchList'])) $deny = 'Your current membership does not allow watching videos.';

			//by playlists
			$lists = wp_get_post_terms( $video_id, $options['custom_taxonomy'], array( 'fields' => 'names' ) );

			if (!is_array($lists))
			{
				if (is_wp_error($lists)) echo 'Error: Can not retrieve "playlist" terms for video post: ' . $lists->get_error_message();

				$lists = array();
			}


			//playlist role required?
			if ($options['vod_role_playlist'])
				foreach ($lists as $key=>$playlist)
				{
					$lists[$key] = $playlist = strtolower(trim($playlist));

					//is role
					if (get_role($playlist)) //video defines access roles
						{
						$deny = 'This video requires special membership. Your current membership: ' .VWvideoShare::getRoles() .'.' ;
						if (VWvideoShare::hasRole($playlist)) //has required role
							{
							$deny = '';
							break;
						}
					}
				}

			//exceptions
			if (in_array('free', $lists)) $deny = '';

			if (in_array('registered', $lists))
				if (is_user_logged_in()) $deny = '';
				else $deny = 'Only registered users can watch this videos. Please login first.';

				if (in_array('unpublished', $lists)) $deny = 'This video has been unpublished.';

				if ($deny)
				{
					$htmlCode .= str_replace('#info#',$deny, html_entity_decode(stripslashes($options['accessDenied'])));
					$htmlCode .= '<br>';
					$htmlCode .= do_shortcode('[videowhisper_preview video="' . $video_id . '"]') . VWvideoShare::poweredBy();
					return $htmlCode;
				}

			//update stats
			$views = get_post_meta($video_id, 'video-views', true);
			if (!$views) $views = 0;
			$views++;
			update_post_meta($video_id, 'video-views', $views);
			update_post_meta($video_id, 'video-lastview', time());

			//snap
			$imagePath = get_post_meta($video_id, 'video-snapshot', true);
			if ($imagePath)
				if (file_exists($imagePath))
				{
					$imageURL = VWvideoShare::path2url($imagePath);
					$posterVar = '&poster=' . urlencode($imageURL);
					$posterProp = ' poster="' . $imageURL . '"';
				} else VWvideoShare::updatePostThumbnail($update_id);



			//embed code?
			if ($atts['embed'])
				if (VWvideoShare::hasPriviledge($options['embedList'])) $showEmbed=1;
				else $showEmbed = 0;
				else $showEmbed = 0;

				$player = $options['player_default'];

			//Detect special conditions browsers & devices
			$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
			$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
			$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");
			$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");

			$Safari  = (stripos($_SERVER['HTTP_USER_AGENT'],"Safari") && !stripos($_SERVER['HTTP_USER_AGENT'], 'Chrome'));

			$Mac = stripos($_SERVER['HTTP_USER_AGENT'],"Mac OS");
			$Firefox = stripos($_SERVER['HTTP_USER_AGENT'],"Firefox");


			if ($Mac && $Firefox) $player = $options['player_firefox_mac'];

			if ($Safari) $player = $options['player_safari'];

			if ($Android) $player = $options['player_android'];

			if ($iPod || $iPhone || $iPad) $player = $options['player_ios'];

			//force a player from shortcode
			if ($atts['player']) $player = $atts['player'];

			if ($_GET['player'] && $options['allowDebug']) $player = sanitize_file_name($_GET['player']);


			if (!$player) $player = $options['player_default'];

			//res
			$vWidth = get_post_meta($video_id, 'video-width', true);
			$vHeight = get_post_meta($video_id, 'video-height', true);
			if (!$vWidth) $vWidth = $options['thumbWidth'];
			if (!$vHeight) $vHeight = $options['thumbHeight'];


			if ($pWidth) //force width but keep aspect ratio
				{
				$pHeight = $pWidth*$vHeight/$vWidth;
				$vWidth = $pWidth;
				$vHeight = $pHeight;
			}

			switch ($player)
			{
			case 'strobe':

				$videoPath = get_post_meta($video_id, 'video-source-file', true);
				$videoURL = VWvideoShare::path2url($videoPath);

				//$videoURLmbr =  site_url() . '/mbr/http/' . $video_id . '.f4m' ;

				$vast = VWvideoShare::adVAST($atts['video']);

				$player_url = plugin_dir_url(__FILE__) . 'strobe/StrobeMediaPlayback.swf';

				$flashvars ='src=' . urlencode($videoURL) . '&autoPlay=false' . $posterVar;

				if ($vast)
				{
					//$flashvars .= '&plugin_mast=' .  urlencode(plugin_dir_url(__FILE__) . 'strobe/MASTPlugin.swf');
					//$flashvars .= '&src_mast_uri=' .  urlencode(plugin_dir_url(__FILE__) . 'strobe/mast_vast_2_wrapper.xml');
					//$flashvars .= 'src_namespace_mast=http://www.akamai.com/mast/1.0';

					//$flashvars .= '&src_namespace_mast=' .  urlencode(plugin_dir_url(__FILE__) . 'strobe/mast_vast_2_wrapper.xml');
				}

				$htmlCode .= '<object class="videoPlayer" width="' . $vWidth . '" height="' . $vHeight . '" type="application/x-shockwave-flash" data="' . $player_url . '"> <param name="movie" value="' . $player_url . '" /><param name="flashvars" value="' .$flashvars . '" /><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="wmode" value="direct" /></object>';

				if ($showEmbed)
				{
					$embedCode = htmlspecialchars($htmlCode);
					$embedCode .= htmlspecialchars('<br><a href="' . $videoURL . '">' . __('Download Video File', 'video-share-vod') . '</a> (' . __('right click and Save As..', 'video-share-vod') . ')');

					$htmlCode .= '<br><h5>' . __('Embed Flash Video Code (Copy & Paste to your Page)', 'video-share-vod') . '</h5>';
					$htmlCode .= $embedCode;

				}
				break;

			case 'strobe-rtmp':
				$videoPath = get_post_meta($video_id, 'video-source-file', true);
				$ext = pathinfo($videoPath, PATHINFO_EXTENSION);


				//use conversion if available
				$videoAdaptive = get_post_meta($video_id, 'video-adaptive', true);
				if ($videoAdaptive) $videoAlts = $videoAdaptive;
				else $videoAlts = array();

				foreach (array('high', 'mobile') as $frm)
					if ($alt = $videoAlts[$frm])
						if (file_exists($alt['file']))
						{
							$ext = pathinfo($alt['file'], PATHINFO_EXTENSION);
							$stream = VWvideoShare::path2stream($alt['file']);
							break;
						};

				//user original
				if (!$stream)
					if (in_array($ext, array('flv','mp4','m4v')))
					{
						//use source if compatible
						$stream = VWvideoShare::path2stream($videoPath);
					}


				if (!$stream) $htmlCode .= 'Adaptive format required but missing for this video!';



				$videoRTMP = $options['rtmpServer'] . '/' . $stream;

				//mbr support
				$videoURLmbr =  site_url() . '/mbr/rtmp/' . $video_id . '.f4m' ;

				if ($stream)
				{

					if ($ext == 'mp4') $stream = 'mp4:' . $stream;

					$player_url = plugin_dir_url(__FILE__) . 'strobe/StrobeMediaPlayback.swf';
					$flashvars ='src=' . urlencode($videoURLmbr) . '&autoPlay=false' . $posterVar;

					$htmlCode .= '<object class="videoPlayer" width="' . $vWidth . '" height="' . $vHeight . '" type="application/x-shockwave-flash" data="' . $player_url . '"> <param name="movie" value="' . $player_url . '" /><param name="flashvars" value="' .$flashvars . '" /><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="wmode" value="direct" /></object>';
				}
				else $htmlCode .= 'Stream not found!';

				if ($showEmbed)
				{
					$embedCode = htmlspecialchars($htmlCode);
					$embedCode .= htmlspecialchars('<br><a href="' . $videoURL . '">Download Video File</a> (right click and Save As..)');

					$htmlCode .= '<br><h5>Embed Flash Video Code (Copy & Paste to your Page)</h5>';
					$htmlCode .= $embedCode;
				}

				break;

			case 'html5':



				$videoAdaptive = get_post_meta($video_id, 'video-adaptive', true);
				if ($videoAdaptive) $videoAlts = $videoAdaptive;
				else $videoAlts = array();

				foreach (array('high', 'mobile') as $frm)
					if ($alt = $videoAlts[$frm])
						if (file_exists($alt['file']))
						{
							$videoURL = VWvideoShare::path2url($alt['file']);
							$videoType = $alt['type'];
							$width = $alt['width'];
							$height = $alt['height'];
							break;
						};

				//backup: use original if mp4
				if (!$videoURL)
				{
					$videoPath = get_post_meta($video_id, 'video-source-file', true);
					$ext = pathinfo($videoPath, PATHINFO_EXTENSION);

					if ($ext == 'mp4')
					{
						$videoURL = VWvideoShare::path2url($videoPath);
						$videoType = 'video/mp4';

						$width = $vWidth;
						$height = $vHeight;
					}
				}


				if (!$videoURL)
				{
					$htmlCode .= 'Mobile adaptive format required but missing for this video!';
					VWvideoShare::convertProcessQueue();
				}

				if (($videoURL))
				{
					$htmlCode .= do_shortcode('[videowhisper_player_html source="' . $videoURL . '" source_type="' . $videoType . '" poster="' . $imageURL . '" width="' . $vWidth . '" height="' . $vHeight . '" id="' . $video_id . '"]');

					if ($showEmbed) $htmlCode .= do_shortcode('[videowhisper_embed_code source="' . $videoURL . '" source_type="' . $videoType . '" poster="' . $imageURL . '" width="' . $vWidth . '" height="' . $vHeight . '" id="' . $video_id . '"]');

				}

				break;

			case 'html5-mobile':

				//only mobile sources

				$videoAdaptive = get_post_meta($video_id, 'video-adaptive', true);
				if ($videoAdaptive) $videoAlts = $videoAdaptive;
				else $videoAlts = array();

				if ($alt = $videoAlts['mobile'])
					if (file_exists($alt['file']))
					{
						$videoURL = VWvideoShare::path2url($alt['file']);
						$videoType = $alt['type'];
						$width = $alt['width'];
						$height = $alt['height'];

					}else $htmlCode .= 'Mobile adaptive format file missing for this video!';
				else $htmlCode .= 'Mobile adaptive format missing for this video!';


				if (($videoURL)) $htmlCode .= do_shortcode('[videowhisper_player_html source="' . $videoURL . '" source_type="' . $videoType . '" poster="' . $imageURL . '" width="' . $vWidth . '" height="' . $vHeight . '" id="' . $video_id . '"]');

				break;


			case 'html5-hls':

				//use conversion

				$videoAdaptive = get_post_meta($video_id, 'video-adaptive', true);
				if ($videoAdaptive) $videoAlts = $videoAdaptive;
				else $videoAlts = array();


				foreach (array('high', 'mobile') as $frm)
					if ($alt = $videoAlts[$frm])
						if (file_exists($alt['file']))
						{
							$stream = VWvideoShare::path2stream($alt['file']);
							$videoType = $alt['type'];
							$width = $alt['width'];
							$height = $alt['height'];
							break;

						}

					if (!$stream) $htmlCode .= 'Mobile adaptive format missing for this video!';

					if ($stream)
					{
						$stream = 'mp4:' . $stream;
						$streamURL = $options['hlsServer'] . '_definst_/' . $stream . '/playlist.m3u8';

						$videoURLmbr =  site_url() . '/mbr/hls/' . $video_id . '.m3u8';

						$htmlCode .= do_shortcode('[videowhisper_player_html source_alt="' . $videoURLmbr . '" source_alt_type="application/x-mpegURL" source="' . $streamURL . '" source_type="' . $videoType . '" poster="' . $imageURL . '" width="' . $vWidth . '" height="' . $vHeight . '" id="' . $video_id . '"]');

					} else $htmlCode .= 'Stream not found!';

				break;

			default:
				$htmlCode .= 'Player not found:' . $player;

			}


			return $htmlCode . VWvideoShare::poweredBy();
		}

		//! Custom Post Pages

		function single_template($single_template)
		{

			if (!is_single())  return $single_template;

			$options = get_option('VWvideoShareOptions');

			$postID = get_the_ID();
			if (get_post_type( $postID ) != $options['custom_post']) return $single_template;

			if ($options['postTemplate'] == '+plugin')
			{
				$single_template_new = dirname( __FILE__ ) . '/template-video.php';
				if (file_exists($single_template_new)) return $single_template_new;
			}

			$single_template_new = get_template_directory() . '/' . $options['postTemplate'];

			if (file_exists($single_template_new)) return $single_template_new;
			else return $single_template;
		}



		function video_page($content)
		{
			if (!is_single()) return $content;
			$postID = get_the_ID() ;

			$options = get_option( 'VWvideoShareOptions' );

			if (get_post_type( $postID ) != $options['custom_post']) return $content;


			if ($options['videoWidth']) $wCode = ' width="' . trim($options['videoWidth']) . '"';
			else $wCode ='';

			$addCode .= '' . '[videowhisper_player video="' . $postID . '" embed="1"'.$wCode.']';

			//playlist
			global $wpdb;

			$terms = get_the_terms( $postID, $options['custom_taxonomy'] );

			if ( $terms && ! is_wp_error( $terms ) )
			{



				$addCode .=  '<div class="w-actionbox">';
				foreach ( $terms as $term )
				{

					if (class_exists("VWliveStreaming"))  if ($options['vwls_channel'])
						{


							$channelID = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . $term->slug . "' and post_type='channel' LIMIT 0,1" );

							if ($channelID)
								$addCode .= ' <a title="' . __('Channel', 'video-share-vod') . ': '. $term->name .'" class="videowhisper_playlist_channel button g-btn type_red size_small mk-button dark-color  mk-shortcode two-dimension small" href="'. get_post_permalink( $channelID ) . '">' . $term->name . ' Channel</a> ' ;
						}


					$addCode .= ' <a title="' . __('Playlist', 'video-share-vod') . ': '. $term->name .'" class="videowhisper_playlist button g-btn type_secondary size_small mk-button dark-color  mk-shortcode two-dimension small" href="'. get_term_link( $term->slug, $options['custom_taxonomy']) . '">' . $term->name . '</a> ' ;


				}
				$addCode .=  '</div>';

			}


			$views = get_post_meta($postID, 'video-views', true);
			if (!$views) $views = 0;

			$addCode .= '<div class="videowhisper_views">' . __('Video Views', 'video-share-vod') . ': ' . $views . '</div>';

			return $addCode . $content ;
		}


		function channel_page($content)
		{
			if (!is_single()) return $content;
			$postID = get_the_ID() ;

			if (get_post_type( $postID ) != 'channel') return $content;

			$channel = get_post( $postID );

			$addCode = '<div class="w-actionbox color_alternate"><h3>' . __('Channel Playlist', 'video-share-vod') . '</h3> ' . '[videowhisper_videos playlist="' . $channel->post_name . '"] </div>';

			return $addCode . $content;

		}

		function tvshow_page($content)
		{
			if (!is_single()) return $content;

			$options = get_option( 'VWvideoShareOptions' );
			$postID = get_the_ID();
			if (get_post_type( $postID ) != $options['tvshows_slug']) return $content;

			$tvshow = get_post( $postID );

			$imageCode = '';
			$post_thumbnail_id = get_post_thumbnail_id($postID);
			if ($post_thumbnail_id) $post_featured_image = wp_get_attachment_image_src($post_thumbnail_id, 'featured_preview') ;

			if ($post_featured_image)
			{
				$imageCode = '<IMG style="padding-bottom: 20px; padding-right:20px" SRC ="'.$post_featured_image[0].'" WIDTH="'.$post_featured_image[1].'" HEIGHT="'.$post_featured_image[2].'" ALIGN="LEFT">';
			}

			$addCode = '<br style="clear:both"><div class="w-actionbox color_alternate"><h3>' . __('Episodes', 'video-share-vod') . '</h3> ' . '[videowhisper_videos playlist="' . $tvshow->post_name . '" select_category="0"] </div>';

			return  $imageCode . $content . $addCode;

		}

		//! Conversions


		//if $action was already done in last $expire, return false
		function timeTo($action, $expire = 60, $options='')
		{
			if (!$options) $options = get_option('VWvideoShareOptions');

			$cleanNow = false;

			$ztime = time();

			$lastClean = 0;
			$lastCleanFile = $options['uploadsPath'] . '/' . $action . '.txt';

			if (!file_exists($dir = dirname($lastCleanFile))) mkdir($dir);
			elseif (file_exists($lastCleanFile)) $lastClean = file_get_contents($lastCleanFile);

			if (!$lastClean) $cleanNow = true;
			else if ($ztime - $lastClean > $expire) $cleanNow = true;

				if ($cleanNow)
					file_put_contents($lastCleanFile, $ztime);

				return $cleanNow;
		}

		function optimumBitrate($width, $height)
		{
			if (!$width) return 500;
			if (!$height) return 500;

			$pixels = $width * $height;

			/*
			$bitrate = 500;
			if ($pixels >= 640*360) $bitrate = 1000;
			if ($pixels >= 854*480) $bitrate = 2500;
			if ($pixels >= 1280*720) $bitrate = 5000;
			if ($pixels >= 1920*1080) $bitrate = 8000;
*/
			$bitrate = floor($pixels*8000/2073600);

			return $bitrate;
		}

		function convertVideo($post_id, $overwrite = false)
		{

			if (!$post_id) return;

			$options = get_option( 'VWvideoShareOptions' );

			if (!$options['convertMobile'] && !$options['convertHigh'] && !$overwrite) return;

			$videoPath = get_post_meta($post_id, 'video-source-file', true);
			if (!$videoPath) return;

			$sourceExt = pathinfo($videoPath, PATHINFO_EXTENSION);


			$videoWidthM = $videoWidth = get_post_meta($post_id, 'video-width', true);
			$videoHeightM = $videoHeight = get_post_meta($post_id, 'video-height', true);

			if (!$videoWidth) return; // no size detected yet

			$videoCodec = get_post_meta($post_id, 'video-codec-video', true);
			$audioCodec = get_post_meta($post_id, 'video-codec-audio', true);

			if (!$videoCodec) return; // no codec detected yet

			$videoBitrate = get_post_meta($post_id, 'video-bitrate', true);


			//valid mp4 for html5 playback?
			if (($sourceExt == 'mp4') && ($videoCodec == 'h264') && ($audioCodec = 'aac')) $isMP4 =1;
			else $isMP4 = 0;



			//convertWatermark
			$cmdW ='';
			if ($options['convertWatermark'])
			{
				$cmdW = ' -filter_complex "overlay=' .$options['convertWatermarkPosition']. '"  -i ' . $options['convertWatermark'] . ' ';
			}


			//retrieve current alternate videos
			$videoAdaptive = get_post_meta($post_id, 'video-adaptive', true);

			if ($videoAdaptive)
				if (is_array($videoAdaptive)) $videoAlts = $videoAdaptive;
				else $videoAlts = unserialize($videoAdaptive);
				else $videoAlts = array();


				//conversion formats
				$formats = array();

			// mobile format
			if ($options['convertMobile']==2 || (!$isMP4 && $options['convertMobile']==1) )
			{
				//limit res
				if ($videoWidth * $videoHeight > 1024*768)
				{
					$videoWidthM = 1024;
					$videoHeightM = ceil($videoHeight * 1024 / $videoWidth);
				}

				$newBitrate = 400;
				if ($videoBitrate) if ($newBitrate > $videoBitrate - 50) $newBitrate = $videoBitrate - 50;

					$formats[0] = array
					(
						//Mobile: MP4/H.264, Baseline profile, max 1024, for wide compatibility
						'id' => 'mobile',
						'cmd' => '-pix_fmt yuv420p -force_key_frames "expr:gte(t,n_forced*5)" -s '.$videoWidthM.'x'.$videoHeightM.' -vb ' . $newBitrate . 'k -vcodec libx264 -movflags +faststart -profile:v baseline -level 3.1 -acodec libfaac -ac 2 -ab 50k',
						'width' => $videoWidthM,
						'height' => $videoHeightM,
						'bitrate' => $newBitrate + 50,
						'type' => 'video/mp4',
						'extension' => 'mp4'
					);
			} else
			{
				//delete old file if present
				$oldFile = $videoAlts['mobile']['file'];
				if ($oldFile) if (file_exists($oldFile)) unlink($oldFile);

					unset($videoAlts['mobile']);
			}


			//!high format

			//convertHigh
			// 0 = No
			// 1 = Auto
			// 2 = Auto + Bitrate
			// 3 = Always

			$newBitrate = VWvideoShare::optimumBitrate($videoWidth, $videoHeight);
			if ($videoBitrate) if ($newBitrate > $videoBitrate-96) $newBitrate = $videoBitrate-96; //don't increase (also includes 96 sound)


				if ($options['convertHigh']==3 || (!$isMP4 && $options['convertHigh']>=1) || (($videoBitrate > $newBitrate) &&$options['convertHigh']>=2))
				{
					//high quality mp4
					//video
					$cmdV = '-pix_fmt yuv420p -force_key_frames "expr:gte(t,n_forced*5)" -s '.$videoWidth.'x'.$videoHeight.' -vcodec libx264 -b:v '.$newBitrate.'k -profile:v main -level 3.1';

					// if h264 copy for auto or autobitrate if lower
					if ($videoCodec == 'h264' && $options['convertHigh']==1 || ($videoCodec == 'h264' && ($videoBitrate <= $newBitrate) && $options['convertHigh']==2))
					{
						$cmdV = '-vcodec copy';
						$newBitrate = $videoBitrate;
					}

					//audio
					$cmdA = '-acodec libfaac -ac 2 -ab 96k';
					if ($audioCodec == 'aac' && $options['convertHigh'] == 1) $cmdA = '-acodec copy';

					$formats[1] = array
					(
						'id' => 'high',
						'cmd' => $cmdV . ' -movflags +faststart '. $cmdA,
						'width' => $videoWidth,
						'height' => $videoHeight,
						'bitrate' => $newBitrate + 96,
						'type' => 'video/mp4',
						'extension' => 'mp4'
					);

				}
			else
			{
				//delete old file if present
				$oldFile = $videoAlts['high']['file'];
				if ($oldFile) if (file_exists($oldFile)) unlink($oldFile);

					unset($videoAlts['high']);
			}

			//hook formats
			$formats = apply_filters('videosharevod_formats', $formats);


			$path =  dirname($videoPath);

			$cmdS = '';
			$cmdHS = '';


			foreach ($formats as $format)
				if (!$videoAlts[$format['id']] || $overwrite)
				{
					$alt = $format;

					$newFile = $post_id .'_'.$alt['id']. '_' . md5(uniqid($post_id . $alt['id'], true))  . '.' . $alt['extension'];
					$alt['file'] = $path . '/' . $newFile;



					//delete old file
					$oldFile = $videoAlts[$format['id']]['file'];
					if ($oldFile) if ($oldFile != $alt['file']) if (file_exists($oldFile)) unlink($oldFile);

							$cmdS .= ' ' . $format['cmd'] . ' ' . $alt['file'];

						unset($alt['cmd']);

					//a process for each output file
					if (!$options['convertSingleProcess'])
					{
						$logPath = $path . '/' . $post_id . '-' . $alt['id'] . '.txt';
						$cmdPath = $path . '/' . $post_id . '-' . $alt['id'] . '-cmd.txt';


						$cmd = 'ulimit -t 7200; nice ' . $options['ffmpegPath'] . ' -y -threads 1 -i ' . $videoPath . $cmdW . ' ' . $format['cmd'] .' ' . $alt['file'] . ' &>' . $logPath . ' &';

						VWvideoShare::convertAdd($cmd);

						exec("echo '$cmd' >> $cmdPath", $output, $returnvalue);
					}

					//segment output for HLS
					if ($options['convertHLS'])
					{

						//clean  previous segmentation
						if ($alt['hls'])
							if (strstr($alt['hls'], $path))
							{
								if (file_exists($alt['hls']))
								{
									$files = glob($alt['hls'] . '/*'); // get all file names
									foreach($files as $file){ // iterate files
										if(is_file($file))
											unlink($file); // delete file
									}
								}

								unlink($alt['hls']);
							}

						$newF = $path . '/' . $post_id .'_'.$alt['id']. '_' . md5(uniqid($post_id . $alt['id'], true));
						if (!file_exists($newF)) mkdir($newF);


						$alt['hls'] = $newF;

						$logPath = $path . '/' . $post_id . '-' . $alt['id'] . '-hls.txt';
						$cmdPath = $path . '/' . $post_id . '-' . $alt['id'] . '-hls-cmd.txt';

						$cmdH = 'ulimit -t 7200; nice ' . $options['ffmpegPath'] . ' -y -threads 1 -i ' . $alt['file'] .' -flags -global_header -map 0 -f segment -segment_list ' . $alt['hls'] . '/index.m3u8 -segment_time 10 -segment_format mpeg_ts -segment_list_type m3u8 '. $alt['hls'] .'/segment%05d.ts' . ' &>' . $logPath . ' &';

						//if input exists start now, otherwise start later
						if (!$options['convertSingleProcess'] && file_exists($alt['file'])) VWvideoShare::convertAdd($cmdH);
						else $cmdHS[] = $cmdH;

						exec("echo '$cmdH' >> $cmdPath", $output, $returnvalue);

					}

					//update alternatives info
					$videoAlts[$alt['id']] = $alt;

				}

			//run all conversions in a single process (one input, multiple outputs)
			if ($options['convertSingleProcess'])
			{
				$logPath = $path . '/' . $post_id . '-convert.txt';
				$cmdPath = $path . '/' . $post_id . '-convert-cmd.txt';

				$cmd = 'ulimit -t 7200; nice ' . $options['ffmpegPath'] . ' -y -threads 1 -i ' . $videoPath . $cmdW . ' ' . $cmdS . ' &>' . $logPath . ' &';

				VWvideoShare::convertAdd($cmd);
				exec("echo '$cmd' >> $cmdPath", $output, $returnvalue);

			}

			//after conversions, do segmentations
			if ($options['convertHLS']) if ($cmdHS) if (count($cmdHS))
						foreach ($cmdHS as $cmdH) VWvideoShare::convertAdd($cmdH);

						update_post_meta( $post_id, 'video-adaptive', $videoAlts );
		}

		function convertAdd($cmd)
		{
			$options = get_option( 'VWvideoShareOptions' );

			if ($options['convertInstant']) exec($cmd, $output, $returnvalue);
			else
				if (!strstr($options['convertQueue'], $cmd))
				{
					$options['convertQueue'] .= ($options['convertQueue']?"\r\n":'') . $cmd;
					update_option('VWvideoShareOptions', $options);
					VWvideoShare::convertProcessQueue();
				}

		}

		function convertProcessQueue($verbose=0)
		{
			$options = get_option( 'VWvideoShareOptions' );

			//not more often than 10s
			if (!VWvideoShare::timeTo($room . '/processQueue', 10, $options)) return;

			//detect if ffmpeg is running
			$cmd = "ps aux | grep '" . $options['ffmpegPath'] . ' -y -threads 1 -i'  .  "'";
			exec($cmd, $output, $returnvalue);

			$transcoding = 0;
			foreach ($output as $line)
				if (!strstr($line, 'grep'))
				{
					$columns = preg_split('/\s+/',$line);
					if ($verbose) echo ($transcoding?'':'<br>FFMPEG Active:') . '<br>' . $line . '';
					$transcoding = 1;
				}


			if (!$transcoding)
			{
				if ($verbose) echo '<BR>No conversion process detected. System is available to start new conversions.';

				//extract first command
				$cmds = explode("\r\n", trim($options['convertQueue']));
				$cmd = array_shift($cmds);

				//save new queue
				$options['convertQueue'] = implode("\r\n", $cmds);
				update_option('VWvideoShareOptions', $options);

				if ($cmd)
				{
					$output = '';
					exec($cmd, $output, $returnvalue);
					if ($verbose)
					{
						echo '<BR>Starting: '. $cmd;
						if (is_array($output)) foreach ($output as $line) echo '<br>' . $line;
					}

				}
			}


		}

		//! Snapshots
		function generateSnapshots($post_id)
		{
			if (!$post_id) return;

			$videoPath = get_post_meta($post_id, 'video-source-file', true);
			if (!$videoPath) return;

			$options = get_option( 'VWvideoShareOptions' );

			$path =  dirname($videoPath);
			$imagePath =  $path . '/' . $post_id . '.jpg';
			$thumbPath =  $path . '/' . $post_id . '_thumb.jpg';
			$logPath = $path . '/' . $post_id . '-snap.txt';
			$cmdPath = $path . '/' . $post_id . '-snap-cmd.txt';

			$snapTime = 9;
			$videoDuration = get_post_meta($post_id, 'video-duration', true);
			if ($videoDuration) if ($videoDuration < $snapTime) $snapTime = floor($videoDuration/2);

				$cmd = $options['ffmpegPath'] . ' -y -i "'.$videoPath.'" -ss 00:00:0' . $snapTime . '.000 -f image2 -vframes 1 "' . $imagePath . '" >& ' . $logPath .' &';

			exec($cmd, $output, $returnvalue);
			exec("echo '$cmd' >> $cmdPath", $output, $returnvalue);

			update_post_meta( $post_id, 'video-snapshot', $imagePath );

			//probably source snap not ready, yet
			update_post_meta( $post_id, 'video-thumbnail', $thumbPath );

			list($width, $height) = VWvideoShare::generateThumbnail($imagePath, $thumbPath);
			if ($width) update_post_meta( $post_id, 'video-width', $width );
			if ($height) update_post_meta( $post_id, 'video-height', $height );
		}


		function generateThumbnail($src, $dest)
		{
			if (!file_exists($src)) return;

			$options = get_option( 'VWvideoShareOptions' );

			//generate thumb
			$thumbWidth = $options['thumbWidth'];
			$thumbHeight = $options['thumbHeight'];

			$srcImage = @imagecreatefromjpeg($src);
			if (!$srcImage) return;

			list($width, $height) = getimagesize($src);

			$destImage = imagecreatetruecolor($thumbWidth, $thumbHeight);

			imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
			imagejpeg($destImage, $dest, 95);

			//return source dimensions
			return array($width, $height);
		}


		function updatePostThumbnail($post_id, $overwrite = false, $verbose = false)
		{
			$imagePath = get_post_meta($post_id, 'video-snapshot', true);
			$thumbPath = get_post_meta($post_id, 'video-thumbnail', true);

			if ($verbose)  echo "<br>Updating thumbnail ($post_id, $imagePath,  $thumbPath)";

			if (!$imagePath) VWvideoShare::generateSnapshots($post_id);
			elseif (!file_exists($imagePath)) VWvideoShare::generateSnapshots($post_id);
			elseif ($overwrite) VWvideoShare::generateSnapshots($post_id);

			if (!$thumbPath) VWvideoShare::generateSnapshots($post_id);
			elseif (!file_exists($thumbPath)) list($width, $height) = VWvideoShare::generateThumbnail($imagePath, $thumbPath);
			else
			{
				if ($overwrite) list($width, $height) = VWvideoShare::generateThumbnail($imagePath, $thumbPath);

				if (!get_the_post_thumbnail($post_id)) //insert if missing
					{
					$wp_filetype = wp_check_filetype(basename($thumbPath), null );

					$attachment = array(
						'guid' => $thumbPath,
						'post_mime_type' => $wp_filetype['type'],
						'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $thumbPath, ".jpg" ) ),
						'post_content' => '',
						'post_status' => 'inherit'
					);


					// Insert the attachment.
					$attach_id = wp_insert_attachment( $attachment, $thumbPath, $post_id );
					set_post_thumbnail($post_id, $attach_id);
				}
				else //just update
					{
					$attach_id = get_post_thumbnail_id($post_id );
					//$thumbPath = get_attached_file($attach_id);
				}

				// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
				require_once( ABSPATH . 'wp-admin/includes/image.php' );


				// Generate the metadata for the attachment, and update the database record.
				$attach_data = wp_generate_attachment_metadata( $attach_id, $thumbPath );
				wp_update_attachment_metadata( $attach_id, $attach_data );

				if ($verbose) var_dump($attach_data);


			}

			if ($width) update_post_meta( $post_id, 'video-width', $width );
			if ($height) update_post_meta( $post_id, 'video-height', $height );

			//do any conversions after detection
			VWvideoShare::convertVideo($post_id);
		}

		function updateVideo($post_id, $overwrite = false)
		{

			if (!$post_id) return;

			$videoPath = get_post_meta($post_id, 'video-source-file', true);
			if (!$videoPath) return; //source missing

			$videoDuration = get_post_meta($post_id, 'video-duration', true);
			if ($videoDuration && !$overwrite) return;

			$options = get_option( 'VWvideoShareOptions' );

			$path =  dirname($videoPath);
			$logPath = $path . '/' . $post_id . '-dur.txt';
			$cmdPath = $path . '/' . $post_id . '-dur-cmd.txt';

			$cmd = $options['ffmpegPath'] . ' -y -i "'.$videoPath.'" 2>&1';

			$info = shell_exec($cmd);
			exec("echo '$info' >> $logPath", $output, $returnvalue);
			exec("echo '$cmd' >> $cmdPath", $output, $returnvalue);

			//duration
			preg_match('/Duration: (.*?),/', $info, $matches);
			$duration = explode(':', $matches[1]);

			$videoDuration = intval($duration[0]) * 3600 + intval($duration[1]) * 60 + intval($duration[2]);
			if ($videoDuration) update_post_meta( $post_id, 'video-duration', $videoDuration );

			//bitrate
			preg_match('/bitrate:\s(?<bitrate>\d+)\skb\/s/', $info, $matches);
			$videoBitrate = $matches['bitrate'];
			if ($videoBitrate) update_post_meta( $post_id, 'video-bitrate', $videoBitrate );

			$videoSize = filesize($videoPath);
			if ($videoSize) update_post_meta( $post_id, 'video-source-size', $videoSize );

			//get resolution
			if(strpos($info, 'Video:') !== false)
			{
				preg_match('/\s(?<width>\d+)[x](?<height>\d+)\s\[/', $info, $matches);
				$width = $matches['width'];
				$height = $matches['height'];

				if ($width) update_post_meta( $post_id, 'video-width', $width );
				if ($height) update_post_meta( $post_id, 'video-height', $height );
			}

			//codecs

			//video
			if (!preg_match('/Stream #(?:[0-9\.]+)(?:.*)\: Video: (?P<videocodec>.*)/',$info,$matches))
				preg_match('/Could not find codec parameters \(Video: (?P<videocodec>.*)/',$info,$matches);
			list($videoCodec) = explode(' ',$matches[1]);
			if ($videoCodec) update_post_meta( $post_id, 'video-codec-video', strtolower($videoCodec) );

			//audio
			$matches = array();
			if (!preg_match('/Stream #(?:[0-9\.]+)(?:.*)\: Audio: (?P<audiocodec>.*)/',$info,$matches))
				preg_match('/Could not find codec parameters \(Audio: (?P<audiocodec>.*)/',$info,$matches);

			//var_dump($matches);

			list($videoCodecAudio) = explode(' ',$matches[1]);
			if ($videoCodecAudio) update_post_meta( $post_id, 'video-codec-audio', strtolower($videoCodecAudio) );

			//do any conversions after detection
			VWvideoShare::convertVideo($post_id);

			return $videoDuration;
		}

		//! VideoWhisper Live Streaming integration filters
		function vw_ls_manage_channel($val, $cid)
		{
			return do_shortcode("[videowhisper_postvideos post=\"$cid\"]");
		}


		function vw_ls_manage_channels_head($val)
		{
			return do_shortcode("[videowhisper_postvideos_process post_type=\"channel\"]");
		}

		//! Utility Functions
		function humanDuration($t,$f=':') // t = seconds, f = separator
			{
			return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
		}

		function humanAge($t)
		{
			if ($t<30) return "NOW";
			return sprintf("%d%s%d%s%d%s", floor($t/86400), 'd ', ($t/3600)%24,'h ', ($t/60)%60,'m') . ' ago';
		}


		function humanFilesize($bytes, $decimals = 2) {
			$sz = 'BKMGTP';
			$factor = floor((strlen($bytes) - 1) / 3);
			return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
		}

		function path2url($file, $Protocol='http://')
		{
			$url = $Protocol.$_SERVER['HTTP_HOST'];


			//on godaddy hosting uploads is in different folder like /var/www/clients/ ..
			$upload_dir = wp_upload_dir();
			if (strstr($file, $upload_dir['basedir']))
				return  $upload_dir['baseurl'] . str_replace($upload_dir['basedir'], '', $file);

			if (strstr($file, $_SERVER['DOCUMENT_ROOT']))
				return  $url . str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);

			return $url . $file;
		}

		function path2stream($path, $withExtension=true)
		{
			$options = get_option( 'VWvideoShareOptions' );

			$stream = substr($path, strlen($options['streamsPath']));
			if ($stream[0]=='/') $stream = substr($stream, 1);

			if (!file_exists($options['streamsPath'] . '/' . $stream)) return '';
			elseif ($withExtension) return $stream;
			else return pathinfo($stream, PATHINFO_FILENAME);
		}

		//! import
		function importFilesClean()
		{
			$options = get_option( 'VWvideoShareOptions' );

			if (!$options['importClean']) return;
			if (!file_exists($options['importPath'])) return;
			if (!file_exists($options['uploadsPath'])) return;

			//last cleanup
			$lastFile = $options['uploadsPath'] . '/importCleanLast.txt';
			if (file_exists($lastFile)) $lastClean = file_get_contents($lastFile);

			//cleaned recently
			if ($lastClean > time()-36000) return;

			//start clean

			//save time
			$myfile = fopen($lastFile, "w");
			if (!$myfile) return;
			fwrite($myfile, time());
			fclose($myfile);

			//scan files and clean
			$folder = $options['importPath'];
			$extensions = VWvideoShare::extensions_flash();
			$ignored = array('.', '..', '.svn', '.htaccess');
			$expirationTime = time() - $options['importClean'] * 86400;

			$fileList = scandir($folder);
			foreach ($fileList as $fileName)
			{
				if (in_array($fileName, $ignored)) continue;
				if (!in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), $extensions  )) continue;

				if (filemtime($folder . $fileName) < $expirationTime) unlink($folder . $fileName);
			}


		}

		function importFilesSelect($prefix, $extensions, $folder)
		{
			if (!file_exists($folder)) return "<div class='error'>Video folder not found: $folder !</div>";

			VWvideoShare::importFilesClean();


			$htmlCode .= '';

			//import files
			if ($_POST['import'])
			{

				if (count($importFiles = $_POST['importFiles']))
				{

					$owner = (int) $_POST['owner'];

					global $current_user;
					get_currentuserinfo();

					if (!$owner) $owner = $current_user->ID;
					elseif ($owner != $current_user->ID && ! current_user_can('edit_users')) return "Only admin can import for others!";

					//handle one or many playlists
					$playlist = $_POST['playlist'];

					//if csv sanitize as array
					if (strpos($playlist, ',') !== FALSE)
					{
						$playlists = explode(',', $playlist);
						foreach ($playlists as $key => $value) $playlists[$key] = sanitize_file_name(trim($value));
						$playlist = $playlists;
					}

					if (!$playlist) return "Importing requires a playlist name!";

					//handle one or many tags
					$tag = $_POST['tag'];

					//if csv sanitize as array
					if (strpos($tag, ',') !== FALSE)
					{
						$tags = explode(',', $playlist);
						foreach ($tags as $key => $value) $tags[$key] = sanitize_file_name(trim($value));
						$tag = $tags;
					}

					$description = sanitize_text_field($_POST['description']);

					$category = sanitize_file_name($_POST['category']);

					foreach ($importFiles as $fileName)
					{
						//$fileName = sanitize_file_name($fileName);
						$ext = pathinfo($fileName, PATHINFO_EXTENSION);
						if (!$ztime = filemtime($folder . $fileName)) $ztime = time();
						$videoName = basename($fileName, '.' . $ext) .' '. date("M j", $ztime);

						$htmlCode .= VWvideoShare::importFile($folder . $fileName, $videoName, $owner, $playlist, $category, $tag, $description);
					}
				}else $htmlCode .= '<div class="warning">No files selected to import!</div>';

			}

			//delete files
			if ($_POST['delete'])
			{

				if (count($importFiles = $_POST['importFiles']))
				{
					foreach ($importFiles as $fileName)
					{
						$htmlCode .= '<BR>Deleting '.$fileName.' ... ';
						$fileName = sanitize_file_name($fileName);
						if (!unlink($folder . $fileName)) $htmlCode .= 'Removing file failed!';
						else $htmlCode .= 'Success.';

					}
				}else $htmlCode .= '<div class="warning">No files selected to delete!</div>';
			}

			//preview file
			if ($preview_name = $_GET['import_preview'])
			{
				//$preview_name = sanitize_file_name($preview_name);
				$preview_url = VWvideoShare::path2url($folder . $preview_name);
				$player_url = plugin_dir_url(__FILE__) . 'strobe/StrobeMediaPlayback.swf';
				$flashvars ='src=' .urlencode($preview_url). '&autoPlay=true';

				$htmlCode .= '<h4>Preview '.$preview_name.'</h4>';

				$htmlCode .= '<object class="previewPlayer" width="480" height="360" type="application/x-shockwave-flash" data="' . $player_url . '"> <param name="movie" value="' . $player_url . '" /><param name="flashvars" value="' .$flashvars . '" /><param name="allowFullScreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="wmode" value="direct" /></object>';
			}

			//list files
			$fileList = scandir($folder);

			$ignored = array('.', '..', '.svn', '.htaccess');

			$prefixL=strlen($prefix);

			//list by date
			$files = array();
			foreach ($fileList as $fileName)
			{

				if (in_array($fileName, $ignored)) continue;
				if (!in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), $extensions  )) continue;
				if ($prefixL) if (substr($fileName,0,$prefixL) != $prefix) continue;

					$files[$fileName] = filemtime($folder . $fileName);
			}

			arsort($files);
			$fileList = array_keys($files);

			if (!$fileList) $htmlCode .=  "<div class='warning'>No matching videos found!</div>";
			else
			{
				$htmlCode .=
					'<script language="JavaScript">
function toggleImportBoxes(source) {
  var checkboxes = new Array();
  checkboxes = document.getElementsByName(\'importFiles\');
  for (var i = 0; i < checkboxes.length; i++)
    checkboxes[i].checked = source.checked;
}
</script>';
				$htmlCode .=  "<table class='widefat videowhisperTable'>";
				$htmlCode .=  '<thead class=""><tr><th><input type="checkbox" onClick="toggleImportBoxes(this)" /></th><th>File Name</th><th>Preview</th><th>Size</th><th>Date</th></tr></thead>';

				$tN = 0;
				$tS = 0;

				foreach ($fileList as $fileName)
				{
					$fsize = filesize($folder . $fileName);
					$tN++;
					$tS += $fsize;

					$htmlCode .=  '<tr>';
					$htmlCode .= '<td><input type="checkbox" name="importFiles[]" value="' . $fileName .'"'. ($fileName==$preview_name?' checked':'').'></td>';
					$htmlCode .=  "<td>$fileName</td>";
					$htmlCode .=  '<td>';
					$link  = add_query_arg( array( 'playlist_import' => $prefix, 'import_preview' => $fileName), get_permalink() );

					$htmlCode .=  " <a class='button size_small g-btn type_blue' href='" . $link ."'>Play</a> ";
					echo '</td>';
					$htmlCode .=  '<td>' .  VWvideoShare::humanFilesize($fsize) . '</td>';
					$htmlCode .=  '<td>' .  date('jS F Y H:i:s', filemtime($folder  . $fileName)) . '</td>';
					$htmlCode .=  '</tr>';
				}
				$htmlCode .=  '<tr><td></td><td>'.$tN.' files</td><td></td><td>'.VWvideoShare::humanFilesize($tS).'</td><td></td></tr>';
				$htmlCode .=  "</table>";

			}
			return $htmlCode;

		}

		function importFilesCount($prefix, $extensions, $folder)
		{
			if (!file_exists($folder)) return '';

			$kS=$k=0;

			$fileList = scandir($folder);

			$ignored = array('.', '..', '.svn', '.htaccess');

			$prefixL=strlen($prefix);

			foreach ($fileList as $fileName)
			{

				if (in_array($fileName, $ignored)) continue;
				if (!in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), $extensions  )) continue;
				if ($prefixL) if (substr($fileName,0,$prefixL) != $prefix) continue;

					$k++;
				$kS+=filesize($folder . $fileName);
			}

			return $k . ' ('.VWvideoShare::humanFilesize($kS).')';
		}


		public static function importFile($path, $name, $owner, $playlists, $category = '', $tags = '', $description = '')
		{
			if (!$owner) return "<br>Missing owner!";
			if (!$playlists) return "<br>Missing playlists!";

			$options = get_option( 'VWvideoShareOptions' );
			if (!VWvideoShare::hasPriviledge($options['shareList'])) return '<br>' . __('You do not have permissions to share videos!', 'video-share-vod');

			if (!file_exists($path)) return "<br>$name: File missing: $path";


			//handle one or many playlists
			if (is_array($playlists)) $playlist = sanitize_file_name(current($playlists));
			else $playlist = sanitize_file_name($playlists);

			if (!$playlist) return "<br>Missing playlist!";

			$htmlCode = '';

			//uploads/owner/playlist/src/file
			$dir = $options['uploadsPath'];
			if (!file_exists($dir)) mkdir($dir);

			$dir .= '/' . $owner;
			if (!file_exists($dir)) mkdir($dir);

			$dir .= '/' . $playlist;
			if (!file_exists($dir)) mkdir($dir);

			//$dir .= '/src';
			//if (!file_exists($dir)) mkdir($dir);

			if (!$ztime = filemtime($path)) $ztime = time();

			$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
			$newFile = md5(uniqid($owner, true))  . '.' . $ext;
			$newPath = $dir . '/' . $newFile;

			//$htmlCode .= "<br>Importing $name as $newFile ... ";

			if ($options['deleteOnImport'])
			{
				if (!rename($path, $newPath))
				{
					$htmlCode .= 'Rename failed. Trying copy ...';
					if (!copy($path, $newPath))
					{
						$htmlCode .= 'Copy also failed. Import failed!';
						return $htmlCode;
					}
					// else $htmlCode .= 'Copy success ...';

					if (!unlink($path)) $htmlCode .= 'Removing original file failed!';
				}
			}
			else
			{
				//just copy
				if (!copy($path, $newPath))
				{
					$htmlCode .= 'Copy failed. Import failed!';
					return $htmlCode;
				}
			}

			//$htmlCode .= 'Moved source file ...';

			$postdate = date("Y-m-d H:i:s", $ztime);

			$post = array(
				'post_name'      => $name,
				'post_title'     => $name,
				'post_author'    => $owner,
				'post_type'      => $options['custom_post'],
				'post_status'    => 'publish',
				'post_date'   => $postdate,
				'post_content'   => $description
			);

			if (!VWvideoShare::hasPriviledge($options['publishList']))
				$post['post_status'] = 'pending';

			$post_id = wp_insert_post( $post);
			if ($post_id)
			{
				update_post_meta( $post_id, 'video-source-file', $newPath );

				wp_set_object_terms($post_id, $playlists, $options['custom_taxonomy']);

				if ($tags) wp_set_object_terms($post_id, $tags, 'post_tag');

				if ($category) wp_set_post_categories($post_id, array($category));

				VWvideoShare::updateVideo($post_id, true);
				VWvideoShare::updatePostThumbnail($post_id, true);
				//VWvideoShare::convertVideo($post_id, true);

				if ($post['post_status'] == 'pending') $htmlCode .= __('Video was submitted and is pending approval.','video-share-vod');
				else
					$htmlCode .= '<br>' . __('Video was published', 'video-share-vod') . ': <a href='.get_post_permalink($post_id).'> #'.$post_id.' '.$name.'</a> <br>' . __('Snapshot, video info and thumbnail will be processed shortly.', 'video-share-vod') ;
			}
			else $htmlCode .= '<br>Video post creation failed!';

			return $htmlCode;
		}

		//! Admin Area
		/* Meta box setup function. */
		function post_meta_boxes_setup() {
			/* Add meta boxes on the 'add_meta_boxes' hook. */
			add_action( 'add_meta_boxes', array( 'VWvideoShare', 'add_post_meta_boxes' ) );

			/* Update post meta on the 'save_post' hook. */
			add_action( 'save_post', array( 'VWvideoShare', 'save_post_meta'), 10, 2);

		}


		/* Create one or more meta boxes to be displayed on the post editor screen. */
		function add_post_meta_boxes() {

			add_meta_box(
				'video-post',      // Unique ID
				esc_html__( 'Video Post' ),    // Title
				array( 'VWvideoShare', 'post_meta_box'),   // Callback function
				'video',         // Admin page (or post type)
				'normal',         // Context
				'high'         // Priority
			);


		}

		/* Display the post meta box. */
		function post_meta_box( $object, $box ) {
?>
 <p>This is a special post type: In backend, videos can be uploaded from Video Share VOD > Upload menu or imported from Video Share VOD > Import menu, if files are already on server.
	 <br>Videos can also be added (uploaded or imported) from frontend if sections are setup (see <a href="http://videosharevod.com/features/quick-start-tutorial/">Setup Tutorial</a> and <a href="admin.php?page=video-share-docs">Plugin Documentation</a>).
	 <br>Custom fields are automatically generated and updated by the plugin. Do not alter custom fields manually as this can result in unexpected behaviour.
  </p>
<?php

		}


		function save_post_meta( $post_id, $post )
		{

			$options = get_option( 'VWvideoShareOptions' );

			//tv show : setup seasons
			if ($post->post_type == $options['tvshows_slug'])
			{
				$meta_value = get_post_meta( $post_id, 'tvshow-seasons', true );
				if (!$meta_value)
				{
					update_post_meta( $post_id, 'tvshow-seasons', '1');
					$meta_value = 1;
				}

				if ($post->post_title)
				{
					if (!term_exists($post->post_title, $options['custom_taxonomy']))
					{
						$args = array( 'description' => 'TV Show: ' . $post->post_title);
						wp_insert_term($post->post_title, $options['custom_taxonomy']);
					}

					$term = get_term_by('name', $post->post_title, $options['custom_taxonomy']);

					if ($meta_value>1) for ($i=1; $i<=$meta_value; $i++)
						if (!term_exists($post->post_title . ' ' . $i, $options['custom_taxonomy']))
						{
							$args = array('parent' => $term->term_id, 'description' => 'TV Show: ' . $post->post_title);

							wp_insert_term($post->post_title . ' ' . $i, $options['custom_taxonomy'], $args);

						}
				}
			}

		}

		//! Admin Videos
		function columns_head_video($defaults) {
			$defaults['featured_image'] = 'Thumbnail';
			$defaults['duration'] = 'Duration &amp; Info';

			return $defaults;
		}

		function columns_register_sortable( $columns ) {
			$columns['duration'] = 'duration';

			return $columns;
		}


		function columns_content_video($column_name, $post_id)
		{

			if ($column_name == 'featured_image')
			{

				$post_thumbnail_id = get_post_thumbnail_id($post_id);

				if ($post_thumbnail_id)
				{

					$post_featured_image = wp_get_attachment_image_src($post_thumbnail_id, 'featured_preview');

					if ($post_featured_image)
					{
						//correct url

						$upload_dir = wp_upload_dir();
						$uploads_url = VWvideoShare::path2url($upload_dir['basedir']);

						$iurl = $post_featured_image[0];
						$relPath = substr($iurl,strlen($uploads_url));

						if (file_exists($relPath)) $rurl = VWvideoShare::path2url($relPath);
						else $rurl = $iurl;

						echo '<img src="' . $rurl . '" />';
					}


					$url  = add_query_arg( array( 'updateThumb'  => $post_id), admin_url('admin.php?page=video-manage') );
					echo '<br><a href="'.$url.'">' . __('Update Thumbnail', 'video-share-vod') . '</a>';


				}
				else
				{
					echo 'Generating ... ';
					VWvideoShare::updatePostThumbnail($post_id);

				}
			}

			if ($column_name == 'duration')
			{

				$videoDuration = get_post_meta($post_id, 'video-duration', true);
				if ($videoDuration)
				{
					echo 'Duration: ' . VWvideoShare::humanDuration($videoDuration);
					echo '<br>Resolution: ' . get_post_meta($post_id, 'video-width', true). 'x' . get_post_meta($post_id, 'video-height', true);
					echo '<br>Source Size: ' . VWvideoShare::humanFilesize(get_post_meta($post_id, 'video-source-size', true));
					echo '<br>Total Space: ' . VWvideoShare::humanFilesize(VWvideoShare::spaceVideo($post_id));
					echo '<br>Bitrate: '. get_post_meta($post_id, 'video-bitrate', true) . ' kbps';

					echo '<br>Codecs: ' . ($codec = get_post_meta($post_id, 'video-codec-video', true)) . ', ' . get_post_meta($post_id, 'video-codec-audio', true);

					if (!$codec) VWvideoShare::updateVideo($post_id, true);
					echo '<br>Files: ';

					$videoPath = get_post_meta($post_id, 'video-source-file', true);
					if (file_exists($videoPath)) echo '<a href="' . VWvideoShare::path2url($videoPath) . '">source</a> ' ;

					$videoAdaptive = get_post_meta($post_id, 'video-adaptive', true);
					if ($videoAdaptive) $videoAlts = $videoAdaptive;
					else $videoAlts = array();

					foreach ($videoAlts as $alt)
						if (file_exists($alt['file'])) echo '<br><a href="' . VWvideoShare::path2url($alt['file']) . '">' . $alt['id'] . '</a> (' . $alt['bitrate'] . ' kbps)';
						else echo $alt['id'] . '.. ';

						$url  = add_query_arg( array( 'updateInfo'  => $post_id), admin_url('admin.php?page=video-manage') );
					$url2 = add_query_arg( array( 'convert'  => $post_id), admin_url('admin.php?page=video-manage') );

					echo '<br><a href="'.$url.'">' . __('Update Video', 'video-share-vod') . '</a>';
					echo '| <a href="'.$url2.'">' . __('Convert Video', 'video-share-vod') . '</a>';

				}
				else
				{
					echo 'Retrieving Info...';
					VWvideoShare::updateVideo($post_id, true);
				}

			}

		}

		public static function parse_query($query)
		{
			/*
			global $pagenow;

			if (is_admin() && $pagenow=='edit.php' && isset($_GET['post_type']) && $_GET['post_type']=='video')
			{
			}
			*/
		}

		function duration_column_orderby( $vars ) {
			if ( isset( $vars['orderby'] ) && 'duration' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
						'meta_key' => 'video-duration',
						'orderby' => 'meta_value_num'
					) );
			}

			return $vars;
		}


		function adminUpload()
		{
?>
		<div class="wrap">
<?php screen_icon(); ?>
		<h2>Video Share / Video on Demand (VOD)</h2>
		<?php
			echo do_shortcode("[videowhisper_upload]");
?>
		Use this page to upload one or multiple videos to server. Configure category, playlists and then choose files or drag and drop files to upload area.
		<br>Playlist(s): Assign videos to multiple playlists, as comma separated values. Ex: subscriber, premium
		<p><a target="_blank" href="http://videosharevod.com/features/video-uploader/">About Video Uploader ...</a></p>

		</div>
		<?php
		}

		function adminStats()
		{
			$options = get_option( 'VWvideoShareOptions' );

?>
		<div class="wrap">
		<?php screen_icon(); ?>
		<h2>Video Statistics</h2>
		<?php

			$post_count = wp_count_posts($options['custom_post']);

			echo '<h3>Video Count</h3>';
			foreach ($post_count as $key => $value)
				echo '<BR>'.$key.' : <a href="edit.php?post_type=' . $options['custom_post']. '&post_status=' .$key. '">' . $value . '</a>';

			echo '<h3>Space Count</h3>';

			function get_meta_values( $key = '', $type = 'video') {
				global $wpdb;
				if( empty( $key ) ) return;
				$r = $wpdb->get_col( $wpdb->prepare( "
        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key = '%s'
        AND p.post_type = '%s'
    ", $key, $type ) );
				return $r;
			}

			$metas = get_meta_values('space-statistics', $options['custom_post']);

			echo '<BR>Stats available for: ' . count($metas);
			$totalStats = [];

			foreach ($metas as $meta)
			{
				$spaceStats = unserialize($meta);
				foreach ($spaceStats as $key => $value)
				{
					$totalStats['total'] += $value;
					$totalStats[$key] += $value;
				}
			}

			echo '<BR>Space used by videos: ';
			foreach ($totalStats as $key => $value) echo '<BR>'.$key .': '. VWvideoShare::humanFilesize($value);

			?>
			<h3>Video Space Tools</h3>
			+ <a href="admin.php?page=video-manage&updateSpace=1">Calculate Current Space Usage for All Videos</a>
		<BR> + <a href="admin.php?page=video-manage&clean=source">Delete Sources</a>
		<BR> + <a href="admin.php?page=video-manage&clean=hls">Delete HLS Segments</a>
		<?php
		}

		function sizeTree($dir) {
			$files = array_diff(scandir($dir), array('.','..'));

			$space = 0;
			foreach ($files as $file) {
				$space += (is_dir("$dir/$file")) ? VWvideoShare::sizeTree("$dir/$file") : filesize("$dir/$file");
			}
			return $space;
		}


		function spaceVideo($post_id)
		{
			//calculate statistics for video

			if (!$post_id) return;
			$options = get_option( 'VWvideoShareOptions' );

			$spaceStatistics = [];

			//source
			$space = 0;
			$videoPath = get_post_meta($post_id, 'video-source-file', true);
			if (file_exists($videoPath)) $space = filesize($videoPath);
			$spaceStatistics['source'] = $space;

			//all generated video files
			$videoAdaptive = get_post_meta($post_id, 'video-adaptive', true);
			if ($videoAdaptive) $videoAlts = $videoAdaptive;
			else $videoAlts = array();

			$space = 0;
			foreach ($videoAlts as $alt)
			{
				if (file_exists($alt['file'])) $spaceStatistics[$alt['id']] = filesize($alt['file']);

				//hls space
				if ($alt['hls']) if (strstr($alt['hls'], $options['uploadsPath']))
					{
						if (file_exists($alt['hls'])) if (is_dir($alt['hls']))
							{
								$spaceStatistics[$alt['id'].'_hls'] =  VWvideoShare::sizeTree($alt['hls']);
							}
					}
			}

			update_post_meta( $post_id, 'space-statistics', $spaceStatistics );

			$spaceTotal = 0;
			foreach ($spaceStatistics as $value) $spaceTotal += $value;

			update_post_meta( $post_id, 'space-total', $spaceTotal );

			//var_dump($spaceStatistics);

			return $spaceTotal;
		}


		function adminManage()
		{

			$options = get_option( 'VWvideoShareOptions' );

?>
		<div class="wrap">
<?php screen_icon(); ?>
		<h2>Manage Videos</h2>
		<?php

			if ( $clean = sanitize_file_name($_GET['clean']))
			{

				echo 'Cleaning video files. Finding posts older than 3 days (to avoid processing of unconverted posts) ...';

				global $wpdb;
				$ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM  {$wpdb->posts} WHERE post_type = '%s'AND post_date < NOW() - INTERVAL 3 DAY",  $options['custom_post'] ) );

				echo '<BR>Videos to clean for: ' . count($ids);

				$confirm = $_GET['confirm'];

				foreach ($ids as $post_id)   $value += VWvideoShare::cleanVideo($post_id, $clean, $confirm);

				echo '<BR>Total clean space: ' .  VWvideoShare::humanFilesize($value);

				if ($confirm) echo '<BR>Successfully cleaned!';
				else echo '<BR>Are you sure you want to delete these files?<BR>This is not reversible: <a href="admin.php?page=video-stats&clean='.$clean.'&confirm=1">Confirm Deletion</a>';
			}

			if ( $_GET['updateSpace'])
			{
				echo '<BR>Calculating space usage for all videos...';
				global $wpdb;
				$ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM  {$wpdb->posts} WHERE post_type = '%s'",  $options['custom_post'] ) );

				echo '<BR>Videos to calculate for: ' . count($ids);

				foreach ($ids as $post_id)   $value += VWvideoShare::spaceVideo($post_id);

				echo '<BR>Total space usage calculated: ' .  VWvideoShare::humanFilesize($value);
				echo '<BR>See current <a href="admin.php?page=video-stats">Full Space Usage Statistics</a>';
			}

			if ( $update_id = (int) $_GET['updateInfo'])
			{
				echo '<BR>Updating Video #' .$update_id. '... <br>';
				VWvideoShare::updateVideo($update_id, true);
				unset($_GET['updateInfo']);

			}

			if ( $update_id = (int) $_GET['updateThumb'])
			{
				echo '<BR>Updating Thumbnail for Video #' .$update_id. '... <br>';
				VWvideoShare::updatePostThumbnail($update_id, true, true);
				unset($_GET['updateThumb']);
			}

			if ( $update_id = (int) $_GET['convert'])
			{
				echo '<BR>Converting Video #' .$update_id. '... <br>';
				VWvideoShare::convertVideo($update_id, true);
				unset($_GET['convert']);

			}
?>
		<h3>Video Information</h3>

		+ Individual videos can be managed from <a href="edit.php?post_type=<?php echo $options['custom_post']; ?>">Videos</a> section. Browsing videos updates space usage calculations.
		<BR>+ Current space usage statistics is available in <a href="admin.php?page=video-stats">Statistics</a> section.

		<h3>Clean Videos</h3>
		+ <a href="admin.php?page=video-manage&clean=source">Delete Sources</a>
		<BR> + <a href="admin.php?page=video-manage&clean=hls">Delete HLS Segments</a>

<?php
		}

		//! Documentation
		function adminDocs()
		{
?>
		<div class="wrap">
<?php screen_icon(); ?>
		<h2>Video Share / Video on Demand (VOD)</h2>
		<h3>External Documentation</h3>
		 	  + <a href="http://videosharevod.com/features/quick-start-tutorial/">Setup Tutorial</a>
		 <BR> + <a href="http://videosharevod.com/hosting/">Hosting Requirements and Options</a>
		 <BR> + <a href="http://www.videowhisper.com/?p=VideoWhisper+Script+Installation">Paid Installation</a> (on compatible hosting)
		 <BR> + <a href="http://www.videowhisper.com/tickets_submit.php">Contact Support</a> (for clarifications, custom development)

		<h3>Shortcodes</h3>

		<h4>[videowhisper_videos playlist="" category_id="" order_by="" perpage="" perrow="" select_category="1" select_order="1" select_page="1" include_css="1" id=""]</h4>
		Displays video list. Loads and updates by AJAX. Optional parameters: video playlist name, maximum videos per page, maximum videos per row.
		<br>order_by: post_date / video-views / video-lastview
		<br>select attributes enable controls to select category, order, page
		<br>include_css: includes the styles (disable if already loaded once on same page)
		<br>id is used to allow multiple instances on same page (leave blank to generate)

		<h4>[videowhisper_upload playlist="" category="" owner=""]</h4>
		Displays interface to upload videos.
		<br>playlist: If not defined owner name is used as playlist for regular users. Admins with edit_users capability can write any playlist name. Multiple playlists can be provided as comma separated values.
		<br>category: If not define a dropdown is listed.
		<br>owner: User is default owner. Only admins with edit_users capability can use different.

	   <h4>[videowhisper_import path="" playlist="" category="" owner=""]</h4>
		Displays interface to import videos.
		<br>path: Path where to import from.
		<br>playlist: If not defined owner name is used as playlist for regular users. Admins with edit_users capability can write any playlist name. Multiple playlists can be provided as comma separated values.
		<br>category: If not define a dropdown is listed.
		<br>owner: User is default owner. Only admins with edit_users capability can use different.

		<h4>[videowhisper_player video="0" player="" width=""]</h4>
		Displays video player. Video post ID is required.
		<br>Player: html5/html5-mobile/strobe/strobe-rtmp/html5-hls/ blank to use settings & detection
		<br>Width: Force a fixed width in pixels (ex: 640) and height will be adjusted to maintain aspect ratio. Leave blank to use video size.

		<h4>[videowhisper_preview video="0"]</h4>
		Displays video preview (snapshot) with link to video post. Video post ID is required.
		Used to display VOD inaccessible items.


		<h4>[videowhisper_playlist name="playlist-name"]</h4>
		Displays playlist player.


		<h4>[videowhisper_player_html source="" source_type="" source_alt="" source_alt_type="" poster="" width="" height="" player=""]</h4>
		Displays configured HTML5 player for a specified video source.
		<br>Player: native/wordpress/video-js/ leave blank to use settings & detection
		<br>source_alt, source_alt_type for multi bitrate source & type like m3u8 supported by videojs
		<br>Ex. [videowhisper_player_html source="http://test.com/test.mp4" type="video/mp4" poster="http://test.com/test.jpg"]

		<h4>[videowhisper_embed_code source="" source_type="" poster="" width="" height=""]</h4>
		Displays html5 embed code.

	<h4>[videowhisper_postvideos post="post id"]</h4>
		Manage post associated videos. Required: post

	<h4>[videowhisper_postvideos_process post="" post_type=""]</h4>
		Process post associated videos (needs to be on same page with [videowhisper_postvideos] for that to work).

		<h3>Troubleshooting</h3>
		+ Check FFMPEG installation and codecs in <a href="admin.php?page=video-share&tab=server">server tab</a>.
		<br>+ Troubleshoot conversions in <a href="admin.php?page=video-share&tab=convert">conversions tab</a>.
		<br>+ If playlists don't show up right on your theme, copy taxonomy-playlist.php from this plugin folder to your theme folder.
		<h3>More...</h3>
		Read more details about <a href="http://videosharevod.com/features/">available features</a> on <a href="http://videosharevod.com/">official plugin site</a> and <a href="http://www.videowhisper.com/tickets_submit.php">contact us</a> anytime for questions, clarifications.
		</div>
		<?php
		}


		//! Settings

		function adminOptionsDefault()
		{
			$root_url = get_bloginfo( "url" ) . "/";
			$upload_dir = wp_upload_dir();

			return array(
				'allowDebug' => '1',

				'disableSetupPages' => '0',
				'vwls_playlist' => '1',

				'vwls_archive_path' =>'/home/youraccount/public_html/streams/',
				'importPath' => '/home/youraccount/public_html/streams/',
				'importClean' => '45',
				'deleteOnImport' => '1',

				'vwls_channel' => '1',
				'ffmpegPath' => '/usr/local/bin/ffmpeg',
				'convertSingleProcess' => '0',
				'convertQueue' => '',
				'convertInstant' => '0',
				'convertMobile' => '1',
				'convertHigh' => '2',
				'convertHLS' => '1',
				'convertWatermark' => plugin_dir_path(__FILE__) .'logo.png',
				'convertWatermarkPosition' => 'main_w-overlay_w-4:4',
				'originalBackup' => '1',

				'custom_post' => 'video',
				'custom_taxonomy' => 'playlist',

				'postTemplate' => '+plugin',
				'playlistTemplate' => '+plugin',

				'videoWidth' => '',

				'player_default' => 'html5',
				'html5_player' => 'video-js',
				'player_ios' => 'html5-mobile',
				'player_safari' => 'html5',
				'player_android' => 'html5-mobile',
				'player_firefox_mac' =>'strobe',
				'playlist_player' => 'video-js',

				'thumbWidth' => '240',
				'thumbHeight' => '180',
				'perPage' =>'6',
				'perRow' =>'0',

				'playlistVideoWidth' => '960',
				'playlistListWidth' => '350',

				'shareList' => 'Super Admin, Administrator, Editor, Author, Contributor',
				'publishList' => 'Super Admin, Administrator, Editor, Author',
				'embedList' => 'Super Admin, Administrator, Editor, Author, Contributor, Subscriber, Guest',

				'watchList' => 'Super Admin, Administrator, Editor, Author, Contributor, Subscriber, Guest',
				'accessDenied' => '<h3>Access Denied</h3>
<p>#info#</p>',
				'vod_role_playlist' => '1',
				'vastLib' => 'iab',
				'vast' => '',
				'adsGlobal' => '0',
				'premiumList' => '',
				'tvshows' => '1',
				'tvshows_slug' => 'tvshow',
				'uploadsPath' => $upload_dir['basedir'] . '/vw_videoshare',
				'rtmpServer' => 'rtmp://your-site.com/videowhisper-x2',
				'streamsPath' =>'/home/youraccount/public_html/streams/',
				'hlsServer' =>'http://your-site.com:1935/videowhisper-x2/',
				'customCSS' => <<<HTMLCODE
<style type="text/css">

.videowhisperVideo
{
position: relative;
display:inline-block;

border:1px solid #aaa;
background-color:#777;
padding: 0px;
margin: 2px;

width: 240px;
height: 180px;
}

.videowhisperVideo:hover {
	border:1px solid #fff;
}

.videowhisperVideo IMG
{
padding: 0px;
margin: 0px;
border: 0px;
}

.videowhisperVideoTitle
{
position: absolute;
top:0px;
left:0px;
right:40px;
margin:8px;
font-size: 14px;
color: #FFF;
text-shadow:1px 1px 1px #333;
}

.videowhisperVideoDuration
{
position: absolute;
bottom:0px;
left:0px;
margin:8px;
font-size: 12px;
color: #FFF;
text-shadow:1px 1px 1px #333;
background: rgba(30, 30, 30, 0.5);
padding: 3px;
border-radius: 3px;
}

.videowhisperVideoResolution
{
position: absolute;
top:0px;
right:0px;
margin:8px;
font-size: 12px;
color: #FFF;
text-shadow:1px 1px 1px #333;
background: rgba(255, 50, 0, 0.5);
padding: 3px;
border-radius: 3px;
}

.videowhisperVideoDate
{
position: absolute;
bottom:0px;
right:0px;
margin: 8px;
font-size: 11px;
color: #FFF;
text-shadow:1px 1px 1px #333;
}

.videowhisperVideoViews
{
position: absolute;
bottom:11px;
right:0px;
margin: 8px;
font-size: 10px;
color: #FFF;
text-shadow:1px 1px 1px #333;
}


.videowhisperDropdown {
    border: 1px solid #111;
    border-radius: 4px;
    overflow: hidden;
    background: #eee;
    width: 240px;
   display: inline-block;
}

.videowhisperSelect {
    width: 100%;
    border: none;
    box-shadow: none;
    background: transparent;
    background-image: none;
    -webkit-appearance: none;
   display: inline-block;
}

.videowhisperSelect:focus {
    outline: none;
}

.videowhisperButton {
	-moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
	-webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
	box-shadow:inset 0px 1px 0px 0px #ffffff;
	-webkit-border-top-left-radius:6px;
	-moz-border-radius-topleft:6px;
	border-top-left-radius:6px;
	-webkit-border-top-right-radius:6px;
	-moz-border-radius-topright:6px;
	border-top-right-radius:6px;
	-webkit-border-bottom-right-radius:6px;
	-moz-border-radius-bottomright:6px;
	border-bottom-right-radius:6px;
	-webkit-border-bottom-left-radius:6px;
	-moz-border-radius-bottomleft:6px;
	border-bottom-left-radius:6px;
	text-indent:0;
	border:1px solid #dcdcdc;
	display:inline-block;
	color:#666666;
	font-family:Verdana;
	font-size:15px;
	font-weight:bold;
	font-style:normal;
	height:50px;
	line-height:50px;
	width:200px;
	text-decoration:none;
	text-align:center;
	text-shadow:1px 1px 0px #ffffff;
	background-color:#e9e9e9;

}

.videowhisperButton:hover {
	background-color:#f9f9f9;
}

.videowhisperButton:active {
	position:relative;
	top:1px;
}
</style>
HTMLCODE
				,
				'crossdomain_xml' =>'<cross-domain-policy>
<allow-access-from domain="*"/>
<site-control permitted-cross-domain-policies="master-only"/>
</cross-domain-policy>',

				'videowhisper' => '0'
			);

		}

		function setupOptions() {

			$adminOptions = VWvideoShare::adminOptionsDefault();

			$options = get_option('VWvideoShareOptions');
			if (!empty($options)) {
				foreach ($options as $key => $option)
					$adminOptions[$key] = $option;
			}
			update_option('VWvideoShareOptions', $adminOptions);

			return $adminOptions;
		}



		function adminOptions()
		{
			$options = VWvideoShare::setupOptions();

			// if ($options['convertQueue']) $options['convertQueue'] = trim($options['convertQueue']);


			if (isset($_GET['cancelConversions']))
			{
				$options['convertQueue'] = '';
				update_option('VWvideoShareOptions', $options);
			}

			if (isset($_POST))
			{
				foreach ($options as $key => $value)
					if (isset($_POST[$key])) $options[$key] = trim($_POST[$key]);
					update_option('VWvideoShareOptions', $options);
			}


			VWvideoShare::setupPages();

			$optionsDefault = VWvideoShare::adminOptionsDefault();

			$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'server';
?>


<div class="wrap">
<?php screen_icon(); ?>
<h2>Video Share / Video on Demand (VOD)</h2>
<h2 class="nav-tab-wrapper">
	<a href="<?php echo get_permalink(); ?>admin.php?page=video-share&tab=server" class="nav-tab <?php echo $active_tab=='server'?'nav-tab-active':'';?>"><?php _e('Server','video-share-vod'); ?></a>
	<a href="<?php echo get_permalink(); ?>admin.php?page=video-share&tab=share" class="nav-tab <?php echo $active_tab=='share'?'nav-tab-active':'';?>"><?php _e('Video Share','video-share-vod'); ?></a>
	<a href="<?php echo get_permalink(); ?>admin.php?page=video-share&tab=convert" class="nav-tab <?php echo $active_tab=='convert'?'nav-tab-active':'';?>"><?php _e('Convert','video-share-vod'); ?></a>
	<a href="<?php echo get_permalink(); ?>admin.php?page=video-share&tab=display" class="nav-tab <?php echo $active_tab=='display'?'nav-tab-active':'';?>"><?php _e('Display','video-share-vod'); ?></a>
	<a href="<?php echo get_permalink(); ?>admin.php?page=video-share&tab=players" class="nav-tab <?php echo $active_tab=='players'?'nav-tab-active':'';?>"><?php _e('Players','video-share-vod'); ?></a>
	<a href="<?php echo get_permalink(); ?>admin.php?page=video-share&tab=ls" class="nav-tab <?php echo $active_tab=='ls'?'nav-tab-active':'';?>"><?php _e('Live Streams','video-share-vod'); ?></a>
	<a href="<?php echo get_permalink(); ?>admin.php?page=video-share&tab=tvshows" class="nav-tab <?php echo $active_tab=='tvshows'?'nav-tab-active':'';?>"><?php _e('TV Shows','video-share-vod'); ?></a>
	<a href="<?php echo get_permalink(); ?>admin.php?page=video-share&tab=vod" class="nav-tab <?php echo $active_tab=='vod'?'nav-tab-active':'';?>"><?php _e('VOD','video-share-vod'); ?></a>
	<a href="<?php echo get_permalink(); ?>admin.php?page=video-share&tab=vast" class="nav-tab <?php echo $active_tab=='vast'?'nav-tab-active':'';?>"><?php _e('VAST/IAB','video-share-vod'); ?></a>
</h2>

<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">

<?php
			switch ($active_tab)
			{

			case 'convert':
				//! convert options
?>
<h3><?php _e('Convert Videos','video-share-vod'); ?></h3>

<h4><?php _e('Conversion Queue','video-share-vod'); ?></h4>
<textarea name="convertQueue_" id="convertQueue" readonly="readonly" cols="120" rows="4"><?php echo $options['convertQueue']?></textarea>
<BR><?php
				if ($options['convertQueue'])
				{
					$cmds = explode("\r\n", $options['convertQueue']);
					if (count($cmds)) echo 'Conversions in queue: '. (count($cmds));
					echo ' <a href="'. get_permalink() . 'admin.php?page=video-share&tab=convert&cancelConversions=1'.'">Cancel Conversions</a>' ;
				}
				else echo 'No conversions in queue.';

				VWvideoShare::convertProcessQueue(1);
				echo '<BR>Next automated check (wp cron): ' . ( wp_next_scheduled( 'cron_5min_event') - time()) . 's';

?>
<h4>Conversion Watermark</h4>
<input name="convertWatermark" type="text" id="convertWatermark" size="80" maxlength="256" value="<?php echo $options['convertWatermark']?>"/>
<BR>Add a floating watermark image over video (encoded in video when converting). Specify absolute path to image file on server (PNG recommended), not web URL. Leave blank to disable.
<br>
<?php
				if (file_exists($options['convertWatermark'])) echo 'File found.';
				else echo 'Not Found! Try: ' . plugin_dir_path(__FILE__) .'logo.png';
?>
<h4>Conversion Watermark Position</h4>
<input name="convertWatermarkPosition" type="text" id="convertWatermarkPosition" size="80" maxlength="256" value="<?php echo $options['convertWatermarkPosition']?>"/>
<BR>Position for <a href="https://ffmpeg.org/ffmpeg-filters.html#overlay-1">overlay filter</a>. Default is 4px from top right corner: main_w-overlay_w-4:4


<h4><?php _e('Allow Original Video as Backup','video-share-vod'); ?></h4>
<select name="originalBackup" id="originalBackup">
  <option value="1" <?php echo ($options['originalBackup']=='1')?"selected":""?>>Yes</option>
  <option value="0" <?php echo $options['originalBackup']?"":"selected"?>>No</option>
</select>
<BR>Use original as backup playback solution if it's in appropriate format and no suitable conversion is available.
<BR>Viewer will see original video (without watermark).
<BR>This can be useful to make video accessible fast, before conversion is done, if available in suitable format.


<h4><?php _e('Convert to Mobile HTML5 Format','video-share-vod'); ?></h4>
<select name="convertMobile" id="convertMobile">
  <option value="2" <?php echo ($options['convertMobile']=='2')?"selected":""?>>Always</option>
  <option value="1" <?php echo ($options['convertMobile']=='1')?"selected":""?>>Auto</option>
  <option value="0" <?php echo $options['convertMobile']?"":"selected"?>>No</option>
</select>
<BR>Convert video to mobile quality mp4 (h264,aac).
<BR>Auto converts only if source is not mp4.
<BR>When targetting latest devices "high" format can be used for all players and "mobile" format disabled.

<h4><?php _e('Convert to High HTML5 Format','video-share-vod'); ?></h4>
<select name="convertHigh" id="convertHigh">
  <option value="3" <?php echo ($options['convertHigh']=='3')?"selected":""?>>Always</option>
  <option value="2" <?php echo ($options['convertHigh']=='2')?"selected":""?>>Auto & Bitrate</option>
  <option value="1" <?php echo ($options['convertHigh']=='1')?"selected":""?>>Auto</option>
  <option value="0" <?php echo $options['convertHigh']?"":"selected"?>>No</option>
</select>
<BR>Convert video to high quality mp4 (h264,aac).
<BR><b>Auto</b> converts only if source is not mp4 and copies h264/aac tracks if available.
<BR><b>Auto & Bitrate</b>  converts if source is not mp4 and/or bitrate is higher that <a href="http://www.videochat-scripts.com/recommended-h264-video-bitrate-based-on-resolution/">recommended</a> (which could cause interruptions, buffering for users and high server bandwidth usage without major quality benefits).
<BR><b>Always</b> will convert anyway (and apply watermark if configured).


<h4><?php _e('Generate HLS Segments','video-share-vod'); ?></h4>
<select name="convertHLS" id="convertHLS">
  <option value="1" <?php echo ($options['convertHLS']=='1')?"selected":""?>>Yes</option>
  <option value="0" <?php echo $options['convertHLS']?"":"selected"?>>No</option>
</select>
<BR>Generates static .ts segments and .m3u8 playlist for HLS playback of conversions. Accessible at /mbr/hls/[video-id].m3u8
<BR>This is a faster alternative to using a HLS server that generates segments live. Uses space but considerably improves latency and server load.
<BR>Segmentation is triggered with conversion (no conversions results in no segmentation).

<h4><?php _e('Multiple Formats in Single Process','video-share-vod'); ?></h4>
<select name="convertSingleProcess" id="convertSingleProcess">
  <option value="1" <?php echo $options['convertSingleProcess']?"selected":""?>>Yes</option>
  <option value="0" <?php echo $options['convertSingleProcess']?"":"selected"?>>No</option>
</select>
<BR>Creates all required video formats (high, mobile) in a single conversion process. This can increase overall performance (source is only read once) but involves higher memory requirements. If disabled each format is created in a different process (recommended).

<h4><?php _e('Instant Conversion','video-share-vod'); ?></h4>
<select name="convertInstant" id="convertInstant">
  <option value="1" <?php echo $options['convertInstant']?"selected":""?>>Yes</option>
  <option value="0" <?php echo $options['convertInstant']?"":"selected"?>>No</option>
</select>
<BR>Starts conversion instantly, without using a conversion queue. Not recommended as multiple conversion processes at same time could temporary freeze server and/or fail.


<h3><?php _e('Troubleshooting'); ?></h3>
This section should aid in troubleshooting conversion issues.
<?php
				$fexec=0;

				echo "<BR>exec: ";
				if(function_exists('exec'))
				{
					echo "function is enabled";

					if(exec('echo EXEC') == 'EXEC')
					{
						echo ' and works';
						$fexec =1;
					}
					else echo ' <b>but does not work</b>';

				}else echo '<b>function is not enabled</b><BR>PHP function "exec" is required to run FFMPEG. Current hosting settings are not compatible with this functionality.';

				if ($fexec)
				{
					echo "<BR>FFMPEG: ";
					$cmd = $options['ffmpegPath'] . ' -codecs';
					exec($cmd, $output, $returnvalue);
					if ($returnvalue == 127)  echo "not detected: $cmd"; else echo "detected. Main codecs:";

					//detect codecs
					if ($output) if (count($output))
							foreach (array('h264', 'vp6', 'faac','speex', 'nellymoser', 'h263', 'mpeg', 'mp3') as $cod)
							{
								$det=0; $outd="";
								echo "<BR>$cod codec: ";
								foreach ($output as $outp) if (strstr($outp,$cod)) { $det=1; $outd=$outp; };
								if ($det) echo "detected ($outd)"; else echo "missing: please configure and install ffmpeg with $cod to handle videos using this codec";
							}
				}
?>
<h4><?php _e('CloudLinux Shared Hosting Requirements'); ?></h4>
CPU Speed: FFMPEG will be called with "-thread 1" to use just 1 thread (meaning 100% of 1 cpu core). That means on cloud limited environments account will need at least 100% CPU speed (to use at least 1 full core) to run conversions.
<BR>Memory: Depending on settings, conversions can fail with "x264 [error]: malloc" error if memory limit does not permit doing conversion. While "mobile" conversion can usually be done with 512Mb memory limit, for "high" quality settings (HD) 768Mb or more would be needed.

<h4><?php _e('System Process Limitations'); ?></h4>
User limits can prevent conversions. Setting cpu limit to 7200 to prevent early termination:<br>
<?php
				if ($fexec)
				{
					$cmd = 'ulimit -t 7200; ulimit -a';
					$output ='';
					exec($cmd, $output, $returnvalue);
					foreach ($output as $outp) echo $outp.'<br>';
				}
				else echo "Not functional without exec.";

				break;


			case 'tvshows':
				//! tvshows options
?>
<h3><?php _e('TV Shows','video-share-vod'); ?></h3>

<h4><?php _e('Enable TV Shows Post Type','video-share-vod'); ?></h4>
Allows setting up TV Shows as custom post types. Plugin will automatically generate playlists for all TV shows so videos can be assigned to TV shows.
<br><select name="tvshows" id="tvshows">
  <option value="1" <?php echo $options['tvshows']?"selected":""?>>Yes</option>
  <option value="0" <?php echo $options['tvshows']?"":"selected"?>>No</option>
</select>

<h4><?php _e('TV Shows Slug','video-share-vod'); ?></h4>
<input name="tvshows_slug" type="text" id="tvshows_slug" size="16" maxlength="32" value="<?php echo $options['tvshows_slug']?>"/>
<?php
				break;
				//! server options
			case 'server':
?>
<h3><?php _e('Server Configuration','video-share-vod'); ?></h3>

<h4><?php _e('Uploads Path','video-share-vod'); ?></h4>
<p><?php _e('Path where video files will be stored. Make sure you use a location outside plugin folder to avoid losing files on updates and plugin uninstallation.','video-share-vod'); ?></p>
<input name="uploadsPath" type="text" id="uploadsPath" size="80" maxlength="256" value="<?php echo $options['uploadsPath']?>"/>
<br>Ex: /home/-your-account-/public_html/wp-content/uploads/vw_videoshare
<br>Ex: /home/-your-account-/public_html/streams/videoshare
<br>If you ever decide to change this, previous files must remain in old location.

<h4><?php _e('FFMPEG Path','video-share-vod'); ?></h4>
<p><?php _e('Path to latest FFMPEG. Required for extracting snapshots, info and converting videos.','video-share-vod'); ?></p>
<input name="ffmpegPath" type="text" id="ffmpegPath" size="100" maxlength="256" value="<?php echo $options['ffmpegPath']?>"/>
<?php

				$fexec=0;

				echo "<BR>exec: ";
				if(function_exists('exec'))
				{
					echo "function is enabled";

					if(exec('echo EXEC') == 'EXEC')
					{
						echo ' and works';
						$fexec =1;
					}
					else echo ' <b>but does not work</b>';

				}else echo '<b>function is not enabled</b><BR>PHP function "exec" is required to run FFMPEG. Current hosting settings are not compatible with this functionality.';

				if ($fexec)
				{
					echo "<BR>FFMPEG: ";
					$cmd = $options['ffmpegPath'] . ' -codecs';
					exec($cmd, $output, $returnvalue);
					if ($returnvalue == 127)  echo "not detected: $cmd"; else echo "detected";

					//detect codecs
					if ($output) if (count($output))
							foreach (array('h264', 'vp6', 'faac','speex', 'nellymoser') as $cod)
							{
								$det=0; $outd="";
								echo "<BR>$cod codec: ";
								foreach ($output as $outp) if (strstr($outp,$cod)) { $det=1; $outd=$outp; };
								if ($det) echo "detected ($outd)"; else echo "missing: please configure and install ffmpeg with $cod to handle videos using this codec";
							}
				}
?>
<br>For best experience with implementing all plugin features and site performance, take a look at these <a href="http://videosharevod.com/hosting/">premium video streaming hosting plans and servers</a> we recommend.

<h4>RTMP Address</h4>
<p>Optional: Required only for RTMP playback. Recommended: <a href="http://videosharevod.com/hosting/" target="_blank">Wowza RTMP Hosting</a>.
<br>RTMP application address for playback.</p>
<input name="rtmpServer" type="text" id="rtmpServer" size="80" maxlength="256" value="<?php echo $options['rtmpServer']?>"/>
<br>Ex: rtmp://your-site.com/videowhisper-x2
<br>Do not use a rtmp address that requires some form of authentication or verification done by another web script as player will not be able to connect.
<br>Avoid using a shared rtmp address. Setup a special rtmp application for playback of videos. For Wowza configure &lt;StreamType&gt;file&lt;/StreamType&gt;.

<h4>RTMP Streams Path</h4>
<p>Optional: Required only for RTMP playback.
<br>Path where rtmp server is configured to stream videos from. Uploads path must be a subfolder of this path to allow rtmp access to videos. </p>
<input name="streamsPath" type="text" id="streamsPath" size="80" maxlength="256" value="<?php echo $options['streamsPath']?>"/>
<br>This must be a substring of, or same as Uploads Path.
<br>Ex: /home/your-account/public_html/streams
<?php
				if (!strstr($options['uploadsPath'], $options['streamsPath']))
					echo '<br><b class="error">Current value seems wrong!</b>';
				else echo '<br>Current value seems fine.';
?>
<h4>HLS URL</h4>
<p>Optional: Required only for HLS playback.
<br>HTTP address to access by HTTP Live Streaming (HLS).</p>
<input name="hlsServer" type="text" id="hlsServer" size="80" maxlength="256" value="<?php echo $options['hlsServer']?>"/>
<br>Ex: http://your-site.com:1935/videowhisper-x2/
<br>For Wowza disable live packetizers: &lt;LiveStreamPacketizers&gt;&lt;/LiveStreamPacketizers&gt;.
<?php
				break;
			case 'ls':
				//! ls options
?>
<h3>Live Streams</h3>
Video Share VOD can import and manage videos generated by archiving live streams (from broadcasts and videochats). Multiple VideoWhisper video communication plugins can use this functionality for managing stream archives.

<h4>Path to Video Archive</h4>
<input name="vwls_archive_path" type="text" id="vwls_archive_path" size="80" maxlength="256" value="<?php echo $options['vwls_archive_path']; ?>"/>
<br>Ex: /home/your-account/public_html/streams/
<br>When using Wowza Streaming Engine configure [install-dir]/conf/Server.xml to save as FLV instead of MP4:
<br>&lt;DefaultStreamPrefix&gt;flv&lt;/DefaultStreamPrefix&gt;
<br>FLV includes support for web based flash audio codecs.


<h3>Live Streaming</h3>
<p>
Find more about <a target="_blank" href="http://videosharevod.com/features/live-streaming/">VideoShareVOD Live Streaming functionality</a> and <a href="http://broadcastlivevideo.com/">Broadcast Live Video turnkey live streaming site solution</a>. <br>
VideoWhisper Live Streaming is a plugin that allows users to broadcast live video channels.
<br>Detection:
<?php
				if (class_exists("VWliveStreaming")) echo 'Installed.';
				else
					echo 'Not detected. Please install and activate <a href="https://wordpress.org/plugins/videowhisper-live-streaming-integration/">WordPress Live Streaming plugin</a> to use this functionality.';
?>
</p>

<h4>Import Live Streaming Playlists</h4>
Enables Live Streaming channel owners to import archived streams. Videos must be archived locally.
<br><select name="vwls_playlist" id="vwls_playlist">
  <option value="1" <?php echo $options['vwls_playlist']?"selected":""?>>Yes</option>
  <option value="0" <?php echo $options['vwls_playlist']?"":"selected"?>>No</option>
</select>

<h4>List Channel Videos</h4>
List videos on channel.
<br><select name="vwls_channel" id="vwls_channel">
  <option value="1" <?php echo $options['vwls_channel']?"selected":""?>>Yes</option>
  <option value="0" <?php echo $options['vwls_channel']?"":"selected"?>>No</option>
</select>


<?php
				break;
			case 'players':
				$options['crossdomain_xml'] = htmlentities(stripslashes($options['crossdomain_xml']));

?>
<h3><?php _e('Players','video-share-vod'); ?></h3>
<?php _e('Strobe RTMP supports multi bitrate sources provided by plugin as:','video-share-vod'); ?> /mbr/rtmp/[video-id].f4m
<br><?php _e('HTML playback is supported on most browsers and devices.','video-share-vod'); ?>

<h4><?php _e('HTML5 Player','video-share-vod'); ?></h4>
<select name="html5_player" id="html5_player">
  <option value="native" <?php echo $options['html5_player']=='native'?"selected":""?>><?php _e('Native HTML5 Tag','video-share-vod'); ?></option>
  <option value="wordpress" <?php echo $options['html5_player']=='wordpress'?"selected":""?>><?php _e('WordPress Player (MediaElement.js)','video-share-vod'); ?></option>
  <option value="video-js" <?php echo $options['html5_player']=='video-js'?"selected":""?>><?php _e('Video.js','video-share-vod'); ?></option>
 </select>

<h3><?php _e('Player Compatibility','video-share-vod'); ?></h3>
<?php _e('Setup appropriate player type and video source depending on OS and browser.','video-share-vod'); ?>
<h4><?php _e('Default Player Type','video-share-vod'); ?></h4>
<select name="player_default" id="player_default">
  <option value="strobe" <?php echo $options['player_default']=='strobe'?"selected":""?>><?php _e('Strobe (Flash)','video-share-vod'); ?></option>
  <option value="html5" <?php echo $options['player_default']=='html5'?"selected":""?>><?php _e('HTML5','video-share-vod'); ?></option>
  <option value="html5-mobile" <?php echo $options['player_default']=='html5-mobile'?"selected":""?>><?php _e('HTML5 Mobile','video-share-vod'); ?></option>
   <option value="strobe-rtmp" <?php echo $options['player_default']=='strobe-rtmp'?"selected":""?>><?php _e('Strobe RTMP','video-share-vod'); ?></option>
</select>
<BR><?php _e('HTML5 Mobile plays lower profile converted video, for mobile support, even if source video is MP4.','video-share-vod'); ?>

<h4><?php _e('Player on iOS','video-share-vod'); ?></h4>
<select name="player_ios" id="player_ios">
  <option value="html5-mobile" <?php echo $options['player_ios']=='html5-mobile'?"selected":""?>><?php _e('HTML5 Mobile','video-share-vod'); ?></option>
   <option value="html5-hls" <?php echo $options['player_ios']=='html5-hls'?"selected":""?>><?php _e('HTML5 HLS','video-share-vod'); ?></option>
</select>

<h4><?php _e('Player on Safari','video-share-vod'); ?></h4>
<select name="player_safari" id="player_safari">
  <option value="strobe" <?php echo $options['player_safari']=='strobe'?"selected":""?>>Strobe</option>
  <option value="html5" <?php echo $options['player_safari']=='html5'?"selected":""?>><?php _e('HTML5','video-share-vod'); ?></option>
  <option value="html5-mobile" <?php echo $options['player_default']=='html5-mobile'?"selected":""?>><?php _e('HTML5 Mobile','video-share-vod'); ?></option>
   <option value="strobe-rtmp" <?php echo $options['player_safari']=='strobe-rtmp'?"selected":""?>><?php _e('Strobe RTMP','video-share-vod'); ?></option>
   <option value="html5-hls" <?php echo $options['player_safari']=='html5-hls'?"selected":""?>><?php _e('HTML5 HLS','video-share-vod'); ?></option>
</select>
<BR><?php _e('Safari requires user to confirm flash player load. Use HTML5 player to avoid this.','video-share-vod'); ?>

<h4><?php _e('Player on Firefox for MacOS','video-share-vod'); ?></h4>
<select name="player_firefox_mac" id="player_default">
  <option value="strobe" <?php echo $options['player_firefox_mac']=='strobe'?"selected":""?>>Strobe</option>
   <option value="strobe-rtmp" <?php echo $options['player_firefox_mac']=='strobe-rtmp'?"selected":""?>><?php _e('Strobe RTMP','video-share-vod'); ?></option>
</select>
<BR><?php _e('Firefox for Mac did not support MP4 HTML5 playback, last time we checked. See <a href="https://bugzilla.mozilla.org/show_bug.cgi?id=851290">bug status</a>.','video-share-vod'); ?>

<h4><?php _e('Player on Android','video-share-vod'); ?></h4>
<select name="player_android" id="player_android">
  <option value="html5" <?php echo $options['player_android']=='html5-mobile'?"selected":""?>><?php _e('HTML5 Mobile','video-share-vod'); ?></option>
  <option value="html5-hls" <?php echo $options['player_ios']=='html5-hls'?"selected":""?>><?php _e('HTML5 HLS','video-share-vod'); ?></option>
  <option value="strobe" <?php echo $options['player_android']=='strobe'?"selected":""?>><?php _e('Flash Strobe','video-share-vod'); ?></option>
   <option value="strobe-rtmp" <?php echo $options['player_android']=='strobe-rtmp'?"selected":""?>><?php _e('Flash Strobe RTMP','video-share-vod'); ?></option>
</select>
<BR><?php _e('Latest Android no longer supports Flash in default browser, so HTML5 is recommended.','video-share-vod'); ?>


<h4><?php _e('Allow Debugging','video-share-vod'); ?></h4>
<select name="allowDebug" id="allowDebug">
  <option value="1" <?php echo $options['allowDebug']=='1'?"selected":""?>><?php _e('Yes','video-share-vod'); ?></option>
  <option value="0" <?php echo $options['allowDebug']=='0'?"selected":""?>><?php _e('No','video-share-vod'); ?></option>
</select>
<br><?php _e('Allows forcing players at runtime using url parameters like ?player=html5-hls&player_html=video-js','video-share-vod'); ?>


<h4><?php _e('Cross Domain Policy','vw2wvc'); ?></h4>
<textarea name="crossdomain_xml" id="crossdomain_xml" cols="100" rows="4"><?php echo $options['crossdomain_xml']?></textarea>
<br>This is required for Flash and Air based players to access videos and scripts from site.
<br>After updating permalinks (<a href="options-permalink.php">Save Changes on Permalinks page</a>) this should become available as <a href="<?php echo $crossdomain_url ?>"><?php echo $crossdomain_url ?></a>.
<br>This works if file doesn't already exist. You can also create the file for faster serving.

<?php
				break;

			case 'display':
				//! display options

				$options['customCSS'] = htmlentities(stripslashes($options['customCSS']));
				$options['custom_post'] = preg_replace('/[^\da-z]/i', '', strtolower($options['custom_post']));
				$options['custom_taxonomy'] = preg_replace('/[^\da-z]/i', '', strtolower($options['custom_taxonomy']));

?>
<h3><?php _e('Display &amp; Listings','video-share-vod'); ?></h3>



<h4>Setup Pages</h4>
<select name="disableSetupPages" id="disableSetupPages">
  <option value="0" <?php echo $options['disableSetupPages']?"":"selected"?>>Yes</option>
  <option value="1" <?php echo $options['disableSetupPages']?"selected":""?>>No</option>
</select>
<br>Create pages for main functionality. Also creates a menu with these pages (VideoWhisper) that can be added to themes. If you delete the pages this option recreates these if not disabled.

<h4>Video Post Name</h4>
<input name="custom_post" type="text" id="custom_post" size="12" maxlength="32" value="<?php echo $options['custom_post']?>"/>
<br>Custom post name for videos (only alphanumeric, lower case). Will be used for video urls. Ex: video
<br><a href="options-permalink.php">Save permalinks</a> to activate new url scheme.
<br>Warning: Changing post type name at runtime will hide previously added items. Previous posts will only show when their post type name is restored.

<h4>Video Post Taxonomy Name</h4>
<input name="custom_taxonomy" type="text" id="custom_taxonomy" size="12" maxlength="32" value="<?php echo $options['custom_taxonomy']?>"/>
<br>Special taxonomy for organising videos. Ex: playlist

<h4><?php _e('Default Videos Per Page','video-share-vod'); ?></h4>
<input name="perPage" type="text" id="perPage" size="3" maxlength="3" value="<?php echo $options['perPage']?>"/>

<h4><?php _e('Default Videos Per Row','video-share-vod'); ?></h4>
<input name="perRow" type="text" id="perRow" size="3" maxlength="3" value="<?php echo $options['perRow']?>"/>
<br>Leave 0 to show as many as container space permits.

<h4><?php _e('Thumbnail Width','video-share-vod'); ?></h4>
<input name="thumbWidth" type="text" id="thumbWidth" size="4" maxlength="4" value="<?php echo $options['thumbWidth']?>"/>

<h4><?php _e('Thumbnail Height','video-share-vod'); ?></h4>
<input name="thumbHeight" type="text" id="thumbHeight" size="4" maxlength="4" value="<?php echo $options['thumbHeight']?>"/>

<h4>Video Post Template Filename</h4>
<input name="postTemplate" type="text" id="postTemplate" size="20" maxlength="64" value="<?php echo $options['postTemplate']?>"/>
<br>Template file located in current theme folder, that should be used to render webcam post page. Ex: page.php, single.php
<?php
				if ($options['postTemplate'] != '+plugin')
				{
					$single_template = get_template_directory() . '/' . $options['postTemplate'];
					echo '<br>' . $single_template . ' : ';
					if (file_exists($single_template)) echo 'Found.';
					else echo 'Not Found! Use another theme file!';
				}
?>
<br>Set "+plugin" to use a template provided by this plugin, instead of theme templates.


<h4><?php _e('Video Width','video-share-vod'); ?></h4>
<input name="videoWidth" type="text" id="videoWidth" size="4" maxlength="4" value="<?php echo $options['videoWidth']?>"/>
<br><?php _e('Leave blank to use video width dynamically for player.','video-share-vod'); ?>

<h4><?php _e('Playlist Video Width','video-share-vod'); ?></h4>
<input name="playlistVideoWidth" type="text" id="playlistVideoWidth" size="4" maxlength="4" value="<?php echo $options['playlistVideoWidth']?>"/>

<h4><?php _e('Playlist List Width','video-share-vod'); ?></h4>
<input name="playlistListWidth" type="text" id="playlistListWidth" size="4" maxlength="4" value="<?php echo $options['playlistListWidth']?>"/>

<h4>Playlist Template Filename</h4>
<input name="playlistTemplate" type="text" id="playlistTemplate" size="20" maxlength="64" value="<?php echo $options['playlistTemplate']?>"/>
<br>Template file located in current theme folder, that should be used to render playlist post page. Ex: page.php, single.php
<?php
				if ($options['postTemplate'] != '+plugin')
				{
					$single_template = get_template_directory() . '/' . $options['playlistTemplate'];
					echo '<br>' . $single_template . ' : ';
					if (file_exists($single_template)) echo 'Found.';
					else echo 'Not Found! Use another theme file!';
				}
?>
<br>Set "+plugin" to use a template provided by this plugin, instead of theme templates.


<h4><?php _e('Custom CSS','video-share-vod'); ?></h4>
<textarea name="customCSS" id="customCSS" cols="64" rows="5"><?php echo $options['customCSS']?></textarea>
<BR><?php _e('Styling used in elements added by this plugin. Must include CSS container &lt;style type=&quot;text/css&quot;&gt; &lt;/style&gt; .','video-share-vod'); ?>
If a plugin update alters listings, just reset CSS to current defaults:<br><textarea readonly cols="100" rows="3"><?php echo $optionsDefault['customCSS']?></textarea>

<h4><?php _e('Show VideoWhisper Powered by','video-share-vod'); ?></h4>
<select name="videowhisper" id="videowhisper">
  <option value="0" <?php echo $options['videowhisper']?"":"selected"?>>No</option>
  <option value="1" <?php echo $options['videowhisper']?"selected":""?>>Yes</option>
</select>
<br><?php _e('Show a mention that videos were posted with VideoWhisper plugin.
','video-share-vod'); ?>
<?php
				break;

			case 'share':
				//! share options
?>
<h3><?php _e('Video Sharing','video-share-vod'); ?></h3>

<h4><?php _e('Users allowed to share videos','video-share-vod'); ?></h4>
<textarea name="shareList" cols="64" rows="2" id="shareList"><?php echo $options['shareList']?></textarea>
<BR><?php _e('Who can share videos: comma separated Roles, user Emails, user ID numbers.','video-share-vod'); ?>
<BR><?php _e('"Guest" will allow everybody including guests (unregistered users).','video-share-vod'); ?>

<h4><?php _e('Users allowed to directly publish videos','video-share-vod'); ?></h4>
<textarea name="publishList" cols="64" rows="2" id="publishList"><?php echo $options['publishList']?></textarea>
<BR><?php _e('Users not in this list will add videos as "pending".','video-share-vod'); ?>
<BR><?php _e('Who can publish videos: comma separated Roles, user Emails, user ID numbers.','video-share-vod'); ?>
<BR><?php _e('"Guest" will allow everybody including guests (unregistered users).','video-share-vod'); ?>

<h4><?php _e('Users allowed to get embed codes','video-share-vod'); ?></h4>
<textarea name="embedList" cols="64" rows="2" id="embedList"><?php echo $options['embedList']?></textarea>
<BR><?php _e('Who can see embed code for videos: comma separated Roles, user Emails, user ID numbers.','video-share-vod'); ?>
<BR><?php _e('"Guest" will allow everybody including guests (unregistered users).','video-share-vod'); ?>
<BR><?php _e('Add code below to your .htaccess file for successful resource embeds:','video-share-vod'); ?>
<BR># Apache config: allow embeds on other sites
<BR>Header set Access-Control-Allow-Origin "*"
<?php
				break;


			case 'vod':
				//! vod options
				$options['accessDenied'] = htmlentities(stripslashes($options['accessDenied']));

?>
<h3>Membership Video On Demand</h3>
<a target="_blank" href="http://videosharevod.com/features/video-on-demand/">About Video On Demand...</a>

<h4>Members allowed to watch video</h4>
<textarea name="watchList" cols="64" rows="3" id="watchList"><?php echo $options['watchList']?></textarea>
<BR>Global video access list: comma separated Roles, user Emails, user ID numbers. Ex: <i>Subscriber, Author, submit.ticket@videowhisper.com, 1</i>
<BR>"Guest" will allow everybody including guests (unregistered users) to watch videos.

<h4>Role Playlists</h4>
Enables access by role playlists: Assign video to a playlist that is a role name.
<br><select name="vod_role_playlist" id="vod_role_playlist">
  <option value="1" <?php echo $options['vod_role_playlist']?"selected":""?>>Yes</option>
  <option value="0" <?php echo $options['vod_role_playlist']?"":"selected"?>>No</option>
</select>
<br>Multiple roles can be assigned to same video. User can have any of the assigned roles, to watch. If user has required role, access is granted even if not in global access list.
<br>Videos without role playlists are accessible as per global video access.

<h4>Exceptions</h4>
Assign videos to these Playlists:
<br><b>free</b> : Anybody can watch, including guests.
<br><b>registered</b> : All members can watch.
<br><b>unpublished</b> : Video is not accessible.

<h4>Access denied message</h4>
<textarea name="accessDenied" cols="64" rows="3" id="accessDenied"><?php echo $options['accessDenied']?>
</textarea>
<BR>HTML info, shows with preview if user does not have access to watch video.
<br>Including #info# will mention rule that was applied.
<?php
				break;

			case 'vast':
				//! vast options
				$options['vast'] = trim($options['vast']);

?>
<h3>Video Ad Serving Template (VAST) / Interactive Media Ads (IMA)</h3>
VAST/IMA is currently supported with Video.js HTML5 player.
<br>VAST data structure configures: (1) The ad media that should be played (2) How should the ad media be played (3) What should be tracked as the media is played. In example pre-roll video ads can be implemented with VAST.
<br>IMA enables ad requests to DoubleClick for Publishers (DFP), the Google AdSense network for Video (AFV) or Games (AFG) or any VAST-compliant ad server.

<h4>Video Ads</h4>
Enable ads for all videos.
<br><select name="adsGlobal" id="adsGlobal">
  <option value="1" <?php echo $options['adsGlobal']?"selected":""?>>Yes</option>
  <option value="0" <?php echo $options['adsGlobal']?"":"selected"?>>No</option>
</select>
<br>Exception Playlists:
<br><b>sponsored</b>: Show ads.
<br><b>adfree</b>: Do not show ads.

<h4>VAST Mode</h4>
<select name="vastLib" id="vastLib">
  <option value="iab" <?php echo $options['vastLib']=='iab'?"":"selected"?>>Google Interactive Media Ads (IMA)</option>
  <option value="vast" <?php echo $options['vastLib']=='vast'?"selected":""?>>Video Ad Serving Template (VAST) </option>
</select>
<br>The Google Interactive Media Ads (IMA) enables publishers to display linear, non-linear, and companion ads in videos and games. Supports VAST 2, VAST 3, VMAP.

<h4>VAST compliant / IMA adTagUrl Address</h4>
<textarea name="vast" cols="64" rows="2" id="vast"><?php echo $options['vast']?>
</textarea>
<br>Ex: http://ad3.liverail.com/?LR_PUBLISHER_ID=1331&LR_CAMPAIGN_ID=229&LR_SCHEMA=vast2
<br>Leave blank to disable video ads.

<h4>Premium Users List</h4>
<p>Premium uses watch videos without advertisements (exception for VAST).</p>
<textarea name="premiumList" cols="64" rows="3" id="premiumList"><?php echo $options['premiumList']?>
</textarea>
<BR>Ads excepted users: comma separated Roles, user Emails, user ID numbers. Ex: <i>Author, Editor, submit.ticket@videowhisper.com, 1</i>

<?php
				break;
			}

			if (!in_array($active_tab, array( 'shortcodes')) ) submit_button(); ?>

</form>
</div>
	 <?php
		}

		function adminImport()
		{
			$options = VWvideoShare::setupOptions();

			if (isset($_POST))
			{
				foreach ($options as $key => $value)
					if (isset($_POST[$key])) $options[$key] = trim($_POST[$key]);
					update_option('VWvideoShareOptions', $options);
			}


			screen_icon(); ?>
<h2>Import Videos from Folder</h2>
	Use this to mass import any number of videos already existent on server.

<?php
			if (file_exists($options['importPath'])) echo do_shortcode('[videowhisper_import path="' . $options['importPath'] . '"]');
			else echo 'Import folder not found on server: '. $options['importPath'];
?>
<h3>Import Settings</h3>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<h4>Import Path</h4>
<p>Server path to import videos from</p>
<input name="importPath" type="text" id="importPath" size="100" maxlength="256" value="<?php echo $options['importPath']?>"/>
<br>Ex: /home/[youraccount]/public_html/streams/
<h4>Delete Original on Import</h4>
<select name="deleteOnImport" id="deleteOnImport">
  <option value="1" <?php echo $options['deleteOnImport']?"selected":""?>>Yes</option>
  <option value="0" <?php echo $options['deleteOnImport']?"":"selected"?>>No</option>
</select>
<br>Remove original file after copy to new location.
<h4>Import Clean</h4>
<p>Delete videos older than:</p>
<input name="importClean" type="text" id="importClean" size="5" maxlength="8" value="<?php echo $options['importClean']?>"/>days
<br>Set 0 to disable automated cleanup (not recommended as an active site can fill up server disks with broadcast archives). Cleanup does not occur more often than 10h to prevent high load.
<?php submit_button(); ?>
</form>
	<?php



		}



		function adminLiveStreaming()
		{
			$options = get_option( 'VWvideoShareOptions' );

			screen_icon(); ?>

<h3>Import Archived Channel Videos</h3>
This allows importing stream archives to playlist of their video channel. <a target="_blank" href="http://videosharevod.com/features/live-streaming/">About Live Streaming...</a><br>
<?php

			if ($channel_name = sanitize_file_name($_GET['playlist_import']))
			{

				$url  = add_query_arg( array( 'playlist_import' => $channel_name), admin_url('admin.php?page=video-share-ls') );


				echo '<form action="' . $url . '" method="post">';
				echo "<h4>Import Archived Videos to Playlist <b>" . $channel_name . "</b></h4>";
				echo VWvideoShare::importFilesSelect( $channel_name, VWvideoShare::extensions_flash(), $options['vwls_archive_path']);
				echo '<INPUT class="button button-primary" TYPE="submit" name="import" id="import" value="Import">';
				global $wpdb;
				$postID = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '" . sanitize_file_name($channel_name) . "' and post_type='channel' LIMIT 0,1" );

				if ($postID)
				{
					$channel = get_post( $postID );
					$owner = $channel->post_author;

					$cats = wp_get_post_categories( $postID);
					if (count($cats)) $category = array_pop($cats);
				}
				else
				{
					global $current_user;
					get_currentuserinfo();
					$owner = $current_user->ID;
					echo ' as ' . $current_user->display_name;
				}

				echo '<input type="hidden" name="playlist" id="playlist" value="' . $channel_name . '">';
				echo '<input type="hidden" name="owner" id="owner" value="' . $owner . '">';
				echo '<input type="hidden" name="category" id="category" value="' . $category . '">';

				echo ' <INPUT class="button button-primary" TYPE="submit" name="delete" id="delete" value="Delete">';

				echo '</form>';
			}


			echo "<h4>Recent Activity</h4>";

			function format_age($t)
			{
				if ($t<30) return "LIVE";
				return sprintf("%d%s%d%s%d%s", floor($t/86400), 'd ', ($t/3600)%24,'h ', ($t/60)%60,'m');
			}

			global $wpdb;
			$table_name3 = $wpdb->prefix . "vw_lsrooms";
			$items =  $wpdb->get_results("SELECT * FROM `$table_name3` ORDER BY edate DESC LIMIT 0, 100");
			echo "<table class='wp-list-table widefat'><thead><tr><th>Channel</th><th>Videos</th><th>Actions</th><th>Last Access</th><th>Type</th></tr></thead>";
			if ($items) foreach ($items as $item)
					if (($fcount = VWvideoShare::importFilesCount( $item->name, VWvideoShare::extensions_flash(), $options['vwls_archive_path']))!='0 (0.00B)')
					{
						echo "<tr><th>" . $item->name . "</th>";

						echo "<td>". $fcount . "</td>";

						$link  = add_query_arg( array( 'playlist_import' => $item->name), admin_url('admin.php?page=video-share-ls') );

						echo '<td><a class="button button-primary" href="' .$link.'">Import</a></td>';
						echo "<td>".format_age(time() - $item->edate)."</td>";
						echo '<td>' . ($item->type==2?"Premium":"Standard") . '</td>';
						echo "</tr>";
					}
				echo '<tr><th>Total</th><th colspan="4">' . VWvideoShare::importFilesCount( '', VWvideoShare::extensions_flash(), $options['vwls_archive_path']) . '</th></tr>';
			echo "</table>";
		}
		//fc above
	}
}

//instantiate
if (class_exists("VWvideoShare")) {
	$videoShare = new VWvideoShare();
}

//Actions and Filters
if (isset($videoShare)) {

	register_activation_hook( __FILE__, array(&$videoShare, 'install' ) );
	register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

	add_action( 'init', array(&$videoShare, 'video_post'),0);
	add_action('admin_menu', array(&$videoShare, 'adminMenu'));
	add_action("plugins_loaded", array(&$videoShare , 'init'));

	add_action( 'parse_request', array(&$videoShare, 'parse_request'));

	//archive
	add_filter( 'archive_template', array('VWvideoShare','archive_template') ) ;


	//cron
	add_filter( 'cron_schedules', array(&$videoShare,'cron_schedules'));
	add_action( 'cron_5min_event', array(&$videoShare, 'convertProcessQueue' ) );
	add_action( 'init', array(&$videoShare, 'setup_schedule'));

	//page template
	add_filter( "single_template", array(&$videoShare,'single_template') );
}

//dev only: instead of Save Permalinks
// flush_rewrite_rules();

?>