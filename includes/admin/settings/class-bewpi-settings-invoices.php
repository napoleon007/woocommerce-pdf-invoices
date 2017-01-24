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
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
			add_action( 'woocommerce_admin_field_documents', array( $this, 'document_settings' ) );
		}

		/**
		 * Output the settings.
		 */
		public function output() {
			global $current_section;

			// Define emails that can be customised here
			$documenter          = BEWPI()->documenter();
			$email_templates     = $documenter->get_documents();

			if ( $current_section ) {
				foreach ( $email_templates as $email_key => $email ) {
					if ( strtolower( $email_key ) == $current_section ) {
						$email->admin_options();
						break;
					}
				}
			} else {
				$settings = $this->get_settings();
				WC_Admin_Settings::output_fields( $settings );
			}

			add_filter( 'admin_footer_text', array( __CLASS__, 'plugin_review_text' ), 50 );
			add_filter( 'update_footer', array( __CLASS__, 'plugin_version' ), 50 );
		}

		/**
		 * Save settings.
		 */
		public function save() {
			global $current_section;

			if ( ! $current_section ) {
				WC_Admin_Settings::save_fields( $this->get_settings() );

			} else {
				$wc_emails = BEWPI_Documents::instance();

				if ( in_array( $current_section, array_map( 'sanitize_title', array_keys( $wc_emails->get_emails() ) ) ) ) {
					foreach ( $wc_emails->get_emails() as $email_id => $email ) {
						if ( $current_section === sanitize_title( $email_id ) ) {
							do_action( 'woocommerce_update_options_' . $this->id . '_' . $email->id );
						}
					}
				} else {
					do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
				}
			}
		}

		/**
		 * Get the section settings array.
		 *
		 * @return mixed|void
		 */
		public function get_settings() {

			$settings = apply_filters( 'woocommerce_email_settings', array(

				array( 'title' => __( 'PDF Documents', 'woocommerce-pdf-invoices' ),  'desc' => __( 'PDF documents from WooCommerce PDF Invoices plugin are listed below. Click on a document to configure it.', 'woocommerce-pdf-invoices' ), 'type' => 'title', 'id' => 'documents_settings' ),

				array( 'type' => 'documents' ),

				array( 'type' => 'sectionend', 'id' => 'documents_settings' ),

				array( 'title' => __( 'General Options', 'woocommerce-pdf-invoices' ), 'desc' => '', 'type' => 'title', 'id' => 'general_settings' ),

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
					'desc_tip' =>  true,
				),

				array( 'type' => 'sectionend', 'id' => 'general_settings' ),

			) );

			return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
		}

		/**
		 * Output email notification settings.
		 */
		public function document_settings() {
			// Define emails that can be customised here
			$documenter          = BEWPI()->documenter();
			$email_templates =   $documenter->get_documents();
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
								'actions'    => ''
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
										} elseif ( $email->is_enabled() ) {
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
