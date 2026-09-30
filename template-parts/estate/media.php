<?php
/**
 * Section médias immersifs de la fiche bien : visite 360° et/ou vidéo.
 *
 * Cible des ancres du bandeau de hero (#wpis-visit360, #wpis-video). Les liens
 * sont résolus par wpis_get_estate_media(), qui absorbe les nommages divergents
 * des logiciels immo (Whise range le 360 dans « links.model »).
 *
 * S'auto-masque si le bien n'a aucun média.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid   = get_the_ID();
$wpis_media = wpis_get_estate_media( $wpis_pid );

// Un embed peut échouer (URL non embarquable, oEmbed muet) : on ne construit la
// section qu'à partir de ce qui a réellement produit du markup.
$wpis_blocks = array();

if ( ! empty( $wpis_media['tour']['url'] ) ) {
	$wpis_embed = wpis_media_embed_html( $wpis_media['tour']['url'], 'tour' );
	if ( '' !== $wpis_embed ) {
		$wpis_blocks[] = array(
			'id'    => 'wpis-visit360',
			'title' => __( 'Visite virtuelle 360°', 'hello-immosync' ),
			'note'  => __( 'Déplacez-vous librement de pièce en pièce, comme sur place.', 'hello-immosync' ),
			'embed' => $wpis_embed,
		);
	}
}

if ( ! empty( $wpis_media['video']['url'] ) ) {
	$wpis_embed = wpis_media_embed_html( $wpis_media['video']['url'], 'video' );
	if ( '' !== $wpis_embed ) {
		$wpis_blocks[] = array(
			'id'    => 'wpis-video',
			'title' => __( 'Vidéo de présentation', 'hello-immosync' ),
			'note'  => '',
			'embed' => $wpis_embed,
		);
	}
}

if ( ! $wpis_blocks ) {
	return;
}

// Titre de section adapté à ce qui est réellement disponible.
if ( 2 === count( $wpis_blocks ) ) {
	$wpis_heading = __( 'Visite 360° & vidéo', 'hello-immosync' );
} else {
	$wpis_heading = $wpis_blocks[0]['title'];
}
?>
<section class="wpis-section border-b border-border" aria-labelledby="wpis-media-title">
	<p class="wpis-eyebrow mb-2"><?php esc_html_e( 'Découvrir le bien', 'hello-immosync' ); ?></p>
	<h2 id="wpis-media-title" class="font-display text-3xl text-text-strong"><?php echo esc_html( $wpis_heading ); ?></h2>

	<div class="mt-8 space-y-10">
		<?php foreach ( $wpis_blocks as $wpis_block ) : ?>
			<div id="<?php echo esc_attr( $wpis_block['id'] ); ?>" class="scroll-mt-28">
				<?php if ( count( $wpis_blocks ) > 1 ) : ?>
					<h3 class="font-display text-xl text-text-strong"><?php echo esc_html( $wpis_block['title'] ); ?></h3>
				<?php endif; ?>
				<?php if ( '' !== $wpis_block['note'] ) : ?>
					<p class="mt-1 font-body text-sm text-text-secondary"><?php echo esc_html( $wpis_block['note'] ); ?></p>
				<?php endif; ?>
				<div class="wpis-media-embed mt-4 overflow-hidden rounded-[var(--radius-card)] bg-surface-alt">
					<?php echo $wpis_block['embed']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — markup déjà échappé dans wpis_media_embed_html(). ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
