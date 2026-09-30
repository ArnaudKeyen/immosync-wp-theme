<?php
/**
 * Section « Estimation gratuite » de la fiche bien.
 *
 * Encart d'acquisition vendeurs, sous la présentation : un visiteur qui consulte
 * un bien est souvent lui-même propriétaire. Un simple bouton vers la page
 * Estimation — jamais de second formulaire sur la fiche (le formulaire de la
 * fiche reste la demande de visite).
 *
 * Textes : « Réglages du thème → Fiches de biens » (wpis_estate_estimate_cta_defaults()).
 * S'auto-masque si aucune page d'estimation n'existe.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_url = wpis_get_estimation_url();
if ( '' === $wpis_url ) {
	return;
}

$wpis_txt = array();
foreach ( wpis_estate_estimate_cta_defaults() as $wpis_name => $wpis_default ) {
	$wpis_txt[ $wpis_name ] = wpis_theme_option( $wpis_name, $wpis_default );
}
?>
<section class="wpis-section border-b border-border" aria-labelledby="wpis-estimate-title">
	<div class="wpis-estimate-cta flex flex-col items-start gap-7 rounded-[var(--radius-card)] bg-text-strong p-8 text-surface sm:p-10">
		<div class="max-w-xl">
			<p class="wpis-eyebrow mb-3"><?php echo esc_html( $wpis_txt['estate_estimate_eyebrow'] ); ?></p>
			<h2 id="wpis-estimate-title" class="font-display text-3xl leading-tight text-surface"><?php echo esc_html( $wpis_txt['estate_estimate_title'] ); ?></h2>
			<p class="mt-4 text-sm leading-relaxed text-surface/80"><?php echo esc_html( $wpis_txt['estate_estimate_text'] ); ?></p>
		</div>
		<a href="<?php echo esc_url( $wpis_url ); ?>" class="wpis-btn-light">
			<?php echo esc_html( $wpis_txt['estate_estimate_button'] ); ?>
			<?php echo wpis_icon( 'arrow', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
	</div>
</section>
