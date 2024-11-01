<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://plugin-boutique.com/simple-ad-inserter/
 * @since      1.0
 *
 * @package    simple-ad-inserter
 * @subpackage simple-ad-inserter/admin
 * @author     Sparkjones
 */
class WP_In_Post_Ads_Admin {

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

	private $supported_post_types;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0
	 */
	public function enqueue_styles() {

		$screen = get_current_screen();
		$screen_id = $screen->id;

		if ( 'mts_ad_page_wp-in-post-ads' === $screen_id || 'mts_ad' === $screen_id ) {

			wp_enqueue_style( 'wp-color-picker' );

			wp_enqueue_style( $this->plugin_name.'_options', plugin_dir_url( __FILE__ ) . 'css/wp-in-post-ads-options.css', array(), $this->version, 'all' );
		}

		if ( 'widgets' === $screen_id ) {

			wp_enqueue_style( $this->plugin_name.'_select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();
		$screen_id = $screen->id;

		if ( 'mts_ad_page_wp-in-post-ads' === $screen_id || 'mts_ad' === $screen_id ) {

			wp_enqueue_script( 'wp-color-picker' );

			wp_enqueue_script( $this->plugin_name.'_options', plugin_dir_url( __FILE__ ) . 'js/wp-in-post-ads-option.js', array( 'jquery' ), $this->version, false );
		}

		if ( 'widgets' === $screen_id ) {

			wp_enqueue_script( $this->plugin_name.'_select2', plugin_dir_url( __FILE__ ) . 'js/select2.full.min.js', array('jquery'), $this->version, false );
			wp_enqueue_script( $this->plugin_name.'_widget', plugin_dir_url( __FILE__ ) . 'js/wp-in-post-ads-widget.js', array('jquery'), $this->version, false );
		}
	}

	/**
	 * Register "WP In Post Ad" Post Type, attached to 'init'
	 *
	 * @since    1.0
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'Ad Inserter', 'post type general name', $this->plugin_name ),
			'singular_name'      => _x( 'Ad Inserter', 'post type singular name', $this->plugin_name ),
			'menu_name'          => _x( 'Ad Inserter', 'admin menu', $this->plugin_name ),
			'name_admin_bar'     => _x( 'Ad Inserter', 'add new on admin bar', $this->plugin_name ),
			'add_new'            => _x( 'Add New Ad', 'notification bar', $this->plugin_name ),
			'add_new_item'       => __( 'Add Post Ad', $this->plugin_name ),
			'new_item'           => __( 'New Post Ad', $this->plugin_name ),
			'edit_item'          => __( 'Edit Post Ad', $this->plugin_name ),
			'view_item'          => __( 'View Post Ad', $this->plugin_name ),
			'all_items'          => __( 'All Ads', $this->plugin_name ),
			'search_items'       => __( 'Search Post Ads', $this->plugin_name ),
			'parent_item_colon'  => __( 'Parent Post Ads:', $this->plugin_name ),
			'not_found'          => __( 'No Ads found.', $this->plugin_name ),
			'not_found_in_trash' => __( 'No Ads found in Trash.', $this->plugin_name )
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'show_ui' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => false,
			'publicly_queryable' => false,
			'menu_position' => 100,
			'menu_icon' => 'dashicons-media-document',
			'has_archive' => false,
			'supports' => array('title', 'editor')
		);

		register_post_type( 'mts_ad' , $args );
	}

	//////////////////////
	////// Settings //////
	//////////////////////

	/**
	 * Register the administration menu, attached to 'admin_menu'
	 *
	 * @since 1.0
	 */
	public function plugin_admin_menu() {

		add_submenu_page(
			'edit.php?post_type=mts_ad',
			__( 'Ads Settings', $this->plugin_name ),
			__( 'Settings', $this->plugin_name ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page.
	 *
	 * @since 1.0
	 */
	public function display_plugin_admin_page() {
	?>
		<div class="wrap">
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab nav-tab-active" href="#wpipa-general-section"><?php _e( 'General', $this->plugin_name ); ?></a>
				<a class="nav-tab" href="#wpipa-groups-section"><?php _e( 'Ad Groups', $this->plugin_name ); ?></a>
				<a class="nav-tab" href="#wpipa-defaults-section"><?php _e( 'Design & Size', $this->plugin_name ); ?></a>
				<a class="nav-tab" href="#wpipa-ga-section"><?php _e( 'Boost your Seo', $this->plugin_name ); ?></a>
			</h2>
			<form method="post" action="options.php">
				<?php settings_fields( $this->plugin_name ); ?>
				<?php do_settings_sections( $this->plugin_name ); ?>
				</div><!-- close last tab -->
				<?php submit_button(); ?>
			</form>
		</div>
	<?php
	}

	/**
	 * Creates our settings sections with fields etc.
	 *
	 * @since    1.0
	 */
	public function settings_api_init() {

		$this->supported_post_types = array(
			'post' => __( 'Posts', $this->plugin_name ),
			'page' => __( 'Pages', $this->plugin_name ),
		);

		$args = array(
			'public' => true,
			'publicly_queryable'=> true,
			'_builtin'=> false,
		);
		$custom_post_types = get_post_types( $args, 'objects' );

		foreach ( $custom_post_types as $post_type ) {

			$this->supported_post_types[ $post_type->name ] = $post_type->labels->name;
		}

		// Add a new setting to the options table
		register_setting( $this->plugin_name, 'wpipa_settings' );

		// Add general section
		add_settings_section(
			'general_settings_section',
			__( 'General Settings', $this->plugin_name ),
			array( $this, 'general_settings_section_callback' ),
			$this->plugin_name
		);

		// Add general section fields
	 	add_settings_field(
			'wpipa_supported_post_types',
			__( 'Supported Post Types:', $this->plugin_name ),
			array( $this, 'options_multiselect_callback' ),
			$this->plugin_name,
			'general_settings_section',
			array(
				'id' => 'wpipa_supported_post_types',
				'options' => $this->supported_post_types,
				'default'=> array('post'),
				'input_class' => '',
				'atts' => '',
				'class' => 'form-field'
			)
		);

		add_settings_field(
			'wpipa_hide_for_logged',
			__( 'Hide Ads for logged in users?', $this->plugin_name ),
			array( $this, 'options_checkbox_callback' ),
			$this->plugin_name,
			'general_settings_section',
			array(
				'id' => 'wpipa_hide_for_logged',
				'label' =>  __( 'Check if you want to hide the ads for logged in users.', $this->plugin_name ),
				'default'=> false,
				'input_class' => '',
				'atts' => '',
				'class' => 'form-field'
			)
		);

		// Add groups section
		add_settings_section(
			'group_settings_section',
			__( 'Ad Groups', $this->plugin_name ),
			array( $this, 'group_settings_section_callback' ),
			$this->plugin_name
		);

		// Add groups section fields
		add_settings_field(
			'wpipa_groups',
			__( 'How many ads to show on same placement?', $this->plugin_name ),
			array( $this, 'options_select_callback' ),
			$this->plugin_name,
			'group_settings_section',
			array(
				'id' => 'wpipa_groups',
				'options' => array(
					'all'    => __( 'Show all ads', $this->plugin_name ),
					'single' => __( 'Show only one ad', $this->plugin_name ),
				),
				'default'=> 'single',
				'input_class' => 'wpipa-has-child-opt',
				'atts' => '',
				'class' => 'form-field'
			)
		);

		add_settings_field(
			'wpipa_ads_orderby',
			__( 'Order Ads By:', $this->plugin_name ),
			array( $this, 'options_select_callback' ),
			$this->plugin_name,
			'group_settings_section',
			array(
				'id' => 'wpipa_ads_orderby',
				'options' => array(
					'date'     => __( 'Date Published', $this->plugin_name ),
					'priority' => __( 'Priority', $this->plugin_name ),
					'rand'     => __( 'Random', $this->plugin_name ),
				),
				'default'=> 'date',
				'input_class' => '',
				'atts' => '',
				'class' => 'form-field'
			)
		);

		add_settings_field(
			'wpipa_ads_order',
			__( 'Order:', $this->plugin_name ),
			array( $this, 'options_select_callback' ),
			$this->plugin_name,
			'group_settings_section',
			array(
				'id' => 'wpipa_ads_order',
				'options' => array(
					'DESC' => __( 'DESC', $this->plugin_name ),
					'ASC'  => __( 'ASC', $this->plugin_name ),
				),
				'default'=> 'DESC',
				'input_class' => '',
				'atts' => '',
				'class' => 'form-field'
			)
		);


		// Add defaults section
		add_settings_section(
			'default_settings_section',
			__( 'Single Ad Defaults', $this->plugin_name ),
			array( $this, 'default_settings_section_callback' ),
			$this->plugin_name
		);



		// Add Google Analytics section
		add_settings_section(
			'ga_settings_section',
			__( '', $this->plugin_name ),
			array( $this, 'ga_settings_section_callback' ),
			$this->plugin_name
		);



	}

	/**
	 * General section callback function.
	 *
	 * @since 1.0
	 */
	public function general_settings_section_callback() {
		?>
		<div id="wpipa-general-section" class="tab-content active">
		<p><?php _e('Set general options.', $this->plugin_name ); ?></p>
		<?php
	}

	/**
	 * Group section callback function.
	 *
	 * @since 1.0
	 */
	public function group_settings_section_callback() {
		?>
		</div>
		<div id="wpipa-groups-section" class="tab-content">
		<p><?php _e('How to dislay ads if two or more of them are supposed to be shown on same placement.', $this->plugin_name ); ?></p>
		<?php
	}
	

	/**
	 * Single ad defaults section callback function.
	 *
	 * @since 1.0
	 */
	public function default_settings_section_callback() {
		?>
		</div>
		<div id="wpipa-defaults-section" class="tab-content">
		<p><?php _e('<h2 style="font-weight:900;">To unlock this feature, please upgrade to the <a href="http://plugin-boutique.com/simple-ad-inserter/">$9 version</a>. You can upgrade <a href="http://plugin-boutique.com/simple-ad-inserter/">here</a>.<br><br><br><br><br></h2>', $this->plugin_name ); ?></p>
		<?php
	}

	/**
	 * Google Analytics section callback function.
	 *
	 * @since 1.0
	 */
	public function ga_settings_section_callback() {
		?>
		</div>
		<div id="wpipa-ga-section" class="tab-content">
		<p><?php _e('<a href="http://seo-servicen.dk/en/"><img src="http://seo-servicen.dk/en/wp-content/uploads/2016/10/ad.png"></a><br>We have helped 300+ websites to top 10 in Google, 100+ of them are in top 3.<br> If you want us to help you, have a look at our seo services <a href="http://seo-servicen.dk/en/">here</a>.', $this->plugin_name ); ?></p>
		<?php
	}

	/**
	 * Placement option callback function.
	 *
	 * @since 1.0
	 */
	public function options_placement_callback( $args ) {

		$options = get_option( 'wpipa_settings' );

		$opt_val = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : $args['default'];
		$num_val = isset( $options['wpipa_number_p'] ) ? $options['wpipa_number_p'] : '1';
		?>
		<div>
			<input type="radio" name="wpipa_settings[<?php echo $args['id'];?>]" id="wpipa_settings_before_content" value="before_content" <?php checked( 'before_content', $opt_val, true ); ?> />
			<label for="wpipa_settings_before_content">
				<?php _e( 'Before Content', $this->plugin_name ); ?>
			</label>
		</div>
		<div>
			<input type="radio" name="wpipa_settings[<?php echo $args['id'];?>]" id="wpipa_settings_after_n_p" value="after_n_p" <?php checked( 'after_n_p', $opt_val, true ); ?> />
			<label for="wpipa_settings_after_n_p">
				<?php _e( 'After', $this->plugin_name ); ?>
				<input type="number" step="1" min="1" name="wpipa_settings[wpipa_number_p]" id="wpipa_settings_wpipa_number_p" value="<?php echo esc_attr( $num_val ); ?>" class="small-text">
				<?php _e( 'Paragraphs.', $this->plugin_name ); ?>
			</label>
		</div>
		<div>
			<input type="radio" name="wpipa_settings[<?php echo $args['id'];?>]" id="wpipa_settings_after_content" value="after_content" <?php checked( 'after_content', $opt_val, true ); ?> />
			<label for="wpipa_settings_after_content">
				<?php _e( 'After Content', $this->plugin_name ); ?>
			</label>
		</div>
		<?php
	}

	/**
	 * Select option callback function.
	 *
	 * @since 1.0
	 */
	public function options_select_callback( $args ) {

		$options = get_option( 'wpipa_settings' );

		$opt_val = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : $args['default'];
		?>
		<select name="wpipa_settings[<?php echo $args['id'];?>]" id="wpipa_settings_<?php echo $args['id'];?>" class="<?php echo $args['input_class'];?>" <?php echo $args['atts'];?>>
		<?php foreach ( $args['options'] as $val => $label ) { ?>
			<option value="<?php echo $val; ?>" <?php selected( $opt_val, $val, true); ?>><?php echo $label ?></option>
		<?php } ?>
		</select>
		<?php
	}

	/**
	 * Multi select option callback function.
	 *
	 * @since 1.0
	 */
	public function options_multiselect_callback( $args ) {

		$options = get_option( 'wpipa_settings' );

		$opt_val = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : $args['default'];
		?>
		<select multiple class="wpipa-multi-select <?php echo $args['input_class']; ?>" name="wpipa_settings[<?php echo $args['id'];?>][]" id="wpipa_settings_<?php echo $args['id'];?>" <?php echo $args['atts'];?>>
			<?php
			if ( !empty( $args['options'] ) ) {
				foreach ( $args['options'] as $id => $name ) {

					$selected =  in_array( $id, $opt_val ) ? ' selected="selected"' : '';
					?>
					<option value="<?php echo esc_attr( $id );?>"<?php echo $selected; ?>><?php echo esc_html( $name ); ?></option>
					<?php
				}
			}
			?>
		</select>
		<?php
	}

	/**
	 * Checkbox option callback function.
	 *
	 * @since 1.0
	 */
	public function options_checkbox_callback( $args ) {

		$options = get_option( 'wpipa_settings' );

		$opt_val = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : $args['default'];

		$html = '<input type="checkbox" id="wpipa_settings_' . $args['id'] . '" name="wpipa_settings[' . $args['id'] . ']" value="1" ' . checked( 1, $opt_val, false ) . ' class="'.$args['input_class'].'" '.$args['atts'].'/>';
		$html .= '<label for="wpipa_settings_' . $args['id'] . '"> ' . $args['label'] . '</label>';
		
		echo $html;
	}

	/**
	 * Number option callback function.
	 *
	 * @since 1.0
	 */
	public function options_number_callback( $args ) {

		$options = get_option( 'wpipa_settings' );

		$opt_val = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : $args['default'];
		?>
		<input type="number" step="1" min="<?php echo $args['min'];?>" name="wpipa_settings[<?php echo $args['id'];?>]" id="wpipa_settings_<?php echo $args['id'];?>" value="<?php echo $opt_val;?>" class="<?php echo $args['input_class'];?>" <?php echo $args['atts'];?>/>
		<?php
	}

	/**
	 * Color option callback function.
	 *
	 * @since 1.0
	 */
	public function options_color_callback( $args ) {

		$options = get_option( 'wpipa_settings' );

		$opt_val = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : $args['default'];
		?>
		<input type="text" name="wpipa_settings[<?php echo $args['id'];?>]" id="wpipa_settings_<?php echo $args['id'];?>" value="<?php echo $opt_val;?>" class="wpipa-color-picker <?php echo $args['input_class'];?>" data-default-color="<?php echo $args['default'];?>"  <?php echo $args['atts'];?>/>
		<?php
	}

	/**
	 * Color option callback function.
	 *
	 * @since 1.0
	 */
	public function options_text_callback( $args ) {

		$options = get_option( 'wpipa_settings' );

		$opt_val = isset( $options[ $args['id'] ] ) ? $options[ $args['id'] ] : $args['default'];
		?>
		<input type="text" name="wpipa_settings[<?php echo $args['id'];?>]" id="wpipa_settings_<?php echo $args['id'];?>" value="<?php echo $opt_val;?>" class="<?php echo $args['input_class'];?>" <?php echo $args['atts'];?>/>
		<?php
	}

	/**
	 * Add shortcode code
	 *
	 * @since    1.0
	 */
	public function shortcode_code() {
		global $post;
		if ( 'mts_ad' === $post->post_type ) {
			echo '<div class="misc-pub-section">';
				echo '<p><i>'.__( 'If you want to insert this ad manually, use the following shortcode:', $this->plugin_name ).'</i></p>';
				echo '<p><code>[wpipa id="'.$post->ID.'"]</code></p>';
			echo '</div>';
		}
	}

	// Add meta box to single editor of supported post types
	public function wpipa_metabox_insert() {
		$options = get_option( 'wpipa_settings' );
		$screens = isset( $options['wpipa_supported_post_types'] ) ? $options['wpipa_supported_post_types'] : array();
		foreach ( $screens as $screen ) {
			add_meta_box(
				'wpipa_metabox',
				__('WP In Post Ads', $this->plugin_name),
				array( $this, 'wpipa_metabox_content' ),
				$screen,
				'side',
				'default'
			);
		}
	}
	

	public function wpipa_metabox_content( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field('wpipa_metabox_save', 'wpipa_metabox_nonce');

		/*
		* Use get_post_meta() to retrieve an existing value
		* from the database and use the value for the form.
		*/
		$disable_ads = get_post_meta( $post->ID, '_wpipa_disable_ads', true );
		?>
		<p>
			<label for="wpipa_disable_ads_field">
				<input type="checkbox" name="wpipa_disable_ads_field" id="wpipa_disable_ads_field" <?php checked( $disable_ads, 'yes' ); ?> value="1" />
				<?php _e( 'Disable in post ads', $this->plugin_name ); ?>
			</label>
		</p>
		<?php
	}

	public function wpipa_metabox_save( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['wpipa_metabox_nonce'] ) ) {
			return;
		}
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['wpipa_metabox_nonce'], 'wpipa_metabox_save' ) ) {
			return;
		}
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return;

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return;
		}

		/* OK, its safe for us to save the data now. */
		if ( isset( $_POST['wpipa_disable_ads_field'] ) ) {
			$val = 'yes';
		} else {
			$val = 'no';
		}

		update_post_meta( $post_id, '_wpipa_disable_ads', $val );
	}

