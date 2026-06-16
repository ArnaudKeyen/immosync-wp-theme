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
				<p class="text-sm leading-relaxed text-cream/70">
					<?php echo esc_html( get_option( 'admin_email' ) ); ?>
				</p>
			</div>

		</div>

		<div class="mt-14 flex flex-col items-start justify-between gap-4 border-t border-cream/10 pt-8 text-xs text-cream/50 sm:flex-row sm:items-center">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Tous droits réservés.', 'hello-immosync' ); ?></p>
			<p><?php esc_html_e( 'Biens immobiliers synchronisés via ImmoSync.', 'hello-immosync' ); ?></p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
