<?php
/**
* Plugin Name: Product Recommendations - Custom Locations
* Plugin URI: https://woocommerce.com/products/product-recommendations/
* Description: Use shortcodes and blocks to display product recommendations in custom pages and locations. Free feature plugin for the official WooCommerce Product Recommendations extension.
* Version: 2.0.0
* Author: SomewhereWarm
* Author URI: https://somewherewarm.com/
*
* Text Domain: woocommerce-product-recommendations-custom-locations
* Domain Path: /languages/
*
* Requires at least: 6.2
* Tested up to: 6.6
*
* WC requires at least: 8.2
* WC tested up to: 9.1
*
* Copyright: © 2017-2024 SomewhereWarm SMPC.
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
 * @version  2.0.0
 */
class WC_Product_Recommendations_Custom_Locations {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version = '2.0.0';

	/**
	 * Min required PRL version.
	 *
	 * @var string
	 */
	private $prl_min_version = '4.0';

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

		// WC version sanity check.
		if ( ! function_exists( 'WC' ) || ! function_exists( 'WC_PRL' ) || version_compare( WC_PRL()->get_plugin_version(), $this->prl_min_version ) < 0 ) {
			add_action( 'admin_notices', array( $this, 'dependencies_notice' ) );
			return false;
		}

		// Declare HPOS compatibility.
		add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );

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
	 * Declare HPOS( Custom Order tables) compatibility.
	 *
	 */
	public function declare_hpos_compatibility() {

		if ( ! class_exists( 'Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			return;
		}

		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', plugin_basename( __FILE__ ), true );
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

	/**
	 * PRL dependency check notice.
	 */
	public function dependencies_notice() {
		if ( ! function_exists( 'WC' ) ) {
			$notice = sprintf( __( 'Product Recommendations - Custom Locations requires at least WooCommerce <strong>%s</strong>.', 'woocommerce-product-recommendations-custom-locations' ), '3.3.0' );
		} else {
			$notice = sprintf( __( '<strong>Product Recommendations - Custom Locations</strong> requires at least <a href="%1$s" target="_blank">WooCommerce Product Recommendations</a> version <strong>%2$s</strong>.', 'woocommerce-product-recommendations-custom-locations' ), 'https://woocommerce.com/products/product-recommendations', $this->prl_min_version );
		}
		echo '<div class="error"><p>' . $notice . '</p></div>';
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
