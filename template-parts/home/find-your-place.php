<?php
/**
 * Accueil — entrée par localité : grille de villes vers l'archive des biens.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_options = wpis_get_filter_options();
$wpis_archive = get_post_type_archive_link( 'wpis_estates' );

// Villes : mode manuel (le client choisit) sinon auto (les plus fréquentes).
$wpis_home_id = (int) get_option( 'page_on_front' );
$wpis_picked  = function_exists( 'get_field' ) ? get_field( 'home_fyp_cities', $wpis_home_id ) : false;
if ( 'manual' === wpis_home_field( 'home_fyp_cities_mode', 'auto' ) && is_array( $wpis_picked ) && $wpis_picked ) {
	$wpis_cities = $wpis_picked;
} else {
	$wpis_cities = array_slice( $wpis_options['cities'], 0, 6 );
}
?>
<section class="wpis-section bg-sand">
	<div class="wpis-container-wide">
		<div class="grid items-center gap-12 lg:grid-cols-2">
			<div>
				<p class="wpis-eyebrow mb-3"><?php echo esc_html( wpis_home_field( 'home_fyp_eyebrow', __( 'Localités', 'hello-immosync' ) ) ); ?></p>
				<h2 class="wpis-title"><?php echo esc_html( wpis_home_field( 'home_fyp_titre', __( 'Explorez par localité', 'hello-immosync' ) ) ); ?></h2>
				<p class="wpis-prose mt-6 max-w-lg">
					<?php echo esc_html( wpis_home_field( 'home_fyp_texte', __( 'Parcourez notre sélection de biens par ville et accédez directement à ceux qui correspondent à votre recherche.', 'hello-immosync' ) ) ); ?>
				</p>
			</div>

			<?php if ( $wpis_cities && $wpis_archive ) : ?>
				<div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
					<?php foreach ( $wpis_cities as $wpis_city ) : ?>
						<a href="<?php echo esc_url( add_query_arg( 'wpis_city', rawurlencode( $wpis_city ), $wpis_archive ) ); ?>"
							class="group flex items-center justify-between rounded-[var(--radius-card)] border border-line bg-cream px-5 py-4 transition-colors hover:border-ink">
							<span class="font-display text-lg text-ink"><?php echo esc_html( $wpis_city ); ?></span>
							<span class="text-brand transition-transform group-hover:translate-x-1"><?php echo wpis_icon( 'arrow', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
