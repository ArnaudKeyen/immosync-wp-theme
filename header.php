<?php
/**
 * En-tête du site.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'bg-cream text-charcoal' ); ?>>
<?php wp_body_open(); ?>

<a class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[100] focus:bg-ink focus:px-4 focus:py-2 focus:text-cream" href="#wpis-content">
	<?php esc_html_e( 'Aller au contenu', 'hello-immosync' ); ?>
</a>

<header class="wpis-header sticky top-0 z-50 border-b border-line bg-cream/95 backdrop-blur" data-wpis-header>
	<div class="wpis-container-wide">
		<div class="flex items-center justify-between py-5">

			<div class="wpis-brand">
				<?php if ( has_custom_logo() ) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="font-display text-2xl tracking-tight text-ink">
						<?php bloginfo( 'name' ); ?>
					</a>
				<?php endif; ?>
			</div>

			<nav class="hidden items-center gap-9 lg:flex" aria-label="<?php esc_attr_e( 'Navigation principale', 'hello-immosync' ); ?>">
				<?php
				if ( has_nav_menu( 'primary' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'menu_class'     => 'flex items-center gap-9',
							'depth'          => 1,
							'link_before'    => '<span class="font-body text-sm tracking-wide text-ink transition-colors hover:text-brand">',
							'link_after'     => '</span>',
							'fallback_cb'    => false,
						)
					);
				} else {
					$wpis_estates_link = get_post_type_archive_link( 'wpis_estates' );
					if ( $wpis_estates_link ) {
						printf(
							'<a class="font-body text-sm tracking-wide text-ink transition-colors hover:text-brand" href="%s">%s</a>',
							esc_url( $wpis_estates_link ),
							esc_html__( 'Nos biens', 'hello-immosync' )
						);
					}
				}
				?>
			</nav>

			<div class="flex items-center gap-4">
				<?php
				$wpis_estimation = get_page_by_path( 'services/estimation' );
				$wpis_estimation_link = $wpis_estimation ? get_permalink( $wpis_estimation ) : '';
				if ( $wpis_estimation_link ) :
					?>
					<a href="<?php echo esc_url( $wpis_estimation_link ); ?>" class="wpis-btn hidden sm:inline-flex">
						<?php esc_html_e( 'Estimation', 'hello-immosync' ); ?>
					</a>
				<?php endif; ?>

				<button type="button" class="lg:hidden" data-wpis-menu-toggle aria-expanded="false" aria-controls="wpis-mobile-menu" aria-label="<?php esc_attr_e( 'Ouvrir le menu', 'hello-immosync' ); ?>">
					<span class="block h-px w-7 bg-ink"></span>
					<span class="mt-1.5 block h-px w-7 bg-ink"></span>
					<span class="mt-1.5 block h-px w-5 bg-ink"></span>
				</button>
			</div>

		</div>
	</div>

	<!-- Menu mobile -->
	<div id="wpis-mobile-menu" class="hidden border-t border-line bg-cream lg:hidden" data-wpis-mobile-menu>
		<div class="wpis-container py-6">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'flex flex-col gap-4',
						'depth'          => 1,
						'link_before'    => '<span class="font-display text-2xl text-ink">',
						'link_after'     => '</span>',
						'fallback_cb'    => false,
					)
				);
			}
			?>
		</div>
	</div>
</header>

<main id="wpis-content" class="wpis-main">
