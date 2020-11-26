<?php
/**
 * WC_PRL_CL_Admin_List_Table_Locations class
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
 * Modifies the custom's post type list table.
 *
 * @class    WC_PRL_CL_Admin_List_Table_Locations
 * @version  1.0.0
 */
class WC_PRL_CL_Admin_List_Table_Locations {

	/**
	 * Post type.
	 *
	 * @var string
	 */
	private static $list_table_type = 'prl_hook';

	/**
	 * Object being shown on the row.
	 *
	 * @var object|null
	 */
	private static $location = null;

	/**
	 * Hook in.
	 */
	public static function init() {

		// Remove quick/bulk edit actions.
		add_filter( 'post_row_actions', array( __CLASS__, 'disable_quick_edit' ), 10, 2 );
		add_filter( 'bulk_actions-edit-' . self::$list_table_type, array( __CLASS__, 'disable_bulk_actions' ) );

		// Custom columns.
		add_filter( 'manage_edit-' . self::$list_table_type . '_sortable_columns', array( __CLASS__, 'sortable_columns' ) );
		add_filter( 'manage_edit-' . self::$list_table_type . '_columns', array( __CLASS__, 'custom_columns' ) ) ;
		add_action( 'manage_' . self::$list_table_type . '_posts_custom_column', array( __CLASS__, 'render_columns' ), 10, 2 );

	}

	/**
	 * Remove Quick Edit action.
	 *
	 * @param  array   $actions
	 * @param  WP_Post $post
	 * @return array
	 */
	public static function disable_quick_edit( $actions, $post ) {

		if ( ! $post ) {
			return $actions;
		}

		if ( self::$list_table_type !== $post->post_type ) {
			return $actions;
		}

		// Remove the Quick Edit link
		if ( isset( $actions[ 'inline hide-if-no-js' ] ) ) {
			unset( $actions[ 'inline hide-if-no-js' ] );
		}

		return $actions;
	}

	/**
	 * Remove Bulk actions.
	 *
	 * @param  array $actions
	 * @return array
	 */
	public static function disable_bulk_actions( $actions ) {
		global $post;

		if ( self::$list_table_type !== $post->post_type ) {
			return $actions;
		}

		// Remove the Quick Edit link
		if ( isset( $actions[ 'edit' ] ) ) {
			unset( $actions[ 'edit' ] );
		}

		return $actions;
	}

	/**
	 * Setup custom table columns.
	 *
	 * @param  array $columns
	 * @return array
	 */
	public static function custom_columns( $columns ) {

		if ( empty( $columns ) && ! is_array( $columns ) ) {
			$columns = array();
		}

		$show_columns                  = array();
		$show_columns[ 'cb' ]          = '<input type="checkbox" />';
		$show_columns[ 'title' ]       = __( 'Name', 'woocommerce-product-recommendations-custom-locations' );
		$show_columns[ 'deployments' ] = __( 'Deployments', 'woocommerce-product-recommendations-custom-locations' );
		$show_columns[ 'date' ]        = __( 'Date', 'woocommerce-product-recommendations-custom-locations' );
		$show_columns[ 'wc_actions' ]  = __( 'Actions', 'woocommerce-product-recommendations-custom-locations' );

		// Note: WP will auto-fill `title` & `date` columns. They are included in the list for ordering purposes.
		return $show_columns;
	}

	/**
	 * Define sortable table columns.
	 *
	 * @param  array $columns
	 * @return array
	 */
	public static function sortable_columns( $columns ) {

		$custom = array(
			// ...
		);

		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Pre-fetch any data for the row each column has access to it.
	 *
	 * @param int $post_id
	 */
	private static function prepare_row_data( $post_id ) {
		self::$location = absint( $post_id );
	}

	/**
	 * Display custom column content.
	 *
	 * @param  string $column
	 * @param  int    $post_id
	 * @return void
	 */
	public static function render_columns( $column, $post_id ) {
		self::prepare_row_data( $post_id );

		if ( ! self::$location ) {
			return;
		}

		if ( is_callable( array( __CLASS__, 'render_' . $column . '_column' ) ) ) {
			self::{"render_{$column}_column"}();
		}
	}

	/**
	 * Render column: deployments.
	 */
	public static function render_deployments_column() {
		$current_deployments = WC_PRL()->db->deployment->query( array( 'return' => 'ids', 'location_id' => 'custom', 'hook' => self::$location ) );
		echo count( $current_deployments );
	}

	/**
	 * Render column: wc_actions.
	 */
	public static function render_wc_actions_column() {
		$disabled = ! self::$location ? ' button-disabled' : '';
		?>
		<p>
			<a id="<?php echo self::$location ?>" class="button<?php echo $disabled ?> wc-action-button wc-action-button-regenerate" href="#" aria-label="<?php esc_attr_e( 'Regenerate recommendations', 'woocommerce-product-recommendations' ) ?>" title="<?php esc_attr_e( 'Regenerate recommendations', 'woocommerce-product-recommendations' ) ?>"><?php esc_html_e( 'Regenerate recommendations', 'woocommerce-product-recommendations' ) ?></a>
			<a id="<?php echo self::$location ?>" class="button wc-action-button wc-action-button-shortcode" href="#" aria-label="<?php esc_attr_e( 'Copy shortcode', 'woocommerce-product-recommendations-custom-locations' ) ?>" title="<?php esc_attr_e( 'Copy shortcode', 'woocommerce-product-recommendations-custom-locations' ) ?>" data-shortcode="<?php echo esc_attr( sprintf( "[woocommerce_prl_recommendations id='%d']", self::$location ) ); ?>"><?php esc_html_e( 'Copy shortcode', 'woocommerce-product-recommendations-custom-locations' ) ?></a>
		</p>
		<?php
	}
}

WC_PRL_CL_Admin_List_Table_Locations::init();
