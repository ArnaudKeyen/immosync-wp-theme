<?php
/**
 * Galerie photos de la fiche bien (grille + lightbox via assets/js/main.js).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid     = get_the_ID();
$wpis_gallery = wpis_get_gallery( $wpis_pid );

if ( count( $wpis_gallery ) < 2 ) {
	return;
}
?>
<section class="wpis-section" aria-labelledby="wpis-gallery-title">
	<div class="mb-8 flex items-end justify-between">
		<div>
			<p class="wpis-eyebrow mb-2"><?php esc_html_e( 'Galerie', 'hello-immosync' ); ?></p>
			<h2 id="wpis-gallery-title" class="font-display text-3xl text-ink"><?php esc_html_e( 'Découvrez le bien en images', 'hello-immosync' ); ?></h2>
		</div>
		<span class="hidden text-sm text-mist sm:block">
			<?php
			printf(
				/* translators: %d: number of photos. */
				esc_html( _n( '%d photo', '%d photos', count( $wpis_gallery ), 'hello-immosync' ) ),
				count( $wpis_gallery )
			);
			?>
		</span>
	</div>

	<div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:gap-4">
		<?php foreach ( $wpis_gallery as $wpis_index => $wpis_image_id ) : ?>
			<?php
			$wpis_full   = wp_get_attachment_image_url( $wpis_image_id, 'full' );
			$wpis_is_big = ( 0 === $wpis_index % 5 ); // Met en avant 1 image sur 5.
			?>
			<button type="button"
				class="group relative block overflow-hidden rounded-[var(--radius-card)] bg-sand <?php echo $wpis_is_big ? 'col-span-2 row-span-2 aspect-square sm:aspect-[4/3]' : 'aspect-square'; ?>"
				data-wpis-gallery-item
				data-full="<?php echo esc_url( $wpis_full ); ?>"
				aria-label="<?php esc_attr_e( 'Agrandir la photo', 'hello-immosync' ); ?>">
				<?php
				echo wp_get_attachment_image(
					$wpis_image_id,
					'wpis-gallery',
					false,
					array(
						'class'   => 'h-full w-full object-cover transition-transform duration-700 group-hover:scale-105',
						'loading' => 'lazy',
					)
				);
				?>
			</button>
		<?php endforeach; ?>
	</div>
</section>
