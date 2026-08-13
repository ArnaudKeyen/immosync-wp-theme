<?php
/**
 * Page d'accueil modulable : registre des blocs + ordre/visibilité.
 *
 * Même contrat que la fiche de bien (inc/estate-sections.php) : front-page.php
 * n'enchaîne plus des get_template_part() en dur, il parcourt une liste ordonnée
 * et filtrable. Chaque agence réordonne ou masque un bloc depuis « Réglages du
 * thème → Page d'accueil » (page d'options ACF), sans toucher au code.
 *
 * Contrat d'une section :
 *  - key   : identifiant stable (utilisé en base pour l'ordre) ;
 *  - label : libellé admin ;
 *  - part  : template-part rendu ;
 *  - args  : (optionnel) arguments passés au template-part.
 *
 * Contrairement aux sections de fiche de bien, les blocs de la home ne se
 * masquent pas tous d'eux-mêmes quand la donnée manque : l'interrupteur
 * « Affiché » est le moyen de contrôle prévu pour ceux-là.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Registre des blocs (ordre par défaut = ordre de déclaration)
 * ---------------------------------------------------------------------- */

/**
 * Registre canonique des blocs de la page d'accueil.
 *
 * Filtrable pour qu'un thème enfant ajoute / retire / renomme un bloc.
 *
 * @return array<string,array{label:string,part:string,args?:array}>
 */
function wpis_get_home_section_registry() {
	$registry = array(
		'hero'      => array(
			'label' => __( 'Hero d’accueil (image + recherche)', 'hello-immosync' ),
			'part'  => 'template-parts/global/page-hero',
			'args'  => array( 'variant' => 'home' ),
		),
		'featured'  => array(
			'label' => __( 'Biens en vedette', 'hello-immosync' ),
			'part'  => 'template-parts/home/featured',
		),
		'locations' => array(
			'label' => __( 'Trouver son lieu (localités)', 'hello-immosync' ),
			'part'  => 'template-parts/home/find-your-place',
		),
		'lifestyle' => array(
			'label' => __( 'Cadre de vie', 'hello-immosync' ),
			'part'  => 'template-parts/home/lifestyle',
		),
		'offmarket' => array(
			'label' => __( 'Off-market', 'hello-immosync' ),
			'part'  => 'template-parts/home/offmarket',
		),
		'about'     => array(
			'label' => __( 'L’agence', 'hello-immosync' ),
			'part'  => 'template-parts/home/about',
		),
		'cta'       => array(
			'label' => __( 'CTA vendeur', 'hello-immosync' ),
			'part'  => 'template-parts/home/cta-seller',
		),
	);

	return apply_filters( 'wpis_home_section_registry', $registry );
}

/**
 * Blocs ordonnés et activés pour l'affichage.
 *
 * Lit l'ordre/visibilité depuis la page d'options (champ ACF « home_sections »).
 * Tout bloc du registre absent des réglages enregistrés est ajouté à la fin
 * (activé) : un nouveau bloc livré dans une mise à jour reste visible sans
 * intervention. Repli complet sur le registre si aucun réglage.
 *
 * @return array<string,array{label:string,part:string,args?:array}>
 */
function wpis_get_home_sections() {
	$registry = wpis_get_home_section_registry();
	$rows     = wpis_theme_option( 'home_sections', array() );

	$ordered = array();
	$seen    = array();

	if ( is_array( $rows ) ) {
		foreach ( $rows as $row ) {
			$key = isset( $row['section'] ) ? $row['section'] : '';
			if ( '' === $key || ! isset( $registry[ $key ] ) || isset( $seen[ $key ] ) ) {
				continue;
			}
			$seen[ $key ] = true;
			if ( ! empty( $row['enabled'] ) ) {
				$ordered[ $key ] = $registry[ $key ];
			}
		}
	}

	// Blocs du registre jamais réglés → ajoutés à la fin, visibles.
	foreach ( $registry as $key => $section ) {
		if ( ! isset( $seen[ $key ] ) ) {
			$ordered[ $key ] = $section;
		}
	}

	return apply_filters( 'wpis_home_sections', $ordered );
}

/**
 * Rend les blocs de la page d'accueil, dans l'ordre réglé.
 *
 * @return void
 */
function wpis_render_home_sections() {
	foreach ( wpis_get_home_sections() as $key => $section ) {
		/**
		 * Permet de court-circuiter ou remplacer le rendu d'un bloc.
		 *
		 * @param bool   $render Rendre le bloc ?
		 * @param string $key    Clé du bloc.
		 */
		if ( ! apply_filters( 'wpis_render_home_section', true, $key ) ) {
			continue;
		}

		$args = isset( $section['args'] ) && is_array( $section['args'] ) ? $section['args'] : array();
		get_template_part( $section['part'], null, array_merge( array( 'section_key' => $key ), $args ) );
	}
}

