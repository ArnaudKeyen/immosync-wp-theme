<?php
/**
 * Accesseurs de champs ImmoSync (WPIS).
 *
 * Centralise la récupération et le formatage des meta fields pour éviter de
 * répéter get_post_meta() dans les templates. S'appuie sur la classe du plugin
 * \WPIS\EstateProperties lorsqu'elle est disponible.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

/**
 * Récupère un champ WPIS brut, de façon sécurisée.
 *
 * @param string   $key     Clé meta (ex. wpis_finance_price).
 * @param int|null $post_id ID du bien (courant par défaut).
 * @param mixed    $default Valeur de repli si vide.
 * @return mixed
 */
function wpis_get_field( $key, $post_id = null, $default = '' ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id ) {
		return $default;
	}
	$value = get_post_meta( $post_id, $key, true );
	if ( '' === $value || null === $value || false === $value ) {
		return $default;
	}
	return is_string( $value ) ? trim( $value ) : $value;
}

/**
 * Récupère une valeur WPIS formatée via le plugin si possible, sinon brute.
 *
 * @param string   $meta    Clé meta.
 * @param int|null $post_id ID du bien.
 * @param string   $default Repli.
 * @return string
 */
function wpis_get_formatted( $meta, $post_id = null, $default = '' ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id ) {
		return $default;
	}

	if ( is_callable( array( '\WPIS\EstateProperties', 'getFormattedValue' ) ) ) {
		$formatted = (string) \WPIS\EstateProperties::getFormattedValue( $post_id, $meta );
		if ( '' !== trim( $formatted ) ) {
			return $formatted;
		}
	}

	$raw = wpis_get_field( $meta, $post_id, $default );
	return is_scalar( $raw ) ? (string) $raw : $default;
}

/**
 * Indique si le prix doit rester masqué.
 *
 * @param int|null $post_id ID du bien.
 * @return bool
 */
function wpis_is_price_hidden( $post_id = null ) {
	$hidden = wpis_get_field( 'wpis_finance_hidden', $post_id, '' );
	return in_array( $hidden, array( '1', 1, true, 'true', 'yes' ), true );
}

/**
 * Prix formaté ("560 500 €") ou mention "Prix sur demande".
 *
 * @param int|null $post_id ID du bien.
 * @return string
 */
function wpis_get_price( $post_id = null ) {
	if ( wpis_is_price_hidden( $post_id ) ) {
		return apply_filters( 'wpis_price_on_request_label', __( 'Prix sur demande', 'hello-immosync' ) );
	}
	$price = wpis_get_field( 'wpis_finance_price', $post_id, '' );
	if ( '' === $price || ! is_numeric( $price ) || (float) $price <= 0 ) {
		return apply_filters( 'wpis_price_on_request_label', __( 'Prix sur demande', 'hello-immosync' ) );
	}
	return wpis_get_formatted( 'wpis_finance_price', $post_id );
}

/**
 * Prix brut (entier) pour les requêtes / données structurées.
 *
 * @param int|null $post_id ID du bien.
 * @return int
 */
function wpis_get_price_raw( $post_id = null ) {
	$price = wpis_get_field( 'wpis_finance_price', $post_id, 0 );
	return is_numeric( $price ) ? (int) $price : 0;
}

/**
 * Devise ISO/symbole.
 *
 * @param int|null $post_id ID du bien.
 * @return string
 */
function wpis_get_currency( $post_id = null ) {
	return (string) wpis_get_field( 'wpis_finance_currency', $post_id, '€' );
}

/**
 * Catégorie du bien (sous-catégorie en repli).
 *
 * @param int|null $post_id ID du bien.
 * @return string
 */
function wpis_get_category( $post_id = null ) {
	$cat = wpis_get_field( 'wpis_category_label', $post_id, '' );
	if ( '' === $cat ) {
		$cat = wpis_get_field( 'wpis_subcategory_label', $post_id, '' );
	}
	return $cat;
}

/**
 * Type d'opération (À vendre / À louer).
 *
 * @param int|null $post_id ID du bien.
 * @return string
 */
