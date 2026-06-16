<?php
/**
 * Template de repli générique.
 *
 * Utilisé pour le blog/les pages standard. Les biens immobiliers disposent de
 * leurs propres templates (archive-wpis_estates.php, single-wpis_estates.php).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="wpis-container wpis-section">

	<?php if ( is_home() && ! is_front_page() ) : ?>
		<header class="mb-14 max-w-2xl">
			<h1 class="wpis-title"><?php single_post_title(); ?></h1>
		</header>
	<?php endif; ?>

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
		<p class="wpis-prose"><?php esc_html_e( 'Aucun contenu pour le moment.', 'hello-immosync' ); ?></p>
	<?php endif; ?>

</div>

<?php
get_footer();
