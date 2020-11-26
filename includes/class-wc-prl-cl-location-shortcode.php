<?php
/**
 * WC_PRL_CL_Location_Shortcode class
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
 * Locations that are used only in cart context.
 *
 * @class    WC_PRL_CL_Location_Shortcode
 * @version  1.0.0
 */
class WC_PRL_CL_Location_Shortcode extends WC_PRL_Location {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id        = 'custom';
		$this->title     = __( 'Custom', 'woocommerce-product-recommendations-custom-locations' );
		$this->cacheable = false;

		$this->defaults = array(
			'engine_type' => array( 'cart', 'product', 'archive', 'order' ),
			'priority'    => 10,
			'args_number' => 0
		);

		parent::__construct();
	}

	/**
	 * Check if the current location page is active.
	 *
	 * @return boolean
	 */
	public function is_active() {
		return true;
	}

	/**
	 * Setup all supported hooks based on the location id.
	 *
	 * @return void
	 */
	protected function setup_hooks() {
		// Hooks are comming from CPTs.
		$this->hooks = array(
			'custom' => array(
				'id'    => 'custom',
				'label' => __( 'Custom', 'woocommerce-product-recommendations-custom-locations' )
			)
		);
	}
}
