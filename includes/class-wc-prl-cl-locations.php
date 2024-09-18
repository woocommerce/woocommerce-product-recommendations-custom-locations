<?php
/**
 * WC_PRL_CL_Locations class
 *
 * @author   WooCommerce
 * @package  WooCommerce Product Recommendations - Custom Locations
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Locations Collection class.
 *
 * @class    WC_PRL_CL_Locations
 * @version  1.0.0
 */
class WC_PRL_CL_Locations {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_prl_get_locations', array( $this, 'get_locations' ), 0 );
		add_filter( 'woocommerce_prl_get_hooks_for_deployment', array( $this, 'get_hooks_for_deployment' ), 0 );
		add_filter( 'woocommerce_prl_get_location_by_hook', array( $this, 'get_location_by_hook' ), 0, 2 );
	}

	/**
	 * Filters all locations in `view` context.
	 *
	 * @param array $locations
	 */
	public function get_locations( $locations ) {

		// Remove CPT custom location.
		if ( isset( $locations[ 'custom' ] ) ) {
			unset( $locations[ 'custom' ] );
		}

		return $locations;
	}

	/**
	 * Filters all hooks for a given deployment.
	 *
	 * @param array $locations
	 */
	public function get_hooks_for_deployment( $hooks ) {

		// Remove CPT custom location.
		if ( isset( $hooks[ 'custom' ] ) ) {
			unset( $hooks[ 'custom' ] );
		}

		return $hooks;
	}

	/**
	 * Filters all hooks for a given deployment.
	 *
	 * @param WC_PRL_Location $location
	 * @param string          $hook
	 */
	public function get_location_by_hook( $location, $hook ) {

		// Return for CPT IDs.
		if ( wc_prl_is_cpt_hook( $hook ) ) {
			$location = WC_PRL()->locations->get_location( 'custom' );
			if ( ! $location ) {
				return false;
			}

			$location->set_current_hook( $hook );
		}

		return $location;
	}
}

new WC_PRL_CL_Locations();
