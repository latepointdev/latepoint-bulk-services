<div class="os-form-w bulk-services-selection-wrapper">
	<div class="os-form-header">
		<h2><?php esc_html_e( 'Select Services to Add', 'latepoint-bulk-services' ); ?></h2>
		<a href="#" class="latepoint-lightbox-close"><i class="latepoint-icon latepoint-icon-x"></i></a>
	</div>
	<div class="os-form-content">
		<?php if ( ! empty( $services ) ) : ?>
			<div class="os-form-group">
				<label><?php esc_html_e( 'Available Services', 'latepoint-bulk-services' ); ?></label>
				<div class="bulk-services-grid">
					<?php foreach ( $services as $service ) : ?>
						<div class="bulk-service-item" 
							 data-service-id="<?php echo esc_attr( $service['id'] ); ?>"
							 data-service-name="<?php echo esc_attr( $service['name'] ); ?>"
							 data-service-duration="<?php echo esc_attr( $service['duration'] ); ?>"
							 data-service-price="<?php echo esc_attr( $service['price'] ); ?>">
							<div class="bulk-service-checkbox">
								<input type="checkbox" 
									   name="selected_services[]" 
									   value="<?php echo esc_attr( $service['id'] ); ?>" 
									   id="service_<?php echo esc_attr( $service['id'] ); ?>" 
									   class="bulk-service-checkbox-input">
								<label for="service_<?php echo esc_attr( $service['id'] ); ?>" class="bulk-service-checkbox-label"></label>
							</div>
							<div class="bulk-service-info">
								<h4 class="bulk-service-name"><?php echo esc_html( $service['name'] ); ?></h4>
								<?php if ( ! empty( $service['short_description'] ) ) : ?>
									<p class="bulk-service-description"><?php echo esc_html( $service['short_description'] ); ?></p>
								<?php endif; ?>
								<div class="bulk-service-meta">
									<div class="bulk-service-duration">
										<i class="latepoint-icon latepoint-icon-clock"></i>
										<?php echo esc_html( $service['duration'] ); ?> <?php esc_html_e( 'min', 'latepoint-bulk-services' ); ?>
									</div>
									<div class="bulk-service-price">
										<i class="latepoint-icon latepoint-icon-dollar-sign"></i>
										<?php echo esc_html( $service['price_formatted'] ); ?>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		<?php else : ?>
			<div class="no-results-w">
				<div class="icon-w"><i class="latepoint-icon latepoint-icon-grid-3"></i></div>
				<h2><?php esc_html_e( 'No Services Found', 'latepoint-bulk-services' ); ?></h2>
			</div>
		<?php endif; ?>
	</div>
	
	<?php if ( ! empty( $services ) ) : ?>
		<div class="bulk-services-selection-summary">
			<div class="selection-summary-content">
				<span class="selected-count">0 services selected</span>
				<span class="total-duration">Total: 0 min</span>
				<span class="total-price">£0.00</span>
			</div>
		</div>
		
		<div class="os-form-buttons">
			<button type="button" class="latepoint-btn latepoint-btn-secondary bulk-services-cancel-btn">
				<?php esc_html_e( 'Cancel', 'latepoint-bulk-services' ); ?>
			</button>
			<button type="button" class="latepoint-btn latepoint-btn-primary bulk-add-services-btn" disabled>
				<i class="latepoint-icon latepoint-icon-plus"></i>
				<span><?php esc_html_e( 'Add Services', 'latepoint-bulk-services' ); ?></span>
			</button>
		</div>
	<?php endif; ?>

	<input type="hidden" name="order_id" value="<?php echo esc_attr( $order_id ); ?>">
	<input type="hidden" name="customer_id" value="<?php echo esc_attr( $customer_id ); ?>">
	<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'bulk_services_add' ) ); ?>">
</div>
