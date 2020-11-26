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

/**
 * Setup PRL menus in WP admin.
 *
 * @version 1.0.0
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

		// Integrate WooCommerce navigation bar.
		add_action( 'admin_menu', array( __CLASS__, 'wc_admin_connect_gc_pages' ) );
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
	 *
	 * @since 1.0.0
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
}

WC_PRL_CL_Admin_Menus::init();
