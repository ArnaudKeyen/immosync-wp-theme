<?php
/**
 * Pied de page du site.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;
?>
</main><!-- #wpis-content -->

<footer class="wpis-footer mt-auto bg-ink text-cream/80">
	<div class="wpis-container-wide py-16 md:py-20">
		<div class="grid gap-12 md:grid-cols-2 lg:grid-cols-4">

			<div class="lg:col-span-2">
				<p class="font-display text-3xl text-cream"><?php bloginfo( 'name' ); ?></p>
				<?php $wpis_desc = get_bloginfo( 'description' ); ?>
				<?php if ( $wpis_desc ) : ?>
					<p class="mt-4 max-w-md text-sm leading-relaxed text-cream/60"><?php echo esc_html( $wpis_desc ); ?></p>
				<?php endif; ?>
			</div>

			<div>
				<p class="wpis-eyebrow mb-5"><?php esc_html_e( 'Navigation', 'hello-immosync' ); ?></p>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'menu_class'     => 'flex flex-col gap-3 text-sm text-cream/70',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
				} else {
					$wpis_estates_link = get_post_type_archive_link( 'wpis_estates' );
					if ( $wpis_estates_link ) {
						printf(
							'<a class="text-sm text-cream/70 transition-colors hover:text-cream" href="%s">%s</a>',
							esc_url( $wpis_estates_link ),
							esc_html__( 'Nos biens', 'hello-immosync' )
						);
					}
				}
				?>
			</div>

			<div>
				<p class="wpis-eyebrow mb-5"><?php esc_html_e( 'Contact', 'hello-immosync' ); ?></p>
				<?php
				$wpis_email   = wpis_theme_option( 'option_agency_email', get_option( 'admin_email' ) );
				$wpis_phone   = wpis_theme_option( 'option_agency_phone', '' );
				$wpis_socials = function_exists( 'get_field' ) ? get_field( 'option_socials', 'option' ) : array();
				?>
				<p class="text-sm leading-relaxed text-cream/70">
					<?php if ( $wpis_phone ) : ?>
						<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $wpis_phone ) ); ?>" class="block transition-colors hover:text-cream"><?php echo esc_html( $wpis_phone ); ?></a>
					<?php endif; ?>
					<?php if ( $wpis_email ) : ?>
						<a href="mailto:<?php echo esc_attr( $wpis_email ); ?>" class="block transition-colors hover:text-cream"><?php echo esc_html( $wpis_email ); ?></a>
					<?php endif; ?>
				</p>
				<?php if ( is_array( $wpis_socials ) && $wpis_socials ) : ?>
					<ul class="mt-4 flex flex-wrap gap-x-4 gap-y-1 text-sm text-cream/70">
						<?php foreach ( $wpis_socials as $wpis_social ) : ?>
							<?php if ( ! empty( $wpis_social['url'] ) ) : ?>
								<li>
									<a href="<?php echo esc_url( $wpis_social['url'] ); ?>" class="transition-colors hover:text-cream" target="_blank" rel="noopener noreferrer">
										<?php echo esc_html( ! empty( $wpis_social['label'] ) ? $wpis_social['label'] : $wpis_social['url'] ); ?>
									</a>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

		</div>

		<div class="mt-14 flex flex-col items-start justify-between gap-4 border-t border-cream/10 pt-8 text-xs text-cream/50 sm:flex-row sm:items-center">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Tous droits réservés.', 'hello-immosync' ); ?></p>
			<p><?php echo esc_html( wpis_theme_option( 'option_footer_note', __( 'Biens immobiliers synchronisés via ImmoSync.', 'hello-immosync' ) ) ); ?></p>
		</div>
	</div>

	<?php
	// Sous-footer : mentions légales IPI (agences immobilières belges).
	// Masqué tant que le numéro IPI n'est pas renseigné.
	$wpis_ipi = wpis_theme_option( 'option_ipi_number', '' );
	if ( $wpis_ipi ) :
		$wpis_vat       = wpis_theme_option( 'option_company_vat', '' );
		$wpis_insurer   = wpis_theme_option( 'option_insurer', 'AXA Belgium SA.' );
		$wpis_policy    = wpis_theme_option( 'option_insurance_policy', '' );
		$wpis_rgpd      = wpis_theme_option( 'option_rgpd_manager', '' );
		$wpis_rgpd_role = wpis_theme_option( 'option_rgpd_manager_role', 'gérant' );
		$wpis_logo_id   = (int) wpis_theme_option( 'option_ipi_logo', 0 );
		?>
		<div class="wpis-subfooter border-t border-cream/10 bg-charcoal">
			<div class="wpis-container-wide py-10">
				<div class="flex flex-col gap-6 sm:flex-row sm:items-start">

					<div class="shrink-0 self-start rounded-[var(--radius-card)] bg-cream p-3">
						<?php
						if ( $wpis_logo_id ) {
							echo wp_get_attachment_image(
								$wpis_logo_id,
								'thumbnail',
								false,
								array(
									'class' => 'h-16 w-auto',
									'alt'   => 'IPI - BIV',
								)
							);
						} else {
							printf(
								'<img src="%s" width="64" height="64" class="block h-16 w-16" alt="%s">',
								esc_url( get_template_directory_uri() . '/assets/images/ipi-logo.svg' ),
								esc_attr__( 'IPI - BIV', 'hello-immosync' )
							);
						}
						?>
					</div>

					<div class="text-xs leading-relaxed text-cream/45">
						<p class="wpis-eyebrow mb-3 text-cream/60"><?php esc_html_e( 'Autorité de surveillance', 'hello-immosync' ); ?></p>
						<p>
							<?php
							printf(
								/* translators: %s: numéro d'agrément IPI. */
								esc_html__( 'Agent immobilier intermédiaire agréé IPI sous le numéro %s octroyé en Belgique.', 'hello-immosync' ),
								esc_html( $wpis_ipi )
							);
							?>
							<?php if ( $wpis_vat ) : ?>
								<br><?php printf( esc_html__( 'N° entreprise : TVA %s', 'hello-immosync' ), esc_html( $wpis_vat ) ); ?>
							<?php endif; ?>
						</p>
						<p class="mt-3">
							<?php
							printf(
								/* translators: %s: lien vers le site de l'IPI. */
								esc_html__( 'Autorité de surveillance : IPI, rue de Luxembourg 16B, 1000 Bruxelles – Soumis au code déontologique de l’IPI : %s', 'hello-immosync' ),
								'<a class="underline transition-colors hover:text-cream" href="https://www.ipi.be" target="_blank" rel="noopener noreferrer">www.ipi.be</a>'
							);
							?>
						</p>
						<?php if ( $wpis_insurer || $wpis_policy ) : ?>
							<p class="mt-3">
								<?php
								echo esc_html__( 'Assurance responsabilité civile professionnelle et cautionnement', 'hello-immosync' ) . ' : ' . esc_html( $wpis_insurer );
								if ( $wpis_policy ) {
									/* translators: %s: numéro de police d'assurance. */
									echo ' – ' . esc_html( sprintf( __( 'police n° %s', 'hello-immosync' ), $wpis_policy ) );
								}
								?>
							</p>
						<?php endif; ?>
						<?php if ( $wpis_rgpd ) : ?>
							<p class="mt-3">
								<?php
								printf(
									/* translators: 1: nom de l'agence, 2: nom du responsable, 3: fonction. */
									esc_html__( 'Responsable en charge du RGPD et du respect de la loi sur le blanchiment d’argent au sein de l’agence %1$s : %2$s (%3$s)', 'hello-immosync' ),
									esc_html( get_bloginfo( 'name' ) ),
									esc_html( $wpis_rgpd ),
									esc_html( $wpis_rgpd_role )
								);
								?>
							</p>
						<?php endif; ?>
					</div>

				</div>
			</div>
		</div>
	<?php endif; ?>
</footer>

<?php wp_footer(); ?>
</body>
</html>
