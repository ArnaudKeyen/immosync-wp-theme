<?php
/**
 * Hero d'accueil : visuel immersif plein écran + barre de recherche.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

// Image de fond : dernier bien disposant d'une image à la une (repli = dégradé).
$wpis_hero_bg   = '';
$wpis_hero_post = wpis_query_estates(
	array(
		'posts_per_page' => 1,
		'meta_query'     => array(
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'EXISTS',
			),
		),
	)
);
if ( $wpis_hero_post->have_posts() ) {
	$wpis_hero_post->the_post();
	$wpis_hero_bg = get_the_post_thumbnail_url( get_the_ID(), 'wpis-hero' );
	wp_reset_postdata();
}
?>
<section class="relative flex min-h-[88vh] items-center overflow-hidden bg-ink">
	<?php if ( $wpis_hero_bg ) : ?>
		<img src="<?php echo esc_url( $wpis_hero_bg ); ?>" alt="" class="absolute inset-0 h-full w-full object-cover" fetchpriority="high">
		<div class="absolute inset-0 bg-gradient-to-b from-ink/55 via-ink/30 to-ink/75"></div>
	<?php else : ?>
		<div class="absolute inset-0 bg-gradient-to-br from-ink via-charcoal to-brand-dark"></div>
	<?php endif; ?>

	<div class="relative w-full pb-44 pt-24">
		<div class="wpis-container-wide">
			<p class="wpis-eyebrow text-cream/80"><?php bloginfo( 'name' ); ?></p>
			<h1 class="mt-5 max-w-4xl font-display text-5xl leading-[1.02] text-cream md:text-7xl">
				<?php esc_html_e( 'Trouvez le lieu où votre vie prend racine.', 'hello-immosync' ); ?>
			</h1>
			<p class="mt-6 max-w-xl font-body text-lg text-cream/80">
				<?php esc_html_e( 'Une sélection de biens d’exception, choisis pour leur caractère et leur art de vivre.', 'hello-immosync' ); ?>
			</p>
		</div>
	</div>

	<!-- Barre de recherche ancrée en bas du hero -->
	<div class="absolute inset-x-0 bottom-0 translate-y-1/2">
		<div class="wpis-container-wide">
			<?php wpis_search_form( array( 'variant' => 'hero' ) ); ?>
		</div>
	</div>
</section>
<div class="h-20 md:h-16"></div><!-- Espace pour la barre de recherche en débord -->
