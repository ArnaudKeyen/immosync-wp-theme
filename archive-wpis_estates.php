<?php
/**
 * Archive des biens immobiliers (listing).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Filtres courants (GET) : appliqués à la requête principale par
// wpis_filter_archive_query(), réutilisés ici pour les liens de tri / pagination.
$wpis_filters = wpis_current_filters();
?>

<section class="border-b border-border bg-surface-alt">
	<div class="wpis-container-wide py-14 md:py-16">
		<p class="wpis-eyebrow mb-3"><?php esc_html_e( 'Nos biens', 'hello-immosync' ); ?></p>
		<h1 class="wpis-title max-w-3xl">
			<?php
			$wpis_archive_title = post_type_archive_title( '', false );
			echo esc_html( $wpis_archive_title ? $wpis_archive_title : __( 'Biens à découvrir', 'hello-immosync' ) );
			?>
		</h1>

		<div class="mt-10">
			<?php wpis_search_form( array( 'variant' => 'inline' ) ); ?>
		</div>
	</div>
</section>

<div class="wpis-container-wide wpis-section">

	<?php wpis_render_estate_results( $GLOBALS['wp_query'], $wpis_filters ); ?>

</div>

<?php
get_footer();
