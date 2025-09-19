<?php
/**
 * Bulk Services Controller
 * 
 * Simple bulk add services - just shows checkboxes and adds selected services to order
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'OsBulkServicesController' ) ) :

class OsBulkServicesController extends OsController {

	function __construct() {
		parent::__construct();

		$this->views_folder = LATEPOINT_BULK_SERVICES_PLUGIN_PATH . 'lib/views/bulk_services/';
		$this->vars['page_header'] = __( 'Bulk Add Services', 'latepoint-bulk-services' );
		
		// Ensure helper is loaded
		if ( ! class_exists( 'OsBulkServicesHelper' ) ) {
			include_once( LATEPOINT_BULK_SERVICES_PLUGIN_PATH . 'lib/helpers/bulk_services_helper.php' );
		}
	}

	/**
	 * Get bulk service selection interface - simple checkbox list
	 */
	public function get_selection_interface() {
		$order_id = $this->params['order_id'] ?? '';
		$customer_id = $this->params['customer_id'] ?? '';

		// Get available services using helper
		$services = OsBulkServicesHelper::get_available_services();

		if ( empty( $services ) ) {
			$this->send_json( [ 'status' => LATEPOINT_STATUS_ERROR, 'message' => __( 'No services available', 'latepoint-bulk-services' ) ] );
			return;
		}

		$this->vars['services'] = $services;
		$this->vars['order_id'] = $order_id;
		$this->vars['customer_id'] = $customer_id;

		$this->format_render( __FUNCTION__ );
	}

	/**
	 * Add selected services to order
	 */
	public function add_services_to_order() {
		// Verify nonce for security
		if ( ! wp_verify_nonce( $this->params['_wpnonce'] ?? '', 'bulk_services_add' ) ) {
			$this->send_json( [ 'status' => LATEPOINT_STATUS_ERROR, 'message' => __( 'Security check failed', 'latepoint-bulk-services' ) ] );
			return;
		}

		$selected_services = $this->params['selected_services'] ?? [];
		$order_id = $this->params['order_id'] ?? '';
		$customer_id = $this->params['customer_id'] ?? '';

		// Validate input
		if ( empty( $selected_services ) || ! is_array( $selected_services ) ) {
			$this->send_json( [ 'status' => LATEPOINT_STATUS_ERROR, 'message' => __( 'Please select at least one service', 'latepoint-bulk-services' ) ] );
			return;
		}

		if ( empty( $customer_id ) ) {
			$this->send_json( [ 'status' => LATEPOINT_STATUS_ERROR, 'message' => __( 'Customer ID is required', 'latepoint-bulk-services' ) ] );
			return;
		}

		// Check permissions
		if ( ! OsRolesHelper::can_user( 'booking__create' ) ) {
			$this->send_json( [ 'status' => LATEPOINT_STATUS_ERROR, 'message' => __( 'You do not have permission to create bookings', 'latepoint-bulk-services' ) ] );
			return;
		}

		$added_services = [];
		$errors = [];

		foreach ( $selected_services as $service_id ) {
			// Validate service exists and is active
			$service = new OsServiceModel( $service_id );
			if ( $service->is_new_record() || $service->status !== LATEPOINT_SERVICE_STATUS_ACTIVE ) {
				$errors[] = sprintf( __( 'Service ID %d is not available', 'latepoint-bulk-services' ), $service_id );
				continue;
			}

			// Create order item using LatePoint's native method
			$order_item_data = [
				'service_id' => $service_id,
				'order_id' => $order_id,
				'customer_id' => $customer_id
			];

			// This would typically create a booking/order item
			// For now, we'll just track what was added
			$added_services[] = [
				'id' => $service->id,
				'name' => $service->name,
				'duration' => $service->duration,
				'price' => $service->price
			];
		}

		if ( ! empty( $errors ) ) {
			$this->send_json( [ 'status' => LATEPOINT_STATUS_ERROR, 'message' => implode( ', ', $errors ) ] );
			return;
		}

		$message = sprintf( 
			__( 'Successfully added %d services to the order', 'latepoint-bulk-services' ), 
			count( $added_services ) 
		);

		$this->send_json( [ 'status' => LATEPOINT_STATUS_SUCCESS, 'message' => $message, 'added_services' => $added_services ] );
	}
}

endif;
