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
?>
<section class="wpis-section">
	<div class="wpis-container-wide">
		<div class="mb-12 max-w-2xl">
			<p class="wpis-eyebrow mb-3"><?php esc_html_e( 'Art de vivre', 'hello-immosync' ); ?></p>
			<h2 class="wpis-title"><?php esc_html_e( 'Bien plus qu’un bien : un mode de vie', 'hello-immosync' ); ?></h2>
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
