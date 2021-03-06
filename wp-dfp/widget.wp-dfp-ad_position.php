<?php
/**
 * Ad Position Widget
 *
 * Simple widget to include an ad position
 */

class WP_DFP_Ad_Widget extends WP_Widget {
	/**
	 * Unique identifier for your widget.
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * widget file.
	 *
	 * @access protected
	 * @since  1.1.8-psi
	 *
	 * @var      string
	 */
	protected $widget_slug = 'wp-dfp-ad-widget';
	/**
	 * Widget form fields
	 *
	 * Holds the fields that will be added to the widget form
	 *
	 * @access protected
	 * @since  1.1.8-psi
	 *
	 * @var      array
	 */
	protected $fields = array(
		'position_title'
	);
	/**
	 * PHP5 Constructor
	 *
	 * Specifies the class name and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 *
	 * @access public
	 * @since  1.1.8-psi
	 */
	public function __construct() {
		parent::__construct(
			$this->get_widget_slug(),
			__( 'WP DFP Ad Position', $this->get_widget_slug() ),
			array(
				'classname'   => $this->get_widget_slug() . '-class',
				'description' => __( 'Displays an ad position', $this->get_widget_slug() )
			)
		);
		// Refreshing the widget's cached output with each new post
		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}
	/**
	 * Outputs the content of the widget.
	 *
	 * @since  1.1.8-psi
	 * @access public
	 *
	 * @param array $args     The array of form elements
	 * @param array $instance The current instance of the widget
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( isset( $instance['position_title'] ) ) {
		  do_action('wp_dfp_render_ad', $instance['position_title']);
		}
		echo $args['after_widget'];
	}
	/**
	 * Generates the administration form for the widget.
	 *
	 * @since  1.1.8-psi
	 * @access public
	 *
	 * @param array instance The array of keys and values for the widget.
	 *
	 * @return mixed
	 */
	public function form( $instance ) {
		$id    = ( $this->get_field_id( 'position_title' ) !== null ? $this->get_field_id( 'position_title' ) : '' );
		$name  = ( $this->get_field_name( 'position_title' ) !== null ? $this->get_field_name( 'position_title' ) : '' );
		$value = ( isset( $instance['position_title'] ) ? $instance['position_title'] : '' );
		?>
		<h4><label for="<?php _e( $id ); ?>"><?php _e( 'Slot Name', 'wp-dfp' ); ?></label></h4>
		<p><span>id=<?php _e( $id ); ?></span>
		<span>name=<?php _e( $name ); ?></span>
		<span>value=<?php _e( $value ); ?></span>
			<select class="widefat" name="<?php _e( $name ); ?>" id="<?php _e( $id ); ?>">
				<?php wp_dfp_ad_select_options( $value ); ?>
			</select>
		</p>
		<?php
	}
	/**
	 * Processes the widget's options to be saved.
	 *
	 * @since  1.1.8-psi
	 * @access public
	 *
	 * @param array new_instance The new instance of values to be generated via the update.
	 * @param array old_instance The previous instance of values before the update.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		foreach ( $this->fields as $field ) {
			$instance[ $field ] = $new_instance[ $field ];
		}
		return $instance;
	}
	/**
	 * Return the widget slug.
	 *
	 * @since  1.1.8-psi
	 * @access public
	 *
	 * @return string Plugin slug variable.
	 */
	public function get_widget_slug() {
		return $this->widget_slug;
	}
	/**
	 * Flushes the cache
	 *
	 * @since  1.1.8-psi
	 * @access public
	 */
	public function flush_widget_cache() {
		wp_cache_delete( $this->get_widget_slug(), 'widget' );
	}
}