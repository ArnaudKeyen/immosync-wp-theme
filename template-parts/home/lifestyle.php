<?php
/**
 * Accueil — section lifestyle locale (art de vivre, quartiers, adresses).
 *
 * Contenu éditorial placeholder premium, à personnaliser par l'agence.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_items = array(
	array(
		'eyebrow' => __( 'Quartiers', 'hello-immosync' ),
		'title'   => __( 'Des adresses qui ont une âme', 'hello-immosync' ),
		'text'    => __( 'Ruelles pavées, places animées, perspectives ouvertes : nous connaissons l’atmosphère de chaque quartier.', 'hello-immosync' ),
		'icon'    => 'location',
	),
	array(
		'eyebrow' => __( 'Tables & commerces', 'hello-immosync' ),
		'title'   => __( 'Le goût du voisinage', 'hello-immosync' ),
		'text'    => __( 'Cafés de quartier, tables de chef, marchés et boutiques : l’art de vivre au quotidien, à deux pas.', 'hello-immosync' ),
		'icon'    => 'compass',
	),
	array(
		'eyebrow' => __( 'Mobilité', 'hello-immosync' ),
		'title'   => __( 'Tout est à portée', 'hello-immosync' ),
		'text'    => __( 'Transports, écoles, espaces verts : un environnement pensé pour fluidifier la vie de famille.', 'hello-immosync' ),
		'icon'    => 'energy',
	),
);

// Surcharge éditable : si le répéteur ACF contient des cartes, il remplace les valeurs par défaut.
$wpis_rows = function_exists( 'get_field' ) ? get_field( 'home_lifestyle_cards', (int) get_option( 'page_on_front' ) ) : false;
if ( is_array( $wpis_rows ) && $wpis_rows ) {
	$wpis_items = array();
	foreach ( $wpis_rows as $wpis_row ) {
		$wpis_items[] = array(
			'eyebrow' => isset( $wpis_row['eyebrow'] ) ? $wpis_row['eyebrow'] : '',
			'title'   => isset( $wpis_row['title'] ) ? $wpis_row['title'] : '',
			'text'    => isset( $wpis_row['text'] ) ? $wpis_row['text'] : '',
			'icon'    => ! empty( $wpis_row['icon'] ) ? $wpis_row['icon'] : 'location',
		);
	}
}
?>
<section class="wpis-section">
	<div class="wpis-container-wide">
		<div class="mb-12 max-w-2xl">
			<p class="wpis-eyebrow mb-3"><?php echo esc_html( wpis_home_field( 'home_lifestyle_eyebrow', __( 'Art de vivre', 'hello-immosync' ) ) ); ?></p>
			<h2 class="wpis-title"><?php echo esc_html( wpis_home_field( 'home_lifestyle_titre', __( 'Bien plus qu’un bien : un mode de vie', 'hello-immosync' ) ) ); ?></h2>
		</div>

		<div class="grid gap-px overflow-hidden rounded-[var(--radius-card)] border border-line bg-line md:grid-cols-3">
			<?php foreach ( $wpis_items as $wpis_item ) : ?>
				<article class="flex flex-col gap-4 bg-cream p-8 md:p-10">
					<span class="text-brand"><?php echo wpis_icon( $wpis_item['icon'], 'w-7 h-7' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<p class="wpis-eyebrow"><?php echo esc_html( $wpis_item['eyebrow'] ); ?></p>
					<h3 class="font-display text-2xl text-ink"><?php echo esc_html( $wpis_item['title'] ); ?></h3>
					<p class="text-sm leading-relaxed text-stone"><?php echo esc_html( $wpis_item['text'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
