<?php
/**
 * Powered By Shortcode functions file.
 *
 * @package WP Plugin Template/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode functions class.
 */
class WP_Plugin_Template_Shortcode_Powered_By extends WP_Plugin_Template_Shortcode {

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

}
