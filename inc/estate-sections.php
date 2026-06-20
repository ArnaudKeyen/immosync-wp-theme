<?php
/**
 * Fiche de bien modulable : registre des sections + ordre/visibilité.
 *
 * La fiche single (single-wpis_estates.php) n'enchaîne plus des get_template_part()
 * en dur : elle parcourt une liste de sections ordonnée et filtrable. Chaque agence
 * peut ainsi réordonner ou masquer des sections depuis « Réglages du thème →
 * Fiches de biens » (page d'options ACF), sans toucher au code.
 *
 * Contrat d'une section :
 *  - key   : identifiant stable (utilisé en base pour l'ordre) ;
 *  - label : libellé admin ;
 *  - part  : template-part rendu (doit s'auto-protéger si aucune donnée).
 *
 * Chaque template-part de section se charge lui-même de ne rien afficher quand le
 * bien n'a pas la donnée correspondante : ordre et visibilité restent donc sûrs.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Registre des sections (ordre par défaut = ordre de déclaration)
 * ---------------------------------------------------------------------- */

/**
 * Registre canonique des sections de la fiche bien.
 *
 * Filtrable pour qu'un thème enfant ajoute / retire une section.
 *
 * @return array<string,array{label:string,part:string}>
 */
function wpis_get_estate_section_registry() {
	$registry = array(
		'overview'  => array(
			'label' => __( 'Présentation & caractéristiques', 'hello-immosync' ),
			'part'  => 'template-parts/estate/details',
		),
		'amenities' => array(
			'label' => __( 'Équipements & commodités', 'hello-immosync' ),
			'part'  => 'template-parts/estate/amenities',
		),
		'areas'     => array(
			'label' => __( 'Surfaces & pièces', 'hello-immosync' ),
			'part'  => 'template-parts/estate/areas',
		),
		'energy'    => array(
			'label' => __( 'Énergie & PEB', 'hello-immosync' ),
			'part'  => 'template-parts/estate/energy',
		),
		'finance'   => array(
			'label' => __( 'Conditions financières', 'hello-immosync' ),
			'part'  => 'template-parts/estate/finance',
		),
		'lifestyle' => array(
			'label' => __( 'Quartier & environs', 'hello-immosync' ),
			'part'  => 'template-parts/estate/local-lifestyle',
		),
	);

	return apply_filters( 'wpis_estate_section_registry', $registry );
}

/**
 * Sections ordonnées et activées pour l'affichage.
 *
 * Lit l'ordre/visibilité depuis la page d'options (champ ACF « estate_sections »).
 * Toute section du registre absente des réglages enregistrés est ajoutée à la fin
 * (activée) : une nouvelle section livrée dans une mise à jour reste visible sans
 * intervention. Repli complet sur le registre si aucun réglage.
 *
 * @return array<string,array{label:string,part:string}>
 */
function wpis_get_estate_sections() {
	$registry = wpis_get_estate_section_registry();
	$rows     = wpis_theme_option( 'estate_sections', array() );

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

	// Sections du registre jamais réglées → ajoutées à la fin, visibles.
	foreach ( $registry as $key => $section ) {
		if ( ! isset( $seen[ $key ] ) ) {
			$ordered[ $key ] = $section;
		}
	}

	return apply_filters( 'wpis_estate_sections', $ordered );
}

/**
 * Rend les sections de la fiche bien, dans l'ordre réglé.
 *
 * @return void
 */
function wpis_render_estate_sections() {
	foreach ( wpis_get_estate_sections() as $key => $section ) {
		/**
		 * Permet de court-circuiter ou remplacer le rendu d'une section.
		 *
		 * @param bool   $render Rendre la section ?
		 * @param string $key    Clé de la section.
		 */
		if ( ! apply_filters( 'wpis_render_estate_section', true, $key ) ) {
			continue;
		}
		get_template_part( $section['part'], null, array( 'section_key' => $key ) );
	}
}

/* -------------------------------------------------------------------------
 * Page d'options « Fiches de biens » (ordre des sections)
 * ---------------------------------------------------------------------- */

