<?php
/**
 * En-tête (hero) de la fiche bien : variantes de mise en page.
 *
 * Même philosophie que les sections modulables (inc/estate-sections.php) : un
 * registre canonique de variantes + un choix unique (global au site) réglé depuis
 * « Réglages du thème → Fiches de biens ». La fiche single n'appelle plus un hero
 * en dur mais wpis_render_estate_hero(), qui rend la variante choisie.
 *
 * Contrat d'une variante :
 *  - label : libellé admin ;
 *  - part  : template-part rendu (doit s'auto-protéger si aucune image).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Registre des variantes de hero
 * ---------------------------------------------------------------------- */

/**
 * Registre canonique des variantes d'en-tête. Filtrable (thème enfant).
 *
 * @return array<string,array{label:string,part:string}>
 */
function wpis_get_estate_hero_registry() {
	$registry = array(
		'stacked' => array(
			'label' => __( 'Titre au-dessus + galerie mosaïque (par défaut)', 'hello-immosync' ),
			'part'  => 'template-parts/estate/hero-stacked',
		),
		'split'   => array(
			'label' => __( 'Grande photo plein cadre + médias (vidéo / 360)', 'hello-immosync' ),
			'part'  => 'template-parts/estate/hero-split',
		),
	);

	return apply_filters( 'wpis_estate_hero_registry', $registry );
}

/**
 * Variante d'en-tête active (choix global réglé sur la page d'options).
 *
 * Repli sur « stacked » si la valeur enregistrée n'existe plus dans le registre.
 *
 * @return string Clé de variante.
 */
function wpis_get_estate_hero_variant() {
	$registry = wpis_get_estate_hero_registry();
	$variant  = wpis_theme_option( 'estate_hero_variant', 'stacked' );

	if ( ! is_string( $variant ) || ! isset( $registry[ $variant ] ) ) {
		$variant = 'stacked';
	}

	return apply_filters( 'wpis_estate_hero_variant', $variant );
}

/**
 * Rend l'en-tête de la fiche selon la variante choisie.
 *
 * @return void
 */
function wpis_render_estate_hero() {
	$registry = wpis_get_estate_hero_registry();
	$variant  = wpis_get_estate_hero_variant();
	$part     = isset( $registry[ $variant ] ) ? $registry[ $variant ]['part'] : 'template-parts/estate/hero-stacked';

	get_template_part( $part, null, array( 'hero_variant' => $variant ) );
}

/* -------------------------------------------------------------------------
 * Slots médias du hero (logique automatique)
 * ---------------------------------------------------------------------- */

/**
 * Résout les tuiles médias affichées à côté de la grande photo (variante split).
 *
 * Logique automatique, sans réglage : la vidéo et la visite virtuelle prennent
 * la place des 2e/3e photos quand elles existent (avec la photo correspondante
 * en poster). Sinon ce sont simplement les photos suivantes de la galerie.
 *
 * Chaque slot :
 *  - type          : 'photo' | 'video' | 'tour' ;
 *  - image_id      : pièce jointe servant de visuel/poster ;
 *  - url           : URL média (vidéo/visite) — vide pour une photo ;
 *  - gallery_index : index dans la galerie (photos uniquement → ouverture modal).
 *
 * @param int|null $post_id ID du bien.
 * @return array<int,array<string,mixed>>
 */
function wpis_get_hero_media_slots( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$gallery = wpis_get_gallery( $post_id );
	$links   = wpis_get_links( $post_id );

	// Poster de repli : la photo demandée, sinon la grande image.
	$poster = static function ( $index ) use ( $gallery ) {
		if ( isset( $gallery[ $index ] ) ) {
			return (int) $gallery[ $index ];
		}
		return isset( $gallery[0] ) ? (int) $gallery[0] : 0;
	};

	$slots = array();

	// Slot 1 → vidéo si dispo, sinon 2e photo (index 1).
	if ( ! empty( $links['video'] ) ) {
		$slots[] = array(
			'type'     => 'video',
			'image_id' => $poster( 1 ),
			'url'      => $links['video'],
		);
	} elseif ( isset( $gallery[1] ) ) {
		$slots[] = array(
			'type'          => 'photo',
			'image_id'      => (int) $gallery[1],
			'url'           => '',
			'gallery_index' => 1,
		);
	}

	// Slot 2 → visite virtuelle si dispo, sinon 3e photo (index 2).
	if ( ! empty( $links['virtualVisit'] ) ) {
		$slots[] = array(
			'type'     => 'tour',
			'image_id' => $poster( 2 ),
			'url'      => $links['virtualVisit'],
		);
	} elseif ( isset( $gallery[2] ) ) {
		$slots[] = array(
			'type'          => 'photo',
			'image_id'      => (int) $gallery[2],
			'url'           => '',
			'gallery_index' => 2,
		);
	}

	return apply_filters( 'wpis_hero_media_slots', $slots, $post_id );
}

