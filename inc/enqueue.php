<?php
/**
 * Chargement des feuilles de style et scripts.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL des polices Google par défaut (premium neutre).
 * Le thème enfant peut filtrer ou remplacer cette URL.
 *
 * @return string
 */
function wpis_fonts_url() {
	$url = 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap';
	return apply_filters( 'wpis_fonts_url', $url );
}

/**
 * Précharge les domaines de polices pour la performance.
 */
function wpis_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.googleapis.com' );
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'wpis_resource_hints', 10, 2 );

/**
 * Enqueue des assets front-end.
 */
function wpis_enqueue_assets() {
	// Feuille Tailwind compilée (assets/css/main.css). Versionnée par mtime pour le cache-busting.
	$css_path = HELLO_IMMOSYNC_DIR . '/assets/css/main.css';
	$css_ver  = file_exists( $css_path ) ? (string) filemtime( $css_path ) : HELLO_IMMOSYNC_VERSION;
	wp_enqueue_style(
		'hello-immosync',
		HELLO_IMMOSYNC_URI . '/assets/css/main.css',
		array(),
		$css_ver
	);

	// Polices.
	wp_enqueue_style( 'hello-immosync-fonts', wpis_fonts_url(), array(), null );

	// Swiper (galerie mosaïque + modal de la fiche bien) — chargé uniquement là où utile.
	$wpis_main_deps = array();
	if ( is_singular( 'wpis_estates' ) ) {
		wp_enqueue_style(
			'swiper',
			HELLO_IMMOSYNC_URI . '/assets/vendor/swiper/swiper-bundle.min.css',
			array(),
			'11.2.10'
		);
		wp_enqueue_script(
			'swiper',
			HELLO_IMMOSYNC_URI . '/assets/vendor/swiper/swiper-bundle.min.js',
			array(),
			'11.2.10',
			true
		);
		$wpis_main_deps[] = 'swiper';
	}

	// JS du thème (menu mobile, galerie, UI de recherche).
	$js_path = HELLO_IMMOSYNC_DIR . '/assets/js/main.js';
	$js_ver  = file_exists( $js_path ) ? (string) filemtime( $js_path ) : HELLO_IMMOSYNC_VERSION;
	wp_enqueue_script(
		'hello-immosync',
		HELLO_IMMOSYNC_URI . '/assets/js/main.js',
		$wpis_main_deps,
		$js_ver,
		true
	);

	// Titres de section injectés dans les formulaires ImmoSync (traduisibles).
	$wpis_form_sections = apply_filters(
		'wpis_form_sections',
		array(
			array(
				'target' => '.form-group__gender',
				'title'  => __( 'Vos coordonnées', 'hello-immosync' ),
			),
			array(
				'target' => '.form-group__addressStreet',
				'title'  => __( 'Votre bien', 'hello-immosync' ),
			),
			array(
				'target' => '.form-group__message',
				'title'  => __( 'Votre message', 'hello-immosync' ),
			),
		)
	);

	wp_localize_script(
		'hello-immosync',
		'WPISFormData',
		array( 'sections' => $wpis_form_sections )
	);
}
add_action( 'wp_enqueue_scripts', 'wpis_enqueue_assets' );
