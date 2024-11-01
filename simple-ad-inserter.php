<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Simple Responsive Ad Inserter
 * Plugin URI:        http://plugin-boutique.com/simple-ad-inserter/
 * Description:       Insert responsive ads easily in posts, pages & widgets. Shortcode support. Seo Optimized. Beautiful. Easy to use. Fully customizable.
 * Version:           1.0
 * Author:            SparkJones
 * Author URI:        http://plugin-boutique.com/simple-ad-inserter/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-in-post-ads-activator.php
 */
function activate_wp_in_post_ads() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-in-post-ads-activator.php';
	WP_In_Post_Ads_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-in-post-ads-deactivator.php
 */
function deactivate_wp_in_post_ads() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-in-post-ads-deactivator.php';
	WP_In_Post_Ads_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_in_post_ads' );
register_deactivation_hook( __FILE__, 'deactivate_wp_in_post_ads' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-in-post-ads.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0
 */
function run_wp_in_post_ads() {

	$plugin = new WP_In_Post_Ads();
	$plugin->run();

}
run_wp_in_post_ads();
