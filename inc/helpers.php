<?php
/**
 * Helpers de formatage génériques (préfixe wpis_).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

/**
 * Formate un nombre selon la locale FR (séparateur de milliers par espace).
 *
 * @param mixed $value    Valeur numérique.
 * @param int   $decimals Décimales.
 * @return string
 */
function wpis_format_number( $value, $decimals = 0 ) {
	if ( '' === $value || null === $value || ! is_numeric( $value ) ) {
		return '';
	}
	return number_format_i18n( (float) $value, $decimals );
}

/**
 * Formate une surface ("236 m²"). Retourne '' si vide ou nulle.
 *
 * @param mixed  $value Surface.
 * @param string $unit  Unité.
 * @return string
 */
function wpis_format_area( $value, $unit = 'm²' ) {
	if ( ! is_numeric( $value ) || (float) $value <= 0 ) {
		return '';
	}
	$formatted = wpis_format_number( $value );
	return trim( $formatted . ' ' . $unit );
}

/**
 * Bibliothèque d'icônes SVG inline (trait = couleur courante).
 *
 * @param string $name    Nom de l'icône.
 * @param string $classes Classes CSS.
 * @return string Markup SVG (vide si inconnu).
 */
function wpis_icon( $name, $classes = 'w-5 h-5' ) {
	$paths = array(
		'bed'      => '<path d="M2 17v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5M2 17h20M2 17v3M22 17v3M6 10V8a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2"/>',
		'bath'     => '<path d="M4 12V6a2 2 0 0 1 2-2 2 2 0 0 1 2 2M3 12h18v2a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-2ZM6 18l-1 3M18 18l1 3"/>',
		'area'     => '<path d="M3 8V3h5M21 8V3h-5M3 16v5h5M21 16v5h-5"/>',
		'land'     => '<path d="M3 21h18M5 21V8l7-5 7 5v13M9 21v-6h6v6"/>',
		'car'      => '<path d="M5 17a2 2 0 1 0 0-.01M19 17a2 2 0 1 0 0-.01M3 13l2-6h14l2 6M3 13h18v4H3v-4Z"/>',
		'location' => '<path d="M12 21s-7-6.3-7-11a7 7 0 0 1 14 0c0 4.7-7 11-7 11Z"/><circle cx="12" cy="10" r="2.5"/>',
		'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 9h18M8 3v4M16 3v4"/>',
		'energy'   => '<path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"/>',
		'video'    => '<path d="M3 6a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6ZM16 9l5-3v12l-5-3"/>',
		'cube'     => '<path d="M12 2 3 7v10l9 5 9-5V7l-9-5ZM3 7l9 5 9-5M12 12v10"/>',
		'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'phone'    => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3-8.6A2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/>',
		'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
		'compass'  => '<circle cx="12" cy="12" r="9"/><path d="m15 9-2 5-5 2 2-5 5-2Z"/>',
	);

	if ( empty( $paths[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="%s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
		esc_attr( $classes ),
		$paths[ $name ]
	);
}