	// Add "Ad Settings" meta box
	public function wpipa_single_metabox() {
		add_meta_box(
			'wpipa_single_metabox',
			__('Ad Settings', $this->plugin_name),
			array( $this, 'wpipa_single_metabox_content' ),
			'mts_ad',
			'normal',
			'high'
		);
	}

	public function wpipa_single_metabox_content( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field('wpipa_single_metabox_save', 'wpipa_single_metabox_nonce');

		// Use global options to set defaults
		$options = get_option( 'wpipa_settings' );
		$wpipa_position  = isset( $options['wpipa_placement'] ) ? $options['wpipa_placement'] : 'before_content';
		$wpipa_number_p  = isset( $options['wpipa_number_p'] ) ? $options['wpipa_number_p'] : '1';
		$wpipa_ad_align  = isset( $options['wpipa_ad_align'] ) ? $options['wpipa_ad_align'] : 'center';
		global $content_width;
		$wpipa_ad_width  = isset( $options['wpipa_ad_width'] ) ? $options['wpipa_ad_width'] : $content_width;
		$wpipa_padding   = isset( $options['wpipa_padding'] ) ? $options['wpipa_padding'] : '20';
		$wpipa_bg_color  = isset( $options['wpipa_bg_color'] ) ? $options['wpipa_bg_color'] : '#333';
		$wpipa_txt_color = isset( $options['wpipa_txt_color'] ) ? $options['wpipa_txt_color'] : '#ffffff';

		/*
		* Use get_post_meta() to retrieve an existing value
		* from the database and use the value for the form.
		*/
		$settings = get_post_meta( $post->ID, '_wpipa_single_settings', true );
		$position = isset( $settings['position'] ) ? $settings['position'] : $wpipa_position;
		$number_p = isset( $settings['number_p'] ) ? $settings['number_p'] : $wpipa_number_p;
		$priority = isset( $settings['priority'] ) ? $settings['priority'] : '1';
		
		$align    = isset( $settings['align'] ) ? $settings['align'] : $wpipa_ad_align;
		$width    = isset( $settings['width'] ) ? $settings['width'] : $wpipa_ad_width;
		$padding  = isset( $settings['padding'] ) ? $settings['padding'] : $wpipa_padding;
		$bg_color = isset( $settings['bg_color'] ) ? $settings['bg_color'] : $wpipa_bg_color;
		$color    = isset( $settings['color'] ) ? $settings['color'] : $wpipa_txt_color;

		$show_after      = isset( $settings['show_after'] ) ? true : false;
		$show_after_days = isset( $settings['show_after_days'] ) ? $settings['show_after_days'] : '1';

		$show_for      = isset( $settings['show_for'] ) ? true : false;
		$show_for_days = isset( $settings['show_for_days'] ) ? $settings['show_for_days'] : '1';


		?>
		<div class="wpipa-form-column">
			<h2><?php _e( 'Placement:', $this->plugin_name ); ?></h2>
			<div class="form-field">
				<input type="radio" name="wpipa_fields[position]" id="wpipa_fields_position_before_content" value="before_content" <?php checked( 'before_content', $position, true ); ?> />
				<label for="wpipa_fields_position_before_content">
					<?php _e( 'Before Content', $this->plugin_name ); ?>
				</label>
			</div>
			<div class="form-field">
				<input type="radio" name="wpipa_fields[position]" id="wpipa_fields_position_after_n_p" value="after_n_p" <?php checked( 'after_n_p', $position, true ); ?> />
				<label for="wpipa_fields_position_after_n_p">
					<?php _e( 'After', $this->plugin_name ); ?>
					<input type="number" step="1" min="1" name="wpipa_fields[number_p]" id="wpipa_fields_number_p" value="<?php echo esc_attr( $number_p ); ?>" class="small-text">
					<?php _e( 'Paragraphs.', $this->plugin_name ); ?>
				</label>
			</div>
			<div class="form-field">
				<input type="radio" name="wpipa_fields[position]" id="wpipa_fields_position_after_content" value="after_content" <?php checked( 'after_content', $position, true ); ?> />
				<label for="wpipa_fields_position_after_content">
					<?php _e( 'After Content', $this->plugin_name ); ?>
				</label>
			</div>
			<div class="form-field">
				<label for="wpipa_fields_priority">
					<?php _e( 'Ad Priority:', $this->plugin_name ); ?>
					<input type="number" step="1" min="1" name="wpipa_fields[priority]" id="wpipa_fields_priority" value="<?php echo $priority; ?>" class="small-text"/>
				</label>
			</div>
		</div>
		<div class="wpipa-form-column last">
			<h2><?php _e( 'Ad Design & Size:', $this->plugin_name ); ?></h2>
			<p><strong>To unlock this feature please upgrade to the <a href="http://plugin-boutique.com/simple-ad-inserter/">$9 version</a>, you can upgrade <a href="http://plugin-boutique.com/simple-ad-inserter/">here</a>.</strong></p>
		</div>
		<div class="wpipa-form-column">
			<h2><?php _e( 'Behavior:', $this->plugin_name ); ?></h2>
			<div class="form-field">
				<label for="wpipa_fields_show_after">
					<input type="checkbox" name="wpipa_fields[show_after]" id="wpipa_fields_show_after" value="1" class="wpipa-checkbox-toggle-enabled" <?php checked( 1, $show_after, true ); ?> />
					<?php _e( 'Show ad after:', $this->plugin_name ); ?>
					<input type="number" step="1" min="1" name="wpipa_fields[show_after_days]" id="wpipa_fields_show_after_days" value="<?php echo $show_after_days; ?>" class="small-text"/>
					<?php _e( 'days from the day the post was published.', $this->plugin_name ); ?>
				</label>
			</div>
			<div class="form-field">
				<label for="wpipa_fields_show_for">
					<input type="checkbox" name="wpipa_fields[show_for]" id="wpipa_fields_show_for" value="1" class="wpipa-checkbox-toggle-enabled" <?php checked( 1, $show_for, true ); ?> />
					<?php _e( 'Show ad for:', $this->plugin_name ); ?>
					<input type="number" step="1" min="1" name="wpipa_fields[show_for_days]" id="wpipa_fields_show_for_days" value="<?php echo $show_for_days; ?>" class="small-text"/>
					<?php _e( 'days.', $this->plugin_name ); ?>
				</label>
			</div>
		</div>
		<?php
	}
	

