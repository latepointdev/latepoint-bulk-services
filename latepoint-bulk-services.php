<?php
/**
 * Plugin Name: LatePoint Addon - Bulk Services
 * Plugin URI: https://latepoint.dev
 * Description: Add multiple services in bulk to orders with advanced scheduling options
 * Version: 1.0.0
 * Author: KNYN.DEV
 * Author URI: https://knyn.dev/
 * Text Domain: latepoint-bulk-services
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// LatePoint class check will be done later in the initialization function

if ( ! class_exists( 'LatePointBulkServices' ) ) :

/**
 * Main Bulk Services Addon Class.
 */
class LatePointBulkServices {

	/**
	 * Addon version.
	 */
	public $version = '1.0.0';
	public $db_version = '1.0.0';
	public $addon_name = 'latepoint-bulk-services';

	/**
	 * LatePoint Bulk Services Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->init_hooks();
	}

	/**
	 * Define LatePoint Bulk Services Constants.
	 */
	public function define_constants() {
		$this->define( 'LATEPOINT_BULK_SERVICES_VERSION', $this->version );
		$this->define( 'LATEPOINT_BULK_SERVICES_PLUGIN_FILE', __FILE__ );
		$this->define( 'LATEPOINT_BULK_SERVICES_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		$this->define( 'LATEPOINT_BULK_SERVICES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}

	public static function public_stylesheets() {
		return plugin_dir_url( __FILE__ ) . 'public/stylesheets/';
	}

	public static function public_javascripts() {
		return plugin_dir_url( __FILE__ ) . 'public/javascripts/';
	}

	/**
	 * Define constant if not already set.
	 */
	public function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		// CONTROLLERS
		include_once( dirname( __FILE__ ) . '/lib/controllers/bulk_services_controller.php' );

		// HELPERS
		include_once( dirname( __FILE__ ) . '/lib/helpers/bulk_services_helper.php' );
	}

	public function init_hooks() {
		// Hook into the latepoint initialization action and initialize this addon
		add_action( 'latepoint_init', [ $this, 'latepoint_init' ] );

		// Include additional helpers and controllers 
		add_action( 'latepoint_includes', [ $this, 'includes' ] );

		// Modify a list of installed add-ons
		add_filter( 'latepoint_installed_addons', [ $this, 'register_addon' ] );

		// Include JS and CSS for the admin panel
		add_action( 'latepoint_admin_enqueue_scripts', [ $this, 'load_admin_scripts_and_styles' ] );

		// Add bulk services functionality to order forms
		add_action( 'latepoint_order_quick_edit_form_content_after', [ $this, 'add_bulk_services_to_order_form' ] );


		// init the addon
		add_action( 'init', array( $this, 'init' ), 0 );

		register_activation_hook( __FILE__, [ $this, 'on_activate' ] );
		register_deactivation_hook( __FILE__, [ $this, 'on_deactivate' ] );
	}

	// Loads addon specific javascript and stylesheets for backend (wp-admin)
	public function load_admin_scripts_and_styles() {
		// Stylesheets
		wp_enqueue_style( 'latepoint-bulk-services-admin', $this->public_stylesheets() . 'bulk-services-admin.css', false, $this->version );

		// Javascripts
		wp_enqueue_script( 'latepoint-bulk-services-admin', $this->public_javascripts() . 'bulk-services-admin.js', array( 'jquery' ), $this->version );
	}


