<?php
/**
 * Coordonnées de l'agence : adresse (+ itinéraire), téléphone, e-mail, horaires,
 * accès & stationnement. Données : Réglages du thème → Réglages généraux
 * (wpis_get_agency_info()). Chaque bloc s'efface s'il est vide.
 *
 * @package HelloImmoSync
 *
 * @var array $args Optionnel : ['schema' => bool] — émet aussi le JSON-LD
 *                  RealEstateAgent (à réserver à une page, ex. Contact).
 */

defined( 'ABSPATH' ) || exit;

$wpis_agency = wpis_get_agency_info();

if ( ! empty( $args['schema'] ) ) {
	wpis_output_agency_jsonld( $wpis_agency );
}

$wpis_directions = '';
if ( null !== $wpis_agency['lat'] && null !== $wpis_agency['lng'] ) {
	$wpis_directions = 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $wpis_agency['lat'] . ',' . $wpis_agency['lng'] );
} elseif ( $wpis_agency['address'] ) {
	$wpis_directions = 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( implode( ', ', $wpis_agency['address'] ) );
}
?>
<div class="wpis-agency-info space-y-8">

	<?php if ( $wpis_agency['address'] ) : ?>
		<div>
			<p class="wpis-agency-info-title"><?php echo wpis_icon( 'location', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Adresse', 'hello-immosync' ); ?></p>
			<address class="not-italic text-text">
				<?php echo implode( '<br>', array_map( 'esc_html', $wpis_agency['address'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</address>
			<?php if ( $wpis_directions ) : ?>
				<a href="<?php echo esc_url( $wpis_directions ); ?>" class="wpis-btn-ghost mt-3 text-xs" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Itinéraire', 'hello-immosync' ); ?>
					<?php echo wpis_icon( 'arrow', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( '' !== $wpis_agency['phone'] || '' !== $wpis_agency['email'] ) : ?>
		<div>
			<p class="wpis-agency-info-title"><?php echo wpis_icon( 'phone', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Nous joindre', 'hello-immosync' ); ?></p>
			<?php if ( '' !== $wpis_agency['phone'] ) : ?>
				<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $wpis_agency['phone'] ) ); ?>" class="block text-text transition-colors hover:text-brand-strong"><?php echo esc_html( $wpis_agency['phone'] ); ?></a>
			<?php endif; ?>
			<?php if ( '' !== $wpis_agency['email'] ) : ?>
				<a href="mailto:<?php echo esc_attr( $wpis_agency['email'] ); ?>" class="block text-text transition-colors hover:text-brand-strong"><?php echo esc_html( $wpis_agency['email'] ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( $wpis_agency['hours'] ) : ?>
		<div>
			<p class="wpis-agency-info-title"><?php echo wpis_icon( 'clock', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Horaires', 'hello-immosync' ); ?></p>
			<dl class="space-y-1">
				<?php foreach ( $wpis_agency['hours'] as $wpis_row ) : ?>
					<div class="flex flex-wrap justify-between gap-x-6 text-text">
						<dt><?php echo esc_html( $wpis_row['days'] ); ?></dt>
						<dd class="text-text-strong"><?php echo esc_html( $wpis_row['hours'] ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>
	<?php endif; ?>

	<?php if ( '' !== $wpis_agency['access'] ) : ?>
		<div>
			<p class="wpis-agency-info-title"><?php echo wpis_icon( 'car', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Accès & stationnement', 'hello-immosync' ); ?></p>
			<p class="text-text"><?php echo nl2br( esc_html( $wpis_agency['access'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
		</div>
	<?php endif; ?>

</div>
