<?php
/**
 * Hero de page réutilisable (modèle hybride).
 *
 * Sources de contenu :
 *  - image de fond  : image mise en avant de la page (taille wpis-hero), avec replis ;
 *  - titre (H1)     : champ ACF page_hero_titre, sinon titre natif de la page ;
 *  - sur-titre      : champ ACF page_hero_eyebrow (variante home : nom du site par défaut) ;
 *  - sous-titre     : champ ACF page_hero_sous_titre ;
 *  - barre de recherche : variante « home » uniquement.
 *
 * @package HelloImmoSync
 *
 * @var array $args {
 *     @type string $variant 'home' (plein écran + recherche) ou 'default'. Défaut 'default'.
 * }
 */

defined( 'ABSPATH' ) || exit;

$wpis_variant = ! empty( $args['variant'] ) ? $args['variant'] : 'default';
$wpis_is_home = ( 'home' === $wpis_variant );

// Contenu textuel (champ ACF avec repli natif). Calculé avant toute requête secondaire.
$wpis_eyebrow  = wpis_page_field( 'page_hero_eyebrow', $wpis_is_home ? get_bloginfo( 'name' ) : '' );
$wpis_title    = wpis_page_field( 'page_hero_titre', get_the_title() );
$wpis_subtitle = wpis_page_field( 'page_hero_sous_titre', '' );

// Image de fond : image mise en avant → (home) dernier bien → repli global → dégradé.
$wpis_bg = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'wpis-hero' ) : '';

if ( ! $wpis_bg && $wpis_is_home && function_exists( 'wpis_query_estates' ) ) {
	$wpis_last = wpis_query_estates(
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
	if ( $wpis_last->have_posts() ) {
		$wpis_last->the_post();
		$wpis_bg = get_the_post_thumbnail_url( get_the_ID(), 'wpis-hero' );
		wp_reset_postdata();
	}
}

if ( ! $wpis_bg ) {
	$wpis_fallback = (int) wpis_theme_option( 'option_hero_fallback', 0 );
	if ( $wpis_fallback ) {
		$wpis_bg = wp_get_attachment_image_url( $wpis_fallback, 'wpis-hero' );
	}
}

// Une page « standard » sans image de fond → en-tête clair (pas de bandeau sombre).
$wpis_light = ( ! $wpis_is_home && ! $wpis_bg );
?>

<?php if ( $wpis_light ) : ?>

	<section class="wpis-section bg-sand">
		<div class="wpis-container-wide">
			<?php if ( $wpis_eyebrow ) : ?>
				<p class="wpis-eyebrow mb-3"><?php echo esc_html( $wpis_eyebrow ); ?></p>
			<?php endif; ?>
			<h1 class="wpis-title max-w-3xl"><?php echo esc_html( $wpis_title ); ?></h1>
			<?php if ( $wpis_subtitle ) : ?>
				<p class="wpis-prose mt-5 max-w-xl"><?php echo esc_html( $wpis_subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</section>

<?php else : ?>

	<section class="relative flex <?php echo $wpis_is_home ? 'min-h-[70vh]' : 'min-h-[52vh] overflow-hidden'; ?> items-center bg-ink">
		<?php if ( $wpis_bg ) : ?>
			<img src="<?php echo esc_url( $wpis_bg ); ?>" alt="" class="absolute inset-0 h-full w-full object-cover" fetchpriority="high">
			<div class="absolute inset-0 bg-gradient-to-b from-ink/55 via-ink/30 to-ink/65"></div>
		<?php else : ?>
			<div class="absolute inset-0 bg-gradient-to-br from-ink via-charcoal to-brand-dark"></div>
		<?php endif; ?>

		<div class="relative w-full <?php echo $wpis_is_home ? 'pb-44 pt-24' : 'py-24'; ?>">
			<div class="wpis-container-wide">
				<?php if ( $wpis_eyebrow ) : ?>
					<p class="wpis-eyebrow text-cream/80"><?php echo esc_html( $wpis_eyebrow ); ?></p>
				<?php endif; ?>
				<h1 class="mt-5 max-w-4xl font-display <?php echo $wpis_is_home ? 'text-5xl leading-[1.02] md:text-7xl' : 'text-4xl leading-tight md:text-6xl'; ?> text-cream">
					<?php echo esc_html( $wpis_title ); ?>
				</h1>
				<?php if ( $wpis_subtitle ) : ?>
					<p class="mt-6 max-w-xl font-body text-lg text-cream/80"><?php echo esc_html( $wpis_subtitle ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $wpis_is_home && function_exists( 'wpis_search_form' ) ) : ?>
			<!-- Barre de recherche ancrée en bas du hero -->
			<div class="absolute inset-x-0 bottom-0 translate-y-1/2">
				<div class="wpis-container-wide">
					<?php wpis_search_form( array( 'variant' => 'hero' ) ); ?>
				</div>
			</div>
		<?php endif; ?>
	</section>

	<?php if ( $wpis_is_home ) : ?>
		<div class="h-20 md:h-16"></div><!-- Espace pour la barre de recherche en débord -->
	<?php endif; ?>

<?php endif; ?>
