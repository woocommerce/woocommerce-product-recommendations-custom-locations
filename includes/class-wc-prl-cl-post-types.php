<?php
/**
 * WC_PRL_CL_Post_Types class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Product Recommendations - Custom Locations
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Recommendations Post Types Class.
 *
 * Registers custom post types and taxonomies.
 *
 * @class    WC_PRL_CL_Post_Types
 * @version  1.0.0
 */
class WC_PRL_CL_Post_Types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 6 );
		add_action( 'admin_bar_menu', array( __CLASS__, 'admin_bar_menu' ), 9999 );

		add_filter( 'post_updated_messages', array( __CLASS__, 'post_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( __CLASS__, 'bulk_post_updated_messages' ), 10, 2 );
	}

	/**
	 * Remove "New Location" item from Admin bar
	 */
	public static function admin_bar_menu( $admin_bar ) {
		$admin_bar->remove_menu( 'new-prl_hook' );
	}

	/**
	 * Register post types.
	 */
	public static function register_post_types() {

		if ( ! is_blog_installed() || post_type_exists( 'prl_hook' ) ) {
			return;
		}

		register_post_type(
			'prl_hook',
			array(
				'labels'              => array(
					'name'                  => __( 'Custom Locations', 'woocommerce-product-recommendations-custom-locations' ),
					'singular_name'         => __( 'Custom Location', 'woocommerce-product-recommendations-custom-locations' ),
					'all_items'             => __( 'Locations', 'woocommerce-product-recommendations-custom-locations' ),
					'menu_name'             => _x( 'Location', 'Admin menu name', 'woocommerce-product-recommendations-custom-locations' ),
					'add_new'               => __( 'Create new', 'woocommerce-product-recommendations-custom-locations' ),
					'add_new_item'          => __( 'Create new location', 'woocommerce-product-recommendations-custom-locations' ),
					'edit'                  => __( 'Edit', 'woocommerce-product-recommendations-custom-locations' ),
					'edit_item'             => __( 'Edit location', 'woocommerce-product-recommendations-custom-locations' ),
					'new_item'              => __( 'New location', 'woocommerce-product-recommendations-custom-locations' ),
					'view_item'             => __( 'View location', 'woocommerce-product-recommendations-custom-locations' ),
					'view_items'            => __( 'View locations', 'woocommerce-product-recommendations-custom-locations' ),
					'search_items'          => __( 'Search locations', 'woocommerce-product-recommendations-custom-locations' ),
					'not_found'             => self::no_locations_boarding(),
					'not_found_in_trash'    => __( 'No locations found in Trash', 'woocommerce-product-recommendations-custom-locations' ),
					'parent'                => __( 'Parent location', 'woocommerce-product-recommendations-custom-locations' ),
					'filter_items_list'     => __( 'Filter locations', 'woocommerce-product-recommendations-custom-locations' ),
					'items_list_navigation' => __( 'Location navigation', 'woocommerce-product-recommendations-custom-locations' ),
					'items_list'            => __( 'Locations list', 'woocommerce-product-recommendations-custom-locations' ),
				),
				'description'         => __( 'Create Custom Locations to display product recommendations on your store.', 'woocommerce-product-recommendations-custom-locations' ),
				'public'              => false,
				'show_ui'             => true,
				'capability_type'     => 'product',
				'map_meta_cap'        => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'hierarchical'        => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => array( 'title' ),
				'has_archive'         => false,
				'show_in_menu'        => false,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => true,
				'show_in_rest'        => false
			)
		);
	}

	/**
	 * Boarding HTML when no engines.
	 */
	public static function no_locations_boarding() {
		ob_start();
		?><div class="prl-custom-locations-empty-state">
			<p class="main">
				<?php esc_html_e( 'Create a Custom Location', 'woocommerce-product-recommendations-custom-locations' ); ?>
			</p>
			<p>
				<?php esc_html_e( 'Ready to offer custom product recommendations?', 'woocommerce-product-recommendations-custom-locations' ); ?>
				<br/>
				<?php esc_html_e( 'Start by creating an Custom Location. Then, deploy it using a shortcode.', 'woocommerce-product-recommendations-custom-locations' ); ?>
			</p>
			<a class="button sw-button-primary sw-button-primary--woo" id="sw-button-primary" href="<?php echo admin_url( 'post-new.php?post_type=prl_hook' ); ?>"><?php esc_html_e( 'Create Custom Location', 'woocommerce-product-recommendations-custom-locations' ); ?></a>
		</div><?php
		$message = ob_get_clean();
		return $message;
	}

	/**
	 * Specify custom action messages.
	 *
	 * @param  array $messages Existing post update messages.
	 * @return array
	 */
	public static function post_updated_messages( $messages ) {

		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		$messages[ 'prl_hook' ] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Location updated.', 'woocommerce-product-recommendations-custom-locations' ),
			4  => __( 'Location updated.', 'woocommerce-product-recommendations-custom-locations' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET[ 'revision' ] ) ? sprintf( __( 'Location restored to revision from %s', 'woocommerce-product-recommendations-custom-locations' ), wp_post_revision_title( (int) $_GET[ 'revision' ], false ) ) : false,
			6  => __( 'Location created.', 'woocommerce-product-recommendations-custom-locations' ),
			7  => __( 'Location saved.', 'woocommerce-product-recommendations-custom-locations' ),
			8  => __( 'Location submitted.', 'woocommerce-product-recommendations-custom-locations' ),
			10 => __( 'Location draft updated.', 'woocommerce-product-recommendations-custom-locations' )
		);

		return $messages;
	}

	/**
	 * Specify custom bulk actions messages for different post types.
	 *
	 * @param  array $bulk_messages Array of messages.
	 * @param  array $bulk_counts Array of how many objects were updated.
	 * @return array
	 */
	public static function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {

		$bulk_messages[ 'prl_hook' ] = array(
			/* translators: %s: location count */
			'deleted'   => _n( '%s location permanently deleted.', '%s locations permanently deleted.', $bulk_counts[ 'deleted' ], 'woocommerce-product-recommendations-custom-locations' ),
			/* translators: %s: location count */
			'trashed'   => _n( '%s location moved to Trash.', '%s locations moved to Trash.', $bulk_counts[ 'trashed' ], 'woocommerce-product-recommendations-custom-locations' ),
			/* translators: %s: location count */
			'untrashed' => _n( '%s location restored from Trash.', '%s locations restored from Trash.', $bulk_counts[ 'untrashed' ], 'woocommerce-product-recommendations-custom-locations' ),
		);

		return $bulk_messages;
	}
}

WC_PRL_CL_Post_Types::init();
