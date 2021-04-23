<?php
/**
 * Powered By Shortcode functions file.
 *
 * @package WP Plugin Template/Includes/Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode functions class.
 */
class WP_Plugin_Template_Shortcode_Powered_By extends WP_Plugin_Template_Shortcode {

	/**
	 * The single instance of WP_Plugin_Template_Shortcode_Powered_By.
	 *
	 * @var     WP_Plugin_Template_Shortcode_Powered_By|null
	 * @access  private
	 * @since   0.1.0
	 */
	private static $_instance = null; //phpcs:ignore

	/**
	 * Shortcode constructor.
	 *
	 * @param WP_Plugin_Template $parent Parent object.
	 */
	public function __construct( $parent ) {
		parent::__construct(
			$parent,
			'wppt_powered_by',
			array( $this, 'wppt_powered_by' )
		);
	}

	/**
	 * Build shortcode [wppt_powered_by].
	 *
	 * @param array                        $atts     Shortcode attributes. Default empty.
	 * @param string                       $content  Shortcode content. Default null.
	 * @param string                       $tag      Shortcode tag (name). Default empty.
	 * @param WP_Plugin_Template_Shortcode $shortcode Shortcode object. Default null.
	 *
	 * @return string|null The shortcode HTML content.
	 */
	public function wppt_powered_by( $atts = array(), $content = null, $tag = '', $shortcode = null ) {
		// Start section.
		$output = __( 'Powered by WP Plugin Template', 'wp-plugin-template' );

		// enclosing tags.
		if ( ! is_null( $content ) ) {
			// run shortcode parser recursively.
			$output .= do_shortcode( $content );
		}

		return $output;
	}

	/**
	 * Main WP_Plugin_Template_Shortcode_Powered_By Instance.
	 *
	 * Ensures only one instance of WP_Plugin_Template_Shortcode_Powered_By is loaded or can be loaded.
	 *
	 * @since 0.1.0
	 * @static
	 * @see WP_Plugin_Template()
	 * @param WP_Plugin_Template $parent Object instance.
	 * @return WP_Plugin_Template_Shortcode_Powered_By instance
	 */
	public static function instance( $parent ) {
		if ( null === self::$_instance ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cloning of WP_Plugin_Template_Shortcode_Powered_By is forbidden.', 'wp-plugin-template' ) ), esc_attr( $this->parent->_version ) );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 0.1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Unserializing instances of WP_Plugin_Template_Shortcode_Powered_By is forbidden.', 'wp-plugin-template' ) ), esc_attr( $this->parent->_version ) );
	} // End __wakeup()

}
