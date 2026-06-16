<?php
/**
 * Accueil — présentation de l'agence.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_name = get_bloginfo( 'name' );
?>
<section class="wpis-section">
	<div class="wpis-container-wide">
		<div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-20">
			<div class="order-2 lg:order-1">
				<div class="aspect-[4/5] overflow-hidden rounded-[var(--radius-card)] bg-gradient-to-br from-sand to-line"></div>
			</div>
			<div class="order-1 lg:order-2">
				<p class="wpis-eyebrow mb-3"><?php esc_html_e( 'L’agence', 'hello-immosync' ); ?></p>
				<h2 class="wpis-title">
					<?php
					printf(
						/* translators: %s: agency name. */
						esc_html__( 'L’immobilier, à hauteur d’émotion — %s', 'hello-immosync' ),
						esc_html( $wpis_name )
					);
					?>
				</h2>
				<div class="wpis-prose mt-6 max-w-lg">
					<p><?php esc_html_e( 'Nous accompagnons celles et ceux qui cherchent davantage qu’un bien : un cadre, une ambiance, un art de vivre. Chaque projet est unique, chaque accompagnement sur mesure.', 'hello-immosync' ); ?></p>
					<p><?php esc_html_e( 'De l’estimation à la signature, notre approche allie exigence, discrétion et sens du détail.', 'hello-immosync' ); ?></p>
				</div>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'wpis_estates' ) ); ?>" class="wpis-btn mt-8">
					<?php esc_html_e( 'Découvrir nos biens', 'hello-immosync' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
