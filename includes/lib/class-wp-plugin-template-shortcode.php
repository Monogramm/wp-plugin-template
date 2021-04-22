<?php
/**
 * Shortcode functions file.
 *
 * @package WP Plugin Template/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode functions class.
 */
class WP_Plugin_Template_Shortcode {

	/**
	 * The main plugin object.
	 *
	 * @var     WP_Plugin_Template|null
	 * @access  public
	 * @since   0.1.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin shortcode's output.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $base = '';

	/**
	 * The tag for the shortcode.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $tag;

	/**
	 * The callback function to run when the shortcode is found.
	 *
	 * Every shortcode callback is passed four parameters by default,
	 * including the reference to this shortcode instance ($shortcode),
	 * an array of attributes ($atts), the shortcode content or null
	 * if not set ($content), and finally the shortcode tag itself
	 * ($shortcode_tag), in that order.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $callback;

	/**
	 * The shortcode default attributes.
	 *
	 * @var     array
	 * @access  public
	 * @since   0.1.0
	 */
	public $default_atts;

	/**
	 * Shortcode constructor.
	 *
	 * @param WP_Plugin_Template $parent       Parent object.
	 * @param string             $tag          Shortcode tag.
	 * @param string             $callback     Shortcode callback.
	 * @param string             $default_atts Shortcode default attributes.
	 */
	public function __construct( $parent, $tag, $callback, $default_atts = array() ) {
		if ( ! $tag ) {
			return;
		}

		$this->parent = $parent;

		$this->base = 'wppt-';

		// Initialize shortcode.
		$this->tag          = $tag;
		$this->callback     = $callback;
		$this->default_atts = $default_atts;

		// Register shortcode.
		add_action( 'init', array( $this, 'register_shortcode' ) );
	}

	/**
	 * Register new shortcode.
	 *
	 * @return void
	 */
	public function register_shortcode() {
		add_shortcode( $this->tag, array( $this, 'do_shortcode' ) );
	}

	/**
	 * Run shortcode callback with normalized attributes merged default attributes.
	 *
	 * @param array  $atts     Shortcode attributes. Default empty.
	 * @param string $content  Shortcode content. Default null.
	 * @param string $tag      Shortcode tag (name). Default empty.
	 *
	 * @return string|null The shortcode HTML content.
	 */
	public function do_shortcode( $atts = array(), $content = null, $tag = '' ) {
		// normalize attribute keys, lowercase.
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );

		// override default attributes with user attributes.
		$sc_atts = shortcode_atts(
			$this->default_atts,
			$atts
		);

		return call_user_func( $this->callback, $sc_atts, $content, $tag, $this );
	}

}
