<?php

/**
 * Fired during plugin activation
 *
 * @link       http://plugin-boutique.com/simple-ad-inserter/
 * @since      1.0
 *
 * @package    simple-ad-inserter
 * @subpackage simple-ad-inserter/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0
 * @package    simple-ad-inserter
 * @subpackage simple-ad-inserter/includes
 * @author     Sparkjones
 */
class WP_In_Post_Ads_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0
	 */
	public static function activate() {

		if ( false == get_option( 'wpipa_inside_post_ads' ) ) {

			add_option( 'wpipa_inside_post_ads', array() );
		}

		if ( false == get_option( 'wpipa_ads_view_count' ) ) {

			add_option( 'wpipa_ads_view_count', array() );
		}
	}
}
