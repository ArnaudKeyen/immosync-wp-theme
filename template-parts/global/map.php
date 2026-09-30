<?php
/**
 * Carte OpenStreetMap (iframe d'intégration, sans script ni clé d'API).
 *
 * @package HelloImmoSync
 *
 * @var array $args ['lat' => float, 'lng' => float, 'title' => string,
 *                   'delta' => float (demi-étendue en degrés, défaut 0.008),
 *                   'class' => string (classes de l'iframe, défaut h-[360px] w-full)].
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $args['lat'], $args['lng'] ) || ! is_numeric( $args['lat'] ) || ! is_numeric( $args['lng'] ) ) {
	return;
}

$wpis_lat   = (float) $args['lat'];
$wpis_lng   = (float) $args['lng'];
$wpis_delta = isset( $args['delta'] ) ? (float) $args['delta'] : 0.008;
$wpis_bbox  = sprintf( '%F,%F,%F,%F', $wpis_lng - $wpis_delta, $wpis_lat - $wpis_delta, $wpis_lng + $wpis_delta, $wpis_lat + $wpis_delta );
$wpis_src   = add_query_arg(
	array(
		'bbox'   => $wpis_bbox,
		'layer'  => 'mapnik',
		'marker' => $wpis_lat . ',' . $wpis_lng,
	),
	'https://www.openstreetmap.org/export/embed.html'
);
?>
<div class="overflow-hidden rounded-[var(--radius-card)] border border-border">
	<iframe
		title="<?php echo esc_attr( ! empty( $args['title'] ) ? $args['title'] : __( 'Carte', 'hello-immosync' ) ); ?>"
		src="<?php echo esc_url( $wpis_src ); ?>"
		class="<?php echo esc_attr( ! empty( $args['class'] ) ? $args['class'] : 'h-[360px] w-full' ); ?>"
		loading="lazy"
		referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>
