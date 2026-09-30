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
		<?php get_template_part( 'template-parts/post/grid' ); ?>
	</div>
</section>

<?php
get_footer();