	public function wpipa_single_metabox_save( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['wpipa_single_metabox_nonce'] ) ) {
			return;
		}
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['wpipa_single_metabox_nonce'], 'wpipa_single_metabox_save' ) ) {
			return;
		}
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return;

		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return;
		}

		/* OK, it's safe for us to save the data now. */
		if ( ! isset( $_POST['wpipa_fields'] ) ) {
			return;
		}

		$wpipa_fields = $_POST['wpipa_fields'];

		// Update the meta field in the database.
		update_post_meta( $post_id, '_wpipa_single_settings', $wpipa_fields );

		update_post_meta( $post_id, '_wpipa_position', $wpipa_fields['position'] );
		update_post_meta( $post_id, '_wpipa_priority', $wpipa_fields['priority'] );

		// Update option which holds Ad IDs on paragraphs
		$existing_arr = get_option( 'wpipa_inside_post_ads' );

		foreach ( $existing_arr as $p => $ids_arr ) {

			// Remove ad id from previous place if needed
			if ( ( $key = array_search( $post_id, $ids_arr ) ) !== false && $p !== $wpipa_fields['number_p'] ) {

				unset( $existing_arr[ $p ][ $key ] );
			}
		}

		if ( 'after_n_p' === $wpipa_fields['position'] ) {

			// Insert ad id in proper place if it's not there already
			if ( isset( $existing_arr[ $wpipa_fields['number_p'] ] ) ) {

				if ( !in_array( $post_id, $existing_arr[ $wpipa_fields['number_p'] ] ) ) {

					array_push( $existing_arr[ $wpipa_fields['number_p'] ], $post_id );
				}

			} else {

				$existing_arr[ $wpipa_fields['number_p'] ] = array( $post_id );
			}
		}

		ksort( $existing_arr );

		update_option( 'wpipa_inside_post_ads', $existing_arr );
	}

	/**
	 * Ad update messages
	 *
	 * @since    1.0
	 *
	 * @param array   $messages
	 * @return array   $messages
	 */
	public function mts_ad_update_messages( $messages ) {

		global $post;

		$post_ID = $post->ID;
		$post_type = get_post_type( $post_ID );

		if ('mts_ad' == $post_type ) {

			$messages['mts_ad'] = array(
                0 => '', // Unused. Messages start at index 1.
                1 => __( 'Ad updated.', $this->plugin_name ),
                2 => __( 'Custom field updated.', $this->plugin_name ),
                3 => __( 'Custom field deleted.', $this->plugin_name ),
                4 => __( 'Ad updated.', $this->plugin_name ),
                5 => isset($_GET['revision']) ? sprintf( __('Ad restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
                6 => __( 'Ad published.', $this->plugin_name ),
                7 => __( 'Ad saved.', $this->plugin_name ),
                8 => __( 'Ad submitted.', $this->plugin_name),
                9 => sprintf( __('Ad scheduled for: <strong>%1$s</strong>.', $this->plugin_name ), date_i18n( __( 'M j, Y @ H:i' ), strtotime( $post->post_date ) ) ),
				10 => __('Ad draft updated.', $this->plugin_name ),
        	);
        }

        return $messages;
    }

	/**
	 * Add custom columns to "mts_ad" listing table
	 *
	 * @since    1.0
	 *
	 * @param array   $columns
	 * @return array   $columns
	 */
	public function mts_ad_columns_head( $columns ) {

		$columns['shortcode'] =  __( 'Shortcode', $this->plugin_name );
		$columns['views']     =  __( 'Views', $this->plugin_name );

 		return $columns;
	}

	/**
	 * Add our column content
	 *
	 * @since    1.0
	 *
	 * @param string   $deprecated
	 * @param string   $column_name
	 * @param string   $term_id
	 * @return string   $icon
	 */
	public function mts_ad_column_content( $column, $post_id ) {

		if ( $column == 'shortcode') {

			echo '<code>[wpipa id="'.$post_id.'"]</code>';
		}

		if ( $column == 'views') {

			$opt_array = get_option('wpipa_ads_view_count');

			if ( isset( $opt_array[ $post_id ] ) ) {

				echo $opt_array[ $post_id ];

			} else {

				echo '0';
			}
		}
	}

	/**
	 * Function to register our widget
	 *
	 * @since    1.0
	 */
	public function wpipa_widget() {

		register_widget( 'WPIPA_Widget' );
	}

	/**
	 * Post select ajax function
	 *
	 * @since    1.0
	 */
	public function wpipa_get_ads() {

		$result = array();

		$search = $_REQUEST['q'];

		$ads_query = array(
			'posts_per_page' => -1,
			'post_status' => array('publish'),
			'post_type' => 'mts_ad',
			'order' => 'ASC',
			'orderby' => 'title',
			'suppress_filters' => false,
			's'=> $search
		);
		$posts = get_posts( $ads_query );

		// We'll return a JSON-encoded result.
		foreach ( $posts as $this_post ) {
			$post_title = $this_post->post_title;
			$id = $this_post->ID;

			$result[] = array(
				'id' => $id,
				'title' => $post_title,
			);
		}

	    echo json_encode( $result );

	    die();
	}

	public function get_post_titles() {
		$result = array();

		if (isset($_REQUEST['post_ids'])) {
			$post_ids = $_REQUEST['post_ids'];
			if (strpos($post_ids, ',') === false) {
				// There is no comma, so we can't explode, but we still want an array
				$post_ids = array( $post_ids );
			} else {
				// There is a comma, so it must be explodable
				$post_ids = explode(',', $post_ids);
			}
		} else {
			$post_ids = array();
		}

		if (is_array($post_ids) && ! empty($post_ids)) {

			$posts = get_posts(array(
				'posts_per_page' => -1,
				'post_status' => array('publish'),
				'post__in' => $post_ids,
				'post_type' => 'mts_ad'
			));
			foreach ( $posts as $this_post ) {
				$result[] = array(
					'id' => $this_post->ID,
					'title' => $this_post->post_title,
				);
			}
		}

		echo json_encode( $result );

		die();
	}
}