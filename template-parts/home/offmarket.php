<?php
/**
 * Accueil — biens off-market / confidentiels.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="wpis-section bg-ink text-cream">
	<div class="wpis-container-wide">
		<div class="grid items-center gap-12 lg:grid-cols-[1.4fr_1fr]">
			<div>
				<p class="wpis-eyebrow mb-3 text-brand"><?php echo esc_html( wpis_home_field( 'home_offmarket_eyebrow', __( 'Off-market', 'hello-immosync' ) ) ); ?></p>
				<h2 class="font-display text-4xl leading-[1.05] text-cream md:text-5xl">
					<?php echo esc_html( wpis_home_field( 'home_offmarket_titre', __( 'Des biens confidentiels, réservés à nos contacts privilégiés.', 'hello-immosync' ) ) ); ?>
				</h2>
				<p class="mt-6 max-w-xl font-body text-base leading-relaxed text-cream/70">
					<?php echo esc_html( wpis_home_field( 'home_offmarket_texte', __( 'Certaines opportunités ne sont jamais publiées. Rejoignez notre cercle pour accéder en avant-première aux biens off-market.', 'hello-immosync' ) ) ); ?>
				</p>
			</div>
			<div class="lg:justify-self-end">
				<a href="mailto:<?php echo esc_attr( get_option( 'admin_email' ) ); ?>?subject=<?php echo esc_attr( rawurlencode( __( 'Accès aux biens off-market', 'hello-immosync' ) ) ); ?>"
					class="inline-flex items-center justify-center gap-2 rounded-[var(--radius-card)] bg-cream px-8 py-4 font-body text-sm font-medium uppercase tracking-[0.12em] text-ink transition-colors hover:bg-brand hover:text-cream">
					<?php echo esc_html( wpis_home_field( 'home_offmarket_bouton', __( 'Demander l’accès', 'hello-immosync' ) ) ); ?>
					<?php echo wpis_icon( 'arrow', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</div>
		</div>
	</div>
</section>