add_action( 'acf/init', 'wpis_register_estate_sections_options', 11 );
/**
 * Sous-page d'options accrochée au menu « Réglages du thème » du parent.
 *
 * Priorité 11 : passe après wpis_register_options_pages() (priorité 10) qui crée
 * le menu top-level « wpis-theme-settings ».
 *
 * @return void
 */
function wpis_register_estate_sections_options() {
	if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
		return;
	}

	acf_add_options_sub_page(
		array(
			'page_title'  => __( 'Fiches de biens', 'hello-immosync' ),
			'menu_title'  => __( 'Fiches de biens', 'hello-immosync' ),
			'menu_slug'   => 'wpis-theme-estate',
			'parent_slug' => 'wpis-theme-settings',
		)
	);
}

add_action( 'acf/init', 'wpis_register_estate_sections_field_group', 11 );
/**
 * Groupe de champs de la page « Fiches de biens » : un répéteur triable par
 * glisser-déposer, une ligne par section (sélecteur de section + interrupteur).
 *
 * @return void
 */
function wpis_register_estate_sections_field_group() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_wpis_estate_sections',
			'title'    => __( 'Sections de la fiche de bien', 'hello-immosync' ),
			'fields'   => array(
				array(
					'key'     => 'field_wpis_estate_sections_msg',
					'label'   => '',
					'name'    => '',
					'type'    => 'message',
					'message' => __( 'Glissez-déposez les lignes pour définir l’ordre d’affichage des sections sur la fiche de bien. Décochez « Affichée » pour masquer une section. Une section reste automatiquement masquée quand le bien ne contient aucune donnée correspondante.', 'hello-immosync' ),
				),
				array(
					'key'          => 'field_wpis_estate_sections',
					'label'        => __( 'Ordre des sections', 'hello-immosync' ),
					'name'         => 'estate_sections',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => __( 'Ajouter une section', 'hello-immosync' ),
					'sub_fields'   => array(
						array(
							'key'      => 'field_wpis_estate_section_key',
							'label'    => __( 'Section', 'hello-immosync' ),
							'name'     => 'section',
							'type'     => 'select',
							'choices'  => array(), // Alimenté via acf/load_field depuis le registre.
							'required' => 1,
							'wrapper'  => array( 'width' => '70' ),
						),
						array(
							'key'           => 'field_wpis_estate_section_enabled',
							'label'         => __( 'Affichée', 'hello-immosync' ),
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
						'value'    => 'wpis-theme-estate',
					),
				),
			),
		)
	);
}

add_filter( 'acf/load_field/key=field_wpis_estate_section_key', 'wpis_acf_load_estate_section_choices' );
/**
 * Alimente le sélecteur de section avec les libellés du registre (source unique
 * de vérité — pas de liste figée à maintenir dans l'admin).
 *
 * @param array $field Champ ACF.
 * @return array
 */
function wpis_acf_load_estate_section_choices( $field ) {
	$field['choices'] = array();
	foreach ( wpis_get_estate_section_registry() as $key => $section ) {
		$field['choices'][ $key ] = $section['label'];
	}
	return $field;
}

add_filter( 'acf/load_value/key=field_wpis_estate_sections', 'wpis_acf_seed_estate_sections', 10, 3 );
/**
 * Pré-remplit le répéteur avec toutes les sections du registre (activées, dans
 * l'ordre par défaut) tant que rien n'a été enregistré : l'admin ouvre une page
 * déjà prête à réordonner, sans devoir ajouter les lignes une à une.
 *
 * @param mixed  $value   Valeur brute en base.
 * @param mixed  $post_id Contexte ACF (ici « option »).
 * @param array  $field   Champ ACF.
 * @return mixed
 */
function wpis_acf_seed_estate_sections( $value, $post_id, $field ) {
	if ( ! empty( $value ) ) {
		return $value;
	}

	$seed = array();
	foreach ( wpis_get_estate_section_registry() as $key => $section ) {
		$seed[] = array(
			'field_wpis_estate_section_key'     => $key,
			'field_wpis_estate_section_enabled' => 1,
		);
	}
	return $seed;
}
