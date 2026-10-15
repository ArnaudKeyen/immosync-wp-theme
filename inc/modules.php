<?php
/**
 * Modules activables : fonctionnalités optionnelles (offres « payantes »).
 *
 * Le thème embarque le code de chaque module, mais ne l'exécute que si le module
 * est activé pour l'agence. L'activation est réservée à l'intégrateur :
 *  - la page « Réglages du thème → Modules » n'est visible que des comptes
 *    portant la capacité `wpis_manage_modules` (un administrateur client ne la
 *    voit pas) — à attribuer via `wp user add-cap <user> wpis_manage_modules` ;
 *  - la constante `WPIS_MODULES` (wp-config.php) prime sur la page, pour figer
 *    l'état côté serveur : `define( 'WPIS_MODULES', 'share,search_ajax' );`
 *    (liste séparée par des virgules ou tableau ; chaîne vide = tout coupé).
 *
 * Contrat d'un module : key stable (stockée en base), label et description admin.
 * Le code du module teste lui-même wpis_module_enabled( 'key' ) avant d'agir.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Registre
 * ---------------------------------------------------------------------- */

/**
 * Registre canonique des modules activables.
 *
 * Filtrable pour qu'un thème enfant (ou une extension) déclare ses modules.
 *
 * @return array<string,array{label:string,description:string}>
 */
function wpis_get_module_registry() {
	$registry = array(
		'immersive_media' => array(
			'label'       => __( 'Vidéo et visite 360°', 'hello-immosync' ),
			'description' => __( 'Affiche les liens, badges et contenus vidéo / visite virtuelle fournis par le logiciel immobilier.', 'hello-immosync' ),
		),
		'map_voyager'     => array(
			'label'       => __( 'Carte interactive (OpenStreetMap)', 'hello-immosync' ),
			'description' => __( 'Remplace la carte OpenStreetMap statique par une carte interactive au style clair de type Google Maps.', 'hello-immosync' ),
		),
		'share'       => array(
			'label'       => __( 'Partage des annonces', 'hello-immosync' ),
			'description' => __( 'Boutons WhatsApp, Facebook et « Copier le lien » sur les fiches de biens.', 'hello-immosync' ),
		),
		'search_ajax' => array(
			'label'       => __( 'Recherche instantanée', 'hello-immosync' ),
			'description' => __( 'Sur la page des biens, les résultats se mettent à jour sans rechargement à chaque changement de filtre.', 'hello-immosync' ),
		),
	);

	/**
	 * Permet d'ajouter / retirer des modules.
	 *
	 * @param array<string,array{label:string,description:string}> $registry Modules.
	 */
	return apply_filters( 'wpis_module_registry', $registry );
}

/* -------------------------------------------------------------------------
 * État
 * ---------------------------------------------------------------------- */

/**
 * Capacité requise pour gérer les modules.
 *
 * @return string
 */
function wpis_modules_capability() {
	/**
	 * Capacité donnant accès à la page « Modules ».
	 *
	 * @param string $capability Capacité.
	 */
	return (string) apply_filters( 'wpis_modules_capability', 'wpis_manage_modules' );
}

/**
 * Modules forcés par la constante WPIS_MODULES, ou null si elle n'est pas définie.
 *
 * @return string[]|null
 */
function wpis_modules_from_constant() {
	if ( ! defined( 'WPIS_MODULES' ) ) {
		return null;
	}

	$value = WPIS_MODULES;
	if ( is_string( $value ) ) {
		$value = explode( ',', $value );
	}
	if ( ! is_array( $value ) ) {
		return array();
	}

	return array_values( array_filter( array_map( 'trim', array_map( 'strval', $value ) ) ) );
}

/**
 * Liste des modules actifs (clés), constante prioritaire sur la page d'options.
 *
 * @return string[]
 */
function wpis_get_enabled_modules() {
	$enabled = wpis_modules_from_constant();

	if ( null === $enabled ) {
		// Lecture directe de l'option (clé de stockage ACF) : pas de dépendance à
		// ACF sur le front, et donc pas de module fantôme si ACF est désactivé.
		$enabled = get_option( 'options_wpis_modules', array() );
		$enabled = is_array( $enabled ) ? $enabled : array();
	}

	return array_values( array_intersect( $enabled, array_keys( wpis_get_module_registry() ) ) );
}

