<?php
/**
 * Bloc « quartier & environs » : carte de géolocalisation + proximités.
 *
 * Le texte éditorial par défaut est volontairement neutre (factuel) :
 * il décrit l'environnement du bien et peut être personnalisé par l'agence
 * via une surcharge de ce template-part dans le thème enfant.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid    = get_the_ID();
$wpis_coords = wpis_get_map_coords( $wpis_pid );
$wpis_city   = wpis_get_field( 'wpis_address_city', $wpis_pid, '' );

if ( ! $wpis_coords && '' === $wpis_city ) {
	return;
}

// Proximités renseignées par ImmoSync (distances formatées, affichées si présentes).
$wpis_proximities = wpis_get_proximities( $wpis_pid );
?>
<section class="wpis-section border-b border-line" aria-labelledby="wpis-lifestyle-title">
	<p class="wpis-eyebrow mb-2"><?php esc_html_e( 'Le quartier', 'hello-immosync' ); ?></p>
	<h2 id="wpis-lifestyle-title" class="font-display text-3xl text-ink">
		<?php
		if ( $wpis_city ) {
			printf(
				/* translators: %s: city name. */
				esc_html__( '%s et ses environs', 'hello-immosync' ),
				esc_html( $wpis_city )
			);
		} else {
			esc_html_e( 'Aux alentours', 'hello-immosync' );
		}
		?>
	</h2>

	<div class="mt-8 grid gap-10 lg:grid-cols-2">
		<div>
			<p class="wpis-prose">
				<?php esc_html_e( 'Découvrez l’environnement du bien : commerces, restaurants, écoles, transports et espaces verts à proximité.', 'hello-immosync' ); ?>
			</p>

			<?php if ( $wpis_proximities ) : ?>
				<p class="wpis-eyebrow mb-3 mt-8"><?php esc_html_e( 'À proximité', 'hello-immosync' ); ?></p>
				<dl class="grid grid-cols-1 gap-x-12 sm:grid-cols-2">
					<?php foreach ( $wpis_proximities as $wpis_prox ) : ?>
						<div class="wpis-spec">
							<dt class="wpis-spec-label"><?php echo esc_html( $wpis_prox['label'] ); ?></dt>
							<dd class="wpis-spec-value"><?php echo esc_html( $wpis_prox['value'] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
		</div>

		<?php if ( $wpis_coords ) : ?>
			<?php
			$wpis_delta = 0.008;
			$wpis_bbox  = sprintf(
				'%F,%F,%F,%F',
				$wpis_coords['lng'] - $wpis_delta,
				$wpis_coords['lat'] - $wpis_delta,
				$wpis_coords['lng'] + $wpis_delta,
				$wpis_coords['lat'] + $wpis_delta
			);
			$wpis_map_src = add_query_arg(
				array(
					'bbox'   => $wpis_bbox,
					'layer'  => 'mapnik',
					'marker' => $wpis_coords['lat'] . ',' . $wpis_coords['lng'],
				),
				'https://www.openstreetmap.org/export/embed.html'
			);
			?>
			<div class="overflow-hidden rounded-[var(--radius-card)] border border-line">
				<iframe
					title="<?php esc_attr_e( 'Localisation du bien', 'hello-immosync' ); ?>"
					src="<?php echo esc_url( $wpis_map_src ); ?>"
					class="h-[360px] w-full"
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"></iframe>
			</div>
		<?php endif; ?>
	</div>
</section>
