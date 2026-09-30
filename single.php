<?php
/**
 * Article (rubrique Actualité / blog).
 *
 * Hero (titre, image mise en avant, catégorie, date), contenu éditorial complet,
 * partage (module « share »), encart « Estimation gratuite » et articles liés.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$wpis_cats = get_the_category();

	get_template_part(
		'template-parts/global/page-hero',
		null,
		array(
			'variant'  => 'default',
			'eyebrow'  => $wpis_cats ? $wpis_cats[0]->name : '',
			'subtitle' => get_the_date(),
		)
	);
	?>
	<article <?php post_class( 'wpis-section bg-surface' ); ?>>
		<div class="wpis-container">
			<div class="mx-auto max-w-3xl">
				<div class="wpis-prose">
					<?php the_content(); ?>
				</div>

				<?php
				wp_link_pages(
					array(
						'before' => '<nav class="wpis-post-pages mt-10 text-sm">' . esc_html__( 'Pages :', 'hello-immosync' ),
						'after'  => '</nav>',
					)
				);
				?>

				<div class="mt-12 flex flex-wrap items-center justify-between gap-6 border-t border-border pt-8">
					<?php
					$wpis_tags = get_the_tags();
					if ( $wpis_tags ) :
						?>
						<ul class="flex flex-wrap gap-2">
							<?php foreach ( $wpis_tags as $wpis_tag ) : ?>
								<li><a href="<?php echo esc_url( get_tag_link( $wpis_tag ) ); ?>" class="wpis-badge border border-border"><?php echo esc_html( $wpis_tag->name ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<span></span>
					<?php endif; ?>
					<?php get_template_part( 'template-parts/estate/share' ); ?>
				</div>

				<?php get_template_part( 'template-parts/estate/estimate-cta' ); ?>
			</div>
		</div>
	</article>

	<?php
	// Articles liés : même catégorie d'abord, sinon les plus récents.
	$wpis_related = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'category__in'        => $wpis_cats ? wp_list_pluck( $wpis_cats, 'term_id' ) : array(),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( ! $wpis_related->have_posts() && $wpis_cats ) {
		$wpis_related = new WP_Query(
			array(
				'post_type'           => 'post',
				'posts_per_page'      => 3,
				'post__not_in'        => array( get_the_ID() ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
	}
	if ( $wpis_related->have_posts() ) :
		?>
		<section class="wpis-section border-t border-border bg-surface-alt" aria-labelledby="wpis-related-title">
			<div class="wpis-container-wide">
				<h2 id="wpis-related-title" class="wpis-title mb-12"><?php esc_html_e( 'À lire aussi', 'hello-immosync' ); ?></h2>
				<div class="grid gap-x-10 gap-y-16 md:grid-cols-2 lg:grid-cols-3">
					<?php
					while ( $wpis_related->have_posts() ) :
						$wpis_related->the_post();
						get_template_part( 'template-parts/post/card', null, array( 'heading' => 'h3' ) );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
endwhile;

get_footer();
