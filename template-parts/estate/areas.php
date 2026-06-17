<?php
/**
 * Section « Surfaces & pièces » de la fiche bien.
 *
 * Détaille toutes les surfaces WPIS renseignées (wpis_areas_*), au-delà des
 * surfaces clés déjà mises en avant dans la grille de caractéristiques.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid   = get_the_ID();
$wpis_areas = wpis_get_area_breakdown( $wpis_pid );

if ( ! $wpis_areas ) {
	return;
}
?>
<section class="wpis-section border-b border-line" aria-labelledby="wpis-areas-title">
	<p class="wpis-eyebrow mb-2"><?php esc_html_e( 'Métré', 'hello-immosync' ); ?></p>
	<h2 id="wpis-areas-title" class="font-display text-3xl text-ink"><?php esc_html_e( 'Surfaces & pièces', 'hello-immosync' ); ?></h2>

	<dl class="mt-8 grid grid-cols-1 gap-x-12 sm:grid-cols-2">
		<?php foreach ( $wpis_areas as $wpis_area ) : ?>
			<div class="wpis-spec">
				<dt class="wpis-spec-label flex items-center gap-2">
					<span class="text-brand"><?php echo wpis_icon( 'area', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG inline du thème. ?></span>
					<?php echo esc_html( $wpis_area['label'] ); ?>
				</dt>
				<dd class="wpis-spec-value"><?php echo esc_html( $wpis_area['value'] ); ?></dd>
			</div>
		<?php endforeach; ?>
	</dl>
</section>
