<?php
/**
 * En-tête « plein cadre » : grande photo avec titre/prix incrustés + colonne de
 * tuiles médias (2e/3e photo, ou vidéo / visite 360° quand elles existent).
 *
 * - Grande image + tuiles photo : ouvrent la modal galerie (Swiper).
 * - Tuile vidéo / visite : ouvrent une lightbox d'embed (sans quitter le site).
 * Le bandeau d'icônes (hero-links) reste affiché en accès secondaire.
 *
 * Repli : aucune image → on retombe sur la variante empilée.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid     = get_the_ID();
$wpis_gallery = wpis_get_gallery( $wpis_pid );

if ( empty( $wpis_gallery ) ) {
	get_template_part( 'template-parts/estate/hero-stacked' );
	return;
}

$wpis_loc       = wpis_get_location( $wpis_pid );
$wpis_price     = wpis_get_price( $wpis_pid );
$wpis_slots     = wpis_get_hero_media_slots( $wpis_pid );
$wpis_count     = count( $wpis_slots );
$wpis_has_tiles = $wpis_count > 0;

$wpis_energy    = wpis_get_energy( $wpis_pid );
$wpis_epc_badge = wpis_epc_badge( $wpis_energy['label'], 'h-auto w-16 md:w-20' );
$wpis_badges    = wpis_estate_badges( $wpis_pid );
$wpis_total     = count( $wpis_gallery );

$wpis_count_label = sprintf(
	/* translators: %d: nombre de photos. */
	esc_html( _n( '%d photo', '%d photos', $wpis_total, 'hello-immosync' ) ),
	$wpis_total
);

// Classes conditionnelles (littéraux complets pour le scan Tailwind).
$wpis_grid_class  = $wpis_has_tiles ? 'lg:h-[clamp(420px,56vh,640px)] lg:grid-cols-[1.9fr_1fr]' : '';
$wpis_big_class   = $wpis_has_tiles ? 'aspect-[4/3] lg:aspect-auto lg:h-full' : 'aspect-[16/9]';
$wpis_tiles_class = ( 2 === $wpis_count )
	? 'grid-cols-2 lg:grid-cols-1 lg:grid-rows-2'
	: 'grid-cols-1 lg:grid-rows-1';
