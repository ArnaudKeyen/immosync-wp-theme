<?php
/**
 * Bloc de résultats de la recherche de biens : compteur, tri, grille, pagination.
 *
 * Rendu à l'identique par l'archive (requête principale) et par la route REST
 * de la recherche instantanée (wpis_rest_search_estates()) : tous les liens
 * sont donc construits à partir des filtres, jamais de l'URL courante (qui vaut
 * /wp-json/… dans le second cas).
 *
 * @package HelloImmoSync
 *
 * @var array $args ['query' => WP_Query, 'filters' => array<string,string>].
 */

defined( 'ABSPATH' ) || exit;

$wpis_query   = $args['query'];
$wpis_filters = $args['filters'];
$wpis_total   = (int) $wpis_query->found_posts;
$wpis_paged   = max( 1, (int) $wpis_query->get( 'paged' ) );

$wpis_sorts = array(
	''           => __( 'Plus récents', 'hello-immosync' ),
	'price-asc'  => __( 'Prix croissant', 'hello-immosync' ),
	'price-desc' => __( 'Prix décroissant', 'hello-immosync' ),
);
?>
<div class="wpis-results" data-wpis-results aria-live="polite">

	<!-- Barre de résultats + tri -->
	<div class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
		<p class="font-body text-sm text-text-secondary">
			<?php
			printf(
				/* translators: %s: number of properties. */
				esc_html( _n( '%s bien disponible', '%s biens disponibles', $wpis_total, 'hello-immosync' ) ),
				'<span class="font-medium text-text-strong">' . esc_html( wpis_format_number( $wpis_total ) ) . '</span>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
			?>
		</p>

		<div class="flex flex-wrap items-center gap-1 text-sm">
			<span class="mr-2 text-text-muted"><?php esc_html_e( 'Trier :', 'hello-immosync' ); ?></span>
			<?php foreach ( $wpis_sorts as $wpis_key => $wpis_label ) : ?>
				<?php
				$wpis_url    = wpis_estates_url( array_merge( $wpis_filters, array( 'sort' => $wpis_key ) ) );
				$wpis_active = ( $wpis_filters['sort'] === $wpis_key );
				?>
				<a href="<?php echo esc_url( $wpis_url ); ?>" data-wpis-results-link
					class="rounded-[var(--radius-pill)] px-3 py-1 transition-colors <?php echo $wpis_active ? 'bg-text-strong text-surface' : 'text-text-secondary hover:text-text-strong'; ?>"
					<?php echo $wpis_active ? 'aria-current="true"' : ''; ?>>
					<?php echo esc_html( $wpis_label ); ?>
				</a>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ( $wpis_query->have_posts() ) : ?>
		<div class="grid grid-cols-1 gap-x-8 gap-y-14 sm:grid-cols-2 lg:grid-cols-3">
			<?php
			while ( $wpis_query->have_posts() ) :
				$wpis_query->the_post();
				wpis_estate_card();
			endwhile;
			wp_reset_postdata();
			?>
		</div>

		<?php
		// Pagination construite depuis l'archive + les filtres (pas depuis l'URL courante).
		$wpis_links = paginate_links(
			array(
				'base'      => trailingslashit( get_post_type_archive_link( 'wpis_estates' ) ) . '%_%',
				'format'    => $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( $GLOBALS['wp_rewrite']->pagination_base . '/%#%', 'paged' ) : '?paged=%#%',
				'add_args'  => wpis_filters_to_query_args( $wpis_filters ),
				'current'   => $wpis_paged,
				'total'     => (int) $wpis_query->max_num_pages,
				'mid_size'  => 1,
				'prev_text' => esc_html__( 'Précédent', 'hello-immosync' ),
				'next_text' => esc_html__( 'Suivant', 'hello-immosync' ),
			)
		);
		if ( $wpis_links ) :
			// Liens marqués pour que la recherche instantanée les intercepte.
			$wpis_links = str_replace( '<a class="', '<a data-wpis-results-link class="', $wpis_links );
			?>
			<div class="wpis-pagination mt-16 flex justify-center">
				<?php echo _navigation_markup( $wpis_links, 'pagination', esc_html__( 'Navigation des biens', 'hello-immosync' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>

	<?php else : ?>
		<div class="rounded-[var(--radius-card)] border border-border bg-white px-8 py-20 text-center">
			<p class="font-display text-2xl text-text-strong"><?php esc_html_e( 'Aucun bien ne correspond à votre recherche.', 'hello-immosync' ); ?></p>
			<p class="mt-3 text-sm text-text-secondary"><?php esc_html_e( 'Essayez d’élargir vos critères.', 'hello-immosync' ); ?></p>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'wpis_estates' ) ); ?>" class="wpis-btn mt-8"><?php esc_html_e( 'Réinitialiser la recherche', 'hello-immosync' ); ?></a>
		</div>
	<?php endif; ?>

</div>
