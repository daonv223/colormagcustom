<?php
/**
 * ColorMag enqueue CSS and JS files.
 *
 * @package    ColorMag
 *
 * @since      ColorMag 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ColorMag_Enqueue_Scripts' ) ) {

	/**
	 * Enqueue Scripts.
	 */
	class ColorMag_Enqueue_Scripts {

		/**
		 * Instance.
		 *
		 * @access private
		 * @var object
		 */
		private static $instance;

		/**
		 * Initiator.
		 */
		public static function get_instance() {

			if ( ! isset( self::$instance ) ) {

				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 */
		private function __construct() {

			$this->setup_hooks();
		}

		/**
		 * Define hooks.
		 *
		 * @return void
		 */
		public function setup_hooks() {

			add_action( 'wp_enqueue_scripts', array( $this, 'colormag_scripts_styles_method' ) );

			add_action( 'enqueue_block_editor_assets', array( $this, 'colormag_block_editor_styles' ), 1 );
		}

		/**
		 * Enqueue CSS and JS files.
		 */
		public function colormag_scripts_styles_method() {

			// Return if enqueueing is not enabled by the user.
			if ( false === apply_filters( 'colormag_enqueue_theme_assets', true ) ) {
				return;
			}

			// Variables.
			$suffix              = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
			$skin_color          = get_theme_mod( 'colormag_color_skin_setting', 'white' );
			$inline_style_handle = ( 'white' === $skin_color ) ? 'colormag_style' : 'colormag_dark_style';

			// Loads our main css.
			wp_enqueue_style( 'colormag_style', get_stylesheet_uri(), array(), COLORMAG_THEME_VERSION );
			wp_style_add_data( 'colormag_style', 'rtl', 'replace' );

			// Loads custom style.
			wp_enqueue_style('custom_style', get_theme_file_uri('custom.css'));
//			wp_enqueue_style( 'jquery.bxslider', COLORMAG_CSS_URL . '/jquery.bxslider' . $suffix . '.css', array(), COLORMAG_THEME_VERSION );
			wp_enqueue_style( 'jquery.bxslider', COLORMAG_CSS_URL . '/jquery.bxslider' . '.css', array(), COLORMAG_THEME_VERSION );


			// Load dark css.
			if ( 'dark' === $skin_color ) {
				wp_enqueue_style( 'colormag_dark_style', get_template_directory_uri() . '/dark.css', array(), COLORMAG_THEME_VERSION );
			}

			/**
			 * Inline CSS from customizer.
			 */
			add_filter( 'colormag_dynamic_theme_css', array( 'ColorMag_Dynamic_CSS', 'render_output' ) );

			// Enqueue required Google font for the theme.
			ColorMag_Generate_Fonts::render_fonts();

			// Generate dynamic CSS to add inline styles for the theme.
			$theme_dynamic_css = apply_filters( 'colormag_dynamic_theme_css', '' );

			wp_add_inline_style( $inline_style_handle, $theme_dynamic_css );

			/**
			 * Adds JavaScript to pages with the comment form to support
			 * sites with threaded comments (when in use).
			 */
			if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
				wp_enqueue_script( 'comment-reply' );
			}

			// BxSlider JS.
			wp_enqueue_script( 'colormag-bxslider', COLORMAG_JS_URL . '/jquery.bxslider' . $suffix . '.js', array( 'jquery' ), COLORMAG_THEME_VERSION, true );

			// Sticky Menu.
			if ( 1 == get_theme_mod( 'colormag_enable_sticky_menu', 0 ) ) {
				// Sticky JS enqueue.
				wp_enqueue_script( 'colormag-sticky-menu', COLORMAG_JS_URL . '/sticky/jquery.sticky' . $suffix . '.js', array( 'jquery' ), COLORMAG_THEME_VERSION, true );
			}

			// News Ticker.
			wp_register_script( 'colormag-news-ticker', COLORMAG_JS_URL . '/news-ticker/jquery.newsTicker' . $suffix . '.js', array( 'jquery' ), COLORMAG_THEME_VERSION, true );
			if ( 1 == get_theme_mod( 'colormag_enable_news_ticker', 0 ) ) {
				wp_enqueue_script( 'colormag-news-ticker', COLORMAG_JS_URL . '/news-ticker/jquery.newsTicker' . $suffix . '.js', array( 'jquery' ), COLORMAG_THEME_VERSION, true );
			}

			// MagnificPopup JS.
			wp_register_script( 'colormag-featured-image-popup', COLORMAG_JS_URL . '/magnific-popup/jquery.magnific-popup' . $suffix . '.js', array( 'jquery' ), COLORMAG_THEME_VERSION, true );

			// MagnificPopup CSS.
			wp_register_style( 'colormag-featured-image-popup-css', COLORMAG_JS_URL . '/magnific-popup/magnific-popup' . $suffix . '.css', array(), COLORMAG_THEME_VERSION );

			if ( ( 1 == get_theme_mod( 'colormag_enable_lightbox', 0 ) ) && ( is_single() || is_page() ) ) {
				wp_enqueue_script( 'colormag-featured-image-popup' );
				wp_enqueue_style( 'colormag-featured-image-popup-css' );
			}

			// EasyTabs JS.
			wp_register_script( 'colormag-easy-tabs', COLORMAG_JS_URL . '/easytabs/jquery.easytabs' . $suffix . '.js', array( 'jquery' ), COLORMAG_THEME_VERSION, true );

			// Navigation JS.
			wp_enqueue_script( 'colormag-navigation', COLORMAG_JS_URL . '/navigation' . $suffix . '.js', array( 'jquery' ), COLORMAG_THEME_VERSION, true );

			// FontAwesome CSS.
			wp_enqueue_style( 'colormag-fontawesome', get_template_directory_uri() . '/assets/library/fontawesome/css/font-awesome' . $suffix . '.css', array(), COLORMAG_THEME_VERSION );

			// Weather Icons.
			wp_register_style( 'owfont', get_template_directory_uri() . '/assets/css/owfont-regular' . $suffix . '.css', array(), COLORMAG_THEME_VERSION );

			// FitVids JS.
			wp_enqueue_script( 'colormag-fitvids', COLORMAG_JS_URL . '/fitvids/jquery.fitvids' . $suffix . '.js', array( 'jquery' ), COLORMAG_THEME_VERSION, true );

			// jQuery Video JS.
			wp_register_script( 'jquery-video', COLORMAG_JS_URL . '/jquery.video' . $suffix . '.js', array( 'jquery' ), COLORMAG_THEME_VERSION, true );

			// HTML5Shiv for Lower IE versions.
			wp_enqueue_script( 'html5', COLORMAG_JS_URL . '/html5shiv' . $suffix . '.js', array(), COLORMAG_THEME_VERSION );
			wp_script_add_data( 'html5', 'conditional', 'lte IE 8' );

			// Skip link focus fix JS enqueue.
			wp_enqueue_script( 'colormag-skip-link-focus-fix', COLORMAG_JS_URL . '/skip-link-focus-fix' . $suffix . '.js', array(), COLORMAG_THEME_VERSION, true );

			// Theme custom JS.
			//wp_enqueue_script( 'colormag-custom', COLORMAG_JS_URL . '/colormag-custom' . $suffix . '.js', array( 'jquery' ), COLORMAG_THEME_VERSION, true );

			wp_enqueue_script( 'colormag-custom', COLORMAG_JS_URL . '/colormag-custom'  . '.js', array( 'jquery' ), COLORMAG_THEME_VERSION, true );

		}

		/**
		 * Enqueue block editor styles.
		 *
		 * @since ColorMag 2.4.6
		 */
		public function colormag_block_editor_styles() {

			wp_enqueue_style( 'colormag-editor-googlefonts', '//fonts.googleapis.com/css?family=Open+Sans:400,600', array(), COLORMAG_THEME_VERSION );
			wp_enqueue_style( 'colormag-block-editor-styles', get_template_directory_uri() . '/style-editor-block.css', array(), COLORMAG_THEME_VERSION );
			wp_enqueue_style( 'colormag-block-editor-dark-styles', get_template_directory_uri() . '/dark.css', array(), COLORMAG_THEME_VERSION );
			wp_style_add_data( 'colormag-block-editor-styles', 'rtl', 'replace' );

		}

	}

}

