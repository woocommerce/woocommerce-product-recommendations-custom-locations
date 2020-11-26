<?php
/**
* Plugin Name: WooCommerce Product Recommendations - Custom Locations
* Plugin URI: https://woocommerce.com/products/product-recommendations/
* Description: Create smarter up-sells and cross-sells, place them anywhere, and measure their impact with in-depth analytics.
* Version: 1.0.0
* Author: SomewhereWarm
* Author URI: https://somewherewarm.com/
*
* Woo: 4486128:9732a1cdebd38f7eb1f58bb712f7fb0e
*
* Text Domain: woocommerce-product-recommendations-custom-locations
* Domain Path: /languages/
*
* Requires at least: 4.4
* Tested up to: 5.5
*
* WC requires at least: 3.3
* WC tested up to: 4.6
*
* Copyright: © 2017-2020 SomewhereWarm SMPC.
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 *
 * @class    WC_Product_Recommendations_Custom_Locations
 * @version  1.0.0
 */
class WC_Product_Recommendations_Custom_Locations {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version = '1.0.0';

	/**
	 * Min required PRL version.
	 *
	 * @var string
	 */
	private $prl_min_version = '1.4.4';

	/**
	 * The single instance of the class.
	 *
	 * @var WC_Product_Recommendations_Custom_Locations
	 */
	protected static $_instance = null;

	/**
	 * Main WC_Product_Recommendations_Custom_Locations instance. Ensures only one instance is loaded or can be loaded - @see 'WC_PRL_CL()'.
	 *
	 * @static
	 * @return  WC_Product_Recommendations_Custom_Locations
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-product-recommendations-custom-locations' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-product-recommendations-custom-locations' ), '1.0.0' );
	}

	/**
	 * Make stuff.
	 */
	protected function __construct() {
		// Entry point.
		add_action( 'plugins_loaded', array( $this, 'initialize_plugin' ), 9 );
	}

	/**
	 * Plugin URL getter.
	 *
	 * @return string
	 */
	public function get_plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Plugin path getter.
	 *
	 * @return string
	 */
	public function get_plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Plugin base path name getter.
	 *
	 * @return string
	 */
	public function get_plugin_basename() {
		return plugin_basename( __FILE__ );
	}

	/**
	 * Plugin version getter.
	 *
	 * @param  boolean  $base
	 * @param  string   $version
	 * @return string
	 */
	public function get_plugin_version( $base = false, $version = '' ) {

		$version = $version ? $version : $this->version;

		if ( $base ) {
			$version_parts = explode( '-', $version );
			$version       = sizeof( $version_parts ) > 1 ? $version_parts[ 0 ] : $version;
		}

		return $version;
	}

	/**
	 * Fire in the hole!
	 */
	public function initialize_plugin() {

		$this->define_constants();

		if ( ! function_exists( 'WC_PRL' ) ) {
			return;
		}

		// WC version sanity check.
		if ( version_compare( WC_PRL()->get_plugin_version(), $this->prl_min_version ) < 0 ) {
			$notice = sprintf( __( '"Custom Locations" mini-extension requires at least WooCommerce Product Recommendations <strong>%s</strong>.', 'woocommerce-product-recommendations-custom-locations' ), $this->prl_min_version );
			require_once( WC_PRL_ABSPATH . 'includes/admin/class-wc-prl-admin-notices.php' );
			WC_PRL_Admin_Notices::add_notice( $notice, 'error' ); // TODO: require without admin notices.
			return false;
		}

		$this->includes();

		// Add main location object (Shortcode).
		add_filter( 'woocommerce_prl_locations', array( $this, 'add_location_object' ) );

		// Add screen ids.
		add_filter( 'woocommerce_prl_screen_ids', array( $this, 'add_screen_ids' ) );
	}

	/**
	 * Constants.
	 */
	public function define_constants() {
			$this->maybe_define_constant( 'WC_PRL_CL_VERSION', $this->version );
			$this->maybe_define_constant( 'WC_PRL_CL_ABSPATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
	}

	/**
	 * Define constants if not present.
	 *
	 * @return boolean
	 */
	protected function maybe_define_constant( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Includes.
	 */
	public function includes() {

		// Install.
		// require_once( WC_PRL_CL_ABSPATH . 'includes/class-wc-prl-cl-install.php' );

		// PRL locations management.
		require_once( WC_PRL_CL_ABSPATH . 'includes/class-wc-prl-cl-locations.php' );

		// Post types.
		require_once( WC_PRL_CL_ABSPATH . 'includes/class-wc-prl-cl-post-types.php' );

		// Admin includes.
		if ( is_admin() ) {
			$this->admin_includes();
		}
	}

	/**
	 * Admin & AJAX functions and hooks.
	 */
	public function admin_includes() {

		// Admin functions and hooks.
		require_once( WC_PRL_CL_ABSPATH . 'includes/admin/class-wc-prl-cl-admin.php' );
	}

	/**
	 * Init a new shortcode global Location.
	 */
	public function add_location_object( $locations ) {

		// Admin functions and hooks.
		require_once( WC_PRL_CL_ABSPATH . 'includes/class-wc-prl-cl-location-shortcode.php' );
		$locations[] = 'WC_PRL_CL_Location_Shortcode';
		return $locations;
	}


	/**
	 * Add PRL CL screen ids.
	 */
	public function add_screen_ids( $screens ) {

		$screens[] = 'prl_hook';
		$screens[] = 'edit-prl_hook';

		return $screens;
	}
}

/**
 * Returns the main instance of WC_Product_Recommendations_Custom_Locations to prevent the need to use globals.
 *
 * @return  WC_Product_Recommendations_Custom_Locations
 */
function WC_PRL_CL() {
	return WC_Product_Recommendations_Custom_Locations::instance();
}

WC_PRL_CL();
