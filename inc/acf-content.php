<?php
/**
 * Contenu éditable via ACF : helpers de lecture + enregistrement des champs.
 *
 * Les *définitions* de champs vivent ici (code, versionné dans le thème parent
 * générique). Le *contenu* saisi vit en base de données, par site. Tout est gardé
 * par function_exists() : si ACF est absent, les templates retombent sur leurs
 * chaînes par défaut (dégradation gracieuse).
 *
 * Deux couches :
 *  - contenu d'une page    → champs attachés à la page (wpis_page_field / wpis_home_field) ;
 *  - réglages transverses  → page d'options (wpis_theme_option).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Helpers de lecture (repli garanti)
 * ---------------------------------------------------------------------- */

/**
 * Vrai si une valeur ACF est « vide » (à remplacer par le repli).
 *
 * @param mixed $val Valeur renvoyée par get_field().
 * @return bool
 */
function wpis_field_is_empty( $val ) {
	return '' === $val || null === $val || false === $val || array() === $val;
}

/**
 * Lit un champ ACF attaché à une page, avec valeur de repli.
 *
 * @param string   $name    Nom du champ ACF.
 * @param mixed    $default Repli si vide / ACF absent.
 * @param int|null $post_id ID de la page (courant par défaut).
 * @return mixed
 */
function wpis_page_field( $name, $default = '', $post_id = null ) {
	if ( function_exists( 'get_field' ) ) {
		$val = get_field( $name, $post_id ? $post_id : get_the_ID() );
		if ( ! wpis_field_is_empty( $val ) ) {
			return $val;
		}
	}
	return $default;
}

/**
 * Lit un champ des sections de la page d'accueil (résout l'ID de la home).
 *
 * @param string $name    Nom du champ ACF.
 * @param mixed  $default Repli.
 * @return mixed
 */
function wpis_home_field( $name, $default = '' ) {
	return wpis_page_field( $name, $default, (int) get_option( 'page_on_front' ) );
}

/**
 * Lit un réglage global du thème (page d'options), avec repli.
 *
 * @param string $name    Nom du champ ACF.
 * @param mixed  $default Repli.
 * @return mixed
 */
function wpis_theme_option( $name, $default = '' ) {
	if ( function_exists( 'get_field' ) ) {
		$val = get_field( $name, 'option' );
		if ( ! wpis_field_is_empty( $val ) ) {
			return $val;
		}
	}
	return $default;
}

/* -------------------------------------------------------------------------
 * Page d'options « Réglages du thème » (parent) + accroche pour l'enfant
 * ---------------------------------------------------------------------- */

add_action( 'acf/init', 'wpis_register_options_pages' );
/**
 * Menu d'admin unique pour les réglages transverses du thème.
 *
 * Le parent enregistre le menu + sa sous-page. Le thème enfant peut accrocher
 * sa propre sous-page de personnalisation via acf_add_options_sub_page() avec
 * 'parent_slug' => 'wpis-theme-settings'.
 */
function wpis_register_options_pages() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'Réglages du thème', 'hello-immosync' ),
			'menu_title' => __( 'Réglages du thème', 'hello-immosync' ),
			'menu_slug'  => 'wpis-theme-settings',
			'capability' => 'edit_theme_options',
			'icon_url'   => 'dashicons-admin-customizer',
			'position'   => 59,
			'redirect'   => true, // Le top-level redirige vers la 1re sous-page.
		)
	);

	acf_add_options_sub_page(
		array(
			'page_title'  => __( 'Réglages généraux', 'hello-immosync' ),
			'menu_title'  => __( 'Réglages généraux', 'hello-immosync' ),
			'menu_slug'   => 'wpis-theme-general',
			'parent_slug' => 'wpis-theme-settings',
		)
	);
}

/* -------------------------------------------------------------------------
 * Définition des groupes de champs
 * ---------------------------------------------------------------------- */

