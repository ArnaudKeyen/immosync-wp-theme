<?php
/**
 * Archive des biens immobiliers (listing).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

get_header();

$wpis_filters = wpis_current_filters();
$wpis_total   = (int) ( $GLOBALS['wp_query']->found_posts ?? 0 );

// Construit les liens de tri en conservant les filtres courants.
$wpis_sort_base = remove_query_arg( array( 'wpis_sort', 'paged' ) );
$wpis_sorts     = array(
	''           => __( 'Plus récents', 'hello-immosync' ),
	'price-asc'  => __( 'Prix croissant', 'hello-immosync' ),
	'price-desc' => __( 'Prix décroissant', 'hello-immosync' ),
);
?>

<section class="border-b border-line bg-sand">
	<div class="wpis-container-wide py-14 md:py-16">
		<p class="wpis-eyebrow mb-3"><?php esc_html_e( 'Nos biens', 'hello-immosync' ); ?></p>
		<h1 class="wpis-title max-w-3xl">
			<?php
			$wpis_archive_title = post_type_archive_title( '', false );
			echo esc_html( $wpis_archive_title ? $wpis_archive_title : __( 'Biens à découvrir', 'hello-immosync' ) );
			?>
		</h1>

		<div class="mt-10">
			<?php wpis_search_form( array( 'variant' => 'inline' ) ); ?>
		</div>
	</div>
</section>

<div class="wpis-container-wide wpis-section">

	<!-- Barre de résultats + tri -->
	<div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
		<p class="font-body text-sm text-stone">
			<?php
			printf(
				/* translators: %s: number of properties. */
				esc_html( _n( '%s bien disponible', '%s biens disponibles', $wpis_total, 'hello-immosync' ) ),
				'<span class="font-medium text-ink">' . esc_html( number_format_i18n( $wpis_total ) ) . '</span>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
			?>
		</p>

		<div class="flex flex-wrap items-center gap-1 text-sm">
			<span class="mr-2 text-mist"><?php esc_html_e( 'Trier :', 'hello-immosync' ); ?></span>
			<?php foreach ( $wpis_sorts as $wpis_key => $wpis_label ) : ?>
				<?php
				$wpis_url    = '' === $wpis_key ? $wpis_sort_base : add_query_arg( 'wpis_sort', $wpis_key, $wpis_sort_base );
				$wpis_active = ( $wpis_filters['sort'] === $wpis_key );
				?>
				<a href="<?php echo esc_url( $wpis_url ); ?>"
					class="rounded-[var(--radius-pill)] px-3 py-1 transition-colors <?php echo $wpis_active ? 'bg-ink text-cream' : 'text-stone hover:text-ink'; ?>">
					<?php echo esc_html( $wpis_label ); ?>
				</a>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ( have_posts() ) : ?>
		<div class="grid grid-cols-1 gap-x-8 gap-y-14 sm:grid-cols-2 lg:grid-cols-3">
			<?php
			while ( have_posts() ) :
				the_post();
				wpis_estate_card();
			endwhile;
			?>
		</div>

		<div class="wpis-pagination mt-16 flex justify-center">
			<?php
			the_posts_pagination(
				array(
					'mid_size'           => 1,
					'prev_text'          => esc_html__( 'Précédent', 'hello-immosync' ),
					'next_text'          => esc_html__( 'Suivant', 'hello-immosync' ),
					'screen_reader_text' => esc_html__( 'Navigation des biens', 'hello-immosync' ),
				)
			);
			?>
		</div>

	<?php else : ?>
		<div class="rounded-[var(--radius-card)] border border-line bg-white px-8 py-20 text-center">
			<p class="font-display text-2xl text-ink"><?php esc_html_e( 'Aucun bien ne correspond à votre recherche.', 'hello-immosync' ); ?></p>
			<p class="mt-3 text-sm text-stone"><?php esc_html_e( 'Essayez d’élargir vos critères.', 'hello-immosync' ); ?></p>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'wpis_estates' ) ); ?>" class="wpis-btn mt-8"><?php esc_html_e( 'Réinitialiser la recherche', 'hello-immosync' ); ?></a>
		</div>
	<?php endif; ?>

</div>

<?php
get_footer();
