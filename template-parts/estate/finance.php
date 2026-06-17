<?php
/**
 * Section « Conditions financières » de la fiche bien.
 *
 * Regroupe les données financières secondaires (hors prix principal affiché dans
 * le hero / la colonne latérale) : charges, revenu cadastral, précompte, prix
 * unitaires garage / parking, disponibilité.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid     = get_the_ID();
$wpis_finance = wpis_get_finance_details( $wpis_pid );

if ( ! $wpis_finance ) {
	return;
}
?>
<section class="wpis-section border-b border-line" aria-labelledby="wpis-finance-title">
	<p class="wpis-eyebrow mb-2"><?php esc_html_e( 'Aspects financiers', 'hello-immosync' ); ?></p>
	<h2 id="wpis-finance-title" class="font-display text-3xl text-ink"><?php esc_html_e( 'Conditions financières', 'hello-immosync' ); ?></h2>

	<dl class="mt-8 grid grid-cols-1 gap-x-12 sm:grid-cols-2">
		<?php foreach ( $wpis_finance as $wpis_row ) : ?>
			<div class="wpis-spec">
				<dt class="wpis-spec-label"><?php echo esc_html( $wpis_row['label'] ); ?></dt>
				<dd class="wpis-spec-value"><?php echo esc_html( $wpis_row['value'] ); ?></dd>
			</div>
		<?php endforeach; ?>
	</dl>

	<p class="mt-6 max-w-2xl font-body text-xs text-stone">
		<?php esc_html_e( 'Informations financières communiquées à titre indicatif et sans valeur contractuelle. Renseignez-vous auprès de l’agence pour les conditions exactes.', 'hello-immosync' ); ?>
	</p>
</section>
