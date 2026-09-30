<?php
/**
 * Archives d'articles (catégorie, étiquette, auteur, date).
 *
 * Les biens ont leur propre archive (archive-wpis_estates.php).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Titre nu (« Esneux » plutôt que « Catégorie : Esneux ») : la rubrique est rappelée au-dessus.
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );
?>

<section class="border-b border-border bg-surface-alt">
	<div class="wpis-container-wide py-14 md:py-16">
		<?php
		$wpis_blog_id = (int) get_option( 'page_for_posts' );
		if ( $wpis_blog_id ) :
			?>
			<p class="wpis-eyebrow mb-3"><a href="<?php echo esc_url( get_permalink( $wpis_blog_id ) ); ?>"><?php echo esc_html( get_the_title( $wpis_blog_id ) ); ?></a></p>
		<?php endif; ?>
		<h1 class="wpis-title max-w-3xl"><?php echo esc_html( wp_strip_all_tags( get_the_archive_title() ) ); ?></h1>
		<?php
		$wpis_desc = get_the_archive_description();
		if ( $wpis_desc ) :
			?>
			<div class="wpis-prose mt-6 max-w-2xl"><?php echo wp_kses_post( $wpis_desc ); ?></div>
		<?php endif; ?>
	</div>
</section>

<section class="wpis-section">
	<div class="wpis-container-wide">
		<?php get_template_part( 'template-parts/post/grid' ); ?>
	</div>
</section>

<?php
get_footer();
