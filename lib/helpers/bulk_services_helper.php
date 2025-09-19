<?php
/**
 * Bulk Services Helper
 * 
 * Helper functions for bulk services functionality
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'OsBulkServicesHelper' ) ) :

class OsBulkServicesHelper {

	/**
	 * Get available services for bulk add
	 */
	public static function get_available_services() {
		$services_model = new OsServiceModel();
		$services = $services_model->where( [ 'status' => LATEPOINT_SERVICE_STATUS_ACTIVE ] )->get_results_as_models();

		$formatted_services = [];
		foreach ( $services as $service ) {
			$price = $service->price ? $service->price : '0';
			$price_formatted = OsMoneyHelper::format_price( $price );
			
			// Get category name if service has a category
			$category_name = '';
			if ( $service->category_id ) {
				$category = new OsServiceCategoryModel( $service->category_id );
				if ( ! $category->is_new_record() ) {
					$category_name = $category->name;
				}
			}
			
			$formatted_services[] = [
				'id'                => $service->id,
				'name'              => $service->name,
				'short_description' => $service->short_description,
				'duration'          => $service->duration,
				'price'             => $price,
				'price_formatted'   => $price_formatted,
				'category_id'       => $service->category_id,
				'category_name'     => $category_name
			];
		}

		return $formatted_services;
	}

	/**
	 * Get available service categories for filtering
	 */
	public static function get_service_categories() {
		$categories_model = new OsServiceCategoryModel();
		$categories = $categories_model->get_results_as_models();

		$formatted_categories = [];
		
		// Add "All Categories" option
		$formatted_categories[] = [
			'id'   => 'all',
			'name' => __( 'All Categories', 'latepoint-bulk-services' )
		];

		// Add "Uncategorized" option if there are services without categories
		$services_model = new OsServiceModel();
		$uncategorized_count = $services_model->where( [ 
			'status' => LATEPOINT_SERVICE_STATUS_ACTIVE,
			'category_id' => 0 
		] )->count();
		
		if ( $uncategorized_count > 0 ) {
			$formatted_categories[] = [
				'id'   => '0',
				'name' => __( 'Uncategorized', 'latepoint-bulk-services' )
			];
		}

		// Add actual categories
		foreach ( $categories as $category ) {
			// Only include categories that have active services
			if ( $category->count_services() > 0 ) {
				$formatted_categories[] = [
					'id'   => $category->id,
					'name' => $category->name
				];
			}
		}

		return $formatted_categories;
	}

	/**
	 * Get bulk services button HTML for order forms
	 */
	public static function get_bulk_services_button( $order_id = '', $customer_id = '' ) {
		$button_html = '<a href="#" class="latepoint-btn latepoint-btn-sm latepoint-btn-outline bulk-services-trigger-btn" 
			data-order-id="' . esc_attr( $order_id ) . '" 
			data-customer-id="' . esc_attr( $customer_id ) . '"
			data-route="' . esc_attr( OsRouterHelper::build_route_name( 'bulk_services', 'get_selection_interface' ) ) . '">
			<i class="latepoint-icon latepoint-icon-layers"></i>
			<span>' . esc_html__( 'Add Bulk Services', 'latepoint-bulk-services' ) . '</span>
		</a>';

		return $button_html;
	}

	/**
	 * Check if bulk services can be added to current order
	 */
	public static function can_add_bulk_services( $order = null ) {
		// Check if multiple items are allowed
		if ( ! OsCartsHelper::can_checkout_multiple_items() ) {
			return false;
		}

		// Check user permissions
		if ( ! OsRolesHelper::can_user( 'booking__create' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Format duration for display
	 */
	public static function format_duration( $minutes ) {
		if ( $minutes < 60 ) {
			return $minutes . ' ' . __( 'min', 'latepoint-bulk-services' );
		} else {
			$hours = floor( $minutes / 60 );
			$remaining_minutes = $minutes % 60;
			
			if ( $remaining_minutes > 0 ) {
				return $hours . 'h ' . $remaining_minutes . 'm';
			} else {
				return $hours . 'h';
			}
		}
	}

	/**
	 * Get total price for selected services
	 */
	public static function calculate_total_price( $services ) {
		$total = 0;
		foreach ( $services as $service ) {
			$total += isset( $service['price'] ) ? floatval( $service['price'] ) : 0;
		}
		return $total;
	}

	/**
	 * Get total duration for selected services
	 */
	public static function calculate_total_duration( $services ) {
		$total = 0;
		foreach ( $services as $service ) {
			$total += isset( $service['duration'] ) ? intval( $service['duration'] ) : 0;
		}
		return $total;
	}

	/**
	 * Validate selected services data
	 */
	public static function validate_selected_services( $selected_services, $customer_id = '' ) {
		$errors = [];

		// Check if services are selected
		if ( empty( $selected_services ) || ! is_array( $selected_services ) ) {
			$errors[] = __( 'At least one service must be selected', 'latepoint-bulk-services' );
		}

		// Check customer
		if ( empty( $customer_id ) ) {
			$errors[] = __( 'Customer is required', 'latepoint-bulk-services' );
		}

		// Validate each service exists and is active
		if ( ! empty( $selected_services ) ) {
			foreach ( $selected_services as $service_id ) {
				$service = new OsServiceModel( $service_id );
				if ( $service->is_new_record() || $service->status !== LATEPOINT_SERVICE_STATUS_ACTIVE ) {
					$errors[] = sprintf( __( 'Service ID %d is not available', 'latepoint-bulk-services' ), $service_id );
				}
			}
		}

		return [
			'valid'  => empty( $errors ),
			'errors' => $errors
		];
	}
}

endif;
