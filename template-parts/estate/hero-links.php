<?php
/**
 * Bandeau d'accès médias de la fiche bien : visite 360°, vidéo, rendez-vous.
 *
 * Affordance secondaire (icônes) commune à toutes les variantes de hero. Les
 * boutons médias sont des ancres internes vers la section « Visite 360° & vidéo »
 * (template-parts/estate/media.php) ; le rendez-vous reste un lien externe.
 *
 * Le bandeau est construit d'abord, rendu ensuite : sans bouton, rien n'est
 * affiché (sinon on laissait une bande bordée vide sur les biens dont le seul
 * lien renseigné n'est pas rendu ici).
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_pid   = get_the_ID();
$wpis_media = wpis_get_estate_media( $wpis_pid );
$wpis_links = wpis_get_links( $wpis_pid );

// La section médias peut avoir été désactivée dans « Réglages du thème → Fiches
// de biens » : dans ce cas l'ancre ne mènerait nulle part, on ouvre le média
// directement dans un nouvel onglet.
$wpis_sections   = wpis_get_estate_sections();
$wpis_has_anchor = isset( $wpis_sections['media'] );

$wpis_buttons = array();

if ( ! empty( $wpis_media['tour']['url'] ) ) {
	$wpis_buttons[] = array(
		'href'     => $wpis_has_anchor ? '#wpis-visit360' : $wpis_media['tour']['url'],
		'icon'     => 'view360',
		'label'    => __( 'Visite 360°', 'hello-immosync' ),
		'external' => ! $wpis_has_anchor,
	);
}

if ( ! empty( $wpis_media['video']['url'] ) ) {
	$wpis_buttons[] = array(
		'href'     => $wpis_has_anchor ? '#wpis-video' : $wpis_media['video']['url'],
		'icon'     => 'play',
		'label'    => __( 'Vidéo', 'hello-immosync' ),
		'external' => ! $wpis_has_anchor,
	);
}

if ( ! empty( $wpis_links['appointment'] ) ) {
	$wpis_buttons[] = array(
		'href'     => $wpis_links['appointment'],
		'icon'     => 'calendar',
		'label'    => __( 'Prendre rendez-vous', 'hello-immosync' ),
		'external' => true,
	);
}

if ( ! $wpis_buttons ) {
	return;
}
?>
<div class="mt-8 border-y border-border bg-surface">
	<div class="wpis-container-wide flex flex-wrap gap-3 py-4">
		<?php foreach ( $wpis_buttons as $wpis_button ) : ?>
			<a href="<?php echo esc_url( $wpis_button['href'] ); ?>"
				class="wpis-btn-outline text-xs"
				<?php if ( ! empty( $wpis_button['external'] ) ) : ?>
					target="_blank" rel="noopener"
				<?php endif; ?>>
				<?php echo wpis_icon( $wpis_button['icon'], 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo esc_html( $wpis_button['label'] ); ?>
			</a>
		<?php endforeach; ?>
	</div>
</div>
