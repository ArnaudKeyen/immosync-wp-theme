<?php
/**
 * Galerie photos de la fiche bien : mosaïque (quelques visuels) + modal Swiper.
 *
 * - ≥ 5 photos  : 1 grande + 4 vignettes ; la dernière porte un overlay « +N photos ».
 * - 1 à 4 photos : une seule grande image (la mosaïque ferait des trous).
 * Tout ouvre une modal Swiper affichant l'intégralité des photos.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid     = get_the_ID();
$wpis_gallery = wpis_get_gallery( $wpis_pid );
$wpis_total   = count( $wpis_gallery );

if ( 0 === $wpis_total ) {
	return;
}

$wpis_is_mosaic = $wpis_total >= 5;
$wpis_visible   = $wpis_is_mosaic ? 5 : 1;          // Vignettes affichées.
$wpis_remaining = $wpis_total - $wpis_visible;      // Restantes (overlay « +N »).
$wpis_tiles     = array_slice( $wpis_gallery, 0, $wpis_visible );

$wpis_energy    = wpis_get_energy( $wpis_pid );
$wpis_epc_badge = wpis_epc_badge( $wpis_energy['label'], 'h-auto w-16 md:w-20' );
$wpis_badges    = wpis_estate_badges( $wpis_pid );

$wpis_count_label = sprintf(
	/* translators: %d: nombre de photos. */
	esc_html( _n( '%d photo', '%d photos', $wpis_total, 'hello-immosync' ) ),
	$wpis_total
);
?>
<section class="wpis-container-wide pt-6" data-wpis-gallery aria-label="<?php esc_attr_e( 'Galerie photos', 'hello-immosync' ); ?>">

	<?php // Mosaïque. Lignes auto dictées par les vignettes 4/3 (pas de hauteur figée). ?>
	<div class="grid gap-2 <?php echo $wpis_is_mosaic ? 'grid-cols-2 sm:grid-cols-4' : ''; ?>">
		<?php foreach ( $wpis_tiles as $wpis_i => $wpis_image_id ) : ?>
			<?php
			$wpis_is_big   = ( 0 === $wpis_i );
			$wpis_is_last  = ( $wpis_i === $wpis_visible - 1 );
			$wpis_show_more = ( $wpis_is_last && $wpis_remaining > 0 );

			if ( $wpis_is_big ) {
				// Toujours 4/3. En mosaïque sur desktop, la grande image s'étire sur
				// 2 lignes (sm:aspect-auto) pour s'aligner sur les 2 vignettes 4/3
				// empilées — le cadre résultant est ~4/3. Sinon 4/3 strict.
				$wpis_tile_class = $wpis_is_mosaic
					? 'col-span-2 aspect-[4/3] sm:row-span-2 sm:aspect-auto'
					: 'aspect-[4/3]';
			} else {
				$wpis_tile_class = 'hidden aspect-[4/3] sm:block';
			}
			?>
			<button type="button"
				class="group relative block overflow-hidden rounded-[var(--radius-card)] bg-sand <?php echo esc_attr( $wpis_tile_class ); ?>"
				data-wpis-gallery-open
				data-index="<?php echo (int) $wpis_i; ?>"
				aria-label="<?php esc_attr_e( 'Voir toutes les photos', 'hello-immosync' ); ?>">
				<?php
				echo wp_get_attachment_image(
					$wpis_image_id,
					$wpis_is_big ? 'wpis-gallery' : 'wpis-card',
					false,
					array(
						'class'         => 'h-full w-full object-cover transition-transform duration-700 group-hover:scale-105',
						'loading'       => $wpis_is_big ? 'eager' : 'lazy',
						'fetchpriority' => $wpis_is_big ? 'high' : 'auto',
					)
				);
				?>

				<?php if ( $wpis_is_big ) : ?>
					<?php if ( '' !== $wpis_badges ) : ?>
						<span class="pointer-events-none absolute left-4 top-4 flex flex-wrap gap-2">
							<?php echo $wpis_badges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — markup de badges échappé en amont. ?>
						</span>
					<?php endif; ?>

					<?php if ( '' !== $wpis_epc_badge ) : ?>
						<span class="pointer-events-none absolute right-4 top-4">
							<?php echo $wpis_epc_badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — markup échappé dans wpis_epc_badge(). ?>
						</span>
					<?php endif; ?>

					<span class="pointer-events-none absolute bottom-4 right-4 rounded-[var(--radius-card)] bg-ink/75 px-3 py-1.5 font-body text-xs font-medium text-cream backdrop-blur">
						<?php echo $wpis_count_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — entier formaté via _n(). ?>
					</span>
				<?php endif; ?>

				<?php if ( $wpis_show_more ) : ?>
					<span class="absolute inset-0 flex items-center justify-center bg-ink/55 font-display text-2xl text-cream transition-colors group-hover:bg-ink/65">
						<?php
						printf(
							/* translators: %d: nombre de photos supplémentaires. */
							esc_html__( '+%d photos', 'hello-immosync' ),
							(int) $wpis_remaining
						);
						?>
					</span>
				<?php endif; ?>
			</button>
		<?php endforeach; ?>
	</div>

	<?php // Modal plein écran (Swiper) — toutes les photos. Partagée entre variantes de hero. ?>
	<?php get_template_part( 'template-parts/estate/gallery-modal', null, array( 'gallery' => $wpis_gallery ) ); ?>
</section>
