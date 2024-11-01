<?php
/**
 * WP In Post Ads Widget class
 *
 * @since 1.0
 */
class WPIPA_Widget extends WP_Widget {

	function __construct() {

		$widget_ops = array( 'classname' => 'wpipa_widget', 'description' => __( 'Display ads', $this->get_plugin_name() ) );
		parent::__construct('wpipa_widget', __( 'Ad Inserter Widget', $this->get_plugin_name() ), $widget_ops );
	}

	function widget( $args, $instance ) {

		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$ads   = $instance['ads'];

		$test_user_conditions = $this->test_user_conditions();
		if ( $test_user_conditions ) {

			// Output
			echo $before_widget;

			if ( $title ) {

				echo $before_title . $title . $after_title;
			}

			if ( ! empty( $ads ) ) {

				foreach ( $ads as $ad_id ) {

					if ( ! empty( $ad_id ) ) {

						echo do_shortcode('[wpipa id="'.$ad_id.'"]');
					}
				}
			}

			echo $after_widget;
		}
	}

	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['ads']   = $new_instance['ads'];

		//var_dump($instance['ads']);

		if (strpos($instance['ads'], ',') === false) {
            // No comma, must be single value - still needs to be in an array for now
            $post_ids = array( $instance['ads'] );
        } else {
            // There is a comma so it's explodable
            $post_ids = explode(',', $instance['ads']);
        }

        $instance['ads'] = $post_ids;

		return $instance;
	}

	function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title' => '',
				'ads'   => array(),
			)
		);

		$title = $instance['title'];
		$ads   = $instance['ads'];

		// Some entries may be arrays themselves!
	    $processed_item_ids = array();
	    foreach ($ads as $this_id) {
	        if (is_array($this_id)) {
	            $processed_item_ids = array_merge( $processed_item_ids, $this_id );
	        } else {
	            $processed_item_ids[] = $this_id;
	        }
	    }

	    if (is_array($processed_item_ids) && !empty($processed_item_ids)) {
	        $processed_item_ids = implode(',', $processed_item_ids);
	    } else {
	        $processed_item_ids = '';
	    }
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', $this->get_plugin_name() ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('ads'); ?>"><?php _e( 'Select Ads:', $this->get_plugin_name() ); ?></label><br />
			<input style="width: 400px;" type="hidden" id="<?php echo esc_attr( $this->get_field_id('ads') ); ?>" name="<?php echo esc_attr( $this->get_field_name('ads') ); ?>" class="wpipa-multi-select"  value="<?php echo $processed_item_ids; ?>" />
		</p>
	<?php
	}

	function get_plugin_name() {
		$wpipa = new WP_In_Post_Ads;

		return  $wpipa->get_plugin_name();
	}

	function get_version() {
		$wpipa = new WP_In_Post_Ads;

		return  $wpipa->get_version();
	}

	function test_user_conditions() {
		$wpipa_public = new WP_In_Post_Ads_Public( $this->get_plugin_name(), $this->get_version() );

		return  $wpipa_public->test_user_conditions();
	}
}