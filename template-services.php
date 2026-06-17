<?php
/**
 * Template Name: Services
 *
 * Page « parente » de services : hero + contenu introductif optionnel +
 * grille des pages enfants en cartes. Pas de CPT : on s'appuie sur la
 * hiérarchie de pages native (page parente + pages enfants).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/global/page-hero', null, array( 'variant' => 'default' ) );

	// Contenu introductif optionnel (éditeur natif).
	if ( '' !== trim( get_the_content() ) ) :
		?>
		<section class="wpis-section pb-0">
			<div class="wpis-container">
				<div class="wpis-prose mx-auto max-w-3xl"><?php the_content(); ?></div>
			</div>
		</section>
		<?php
	endif;

	// Pages enfants → cartes.
	$wpis_children = get_pages(
		array(
			'parent'      => get_the_ID(),
			'sort_column' => 'menu_order,post_title',
			'sort_order'  => 'ASC',
		)
	);

	if ( $wpis_children ) :
		?>
		<section class="wpis-section">
			<div class="wpis-container-wide">
				<div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
					<?php foreach ( $wpis_children as $wpis_child ) : ?>
						<a href="<?php echo esc_url( get_permalink( $wpis_child ) ); ?>" class="group flex flex-col rounded-[var(--radius-card)] border border-line bg-cream p-8 transition-colors hover:border-ink">
							<?php if ( has_post_thumbnail( $wpis_child ) ) : ?>
								<div class="mb-6 aspect-[4/3] overflow-hidden rounded-[var(--radius-card)] bg-sand">
									<?php
									echo get_the_post_thumbnail(
										$wpis_child,
										'wpis-card',
										array(
											'class'   => 'h-full w-full object-cover',
											'loading' => 'lazy',
										)
									);
									?>
								</div>
							<?php endif; ?>
							<h2 class="font-display text-2xl text-ink"><?php echo esc_html( get_the_title( $wpis_child ) ); ?></h2>
							<?php $wpis_child_ex = get_the_excerpt( $wpis_child ); ?>
							<?php if ( $wpis_child_ex ) : ?>
								<p class="wpis-prose mt-3 text-sm"><?php echo esc_html( wp_trim_words( $wpis_child_ex, 22 ) ); ?></p>
							<?php endif; ?>
							<span class="mt-6 inline-flex items-center gap-1.5 text-sm text-brand">
								<?php esc_html_e( 'Découvrir', 'hello-immosync' ); ?>
								<?php echo wpis_icon( 'arrow', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	endif;
endwhile;

get_footer();
