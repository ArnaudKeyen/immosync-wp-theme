<?php
/**
 * Hero de la fiche bien : image plein écran + titre, localisation, prix.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid     = get_the_ID();
$wpis_gallery = wpis_get_gallery( $wpis_pid );
$wpis_hero_id = $wpis_gallery ? $wpis_gallery[0] : 0;
$wpis_links   = wpis_get_links( $wpis_pid );
?>
<section class="relative">
	<div class="relative h-[62vh] min-h-[460px] w-full overflow-hidden bg-ink">
		<?php if ( $wpis_hero_id ) : ?>
			<?php
			echo wp_get_attachment_image(
				$wpis_hero_id,
				'wpis-hero',
				false,
				array(
					'class'    => 'absolute inset-0 h-full w-full object-cover',
					'fetchpriority' => 'high',
				)
			);
			?>
		<?php endif; ?>
		<div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-ink/20 to-transparent"></div>

		<div class="absolute inset-x-0 bottom-0">
			<div class="wpis-container-wide pb-10 md:pb-14">
				<div class="flex flex-wrap gap-2">
					<?php echo wpis_estate_badges( $wpis_pid ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<h1 class="mt-4 max-w-4xl font-display text-4xl leading-[1.05] text-cream md:text-6xl">
					<?php echo esc_html( wpis_get_title( $wpis_pid ) ); ?>
				</h1>
				<div class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-cream/85">
					<?php $wpis_loc = wpis_get_location( $wpis_pid ); ?>
					<?php if ( '' !== $wpis_loc ) : ?>
						<span class="flex items-center gap-2 font-body text-sm">
							<?php echo wpis_icon( 'location', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php echo esc_html( $wpis_loc ); ?>
						</span>
					<?php endif; ?>
					<span class="font-display text-2xl text-cream md:text-3xl"><?php echo esc_html( wpis_get_price( $wpis_pid ) ); ?></span>
				</div>
			</div>
		</div>
	</div>

	<?php if ( $wpis_links ) : ?>
		<div class="border-b border-line bg-cream">
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
