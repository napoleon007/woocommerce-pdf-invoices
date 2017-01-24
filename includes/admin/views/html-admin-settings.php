<?php
/**
 * Admin settings page.
 *
 * @author      Bas Elbers
 * @category    Admin
 * @package     BE_WooCommerce_PDF_Invoices/Admin
 * @version     1.0.0
 */

$current_tab = ( isset( $_GET['tab'] ) ) ? sanitize_key( $_GET['tab'] ) : 'bewpi_general_settings'; ?>
<div class="wrap">
	<h2 class="nav-tab-wrapper">
		<?php foreach ( self::$settings as $setting ) :
			$tab_key = 'bewpi_' . $setting->id . '_settings';
			$tab_title = $setting->label;
			$active = ( $current_tab === $tab_key ) ? 'nav-tab-active' : '';
			printf( '<a class="nav-tab %1$s" href="?page=bewpi-invoices&tab=%2$s">%3$s</a>', $active, $tab_key, $tab_title );
		endforeach; ?>
	</h2>
	<form class="bewpi-settings-form" method="post" action="options.php" enctype="multipart/form-data">
		<?php
		wp_nonce_field( 'update-options' );
		settings_fields( $current_tab );
		do_settings_sections( $current_tab );
		submit_button();
		?>
	</form>
	<?php if ( ! is_plugin_active( 'woocommerce-pdf-invoices-premium/bootstrap.php' ) ) :
		include BEWPI_DIR . 'includes/admin/views/html-admin-settings-sidebar.php';
	endif; ?>
</div>
