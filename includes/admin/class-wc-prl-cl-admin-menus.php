<?php
/**
 * WC_PRL_CL_Admin_Menus class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Product Recommendations - Custom Locations
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\Features\Navigation\Menu;
use Automattic\WooCommerce\Admin\Features\Navigation\Screen;

/**
 * Setup PRL menus in WP admin.
 *
 * @version 1.0.1
 */
class WC_PRL_CL_Admin_Menus {

	/**
	 * Setup.
	 */
	public static function init() {
		self::add_hooks();
	}

	/**
	 * Admin hooks.
	 */
	public static function add_hooks() {

		// Tabs.
		add_action( 'woocommerce_prl_admin_tabs', array( __CLASS__, 'add_admin_tabs' ) );
		add_filter( 'woocommerce_prl_admin_current_tab', array( __CLASS__, 'get_current_tab' ) );

		// Integrate WooCommerce breadcrumb bar.
		add_action( 'admin_menu', array( __CLASS__, 'wc_admin_connect_gc_pages' ) );

		// Integrate WooCommerce side navigation.
		add_action( 'admin_menu', array( __CLASS__, 'register_navigation_pages' ) );
	}

	/**
	 * Connect pages with navigation bar.
	 *
	 * @return void
	 */
	public static function wc_admin_connect_gc_pages() {

		if ( WC_PRL_Core_Compatibility::is_wc_admin_enabled() ) {

			$posttype_list_base = 'edit.php';

			// WooCommerce > Engines.
			wc_admin_connect_page(
				array(
					'id'        => 'woocommerce-custom-locations',
					'parent'    => 'woocommerce-product-recommendations',
					'screen_id' => wc_prl_get_formatted_screen_id( 'edit-prl_hook' ),
					'title'     => __( 'Custom Locations', 'woocommerce-product-recommendations-custom-locations' ),
					'path'      => add_query_arg( 'post_type', 'prl_hook', $posttype_list_base ),
				)
			);

			// WooCommerce > Engines > Add New.
			wc_admin_connect_page(
				array(
					'id'        => 'woocommerce-custom-locations-add',
					'parent'    => 'woocommerce-custom-locations',
					'screen_id' => wc_prl_get_formatted_screen_id( 'prl_hook' ) . '-add',
					'title'     => __( 'Add New', 'woocommerce-product-recommendations-custom-locations' ),
				)
			);

			// WooCommerce > Engines > Edit Engine.
			wc_admin_connect_page(
				array(
					'id'        => 'woocommerce-custom-locations-edit',
					'parent'    => 'woocommerce-custom-locations',
					'screen_id' => wc_prl_get_formatted_screen_id( 'prl_hook' ),
					'title'     => __( 'Edit Location', 'woocommerce-product-recommendations-custom-locations' ),
				)
			);
		}
	}

	/**
	 * Renders tabs on our custom post types pages.
	 *
	 * @internal
	 */
	public static function add_admin_tabs( $tabs ) {
		$tabs[ 'custom_locations' ] = array(
			'title' => __( 'Custom Locations', 'woocommerce-product-recommendations-custom-locations' ),
			'url'   => admin_url( 'edit.php?post_type=prl_hook' ),
		);

		return $tabs;
	}

	/**
	 * Returns the current admin tab.
	 *
	 * @param  string  $current_tab
	 * @return string
	 */
	public static function get_current_tab( $current_tab ) {

		if ( $screen = get_current_screen() ) {
			if ( in_array( $screen->id, array( 'prl_hook', 'edit-prl_hook' ), true ) ) {
				$current_tab = 'custom_locations';
			}
		}

		return $current_tab;
	}

	/**
	 * Register WooCommerce menu pages.
	 *
	 * @since  1.0.1
	 *
	 * @return void
	 */
	public static function register_navigation_pages() {

		if ( ! class_exists( '\Automattic\WooCommerce\Admin\Features\Navigation\Menu' ) || ! class_exists( '\Automattic\WooCommerce\Admin\Features\Navigation\Screen' ) ) {
			return;
		}

		$match_expression = isset( $_GET[ 'post' ] ) && get_post_type( intval( $_GET[ 'post' ] ) ) === 'prl_hook'
			? '(edit.php|post.php)'
			: null;
		if ( is_null( $match_expression ) ) {
			$match_expression = isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] === 'prl_hook'
			? '(post-new.php)'
			: null;
		}

		Menu::add_plugin_item(
			array(
				'id'              => 'prl-custom-locations',
				'title'           => __( 'Custom Locations', 'woocommerce-product-recommendations' ),
				'capability'      => 'manage_woocommerce',
				'url'             => 'edit.php?post_type=prl_hook',
				'parent'          => 'prl-recommendations-category',
				'matchExpression' => $match_expression,
				'order'           => 40
			)
		);

		Screen::register_post_type( 'prl_hook' );
	}
}

WC_PRL_CL_Admin_Menus::init();
