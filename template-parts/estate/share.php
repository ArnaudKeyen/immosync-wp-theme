<?php
/**
 * Partage d'un bien (module « share ») : WhatsApp, Facebook, copie du lien, et
 * feuille de partage native du téléphone quand elle existe.
 *
 * Liens de partage classiques (aucun script tiers, aucun cookie) ; le JS
 * (WPIS.share, assets/js/main.js) n'ajoute que la copie et le partage natif.
 *
 * @package HelloImmoSync
 *
 * @var array $args Optionnel : ['tone' => 'default'|'inverse'] (inverse = sur image / fond sombre).
 */

defined( 'ABSPATH' ) || exit;

if ( ! wpis_module_enabled( 'share' ) ) {
	return;
}

$wpis_pid   = get_the_ID();
$wpis_url   = get_permalink( $wpis_pid );
$wpis_title = wpis_get_title( $wpis_pid );
$wpis_tone  = ( ! empty( $args['tone'] ) && 'inverse' === $args['tone'] ) ? 'inverse' : 'default';

$wpis_networks = array(
	'whatsapp' => array(
		'label' => __( 'Partager sur WhatsApp', 'hello-immosync' ),
		'icon'  => 'whatsapp',
		'url'   => 'https://wa.me/?text=' . rawurlencode( $wpis_title . ' — ' . $wpis_url ),
	),
	'facebook' => array(
		'label' => __( 'Partager sur Facebook', 'hello-immosync' ),
		'icon'  => 'facebook',
		'url'   => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $wpis_url ),
	),
);

/**
 * Réseaux proposés dans le bloc de partage (clé => label, icon, url).
 *
 * @param array  $networks Réseaux.
 * @param int    $post_id  ID du bien.
 * @param string $url      URL partagée.
 * @param string $title    Titre du bien.
 */
$wpis_networks = apply_filters( 'wpis_share_networks', $wpis_networks, $wpis_pid, $wpis_url, $wpis_title );
?>
<div class="wpis-share wpis-share--<?php echo esc_attr( $wpis_tone ); ?>" data-wpis-share
	data-url="<?php echo esc_url( $wpis_url ); ?>" data-title="<?php echo esc_attr( $wpis_title ); ?>">
	<span class="wpis-share-label"><?php esc_html_e( 'Partager', 'hello-immosync' ); ?></span>

	<?php // Feuille de partage native (mobile) : révélée par le JS si navigator.share existe. ?>
	<button type="button" class="wpis-share-btn" data-wpis-share-native hidden
		aria-label="<?php esc_attr_e( 'Partager ce bien', 'hello-immosync' ); ?>">
		<?php echo wpis_icon( 'share', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</button>

	<?php foreach ( $wpis_networks as $wpis_key => $wpis_network ) : ?>
		<a class="wpis-share-btn" href="<?php echo esc_url( $wpis_network['url'] ); ?>" target="_blank" rel="noopener noreferrer"
			data-wpis-share-network="<?php echo esc_attr( $wpis_key ); ?>"
			aria-label="<?php echo esc_attr( $wpis_network['label'] ); ?>" title="<?php echo esc_attr( $wpis_network['label'] ); ?>">
			<?php echo wpis_icon( $wpis_network['icon'], 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
	<?php endforeach; ?>

	<button type="button" class="wpis-share-btn" data-wpis-share-copy
		aria-label="<?php esc_attr_e( 'Copier le lien', 'hello-immosync' ); ?>" title="<?php esc_attr_e( 'Copier le lien', 'hello-immosync' ); ?>">
		<?php echo wpis_icon( 'link', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</button>

	<span class="wpis-share-status" data-wpis-share-status role="status" aria-live="polite"
		data-copied="<?php esc_attr_e( 'Lien copié', 'hello-immosync' ); ?>"
		data-failed="<?php esc_attr_e( 'Copie impossible', 'hello-immosync' ); ?>"></span>
</div>