?>
<section data-wpis-gallery>
	<div class="wpis-container-wide pt-6">
		<div class="grid gap-2 <?php echo esc_attr( $wpis_grid_class ); ?>">

			<?php // Grande image plein cadre + incrustations. ?>
			<button type="button"
				class="group relative block overflow-hidden rounded-[var(--radius-card)] bg-sand <?php echo esc_attr( $wpis_big_class ); ?>"
				data-wpis-gallery-open
				data-index="0"
				aria-label="<?php esc_attr_e( 'Voir toutes les photos', 'hello-immosync' ); ?>">
				<?php
				echo wp_get_attachment_image(
					$wpis_gallery[0],
					'wpis-gallery',
					false,
					array(
						'class'         => 'h-full w-full object-cover transition-transform duration-700 group-hover:scale-105',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
					)
				);
				?>

				<?php // Voile dégradé bas pour la lisibilité du texte incrusté. ?>
				<span class="pointer-events-none absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink/80 via-ink/30 to-transparent"></span>

				<?php if ( '' !== $wpis_epc_badge ) : ?>
					<span class="pointer-events-none absolute right-4 top-4">
						<?php echo $wpis_epc_badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — markup échappé dans wpis_epc_badge(). ?>
					</span>
				<?php endif; ?>

				<span class="pointer-events-none absolute bottom-4 right-4 rounded-[var(--radius-card)] bg-ink/75 px-3 py-1.5 font-body text-xs font-medium text-cream backdrop-blur">
					<?php echo $wpis_count_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — entier formaté via _n(). ?>
				</span>

				<?php // Titre / localisation / prix incrustés. ?>
				<span class="pointer-events-none absolute inset-x-0 bottom-0 p-5 text-left md:p-8">
					<?php if ( '' !== $wpis_badges ) : ?>
						<span class="mb-3 flex flex-wrap gap-2">
							<?php echo $wpis_badges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — markup de badges échappé en amont. ?>
						</span>
					<?php endif; ?>
					<span class="block font-display text-3xl leading-[1.05] text-cream md:text-5xl"><?php echo esc_html( wpis_get_title( $wpis_pid ) ); ?></span>
					<span class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-cream/90">
						<?php if ( '' !== $wpis_loc ) : ?>
							<span class="flex items-center gap-1.5 font-body text-sm">
								<?php echo wpis_icon( 'location', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG inline du thème. ?>
								<?php echo esc_html( $wpis_loc ); ?>
							</span>
						<?php endif; ?>
						<?php if ( '' !== $wpis_price ) : ?>
							<span class="font-display text-xl md:text-2xl"><?php echo esc_html( $wpis_price ); ?></span>
						<?php endif; ?>
					</span>
				</span>
			</button>

			<?php if ( $wpis_has_tiles ) : ?>
				<div class="grid gap-2 <?php echo esc_attr( $wpis_tiles_class ); ?> lg:h-full">
					<?php
					foreach ( $wpis_slots as $wpis_i => $wpis_slot ) :
						$wpis_is_media = in_array( $wpis_slot['type'], array( 'video', 'tour' ), true );
						$wpis_embed_id = 'wpis-embed-' . $wpis_pid . '-' . $wpis_i;

						if ( 'video' === $wpis_slot['type'] ) {
							$wpis_icon_name = 'play';
							$wpis_tag_label = __( 'Vidéo', 'hello-immosync' );
							$wpis_aria      = __( 'Lire la vidéo', 'hello-immosync' );
						} elseif ( 'tour' === $wpis_slot['type'] ) {
							$wpis_icon_name = 'rotate';
							$wpis_tag_label = __( 'Visite 360°', 'hello-immosync' );
							$wpis_aria      = __( 'Ouvrir la visite virtuelle', 'hello-immosync' );
						} else {
							$wpis_aria = __( 'Voir toutes les photos', 'hello-immosync' );
						}
						?>
						<button type="button"
							class="group relative block overflow-hidden rounded-[var(--radius-card)] bg-sand aspect-[4/3] lg:aspect-auto lg:h-full"
							<?php if ( $wpis_is_media ) : ?>
								data-wpis-embed-open="<?php echo esc_attr( $wpis_embed_id ); ?>"
							<?php else : ?>
								data-wpis-gallery-open
								data-index="<?php echo (int) $wpis_slot['gallery_index']; ?>"
							<?php endif; ?>
							aria-label="<?php echo esc_attr( $wpis_aria ); ?>">
							<?php
							echo wp_get_attachment_image(
								$wpis_slot['image_id'],
								'wpis-card',
								false,
								array(
									'class'   => 'h-full w-full object-cover transition-transform duration-700 group-hover:scale-105',
									'loading' => 'lazy',
								)
							);
							?>

							<?php if ( $wpis_is_media ) : ?>
								<span class="absolute inset-0 flex items-center justify-center bg-ink/30 transition-colors group-hover:bg-ink/45">
									<span class="flex h-14 w-14 items-center justify-center rounded-full bg-cream/90 text-ink shadow-lg backdrop-blur transition-transform duration-300 group-hover:scale-110">
										<?php echo wpis_icon( $wpis_icon_name, 'w-6 h-6' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG inline du thème. ?>
									</span>
								</span>
								<span class="wpis-badge absolute bottom-3 left-3"><?php echo esc_html( $wpis_tag_label ); ?></span>
							<?php endif; ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>

	<?php get_template_part( 'template-parts/estate/hero-links' ); ?>

	<?php // Modal galerie (photos) partagée. ?>
	<?php get_template_part( 'template-parts/estate/gallery-modal', null, array( 'gallery' => $wpis_gallery ) ); ?>

	<?php
	// Gabarits d'embed (inertes tant que non clonés) + lightbox unique.
	$wpis_embeds = array();
	foreach ( $wpis_slots as $wpis_i => $wpis_slot ) {
		if ( ! in_array( $wpis_slot['type'], array( 'video', 'tour' ), true ) ) {
			continue;
		}
		$wpis_markup = wpis_media_embed_html( $wpis_slot['url'], $wpis_slot['type'] );
		if ( '' !== $wpis_markup ) {
			$wpis_embeds[ 'wpis-embed-' . $wpis_pid . '-' . $wpis_i ] = $wpis_markup;
		}
	}
	?>

	<?php if ( ! empty( $wpis_embeds ) ) : ?>
		<?php foreach ( $wpis_embeds as $wpis_embed_id => $wpis_markup ) : ?>
			<template data-wpis-embed-tpl="<?php echo esc_attr( $wpis_embed_id ); ?>"><?php echo $wpis_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — iframe d'embed construite et échappée dans wpis_media_embed_html(). ?></template>
		<?php endforeach; ?>

		<div class="fixed inset-0 z-[200] hidden bg-ink/95"
			data-wpis-embed-modal
			role="dialog"
			aria-modal="true"
			aria-label="<?php esc_attr_e( 'Média du bien', 'hello-immosync' ); ?>">
			<button type="button"
				class="absolute right-4 top-4 z-10 flex h-11 w-11 items-center justify-center text-2xl text-cream/70 transition-colors hover:text-cream"
				data-wpis-embed-close
				aria-label="<?php esc_attr_e( 'Fermer', 'hello-immosync' ); ?>">&#10005;</button>
			<div class="flex h-full w-full items-center justify-center p-4" data-wpis-embed-stage></div>
		</div>
	<?php endif; ?>
</section>
