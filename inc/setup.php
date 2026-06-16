<?php
/**
 * Configuration du thème : supports, menus, tailles d'images.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

/**
 * Déclare les fonctionnalités supportées par le thème.
 */
function wpis_theme_setup() {
	load_theme_textdomain( 'hello-immosync', HELLO_IMMOSYNC_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Tailles d'images dédiées aux biens immobiliers.
	add_image_size( 'wpis-card', 800, 600, true );      // Carte de listing.
	add_image_size( 'wpis-card-2x', 1200, 900, true );  // Carte (écrans HiDPI).
	add_image_size( 'wpis-hero', 1920, 1080, true );    // Hero plein écran.
	add_image_size( 'wpis-gallery', 1600, 1200, false ); // Galerie fiche bien.
	add_image_size( 'wpis-thumb', 400, 300, true );     // Vignette (biens similaires).

	register_nav_menus(
		array(
			'primary' => __( 'Menu principal', 'hello-immosync' ),
			'footer'  => __( 'Menu pied de page', 'hello-immosync' ),
		)
	);
}
add_action( 'after_setup_theme', 'wpis_theme_setup' );

/**
 * Libellés lisibles pour les tailles d'images personnalisées (admin).
 *
 * @param array $sizes Tailles existantes.
 * @return array
 */
function wpis_custom_image_sizes( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'wpis-card'    => __( 'Carte bien', 'hello-immosync' ),
			'wpis-hero'    => __( 'Hero bien', 'hello-immosync' ),
			'wpis-gallery' => __( 'Galerie bien', 'hello-immosync' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'wpis_custom_image_sizes' );
