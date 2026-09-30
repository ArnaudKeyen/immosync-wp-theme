<?php
/**
 * Grille d'articles + pagination (boucle principale). Index du blog et archives.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;
?>
<?php if ( have_posts() ) : ?>
	<div class="grid gap-x-10 gap-y-16 md:grid-cols-2 lg:grid-cols-3">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/post/card' );
		endwhile;
		?>
	</div>

	<div class="wpis-pagination mt-16 flex justify-center">
		<?php
		the_posts_pagination(
			array(
				'mid_size'  => 1,
				'prev_text' => esc_html__( 'Précédent', 'hello-immosync' ),
				'next_text' => esc_html__( 'Suivant', 'hello-immosync' ),
			)
		);
		?>
	</div>
<?php else : ?>
	<p class="wpis-prose"><?php esc_html_e( 'Aucun article pour le moment.', 'hello-immosync' ); ?></p>
<?php endif; ?>
