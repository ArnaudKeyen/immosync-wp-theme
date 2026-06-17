<?php
/**
 * Modal plein écran (Swiper) de la galerie d'un bien — toutes les photos.
 *
 * Partagé par les variantes de hero (mosaïque empilée + plein cadre). Doit être
 * placé à l'intérieur d'une racine [data-wpis-gallery] dont les déclencheurs
 * portent [data-wpis-gallery-open][data-index].
 *
 * @param int[] $args['gallery'] IDs des images, dans l'ordre d'affichage.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_gallery = isset( $args['gallery'] ) && is_array( $args['gallery'] ) ? $args['gallery'] : array();

if ( empty( $wpis_gallery ) ) {
	return;
}
?>
<div class="wpis-gallery-modal fixed inset-0 z-[200] hidden bg-ink/95"
	data-wpis-gallery-modal
	role="dialog"
	aria-modal="true"
	aria-label="<?php esc_attr_e( 'Galerie photos', 'hello-immosync' ); ?>">

	<button type="button"
		class="absolute right-4 top-4 z-10 flex h-11 w-11 items-center justify-center text-2xl text-cream/70 transition-colors hover:text-cream"
		data-wpis-gallery-close
		aria-label="<?php esc_attr_e( 'Fermer', 'hello-immosync' ); ?>">&#10005;</button>

	<div class="swiper h-full w-full" data-wpis-swiper>
		<div class="swiper-wrapper">
			<?php foreach ( $wpis_gallery as $wpis_image_id ) : ?>
				<div class="swiper-slide flex items-center justify-center">
					<?php
					echo wp_get_attachment_image(
						$wpis_image_id,
						'large',
						false,
						array(
							'class'   => 'max-h-[88vh] max-w-[92vw] object-contain',
							'loading' => 'lazy',
						)
					);
					?>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="swiper-button-prev"></div>
		<div class="swiper-button-next"></div>
		<div class="swiper-pagination"></div>
	</div>
</div>
