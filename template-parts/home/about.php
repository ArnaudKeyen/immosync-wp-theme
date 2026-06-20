<?php
/**
 * Accueil — présentation de l'agence.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

$wpis_name = get_bloginfo( 'name' );

// Titre par défaut (nom de l'agence injecté) si le champ ACF est vide.
$wpis_about_title_default = sprintf(
	/* translators: %s: agency name. */
	__( 'À propos de %s', 'hello-immosync' ),
	$wpis_name
);

// Texte par défaut (HTML maîtrisé) si le champ WYSIWYG est vide.
$wpis_about_text_default = '<p>' . __( 'Nous accompagnons chaque projet immobilier avec rigueur et disponibilité, de la première visite à la signature.', 'hello-immosync' ) . '</p>'
	. '<p>' . __( 'Notre équipe met sa connaissance du marché local au service de vos projets d’achat et de vente.', 'hello-immosync' ) . '</p>';

$wpis_about_image = (int) wpis_home_field( 'home_about_image', 0 );
?>
<section class="wpis-section">
	<div class="wpis-container-wide">
		<div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-20">
			<div class="order-2 lg:order-1">
				<?php if ( $wpis_about_image ) : ?>
					<div class="aspect-[4/5] overflow-hidden rounded-[var(--radius-card)] bg-sand">
						<?php echo wp_get_attachment_image( $wpis_about_image, 'wpis-card-2x', false, array( 'class' => 'h-full w-full object-cover' ) ); ?>
					</div>
				<?php else : ?>
					<div class="aspect-[4/5] overflow-hidden rounded-[var(--radius-card)] bg-gradient-to-br from-sand to-line"></div>
				<?php endif; ?>
			</div>
			<div class="order-1 lg:order-2">
				<p class="wpis-eyebrow mb-3"><?php echo esc_html( wpis_home_field( 'home_about_eyebrow', __( 'L’agence', 'hello-immosync' ) ) ); ?></p>
				<h2 class="wpis-title">
					<?php echo esc_html( wpis_home_field( 'home_about_titre', $wpis_about_title_default ) ); ?>
				</h2>
				<div class="wpis-prose mt-6 max-w-lg">
					<?php echo wp_kses_post( wpis_home_field( 'home_about_texte', $wpis_about_text_default ) ); ?>
				</div>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'wpis_estates' ) ); ?>" class="wpis-btn mt-8">
					<?php echo esc_html( wpis_home_field( 'home_about_bouton', __( 'Découvrir nos biens', 'hello-immosync' ) ) ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
