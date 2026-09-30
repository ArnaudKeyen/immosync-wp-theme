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

	<?php get_template_part( 'template-parts/post/grid' ); ?>

</div>

<?php
get_footer();
