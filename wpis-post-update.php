<?php
/**
 * Hook « post-update » exécuté par le plugin wp-immo-sync après la synchro d'un
 * bien : le plugin inclut ce fichier depuis le thème et appelle wpis_post_update().
 *
 * Rôle : forcer côté serveur des valeurs cohérentes que la saisie manuelle ne
 * garantit pas —
 *   - titre et slug normalisés « Catégorie transaction à Ville » (le statut étant
 *     géré à part, on repart de zéro et on purge les « VENDU »/« OPTION » saisis
 *     dans le titre) ;
 *   - libellé de statut normalisé (Option Location → Option, Autre → Offre en cours) ;
 *   - ordre de statut numérique (wpis_status_order) pour le tri « disponibles
 *     d'abord, indisponibles en dernier » ;
 *   - métas d'adresse pré-calculées (zip/ville, adresse complète, coordonnées GPS).
 *
 * @param int $postId ID du bien (CPT wpis_estates).
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpis_post_update' ) ) {
	function wpis_post_update( $postId ) {
		$meta = get_post_custom( $postId );

		$get = static function ( $key ) use ( $meta ) {
			return isset( $meta[ $key ][0] ) ? $meta[ $key ][0] : '';
		};

		// --- Infos principales -------------------------------------------------
		$wpisID        = $get( 'wpis_id' );
		$categoryLabel = $get( 'wpis_subcategory_label' );
		if ( '' === $categoryLabel ) {
			$categoryLabel = $get( 'wpis_category_label' );
		}
		$purposeLabel = $get( 'wpis_purpose_label' );

		// --- Adresse -----------------------------------------------------------
		$addressStreet  = $get( 'wpis_address_street' );
		$addressNumber  = $get( 'wpis_address_number' );
		$addressBox     = $get( 'wpis_address_box' );
		$addressZip     = $get( 'wpis_address_zip' );
		$addressCity    = $get( 'wpis_address_city' );
		$addressCountry = $get( 'wpis_address_country' );

		$zipCity         = trim( $addressZip . ' - ' . $addressCity, ' -' );
		$addressComplete = format_address_fr( $addressStreet, $addressNumber, $addressBox, $addressZip, $addressCity, $addressCountry );

		// --- GPS : vraies valeurs (l'ancien code stockait un booléen isset()) ---
		// Ordre conservé de l'existant : longitude;latitude.
		$longitude  = $get( 'wpis_address_longitude' );
		$latitude   = $get( 'wpis_address_latitude' );
		$coordinate = ( '' !== $longitude || '' !== $latitude ) ? $longitude . ';' . $latitude : '';

		// --- Description : version la plus riche disponible --------------------
		$post_content = $get( 'wpis_description_long' );
		if ( '' === $post_content ) {
			$post_content = $get( 'wpis_description_base' );
		}
		if ( '' === $post_content ) {
			$post_content = $get( 'wpis_description_short' );
		}

		// --- Titre forcé « Catégorie transaction à Ville » (segments vides ignorés) ---
		$post_title = wpis_pu_capitalize( $categoryLabel );
		$purpose    = mb_strtolower( $purposeLabel );
		if ( '' !== $purpose ) {
			$post_title = trim( $post_title . ' ' . $purpose );
		}
		if ( '' !== $addressCity ) {
			$post_title = trim( $post_title . ' à ' . $addressCity );
		}

		// --- Slug forcé et assaini (accents/espaces gérés), suffixé par l'ID ----
		$post_name = sanitize_title( implode( '-', array_filter( array( $purposeLabel, $categoryLabel, $addressCity ) ) ) );
		if ( '' !== $wpisID ) {
			$post_name = trim( $post_name . '-' . $wpisID, '-' );
		}

		// --- Statut : normalisation du libellé + ordre numérique pour le tri ---
		$statusLabel = $get( 'wpis_status_label' );
		$statusOrder = 14; // Défaut médian pour un statut inconnu (ni tout en haut, ni tout en bas).

		switch ( $statusLabel ) {
			case 'Nouveau':
				$statusOrder = 20;
				break;
			case 'À vendre':
			case 'À louer':
			case 'Actif':
				$statusOrder = 19;
				break;
			case 'Option':
				$statusOrder = 15;
				break;
			case 'Option Location':
				$statusLabel = 'Option';
				$statusOrder = 12;
				break;
			case 'Autre':
				$statusLabel = 'Offre en cours';
				$statusOrder = 11;
				break;
			case 'Sous compromis':
				$statusOrder = 10;
				break;
			case 'Vendu':
			case 'Loué':
				$statusOrder = 5;
				break;
		}

		$my_post = array(
			'ID'         => $postId,
			'meta_input' => array(
				'wpis_custom_addressZipCity'  => $zipCity,
				'wpis_custom_addressComplete' => $addressComplete,
				'wpis_custom_address_gps'     => $coordinate,
				'wpis_status_label'           => $statusLabel,
				'wpis_status_order'           => $statusOrder,
			),
		);
		// On n'écrase pas avec du vide si l'info manque.
		if ( '' !== $post_content ) {
			$my_post['post_content'] = $post_content;
		}
		if ( '' !== $post_title ) {
			$my_post['post_title'] = $post_title;
		}
		if ( '' !== $post_name ) {
			$my_post['post_name'] = $post_name;
		}

		wp_update_post( $my_post );
	}
}

if ( ! function_exists( 'wpis_pu_capitalize' ) ) {
	/**
	 * Capitalise la première lettre d'une chaîne UTF-8 (mb-safe), le reste inchangé.
	 *
	 * @param string $str
	 * @return string
	 */
	function wpis_pu_capitalize( $str ) {
		$str = trim( (string) $str );
		if ( '' === $str ) {
			return '';
		}
		return mb_strtoupper( mb_substr( $str, 0, 1 ) ) . mb_substr( $str, 1 );
	}
}

if ( ! function_exists( 'format_address_fr' ) ) {
	/**
	 * Construit une adresse française complète « n° boîte b, rue, zip ville, pays ».
	 *
	 * @return string
	 */
	function format_address_fr( $addressStreet, $addressNumber, $addressBox, $addressZip, $addressCity, $addressCountry ) {
		$formattedAddress = $addressNumber . ' ';

		if ( ! empty( $addressBox ) ) {
			$formattedAddress .= 'boîte ' . $addressBox;
		}

		if ( ! empty( $addressNumber ) || ! empty( $addressBox ) ) {
			$formattedAddress .= ', ';
		}

		$formattedAddress .= $addressStreet . ', ' . $addressZip . ' ' . $addressCity;

		if ( ! empty( $addressCountry ) ) {
			$formattedAddress .= ', ' . $addressCountry;
		}

		return $formattedAddress;
	}
}
