<?php
/**
 * Accueil — « Trouvez votre lieu de vie » : entrée par quartier / ville.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_options = wpis_get_filter_options();
$wpis_cities  = array_slice( $wpis_options['cities'], 0, 6 );
$wpis_archive = get_post_type_archive_link( 'wpis_estates' );
?>
<section class="wpis-section bg-sand">
	<div class="wpis-container-wide">
		<div class="grid items-center gap-12 lg:grid-cols-2">
			<div>
				<p class="wpis-eyebrow mb-3"><?php esc_html_e( 'Find your place', 'hello-immosync' ); ?></p>
				<h2 class="wpis-title"><?php esc_html_e( 'Trouvez votre lieu de vie', 'hello-immosync' ); ?></h2>
				<p class="wpis-prose mt-6 max-w-lg">
					<?php esc_html_e( 'Chaque quartier raconte une histoire. Choisissez d’abord l’endroit où vous vous voyez vivre — nous nous occupons de trouver le bien qui lui ressemble.', 'hello-immosync' ); ?>
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
