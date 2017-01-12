<?php
/**
 * Admin Settings Class.
 *
 * @author   Bas Elbers
 * @category Admin
 * @package  BE_WooCommerce_PDF_Invoices/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BEWPI_Admin_Settings' ) ) {
	/**
	 * Class BEWPI_Admin_Settings.
	 */
	class BEWPI_Admin_Settings {
		/**
		 * Setting pages.
		 *
		 * @var array
		 */
		private static $settings = array();

		/**
		 * Initialize settings.
		 */
		public static function init() {
			self::init_hooks();
		}

		/**
		 * Initialize hooks.
		 */
		private static function init_hooks() {
			add_action( 'admin_menu', array( __CLASS__, 'add_wc_submenu_options_page' ) );
			add_action( 'admin_init', array( __CLASS__, 'get_settings_pages' ) );
		}

		/**
		 * Add submenu to WooCommerce menu and display options page.
		 */
		public static function add_wc_submenu_options_page() {
			add_submenu_page( 'woocommerce', __( 'Invoices', 'woocommerce-pdf-invoices' ), __( 'Invoices', 'woocommerce-pdf-invoices' ), 'manage_options', 'bewpi-invoices', array(
				__CLASS__,
				'output',
			) );
		}

		/**
		 * Include the settings page classes.
		 */
		public static function get_settings_pages() {
			if ( empty( self::$settings ) ) {
				$settings = array();

				include_once BEWPI_DIR . 'includes/abstracts/abstract-bewpi-setting.php';

				$settings[] = include BEWPI_DIR . 'includes/admin/settings/class-bewpi-settings-general.php';
				$settings[] = include BEWPI_DIR . 'includes/admin/settings/class-bewpi-settings-template.php';

				self::$settings = apply_filters( 'bewpi_get_settings_pages', $settings );
			}

			return self::$settings;
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

		/**
		 * WooCommerce PDF Invoices settings page.
		 */
		public static function output() {
			include BEWPI_DIR . 'includes/admin/views/html-admin-settings.php';

			// add rate plugin text in footer.
			add_filter( 'admin_footer_text', array( __CLASS__, 'plugin_review_text' ), 50 );
			add_filter( 'update_footer', array( __CLASS__, 'plugin_version' ), 50 );
		}
	}

	BEWPI_Admin_Settings::init();

}