add_action( 'acf/init', 'wpis_register_field_groups' );
/**
 * Enregistre tous les groupes de champs du thème (hero de page, sections home,
 * réglages globaux).
 */
function wpis_register_field_groups() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	// --- Groupe A : Hero de page (toutes les pages) ----------------------
	acf_add_local_field_group(
		array(
			'key'      => 'group_wpis_page_hero',
			'title'    => __( 'Hero de page', 'hello-immosync' ),
			'fields'   => array(
				array(
					'key'          => 'field_wpis_page_hero_eyebrow',
					'label'        => __( 'Sur-titre (eyebrow)', 'hello-immosync' ),
					'name'         => 'page_hero_eyebrow',
					'type'         => 'text',
					'instructions' => __( 'Petit texte au-dessus du titre. Laisser vide pour ne rien afficher.', 'hello-immosync' ),
				),
				array(
					'key'          => 'field_wpis_page_hero_titre',
					'label'        => __( 'Titre du hero', 'hello-immosync' ),
					'name'         => 'page_hero_titre',
					'type'         => 'text',
					'instructions' => __( 'Laisser vide pour utiliser le titre de la page.', 'hello-immosync' ),
				),
				array(
					'key'          => 'field_wpis_page_hero_sous_titre',
					'label'        => __( 'Sous-titre', 'hello-immosync' ),
					'name'         => 'page_hero_sous_titre',
					'type'         => 'textarea',
					'rows'         => 3,
					'new_lines'    => '',
					'instructions' => __( 'Phrase d’introduction sous le titre.', 'hello-immosync' ),
				),
				array(
					'key'     => 'field_wpis_page_hero_msg',
					'label'   => '',
					'name'    => '',
					'type'    => 'message',
					'message' => __( 'L’image de fond du hero = l’image mise en avant de la page (colonne de droite « Image mise en avant »).', 'hello-immosync' ),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'page',
					),
				),
			),
		)
	);

	// --- Groupe B : Page d'accueil — sections (front page) ---------------
	acf_add_local_field_group(
		array(
			'key'      => 'group_wpis_home_sections',
			'title'    => __( 'Page d’accueil — sections', 'hello-immosync' ),
			'fields'   => array(
				// Onglet : Find your place.
				array(
					'key'   => 'field_wpis_home_tab_fyp',
					'label' => __( 'Find your place', 'hello-immosync' ),
					'type'  => 'tab',
				),
				array(
					'key'   => 'field_wpis_home_fyp_eyebrow',
					'label' => __( 'Sur-titre', 'hello-immosync' ),
					'name'  => 'home_fyp_eyebrow',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_wpis_home_fyp_titre',
					'label' => __( 'Titre', 'hello-immosync' ),
					'name'  => 'home_fyp_titre',
					'type'  => 'text',
				),
				array(
					'key'       => 'field_wpis_home_fyp_texte',
					'label'     => __( 'Texte', 'hello-immosync' ),
					'name'      => 'home_fyp_texte',
					'type'      => 'textarea',
					'rows'      => 3,
					'new_lines' => '',
				),
				array(
					'key'           => 'field_wpis_home_fyp_cities_mode',
					'label'         => __( 'Villes affichées', 'hello-immosync' ),
					'name'          => 'home_fyp_cities_mode',
					'type'          => 'radio',
					'choices'       => array(
						'auto'   => __( 'Automatique (les plus fréquentes)', 'hello-immosync' ),
						'manual' => __( 'Je choisis les villes', 'hello-immosync' ),
					),
					'default_value' => 'auto',
					'layout'        => 'horizontal',
				),
				array(
					'key'               => 'field_wpis_home_fyp_cities',
					'label'             => __( 'Villes', 'hello-immosync' ),
					'name'              => 'home_fyp_cities',
					'type'              => 'select',
					'multiple'          => 1,
					'ui'                => 1,
					'choices'           => array(), // Alimenté dynamiquement (acf/load_field).
					'instructions'      => __( 'Choisis les villes à mettre en avant (liste alimentée par les biens existants).', 'hello-immosync' ),
					'conditional_logic' => array(
						array(
							array(
								'field'    => 'field_wpis_home_fyp_cities_mode',
								'operator' => '==',
								'value'    => 'manual',
							),
						),
					),
				),
				// Onglet : Art de vivre.
				array(
					'key'   => 'field_wpis_home_tab_life',
					'label' => __( 'Art de vivre', 'hello-immosync' ),
					'type'  => 'tab',
				),
				array(
					'key'   => 'field_wpis_home_life_eyebrow',
					'label' => __( 'Sur-titre', 'hello-immosync' ),
					'name'  => 'home_lifestyle_eyebrow',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_wpis_home_life_titre',
					'label' => __( 'Titre', 'hello-immosync' ),
					'name'  => 'home_lifestyle_titre',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_wpis_home_life_cards',
					'label'        => __( 'Cartes', 'hello-immosync' ),
					'name'         => 'home_lifestyle_cards',
					'type'         => 'repeater',
					'instructions' => __( 'Laisser vide pour utiliser les 3 cartes par défaut.', 'hello-immosync' ),
					'layout'       => 'block',
					'button_label' => __( 'Ajouter une carte', 'hello-immosync' ),
					'sub_fields'   => array(
						array(
							'key'           => 'field_wpis_home_life_card_icon',
							'label'         => __( 'Icône', 'hello-immosync' ),
							'name'          => 'icon',
							'type'          => 'select',
							'choices'       => array(
								'location' => 'location',
								'compass'  => 'compass',
								'energy'   => 'energy',
								'calendar' => 'calendar',
								'area'     => 'area',
								'mail'     => 'mail',
								'phone'    => 'phone',
							),
							'default_value' => 'location',
						),
						array(
							'key'   => 'field_wpis_home_life_card_eyebrow',
							'label' => __( 'Sur-titre', 'hello-immosync' ),
							'name'  => 'eyebrow',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_wpis_home_life_card_titre',
							'label' => __( 'Titre', 'hello-immosync' ),
							'name'  => 'title',
							'type'  => 'text',
						),
						array(
							'key'       => 'field_wpis_home_life_card_texte',
							'label'     => __( 'Texte', 'hello-immosync' ),
							'name'      => 'text',
							'type'      => 'textarea',
							'rows'      => 3,
							'new_lines' => '',
						),
					),
				),
				// Onglet : Off-market.
				array(
					'key'   => 'field_wpis_home_tab_off',
					'label' => __( 'Off-market', 'hello-immosync' ),
					'type'  => 'tab',
				),
				array(
					'key'   => 'field_wpis_home_off_eyebrow',
					'label' => __( 'Sur-titre', 'hello-immosync' ),
					'name'  => 'home_offmarket_eyebrow',
					'type'  => 'text',
				),
				array(
					'key'       => 'field_wpis_home_off_titre',
					'label'     => __( 'Titre', 'hello-immosync' ),
					'name'      => 'home_offmarket_titre',
					'type'      => 'textarea',
					'rows'      => 2,
					'new_lines' => '',
				),
				array(
					'key'       => 'field_wpis_home_off_texte',
					'label'     => __( 'Texte', 'hello-immosync' ),
					'name'      => 'home_offmarket_texte',
					'type'      => 'textarea',
					'rows'      => 3,
					'new_lines' => '',
				),
				array(
					'key'   => 'field_wpis_home_off_bouton',
					'label' => __( 'Libellé du bouton', 'hello-immosync' ),
					'name'  => 'home_offmarket_bouton',
					'type'  => 'text',
				),
				// Onglet : L'agence.
				array(
					'key'   => 'field_wpis_home_tab_about',
					'label' => __( 'L’agence', 'hello-immosync' ),
					'type'  => 'tab',
				),
				array(
					'key'   => 'field_wpis_home_about_eyebrow',
					'label' => __( 'Sur-titre', 'hello-immosync' ),
					'name'  => 'home_about_eyebrow',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_wpis_home_about_titre',
					'label' => __( 'Titre', 'hello-immosync' ),
					'name'  => 'home_about_titre',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_wpis_home_about_texte',
					'label'        => __( 'Texte de présentation', 'hello-immosync' ),
					'name'         => 'home_about_texte',
					'type'         => 'wysiwyg',
					'tabs'         => 'visual',
					'toolbar'      => 'basic', // Gras, italique, lien, listes — pas de titres.
					'media_upload' => 0,
					'instructions' => __( 'Éditeur enrichi (toolbar basique). Le style premium reste géré par le thème.', 'hello-immosync' ),
				),
				array(
					'key'          => 'field_wpis_home_about_image',
					'label'        => __( 'Image', 'hello-immosync' ),
					'name'         => 'home_about_image',
					'type'         => 'image',
					'return_format' => 'id',
					'preview_size' => 'medium',
					'library'      => 'all',
					'instructions' => __( 'Laisser vide pour conserver le dégradé par défaut.', 'hello-immosync' ),
				),
				array(
					'key'   => 'field_wpis_home_about_bouton',
					'label' => __( 'Libellé du bouton', 'hello-immosync' ),
					'name'  => 'home_about_bouton',
					'type'  => 'text',
				),
				// Onglet : CTA vendeur.
				array(
					'key'   => 'field_wpis_home_tab_cta',
					'label' => __( 'CTA vendeur', 'hello-immosync' ),
					'type'  => 'tab',
				),
				array(
					'key'   => 'field_wpis_home_cta_eyebrow',
					'label' => __( 'Sur-titre', 'hello-immosync' ),
					'name'  => 'home_cta_eyebrow',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_wpis_home_cta_titre',
					'label' => __( 'Titre', 'hello-immosync' ),
					'name'  => 'home_cta_titre',
					'type'  => 'text',
				),
				array(
					'key'       => 'field_wpis_home_cta_texte',
					'label'     => __( 'Texte', 'hello-immosync' ),
					'name'      => 'home_cta_texte',
					'type'      => 'textarea',
					'rows'      => 3,
					'new_lines' => '',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
		)
	);

	// --- Groupe D : Réglages du thème (page d'options) -------------------
	acf_add_local_field_group(
		array(
			'key'      => 'group_wpis_theme_settings',
			'title'    => __( 'Réglages généraux', 'hello-immosync' ),
			'fields'   => array(
				array(
					'key'   => 'field_wpis_opt_phone',
					'label' => __( 'Téléphone', 'hello-immosync' ),
					'name'  => 'option_agency_phone',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_wpis_opt_email',
					'label' => __( 'E-mail de contact', 'hello-immosync' ),
					'name'  => 'option_agency_email',
					'type'  => 'email',
					'instructions' => __( 'Laisser vide pour utiliser l’e-mail d’administration.', 'hello-immosync' ),
				),
				array(
					'key'          => 'field_wpis_opt_socials',
					'label'        => __( 'Réseaux sociaux', 'hello-immosync' ),
					'name'         => 'option_socials',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => __( 'Ajouter un réseau', 'hello-immosync' ),
					'sub_fields'   => array(
						array(
							'key'   => 'field_wpis_opt_social_label',
							'label' => __( 'Nom', 'hello-immosync' ),
							'name'  => 'label',
							'type'  => 'text',
						),
						array(
							'key'   => 'field_wpis_opt_social_url',
							'label' => __( 'URL', 'hello-immosync' ),
							'name'  => 'url',
							'type'  => 'url',
						),
					),
				),
				array(
					'key'       => 'field_wpis_opt_footer_note',
					'label'     => __( 'Note de pied de page', 'hello-immosync' ),
					'name'      => 'option_footer_note',
					'type'      => 'textarea',
					'rows'      => 2,
					'new_lines' => '',
				),
				array(
					'key'           => 'field_wpis_opt_hero_fallback',
					'label'         => __( 'Image de repli du hero', 'hello-immosync' ),
					'name'          => 'option_hero_fallback',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'medium',
					'instructions'  => __( 'Image de fond utilisée quand une page n’a pas d’image mise en avant.', 'hello-immosync' ),
				),
				array(
					'key'     => 'field_wpis_opt_schema_msg',
					'label'   => '',
					'name'    => '',
					'type'    => 'message',
					'message' => __( 'SEO — données structurées', 'hello-immosync' ),
				),
				array(
					'key'           => 'field_wpis_opt_schema_estates',
					'label'         => __( 'Données structurées des biens (Schema.org)', 'hello-immosync' ),
					'name'          => 'option_schema_estates',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 1,
					'instructions'  => __( '⚠️ Émet le JSON-LD « Residence » sur les fiches biens. À DÉSACTIVER si un plugin SEO (RankMath, Yoast…) gère déjà le schéma des biens, pour éviter un doublon de données structurées.', 'hello-immosync' ),
				),
				array(
					'key'     => 'field_wpis_opt_ipi_msg',
					'label'   => '',
					'name'    => '',
					'type'    => 'message',
					'message' => __( 'Mentions légales IPI (agences immobilières belges). Le bloc n’apparaît dans le footer que si le numéro IPI est renseigné.', 'hello-immosync' ),
				),
				array(
					'key'   => 'field_wpis_opt_ipi_number',
					'label' => __( 'Numéro d’agrément IPI', 'hello-immosync' ),
					'name'  => 'option_ipi_number',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_wpis_opt_company_vat',
					'label'        => __( 'N° d’entreprise / TVA', 'hello-immosync' ),
					'name'         => 'option_company_vat',
					'type'         => 'text',
					'instructions' => __( 'Ex. : BE 0123.456.789', 'hello-immosync' ),
				),
				array(
					'key'           => 'field_wpis_opt_insurer',
					'label'         => __( 'Assureur RC professionnelle', 'hello-immosync' ),
					'name'          => 'option_insurer',
					'type'          => 'text',
					'default_value' => 'AXA Belgium SA.',
				),
				array(
					'key'   => 'field_wpis_opt_insurance_policy',
					'label' => __( 'N° de police d’assurance', 'hello-immosync' ),
					'name'  => 'option_insurance_policy',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_wpis_opt_rgpd_manager',
					'label' => __( 'Responsable RGPD / anti-blanchiment', 'hello-immosync' ),
					'name'  => 'option_rgpd_manager',
					'type'  => 'text',
				),
				array(
					'key'           => 'field_wpis_opt_rgpd_role',
					'label'         => __( 'Fonction du responsable', 'hello-immosync' ),
					'name'          => 'option_rgpd_manager_role',
					'type'          => 'text',
					'default_value' => 'gérant',
				),
				array(
					'key'           => 'field_wpis_opt_ipi_logo',
					'label'         => __( 'Logo IPI (officiel)', 'hello-immosync' ),
					'name'          => 'option_ipi_logo',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'thumbnail',
					'instructions'  => __( 'Laisser vide pour utiliser le logo IPI/BIV fourni par le thème.', 'hello-immosync' ),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'wpis-theme-general',
					),
				),
			),
		)
	);
}

add_filter( 'acf/load_field/key=field_wpis_home_fyp_cities', 'wpis_acf_load_cities_choices' );
/**
 * Alimente dynamiquement le champ « Villes » avec les villes réellement
 * présentes dans les biens (évite tout retour au code pour ajouter une ville).
 *
 * @param array $field Champ ACF.
 * @return array
 */
function wpis_acf_load_cities_choices( $field ) {
	$field['choices'] = array();
	if ( function_exists( 'wpis_get_filter_options' ) ) {
		$opts = wpis_get_filter_options();
		if ( ! empty( $opts['cities'] ) ) {
			foreach ( $opts['cities'] as $wpis_city ) {
				$field['choices'][ $wpis_city ] = $wpis_city;
			}
		}
	}
	return $field;
}
