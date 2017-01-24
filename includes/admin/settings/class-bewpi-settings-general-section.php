<?php
/**
 * Settings General
 *
 * @author      Bas Elbers
 * @category    Admin
 * @package     BE_WooCommerce_PDF_Invoices/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BEWPI_Settings_General' ) ) {
	/**
	 * Class BEWPI_General_Settings.
	 */
	class BEWPI_Settings_General {
		/**
		 * Constant template settings key
		 *
		 * @var string
		 */
		const SETTINGS_KEY = 'bewpi_general_settings';

		/**
		 * Initializes the template settings.
		 */
		public function __construct() {
			//$this->id = 'general';
			//$this->label = __( 'General', 'woocommerce-pdf-invoices' );
			//$this->key = self::PREFIX . $this->id . '_settings';

			//parent::create_sections( $this->get_sections() );
			//parent::create_fields( $this->get_settings() );

			//register_setting( self::SETTINGS_KEY, self::SETTINGS_KEY, array( $this, 'save' ) );
		}

		public function settings() {
			$settings = array(
				'section_id' => 'email',
				'section_title' => __( 'Email Options', 'woocommerce-pdf-invoices' ),
				'section_description' => '', // optional
				'section_order' => 5,
				'fields' => array(
					array(
						'id' => self::PREFIX . 'email_types',
						'title' => __( 'Attach to Emails', 'woocommerce-pdf-invoices' ),
						'desc' => '',
						'placeholder' => '',
						'type' => 'checkboxes',
						'std' => 'This is std',
						'choices' => array(
							'new_order' => __( 'New order', 'woocommerce-pdf-invoices' ),
							'customer_on_hold_order' => __( 'Order on-hold', 'woocommerce-pdf-invoices' ),
							'customer_processing_order' => __( 'Processing order', 'woocommerce-pdf-invoices' ),
							'customer_completed_order' => __( 'Completed order', 'woocommerce-pdf-invoices' ),
							'customer_invoice' => __( 'Customer invoice', 'woocommerce-pdf-invoices' ),
						)
					),
				)
			);

			return $settings;
		}

		/**
		 * Settings configuration.
		 *
		 * @return array
		 */
		public static function get_settings() {
			return apply_filters( 'woocommerce_invoices_general_settings', array(
				array(
					'title' => __( 'General', 'woocommerce' ),
					'type'  => 'title',
					'id'    => 'invoices_general_options'
				),

				array(
					'title'    => __( 'PDF view mode', 'woocommerce' ),
					'desc'     => __( 'This controls if you want to view PDF in browser or download it.', 'woocommerce' ),
					'id'       => 'bewpi_view_pdf',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:300px;',
					'default'  => 'browser',
					'type'     => 'select',
					'options'  => array(
						'browser'  => __( 'Browser', 'woocommerce' ),
						'download'   => __( 'Download', 'woocommerce' ),
					),
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Dimensions Unit', 'woocommerce' ),
					'desc'     => __( 'This controls what unit you will define lengths in.', 'woocommerce' ),
					'id'       => 'woocommerce_dimension_unit',
					'class'    => 'wc-enhanced-select',
					'css'      => 'min-width:300px;',
					'default'  => 'cm',
					'type'     => 'select',
					'options'  => array(
						'm'  => __( 'm', 'woocommerce' ),
						'cm' => __( 'cm', 'woocommerce' ),
						'mm' => __( 'mm', 'woocommerce' ),
						'in' => __( 'in', 'woocommerce' ),
						'yd' => __( 'yd', 'woocommerce' ),
					),
					'desc_tip' => true,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'invoices_general_options',
				),

				array(
					'title' => __( 'Invoice Options', 'woocommerce' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'invoices_invoice_options_options',
				),

				array(
					'title'           => __( 'Product Ratings', 'woocommerce' ),
					'desc'            => __( 'Enable ratings on reviews', 'woocommerce' ),
					'id'              => 'woocommerce_enable_review_rating',
					'default'         => 'yes',
					'type'            => 'checkbox',
					'checkboxgroup'   => 'start',
					'show_if_checked' => 'option',
				),

				array(
					'desc'            => __( 'Ratings are required to leave a review', 'woocommerce' ),
					'id'              => 'woocommerce_review_rating_required',
					'default'         => 'yes',
					'type'            => 'checkbox',
					'checkboxgroup'   => '',
					'show_if_checked' => 'yes',
					'autoload'        => false,
				),

				array(
					'desc'            => __( 'Show "verified owner" label for customer reviews', 'woocommerce' ),
					'id'              => 'woocommerce_review_rating_verification_label',
					'default'         => 'yes',
					'type'            => 'checkbox',
					'checkboxgroup'   => '',
					'show_if_checked' => 'yes',
					'autoload'        => false,
				),

				array(
					'desc'            => __( 'Only allow reviews from "verified owners"', 'woocommerce' ),
					'id'              => 'woocommerce_review_rating_verification_required',
					'default'         => 'no',
					'type'            => 'checkbox',
					'checkboxgroup'   => 'end',
					'show_if_checked' => 'yes',
					'autoload'        => false,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'product_rating_options'
				),
				)
			);


			$settings = array(
				array(
					'id'       => 'bewpi-email-types',
					'name'     => self::PREFIX . 'email_types',
					'title'    => __( 'Attach to Emails', 'woocommerce-pdf-invoices' ),
					'page'     => self::SETTINGS_KEY,
					'section'  => 'email',
					'type'     => 'multiple_checkbox',
					'desc'     => '',
					'options'  => array(
						array(
							'name'    => __( 'New order', 'woocommerce-pdf-invoices' ),
							'value'   => 'new_order',
							'default' => 0,
						),
						array(
							'name'    => __( 'Order on-hold', 'woocommerce-pdf-invoices' ),
							'value'   => 'customer_on_hold_order',
							'default' => 0,
						),
						array(
							'name'    => __( 'Processing order', 'woocommerce-pdf-invoices' ),
							'value'   => 'customer_processing_order',
							'default' => 0,
						),
						array(
							'name'    => __( 'Completed order', 'woocommerce-pdf-invoices' ),
							'value'   => 'customer_completed_order',
							'default' => 1,
						),
						array(
							'name'    => __( 'Customer invoice', 'woocommerce-pdf-invoices' ),
							'value'   => 'customer_invoice',
							'default' => 0,
						),
					),
				),
				array(
					'id'       => 'bewpi-woocommerce-subscriptions-email-types',
					'name'     => self::PREFIX . 'woocommerce_subscriptions_email_types',
					'title'    => __( 'Attach to WooCommerce Subscriptions Emails', 'woocommerce-pdf-invoices' )
					              . sprintf( ' <img src="%1$s" alt="%2$s" title="%2$s" width="18"/>', BEWPI_URL . 'assets/images/star-icon.png', __( 'Premium', 'woocommerce-pdf-invoices' ) ),
					'page'     => self::SETTINGS_KEY,
					'section'  => 'email',
					'type'     => 'multiple_checkbox',
					'desc'     => '',
					'options'  => array(
						array(
							'name'    => __( 'New Renewal Order', 'woocommerce-subscriptions' ),
							'value'   => 'new_renewal_order',
							'default' => 0,
							'disabled' => 1,
						),
						array(
							'name'      => __( 'Subscription Switch Complete', 'woocommerce-subscriptions' ),
							'value'     => 'customer_completed_switch_order',
							'default'   => 0,
							'disabled'  => 1,
						),
						array(
							'name'      => __( 'Processing Renewal order', 'woocommerce-subscriptions' ),
							'value'     => 'customer_processing_renewal_order',
							'default'   => 0,
							'disabled'  => 1,
						),
						array(
							'name'      => __( 'Completed Renewal Order', 'woocommerce-subscriptions' ),
							'value'     => 'customer_completed_renewal_order',
							'default'   => 0,
							'disabled'  => 1,
						),
						array(
							'name'      => __( 'Customer Renewal Invoice', 'woocommerce-subscriptions' ),
							'value'     => 'customer_renewal_invoice',
							'default'   => 0,
							'disabled'  => 1,
						),
					),
				),
				array(
					'id'       => 'bewpi-view-pdf',
					'name'     => self::PREFIX . 'view_pdf',
					'title'    => __( 'View PDF', 'woocommerce-pdf-invoices' ),
					'page'     => self::SETTINGS_KEY,
					'section'  => 'download',
					'type'     => 'text',
					'desc'     => '',
					'options'  => array(
						array(
							'name'  => __( 'Download', 'woocommerce-pdf-invoices' ),
							'value' => 'download',
						),
						array(
							'name'  => __( 'Open in new browser tab/window', 'woocommerce-pdf-invoices' ),
							'value' => 'browser',
						),
					),
					'default'  => 'download',
				),
				array(
					'id'       => 'bewpi-download-invoice-account',
					'name'     => self::PREFIX . 'download_invoice_account',
					'title'    => '',
					'page'     => self::SETTINGS_KEY,
					'section'  => 'download',
					'type'     => 'checkbox',
					'desc'     => __( 'Enable download from my account', 'woocommerce-pdf-invoices' )
					              . '<br/><div class="bewpi-notes">'
					              . __( 'By default PDF is only downloadable when order has been paid, so order status should be Processing or Completed.', 'woocommerce-pdf-invoices' )
					              . '</div>',
					'class'    => 'bewpi-checkbox-option-title',
					'default'  => 1,
				),
				array(
					'id'       => 'bewpi-email-it-in',
					'name'     => self::PREFIX . 'email_it_in',
					'title'    => '',
					'page'     => self::SETTINGS_KEY,
					'section'  => 'cloud_storage',
					'type'     => 'checkbox',
					'desc'     => __( 'Enable Email It In', 'woocommerce-pdf-invoices' ),
					'class'    => 'bewpi-checkbox-option-title',
					'default'  => 0,
				),
				array(
					'id'       => 'bewpi-email-it-in-account',
					'name'     => self::PREFIX . 'email_it_in_account',
					'title'    => __( 'Email It In account', 'woocommerce-pdf-invoices' ),
					'page'     => self::SETTINGS_KEY,
					'section'  => 'cloud_storage',
					'type'     => 'text',
					'desc'     => sprintf( __( 'Get your account from your Email It In <a href="%1$s">user account</a>.', 'woocommerce-pdf-invoices' ), 'https://www.emailitin.com/user_account' ),
					'default'  => '',
				),
				array(
					'id'       => 'bewpi-invoice-number-column',
					'name'     => self::PREFIX . 'invoice_number_column',
					'title'    => '',
					'page'     => self::SETTINGS_KEY,
					'section'  => 'interface',
					'type'     => 'checkbox',
					'desc'     => __( 'Enable Invoice Number column' )
					              . '<br/><div class="bewpi-notes">' . __( 'Display invoice numbers on Shop Order page.', 'woocommerce-pdf-invoices' ) . '</div>',
					'class'    => 'bewpi-checkbox-option-title',
					'default'  => 0,
				),
				array(
					'id'       => 'bewpi-mpdf-debug',
					'name'     => self::PREFIX . 'mpdf_debug',
					'title'    => '',
					'page'     => self::SETTINGS_KEY,
					'section'  => 'debug',
					'type'     => 'checkbox',
					'desc'     => __( 'Enable mPDF debugging' )
					              . '<br/><div class="bewpi-notes">' . __( 'Enable mPDF debugging if you aren\'t able to create an invoice.', 'woocommerce-pdf-invoices' ) . '</div>',
					'class'    => 'bewpi-checkbox-option-title',
					'default'  => 0,
				),
			);
		}

		/**
		 * All sections.
		 *
		 * @return array
		 */
		private function get_sections() {
			$sections = array(
				array(
					'id' => 'email',
					'title' => __( 'Email Options', 'woocommerce-pdf-invoices' ),
					'callback' => null,
					'page' => self::SETTINGS_KEY,
				),
				array(
					'id' => 'download',
					'title' => __( 'Download Options', 'woocommerce-pdf-invoices' ),
					'callback' => null,
					'page' => self::SETTINGS_KEY,
				),
				array(
					'id' => 'cloud_storage',
					'title' => __( 'Cloud Storage Options', 'woocommerce-pdf-invoices' ),
					'callback' => array( $this, 'cloud_storage_section_callback' ),
					'page' => self::SETTINGS_KEY,
				),
				array(
					'id' => 'interface',
					'title' => __( 'Interface Options', 'woocommerce-pdf-invoices' ),
					'callback' => null,
					'page' => self::SETTINGS_KEY,
				),
				array(
					'id' => 'debug',
					'title' => __( 'Debug Options', 'woocommerce-pdf-invoices' ),
					'callback' => null,
					'page' => self::SETTINGS_KEY,
				),
			);

			return apply_filters( 'bewpi_general_settings_sections', $sections );
		}

		/**
		 * Cloud Storage section description.
		 */
		public function cloud_storage_section_callback() {
			printf( __( 'Sign-up at <a href="%1$s">Email It In</a> to send invoices to your Dropbox, OneDrive, Google Drive or Egnyte and enter your account below.', 'woocommerce-pdf-invoices' ), 'https://emailitin.com' );
		}

		/**
		 * Create options and merging defaults.
		 *
		 * @param array $settings Option group settings.
		 */
		public function create_options( $settings ) {
			// remove multiple checkbox types from settings.
			foreach ( $settings as $index => $setting ) {
				if ( array_key_exists( 'type', $setting ) && 'multiple_checkbox' === $setting['type'] ) {
					unset( $settings[ $index ] );
				}
			}

			// defaults of email types are within a lower hierarchy.
			$defaults = array();
			foreach ( $settings as $setting ) {
				if ( array_key_exists( 'type', $setting ) && 'multiple_checkbox' === $setting['type'] ) {
					$defaults = array_merge( $defaults, wp_list_pluck( $setting['options'], 'default', 'value' ) );
				}
			}

			// merge email type defaults.
			$defaults = array_merge( $defaults, wp_list_pluck( $settings, 'default', 'name' ) );
			$options  = array_merge( $defaults, (array) get_option( self::SETTINGS_KEY ) );

			update_option( self::SETTINGS_KEY, $options );
		}

		/**
		 * Save and validate options.
		 *
		 * @param array $input Option values.
		 *
		 * @return mixed|void
		 */
		public function save( $input ) {
			$output = get_option( self::SETTINGS_KEY );
			foreach ( $input as $key => $value ) {
				// strip all html tags and properly handle quoted strings.
				$output[ $key ] = stripslashes( $input[ $key ] );
			}

			// sanitize email it in account.
			if ( isset( $input['email_it_in_account'] ) ) {
				$sanitized_email = sanitize_email( $input['email_it_in_account'] );
				$output['email_it_in_account'] = $sanitized_email;
			}

			return apply_filters( 'bewpi_save_settings_' . $this->id, $output, $input );
		}
	}

	return BEWPI_Settings_General::get_settings();
}
