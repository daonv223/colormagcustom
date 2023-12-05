<?php

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'daong_custom_header' ) ) :

	/**
	 * Before header main.
	 */
	function daong_custom_header() {
		?>
		<link
			rel="stylesheet"
			href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
		/>
		<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
		<?php
	}
endif;

add_action( 'wp_head', 'daong_custom_header' );