/**
 * Le module est-il actif ?
 *
 * @param string $key Clé du module (voir wpis_get_module_registry()).
 * @return bool
 */
function wpis_module_enabled( $key ) {
	$enabled = in_array( $key, wpis_get_enabled_modules(), true );

	/**
	 * Dernier mot sur l'activation d'un module (tests, cas particuliers).
	 *
	 * @param bool   $enabled État calculé.
	 * @param string $key     Clé du module.
	 */
	return (bool) apply_filters( 'wpis_module_enabled', $enabled, $key );
}

/* -------------------------------------------------------------------------
 * Page d'options réservée « Réglages du thème → Modules »
 * ---------------------------------------------------------------------- */

add_action( 'acf/init', 'wpis_register_modules_options', 11 );
/**
 * Sous-page « Modules », visible uniquement avec la capacité dédiée.
 *
 * Priorité 11 : après la création du menu top-level « wpis-theme-settings ».
 *
 * @return void
 */
function wpis_register_modules_options() {
	if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
		return;
	}

	acf_add_options_sub_page(
		array(
			'page_title'  => __( 'Modules', 'hello-immosync' ),
			'menu_title'  => __( 'Modules', 'hello-immosync' ),
			'menu_slug'   => 'wpis-theme-modules',
			'parent_slug' => 'wpis-theme-settings',
			'capability'  => wpis_modules_capability(),
		)
	);
}

add_action( 'acf/init', 'wpis_register_modules_field_group', 11 );
/**
 * Groupe de champs de la page « Modules » : une case par module du registre.
 *
 * @return void
 */
function wpis_register_modules_field_group() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_wpis_modules',
			'title'    => __( 'Modules activés', 'hello-immosync' ),
			'fields'   => array(
				array(
					'key'     => 'field_wpis_modules_msg',
					'label'   => '',
					'name'    => '',
					'type'    => 'message',
					'message' => '', // Alimenté via acf/load_field (état de la constante).
				),
				array(
					'key'           => 'field_wpis_modules',
					'label'         => __( 'Modules', 'hello-immosync' ),
					'name'          => 'wpis_modules',
					'type'          => 'checkbox',
					'choices'       => array(), // Alimenté via acf/load_field depuis le registre.
					'layout'        => 'vertical',
					'return_format' => 'value',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'wpis-theme-modules',
					),
				),
			),
		)
	);
}

add_filter( 'acf/load_field/key=field_wpis_modules', 'wpis_acf_load_module_choices' );
/**
 * Alimente les cases avec le registre (libellé + description).
 *
 * @param array $field Champ ACF.
 * @return array
 */
function wpis_acf_load_module_choices( $field ) {
	$field['choices'] = array();
	foreach ( wpis_get_module_registry() as $key => $module ) {
		$field['choices'][ $key ] = sprintf( '<strong>%s</strong> — %s', esc_html( $module['label'] ), esc_html( $module['description'] ) );
	}
	return $field;
}

add_filter( 'acf/load_field/key=field_wpis_modules_msg', 'wpis_acf_load_modules_message' );
/**
 * Message d'en-tête : rappelle le rôle de la page et signale la constante.
 *
 * @param array $field Champ ACF.
 * @return array
 */
function wpis_acf_load_modules_message( $field ) {
	$field['message'] = __( 'Fonctionnalités optionnelles du thème, activées selon l’offre de l’agence. Cette page n’est visible que des intégrateurs.', 'hello-immosync' );

	$forced = wpis_modules_from_constant();
	if ( null !== $forced ) {
		$field['message'] .= '<br><strong>' . sprintf(
			/* translators: %s: liste des modules forcés. */
			esc_html__( 'La constante WPIS_MODULES (wp-config.php) prime sur cette page : modules actifs = %s.', 'hello-immosync' ),
			$forced ? esc_html( implode( ', ', $forced ) ) : esc_html__( 'aucun', 'hello-immosync' )
		) . '</strong>';
	}
	return $field;
}
