<?php
/**
 * Plugin Name: Facebook Page Box
 * Plugin URI: http://wolfthemes.com/plugin/wolf-facebook-page-box
 * Description: A Facebook page box widget and shortcode for WordPress.
 * Version: 1.0.9
 * Author: WolfThemes
 * Author URI: https://wolfthemes.com
 * Requires at least: 5.0
 * Tested up to: 5.5
 *
 * Text Domain: wolf-facebook-page-box
 * Domain Path: /languages/
 *
 * @package WolfFacebookPageBox
 * @category Core
 * @author WolfThemes
 *
 * Verified customers who have purchased a premium theme at https://wlfthm.es/tf/
 * will have access to support for this plugin in the forums
 * https://wlfthm.es/help/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Wolf_Facebook_Page_Box' ) ) {
	/**
	 * Main Wolf_Facebook_Page_Box Class
	 *
	 * Contains the main functions for Wolf_Facebook_Page_Box
	 *
	 * @class Wolf_Facebook_Page_Box
	 * @version 1.0.9
	 * @since 1.0.0
	 * @package WolfVideos
	 * @author WolfThemes
	 */
	class Wolf_Facebook_Page_Box {

		/**
		 * @var string
		 */
		public $version = '1.0.9';

		/**
		 * @var Facebook Page Box The single instance of the class
		 */
		protected static $_instance = null;

		/**
		 * @var the support forum URL
		 */
		private $support_url = 'http://help.wolfthemes.com/';

		/**
		 * @var string
		 */
		public $plugin_url;

		/**
		 * @var string
		 */
		public $plugin_path;

		/**
		 * Main Facebook Page Box Instance
		 *
		 * Ensures only one instance of Facebook Page Box is loaded or can be loaded.
		 *
		 * @static
		 * @see WPM()
		 * @return Facebook Page Box - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Wolf_Facebook_Page_box Constructor.
		 */
		public function __construct() {

			// init
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			// register shortcode
			add_shortcode( 'wolf_facebook_page_box', array( $this, 'shortcode' ) );

			// output script
			//add_action( 'wp_head', array( $this, 'output_script' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );

			// Widget
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );

			add_action( 'admin_init', array( $this, 'plugin_update' ) );
		}

		/**
		 * Load Localisation files.
		 */
		public function load_plugin_textdomain() {

			$domain = 'wolf-facebook-page-box';
			$locale = apply_filters( 'wolf-facebook-page-box', get_locale(), $domain );
			load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * register_widgets function.
		 */
		public function register_widgets() {

			// Include
			include_once( 'classes/class-wfpb-widget.php' );

			// Register widgets
			register_widget( 'WFPB_Widget' );
		}

		/**
		 * Shortcode
		 *
		 * @access public
		 * @param array $atts
		 * @return string
		 */
		public function shortcode( $atts ) {

			extract(
				shortcode_atts(
					array(
						'page_url' => 'https://www.facebook.com/wolfthemes',
						'height' => 400,
						'hide_cover' => 'false',
						'show_posts' => 'true',
						'show_faces' => 'true',
						'small_header' => 'false',
					), $atts
				)
			);

			$hide_cover = ( 'false' == $hide_cover || '0' == $hide_cover || '' == $hide_cover ) ? false : true;
			$show_posts = ( 'false' == $show_posts || '0' == $show_posts || '' == $show_posts ) ? false : true;
			$show_faces = ( 'false' == $show_faces || '0' == $show_faces || '' == $show_faces ) ? false : true;
			$small_header = ( 'false' == $small_header || '0' == $small_header || '' == $small_header ) ? false : true;

			return $this->facebook_box( $page_url, $height, $hide_cover, $show_posts, $show_faces, $small_header );
		}

		/**
		 * Enqueue script
		 *
		 * @access public
		 * @param array $atts
		 * @return string
		 */
		function enqueue_script() {
			$lang = apply_filters( 'wfpb_lang_code', esc_html__( 'en_US', 'wolf-facebook-page-box' ) );

			wp_register_script( 'wolf-facebook-page-box', $this->plugin_url() . '/assets/js/wfpb.js', array(), $this->version, true );

			$inline_script = '(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/' . esc_js( $lang ) . '/sdk.js#xfbml=1&version=v2.8";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));';

			wp_add_inline_script( 'wolf-facebook-page-box', $inline_script );

			wp_enqueue_script( 'wolf-facebook-page-box' );
		}

		/**
		 * Output Facebook Page Box
		 *
		 * @access public
		 * @param array $atts
		 * @return string
		 */
		public function facebook_box(
			$page_url = 'https://www.facebook.com/facebook',
			$height = 400,
			$hide_cover = false,
			$show_posts = true,
			$show_faces = true,
			$small_header = false
			) {

			$page_url = esc_url( $page_url  );
			$height = absint( $height );

			$hide_cover = ( $hide_cover ) ? 'true' : 'false';
			$small_header = ( $small_header ) ? 'true' : 'false';
			$show_posts = ( $show_posts ) ? 'timeline' : 'false';
			$show_faces = ( $show_faces ) ? 'true' : 'false';

			wp_enqueue_script( 'wolf-facebook-page-box' );

			$output = '<style>
.fb_iframe_widget > span,
.fb_iframe_widget > div,
.fb_iframe_widget iframe{
	max-width:500px;
  	width:100%!important;
}</style>';

			$output = "<div class='fb-page'
				data-adapt-container-width='true'
				data-small-header='$small_header'
				data-href='$page_url'
				data-width='500'
				data-height='$height'
				data-hide-cover='$hide_cover'
				data-show-facepile='$show_faces'
				data-tabs='$show_posts'>
				<div class='fb-xfbml-parse-ignore'><blockquote cite='$page_url'><a href='$page_url'>Facebook</a></blockquote></div></div>";

			return $output;
		}

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			if ( $this->plugin_url ) return $this->plugin_url;
			return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			if ( $this->plugin_path ) return $this->plugin_path;
			return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		/**
		 * Plugin update
		 */
		public function plugin_update() {

			if ( ! class_exists( 'WP_GitHub_Updater' ) ) {
				include_once 'updater.php';
			}

			$repo = 'wolfthemes/wolf-facebook-page-box';

			$config = array(
				'slug' => plugin_basename( __FILE__ ),
				'proper_folder_name' => 'wolf-facebook-page-box',
				'api_url' => 'https://api.github.com/repos/' . $repo . '',
				'raw_url' => 'https://raw.github.com/' . $repo . '/master/',
				'github_url' => 'https://github.com/' . $repo . '',
				'zip_url' => 'https://github.com/' . $repo . '/archive/master.zip',
				'sslverify' => true,
				'requires' => '5.0',
				'tested' => '5.5',
				'readme' => 'README.md',
				'access_token' => '',
			);

			new WP_GitHub_Updater( $config );
		}

	} // end class

} // end class exists check

/**
 * Returns the main instance of WFPB to prevent the need to use globals.
 *
 * @return Wolf_Facebook_Page_Box
 */
function WFPB() {
	return Wolf_Facebook_Page_Box::instance();
}
WFPB(); // Go
