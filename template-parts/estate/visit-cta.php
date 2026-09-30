<?php
/**
 * Bouton « Demander une visite » de l'en-tête de fiche.
 *
 * Action principale, visible dès le haut de la fiche (toutes variantes de hero) :
 * descend jusqu'au formulaire de la colonne contact (#wpis-contact,
 * template-parts/estate/contact-agent.php) — un seul formulaire par fiche.
 * Masqué pour un bien vendu / loué.
 *
 * @package HelloImmoSync
 *
 * @var array $args Optionnel : ['class' => string] (classe du bouton, défaut wpis-btn).
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid = get_the_ID();
if ( wpis_is_sold( $wpis_pid ) ) {
	return;
}

/**
 * Libellé du bouton de demande de visite en haut de fiche.
 *
 * @param string $label   Libellé.
 * @param int    $post_id ID du bien.
 */
$wpis_label = apply_filters( 'wpis_estate_visit_label', __( 'Demander une visite', 'hello-immosync' ), $wpis_pid );
$wpis_class = ! empty( $args['class'] ) ? $args['class'] : 'wpis-btn';
?>
<a href="#wpis-contact" class="wpis-visit-cta <?php echo esc_attr( $wpis_class ); ?>">
	<?php echo wpis_icon( 'calendar', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php echo esc_html( $wpis_label ); ?>
</a>
