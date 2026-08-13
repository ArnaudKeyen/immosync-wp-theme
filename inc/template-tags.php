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
 * Retourne le markup du badge de statut d'un bien.
 *
 * On affiche toujours le statut commercial (Nouveau, Option, Sous compromis,
 * Vendu…) et jamais le type d'opération : celui-ci est déjà porté par le titre
 * normalisé (« Appartement à vendre à Uccle »), alors que le statut est la
 * seule information qui distingue deux biens du même listing.
 *
 * @param int|null $post_id ID du bien.
 * @return string
 */
function wpis_estate_badges( $post_id = null ) {
	$status = wpis_get_status( $post_id );
	if ( '' === $status ) {
		// Filet de sécurité : certains flux ne renseignent que l'opération.
		$status = wpis_get_purpose( $post_id );
	}
	if ( '' === $status ) {
		return '';
	}

	$variants = array(
		'sold'    => 'wpis-badge-sold',
		'pending' => 'wpis-badge-pending',
	);
	$level    = wpis_get_status_level( $post_id );
	$variant  = isset( $variants[ $level ] ) ? $variants[ $level ] : 'wpis-badge-brand';

	return '<span class="wpis-badge ' . esc_attr( $variant ) . '">' . esc_html( $status ) . '</span>';
}

/**
 * Titre d'affichage du bien.
 *
 * Le titre WordPress est prioritaire : wpis-post-update.php le normalise en
 * « Catégorie transaction à Ville » à chaque synchro. On retombe ensuite sur le
 * titre éditorial du flux, puis en dernier recours sur wpis_name, qui n'est
 * qu'un nom interne au logiciel immo (« Demo Duplex »).
 *
 * @param int|null $post_id ID du bien.
 * @return string
 */
function wpis_get_title( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	$title = trim( (string) get_the_title( $post_id ) );
	if ( '' === $title ) {
		$title = wpis_get_field( 'wpis_description_title', $post_id, '' );
	}
	if ( '' === $title ) {
		$title = wpis_get_field( 'wpis_name', $post_id, '' );
	}

	return $title;
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
