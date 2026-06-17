<?php
/**
 * En-tête « plein cadre » : grande image en cover plein écran, titre/prix/badges
 * incrustés, et 2 vignettes médias superposées en bas à droite.
 *
 * - Image de fond : cliquable → ouvre la modal galerie (Swiper).
 * - Vignettes (2e/3e photo, ou vidéo / visite 360° si dispo) : superposées par-
 *   dessus l'image, en 4/3 ; photo → modal galerie, vidéo/360 → lightbox d'embed.
 * Le bandeau d'icônes (hero-links) reste en accès secondaire.
 *
 * Repli : aucune image → variante empilée.
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
?>
<section data-wpis-gallery>
	<div class="relative aspect-[4/3] w-full overflow-hidden bg-sand sm:aspect-[16/9] xl:aspect-[2/1]">

		<?php // Image de fond plein cadre, cliquable → modal galerie. ?>
		<button type="button"
			class="group absolute inset-0 block h-full w-full"
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
		</button>

		<?php // Voile dégradé bas pour la lisibilité du texte incrusté. ?>
		<span class="pointer-events-none absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-ink/80 via-ink/20 to-transparent"></span>

		<?php // Calque d'incrustations, aligné sur le conteneur du site. ?>
		<div class="pointer-events-none absolute inset-0">
			<div class="wpis-container-wide relative h-full">

				<span class="absolute left-5 top-5 rounded-[var(--radius-card)] bg-ink/55 px-3 py-1.5 font-body text-xs font-medium text-cream backdrop-blur sm:left-8 lg:left-12">
					<?php echo $wpis_count_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — entier formaté via _n(). ?>
				</span>

				<?php if ( '' !== $wpis_epc_badge ) : ?>
					<span class="absolute right-5 top-5 sm:right-8 lg:right-12">
						<?php echo $wpis_epc_badge; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — markup échappé dans wpis_epc_badge(). ?>
					</span>
				<?php endif; ?>

				<?php // Titre / localisation / prix incrustés, en bas à gauche. ?>
				<div class="absolute inset-x-5 bottom-5 sm:inset-x-8 sm:bottom-8 lg:inset-x-12 lg:bottom-10">
					<div class="max-w-xl <?php echo $wpis_has_tiles ? 'sm:max-w-[46%]' : ''; ?>">
						<?php if ( '' !== $wpis_badges ) : ?>
							<span class="mb-3 flex flex-wrap gap-2">
								<?php echo $wpis_badges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — markup de badges échappé en amont. ?>
							</span>
						<?php endif; ?>
						<h1 class="font-display text-3xl leading-[1.05] text-cream md:text-5xl"><?php echo esc_html( wpis_get_title( $wpis_pid ) ); ?></h1>
						<div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-cream/90">
							<?php if ( '' !== $wpis_loc ) : ?>
								<span class="flex items-center gap-1.5 font-body text-sm">
									<?php echo wpis_icon( 'location', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG inline du thème. ?>
									<?php echo esc_html( $wpis_loc ); ?>
								</span>
							<?php endif; ?>
							<?php if ( '' !== $wpis_price ) : ?>
								<span class="font-display text-xl md:text-2xl"><?php echo esc_html( $wpis_price ); ?></span>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<?php // Vignettes médias superposées, en bas à droite (masquées sous sm). ?>
				<?php if ( $wpis_has_tiles ) : ?>
					<div class="pointer-events-auto absolute bottom-5 right-5 hidden w-[52%] max-w-[480px] gap-2 sm:flex sm:bottom-8 sm:right-8 lg:bottom-10 lg:right-12 lg:gap-3">
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
								class="group relative block aspect-[4/3] min-w-0 flex-1 overflow-hidden rounded-[var(--radius-card)] bg-sand shadow-xl ring-1 ring-cream/25 transition-transform duration-300 hover:-translate-y-0.5"
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
										<span class="flex h-11 w-11 items-center justify-center rounded-full bg-cream/90 text-ink shadow-lg backdrop-blur transition-transform duration-300 group-hover:scale-110">
											<?php echo wpis_icon( $wpis_icon_name, 'w-5 h-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG inline du thème. ?>
										</span>
									</span>
									<span class="wpis-badge absolute bottom-2 left-2 text-[10px]"><?php echo esc_html( $wpis_tag_label ); ?></span>
								<?php endif; ?>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

			</div>
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
