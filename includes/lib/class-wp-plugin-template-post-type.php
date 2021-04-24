<?php
/**
 * Post type declaration file.
 *
 * @package WP Plugin Template/Includes/Lib
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Post type declaration class.
 */
class WP_Plugin_Template_Post_Type {

	/**
	 * The name for the custom post type.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $post_type;

	/**
	 * The plural name for the custom post type posts.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $plural;

	/**
	 * The singular name for the custom post type posts.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $single;

	/**
	 * The description of the custom post type.
	 *
	 * @var     string
	 * @access  public
	 * @since   0.1.0
	 */
	public $description;

	/**
	 * The options of the custom post type.
	 *
	 * @var     array
	 * @access  public
	 * @since   0.1.0
	 */
	public $options;

	/**
	 * The default custom meta of the custom post type.
	 *
	 * @var     array
	 * @access  public
	 * @since   0.1.0
	 */
	public $defaults;

	/**
	 * The default custom fields of the custom post type.
	 *
	 * @var     array
	 * @access  public
	 * @since   0.1.0
	 */
	public $fields;

	/**
	 * The registered post type object.
	 *
	 * @var     WP_Post_Type|null
	 * @access  public
	 * @since   0.1.0
	 */
	public $post_type_ob;

	/**
	 * Constructor.
	 *
	 * @param string $post_type Post type.
	 * @param string $plural Post type plural name.
	 * @param string $single Post type singular name.
	 * @param string $description Post type description.
	 * @param array  $options Post type options.
	 * @param array  $defaults Post type default values.
	 * @param array  $fields Post type custom fields.
	 */
	public function __construct( $post_type = '', $plural = '', $single = '', $description = '', $options = array(), $defaults = array(), $fields = array() ) {
		if ( ! $post_type || ! $plural || ! $single ) {
			return;
		}

		// Post type name and labels.
		$this->post_type   = $post_type;
		$this->plural      = $plural;
		$this->single      = $single;
		$this->description = $description;
		$this->options     = $options;
		$this->defaults    = $defaults;
		$this->fields      = $fields;
		$this->_query_args = array(
			'post_type' => $this->post_type,
		);

		// Regsiter post type.
		add_action( 'init', array( $this, 'register_post_type' ) );

		// Register post type action on save.
		if ( ! empty( $this->defaults ) ) {
			add_action( "save_post_{$this->post_type}", array( $this, 'save_post_defaults' ), 10, 2 );
		}

		// Register post type custom fields.
		if ( ! empty( $this->fields ) ) {
			add_action( "add_meta_boxes_{$this->post_type}", array( $this, 'add_custom_meta_boxes' ) );
			add_filter( "{$this->post_type}_custom_fields", array( $this, 'post_custom_fields' ) );
		}

		// Display custom update messages for posts edits.
		add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_updated_messages' ), 10, 2 );
	}

	/**
	 * Register new post type
	 *
	 * @return void
	 */
	public function register_post_type() {
		//phpcs:disable
		$labels = array(
			'name'               => $this->plural,
			'singular_name'      => $this->single,
			'name_admin_bar'     => $this->single,
			'add_new'            => _x( 'Add New', $this->post_type, 'wp-plugin-template' ),
			'add_new_item'       => sprintf( __( 'Add New %s', 'wp-plugin-template' ), $this->single ),
			'edit_item'          => sprintf( __( 'Edit %s', 'wp-plugin-template' ), $this->single ),
			'new_item'           => sprintf( __( 'New %s', 'wp-plugin-template' ), $this->single ),
			'all_items'          => sprintf( __( 'All %s', 'wp-plugin-template' ), $this->plural ),
			'view_item'          => sprintf( __( 'View %s', 'wp-plugin-template' ), $this->single ),
			'search_items'       => sprintf( __( 'Search %s', 'wp-plugin-template' ), $this->plural ),
			'not_found'          => sprintf( __( 'No %s Found', 'wp-plugin-template' ), $this->plural ),
			'not_found_in_trash' => sprintf( __( 'No %s Found In Trash', 'wp-plugin-template' ), $this->plural ),
			'parent_item_colon'  => sprintf( __( 'Parent %s', 'wp-plugin-template' ), $this->single ),
			'menu_name'          => $this->plural,
		);
		//phpcs:enable

		$args = array(
			'labels'                => apply_filters( $this->post_type . '_labels', $labels ),
			'description'           => $this->description,
			'public'                => true,
			'publicly_queryable'    => true,
			'exclude_from_search'   => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'query_var'             => true,
			'can_export'            => true,
			'rewrite'               => true,
			'capability_type'       => 'post',
			'has_archive'           => true,
			'hierarchical'          => true,
			'show_in_rest'          => true,
			'rest_base'             => $this->post_type,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports'              => array( 'title', 'editor', 'excerpt', 'comments', 'thumbnail' ),
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-admin-post',
		);

		$args = array_merge( $args, $this->options );

		$this->post_type_ob = register_post_type( $this->post_type, apply_filters( $this->post_type . '_register_args', $args, $this->post_type ) );
	}

