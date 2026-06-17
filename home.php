<?php
/**
 * Index du blog (page des articles définie dans Réglages → Lecture).
 *
 * Hero piloté par la page « Blog » (titre + image mise en avant + champs ACF),
 * puis la liste des articles.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Hero basé sur la page « Blog » (et non sur le premier article).
$wpis_blog_id = (int) get_option( 'page_for_posts' );
if ( $wpis_blog_id ) {
	global $post;
	$wpis_saved_post = $post;
	$post            = get_post( $wpis_blog_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	setup_postdata( $post );
	get_template_part( 'template-parts/global/page-hero', null, array( 'variant' => 'default' ) );
	$post = $wpis_saved_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	wp_reset_postdata();
}
?>

<section class="wpis-section">
	<div class="wpis-container-wide">
		<?php if ( have_posts() ) : ?>
			<div class="grid gap-12 md:grid-cols-2 lg:grid-cols-3">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'flex flex-col' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a href="<?php the_permalink(); ?>" class="mb-5 block aspect-[4/3] overflow-hidden rounded-[var(--radius-card)] bg-sand">
								<?php the_post_thumbnail( 'wpis-card', array( 'class' => 'h-full w-full object-cover' ) ); ?>
							</a>
						<?php endif; ?>
						<h2 class="font-display text-2xl text-ink">
							<a href="<?php the_permalink(); ?>" class="hover:text-brand"><?php the_title(); ?></a>
						</h2>
						<div class="wpis-prose mt-3"><?php the_excerpt(); ?></div>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<div class="mt-16">
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
	</div>
</section>

<?php
get_footer();
