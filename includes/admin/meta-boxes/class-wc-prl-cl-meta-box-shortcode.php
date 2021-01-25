<?php
/**
 * WC_PRL_CL_Meta_Box_Shortcode class
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
 * Engine details meta-box.
 *
 * @class    WC_PRL_CL_Meta_Box_Shortcode
 * @version  1.0.0
 */
class WC_PRL_CL_Meta_Box_Shortcode extends WC_PRL_Meta_Box {

	 /**
	  * Constructor.
	  */
	public function __construct() {

		$this->id              = 'wc-prl-cl-shortcode';
		$this->context         = 'side';
		$this->priority        = 'default';
		$this->screens         = array( 'prl_hook' );
		$this->postbox_classes = array( 'wc-prl', 'woocommerce' );

		parent::__construct();
	}

	/**
	 * Returns the meta box title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Shortcode', 'woocommerce-product-recommendations-custom-locations' );
	}

	/**
	 * Displays the engine details meta box.
	 *
	 * @param WP_Post $post
	 */
	public function output( WP_Post $post ) {

		// Prepare.
		$this->post = $post;

		if ( ! $post ) {
			return;
		}

		?>
		<div id="wc-prl-cl-shortcode">
			<p><?php esc_html_e( 'To render the recommendations generated in this Custom Location, add the following shortcode to any post or page:', 'woocommerce-product-recommendations-custom-locations' ) ?></p>
			<code><?php echo sprintf( "[woocommerce_prl_recommendations id='%d']", $post->ID ); ?></code>
			<span class="meta"><a href="#" class="copy"><?php esc_html_e( 'Copy shortcode', 'woocommerce-product-recommendations-custom-locations' ) ?></a></span>
		</div>
		<?php
	}
}