/* -------------------------------------------------------------------------
 * Page d'options « Page d'accueil » (ordre des blocs)
 * ---------------------------------------------------------------------- */

add_action( 'acf/init', 'wpis_register_home_sections_options', 11 );
/**
 * Sous-page d'options accrochée au menu « Réglages du thème ».
 *
 * Priorité 11 : passe après wpis_register_options_pages() (priorité 10) qui crée
 * le menu top-level « wpis-theme-settings ».
 *
 * @return void
 */
function wpis_register_home_sections_options() {
	if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
		return;
	}

	acf_add_options_sub_page(
		array(
			'page_title'  => __( 'Page d’accueil', 'hello-immosync' ),
			'menu_title'  => __( 'Page d’accueil', 'hello-immosync' ),
			'menu_slug'   => 'wpis-theme-home',
			'parent_slug' => 'wpis-theme-settings',
		)
	);
}

add_action( 'acf/init', 'wpis_register_home_sections_field_group', 11 );
/**
 * Groupe de champs de la page « Page d'accueil » : un répéteur triable par
 * glisser-déposer, une ligne par bloc (sélecteur de bloc + interrupteur).
 *
 * Clé « group_wpis_home_layout » : « group_wpis_home_sections » est déjà utilisé
 * par le groupe de contenu de la home (inc/acf-content.php).
 *
 * @return void
 */
function wpis_register_home_sections_field_group() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_wpis_home_layout',
			'title'    => __( 'Blocs de la page d’accueil', 'hello-immosync' ),
			'fields'   => array(
				array(
					'key'     => 'field_wpis_home_sections_msg',
					'label'   => '',
					'name'    => '',
					'type'    => 'message',
					'message' => __( 'Glissez-déposez les lignes pour définir l’ordre d’affichage des blocs sur la page d’accueil. Décochez « Affiché » pour masquer un bloc. Les textes de chaque bloc se modifient, eux, depuis la page d’accueil elle-même (Pages → Accueil).', 'hello-immosync' ),
				),
				array(
					'key'          => 'field_wpis_home_sections',
					'label'        => __( 'Ordre des blocs', 'hello-immosync' ),
					'name'         => 'home_sections',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => __( 'Ajouter un bloc', 'hello-immosync' ),
					'sub_fields'   => array(
						array(
							'key'      => 'field_wpis_home_section_key',
							'label'    => __( 'Bloc', 'hello-immosync' ),
							'name'     => 'section',
							'type'     => 'select',
							'choices'  => array(), // Alimenté via acf/load_field depuis le registre.
							'required' => 1,
							'wrapper'  => array( 'width' => '70' ),
						),
						array(
							'key'           => 'field_wpis_home_section_enabled',
							'label'         => __( 'Affiché', 'hello-immosync' ),
							'name'          => 'enabled',
							'type'          => 'true_false',
							'ui'            => 1,
							'default_value' => 1,
							'wrapper'       => array( 'width' => '30' ),
						),
					),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'wpis-theme-home',
					),
				),
			),
		)
	);
}

add_filter( 'acf/load_field/key=field_wpis_home_section_key', 'wpis_acf_load_home_section_choices' );
/**
 * Alimente le sélecteur de bloc avec les libellés du registre (source unique de
 * vérité — pas de liste figée à maintenir dans l'admin).
 *
 * @param array $field Champ ACF.
 * @return array
 */
function wpis_acf_load_home_section_choices( $field ) {
	$field['choices'] = array();
	foreach ( wpis_get_home_section_registry() as $key => $section ) {
		$field['choices'][ $key ] = $section['label'];
	}
	return $field;
}

add_filter( 'acf/load_value/key=field_wpis_home_sections', 'wpis_acf_seed_home_sections', 10, 3 );
/**
 * Pré-remplit le répéteur avec tous les blocs du registre (activés, dans l'ordre
 * par défaut) tant que rien n'a été enregistré : l'admin ouvre une page déjà
 * prête à réordonner, sans devoir ajouter les lignes une à une.
 *
 * @param mixed $value   Valeur brute en base.
 * @param mixed $post_id Contexte ACF (ici « option »).
 * @param array $field   Champ ACF.
 * @return mixed
 */
function wpis_acf_seed_home_sections( $value, $post_id, $field ) {
	if ( ! empty( $value ) ) {
		return $value;
	}

	$seed = array();
	foreach ( wpis_get_home_section_registry() as $key => $section ) {
		$seed[] = array(
			'field_wpis_home_section_key'     => $key,
			'field_wpis_home_section_enabled' => 1,
		);
	}
	return $seed;
}
