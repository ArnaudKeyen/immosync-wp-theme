<?php
/**
 * Détails de la fiche bien : description longue, caractéristiques, infos techniques.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid = get_the_ID();

// Description longue (champ WPIS, sinon contenu de l'article).
$wpis_description = wpis_get_field( 'wpis_description_long', $wpis_pid, '' );
if ( '' === $wpis_description ) {
	$wpis_description = wpis_get_field( 'wpis_description_base', $wpis_pid, '' );
}
if ( '' === $wpis_description ) {
	$wpis_description = get_the_content();
}

$wpis_features = wpis_get_estate_features( $wpis_pid, false );

// Informations techniques (paires label => valeur, vides ignorées).
$wpis_specs = array_filter(
	array(
		__( 'Référence', 'hello-immosync' )             => wpis_get_reference( $wpis_pid ),
		__( 'Type', 'hello-immosync' )                  => wpis_get_category( $wpis_pid ),
		__( 'Sous-type', 'hello-immosync' )             => wpis_get_field( 'wpis_subcategory_label', $wpis_pid, '' ),
		__( 'Année de construction', 'hello-immosync' ) => wpis_get_field( 'wpis_building_constructionYear', $wpis_pid, '' ),
		__( 'Année de rénovation', 'hello-immosync' )   => wpis_get_field( 'wpis_building_renovationYear', $wpis_pid, '' ),
		__( 'État du bien', 'hello-immosync' )          => wpis_get_field( 'wpis_building_state', $wpis_pid, '' ),
		__( 'Orientation', 'hello-immosync' )           => wpis_get_field( 'wpis_building_orientation', $wpis_pid, '' ),
		__( 'Orientation terrasse', 'hello-immosync' )  => wpis_get_field( 'wpis_building_terraceOrientation', $wpis_pid, '' ),
		__( 'Orientation jardin', 'hello-immosync' )    => wpis_get_field( 'wpis_building_gardenOrientation', $wpis_pid, '' ),
		__( 'Étage', 'hello-immosync' )                 => wpis_get_field( 'wpis_configuration_floorCurrent', $wpis_pid, '' ),
		__( 'Nombre d’étages', 'hello-immosync' )       => wpis_get_field( 'wpis_configuration_floorTotal', $wpis_pid, '' ),
		__( 'Chambres', 'hello-immosync' )              => wpis_get_field( 'wpis_configuration_bedrooms', $wpis_pid, '' ),
		__( 'Salles de bain', 'hello-immosync' )        => wpis_get_field( 'wpis_configuration_bathrooms', $wpis_pid, '' ),
		__( 'Salles de douche', 'hello-immosync' )      => wpis_get_field( 'wpis_configuration_showerrooms', $wpis_pid, '' ),
		__( 'Toilettes', 'hello-immosync' )             => wpis_get_field( 'wpis_configuration_toilets', $wpis_pid, '' ),
		__( 'Cuisines', 'hello-immosync' )              => wpis_get_field( 'wpis_configuration_kitchens', $wpis_pid, '' ),
		__( 'Garages', 'hello-immosync' )               => wpis_get_field( 'wpis_configuration_garages', $wpis_pid, '' ),
		__( 'Emplacements de parking', 'hello-immosync' ) => wpis_get_field( 'wpis_configuration_parking', $wpis_pid, '' ),
		__( 'Caves', 'hello-immosync' )                 => wpis_get_field( 'wpis_configuration_cave', $wpis_pid, '' ),
		__( 'Meublé', 'hello-immosync' )                => wpis_is_true( 'wpis_properties_isFurnished', $wpis_pid ) ? __( 'Oui', 'hello-immosync' ) : '',
	),
	static function ( $value ) {
		return '' !== $value && '0' !== (string) $value;
	}
);
?>
<section class="wpis-section border-b border-line" aria-labelledby="wpis-overview-title">
	<p class="wpis-eyebrow mb-2"><?php esc_html_e( 'Le bien', 'hello-immosync' ); ?></p>
	<h2 id="wpis-overview-title" class="font-display text-3xl text-ink"><?php esc_html_e( 'Présentation', 'hello-immosync' ); ?></h2>

	<?php if ( $wpis_features ) : ?>
		<ul class="mt-8 grid grid-cols-2 gap-px overflow-hidden rounded-[var(--radius-card)] border border-line bg-line sm:grid-cols-3 lg:grid-cols-4">
			<?php foreach ( $wpis_features as $wpis_feature ) : ?>
				<li class="flex flex-col gap-1 bg-cream px-5 py-6">
					<span class="text-brand"><?php echo wpis_icon( $wpis_feature['icon'], 'w-5 h-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span class="mt-2 font-display text-2xl text-ink"><?php echo esc_html( $wpis_feature['value'] ); ?></span>
					<span class="text-xs uppercase tracking-[0.14em] text-stone"><?php echo esc_html( $wpis_feature['label'] ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if ( '' !== trim( wp_strip_all_tags( $wpis_description ) ) ) : ?>
		<div class="wpis-prose mt-10 max-w-3xl">
			<?php echo wp_kses_post( wpautop( $wpis_description ) ); ?>
		</div>
	<?php endif; ?>

	<?php if ( $wpis_specs ) : ?>
		<div class="mt-12">
			<h3 class="mb-4 font-display text-2xl text-ink"><?php esc_html_e( 'Informations techniques', 'hello-immosync' ); ?></h3>
			<dl class="grid grid-cols-1 gap-x-12 sm:grid-cols-2">
				<?php foreach ( $wpis_specs as $wpis_label => $wpis_value ) : ?>
					<div class="wpis-spec">
						<dt class="wpis-spec-label"><?php echo esc_html( $wpis_label ); ?></dt>
						<dd class="wpis-spec-value"><?php echo esc_html( $wpis_value ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>
	<?php endif; ?>
</section>
