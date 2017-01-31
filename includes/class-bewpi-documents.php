<?php
/**
 * PDF Documents Controller Class.
 *
 * WooCommerce PDF Invoices Documents Class which handles the attachment of the pdf invoices.sending on transactional emails and email templates. This class loads in available emails.
 *
 * @class 		WC_Emails
 * @version		2.3.0
 * @package		WooCommerce/Classes/Emails
 * @category	Class
 * @author 		WooThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BEWPI_Documents.
 */
class BEWPI_Documents {

	/**
	 * Array with all documents.
	 *
	 * @var array
	 */
	public $documents;

	/**
	 * The single instance of the class.
	 *
	 * @var BEWPI_Documents
	 */
	protected static $_instance = null;

	/**
	 * Main WC_Emails Instance.
	 *
	 * Ensures only one instance of WC_Emails is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @static
	 * @return WC_Emails Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * BEWPI_Documents constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Init email classes.
	 */
	public function init() {
		// Include document classes.
		include_once BEWPI_DIR . 'includes/documents/class-bewpi-document.php';

		$this->documents['BEWPI_Document_Invoice'] = include BEWPI_DIR . 'includes/documents/class-bewpi-document-invoice.php';

		$this->documents = apply_filters( 'bewpi_document_classes', $this->documents );
	}

	/**
	 * Return the email classes - used in admin to load settings.
	 *
	 * @return array
	 */
	public function get_documents() {
		return $this->documents;
	}
}
