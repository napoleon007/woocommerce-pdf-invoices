<?php
/**
 * Settings configuration.
 *
 * Validate and output settings.
 *
 * @author      Bas Elbers
 * @category    Abstract Class
 * @package     BE_WooCommerce_PDF_Invoices/Abstracts
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BEWPI_Abstract_Setting' ) ) {
	/**
	 * Class BEWPI_Abstract_Setting.
	 */
	abstract class BEWPI_Abstract_Setting {
		/**
		 * Settings slug.
		 *
		 * @var string
		 */
		public $id;

		/**
		 * Settings name.
		 *
		 * @var string
		 */
		public $label;

		/**
		 * Settings key.
		 *
		 * @var string
		 */
		protected $key;

		/**
		 * Options and settings prefix.
		 *
		 * @var string
		 */
		const PREFIX = 'bewpi_';

		/**
		 * Gets all the tags that are allowed.
		 *
		 * @return string|void
		 */
		protected function allowed_tags_text() {
			$allowed_tags_encoded = array_map( 'htmlspecialchars', array( '<b>', '<i>', '<br>', '<br/>' ) );
			$allowed_tags_formatted  = '<code>' . join( '</code>, <code>', $allowed_tags_encoded ) . '</code>';
			$allowed_tags_text = sprintf( __( 'Allowed HTML tags: %1$s.', 'woocommerce-pdf-invoices' ), $allowed_tags_formatted );
			return $allowed_tags_text;
		}

		/**
		 * String validation.
		 *
		 * @param string $str the string to validate.
		 *
		 * @return bool
		 */
		protected function strip_str( $str ) {
			$str = preg_replace( '/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i', '<$1$2>', $str );
			return strip_tags( $str, '<b><i><br><br/>' );
		}

		/**
		 * Multiple checkboxes html.
		 *
		 * @param array $args option arguments.
		 */
		public function multiple_checkbox_callback( $args ) {
			include BEWPI_DIR . 'includes/admin/views/html-multiple-checkbox-setting.php';
		}

		/**
		 * Select html.
		 *
		 * @param array $args option arguments.
		 */
		public function select_callback( $args ) {
			$options = get_option( $args['page'] );
			?>
			<select id="<?php echo $args['id']; ?>" name="<?php echo $args['page'] . '[' . $args['name'] . ']'; ?>">
				<?php
				foreach ( $args['options'] as $option ) :
					?>
					<option
						value="<?php echo esc_attr( $option['value'] ); ?>" <?php selected( $options[ $args['name'] ], $option['value'] ); ?>><?php echo esc_html( $option['name'] ); ?></option>
					<?php
				endforeach;
				?>
			</select>
			<div class="bewpi-notes"><?php echo $args['desc']; ?></div>
			<?php
		}

		public function input_callback( $args ) {
			$options     = get_option( $args['page'] );
			$class       = ( isset( $args['class'] ) ) ? $args['class'] : "bewpi-notes";
			$is_checkbox = $args['type'] === 'checkbox';
			if ( $is_checkbox ) { ?>
				<input type="hidden" name="<?php echo $args['page'] . '[' . $args['name'] . ']'; ?>" value="0"/>
			<?php } ?>
			<input id="<?php echo $args['id']; ?>"
			       name="<?php echo $args['page'] . '[' . $args['name'] . ']'; ?>"
			       type="<?php echo $args['type']; ?>"
			       value="<?php echo $is_checkbox ? 1 : esc_attr( $options[ $args['name'] ] ); ?>"

				<?php if ( $is_checkbox ) {
					checked( $options[ $args['name'] ] );
				}

				if ( isset ( $args['attrs'] ) ) {
					foreach ( $args['attrs'] as $attr ) {
						echo $attr . ' ';
					}
				}
				?>
			/>
			<?php if ( $is_checkbox ) { ?>
				<label for="<?php echo $args['id']; ?>"
				       class="<?php echo $class; ?>"><?php echo $args['desc']; ?></label>
			<?php } else { ?>
				<div class="<?php echo $class; ?>"><?php echo $args['desc']; ?></div>
			<?php } ?>
			<?php
		}

		public function logo_callback( $args ) {
			$options = get_option( $args['page'] );
			?>
			<input id="<?php echo $args['id']; ?>"
			       name="<?php echo $args['name']; ?>"
			       type="<?php echo $args['type']; ?>"
			       accept="image/*"
			/>
			<div class="bewpi-notes"><?php echo $args['desc']; ?></div>
			<input id="<?php echo $args['id'] . '-value'; ?>"
			       name="<?php echo $args['name']; ?>"
			       type="hidden"
			       value="<?php echo esc_attr( $options[ $args['name'] ] ); ?>"
			/>

			<?php
			if ( ! empty( $options[ $args['name'] ] ) ) {
				?>
				<div id="<?php echo $args['id'] . '-wrapper'; ?>">
					<img id="<?php echo $args['id'] . '-image'; ?>"
					     src="<?php echo esc_attr( $options[ $args['name'] ] ); ?>"/>
					<img id="<?php echo $args['id'] . '-delete'; ?>"
					     src="<?php echo BEWPI_URL . '/assets/images/delete-icon.png'; ?>"
					     onclick="bewpi.setting.removeCompanyLogo()"
					     title="<?php _e( 'Remove logo', 'woocommerce-pdf-invoices' ); ?>"/>
				</div>
				<?php
			}
		}

		public function textarea_callback( $args ) {
			$options = get_option( $args['page'] );
			?>
			<textarea id="<?php echo $args['id']; ?>"
			          name="<?php echo $args['page'] . '[' . $args['name'] . ']'; ?>"
			          rows="5"
			><?php echo esc_textarea( $options[ $args['name'] ] ); ?></textarea>
			<div class="bewpi-notes"><?php echo $args['desc']; ?></div>
			<?php
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
			$options  = array_merge( $defaults, (array) get_option( $this->key ) );

			update_option( self::SETTINGS_KEY, $options );
		}

		/**
		 * Save settings sections.
		 *
		 * @param array $sections Settings sections from specific option group.
		 */
		protected static function create_sections( $sections ) {
			foreach ( $sections as $section ) {
				add_settings_section( $section['id'], $section['title'], $section['callback'], $section['page'] );
			}
		}

		/**
		 * Save settings fields.
		 *
		 * @param array $settings Settings from specific option group.
		 */
		public static function create_fields( $settings ) {
			foreach ( $settings as $setting ) {
				add_settings_field( $setting['name'], $setting['title'], $setting['callback'], $setting['page'], $setting['section'], $setting );
			};
		}
	}
}