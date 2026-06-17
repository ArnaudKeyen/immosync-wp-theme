<?php
/**
 * Template de page standard.
 *
 * Hero hybride (titre + image mise en avant + champs ACF) puis contenu natif
 * (éditeur WordPress / Gutenberg).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/global/page-hero', null, array( 'variant' => 'default' ) );
	?>
	<article <?php post_class( 'wpis-section' ); ?>>
		<div class="wpis-container">
			<div class="wpis-prose mx-auto max-w-3xl">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