ColorMag_Enqueue_Scripts::get_instance();

/**
 * Action hook to get the required Google fonts for this theme.
 */
function colormag_get_fonts() {

	/**
	 * Header options.
	 */

	/**
	 * Typography options.
	 */
	$breaking_news_typography_default = array(
		'font-family' => 'default',
		'font-weight' => 'regular',
	);

	/**
	 * Enqueue required Google fonts.
	 */
}

add_action( 'colormag_get_fonts', 'colormag_get_fonts' );


/**
 * Filter hook to get the required Google font subsets for this theme.
 */
function colormag_font_subset() {

	$google_font_subsets = array();

	/**
	 * Typography options.
	 */
	return $google_font_subsets;
}

add_filter( 'colormag_font_subset', 'colormag_font_subset' );


/**
 * Enqueue image upload script for use within widgets.
 */
function colormag_image_uploader() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_media();
	wp_enqueue_script( 'colormag-widget-image-upload', COLORMAG_JS_URL . '/image-uploader' . $suffix . '.js', false, COLORMAG_THEME_VERSION, true );

}

add_action( 'admin_enqueue_scripts', 'colormag_image_uploader' );


if ( ! function_exists( 'colormag_darkcolor' ) ) :

	/**
	 * Generate darker color
	 *
	 * @param string $hex   Hex color value.
	 * @param string $steps Steps to change the hex color value for equivalent dark color.
	 *
	 * @return string
	 */
	function colormag_darkcolor( $hex, $steps ) {

		// Steps should be between -255 and 255. Negative = darker, positive = lighter.
		$steps = max( -255, min( 255, $steps ) );

		// Normalize into a six character long hex string.
		$hex = str_replace( '#', '', $hex );
		if ( strlen( $hex ) == 3 ) {
			$hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );
		}

		// Split into three parts: R, G and B.
		$color_parts = str_split( $hex, 2 );
		$return      = '#';

		foreach ( $color_parts as $color ) {

			// Convert to decimal.
			$color = hexdec( $color );

			// Adjust the color.
			$color = max( 0, min( 255, $color + $steps ) );

			$return .= str_pad( dechex( $color ), 2, '0', STR_PAD_LEFT ); // Make two char hex code.

		}

		return $return;

	}

