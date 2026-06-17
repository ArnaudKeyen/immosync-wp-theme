<?php
/**
 * En-tête de la fiche bien : titre, localisation, prix, puis galerie mosaïque.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid   = get_the_ID();
$wpis_links = wpis_get_links( $wpis_pid );
$wpis_loc   = wpis_get_location( $wpis_pid );
$wpis_price = wpis_get_price( $wpis_pid );
?>
<section>
	<div class="wpis-container-wide pt-8 md:pt-12">
		<div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-4">
			<div class="max-w-3xl">
				<h1 class="wpis-title"><?php echo esc_html( wpis_get_title( $wpis_pid ) ); ?></h1>
				<?php if ( '' !== $wpis_loc ) : ?>
					<p class="mt-3 flex items-center gap-2 font-body text-stone">
						<?php echo wpis_icon( 'location', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG inline du thème. ?>
						<?php echo esc_html( $wpis_loc ); ?>
					</p>
				<?php endif; ?>
			</div>
			<?php if ( '' !== $wpis_price ) : ?>
				<span class="font-display text-3xl text-ink md:text-4xl"><?php echo esc_html( $wpis_price ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<?php get_template_part( 'template-parts/estate/gallery' ); ?>

	<?php if ( $wpis_links ) : ?>
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
	<?php endif; ?>
</section>
