<?php
/**
 * WooCommerce PDF Invoices Templates
 *
 * @class 		BEWPI_Templates
 * @version		1.0.0
 * @package		BE_WooCommerce_PDF_Invoices/Classes/Templates
 * @category	Class
 * @author 		Bas Elbers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BEWPI_Templates' ) ) {
	/**
	 * Class BEWPI_Templates.
	 */
	class BEWPI_Templates {

		/**
		 * PDF Templates.
		 *
		 * @var array
		 */
		private $templates;

		/**
		 * Class instance.
		 *
		 * @var BEWPI_Templates
		 */
		protected static $_instance = null;

		/**
		 * Main BEWPI_Templates Instance.
		 *
		 * Ensures only one instance of BEWPI_Templates is loaded or can be loaded.
		 *
		 * @since 2.6.0
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
		 * BEWPI_Templates constructor.
		 */
		public function __construct() {
			$this->init();
		}

		/**
		 * Initialize Templates Class.
		 */
		private function init() {
			// initialize templates.
			$template_directories = apply_filters( 'bewpi_template_directories', array(
				BEWPI_TEMPLATES_DIR . 'invoices/simple/',
				BEWPI_CUSTOM_TEMPLATES_INVOICES_DIR . 'simple/',
			) );

			foreach ( $template_directories as $template_dir ) {
				foreach ( glob( $template_dir . '*', GLOB_ONLYDIR ) as $template_path ) {
					$template_name     = basename( $template_path );
					$this->templates[] = array(
						'id'    => sanitize_title( $template_name ),
						'title' => ucfirst( sanitize_title( $template_name ) ),
					);
				}
			}
		}

		/**
		 * Get PDF templates.
		 *
		 * @return array
		 */
		public function get_templates() {
			return $this->templates;
		}

		/**
		 * Theme logo.
		 */
		public function get_theme_logo_url() {
			$theme_logo = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ) , 'full' );
			if ( ! $theme_logo ) {
				return '';
			}

			return $theme_logo[0];
		}
	}
}
