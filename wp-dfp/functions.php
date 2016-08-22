<?php defined( 'ABSPATH' ) or die( 'No direct access please!' );

/**
 * Helper functions
 *
 * @package WordPress
 * @subpackage WP_DFP
 */

if ( !function_exists( 'wp_dfp_ad_slot' ) ) {

	/**
	 * Gets an instance of the WP_DFP_Ad_Slot class
	 *
	 * @since 1.0
	 *
	 * @param string|WP_Post $slot The name of the ad slot or a WP_Post object.
	 *
	 * @return WP_DFP_Ad_Slot
	 */
	function wp_dfp_ad_slot( $slot ) {
		if ( !class_exists( 'WP_DFP_Ad_Slot' ) ) {
			WP_DFP::inc( 'class-wp-dfp-ad-slot.php' );
		}

		try {
			return new WP_DFP_Ad_Slot( $slot );
		} catch ( InvalidArgumentException $e ) {
			return new WP_Error();
		}
	}

}

if ( !function_exists( 'wp_parse_html_atts' ) ) {

	/**
	 * Gets an array of HTML attributes as a string
	 *
	 * @since 1.0
	 *
	 * @param array $atts An array of HTML attributes where the key is the attribute name and value is the attribute value.
	 *
	 * @return string
	 */
	function wp_parse_html_atts( array $atts ) {
		foreach ( $atts as $name => &$value ) {
			if ( is_array( $value ) ) {
				$value = join( ' ', $value );
			}

			$value = $name . '="' . esc_attr( $value ) . '"';
		}
		
		return join( ' ', $atts );
	}

}

if ( !function_exists( 'wp_dfp_settings_url' ) ) {

	/**
	 * Gets the URL to the WP_DFP settings page
	 *
	 * @since 1.0
	 *
	 * @return string      The URL to the WP_DFP settings page.
	 */
	function wp_dfp_settings_url() {
		return admin_url( 'edit.php?post_type=' . WP_DFP::POST_TYPE . '&page=wp_dfp_settings' );
	}

}


if ( !function_exists( 'wp_dfp_get_ad_positions' ) ) {
	/**
	 * Returns array of DFP_Ad_Position objects
	 *
	 * @since 0.2.5
	 *
	 * @return array
	 */
	function wp_dfp_get_ad_positions() {
		$args = array(
			'post_type'           => WP_DFP::POST_TYPE,
			'post_status'         => 'publish',
			'posts_per_page'      => - 1,
			'ignore_sticky_posts' => 1
		);
		/**
		 * @var WP_Query $all_ads
		 */
		$all_ads = new WP_Query( $args );
		$positions = array();
		if ( $all_ads->have_posts() ) {
			while ( $all_ads->have_posts() ) :
				$all_ads->the_post();
				$positions[] = get_post();
			endwhile;
		}
		//foreach ( $positions as $key => $position ) {
		//	if ( $position->post_id == null ) {
		//		unset( $positions[ $key ] );
		//	}
		//}
		wp_reset_query();
		return $positions;
	}

}


if ( !function_exists( 'wp_dfp_ad_select_options' ) ) {
	/**
	 * Creates Select Options for widget
	 *
	 * @since  0.2.0
	 * @access public
	 *
	 * @param int|string $value Value
	 */
	function wp_dfp_ad_select_options( $value ) {
		echo '<option value="false">Select Position</option>';
		$positions = wp_dfp_get_ad_positions();
		foreach ( $positions as $position ) {
			echo '<option' . selected( $value, $position->post_name ) . ' value="' . $position->post_name . '">(' . $position->post_name . ') ' . $position->post_title . '</option>';
		}
	}
}