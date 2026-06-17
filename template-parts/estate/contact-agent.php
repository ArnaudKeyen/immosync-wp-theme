<?php
/**
 * Encart latéral : synthèse prix, agent de contact et CTA.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid    = get_the_ID();
$wpis_agent  = wpis_get_agent( $wpis_pid );
$wpis_agency = wpis_get_agency( $wpis_pid );
$wpis_email  = $wpis_agent['email'] ? $wpis_agent['email'] : $wpis_agency['email'];
$wpis_phone  = $wpis_agent['phone'] ? $wpis_agent['phone'] : $wpis_agency['phone'];
?>
<aside class="space-y-6">

	<!-- Synthèse prix -->
	<div class="rounded-[var(--radius-card)] border border-line bg-white p-7">
		<p class="wpis-eyebrow mb-2"><?php echo esc_html( wpis_get_purpose( $wpis_pid ) ? wpis_get_purpose( $wpis_pid ) : __( 'Prix', 'hello-immosync' ) ); ?></p>
		<p class="font-display text-4xl text-ink"><?php echo esc_html( wpis_get_price( $wpis_pid ) ); ?></p>

		<dl class="mt-6 space-y-1">
			<?php $wpis_ref = wpis_get_reference( $wpis_pid ); ?>
			<?php if ( '' !== $wpis_ref ) : ?>
				<div class="wpis-spec">
					<dt class="wpis-spec-label"><?php esc_html_e( 'Référence', 'hello-immosync' ); ?></dt>
					<dd class="wpis-spec-value"><?php echo esc_html( $wpis_ref ); ?></dd>
				</div>
			<?php endif; ?>
			<?php $wpis_status = wpis_get_status( $wpis_pid ); ?>
			<?php if ( '' !== $wpis_status ) : ?>
				<div class="wpis-spec">
					<dt class="wpis-spec-label"><?php esc_html_e( 'Statut', 'hello-immosync' ); ?></dt>
					<dd class="wpis-spec-value"><?php echo esc_html( $wpis_status ); ?></dd>
				</div>
			<?php endif; ?>
		</dl>
	</div>

	<!-- Agent / agence -->
	<?php if ( '' !== $wpis_agent['name'] || '' !== $wpis_agency['name'] ) : ?>
		<div class="rounded-[var(--radius-card)] border border-line bg-white p-7">
			<p class="wpis-eyebrow mb-4"><?php esc_html_e( 'Votre contact', 'hello-immosync' ); ?></p>
			<div class="flex items-center gap-4">
				<?php if ( '' !== $wpis_agent['picture'] ) : ?>
					<img src="<?php echo esc_url( $wpis_agent['picture'] ); ?>" alt="<?php echo esc_attr( $wpis_agent['name'] ); ?>" class="h-16 w-16 rounded-full object-cover" loading="lazy">
				<?php endif; ?>
				<div>
					<?php if ( '' !== $wpis_agent['name'] ) : ?>
						<p class="font-display text-xl text-ink"><?php echo esc_html( $wpis_agent['name'] ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $wpis_agency['name'] ) : ?>
						<p class="text-sm text-stone"><?php echo esc_html( $wpis_agency['name'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<div class="mt-6 space-y-3">
				<?php if ( '' !== $wpis_phone ) : ?>
					<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $wpis_phone ) ); ?>" class="flex items-center gap-3 text-sm text-charcoal transition-colors hover:text-brand">
						<span class="text-brand"><?php echo wpis_icon( 'phone', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php echo esc_html( $wpis_phone ); ?>
					</a>
				<?php endif; ?>
				<?php if ( '' !== $wpis_email ) : ?>
					<a href="mailto:<?php echo esc_attr( $wpis_email ); ?>" class="flex items-center gap-3 text-sm text-charcoal transition-colors hover:text-brand">
						<span class="text-brand"><?php echo wpis_icon( 'mail', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php echo esc_html( $wpis_email ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<!-- Formulaire de contact (shortcode plugin) -->
	<div class="rounded-[var(--radius-card)] border border-line bg-sand p-7">
		<p class="wpis-eyebrow mb-2"><?php esc_html_e( 'Intéressé(e) ?', 'hello-immosync' ); ?></p>
		<h3 class="font-display text-2xl text-ink"><?php esc_html_e( 'Demander une visite', 'hello-immosync' ); ?></h3>
		<div class="wpis-form-shell mt-5">
			<?php
			if ( shortcode_exists( 'wpis-form-estate' ) ) {
				echo do_shortcode( '[wpis-form-estate style="off"]' );
			} elseif ( '' !== $wpis_email ) {
				printf(
					'<a class="wpis-btn w-full" href="mailto:%1$s?subject=%2$s">%3$s</a>',
					esc_attr( $wpis_email ),
					esc_attr( rawurlencode( sprintf( /* translators: %s: estate title. */ __( 'Demande d’information — %s', 'hello-immosync' ), wpis_get_title( $wpis_pid ) ) ) ),
					esc_html__( 'Contacter par e-mail', 'hello-immosync' )
				);
			}
			?>
		</div>
	</div>

</aside>
