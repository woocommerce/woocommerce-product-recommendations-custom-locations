<?php
/**
 * WC_PRL_CL_Admin class
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
 * Admin Class.
 *
 * Loads admin scripts, includes admin classes and adds admin hooks.
 *
 * @class    WC_PRL_CL_Admin
 * @version  1.0.0
 */
class WC_PRL_CL_Admin {

	/**
	 * Bundled selectSW library version.
	 *
	 * @var string
	 */
	private static $bundled_selectsw_version = '1.1.3';

	/**
	 * Setup Admin class.
	 */
	public static function init() {

		// Admin initializations.
		add_action( 'init', array( __CLASS__, 'admin_init' ), 20 );

		// Add meta box.
		add_action( 'current_screen', array( __CLASS__, 'init_meta_boxes' ) );

		// Enqueue scripts.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_resources' ), 11 );
	}

	/**
	 * Admin init.
	 */
	public static function admin_init() {
		self::includes();

		/*---------------------------------------------------*/
		/*  Print condition JS templates in footer.          */
		/*---------------------------------------------------*/

		add_action( 'admin_footer', array( __CLASS__, 'print_conditions_field_scripts' ) );

		// Filter Deployments list table location column.
		add_filter( 'manage_prl_deployments_location_column', array( __CLASS__, 'add_cpt_location_column' ), 10, 3 );

		// Remove all deployments when deleting a custom location.
		add_action( 'before_delete_post', array( __CLASS__, 'before_delete_location' ), 0 );
	}

	/**
	 * Print footer templates in custom location CPT.
	 */
	public static function add_cpt_location_column( $output, $location, $item ) {
		$hook = absint( $item[ 'hook' ] );
		if ( 'custom' === $location->get_location_id() && wc_prl_is_cpt_hook( $hook ) ) {
			$post_id = absint( $hook );
			$post    = get_post( $post_id );
			if ( ! $post || ! is_a( $post, 'WP_Post' ) ) {
				$output = esc_html( __( 'N/A', 'woocommerce-product-recommendations' ) );
			} else {
				$link = admin_url( sprintf( 'post.php?post=%d&action=edit', $post_id ) );
				$output = sprintf( '<a href="%1$s">%2$s</a>', $link, apply_filters( 'the_title', $post->post_title ) );
			}
		}

		return $output;
	}

	/**
	 * Delete all deployments when deleting a custom location.
	 */
	public static function before_delete_location( $post_id ) {

		// Fetch the post type.
		$post_type = get_post_type( $post_id );
		if ( $post_type !== 'prl_hook' ) {
			return;
		}

		// Fetch and delete.
		$args = array(
			'return' => 'objects',
			'hook'   => absint( $post_id ),
		);

		$deployments = WC_PRL()->db->deployment->query( $args );
		if ( $deployments ) {
			foreach( $deployments as $deployment ) {
				$deployment->delete();
			}
		}
	}

	/**
	 * Print footer templates in custom location CPT.
	 */
	public static function print_conditions_field_scripts() {
		global $post;
		if ( ! $post || ! is_a( $post, 'WP_Post' ) ) {
			return;
		}
		// if ( 'prl_hook' !== $post->post_type ) {
		// 	return;
		// }

		WC_PRL()->conditions->print_js_templates();
	}

	/**
	 * Inclusions.
	 */
	protected static function includes() {

		// Admin Menus.
		require_once( WC_PRL_CL_ABSPATH . 'includes/admin/class-wc-prl-cl-admin-menus.php' );

		// Admin list table settings.
		require_once( WC_PRL_CL_ABSPATH . 'includes/admin/list-tables/class-wc-prl-cl-admin-list-locations.php' );
		require_once( WC_PRL_CL_ABSPATH . 'includes/admin/meta-boxes/class-wc-prl-cl-meta-box-location-configuration.php' );
		require_once( WC_PRL_CL_ABSPATH . 'includes/admin/meta-boxes/class-wc-prl-cl-meta-box-shortcode.php' );
	}

	/**
	 * Add meta-boxes.
	 */
	public static function init_meta_boxes( $metaboxes ) {
		new WC_PRL_CL_Meta_Box_Location_Configuration();
		new WC_PRL_CL_Meta_Box_Shortcode();
	}

	/**
	 * Admin scripts.
	 */
	public static function admin_resources() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'wc-prl-cl-admin-css', WC_PRL_CL()->get_plugin_url() . '/assets/css/admin/wc-prl-cl-admin.css', array(), WC_PRL_CL()->get_plugin_version() );
		wp_style_add_data( 'wc-prl-cl-admin-css', 'rtl', 'replace' );

		wp_register_script( 'wc-prl-cl-writepanel', WC_PRL_CL()->get_plugin_url() . '/assets/js/admin/wc-prl-cl-admin' . $suffix . '.js', array( 'jquery', 'wp-util', 'sw-admin-select-init' ), WC_PRL_CL()->get_plugin_version() );

		/*
		 * Enqueue specific styles & scripts.
		 */
		if ( WC_PRL()->is_current_screen() ) {

			wp_enqueue_script( 'wc-prl-cl-writepanel' );
			wp_enqueue_style( 'wc-prl-cl-admin-css' );

			$params = array(
				'i18n_copied' => __( 'Shortcode copied.', 'woocommerce-product-recommendations-custom-locations' )
			);

			wp_localize_script( 'wc-prl-cl-writepanel', 'wc_prl_cl_admin_params', $params );
		}
	}

}

WC_PRL_CL_Admin::init();
