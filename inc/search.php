<?php
/**
 * Recherche et filtres immobiliers.
 *
 * Les filtres sont passés en GET sur l'archive des biens :
 *   ?wpis_city=&wpis_category=&wpis_purpose=&wpis_price_min=&wpis_price_max=
 *    &wpis_bedrooms=&wpis_sort=
 *
 * Trois couches, du socle à l'enrichissement :
 *  1. server-side : pre_get_posts filtre la requête principale de l'archive
 *     (fonctionne sans JavaScript) ;
 *  2. facettes : le formulaire désactive les options qui ne mènent à aucun bien
 *     (dataset embarqué, calcul côté navigateur — assets/js/search.js) ;
 *  3. module « search_ajax » : sur l'archive, les résultats sont rechargés via
 *     la route REST wpis/v1/estates sans rechargement de page.
 * Les couches 1 et 3 partagent wpis_build_estate_query_args() et le même
 * template-part de résultats : le HTML servi est identique dans les deux cas.
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
	return wpis_sanitize_filters( wp_unslash( $_GET ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

/**
 * Normalise un jeu de paramètres bruts (GET, requête REST) en filtres assainis.
 *
 * @param array $raw Paramètres bruts, clés « wpis_* » (déjà déséchappés).
 * @return array<string,string>
 */