	/**
	 * Add meta boxes for custom post type.
	 *
	 * @return void
	 */
	public function add_custom_meta_boxes() {
		foreach ( $this->fields as $field ) {
			if ( isset( $field['metaboxes'] ) ) {
				foreach ( $field['metaboxes'] as $id => $metabox ) {
					$title         = isset( $metabox['title'] ) ? $metabox['title'] : '';
					$context       = isset( $metabox['context'] ) ? $metabox['context'] : 'advanced';
					$priority      = isset( $metabox['priority'] ) ? $metabox['priority'] : 'default';
					$callback_args = isset( $metabox['callback_args'] ) ? $metabox['callback_args'] : null;

					WP_Plugin_Template::instance()->admin_api->add_meta_box( $id, $title, $this->post_type, $context, $priority, $callback_args );
				}
			}
		}
	}

	/**
	 * Get post type custom fields.
	 *
	 * @return array post type custom fields
	 */
	public function post_custom_fields() {
		return $this->fields;
	}

	/**
	 * Set up admin messages for post type
	 *
	 * @param  array $messages Default message.
	 * @return array           Modified messages.
	 */
	public function updated_messages( $messages = array() ) {
		global $post, $post_ID;
		//phpcs:disable
		$messages[ $this->post_type ] = array(
			0  => '',
			1  => sprintf( __( '%1$s updated. %2$sView %3$s%4$s.', 'wp-plugin-template' ), $this->single, '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
			2  => __( 'Custom field updated.', 'wp-plugin-template' ),
			3  => __( 'Custom field deleted.', 'wp-plugin-template' ),
			4  => sprintf( __( '%1$s updated.', 'wp-plugin-template' ), $this->single ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s.', 'wp-plugin-template' ), $this->single, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( '%1$s published. %2$sView %3$s%4s.', 'wp-plugin-template' ), $this->single, '<a href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
			7  => sprintf( __( '%1$s saved.', 'wp-plugin-template' ), $this->single ),
			8  => sprintf( __( '%1$s submitted. %2$sPreview post%3$s%4$s.', 'wp-plugin-template' ), $this->single, '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', $this->single, '</a>' ),
			9  => sprintf( __( '%1$s scheduled for: %2$s. %3$sPreview %4$s%5$s.', 'wp-plugin-template' ), $this->single, '<strong>' . date_i18n( __( 'M j, Y @ G:i', 'wp-plugin-template' ), strtotime( $post->post_date ) ) . '</strong>', '<a target="_blank" href="' . esc_url( get_permalink( $post_ID ) ) . '">', $this->single, '</a>' ),
			10 => sprintf( __( '%1$s draft updated. %2$sPreview %3$s%4$s.', 'wp-plugin-template' ), $this->single, '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) . '">', $this->single, '</a>' ),
		);
		//phpcs:enable

		return $messages;
	}

	/**
	 * Set up bulk admin messages for post type
	 *
	 * @param  array $bulk_messages Default bulk messages.
	 * @param  array $bulk_counts   Counts of selected posts in each status.
	 * @return array                Modified messages.
	 */
	public function bulk_updated_messages( $bulk_messages = array(), $bulk_counts = array() ) {
		//phpcs:disable
		$bulk_messages[ $this->post_type ] = array(
			'updated'   => sprintf( _n( '%1$s %2$s updated.', '%1$s %3$s updated.', $bulk_counts['updated'], 'wp-plugin-template' ), $bulk_counts['updated'], $this->single, $this->plural ),
			'locked'    => sprintf( _n( '%1$s %2$s not updated, somebody is editing it.', '%1$s %3$s not updated, somebody is editing them.', $bulk_counts['locked'], 'wp-plugin-template' ), $bulk_counts['locked'], $this->single, $this->plural ),
			'deleted'   => sprintf( _n( '%1$s %2$s permanently deleted.', '%1$s %3$s permanently deleted.', $bulk_counts['deleted'], 'wp-plugin-template' ), $bulk_counts['deleted'], $this->single, $this->plural ),
			'trashed'   => sprintf( _n( '%1$s %2$s moved to the Trash.', '%1$s %3$s moved to the Trash.', $bulk_counts['trashed'], 'wp-plugin-template' ), $bulk_counts['trashed'], $this->single, $this->plural ),
			'untrashed' => sprintf( _n( '%1$s %2$s restored from the Trash.', '%1$s %3$s restored from the Trash.', $bulk_counts['untrashed'], 'wp-plugin-template' ), $bulk_counts['untrashed'], $this->single, $this->plural ),
		);
		//phpcs:enable

		return $bulk_messages;
	}

	/**
	 * Saves post type default custom meta fields.
	 *
	 * @param  int     $post_id   Post ID.
	 * @param  WP_Post $post  Post object.
	 */
	public function save_post_defaults( $post_id, WP_Post $post ) {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post' ) || $this->post_type !== $post->post_type ) {
			return;
		}

		foreach ( $this->defaults as $key => $value ) {
			switch ( $key ) {
				case 'meta':
					foreach ( $value as $meta_key => $meta_value ) {
						if ( ! get_post_meta( $post_id, $meta_key, true ) ) {
							update_post_meta( $post_id, $meta_key, $meta_value );
						}
					}
					break;

				default:
					if ( empty( $post->$key ) ) {
						$post->$key = $value;
					}
					break;
			}
		}
	}

	/**
	 * Retrieves an array of the latest posts, or posts matching the given criteria.
	 *
	 * @param  array $args      Arguments to retrieve posts.
	 * @return WP_Post[]|int[]  Array of post objects or post IDs.
	 */
	public function get_posts( $args = array() ) {
		$args = array_merge( $this->_query_args, $args );

		return get_posts( $args );
	}

	/**
	 * Retrieves a WP_Post instance of this post type.
	 *
	 * @param  int $post_id Post ID.
	 *
	 * @return WP_Post|false  Post object, false otherwise.
	 */
	public function get_instance( $post_id ) {
		global $wpdb;

		$post_id = (int) $post_id;
		if ( ! $post_id ) {
			return false;
		}

		$_post = wp_cache_get( $post_id, 'posts' );

		if ( ! $_post ) {
			$_post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d AND post_type = %s LIMIT 1", $post_id, $this->post_type ) );

			if ( ! $_post ) {
				return false;
			}

			$_post = sanitize_post( $_post, 'raw' );
			wp_cache_add( $_post->ID, $_post, 'posts' );
		} elseif ( empty( $_post->filter ) ) {
			$_post = sanitize_post( $_post, 'raw' );
		}

		return new WP_Post( $_post );
	}

	/**
	 * Create a new WP_Post instance of this post type.
	 *
	 * @return WP_Post  Post object.
	 */
	public function new_instance() {
		$post            = new WP_Post( $_post );
		$post->post_type = $this->post_type;

		// Initialize post with default meta.
		if ( ! empty( $this->meta ) ) {
			foreach ( $this->meta as $key => $value ) {
				$post->$key = $value;
			}
		}

		return $post;
	}

	/**
	 * Insert or update a post.
	 *
	 * @param  array $postarr An array of elements that make up a post to update or insert.
	 * @param  bool  $wp_error Whether to return a WP_Error on failure.
	 *
	 * @return int|WP_Error  The post ID on success. The value 0 or WP_Error on failure.
	 */
	public function insert( array $postarr, $wp_error = false ) {
		$postarr['post_type'] = $this->post_type;

		return wp_insert_post( $postarr, $wp_error );
	}

	/**
	 * Retrieves a WP_Post instance terms of this post type.
	 *
	 * @param  int    $post_id Post ID.
	 * @param  string $taxonomy Taxonomy name.
	 *
	 * @return WP_Term[]|false|WP_Error Array of WP_Term objects on success, false if there are no terms or the post does not exist, WP_Error on failure.
	 */
	public function get_terms( $post_id, $taxonomy ) {
		$post = $this->get_instance( $post_id );

		return get_the_terms( $post, $taxonomy );
	}

}
