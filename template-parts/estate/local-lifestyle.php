<?php
/**
 * Bloc lifestyle local : carte de géolocalisation + storytelling de quartier.
 *
 * Le texte éditorial est volontairement générique (placeholder premium) :
 * il met en valeur l'art de vivre autour du bien et doit être personnalisé
 * par l'agence (commerces, restaurants, ambiance, transports).
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
	<p class="wpis-eyebrow mb-2"><?php esc_html_e( 'L’art de vivre', 'hello-immosync' ); ?></p>
	<h2 id="wpis-lifestyle-title" class="font-display text-3xl text-ink">
		<?php
		printf(
			/* translators: %s: city name. */
			esc_html__( 'Vivre à %s', 'hello-immosync' ),
			esc_html( $wpis_city ? $wpis_city : __( 'proximité', 'hello-immosync' ) )
		);
		?>
	</h2>

	<div class="mt-8 grid gap-10 lg:grid-cols-2">
		<div>
			<p class="wpis-prose">
				<?php esc_html_e( 'Au-delà des murs, c’est un cadre de vie qui se dessine : des rues à arpenter, des adresses où s’attabler, une atmosphère de quartier. Ce bien s’inscrit dans un environnement pensé pour le quotidien — commerces de bouche, terrasses, espaces verts et accès facilités.', 'hello-immosync' ); ?>
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
