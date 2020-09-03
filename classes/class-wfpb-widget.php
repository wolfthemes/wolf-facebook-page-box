<?php
/**
 * Facebook Page Box Widget
 *
 * Displays a facebook page box plugin
 *
 * @author WpWolf
 * @category Widgets
 * @package WolfFacebookPageBox
 * @version 1.0.0
 * @extends WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WFPB_Widget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Widget settings
		$ops = array( 'classname' => 'widget_facebook_page_box', 'description' => esc_html__( 'Display a Facebook page box', 'wolf-facebook-page-box' ) );

		// Create the widget
		parent::__construct( 'widget_facebook_page_box', esc_html__( 'Facebook Page Box', 'wolf-facebook-page-box' ), $ops );

	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @param array $args
	 * @param array $instance
	 */
	function widget( $args, $instance ) {

		extract( $args );
		$title = ( isset( $instance['title'] ) ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$page_url = ( isset( $instance['page_url'] ) ) ? esc_url( $instance['page_url'] ) : 'https://www.facebook.com/wolfthemes/';
		$height = ( isset( $instance['height'] ) ) ? absint( $instance['height'] ) : 400;
		$hide_cover = ( isset( $instance['hide_cover'] ) && $instance['hide_cover'] ) ? true : false;
		$show_posts = ( isset( $instance['show_posts'] ) ) ? true : false;
		$show_faces = ( isset( $instance['show_faces'] ) ) ? true : false;
		$small_header = ( isset( $instance['small_header'] ) ) ? true : false;

		echo $before_widget;
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
		echo WFPB()->facebook_box( $page_url, $height, $hide_cover, $show_posts, $show_faces, $small_header );
		echo $after_widget;

	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['page_url'] = esc_url( $new_instance['page_url'] );
		$instance['height'] = absint( $new_instance['height'] );
		$instance['hide_cover'] = ( $new_instance['hide_cover'] ) ? true : false;
		$instance['show_posts'] = ( $new_instance['show_posts'] ) ? true : false;
		$instance['show_faces'] = ( $new_instance['show_faces'] ) ? true : false;
		$instance['small_header'] = ( $new_instance['small_header'] ) ? true : false;
		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @param array $instance
	 */
	function form( $instance ) {

		// Set up some default widget settings
		$defaults = array(
			'title' => esc_html__( 'Join us', 'wolf-facebook-page-box' ),
			'page_url' => 'https://www.facebook.com/wolfthemes',
			'height' => 400,
			'hide_cover' => 0,
			'show_posts' => 1,
			'show_faces' => 1,
			'small_header' => 0,
			);
		$instance = wp_parse_args( ( array ) $instance, $defaults);
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e(  'Title' , 'wolf-facebook-page-box' ); ?>:</label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'page_url' ) ); ?>"><?php esc_html_e(  'Facebook Page URL' , 'wolf-facebook-page-box' ); ?>:</label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'page_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'page_url' ) ); ?>" value="<?php echo esc_url( $instance['page_url'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>"><?php esc_html_e( 'height', 'wolf-facebook-page-box' ); ?>:</label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" value="<?php echo absint( $instance['height'] ); ?>">
			<br>
			<small><?php sprintf( esc_html__( 'default is %s', 'wolf-facebook-page-box' ), '400px' ); ?></small>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_posts'], true ) ?> id="<?php echo esc_attr( $this->get_field_id( 'show_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_posts' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_posts' ) ); ?>"><?php esc_html_e( 'Show posts', 'wolf-facebook-page-box' ); ?></label>
			<br>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_faces'], true ) ?> id="<?php echo esc_attr( $this->get_field_id( 'show_faces' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_faces' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_faces' ) ); ?>"><?php esc_html_e( 'Show faces', 'wolf-facebook-page-box' ); ?></label>
			<br>
			<input class="checkbox" type="checkbox" <?php checked( $instance['small_header'], true ) ?> id="<?php echo esc_attr( $this->get_field_id( 'small_header' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'small_header' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'small_header' ) ); ?>"><?php esc_html_e( 'Small header', 'wolf-facebook-page-box' ); ?></label>
			<br>
			<input class="checkbox" type="checkbox" <?php checked( $instance['hide_cover'], true ) ?> id="<?php echo esc_attr( $this->get_field_id( 'hide_cover' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_cover' ) ); ?>">
			<label for="<?php echo esc_attr( $this->get_field_id( 'hide_cover' ) ); ?>"><?php esc_html_e( 'Hide cover', 'wolf-facebook-page-box' ); ?></label>
		</p>
		<?php
	}
}