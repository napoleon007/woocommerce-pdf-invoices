<?php
/**
 * WooCommerce PDF Invoices Invoices Tab Settings
 *
 * @author   Bas Elbers
 * @category Admin
 * @package  BE_WooCommerce_PDF_Invoices/Admin
 * @version  3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BEWPI_Settings_Invoices' ) ) {
	/**
	 * Invoices Settings.
	 */
	class BEWPI_Settings_Invoices extends BEWPI_Settings_Page {
		/**
		 * BEWPI_Settings_Invoices constructor.
		 */
		public function __construct() {

			$this->id    = 'invoices';
			$this->label = __( 'Invoices', 'woocommerce' );

			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 99 );
			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
			add_action( 'woocommerce_admin_field_documents', array( $this, 'document_settings' ) );
		}

		/**
		 * Get sections.
		 *
		 * @return array
		 */
		public function get_sections() {

			$sections = array(
				''            => __( 'General', 'woocommerce-pdf-invoices' ),
				'debug'       => __( 'Debug', 'woocommerce-pdf-invoices' ),
			);

			return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
		}

		/**
		 * Output the settings.
		 */
		public function output() {
			global $current_section;

			// show admin options if current section is document type section.
			if ( ! array_key_exists( $current_section, $this->get_sections() ) ) {
				$documenter      = BEWPI()->documenter();
				foreach ( $documenter->get_documents() as $document_key => $document ) {
					if ( strtolower( $document_key ) === $current_section ) {
						$document->admin_options();
						break;
					}
				}
			} else {
				WC_Admin_Settings::output_fields( $this->get_settings( $current_section ) );
			}

			add_filter( 'admin_footer_text', array( __CLASS__, 'plugin_review_text' ), 50 );
			add_filter( 'update_footer', array( __CLASS__, 'plugin_version' ), 50 );
		}

		/**
		 * Save settings.
		 */
		public function save() {
			global $current_section;

			// save section or document type options?
			if ( array_key_exists( $current_section, $this->get_sections() ) ) {
				WC_Admin_Settings::save_fields( $this->get_settings( $current_section ) );
			} else {
				$documenter = BEWPI()->documenter();
				foreach (  $documenter->get_documents() as $document_key => $document ) {
					if ( strtolower( $document_key ) === $current_section ) {
						do_action( 'woocommerce_update_options_' . $this->id . '_' . $document->id );
						break;
					}
				}
			}
		}

		/**
		 * Get the section settings array.
		 *
		 * @param string $current_section Settings section.
		 *
		 * @return mixed|void
		 */
		public function get_settings( $current_section = '' ) {

			if ( 'debug' === $current_section ) {

				$settings = apply_filters( 'woocommerce_invoices_debug_settings', array(

					array(
						'title' => __( 'Debug Options', 'woocommerce-pdf-invoices' ),
						'desc'  => '',
						'type'  => 'title',
						'id'    => 'debug_settings',
					),

					array(
						'title'   => __( 'Enable mPDF debugging', 'woocommerce-pdf-invoices' ),
						'desc'    => '',
						'id'      => 'bewpi_mpdf_debugging',
						'default' => 'no',
						'type'    => 'checkbox',
					),

					array( 'type' => 'sectionend', 'id' => 'debug_settings' ),

				) );

			} else {

				$settings = apply_filters( 'woocommerce_invoices_general_settings', array(

					array(
						'title' => __( 'PDF Documents', 'woocommerce-pdf-invoices' ),
						'desc'  => __( 'PDF documents from WooCommerce PDF Invoices plugin are listed below. Click on a document to configure it.', 'woocommerce-pdf-invoices' ),
						'type'  => 'title',
						'id'    => 'documents_settings',
					),

					array( 'type' => 'documents' ),

					array( 'type' => 'sectionend', 'id' => 'documents_settings' ),

					array(
						'title' => __( 'General Options', 'woocommerce-pdf-invoices' ),
						'desc'  => '',
						'type'  => 'title',
						'id'    => 'general_settings',
					),

					array(
						'title'    => __( 'PDF view mode', 'woocommerce-pdf-invoices' ),
						'desc'     => __( 'This controls how to view the PDF document.', 'woocommerce' ),
						'id'       => 'bewpi_pdf_view_mode',
						'class'    => 'wc-enhanced-select',
						'css'      => 'min-width:300px;',
						'default'  => 'browser',
						'type'     => 'select',
						'options'  => array(
							'browser'  => __( 'Browser', 'woocommerce-pdf-invoices' ),
							'download' => __( 'Download', 'woocommerce-pdf-invoices' ),
						),
						'desc_tip' => true,
					),

					array(
						'title'   => __( 'Enable Invoice Number column' ),
						'desc'    => __( 'Show invoice numbers in Shop Order table.', 'woocommerce-pdf-invoices' ),
						'id'      => 'bewpi_invoice_number_column',
						'default' => 'no',
						'type'    => 'checkbox',
					),

					array( 'type' => 'sectionend', 'id' => 'general_settings' ),

					array(
						'title' => __( 'Cloud Storage Options', 'woocommerce-pdf-invoices' ),
						'desc'  => sprintf( __( 'Sign-up at <a href="%1$s">Email It In</a> to send invoices to your Dropbox, OneDrive, Google Drive or Egnyte and enter your <a href="%2$s">account</a> below.', 'woocommerce-pdf-invoices' ),
							'https://emailitin.com',
							'https://www.emailitin.com/user_account'
						),
						'type'  => 'title',
						'id'    => 'cloud_settings',
					),

					array(
						'title'   => __( 'Enable Email It In', 'woocommerce-pdf-invoices' ),
						'desc'    => '',
						'id'      => 'bewpi_emailitin',
						'default' => 'no',
						'type'    => 'checkbox',
					),

					array(
						'title'   => __( 'Email It In account', 'woocommerce-pdf-invoices' ),
						'desc'    => '',
						'id'      => 'bewpi_emailitin_account',
						'default' => '',
						'type'    => 'text',
						'css'      => 'min-width:350px;',
					),

					array( 'type' => 'sectionend', 'id' => 'cloud_settings' ),

				) );
			}

			return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
		}

		/**
		 * Output email notification settings.
		 */
		public function document_settings() {
			$documenter      = BEWPI()->documenter();
			$email_templates = $documenter->get_documents();
			?>
			<tr valign="top">
				<td class="wc_emails_wrapper" colspan="2">
					<table class="wc_emails widefat" cellspacing="0">
						<thead>
						<tr>
							<?php
							$columns = apply_filters( 'woocommerce_email_setting_columns', array(
								'status'     => '',
								'name'       => __( 'Email', 'woocommerce' ),
								'email_type' => __( 'Content Type', 'woocommerce' ),
								'recipient'  => __( 'Recipient(s)', 'woocommerce' ),
								'actions'    => '',
							) );
							foreach ( $columns as $key => $column ) {
								echo '<th class="wc-email-settings-table-' . esc_attr( $key ) . '">' . esc_html( $column ) . '</th>';
							}
							?>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach ( $email_templates as $email_key => $email ) {
							echo '<tr>';

							foreach ( $columns as $key => $column ) {

								switch ( $key ) {
									case 'name' :
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
											<a href="' . admin_url( 'admin.php?page=wc-settings&tab=invoices&section=' . strtolower( $email_key ) ) . '">' . $email->get_title() . '</a>
											' . wc_help_tip( $email->get_description() ) . '
										</td>';
										break;
									case 'recipient' :
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
											' . esc_html( $email->is_customer_email() ? __( 'Customer', 'woocommerce' ) : $email->get_recipient() ) . '
										</td>';
										break;
									case 'status' :
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">';

										if ( $email->is_manual() ) {
											echo '<span class="status-manual tips" data-tip="' . __( 'Manually sent', 'woocommerce' ) . '">' . __( 'Manual', 'woocommerce' ) . '</span>';
										} elseif ( $email->is_attached() ) {
											echo '<span class="status-enabled tips" data-tip="' . __( 'Enabled', 'woocommerce' ) . '">' . __( 'Yes', 'woocommerce' ) . '</span>';
										} else {
											echo '<span class="status-disabled tips" data-tip="' . __( 'Disabled', 'woocommerce' ) . '">-</span>';
										}

										echo '</td>';
										break;
									case 'email_type' :
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
											' . esc_html( $email->get_content_type() ) . '
										</td>';
										break;
									case 'actions' :
										echo '<td class="wc-email-settings-table-' . esc_attr( $key ) . '">
											<a class="button alignright tips" data-tip="' . __( 'Configure', 'woocommerce' ) . '" href="' . admin_url( 'admin.php?page=wc-settings&tab=invoices&section=' . strtolower( $email_key ) ) . '">' . __( 'Configure', 'woocommerce' ) . '</a>
										</td>';
										break;
									default :
										do_action( 'woocommerce_email_setting_column_' . $key, $email );
										break;
								}
							}

							echo '</tr>';
						}
						?>
						</tbody>
					</table>
				</td>
			</tr>
			<?php
		}

		/**
		 * Add rate plugin text to footer of settings page.
		 *
		 * @return string
		 */
		public static function plugin_review_text() {
			return sprintf( __( 'If you like <strong>WooCommerce PDF Invoices</strong> please leave us a <a href="%s">★★★★★</a> rating. A huge thank you in advance!', 'woocommerce-pdf-invoices' ), 'https://wordpress.org/support/view/plugin-reviews/woocommerce-pdf-invoices?rate=5#postform' );
		}

		/**
		 * Plugin version text in footer of settings page.
		 *
		 * @return string
		 */
		public static function plugin_version() {
			return sprintf( __( 'Version %s', 'woocommerce-pdf-invoices' ), BEWPI_VERSION );
		}
	}
}

return new BEWPI_Settings_Invoices();
