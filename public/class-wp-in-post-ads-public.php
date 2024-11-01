<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://mythemeshop.com
 * @since      1.0
 *
 * @package  simple-ad-inserter
 * @subpackage simple-ad-inserter/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package  simple-ad-inserter
 * @subpackage simple-ad-inserter/public
 * @author     MyThemeShop
 */
class WP_In_Post_Ads_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-in-post-ads-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {

		$options = get_option( 'wpipa_settings' );
		$supported_pt = isset( $options['wpipa_supported_post_types'] ) ? $options['wpipa_supported_post_types'] : array('post');

		if ( isset( $options['wpipa_ga'] ) && !empty( $supported_pt ) ) {

			if ( is_singular( $supported_pt ) ) {

				$ga_cat   = isset( $options['wpipa_ga_category'] ) ? $options['wpipa_ga_category'] : '';
				$ga_label = isset( $options['wpipa_ga_label'] ) ? $options['wpipa_ga_label'] : '';

				if ( !empty( $ga_cat ) && !empty( $ga_label ) ) {

					wp_enqueue_script( $this->plugin_name.'_ga', plugin_dir_url( __FILE__ ) . 'js/wp-in-post-ads-ga.js', array( 'jquery' ), $this->version, true );
					wp_localize_script(
						$this->plugin_name.'_ga',
						'wpipaVars',
						array(
							'ga_category' => $ga_cat,
							'ga_label'    => $ga_label,
						)
					);
				}
			}
		}

