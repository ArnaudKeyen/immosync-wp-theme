<?php
/**
 * Fiche d'un bien immobilier.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<article <?php post_class( 'wpis-estate' ); ?>>

		<?php wpis_render_estate_hero(); ?>

		<div class="wpis-container-wide">
			<div class="grid gap-12 lg:grid-cols-[1fr_380px] lg:gap-16">

				<!-- Colonne principale : sections modulables (ordre réglable en admin) -->
				<div>
					<?php wpis_render_estate_sections(); ?>
				</div>

				<!-- Colonne latérale (sticky) -->
				<div class="lg:py-16">
					<div class="lg:sticky lg:top-28">
						<?php get_template_part( 'template-parts/estate/contact-agent' ); ?>
					</div>
				</div>

			</div>
		</div>

		<?php get_template_part( 'template-parts/estate/similar' ); ?>

	</article>

	<?php
endwhile;

get_footer();