	public function add_bulk_services_to_order_form( $order ) {
		// Ensure helper is loaded
		if ( ! class_exists( 'OsBulkServicesHelper' ) ) {
			include_once( dirname( __FILE__ ) . '/lib/helpers/bulk_services_helper.php' );
		}

		// Check if bulk services can be added
		if ( ! OsBulkServicesHelper::can_add_bulk_services( $order ) ) {
			return;
		}

		// Get available services and categories for the bulk add interface
		$services = OsBulkServicesHelper::get_available_services();
		$categories = OsBulkServicesHelper::get_service_categories();
		
		if ( empty( $services ) ) {
			return; // No services available
		}
		?>
		<!-- Bulk services interface embedded directly in Order Items section -->
		<div class="bulk-services-wrapper">
			<div class="os-form-sub-header">
				<h3><?php esc_html_e( 'Bulk Add Services', 'latepoint-bulk-services' ); ?></h3>
				<div class="os-form-sub-header-actions">
					<a href="#" class="latepoint-btn latepoint-btn-sm latepoint-btn-link bulk-services-cancel-btn">
						<span><?php esc_html_e( 'Cancel', 'latepoint-bulk-services' ); ?></span>
					</a>
				</div>
			</div>
			<?php if ( count( $categories ) > 1 ) : ?>
			<div class="bulk-services-category-filter">
				<label for="bulk-services-category-select"><?php esc_html_e( 'Filter by Category:', 'latepoint-bulk-services' ); ?></label>
				<select id="bulk-services-category-select" class="bulk-services-category-select">
					<?php foreach ( $categories as $category ) : ?>
						<option value="<?php echo esc_attr( $category['id'] ); ?>"><?php echo esc_html( $category['name'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php endif; ?>
			<div class="os-complex-connections-selector bulk-services-selector">
				<?php foreach ( $services as $service ) : ?>
					<div class="connection bulk-service-connection" 
						 data-service-id="<?php echo esc_attr( $service['id'] ); ?>"
						 data-category-id="<?php echo esc_attr( $service['category_id'] ); ?>">
						<div class="connection-i selector-trigger">
							<h3 class="connection-name"><?php echo esc_html( $service['name'] ); ?> • </h3>
							<div class="connection-details">
								<?php echo esc_html( $service['duration'] ); ?><?php esc_html_e( 'min', 'latepoint-bulk-services' ); ?> • <?php echo esc_html( $service['price_formatted'] ); ?>
								<?php if ( ! empty( $service['category_name'] ) ) : ?>
									<span class="service-category"> • <?php echo esc_html( $service['category_name'] ); ?></span>
								<?php endif; ?>
							</div>
							<input type="hidden" class="connection-child-is-connected" value="no">
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="bulk-services-actions">
				<button type="button" class="latepoint-btn latepoint-btn-primary bulk-add-selected-services-btn">
					<i class="latepoint-icon latepoint-icon-plus"></i>
					<span><?php esc_html_e( 'Add Selected Services', 'latepoint-bulk-services' ); ?></span>
				</button>
			</div>
		</div>

		<script type="text/javascript">
		jQuery(document).ready(function($) {
			// Find the Order Items section and add our bulk add button to the header actions
			var $orderItemsHeader = $('.order-items-info-w .os-form-sub-header-actions');
			var $addItemBtn = $('.order-form-add-item-btn');
			
			if ($orderItemsHeader.length && $addItemBtn.length && !$('.bulk-services-trigger-btn').length) {
				var bulkButton = '<a href="#" class="latepoint-btn latepoint-btn-sm latepoint-btn-link bulk-services-trigger-btn">' +
					'<i class="latepoint-icon latepoint-icon-layers"></i><span><?php esc_html_e( 'Bulk Add', 'latepoint-bulk-services' ); ?></span>' +
					'</a>';
				
				// Insert bulk button right after the "Add Another Item" button in the header actions
				$addItemBtn.after(' ' + bulkButton);
				
				// Move the bulk interface to be right after the order items list
				var $bulkWrapper = $('.bulk-services-wrapper');
				var $orderItemsList = $('.order-items-list');
				
				if ($bulkWrapper.length && $orderItemsList.length) {
					// Insert right after the order items list, within the order-items-info-w container
					$orderItemsList.after($bulkWrapper);
				}
			}
			
			// Handle bulk add button click
			$(document).on('click', '.bulk-services-trigger-btn', function(e) {
				e.preventDefault();
				$('.bulk-services-wrapper').slideDown();
				// Disable the button temporarily
				$(this).addClass('disabled');
			});
			
			// Handle cancel button
			$(document).on('click', '.bulk-services-cancel-btn', function(e) {
				e.preventDefault();
				$('.bulk-services-wrapper').slideUp();
				$('.bulk-services-trigger-btn').removeClass('disabled');
				// Reset selections and filters
				$('.bulk-service-connection').removeClass('active').show();
				$('.connection-child-is-connected').val('no');
				$('.bulk-services-category-select').val('all');
			});
			
			// Handle category filter change
			$(document).on('change', '.bulk-services-category-select', function() {
				var selectedCategory = $(this).val();
				var $connections = $('.bulk-service-connection');
				
				if (selectedCategory === 'all') {
					// Show all services
					$connections.show();
				} else {
					// Hide all first, then show matching category
					$connections.hide();
					$connections.filter('[data-category-id="' + selectedCategory + '"]').show();
				}
			});
			
			// Handle service selection (same as bundles form)
			$(document).on('click', '.bulk-service-connection .selector-trigger', function() {
				var $connection = $(this).closest('.connection');
				var $hiddenField = $connection.find('.connection-child-is-connected');
				
				if ($connection.hasClass('active')) {
					$connection.removeClass('active');
					$hiddenField.val('no');
				} else {
					$connection.addClass('active');
					$hiddenField.val('yes');
				}
			});
			
			// Handle add selected services
			$(document).on('click', '.bulk-add-selected-services-btn', function(e) {
				e.preventDefault();
				
				var selectedServices = [];
				$('.bulk-service-connection.active').each(function() {
					selectedServices.push($(this).data('service-id'));
				});
				
				if (selectedServices.length === 0) {
					alert('<?php esc_html_e( 'Please select at least one service', 'latepoint-bulk-services' ); ?>');
					return;
				}
				
				var $submitBtn = $(this);
				var originalText = $submitBtn.find('span').text();
				
				$submitBtn.prop('disabled', true).find('span').text('<?php esc_html_e( 'Adding...', 'latepoint-bulk-services' ); ?>');
				
				// Add each service individually using LatePoint's native method
				var servicesAdded = 0;
				var totalServices = selectedServices.length;
				
				selectedServices.forEach(function(serviceId) {
					var data = {
						action: latepoint_helper.route_action,
						route_name: '<?php echo esc_js( OsRouterHelper::build_route_name( 'orders', 'generate_booking_order_item_block' ) ); ?>',
						params: {
							service_id: serviceId,
							order_id: '<?php echo esc_js( $order->id ); ?>',
							customer_id: '<?php echo esc_js( $order->customer_id ); ?>'
						},
						return_format: 'json'
					};
					
					$.post(latepoint_helper.ajaxurl, data, function(response) {
						if (response.status === 'success') {
							$('.order-items-list').prepend(response.message);
							$('.order-items-list .no-results').remove();
							
							servicesAdded++;
							
							if (servicesAdded === totalServices) {
								// Hide bulk services interface and re-enable trigger button
								$('.bulk-services-wrapper').slideUp();
								$('.bulk-services-trigger-btn').removeClass('disabled');
								
								// Reset selections
								$('.bulk-service-connection').removeClass('active');
								$('.connection-child-is-connected').val('no');
								
								// Trigger LatePoint events
								if (typeof latepoint_init_booking_form_fields === 'function') {
									latepoint_init_booking_form_fields();
								}
								if (typeof latepoint_reload_price_breakdown === 'function') {
									latepoint_reload_price_breakdown();
								}
							}
						} else {
							alert(response.message || '<?php esc_html_e( 'Error adding service', 'latepoint-bulk-services' ); ?>');
						}
					}).fail(function() {
						alert('<?php esc_html_e( 'Error adding service', 'latepoint-bulk-services' ); ?>');
					}).always(function() {
						if (servicesAdded === totalServices) {
							$submitBtn.prop('disabled', false).find('span').text(originalText);
						}
					});
				});
			});
		});
		</script>
		<?php
	}


	/**
	 * Init addon when WordPress Initialises.
	 */
	public function init() {
		// Set up localisation.
		$this->load_plugin_textdomain();
	}

	public function latepoint_init() {
		// Initialize routing for bulk services
		if ( class_exists( 'OsRouterHelper' ) ) {
			// Ensure our controller is loaded and available for routing
			if ( ! class_exists( 'OsBulkServicesController' ) ) {
				include_once( LATEPOINT_BULK_SERVICES_PLUGIN_PATH . 'lib/controllers/bulk_services_controller.php' );
			}
		}
	}

	// set text domain for the addon, for string translations to work
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'latepoint-bulk-services', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	public function on_deactivate() {
		// Clean up if needed
	}

	public function on_activate() {
		do_action( 'latepoint_on_addon_activate', $this->addon_name, $this->version );
	}


	public function register_addon( $installed_addons ) {
		$installed_addons[] = [
			'name'       => $this->addon_name,
			'db_version' => $this->db_version,
			'version'    => $this->version
		];

		return $installed_addons;
	}
}

endif;

// Initialize the addon
function latepoint_bulk_services_init() {
	if ( class_exists( 'LatePoint' ) ) {
		$GLOBALS['LATEPOINT_BULK_SERVICES'] = new LatePointBulkServices();
	} else {
		// Add admin notice if LatePoint is not active
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-error"><p><strong>Bulk Services Addon:</strong> LatePoint plugin is required but not active.</p></div>';
		});
	}
}

// Hook into plugins_loaded to ensure LatePoint is loaded first
add_action( 'plugins_loaded', 'latepoint_bulk_services_init', 20 );

// Also try immediate initialization for debugging
if ( class_exists( 'LatePoint' ) ) {
	$LATEPOINT_BULK_SERVICES = new LatePointBulkServices();
}
