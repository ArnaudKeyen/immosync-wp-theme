<?php
/**
 * Section « Équipements & commodités » de la fiche bien.
 *
 * Affiche les commodités WPIS (wpis_amenities_generic + _custom), enrichies de
 * quelques marqueurs de confort déduits de la configuration (meublé, ascenseur…).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid       = get_the_ID();
$wpis_amenities = wpis_get_amenities( $wpis_pid );

// Marqueurs de confort issus de champs dédiés (en tête, mis en avant).
$wpis_flags = array();
if ( wpis_is_true( 'wpis_properties_isFurnished', $wpis_pid ) ) {
	$wpis_flags[] = __( 'Meublé', 'hello-immosync' );
}

$wpis_items = array_values( array_unique( array_merge( $wpis_flags, $wpis_amenities ) ) );

if ( ! $wpis_items ) {
	return;
}
?>
<section class="wpis-section border-b border-line" aria-labelledby="wpis-amenities-title">
	<p class="wpis-eyebrow mb-2"><?php esc_html_e( 'Confort', 'hello-immosync' ); ?></p>
	<h2 id="wpis-amenities-title" class="font-display text-3xl text-ink"><?php esc_html_e( 'Équipements & commodités', 'hello-immosync' ); ?></h2>

	<ul class="mt-8 grid grid-cols-1 gap-x-8 gap-y-3 sm:grid-cols-2 lg:grid-cols-3">
		<?php foreach ( $wpis_items as $wpis_item ) : ?>
			<li class="flex items-center gap-3 font-body text-sm text-charcoal">
				<span class="shrink-0 text-brand"><?php echo wpis_icon( 'check', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — SVG inline du thème. ?></span>
				<?php echo esc_html( $wpis_item ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