endif;

if ( ! function_exists( 'colormag_parse_css' ) ) :

	/**
	 * Parses CSS.
	 *
	 * @param string|array $default_value Default value.
	 * @param string|array $output_value  Updated value.
	 * @param array        $css_output    Array of CSS.
	 * @param string       $min_media     Min Media breakpoint.
	 * @param string       $max_media     Max Media breakpoint.
	 *
	 * @return string Generated CSS.
	 */
	function colormag_parse_css( $default_value, $output_value, $css_output = array(), $min_media = '', $max_media = '' ) {

		// Return if default value matches.
		if ( $default_value === $output_value ) {
			return;
		}

		$parse_css = '';

		if ( is_array( $css_output ) && count( $css_output ) > 0 ) {

			foreach ( $css_output as $selector => $properties ) {

				if ( null === $properties ) {
					break;
				}

				if ( ! count( $properties ) ) {
					continue;
				}

				$temp_parse_css   = $selector . '{';
				$properties_added = 0;

				foreach ( $properties as $property => $value ) {

					if ( '' === $value ) {
						continue;
					}

					$properties_added ++;
					$temp_parse_css .= $property . ':' . $value . ';';
				}

				$temp_parse_css .= '}';

				if ( $properties_added > 0 ) {
					$parse_css .= $temp_parse_css;
				}
			}

			if ( '' !== $parse_css && ( '' !== $min_media || '' !== $max_media ) ) {

				$media_css       = '@media ';
				$min_media_css   = '';
				$max_media_css   = '';
				$media_separator = '';

				if ( '' !== $min_media ) {
					$min_media_css = 'screen and (min-width:' . $min_media . 'px)';
				}

				if ( '' !== $max_media ) {
					$max_media_css = 'screen and (max-width:' . $max_media . 'px)';
				}

				if ( '' !== $min_media && '' !== $max_media ) {
					$media_separator = ' and ';
				}

				$media_css .= $min_media_css . $media_separator . $max_media_css . '{' . $parse_css . '}';

				return $media_css;
			}
		}

		return $parse_css;
	}

