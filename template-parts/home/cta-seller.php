<?php
/**
 * Accueil — appel à l'action vendeur (estimation / contact).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="wpis-section bg-sand">
	<div class="wpis-container">
		<div class="mx-auto max-w-2xl text-center">
			<p class="wpis-eyebrow mb-3"><?php esc_html_e( 'Vous vendez ?', 'hello-immosync' ); ?></p>
			<h2 class="wpis-title"><?php esc_html_e( 'Estimez votre bien en toute confidentialité', 'hello-immosync' ); ?></h2>
			<p class="wpis-prose mt-6">
				<?php esc_html_e( 'Obtenez une estimation juste et sans engagement, réalisée par nos experts qui connaissent votre marché.', 'hello-immosync' ); ?>
			</p>
		</div>

		<div class="mx-auto mt-10 max-w-xl">
			<?php
			if ( shortcode_exists( 'wpis-form-evaluation' ) ) {
				echo '<div class="rounded-[var(--radius-card)] border border-line bg-cream p-8">' . do_shortcode( '[wpis-form-evaluation style="off"]' ) . '</div>';
			} else {
				printf(
					'<div class="text-center"><a class="wpis-btn" href="mailto:%1$s?subject=%2$s">%3$s</a></div>',
					esc_attr( get_option( 'admin_email' ) ),
					esc_attr( rawurlencode( __( 'Demande d’estimation', 'hello-immosync' ) ) ),
					esc_html__( 'Demander une estimation', 'hello-immosync' )
				);
			}
			?>
		</div>
	</div>
</section>