		wp_enqueue_script( $this->plugin_name.'_view_count', plugin_dir_url( __FILE__ ) . 'js/wp-in-post-ads-views.js', array( 'jquery' ), $this->version, true );
		wp_localize_script(
			$this->plugin_name.'_view_count',
			'wpipaViews',
			array(
				'url' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Ajax update view count
	 *
	 * @since    1.0
	 */
	public function mts_ads_view_count() {

		$ids = $_POST['ids'];
    	
		if ( !empty( $ids ) ) {

			$ids = ltrim ($ids, ',');

			$ids_arr = explode( ',', $ids );

			$opt_arr = get_option( 'wpipa_ads_view_count' );

			foreach ( $ids_arr as $id ) {
				
				if ( isset( $opt_arr[ $id ] ) ) {

					$opt_arr[ $id ] = (int) $opt_arr[ $id ] + 1;

				} else {

					$opt_arr[ $id ] = 1;
				}
			}

			update_option( 'wpipa_ads_view_count', $opt_arr );
		}

		die();
	}

	/**
	 * Get the ads
	 *
	 * @since    1.0
	 */
	public function get_content_ads() {

		$options = get_option( 'wpipa_settings' );

		$return_arr = array();
		$places_arr = array();

		if ( 'manual' === $options['wpipa_insertion_type'] ) {

			return $return_arr;
		}

		$count = 0;

		if ( isset( $options['wpipa_show_after_title'] ) ) {

			$count++;
			$places_arr[] = 'wpipa_show_after_title';
		}

		if ( isset( $options['wpipa_show_after_p'] ) ) {

			$count++;
			$places_arr[] = 'wpipa_show_after_p';
		}

		if ( isset( $options['wpipa_show_after_content'] ) ) {

			$count++;
			$places_arr[] = 'wpipa_show_after_content';
		}

		if ( 0 === $count )  {

			return $return_arr;
		}

		$orderby = isset( $options['wpipa_ads_orderby'] ) ? $options['wpipa_ads_orderby'] : 'date';

		$args = array(
			'posts_per_page'   => $count,
			'orderby'          => $orderby,
			'order'            => 'DESC',
			'post_type'        => 'mts_ad',
			'post_status'      => 'publish',
		);
		$return_arr = get_posts( $args );

		$return_arr = array_combine( $places_arr, $return_arr );

		return $return_arr;
	}

	/**
	 * Insert ads into post content
	 *
	 * @since    1.0
	 */
	public function insert_post_ads( $content ) {

		if ( !is_main_query() || is_admin() ) {

			return $content;
		}

		$post_id = get_the_id();
		$disable_ads = get_post_meta( $post_id, '_wpipa_disable_ads', true );

		if ( 'yes' === $disable_ads ) {

			return $content;
		}

		$test_user_conditions = $this->test_user_conditions();
		if ( !$test_user_conditions ) {

			return $content;
		}

		$options = get_option( 'wpipa_settings' );

		$supported_pt = isset( $options['wpipa_supported_post_types'] ) ? $options['wpipa_supported_post_types'] : array('post');

		if ( empty( $supported_pt ) || !is_singular( $supported_pt ) ) {

			return $content;
		}

		$wpipa_groups = isset( $options['wpipa_groups'] ) ? $options['wpipa_groups'] : 'all';
		
		$orderby = isset( $options['wpipa_ads_orderby'] ) ? $options['wpipa_ads_orderby'] : 'date';
		$meta_key = '';
		if ( 'priority' === $orderby ) {
			$orderby = 'meta_value_num date';
			$meta_key = '_wpipa_priority';
		}
		$order = isset( $options['wpipa_ads_order'] ) ? $options['wpipa_ads_order'] : 'DESC';

		$shared_args = array(
			'post_type'      => 'mts_ad',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => $orderby,
			'order'          => $order,
		);
		if ( !empty( $meta_key) ) $shared_args['meta_key'] = $meta_key;

		
		$wpipa_inside_post_ads = get_option( 'wpipa_inside_post_ads' );

		$closing_p = '</p>';
		$paragraphs = explode( $closing_p, $content );

		foreach ( $paragraphs as $index => $paragraph ) {

			if ( trim( $paragraph ) ) {

				$paragraphs[ $index ] .= $closing_p;
			}

			if ( array_key_exists( $index + 1, $wpipa_inside_post_ads ) ) {

				if ( !empty( $wpipa_inside_post_ads[ $index + 1 ] ) ) {

					$inside_content_meta_query = array(
						'meta_query' => array(
							array(
								'key'     => '_wpipa_position',
								'value'   => 'after_n_p',
								'compare' => 'LIKE'
							),
						),
						'post__in' => $wpipa_inside_post_ads[ $index + 1 ]
					);
					$inside_content_args = array_merge( $shared_args, $inside_content_meta_query );
					$inside_content_ads  = get_posts( $inside_content_args );

					$inside_content_ad = '';

					foreach ( $inside_content_ads as $key => $ad_object ) {

						$settings = get_post_meta( $ad_object->ID, '_wpipa_single_settings', true );

						$test_inside_content_ads_date_conditions = $this->test_date_conditions( $post_id, $settings );

						if ( $test_inside_content_ads_date_conditions ) {

							$inside_content_ad .= $this->get_ad( $ad_object );

							if ( 'single' === $wpipa_groups ) break;
						}
					}

					$paragraphs[ $index ] .= $inside_content_ad;
				}
			}
		}
		
		$content = implode( '', $paragraphs );

		$before_content_meta_query = array(
			'meta_query' => array(
				array(
					'key'     => '_wpipa_position',
					'value'   => 'before_content',
					'compare' => 'LIKE'
				),
			)
		);
		$before_content_args = array_merge( $shared_args, $before_content_meta_query );
		$before_content_ads = get_posts( $before_content_args );

		$before_content_ad = '';
		foreach ( $before_content_ads as $key => $ad_object ) {

			$settings = get_post_meta( $ad_object->ID, '_wpipa_single_settings', true );

			$test_before_content_ads_date_conditions = $this->test_date_conditions( $post_id, $settings );

			if ( $test_before_content_ads_date_conditions ) {

				$before_content_ad .= $this->get_ad( $ad_object );

				if ( 'single' === $wpipa_groups ) break;
			}
		}

		$content = $before_content_ad . $content;

		$after_content_meta_query = array(
			'meta_query' => array(
				array(
					'key'     => '_wpipa_position',
					'value'   => 'after_content',
					'compare' => 'LIKE'
				),
			)
		);
		$after_content_args = array_merge( $shared_args, $after_content_meta_query );
		$after_content_ads = get_posts( $after_content_args );

		$after_content_ad = '';
		foreach ( $after_content_ads as $key => $ad_object ) {

			$settings = get_post_meta( $ad_object->ID, '_wpipa_single_settings', true );

			$test_after_content_ads_date_conditions = $this->test_date_conditions( $post_id, $settings );

			if ( $test_after_content_ads_date_conditions ) {

				$after_content_ad .= $this->get_ad( $ad_object );

				if ( 'single' === $wpipa_groups ) break;

			}
		}

		$content = $content . $after_content_ad;

		return $content;
	}

	/**
	 * Get ad
	 *
	 * @since    1.0
	 */
	public function get_ad( $ad ) {

		$output = '';

		$def_class = apply_filters( 'wpipa_default_class', array('wpipa') );

		if ( $ad ) {

			if ( !in_the_loop() ) {

				$class = 'class="' . join( ' ', get_post_class( $def_class, $ad->ID ) ) . '"';

			} else {

				$class = 'class="' . join( ' ', $def_class ) . '"';
			}

			$output = wptexturize( $ad->post_content );
			$output = convert_smilies( $output );
			$output = convert_chars( $output );
			$output = wpautop( $output );
			$output = shortcode_unautop( $output );
			$output = do_shortcode( $output );
			$output = prepend_attachment( $output );

			$settings = get_post_meta( $ad->ID, '_wpipa_single_settings', true );

			$align    = $settings['align'];
			$width    = $settings['width'];
			$padding  = $settings['padding'];
			$bg_color = $settings['bg_color'];
			$color    = $settings['color'];
			
			$wpipa_inline_style = 'style="background-color:'.$bg_color.';color:'.$color.';padding:'.$padding.'px;';
			if ( 'center' === $align ) {
				$container_inline_style  = '';
				$wpipa_inline_style      .= 'max-width:'.$width.'px;';
			} else {
				$container_inline_style  = ' style="max-width:'.$width.'px;"';
				$wpipa_inline_style      .= '';
			}
			$wpipa_inline_style .= '"';

			$output = '<div id="wpipa-' . $ad->ID . '-container" data-id="' . $ad->ID . '" class="wpipa-container wpipa-align-'.$align.'"'.$container_inline_style.'><div id="wpipa-' . $ad->ID . '" '.$class.$wpipa_inline_style.'>' . $output . '</div></div>';
			
			
		}
		
		return apply_filters( 'wpipa_get_ad', $output );
	}

	/**
	 * Insert After p
	 *
	 * @since    1.0
	 */
	public function insert_after_paragraph( $insertion, $paragraph_id, $content ) {

		$closing_p = '</p>';
		$paragraphs = explode( $closing_p, $content );

		foreach ( $paragraphs as $index => $paragraph ) {

			if ( trim( $paragraph ) ) {

				$paragraphs[ $index ] .= $closing_p;
			}

			if ( $paragraph_id == $index + 1 ) {

				$paragraphs[ $index ] .= $insertion;
			}
		}
		
		return implode( '', $paragraphs );
	}

	/**
	 * Register "wpipa" shortcode
	 *
	 * @since    1.0
	 */
	public function register_shortcode() {

		add_shortcode('wpipa', array( $this, 'wpipa_shortcode' ) );
	}

	/**
	 * Shortcode callback
	 *
	 * @since    1.0
	 */
	public function wpipa_shortcode( $atts, $content = null ) {
		extract( shortcode_atts( array(
			'id' => null,
		), $atts ) );

		$post = get_post( $id );
		
		$ad = $this->get_ad( $post );

		return $ad;
	}
	
	/**
	 * Test should ads be displayed based on date conditions
	 *
	 * @since    1.0
	 */
	public function test_date_conditions( $post_id, $options ) {

		$post_published_date = get_the_date( 'U', $post_id );

		$show_after_days = isset( $options['show_after'] );
		$hide_after_days = isset( $options['show_for'] );
		$show_after_days_num = ( $show_after_days && isset( $options['show_after_days'] ) ) ? (int) $options['show_after_days'] : 0;
		$hide_after_days_num = isset( $options['show_for_days'] ) ? (int) $options['show_for_days'] : 1;

		if ( !$show_after_days && !$hide_after_days ) {

			return true;
		}

		$show_after = true;
		if ( $show_after_days ) {

			$show_after = (bool) ( date( 'U', time() ) >= $post_published_date + $show_after_days_num * 86400 );
		}

		$hide_after = true;
		if ( $hide_after_days ) {

			$hide_after = (bool) ( date( 'U', time() ) < $post_published_date + $show_after_days_num + $hide_after_days_num * 86400 );
		}

		return (bool) ( $show_after && $hide_after );
	}

	/**
	 * Test should ads be displayed based on user logged in state
	 *
	 * @since    1.0
	 */
	public function test_user_conditions() {

		$options = get_option( 'wpipa_settings' );

		if ( isset( $options['wpipa_hide_for_logged'] ) && is_user_logged_in() ) {

			return false;

		} else {

			return true;
		}
	}
}