endif;

if ( ! function_exists( 'colormag_parse_background_css' ) ) :

	/**
	 * Returns the background CSS property for dynamic CSS generation.
	 *
	 * @param string|array $default_value Default value.
	 * @param string|array $output_value  Updated value.
	 * @param string       $selector      CSS selector.
	 *
	 * @return string|void Generated CSS for background CSS property.
	 */
	function colormag_parse_background_css( $default_value, $output_value, $selector ) {

		if ( $default_value == $output_value ) {
			return;
		}

		$parse_css  = '';
		$parse_css .= $selector . '{';

		// For background color.
		if ( isset( $output_value['background-color'] ) && ( $output_value['background-color'] != $default_value['background-color'] ) ) {
			$parse_css .= 'background-color:' . $output_value['background-color'] . ';';
		}

		// For background image.
		if ( isset( $output_value['background-image'] ) && ( $output_value['background-image'] != $default_value['background-image'] ) ) {
			$parse_css .= 'background-image:url(' . $output_value['background-image'] . ');';
		}

		// For background position.
		if ( isset( $output_value['background-position'] ) && ( $output_value['background-position'] != $default_value['background-position'] ) ) {
			$parse_css .= 'background-position:' . $output_value['background-position'] . ';';
		}

		// For background size.
		if ( isset( $output_value['background-size'] ) && ( $output_value['background-size'] != $default_value['background-size'] ) ) {
			$parse_css .= 'background-size:' . $output_value['background-size'] . ';';
		}

		// For background attachment.
		if ( isset( $output_value['background-attachment'] ) && ( $output_value['background-attachment'] != $default_value['background-attachment'] ) ) {
			$parse_css .= 'background-attachment:' . $output_value['background-attachment'] . ';';
		}

		// For background repeat.
		if ( isset( $output_value['background-repeat'] ) && ( $output_value['background-repeat'] != $default_value['background-repeat'] ) ) {
			$parse_css .= 'background-repeat:' . $output_value['background-repeat'] . ';';
		}

		$parse_css .= '}';

		return $parse_css;

	}

endif;

if ( ! function_exists( 'colormag_parse_dimension_css' ) ) {
	/**
	 * Returns the background CSS property for dynamic CSS generation.
	 *
	 * @param string|array $default_value Default value.
	 * @param string|array $output_value  Updated value.
	 * @param string       $selector      CSS selector.
	 * @param string       $property      CSS property.
	 *
	 * @return string|void Generated CSS for dimension CSS.
	 */
	function colormag_parse_dimension_css( $default_value, $output_value, $selector, $property ) {

		if ( $default_value === $output_value ) {
			return;
		}

		$parse_css = $selector . '{';

		$unit = isset( $output_value['unit'] ) ? $output_value['unit'] : ( isset( $default_value['unit'] ) ? $default_value['unit'] : 'px' );

		if ( isset( $output_value['top'] ) && ! empty( $output_value['top'] ) && ( $output_value['top'] !== $default_value['top'] ) ) {
			$parse_css .= $property . '-top:' . $output_value['top'] . $unit . ';';
		}

		if ( isset( $output_value['top'] ) && ! empty( $output_value['top'] ) && ( $output_value['right'] !== $default_value['right'] ) ) {
			$parse_css .= $property . '-right:' . $output_value['right'] . $unit . ';';
		}

		if ( isset( $output_value['bottom'] ) && ! empty( $output_value['bottom'] ) && ( $output_value['bottom'] !== $default_value['bottom'] ) ) {
			$parse_css .= $property . '-bottom:' . $output_value['bottom'] . $unit . ';';
		}

		if ( isset( $output_value['left'] ) && ! empty( $output_value['left'] ) && ( $output_value['left'] !== $default_value['left'] ) ) {
			$parse_css .= $property . '-left:' . $output_value['left'] . $unit . ';';
		}

		$parse_css .= '}';

		return $parse_css;
	}
}

