<?php
/**
 * Accueil — biens sélectionnés.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_featured = wpis_query_estates( array( 'posts_per_page' => 6 ) );

if ( ! $wpis_featured->have_posts() ) {
	return;
}
?>
<section class="wpis-section">
	<div class="wpis-container-wide">
		<div class="mb-12 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
			<div>
				<p class="wpis-eyebrow mb-2"><?php esc_html_e( 'Sélection', 'hello-immosync' ); ?></p>
				<h2 class="wpis-title"><?php esc_html_e( 'Biens d’exception', 'hello-immosync' ); ?></h2>
			</div>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'wpis_estates' ) ); ?>" class="wpis-btn-ghost">
				<?php esc_html_e( 'Voir tous les biens', 'hello-immosync' ); ?>
				<?php echo wpis_icon( 'arrow', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		</div>

		<div class="grid grid-cols-1 gap-x-8 gap-y-14 sm:grid-cols-2 lg:grid-cols-3">
			<?php
			while ( $wpis_featured->have_posts() ) :
				$wpis_featured->the_post();
				wpis_estate_card();
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
