<?php
/**
 * Bandeau d'accès médias de la fiche bien : visite virtuelle, vidéo, rendez-vous.
 *
 * Affordance secondaire (icônes) commune à toutes les variantes de hero. S'auto-
 * masque si le bien n'a aucun lien.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_links = wpis_get_links( get_the_ID() );

if ( ! $wpis_links ) {
	return;
}
?>
<div class="mt-8 border-y border-line bg-cream">
	<div class="wpis-container-wide flex flex-wrap gap-3 py-4">
		<?php if ( ! empty( $wpis_links['virtualVisit'] ) ) : ?>
			<a href="<?php echo esc_url( $wpis_links['virtualVisit'] ); ?>" target="_blank" rel="noopener" class="wpis-btn-outline text-xs">
				<?php echo wpis_icon( 'cube', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php esc_html_e( 'Visite virtuelle', 'hello-immosync' ); ?>
			</a>
		<?php endif; ?>
		<?php if ( ! empty( $wpis_links['video'] ) ) : ?>
			<a href="<?php echo esc_url( $wpis_links['video'] ); ?>" target="_blank" rel="noopener" class="wpis-btn-outline text-xs">
				<?php echo wpis_icon( 'video', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php esc_html_e( 'Vidéo', 'hello-immosync' ); ?>
			</a>
		<?php endif; ?>
		<?php if ( ! empty( $wpis_links['appointment'] ) ) : ?>
			<a href="<?php echo esc_url( $wpis_links['appointment'] ); ?>" target="_blank" rel="noopener" class="wpis-btn-outline text-xs">
				<?php echo wpis_icon( 'calendar', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php esc_html_e( 'Prendre rendez-vous', 'hello-immosync' ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
