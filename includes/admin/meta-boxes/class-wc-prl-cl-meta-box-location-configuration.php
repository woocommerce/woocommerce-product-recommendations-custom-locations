<?php
/**
 * WC_PRL_CL_Meta_Box_Location_Configuration class
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
 * @class    WC_PRL_CL_Meta_Box_Location_Configuration
 * @version  1.0.0
 */
class WC_PRL_CL_Meta_Box_Location_Configuration extends WC_PRL_Meta_Box {

	 /**
	  * Constructor.
	  */
	public function __construct() {

		$this->id              = 'wc-prl-cl-location-configuration';
		$this->context         = 'normal';
		$this->priority        = 'high';
		$this->screens         = array( 'prl_hook' ); // Only in `prl_hook` post_type.
		$this->postbox_classes = array( 'wc-prl', 'wc-prl-plain-metabox', 'woocommerce' );

		parent::__construct();
	}

	/**
	 * Returns the meta box title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Location Configuration', 'woocommerce-product-recommendations-custom-locations' );
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

		$args = array(
			'return'      => 'objects',
			'location_id' => 'custom',
			'hook'        => $post->ID,
			'order_by'    => array( 'display_order' => 'ASC' )
		);

		$deployments = WC_PRL()->db->deployment->query( $args );
		$hook        = $post->ID;
		?>
		<div id="wc-prl-custom-location-data">
			<div class="wc-prl-field_content">

				<!-- hack it to work... -->
				<div class="wc-prl-hooks" style="display:none;"><div class="wc-prl-hooks__tab--active"><div class="current_count"><?php echo count( $deployments ); ?></div></div></div>

				<div class="wc-prl-deployments wc-metaboxes-wrapper">

					<div class="toolbar">
						<span class="bulk_toggle_wrapper<?php echo empty( $deployments ) ? ' disabled' : '' ; ?>">
							<a href="#" class="expand_all"><?php esc_html_e( 'Expand all', 'woocommerce' ); ?></a>
							<a href="#" class="close_all"><?php esc_html_e( 'Close all', 'woocommerce' ); ?></a>
						</span>
					</div>

					<div class="wc-prl-deployments__list wc-metaboxes ui-sortable" data-filter_type="cart,product,archive,order">

						<?php
						if ( ! empty( $deployments ) ) {

							foreach ( $deployments as $index => $deployment ) {

								$options                      = array();
								$options[ 'id' ]              = $deployment->get_id();
								$options[ 'engine_id' ]       = $deployment->get_engine_id();
								$options[ 'engine_type' ]     = $deployment->get_engine_type();
								$options[ 'filter_type' ]     = array( 'cart' );
								$options[ 'active' ]          = $deployment->is_active() ? 'on' : 'off';
								$options[ 'display_order' ]   = $deployment->get_display_order();
								$options[ 'title' ]           = $deployment->get_title();
								$options[ 'description' ]     = $deployment->get_description();
								$options[ 'rows' ]            = absint( $deployment->get_limit() / $deployment->get_columns() );
								$options[ 'columns' ]         = $deployment->get_columns();
								$options[ 'conditions' ]      = $deployment->get_conditions_data();

								// Render.
								WC_PRL()->deployments->get_admin_metaboxes_content( $index, $options, false );
							}
						}
						?>

						<div class="wc-prl-deployments__boarding <?php echo ! empty( $deployments ) ? 'wc-prl-deployments__boarding--hidden' : '' ; ?>">
							<div class="wc-prl-deployments__boarding__message">
								<h3><?php echo __( 'No Engines found', 'woocommerce-product-recommendations' ); ?></h3>
								<p><?php echo __( 'You have not added Engines to this location. Deploy an Engine now?', 'woocommerce-product-recommendations' ); ?></p>
							</div>
						</div>

					</div>

					<div class="wc-prl-deployments__list__buttons <?php echo empty( $deployments ) ? 'wc-prl-deployments__list__buttons--empty' : '' ; ?>">
						<button class="wc-prl-deployments__add button"><?php esc_html_e( 'Deploy an Engine', 'woocommerce-product-recommendations' ); ?></button>
						<button type="submit" class="wc-prl-deployments__save button button-primary"><?php esc_html_e( 'Save changes', 'woocommerce-product-recommendations' ); ?></button>
					</div>
				</div>

			</div>
		</div>
		<?php
	}

	/**
	 * Handles the request data.
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 */
	public function update( $post_id, $post ) {

		$deployments = (array) $_POST[ 'deployment' ];
		if ( empty( $deployments ) || empty( $post_id ) ) {
			return false;
		}

		$location = WC_PRL()->locations->get_location( 'shortcode' );
		if ( ! $location ) {
			return false;
		}

		foreach ( $deployments as $data ) {

			$deployment = false;
			if ( isset( $data[ 'id' ] ) && $data[ 'id' ] != 0 ) {
				$deployment = new WC_PRL_Deployment( absint( $data[ 'id' ] ) );
			}

			$args = array(
				'engine_id'       => isset( $data[ 'engine_id' ] ) ? absint( $data[ 'engine_id' ] ) : 0,
				'title'           => isset( $data[ 'title' ] ) ? strip_tags( wp_unslash( $data[ 'title' ] ) ) : '',
				'description'     => wp_kses_post( wp_unslash( $data[ 'description' ] ) ),
				'location_id'     => $location->get_location_id(),
				'hook'            => $post_id,
				'conditions_data' => isset( $data[ 'conditions' ] ) && is_array( $data[ 'conditions' ] ) ? array_values( $data[ 'conditions' ] ) : array(),
				'display_order'   => isset( $data[ 'display_order' ] ) ? absint( $data[ 'display_order' ] ) : 1
			);

			if ( isset( $data[ 'active' ] ) ) {
				$args[ 'active' ] = $data[ 'active' ];
			}

			if ( isset( $data[ 'columns' ] ) && $data[ 'columns' ] > 0 ) { // If is not set or 0 let the default.
				$args[ 'columns' ] = absint( $data[ 'columns' ] );
			}

			if ( isset( $data[ 'rows' ] ) && $data[ 'rows' ] > 0 ) { // If is not set or 0 let the default.
				$args[ 'rows' ] = absint( $data[ 'rows' ] );
			}

			// Add to database.
			try {

				if ( $deployment ) {
					WC_PRL()->db->deployment->update( $deployment->data, $args );
				} else {
					WC_PRL()->db->deployment->add( $args );
				}

			} catch ( Exception $e ) {
				WC_PRL_Admin_Notices::add_notice( $e->getMessage(), 'error', true );
			}
		}

	}
}
