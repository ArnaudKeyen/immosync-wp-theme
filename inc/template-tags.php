<?php
/**
 * Helpers de rendu (cartes, badges, formulaire de recherche).
 *
 * Ces fonctions délèguent à des template-parts surchargables par le thème enfant.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

/**
 * Affiche une carte de bien (dans une boucle WordPress).
 *
 * @param array $args Arguments transmis au template-part.
 * @return void
 */
function wpis_estate_card( $args = array() ) {
	get_template_part( 'template-parts/estate/card', null, $args );
}

/**
 * Affiche le formulaire de recherche immobilier.
 *
 * @param array $args Arguments transmis au template-part.
 * @return void
 */
function wpis_search_form( $args = array() ) {
	get_template_part( 'template-parts/global/search-bar', null, $args );
}

/**
 * Retourne le markup des badges de statut d'un bien (opération + vendu).
 *
 * @param int|null $post_id ID du bien.
 * @return string
 */
function wpis_estate_badges( $post_id = null ) {
	$badges  = '';
	$purpose = wpis_get_purpose( $post_id );
	$sold    = wpis_is_sold( $post_id );

	if ( $sold ) {
		$badges .= '<span class="wpis-badge wpis-badge-sold">' . esc_html( wpis_get_status( $post_id ) ) . '</span>';
	} elseif ( '' !== $purpose ) {
		$badges .= '<span class="wpis-badge wpis-badge-brand">' . esc_html( $purpose ) . '</span>';
	}

	return $badges;
}

/**
 * Titre d'affichage du bien (nom WPIS, sinon titre WordPress).
 *
 * @param int|null $post_id ID du bien.
 * @return string
 */
function wpis_get_title( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$name    = wpis_get_field( 'wpis_name', $post_id, '' );
	return '' !== $name ? $name : get_the_title( $post_id );
}

/**
 * Description courte (champ WPIS dédié, sinon extrait).
 *
 * @param int|null $post_id ID du bien.
 * @param int      $words   Longueur max en mots.
 * @return string
 */
function wpis_get_excerpt( $post_id = null, $words = 24 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	$text = wpis_get_field( 'wpis_description_short', $post_id, '' );
	if ( '' === $text ) {
		$text = wpis_get_field( 'wpis_description_base', $post_id, '' );
	}
	if ( '' === $text ) {
		$text = get_the_excerpt( $post_id );
	}
	$text = wp_strip_all_tags( $text );
	return wp_trim_words( $text, $words, '…' );
}
