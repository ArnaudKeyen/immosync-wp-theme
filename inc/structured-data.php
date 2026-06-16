<?php
/**
 * Données structurées JSON-LD pour les fiches biens (SEO).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

/**
 * Injecte le JSON-LD d'un bien dans le <head> de la fiche.
 *
 * @return void
 */
function wpis_output_estate_jsonld() {
	if ( ! is_singular( 'wpis_estates' ) ) {
		return;
	}

	$post_id = get_the_ID();

	$data = array(
		'@context' => 'https://schema.org',
		'@type'    => 'Residence',
		'name'     => wpis_get_title( $post_id ),
		'url'      => get_permalink( $post_id ),
	);

	$description = wpis_get_excerpt( $post_id, 55 );
	if ( '' !== $description ) {
		$data['description'] = $description;
	}

	// Images de la galerie.
	$images = array();
	foreach ( wpis_get_gallery( $post_id ) as $image_id ) {
		$src = wp_get_attachment_image_url( $image_id, 'large' );
		if ( $src ) {
			$images[] = $src;
		}
	}
	if ( $images ) {
		$data['image'] = array_slice( $images, 0, 8 );
	}

	// Adresse.
	$address = array_filter(
		array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => trim( wpis_get_field( 'wpis_address_street', $post_id, '' ) . ' ' . wpis_get_field( 'wpis_address_number', $post_id, '' ) ),
			'postalCode'      => wpis_get_field( 'wpis_address_zip', $post_id, '' ),
			'addressLocality' => wpis_get_field( 'wpis_address_city', $post_id, '' ),
			'addressCountry'  => wpis_get_field( 'wpis_address_country', $post_id, '' ),
		)
	);
	if ( count( $address ) > 1 ) {
		$data['address'] = $address;
	}

	// Géolocalisation.
	$coords = wpis_get_map_coords( $post_id );
	if ( $coords ) {
		$data['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => $coords['lat'],
			'longitude' => $coords['lng'],
		);
	}

	// Surface habitable.
	$living = wpis_get_field( 'wpis_areas_living', $post_id, '' );
	if ( is_numeric( $living ) && (float) $living > 0 ) {
		$data['floorSize'] = array(
			'@type'    => 'QuantitativeValue',
			'value'    => (float) $living,
			'unitCode' => 'MTK',
		);
	}

	// Chambres.
	$bedrooms = wpis_get_field( 'wpis_configuration_bedrooms', $post_id, '' );
	if ( is_numeric( $bedrooms ) && (int) $bedrooms > 0 ) {
		$data['numberOfRooms'] = (int) $bedrooms;
	}

	// Offre (prix), si visible.
	if ( ! wpis_is_price_hidden( $post_id ) ) {
		$price = wpis_get_price_raw( $post_id );
		if ( $price > 0 ) {
			$data['offers'] = array(
				'@type'         => 'Offer',
				'price'         => $price,
				'priceCurrency' => 'EUR',
				'availability'  => wpis_is_sold( $post_id ) ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock',
			);
		}
	}

	echo "\n<script type=\"application/ld+json\">" . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}
add_action( 'wp_head', 'wpis_output_estate_jsonld' );