if ( ! function_exists( 'colormag_parse_border_css' ) ) {
	/**
	 * Returns the background CSS property for dynamic CSS generation.
	 *
	 * @param string|array $default_value Default value.
	 * @param string|array $output_value  Updated value.
	 * @param string       $selector      CSS selector.
	 * @param string       $property      CSS property.
	 *
	 * @return string|void Generated CSS for border CSS.
	 */
	function colormag_parse_border_css( $default_value, $output_value, $selector, $property ) {
		if ( $default_value === $output_value ) {
			return;
		}
		$parse_css = $selector . '{';
		if ( isset( $output_value ) && ! empty( $output_value ) && ( $output_value !== $default_value ) ) {
			if ( $property == 'radius' || $property == 'width' ) {
				$parse_css .= 'border-' . $property . ':' . $output_value . 'px;';
			} else {
				$parse_css .= 'border-' . $property . ':' . $output_value . ';';
			}
		}
		$parse_css .= '}';
		return $parse_css;
	}
}

if ( ! function_exists( 'colormag_parse_typography_css' ) ) :

	/**
	 * Returns the background CSS property for dynamic CSS generation.
	 *
	 * @param string|array $default_value Default value.
	 * @param string|array $output_value  Updated value.
	 * @param string       $selector      CSS selector.
	 * @param array        $devices       Devices for breakpoints.
	 *
	 * @return string|void Generated CSS for typography CSS.
	 */
	function colormag_parse_typography_css( $default_value, $output_value, $selector, $devices = array() ) {

		if ( $default_value === $output_value ) {
			return;
		}

		$parse_css = $selector . '{';

		// For font family.
		if ( isset( $output_value['font-family'] ) && ! empty( $output_value['font-family'] ) && ( $output_value['font-family'] !== $default_value['font-family'] ) ) {
			$parse_css .= 'font-family:' . $output_value['font-family'] . ';';
		}

		// For font style.
		if ( isset( $output_value['font-style'] ) && ! empty( $output_value['font-style'] ) && ( $output_value['font-style'] !== $default_value['font-style'] ) ) {
			$parse_css .= 'font-style:' . $output_value['font-style'] . ';';
		}

		// For text transform.
		if ( isset( $output_value['text-transform'] ) && ! empty( $output_value['text-transform'] ) && ( $output_value['text-transform'] !== $default_value['text-transform'] ) ) {
			$parse_css .= 'text-transform:' . $output_value['text-transform'] . ';';
		}

		// For text decoration.
		if ( isset( $output_value['text-decoration'] ) && ! empty( $output_value['text-decoration'] ) && ( $output_value['text-decoration'] !== $default_value['text-decoration'] ) ) {
			$parse_css .= 'text-decoration:' . $output_value['text-decoration'] . ';';
		}

		// For font weight.
		if ( isset( $output_value['font-weight'] ) && ! empty( $output_value['font-weight'] ) && ( $output_value['font-weight'] !== $default_value['font-weight'] ) ) {
			$font_weight_value = $output_value['font-weight'];

			if ( 'italic' === $font_weight_value || 'regular' === $font_weight_value ) {
				$parse_css .= 'font-weight:' . 400 . ';';
			} else {
				$parse_css .= 'font-weight:' . str_replace( 'italic', '', $font_weight_value ) . ';';
			}
		}

		// For font size on desktop.
		$font_size_unit = isset( $output_value['font-size']['desktop']['unit'] ) ? $output_value['font-size']['desktop']['unit'] : 'px';

		if ( isset( $output_value['font-size']['desktop']['size'] ) && ! empty( $output_value['font-size']['desktop']['size'] ) && ( $output_value['font-size']['desktop']['size'] !== $default_value['font-size']['desktop']['size'] ) ) {
			$parse_css .= 'font-size:' . $output_value['font-size']['desktop']['size'] . $font_size_unit . ';';
		}

		// For line height on desktop.
		$line_height_unit_value = isset( $output_value['line-height']['desktop']['unit'] ) ? $output_value['line-height']['desktop']['unit'] : 'px';
		$line_height_unit       = ( '-' !== $line_height_unit_value ) ? $line_height_unit_value : '';

		if ( isset( $output_value['line-height']['desktop']['size'] ) && ! empty( $output_value['line-height']['desktop']['size'] ) && ( $output_value['line-height']['desktop']['size'] !== $default_value['line-height']['desktop']['size'] ) ) {
			$parse_css .= 'line-height:' . $output_value['line-height']['desktop']['size'] . $line_height_unit . ';';
		}

		// For letter spacing on desktop.
		$letter_spacing_unit = isset( $output_value['letter-spacing']['desktop']['unit'] ) ? $output_value['letter-spacing']['desktop']['unit'] : 'px';

		if ( isset( $output_value['letter-spacing']['desktop']['size'] ) && ! empty( $output_value['letter-spacing']['desktop']['size'] ) && ( $output_value['letter-spacing']['desktop']['size'] !== $default_value['letter-spacing']['desktop']['size'] ) ) {
			$parse_css .= 'letter-spacing:' . $output_value['letter-spacing']['desktop']['size'] . $letter_spacing_unit . ';';
		}

		$parse_css .= '}';

		// For responsive devices.
		if ( is_array( $devices ) ) {

			foreach ( $devices as $device => $size ) {

				// For tablet devices.
				if ( 'tablet' === $device && $size ) {
					if ( isset( $output_value['font-size']['tablet']['size'] ) && ! empty( $output_value['font-size']['tablet']['size'] ) && $output_value['font-size']['tablet']['size'] !== $default_value['font-size']['tablet']['size'] ) {

						$font_size_tablet_unit = $output_value['font-size']['tablet']['unit'] ? $output_value['font-size']['tablet']['unit'] : 'px';

						$parse_css .= '@media(max-width:' . $size . 'px){';
						$parse_css .= $selector . '{';
						$parse_css .= 'font-size:' . $output_value['font-size']['tablet']['size'] . $font_size_tablet_unit . ';';
						$parse_css .= '}';
						$parse_css .= '}';
					}

					if ( isset( $output_value['line-height']['tablet']['size'] ) && ! empty( $output_value['line-height']['tablet']['size'] ) && $output_value['line-height']['tablet']['size'] !== $default_value['line-height']['tablet']['size'] ) {

						$line_height_tablet_unit_value = $output_value['line-height']['tablet']['unit'] ? $output_value['line-height']['tablet']['unit'] : '';
						$line_height_tablet_unit       = ( '-' !== $line_height_tablet_unit_value ) ? $line_height_tablet_unit_value : '';

						$parse_css .= '@media(max-width:' . $size . 'px){';
						$parse_css .= $selector . '{';
						$parse_css .= 'line-height:' . $output_value['line-height']['tablet']['size'] . $line_height_tablet_unit . ';';
						$parse_css .= '}';
						$parse_css .= '}';
					}

					if ( isset( $output_value['letter-spacing']['tablet']['size'] ) && ! empty( $output_value['letter-spacing']['tablet']['size'] ) && $output_value['letter-spacing']['tablet']['size'] !== $default_value['letter-spacing']['tablet']['size'] ) {

						$letter_spacing_tablet_unit = $output_value['letter-spacing']['tablet']['unit'] ? $output_value['letter-spacing']['tablet']['unit'] : 'px';

						$parse_css .= '@media(max-width:' . $size . 'px){';
						$parse_css .= $selector . '{';
						$parse_css .= 'letter-spacing:' . $output_value['letter-spacing']['tablet']['size'] . $letter_spacing_tablet_unit . ';';
						$parse_css .= '}';
						$parse_css .= '}';
					}
				}

				// For mobile devices.
				if ( 'mobile' === $device && $size ) {
					if ( isset( $output_value['font-size']['mobile']['size'] ) && ! empty( $output_value['font-size']['mobile']['size'] ) && $output_value['font-size']['mobile']['size'] !== $default_value['font-size']['mobile']['size'] ) {

						$font_size_mobile_unit = $output_value['font-size']['mobile']['unit'] ? $output_value['font-size']['mobile']['unit'] : 'px';

						$parse_css .= '@media(max-width:' . $size . 'px){';
						$parse_css .= $selector . '{';
						$parse_css .= 'font-size:' . $output_value['font-size']['mobile']['size'] . $font_size_mobile_unit . ';';
						$parse_css .= '}';
						$parse_css .= '}';
					}

					if ( isset( $output_value['line-height']['mobile']['size'] ) && ! empty( $output_value['line-height']['mobile']['size'] ) && $output_value['line-height']['mobile']['size'] !== $default_value['line-height']['mobile']['size'] ) {

						$line_height_mobile_unit_value = $output_value['line-height']['mobile']['unit'] ? $output_value['line-height']['mobile']['unit'] : '';
						$line_height_mobile_unit       = ( '-' !== $line_height_mobile_unit_value ) ? $line_height_mobile_unit_value : '';

						$parse_css .= '@media(max-width:' . $size . 'px){';
						$parse_css .= $selector . '{';
						$parse_css .= 'line-height:' . $output_value['line-height']['mobile']['size'] . $line_height_mobile_unit . ';';
						$parse_css .= '}';
						$parse_css .= '}';
					}

					if ( isset( $output_value['letter-spacing']['mobile']['size'] ) && ! empty( $output_value['letter-spacing']['mobile']['size'] ) && $output_value['letter-spacing']['mobile']['size'] !== $default_value['letter-spacing']['mobile']['size'] ) {

						$letter_spacing_mobile_unit = $output_value['letter-spacing']['mobile']['unit'] ? $output_value['letter-spacing']['mobile']['unit'] : 'px';

						$parse_css .= '@media(max-width:' . $size . 'px){';
						$parse_css .= $selector . '{';
						$parse_css .= 'letter-spacing:' . $output_value['letter-spacing']['mobile']['size'] . $letter_spacing_mobile_unit . ';';
						$parse_css .= '}';
						$parse_css .= '}';
					}
				}
			}
		}

		return $parse_css;
	}

endif;

if ( ! function_exists( 'colormag_parse_slider_css' ) ) :

	/**
	 * Returns the background CSS property for dynamic CSS generation.
	 *
	 * @param string|array $default_value Default value.
	 * @param string|array $output_value Updated value.
	 * @param string $selector CSS selector.
	 * @param string $property CSS property.
	 *
	 * @return string|void Generated CSS for dimension CSS.
	 */
	function colormag_parse_slider_css( $default_value, $output_value, $selector, $property ) {

		if ( $default_value === $output_value ) {
			return;
		}

		$parse_css = '';

		if ( isset( $output_value['size'] ) ) {

			$parse_css = $selector . '{';

			$unit       = isset( $output_value['unit'] ) ? $output_value['unit'] : ( isset( $default_value['unit'] ) ? $default_value['unit'] : 'px' );
			$parse_css .= $property . ':' . $output_value['size'] . $unit . ';';

			$parse_css .= '}';
		}

		return $parse_css;
	}

	endif;
