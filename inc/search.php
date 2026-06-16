<?php
/**
 * Recherche et filtres immobiliers — server-side (WP_Query / pre_get_posts).
 *
 * Les filtres sont passés en GET sur l'archive des biens :
 *   ?wpis_city=&wpis_category=&wpis_purpose=&wpis_price_min=&wpis_price_max=
 *    &wpis_bedrooms=&wpis_sort=
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lit les filtres courants depuis la requête (assainis).
 *
 * @return array<string,string>
 */
function wpis_current_filters() {
	$get = function ( $key ) {
		return isset( $_GET[ $key ] ) ? sanitize_text_field( wp_unslash( $_GET[ $key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	};

	return array(
		'city'      => $get( 'wpis_city' ),
		'category'  => $get( 'wpis_category' ),
		'purpose'   => $get( 'wpis_purpose' ),
		'price_min' => $get( 'wpis_price_min' ),
		'price_max' => $get( 'wpis_price_max' ),
		'bedrooms'  => $get( 'wpis_bedrooms' ),
		'sort'      => $get( 'wpis_sort' ),
	);
}

/**
 * Valeurs distinctes disponibles pour les filtres (villes, catégories, opérations).
 * Mises en cache via transient.
 *
 * @return array{cities:string[],categories:string[],purposes:string[]}
 */
function wpis_get_filter_options() {
	$cached = get_transient( 'wpis_filter_options' );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	global $wpdb;

	$collect = function ( $meta_key ) use ( $wpdb ) {
		$rows = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT pm.meta_value
				 FROM {$wpdb->postmeta} pm
				 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				 WHERE pm.meta_key = %s
				   AND pm.meta_value <> ''
				   AND p.post_type = 'wpis_estates'
				   AND p.post_status = 'publish'
				 ORDER BY pm.meta_value ASC",
				$meta_key
			)
		);
		return array_values( array_unique( array_filter( (array) $rows ) ) );
	};

	$options = array(
		'cities'     => $collect( 'wpis_address_city' ),
		'categories' => $collect( 'wpis_category_label' ),
		'purposes'   => $collect( 'wpis_purpose_label' ),
	);

	set_transient( 'wpis_filter_options', $options, HOUR_IN_SECONDS );
	return $options;
}

/**
 * Invalide le cache des options de filtres quand un bien est modifié.
 */
function wpis_flush_filter_options() {
	delete_transient( 'wpis_filter_options' );
}
add_action( 'save_post_wpis_estates', 'wpis_flush_filter_options' );
add_action( 'deleted_post', 'wpis_flush_filter_options' );

/**
 * Applique les filtres à la requête principale de l'archive des biens.
 *
 * @param WP_Query $query Requête.
 * @return void
 */
function wpis_filter_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( ! $query->is_post_type_archive( 'wpis_estates' ) ) {
		return;
	}

	$filters    = wpis_current_filters();
	$meta_query = array();

	if ( '' !== $filters['city'] ) {
		$meta_query[] = array(
			'key'     => 'wpis_address_city',
			'value'   => $filters['city'],
			'compare' => '=',
		);
	}
	if ( '' !== $filters['category'] ) {
		$meta_query[] = array(
			'key'     => 'wpis_category_label',
			'value'   => $filters['category'],
			'compare' => '=',
		);
	}
	if ( '' !== $filters['purpose'] ) {
		$meta_query[] = array(
			'key'     => 'wpis_purpose_label',
			'value'   => $filters['purpose'],
			'compare' => '=',
		);
	}
	if ( is_numeric( $filters['price_min'] ) ) {
		$meta_query[] = array(
			'key'     => 'wpis_finance_price',
			'value'   => (int) $filters['price_min'],
			'type'    => 'NUMERIC',
			'compare' => '>=',
		);
	}
	if ( is_numeric( $filters['price_max'] ) ) {
		$meta_query[] = array(
			'key'     => 'wpis_finance_price',
			'value'   => (int) $filters['price_max'],
			'type'    => 'NUMERIC',
			'compare' => '<=',
		);
	}
	if ( is_numeric( $filters['bedrooms'] ) && (int) $filters['bedrooms'] > 0 ) {
		$meta_query[] = array(
			'key'     => 'wpis_configuration_bedrooms',
			'value'   => (int) $filters['bedrooms'],
			'type'    => 'NUMERIC',
			'compare' => '>=',
		);
	}

	if ( count( $meta_query ) > 1 ) {
		$meta_query['relation'] = 'AND';
	}
	if ( ! empty( $meta_query ) ) {
		$query->set( 'meta_query', $meta_query );
	}

	// Tri.
	switch ( $filters['sort'] ) {
		case 'price-asc':
			$query->set( 'meta_key', 'wpis_finance_price' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'ASC' );
			break;
		case 'price-desc':
			$query->set( 'meta_key', 'wpis_finance_price' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'DESC' );
			break;
		case 'date-asc':
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'ASC' );
			break;
		default:
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'DESC' );
			break;
	}

	$query->set( 'posts_per_page', apply_filters( 'wpis_estates_per_page', 12 ) );
}
add_action( 'pre_get_posts', 'wpis_filter_archive_query' );

/**
 * Récupère une sélection de biens (biens mis en avant / similaires).
 *
 * @param array $args Arguments WP_Query partiels.
 * @return WP_Query
 */
function wpis_query_estates( $args = array() ) {
	$defaults = array(
		'post_type'           => 'wpis_estates',
		'post_status'         => 'publish',
		'posts_per_page'      => 6,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	return new WP_Query( wp_parse_args( $args, $defaults ) );
}