function wpis_sanitize_filters( $raw ) {
	$get = static function ( $key ) use ( $raw ) {
		return isset( $raw[ $key ] ) && is_scalar( $raw[ $key ] ) ? sanitize_text_field( (string) $raw[ $key ] ) : '';
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
 * Filtres non vides, sous leur nom de paramètre GET (pour reconstruire une URL).
 *
 * @param array<string,string> $filters Filtres assainis.
 * @return array<string,string>
 */
function wpis_filters_to_query_args( $filters ) {
	$args = array();
	foreach ( $filters as $key => $value ) {
		if ( '' !== $value ) {
			$args[ 'wpis_' . $key ] = $value;
		}
	}
	return $args;
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
 * Arguments WP_Query (filtres, tri, pagination) correspondant à un jeu de filtres.
 *
 * Source unique partagée par la requête principale de l'archive et la route
 * REST de la recherche instantanée.
 *
 * @param array<string,string> $filters Filtres assainis (wpis_sanitize_filters()).
 * @return array<string,mixed>
 */
function wpis_build_estate_query_args( $filters ) {
	$args = array();

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
		$args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
	}

	// Tri.
	switch ( $filters['sort'] ) {
		case 'price-asc':
			$args['meta_key'] = 'wpis_finance_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'ASC';
			break;
		case 'price-desc':
			$args['meta_key'] = 'wpis_finance_price'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'DESC';
			break;
		case 'date-asc':
			$args['orderby'] = 'date';
			$args['order']   = 'ASC';
			break;
		default:
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
			break;
	}

	$args['posts_per_page'] = (int) apply_filters( 'wpis_estates_per_page', 12 );

	/**
	 * Derniers ajustements des arguments de la recherche de biens.
	 *
	 * @param array<string,mixed>  $args    Arguments WP_Query.
	 * @param array<string,string> $filters Filtres assainis.
	 */
	return apply_filters( 'wpis_estate_query_args', $args, $filters );
}

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

	foreach ( wpis_build_estate_query_args( wpis_current_filters() ) as $key => $value ) {
		$query->set( $key, $value );
	}
}
add_action( 'pre_get_posts', 'wpis_filter_archive_query' );

/* -------------------------------------------------------------------------
 * Facettes (options sans résultat désactivées dans le formulaire)
 * ---------------------------------------------------------------------- */

/**
 * Dataset des biens pour la recherche à facettes : une entrée par bien publié,
 * avec ses valeurs de filtre. Mis en cache via transient, invalidé à chaque
 * enregistrement / suppression de bien (comme wpis_get_filter_options()).
 *
 * Les clés reflètent les critères de wpis_build_estate_query_args() :
 *   purpose  ↔ wpis_purpose_label          (=)
 *   category ↔ wpis_category_label         (=)
 *   city     ↔ wpis_address_city           (=)
 *   bedrooms ↔ wpis_configuration_bedrooms (>=)
 *   price    ↔ wpis_finance_price          (<=)
 *
 * @return array<int,array{purpose:string,category:string,city:string,bedrooms:int,price:int}>
 */
function wpis_get_estate_facets() {
	$cached = get_transient( 'wpis_estate_facets' );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$ids = get_posts(
		array(
			'post_type'      => 'wpis_estates',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	update_meta_cache( 'post', $ids ); // Une requête pour toutes les metas.

	$facets = array();
	foreach ( $ids as $id ) {
		$facets[] = array(
			'purpose'  => (string) get_post_meta( $id, 'wpis_purpose_label', true ),
			'category' => (string) get_post_meta( $id, 'wpis_category_label', true ),
			'city'     => (string) get_post_meta( $id, 'wpis_address_city', true ),
			'bedrooms' => (int) get_post_meta( $id, 'wpis_configuration_bedrooms', true ),
			'price'    => (int) get_post_meta( $id, 'wpis_finance_price', true ),
		);
	}

	set_transient( 'wpis_estate_facets', $facets, HOUR_IN_SECONDS );
	return $facets;
}

/**
 * Invalide le dataset des facettes quand un bien change.
 */
function wpis_flush_estate_facets() {
	delete_transient( 'wpis_estate_facets' );
}
add_action( 'save_post_wpis_estates', 'wpis_flush_estate_facets' );
add_action( 'deleted_post', 'wpis_flush_estate_facets' );

/**
 * Charge le script de recherche (facettes + recherche instantanée) et lui
 * transmet sa configuration. Appelé par le template-part search-bar : le script
 * n'est chargé que sur les pages qui affichent réellement le formulaire.
 *
 * @return void
 */
function wpis_enqueue_search_script() {
	if ( wp_script_is( 'wpis-search', 'enqueued' ) ) {
		return;
	}

	$rel  = '/assets/js/search.js';
	$path = HELLO_IMMOSYNC_DIR . $rel;
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_script( 'wpis-search', HELLO_IMMOSYNC_URI . $rel, array(), (string) filemtime( $path ), true );

	$is_archive = is_post_type_archive( 'wpis_estates' );
	wp_localize_script(
		'wpis-search',
		'WPISSearch',
		array(
			'estates'  => wpis_get_estate_facets(),
			// Sur l'archive, chaque changement relance la recherche (sans le bouton).
			'archive'  => $is_archive,
			// Module « Recherche instantanée » : résultats rechargés sans quitter la page.
			'ajax'     => $is_archive && wpis_module_enabled( 'search_ajax' ),
			'endpoint' => esc_url_raw( rest_url( 'wpis/v1/estates' ) ),
			'i18n'     => array(
				'error' => __( 'La recherche n’a pas pu aboutir. Veuillez réessayer.', 'hello-immosync' ),
			),
		)
	);
}

/* -------------------------------------------------------------------------
 * Résultats (archive + recherche instantanée)
 * ---------------------------------------------------------------------- */

/**
 * URL de l'archive pour un jeu de filtres (et une page), sans paramètre vide.
 *
 * @param array<string,string> $filters Filtres assainis.
 * @param int                  $paged   Page (1 = première).
 * @return string
 */
function wpis_estates_url( $filters, $paged = 1 ) {
	$base = get_post_type_archive_link( 'wpis_estates' );
	if ( ! $base ) {
		return '';
	}

	$args = wpis_filters_to_query_args( $filters );
	if ( $paged > 1 ) {
		global $wp_rewrite;
		if ( $wp_rewrite->using_permalinks() ) {
			$base = trailingslashit( $base ) . user_trailingslashit( $wp_rewrite->pagination_base . '/' . (int) $paged, 'paged' );
		} else {
			$args['paged'] = (int) $paged;
		}
	}
	return add_query_arg( array_map( 'rawurlencode', $args ), $base );
}

/**
 * Affiche le bloc de résultats (compteur, tri, grille, pagination) pour une requête.
 *
 * @param WP_Query             $query   Requête de biens.
 * @param array<string,string> $filters Filtres assainis appliqués.
 * @return void
 */
function wpis_render_estate_results( $query, $filters ) {
	get_template_part(
		'template-parts/estate/results',
		null,
		array(
			'query'   => $query,
			'filters' => $filters,
		)
	);
}

add_action( 'rest_api_init', 'wpis_register_search_route' );
/**
 * Route REST de la recherche instantanée : GET /wp-json/wpis/v1/estates?wpis_*=…
 *
 * Renvoie le HTML du bloc de résultats (même template-part que l'archive) et
 * l'URL canonique correspondante, à pousser dans l'historique du navigateur.
 *
 * @return void
 */
function wpis_register_search_route() {
	register_rest_route(
		'wpis/v1',
		'/estates',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'wpis_rest_search_estates',
			'permission_callback' => static function () {
				return wpis_module_enabled( 'search_ajax' );
			},
			'args'                => array(
				'paged' => array(
					'type'    => 'integer',
					'minimum' => 1,
					'default' => 1,
				),
			),
		)
	);
}

/**
 * Callback de la route wpis/v1/estates.
 *
 * @param WP_REST_Request $request Requête.
 * @return WP_REST_Response
 */
function wpis_rest_search_estates( $request ) {
	$filters = wpis_sanitize_filters( $request->get_query_params() );
	$paged   = max( 1, (int) $request->get_param( 'paged' ) );

	$query = new WP_Query(
		array_merge(
			array(
				'post_type'   => 'wpis_estates',
				'post_status' => 'publish',
				'paged'       => $paged,
			),
			wpis_build_estate_query_args( $filters )
		)
	);

	ob_start();
	wpis_render_estate_results( $query, $filters );
	$html = ob_get_clean();

	return new WP_REST_Response(
		array(
			'html'  => $html,
			'count' => (int) $query->found_posts,
			'url'   => wpis_estates_url( $filters, $paged ),
		)
	);
}

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
