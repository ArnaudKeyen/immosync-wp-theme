<?php
/**
 * En-tête « empilé » (variante par défaut) : titre, localisation, prix, puis
 * galerie mosaïque, puis bandeau d'accès médias.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid   = get_the_ID();
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

	<?php get_template_part( 'template-parts/estate/hero-links' ); ?>
</section>