/**
 * Markup d'embed d'un média (vidéo ou visite), enveloppé pour un ratio responsive.
 *
 * Vidéo : oEmbed natif WordPress (YouTube, Vimeo, Dailymotion…). Visite / 360 :
 * iframe directe (Matterport, visionneuses 360…). Rien si l'URL est vide ou non
 * embarquable.
 *
 * @param string $url  URL du média.
 * @param string $type 'video' | 'tour'.
 * @return string Markup HTML (vide si rien à embarquer).
 */
function wpis_media_embed_html( $url, $type = 'video' ) {
	$url = esc_url_raw( $url );
	if ( '' === $url ) {
		return '';
	}

	$src = '';

	if ( 'video' === $type ) {
		// YouTube : construit l'URL d'embed (fiable sans dépendre de l'oEmbed serveur).
		if ( preg_match( '~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~i', $url, $m ) ) {
			$src = 'https://www.youtube.com/embed/' . $m[1] . '?rel=0';
		} elseif ( preg_match( '~vimeo\.com/(?:video/)?(\d+)~i', $url, $m ) ) {
			$src = 'https://player.vimeo.com/video/' . $m[1];
		} else {
			// Autres fournisseurs : oEmbed natif WordPress.
			$oembed = wp_oembed_get( $url, array( 'width' => 1280 ) );
			if ( $oembed ) {
				return '<div class="wpis-embed-frame">' . $oembed . '</div>';
			}
		}
	}

	if ( '' === $src ) {
		$src = $url; // Visite virtuelle / 360 / repli : iframe directe.
	}

	return '<div class="wpis-embed-frame"><iframe src="' . esc_url( $src ) . '" title="' . esc_attr__( 'Média du bien', 'hello-immosync' ) . '" frameborder="0" allow="autoplay; fullscreen; xr-spatial-tracking" allowfullscreen loading="lazy"></iframe></div>';
}

/* -------------------------------------------------------------------------
 * Réglage « Mise en page de l'en-tête » (page d'options « Fiches de biens »)
 * ---------------------------------------------------------------------- */

add_action( 'acf/init', 'wpis_register_estate_hero_field_group', 11 );
/**
 * Sélecteur de variante d'en-tête, posé sur la page d'options « Fiches de biens »
 * (au-dessus du répéteur d'ordre des sections via menu_order négatif).
 *
 * @return void
 */
function wpis_register_estate_hero_field_group() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'        => 'group_wpis_estate_hero',
			'title'      => __( 'En-tête de la fiche de bien', 'hello-immosync' ),
			'fields'     => array(
				array(
					'key'           => 'field_wpis_estate_hero_variant',
					'label'         => __( 'Mise en page de l’en-tête', 'hello-immosync' ),
					'name'          => 'estate_hero_variant',
					'type'          => 'select',
					'instructions'  => __( 'Disposition de l’en-tête (titre, prix, galerie) appliquée à toutes les fiches de biens. La variante « plein cadre » promeut automatiquement la vidéo et la visite virtuelle en grandes tuiles cliquables quand elles existent.', 'hello-immosync' ),
					'choices'       => array(), // Alimenté via acf/load_field depuis le registre.
					'default_value' => 'stacked',
					'return_format' => 'value',
					'allow_null'    => 0,
					'ui'            => 1,
				),
			),
			'location'   => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'wpis-theme-estate',
					),
				),
			),
			'menu_order' => -5,
		)
	);
}

add_filter( 'acf/load_field/key=field_wpis_estate_hero_variant', 'wpis_acf_load_estate_hero_choices' );
/**
 * Alimente le sélecteur de variante avec les libellés du registre (source unique).
 *
 * @param array $field Champ ACF.
 * @return array
 */
function wpis_acf_load_estate_hero_choices( $field ) {
	$field['choices'] = array();
	foreach ( wpis_get_estate_hero_registry() as $key => $variant ) {
		$field['choices'][ $key ] = $variant['label'];
	}
	return $field;
}
