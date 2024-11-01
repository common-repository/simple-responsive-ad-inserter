<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://plugin-boutique.com/simple-ad-inserter/
 * @since      1.0
 *
 * @package    simple-ad-inserter
 * @subpackage simple-ad-inserter/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0
 * @package    simple-ad-inserter
 * @subpackage simple-ad-inserter/includes
 * @author     Sparkjones
 */
class WP_In_Post_Ads {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      WP_In_Post_Ads_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0
	 */
	public function __construct() {

		$this->plugin_name = 'wp-in-post-ads';
		$this->version = '1.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_In_Post_Ads_Loader. Orchestrates the hooks of the plugin.
	 * - WP_In_Post_Ads_i18n. Defines internationalization functionality.
	 * - WP_In_Post_Ads_Admin. Defines all hooks for the admin area.
	 * - WP_In_Post_Ads_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-in-post-ads-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-in-post-ads-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-in-post-ads-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-in-post-ads-public.php';

		/**
		 * Widget class
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-in-post-ads-widget.php';

		$this->loader = new WP_In_Post_Ads_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_In_Post_Ads_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WP_In_Post_Ads_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WP_In_Post_Ads_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add the options page and dashboard menu item.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'plugin_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'settings_api_init' );

		// Register our post type
		$this->loader->add_action( 'init', $plugin_admin, 'register_post_type' );

		// Display shortcode code
		$this->loader->add_action( 'post_submitbox_misc_actions', $plugin_admin, 'shortcode_code' );

		// Add options metabox
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'wpipa_single_metabox' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'wpipa_single_metabox_save' );

		// Add columns to post type tables
		$this->loader->add_filter( 'manage_mts_ad_posts_columns', $plugin_admin, 'mts_ad_columns_head', 10 );
		$this->loader->add_action( 'manage_mts_ad_posts_custom_column', $plugin_admin, 'mts_ad_column_content', 10, 2 );
		
		// Post metabox to disable ads
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'wpipa_metabox_insert' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'wpipa_metabox_save' );

		// Widget
		$this->loader->add_action( 'widgets_init', $plugin_admin, 'wpipa_widget' );

		$this->loader->add_action( 'wp_ajax_wpipa_get_ads', $plugin_admin, 'wpipa_get_ads' );
		$this->loader->add_action( 'wp_ajax_get_post_titles', $plugin_admin, 'get_post_titles' );

		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'mts_ad_update_messages' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WP_In_Post_Ads_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 999999 );

		$this->loader->add_action( 'the_content', $plugin_public, 'insert_post_ads', 999999 );

		$this->loader->add_action( 'init', $plugin_public, 'register_shortcode' );

		$this->loader->add_action( 'wp_ajax_mts_ads_view_count', $plugin_public, 'mts_ads_view_count' );
		$this->loader->add_action( 'wp_ajax_nopriv_mts_ads_view_count', $plugin_public, 'mts_ads_view_count' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0
	 * @return    WP_In_Post_Ads_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