function wpis_get_purpose( $post_id = null ) {
	return wpis_get_field( 'wpis_purpose_label', $post_id, '' );
}

/**
 * Statut commercial ("Vendu", "Disponible"…).
 *
 * @param int|null $post_id ID du bien.
 * @return string
 */
function wpis_get_status( $post_id = null ) {
	return wpis_get_field( 'wpis_status_label', $post_id, '' );
}

/**
 * Le bien est-il vendu / loué (indisponible) ?
 *
 * @param int|null $post_id ID du bien.
 * @return bool
 */
function wpis_is_sold( $post_id = null ) {
	$status = mb_strtolower( wpis_get_status( $post_id ) );
	$sold_statuses = apply_filters(
		'wpis_sold_statuses',
		array( 'vendu', 'vendue', 'loué', 'loue', 'louée', 'sold', 'rented', 'sous compromis', 'sous option', 'vendu sous conditions' )
	);
	foreach ( $sold_statuses as $needle ) {
		if ( '' !== $status && false !== mb_strpos( $status, $needle ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Référence du bien.
 *
 * @param int|null $post_id ID du bien.
 * @return string
 */
function wpis_get_reference( $post_id = null ) {
	return wpis_get_field( 'wpis_reference', $post_id, '' );
}

/**
 * Localisation lisible (ville, code postal).
 *
 * @param int|null $post_id ID du bien.
 * @param bool     $with_street Inclure rue et numéro.
 * @return string
 */
function wpis_get_location( $post_id = null, $with_street = false ) {
	$city   = wpis_get_field( 'wpis_address_city', $post_id, '' );
	$zip    = wpis_get_field( 'wpis_address_zip', $post_id, '' );
	$street = wpis_get_field( 'wpis_address_street', $post_id, '' );
	$number = wpis_get_field( 'wpis_address_number', $post_id, '' );

	$parts = array();
	if ( $with_street && '' !== $street ) {
		$parts[] = trim( $street . ' ' . $number );
	}
	$locality = trim( $zip . ' ' . $city );
	if ( '' !== $locality ) {
		$parts[] = $locality;
	}
	return implode( ', ', array_filter( $parts ) );
}

/**
 * Coordonnées GPS ou null.
 *
 * @param int|null $post_id ID du bien.
 * @return array{lat:float,lng:float}|null
 */
function wpis_get_map_coords( $post_id = null ) {
	$lat = wpis_get_field( 'wpis_address_latitude', $post_id, '' );
	$lng = wpis_get_field( 'wpis_address_longitude', $post_id, '' );
	if ( is_numeric( $lat ) && is_numeric( $lng ) && (float) $lat && (float) $lng ) {
		return array(
			'lat' => (float) $lat,
			'lng' => (float) $lng,
		);
	}
	return null;
}

/**
 * Caractéristiques clés sous forme de liste {key, label, value, icon}.
 * Les entrées vides/nulles sont ignorées.
 *
 * @param int|null $post_id ID du bien.
 * @param bool     $compact Jeu réduit (cartes) ou complet (fiche).
 * @return array<int,array{key:string,label:string,value:string,icon:string}>
 */
function wpis_get_estate_features( $post_id = null, $compact = false ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	$defs = array(
		array( 'wpis_configuration_bedrooms', __( 'Chambres', 'hello-immosync' ), 'bed', 'number' ),
		array( 'wpis_configuration_bathrooms', __( 'Salles de bain', 'hello-immosync' ), 'bath', 'number' ),
		array( 'wpis_areas_living', __( 'Surface habitable', 'hello-immosync' ), 'area', 'area' ),
		array( 'wpis_areas_total', __( 'Surface totale', 'hello-immosync' ), 'area', 'area' ),
		array( 'wpis_areas_ground', __( 'Terrain', 'hello-immosync' ), 'land', 'area' ),
		array( 'wpis_configuration_garages', __( 'Garages', 'hello-immosync' ), 'car', 'number' ),
		array( 'wpis_configuration_terraces', __( 'Terrasses', 'hello-immosync' ), 'compass', 'number' ),
	);

	if ( $compact ) {
		$defs = array(
			array( 'wpis_configuration_bedrooms', __( 'Chambres', 'hello-immosync' ), 'bed', 'number' ),
			array( 'wpis_areas_living', __( 'Surface', 'hello-immosync' ), 'area', 'area' ),
			array( 'wpis_areas_ground', __( 'Terrain', 'hello-immosync' ), 'land', 'area' ),
		);
	}

	$features = array();
	foreach ( $defs as $def ) {
		list( $key, $label, $icon, $type ) = $def;
		$raw = wpis_get_field( $key, $post_id, '' );
		if ( '' === $raw || ( is_numeric( $raw ) && (float) $raw <= 0 ) ) {
			continue;
		}
		$value = 'area' === $type ? wpis_format_area( $raw ) : wpis_format_number( $raw );
		if ( '' === $value ) {
			continue;
		}
		$features[] = array(
			'key'   => $key,
			'label' => $label,
			'value' => $value,
			'icon'  => $icon,
		);
	}
	return $features;
}

/**
 * IDs des images de la galerie : image à la une en tête + pièces jointes.
 *
 * @param int|null $post_id ID du bien.
 * @return int[]
 */
function wpis_get_gallery( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id ) {
		return array();
	}

	$ids   = array();
	$thumb = (int) get_post_thumbnail_id( $post_id );
	if ( $thumb ) {
		$ids[] = $thumb;
	}

	$media = get_attached_media( 'image', $post_id );
	foreach ( $media as $item ) {
		$ids[] = (int) $item->ID;
	}

	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Le bien a-t-il une visite virtuelle ?
 *
 * @param int|null $post_id ID du bien.
 * @return bool
 */
function wpis_has_virtual_visit( $post_id = null ) {
	return '' !== wpis_get_field( 'wpis_links_virtualVisit', $post_id, '' );
}

/**
 * Le bien a-t-il une vidéo ?
 *
 * @param int|null $post_id ID du bien.
 * @return bool
 */
function wpis_has_video( $post_id = null ) {
	return '' !== wpis_get_field( 'wpis_links_video', $post_id, '' );
}

/**
 * Liens médias du bien.
 *
 * @param int|null $post_id ID du bien.
 * @return array<string,string>
 */
function wpis_get_links( $post_id = null ) {
	return array_filter(
		array(
			'virtualVisit' => wpis_get_field( 'wpis_links_virtualVisit', $post_id, '' ),
			'video'        => wpis_get_field( 'wpis_links_video', $post_id, '' ),
			'model'        => wpis_get_field( 'wpis_links_model', $post_id, '' ),
			'appointment'  => wpis_get_field( 'wpis_links_appointment', $post_id, '' ),
		)
	);
}

/**
 * Données PEB / énergie.
 *
 * @param int|null $post_id ID du bien.
 * @return array{label:string,value:string,heating:string}
 */
function wpis_get_energy( $post_id = null ) {
	return array(
		'label'   => wpis_get_field( 'wpis_energy_epcLabel', $post_id, '' ),
		'value'   => wpis_get_field( 'wpis_energy_epcValue', $post_id, '' ),
		'heating' => wpis_get_field( 'wpis_energy_heatingType', $post_id, '' ),
	);
}

/**
 * Informations agent.
 *
 * @param int|null $post_id ID du bien.
 * @return array<string,string>
 */
function wpis_get_agent( $post_id = null ) {
	$first = wpis_get_field( 'wpis_agent_firstname', $post_id, '' );
	$last  = wpis_get_field( 'wpis_agent_lastname', $post_id, '' );
	return array(
		'name'    => trim( $first . ' ' . $last ),
		'email'   => wpis_get_field( 'wpis_agent_email', $post_id, '' ),
		'phone'   => wpis_get_field( 'wpis_agent_phone', $post_id, '' ),
		'picture' => wpis_get_field( 'wpis_agent_picture', $post_id, '' ),
	);
}

/**
 * Informations agence.
 *
 * @param int|null $post_id ID du bien.
 * @return array<string,string>
 */
function wpis_get_agency( $post_id = null ) {
	return array(
		'name'    => wpis_get_field( 'wpis_agency_name', $post_id, '' ),
		'email'   => wpis_get_field( 'wpis_agency_email', $post_id, '' ),
		'phone'   => wpis_get_field( 'wpis_agency_phone', $post_id, '' ),
		'address' => wpis_get_field( 'wpis_agency_addressFormatted', $post_id, '' ),
	);
}

/**
 * Liste plate des équipements / commodités (generic + custom fusionnés).
 *
 * Les champs wpis_amenities_* sont des tableaux sérialisés. Les entrées custom
 * peuvent être des chaînes ou des tableaux {label,value} : on extrait le libellé.
 *
 * @param int|null $post_id ID du bien.
 * @return string[] Libellés uniques, non vides.
 */
function wpis_get_amenities( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id ) {
		return array();
	}

	$list = array();
	foreach ( array( 'wpis_amenities_generic', 'wpis_amenities_custom' ) as $key ) {
		$set = get_post_meta( $post_id, $key, true );
		if ( ! is_array( $set ) ) {
			continue;
		}
		foreach ( $set as $item ) {
			if ( is_array( $item ) ) {
				$item = isset( $item['label'] ) ? $item['label'] : reset( $item );
			}
			$item = trim( (string) $item );
			if ( '' !== $item ) {
				$list[] = $item;
			}
		}
	}
	return array_values( array_unique( $list ) );
}

/**
 * Détail des surfaces par espace (toutes les wpis_areas_* renseignées).
 *
 * @param int|null $post_id ID du bien.
 * @return array<int,array{label:string,value:string}>
 */
function wpis_get_area_breakdown( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	$defs = array(
		'wpis_areas_total'         => __( 'Surface totale', 'hello-immosync' ),
		'wpis_areas_living'        => __( 'Surface habitable', 'hello-immosync' ),
		'wpis_areas_ground'        => __( 'Terrain', 'hello-immosync' ),
		'wpis_areas_garden'        => __( 'Jardin', 'hello-immosync' ),
		'wpis_areas_terrace'       => __( 'Terrasse', 'hello-immosync' ),
		'wpis_areas_kitchen'       => __( 'Cuisine', 'hello-immosync' ),
		'wpis_areas_diningroom'    => __( 'Salle à manger', 'hello-immosync' ),
		'wpis_areas_bathroom'      => __( 'Salle de bain', 'hello-immosync' ),
		'wpis_areas_office'        => __( 'Bureau', 'hello-immosync' ),
		'wpis_areas_dressing'      => __( 'Dressing', 'hello-immosync' ),
		'wpis_areas_washroom'      => __( 'Buanderie', 'hello-immosync' ),
		'wpis_areas_cellar'        => __( 'Cave', 'hello-immosync' ),
		'wpis_areas_storage'       => __( 'Rangement', 'hello-immosync' ),
		'wpis_areas_bedroom1'      => __( 'Chambre 1', 'hello-immosync' ),
		'wpis_areas_bedroom2'      => __( 'Chambre 2', 'hello-immosync' ),
		'wpis_areas_bedroom3'      => __( 'Chambre 3', 'hello-immosync' ),
		'wpis_areas_bedroom4'      => __( 'Chambre 4', 'hello-immosync' ),
		'wpis_areas_bedroom5'      => __( 'Chambre 5', 'hello-immosync' ),
		'wpis_areas_showroom'      => __( 'Showroom', 'hello-immosync' ),
		'wpis_areas_displayWindow' => __( 'Vitrine', 'hello-immosync' ),
		'wpis_areas_manufacturing' => __( 'Atelier / production', 'hello-immosync' ),
	);

	$out = array();
	foreach ( $defs as $key => $label ) {
		$value = wpis_format_area( wpis_get_field( $key, $post_id, '' ) );
		if ( '' !== $value ) {
			$out[] = array(
				'label' => $label,
				'value' => $value,
			);
		}
	}
	return $out;
}

/**
 * Conditions financières secondaires (hors prix principal).
 *
 * @param int|null $post_id ID du bien.
 * @return array<int,array{label:string,value:string}>
 */
function wpis_get_finance_details( $post_id = null ) {
	$post_id  = $post_id ? (int) $post_id : get_the_ID();
	$currency = wpis_get_currency( $post_id );

	$out = array();

	// Disponibilité (texte libre).
	$availability = wpis_get_field( 'wpis_finance_availability', $post_id, '' );
	if ( '' !== $availability ) {
		$out[] = array(
			'label' => __( 'Disponibilité', 'hello-immosync' ),
			'value' => $availability,
		);
	}

	// Montants (formatés avec la devise).
	$money = array(
		'wpis_finance_chargeRenter'            => __( 'Charges locataire', 'hello-immosync' ),
		'wpis_finance_chargeOwner'             => __( 'Charges propriétaire', 'hello-immosync' ),
		'wpis_finance_annuityMonthly'          => __( 'Rente mensuelle', 'hello-immosync' ),
		'wpis_finance_cadastre'                => __( 'Revenu cadastral', 'hello-immosync' ),
		'wpis_finance_propertyTax'             => __( 'Précompte immobilier', 'hello-immosync' ),
		'wpis_finance_fieldValue'              => __( 'Valeur du terrain', 'hello-immosync' ),
		'wpis_finance_constructionValue'       => __( 'Valeur de construction', 'hello-immosync' ),
		'wpis_finance_notarialDeedValue'       => __( 'Valeur acte notarié', 'hello-immosync' ),
		'wpis_finance_garageUnitPrice'         => __( 'Prix unitaire garage', 'hello-immosync' ),
		'wpis_finance_parkingInteriorUnitPrice' => __( 'Parking intérieur', 'hello-immosync' ),
		'wpis_finance_parkingExteriorUnitPrice' => __( 'Parking extérieur', 'hello-immosync' ),
	);
	foreach ( $money as $key => $label ) {
		$value = wpis_format_price_value( wpis_get_field( $key, $post_id, '' ), $currency );
		if ( '' !== $value ) {
			$out[] = array(
				'label' => $label,
				'value' => $value,
			);
		}
	}
	return $out;
}

/**
 * Distances de proximité renseignées (toutes les wpis_proximity_*).
 *
 * @param int|null $post_id ID du bien.
 * @return array<int,array{label:string,value:string}>
 */
function wpis_get_proximities( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	$defs = array(
		'wpis_proximity_transports' => __( 'Transports en commun', 'hello-immosync' ),
		'wpis_proximity_bus'        => __( 'Bus', 'hello-immosync' ),
		'wpis_proximity_tram'       => __( 'Tram', 'hello-immosync' ),
		'wpis_proximity_metro'      => __( 'Métro', 'hello-immosync' ),
		'wpis_proximity_station'    => __( 'Gare', 'hello-immosync' ),
		'wpis_proximity_highway'    => __( 'Autoroute', 'hello-immosync' ),
		'wpis_proximity_stores'     => __( 'Commerces', 'hello-immosync' ),
		'wpis_proximity_school'     => __( 'Écoles', 'hello-immosync' ),
		'wpis_proximity_sportCenter' => __( 'Centre sportif', 'hello-immosync' ),
		'wpis_proximity_airport'    => __( 'Aéroport', 'hello-immosync' ),
		'wpis_proximity_beach'      => __( 'Plage', 'hello-immosync' ),
	);

	$out = array();
	foreach ( $defs as $key => $label ) {
		$value = wpis_format_distance( wpis_get_field( $key, $post_id, '' ) );
		if ( '' !== $value ) {
			$out[] = array(
				'label' => $label,
				'value' => $value,
			);
		}
	}
	return $out;
}

/**
 * Indique si une valeur WPIS booléenne est vraie ("1", 1, true…).
 *
 * @param string   $key     Clé meta.
 * @param int|null $post_id ID du bien.
 * @return bool
 */
function wpis_is_true( $key, $post_id = null ) {
	$val = wpis_get_field( $key, $post_id, '' );
	return in_array( $val, array( '1', 1, true, 'true', 'yes', 'oui' ), true );
}
