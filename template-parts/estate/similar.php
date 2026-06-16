<?php
/**
 * Biens similaires (même catégorie ou même ville).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid      = get_the_ID();
$wpis_category = wpis_get_category( $wpis_pid );
$wpis_city     = wpis_get_field( 'wpis_address_city', $wpis_pid, '' );

$wpis_meta = array( 'relation' => 'OR' );
if ( '' !== $wpis_category ) {
	$wpis_meta[] = array(
		'key'   => 'wpis_category_label',
		'value' => $wpis_category,
	);
}
if ( '' !== $wpis_city ) {
	$wpis_meta[] = array(
		'key'   => 'wpis_address_city',
		'value' => $wpis_city,
	);
}

$wpis_args = array(
	'posts_per_page' => 3,
	'post__not_in'   => array( $wpis_pid ),
	'orderby'        => 'rand',
);
if ( count( $wpis_meta ) > 1 ) {
	$wpis_args['meta_query'] = $wpis_meta;
}

$wpis_similar = wpis_query_estates( $wpis_args );

// Repli : derniers biens si aucune correspondance.
if ( ! $wpis_similar->have_posts() ) {
	$wpis_similar = wpis_query_estates(
		array(
			'posts_per_page' => 3,
			'post__not_in'   => array( $wpis_pid ),
		)
	);
}

if ( ! $wpis_similar->have_posts() ) {
	return;
}
?>
<section class="wpis-section bg-sand" aria-labelledby="wpis-similar-title">
	<div class="wpis-container-wide">
		<div class="mb-10">
			<p class="wpis-eyebrow mb-2"><?php esc_html_e( 'À découvrir également', 'hello-immosync' ); ?></p>
			<h2 id="wpis-similar-title" class="font-display text-3xl text-ink"><?php esc_html_e( 'Biens similaires', 'hello-immosync' ); ?></h2>
		</div>

		<div class="grid grid-cols-1 gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
			<?php
			while ( $wpis_similar->have_posts() ) :
				$wpis_similar->the_post();
				wpis_estate_card();
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
