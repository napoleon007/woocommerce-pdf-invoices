<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BEWPI_Document_Invoice' ) ) :

	/**
	 * Cancelled Order Email.
	 *
	 * An email sent to the admin when an order is cancelled.
	 *
	 * @class       WC_Email_Cancelled_Order
	 * @version     2.2.7
	 * @package     WooCommerce/Classes/Emails
	 * @author      WooThemes
	 * @extends     WC_Email
	 */
	class BEWPI_Document_Invoice extends BEWPI_Document {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'invoice';
			$this->title          = __( 'Invoice', 'woocommerce' );
			$this->description    = __( 'Cancelled order emails are sent to chosen recipient(s) when orders have been marked cancelled (if they were previously processing or on-hold).', 'woocommerce' );
			$this->heading        = __( 'Cancelled order', 'woocommerce' );
			$this->subject        = __( '[{site_title}] Cancelled order ({order_number})', 'woocommerce' );
			$this->template_html  = 'emails/admin-cancelled-order.php';
			$this->template_plain = 'emails/plain/admin-cancelled-order.php';

			// Triggers for this email
			add_action( 'woocommerce_order_status_pending_to_cancelled_notification', array( $this, 'trigger' ) );
			add_action( 'woocommerce_order_status_on-hold_to_cancelled_notification', array( $this, 'trigger' ) );

			// Call parent constructor
			parent::__construct();

			// Other settings
			$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
		}

		/**
		 * Trigger.
		 *
		 * @param int $order_id
		 */
		public function trigger( $order_id ) {
			if ( $order_id ) {
				$this->object                  = wc_get_order( $order_id );
				$this->find['order-date']      = '{order_date}';
				$this->find['order-number']    = '{order_number}';
				$this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( $this->object->order_date ) );
				$this->replace['order-number'] = $this->object->get_order_number();
			}

			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		/**
		 * Get content html.
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html( $this->template_html, array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => true,
				'plain_text'    => false,
				'email'         => $this
			) );
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html( $this->template_plain, array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => true,
				'plain_text'    => true,
				'email'         => $this,
			) );
		}

		/**
		 * Initialize settings form fields.
		 */
		public function init_form_fields() {
			$mailer    = WC()->mailer();
			$templater = BEWPI()->templater();

			$this->form_fields = array(
				array(
					'title' => __( 'General Options', 'woocommerce-pdf-invoices' ),
					'type'  => 'title',
				),
				'template'              => array(
					'title'       => __( 'Template', 'woocommerce-pdf-invoices' ),
					'type'        => 'select',
					'description' => __( 'Choose which PDF template you want to use as invoice.', 'woocommerce-pdf-invoices' ),
					'default'     => 'micro',
					'options'     => wp_list_pluck( $templater->get_templates(), 'title', 'id' ),
					'desc_tip'    => true,
				),
				'email_types'           => array(
					'title'       => __( 'Email types', 'woocommerce' ),
					'type'        => 'multiselect',
					'description' => __( 'Choose to which email types the invoice should be attached to.', 'woocommerce-pdf-invoices' ),
					'default'     => '',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => wp_list_pluck( $mailer->get_emails(), 'title', 'id' ),
					'desc_tip'    => true,
				),
				'color_theme'           => array(
					'title'       => __( 'Color', 'woocommerce-pdf-invoices' ),
					'description' => __( 'The color theme for the invoice template.', 'woocommerce-pdf-invoices' ),
					'type'        => 'color',
					'css'         => 'width:6em;',
					'default'     => '#11a7e7',
					'desc_tip'    => true,
				),
				'downloadable'          => array(
					'title'   => __( 'Enable download', 'woocommerce-pdf-invoices' ),
					'type'    => 'checkbox',
					'label'   => __( 'Download PDF from My Account when order has been paid and status is Processing or Completed.', 'woocommerce-pdf-invoices' ),
					'default' => 'yes',
				),
				'hide_shipping_address' => array(
					'title' => __( 'Hide shipping address' ),
					'label' => __( 'By default the shipping address won\'t be shown when order has only virtual products.', 'woocommerce-pdf-invoices' ),
					'type'  => 'checkbox',
				),
				'hide_customer_notes' => array(
					'title' => __( 'Hide customer notes' ),
					'label' => __( 'By default the customer notes will be shown.', 'woocommerce-pdf-invoices' ),
					'type'  => 'checkbox',
				),
				'terms' => array(
					'title'    => __( 'Terms & conditions etc.', 'woocommerce-pdf-invoices' ),
					'description' => __( 'Add terms & conditions, policies etc.', 'woocommerce-pdf-invoices' ),
					'type'     => 'textarea',
					'css'      => 'max-width: 350px;min-height:140px;',
					'default' => '',
					'desc_tip' => true,
				),
			);
		}
	}

endif;

return new BEWPI_Document_Invoice();
