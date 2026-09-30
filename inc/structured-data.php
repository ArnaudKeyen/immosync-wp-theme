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

	// Réglage thème : désactivable si un plugin SEO gère déjà le schéma des biens.
	if ( function_exists( 'get_field' ) ) {
		$wpis_schema_on = get_field( 'option_schema_estates', 'option' );
		if ( null !== $wpis_schema_on && ! $wpis_schema_on ) {
			return;
		}
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

	// Surface habitable (areas.total — areas.living n'est que le séjour).
	$floor = wpis_get_field( 'wpis_areas_total', $post_id, '' );
	if ( is_numeric( $floor ) && (float) $floor > 0 ) {
		$data['floorSize'] = array(
			'@type'    => 'QuantitativeValue',
			'value'    => (float) $floor,
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
			$availability = array(
				'sold'    => 'https://schema.org/SoldOut',
				'pending' => 'https://schema.org/LimitedAvailability',
			);
			$level        = wpis_get_status_level( $post_id );

			$data['offers'] = array(
				'@type'         => 'Offer',
				'price'         => $price,
				'priceCurrency' => 'EUR',
				'availability'  => isset( $availability[ $level ] ) ? $availability[ $level ] : 'https://schema.org/InStock',
			);
		}
	}

	echo "\n<script type=\"application/ld+json\">" . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}
add_action( 'wp_head', 'wpis_output_estate_jsonld' );

/**
 * JSON-LD RealEstateAgent de l'agence (nom, adresse, coordonnées, géolocalisation).
 *
 * Émis à la demande par template-parts/global/agency-info.php (argument
 * « schema »), typiquement sur la page Contact uniquement. Désactivable via le
 * filtre wpis_agency_jsonld (ex. si le plugin SEO décrit déjà l'agence).
 *
 * @param array $agency Coordonnées (wpis_get_agency_info()).
 * @return void
 */
function wpis_output_agency_jsonld( $agency ) {
	$data = array(
		'@context' => 'https://schema.org',
		'@type'    => 'RealEstateAgent',
		'name'     => $agency['name'],
		'url'      => home_url( '/' ),
	);

	if ( $agency['address'] ) {
		$lines   = $agency['address'];
		$street  = array_shift( $lines );
		$address = array(
			'@type'          => 'PostalAddress',
			'streetAddress'  => $street,
			'addressCountry' => 'BE',
		);
		// Dernière ligne « 4130 Esneux » → code postal + localité.
		$locality = $lines ? end( $lines ) : '';
		if ( preg_match( '/^(\d{4,5})\s+(.+)$/u', $locality, $m ) ) {
			$address['postalCode']      = $m[1];
			$address['addressLocality'] = $m[2];
		} elseif ( '' !== $locality ) {
			$address['addressLocality'] = $locality;
		}
		$data['address'] = $address;
	}
	if ( '' !== $agency['phone'] ) {
		$data['telephone'] = $agency['phone'];
	}
	if ( '' !== $agency['email'] ) {
		$data['email'] = $agency['email'];
	}
	if ( null !== $agency['lat'] && null !== $agency['lng'] ) {
		$data['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => $agency['lat'],
			'longitude' => $agency['lng'],
		);
	}

	/**
	 * Données JSON-LD de l'agence ; renvoyer false pour ne rien émettre.
	 *
	 * @param array|false $data   Données.
	 * @param array       $agency Coordonnées.
	 */
	$data = apply_filters( 'wpis_agency_jsonld', $data, $agency );
	if ( ! $data ) {
		return;
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
