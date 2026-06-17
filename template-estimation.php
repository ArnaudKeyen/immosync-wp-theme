<?php
/**
 * Template Name: Estimation
 *
 * Page estimation orientée conversion :
 *  - en-tête + formulaire wpis dès le début de la page (colonne de droite) ;
 *  - arguments commerciaux pour instaurer la confiance envers La Maison Claire ;
 *  - déroulé « comment ça marche » + réassurance finale.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$wpis_eyebrow  = wpis_page_field( 'page_hero_eyebrow', __( 'Estimation gratuite & sans engagement', 'hello-immosync' ) );
	$wpis_title    = wpis_page_field( 'page_hero_titre', get_the_title() );
	$wpis_subtitle = wpis_page_field( 'page_hero_sous_titre', '' );

	// Arguments de confiance (icône / titre / texte).
	$wpis_arguments = array(
		array(
			'icon'  => 'area',
			'title' => __( 'Une estimation juste, sans complaisance', 'hello-immosync' ),
			'text'  => __( 'Une valeur fondée sur des données de marché réelles et des biens comparables — jamais sur une promesse séduisante mais intenable.', 'hello-immosync' ),
		),
		array(
			'icon'  => 'location',
			'title' => __( 'Une connaissance fine du terrain', 'hello-immosync' ),
			'text'  => __( 'Nos experts connaissent votre quartier, ses prix et ses dynamiques. Une lecture locale qu’aucun algorithme ne remplace.', 'hello-immosync' ),
		),
		array(
			'icon'  => 'shield',
			'title' => __( 'Une confidentialité absolue', 'hello-immosync' ),
			'text'  => __( 'Votre démarche reste discrète. Vos informations ne sont ni partagées, ni revendues, ni diffusées à votre insu.', 'hello-immosync' ),
		),
		array(
			'icon'  => 'check',
			'title' => __( 'Aucun engagement', 'hello-immosync' ),
			'text'  => __( 'L’estimation est offerte et ne vous engage à rien. Vous restez libre de chacune de vos décisions.', 'hello-immosync' ),
		),
		array(
			'icon'  => 'handshake',
			'title' => __( 'Un accompagnement de bout en bout', 'hello-immosync' ),
			'text'  => __( 'De l’estimation à la signature, un interlocuteur dédié veille sur chaque détail, avec exigence et discrétion.', 'hello-immosync' ),
		),
		array(
			'icon'  => 'clock',
			'title' => __( 'Une réponse rapide', 'hello-immosync' ),
			'text'  => __( 'Nous revenons vers vous sans délai, avec une analyse claire, argumentée et personnalisée.', 'hello-immosync' ),
		),
	);

	// Déroulé en trois étapes.
	$wpis_steps = array(
		array(
			'title' => __( 'Décrivez votre bien', 'hello-immosync' ),
			'text'  => __( 'Quelques minutes suffisent : surface, localisation, caractéristiques essentielles.', 'hello-immosync' ),
		),
		array(
			'title' => __( 'Nos experts analysent', 'hello-immosync' ),
			'text'  => __( 'Nous étudions votre bien à la lumière du marché local et des transactions comparables.', 'hello-immosync' ),
		),
		array(
			'title' => __( 'Recevez votre estimation', 'hello-immosync' ),
			'text'  => __( 'Une valeur argumentée, accompagnée d’un échange personnalisé avec votre conseiller.', 'hello-immosync' ),
		),
	);
	?>

	<!-- En-tête + formulaire (le formulaire wpis est placé dès le début de la page) -->
	<section class="relative overflow-hidden bg-ink text-cream">
		<div class="absolute inset-0 bg-gradient-to-br from-ink via-charcoal to-brand-dark opacity-90"></div>
		<div class="relative wpis-section">
			<div class="wpis-container-wide">
				<div class="grid items-start gap-12 lg:grid-cols-[1.05fr_1fr] lg:gap-16">

					<!-- Colonne gauche : promesse + réassurances rapides -->
					<div class="lg:pt-6">
						<?php if ( $wpis_eyebrow ) : ?>
							<p class="wpis-eyebrow text-brand"><?php echo esc_html( $wpis_eyebrow ); ?></p>
						<?php endif; ?>
						<h1 class="mt-5 max-w-2xl font-display text-4xl leading-[1.05] text-cream md:text-6xl">
							<?php echo esc_html( $wpis_title ); ?>
						</h1>
						<?php if ( $wpis_subtitle ) : ?>
							<p class="mt-6 max-w-xl font-body text-lg text-cream/80"><?php echo esc_html( $wpis_subtitle ); ?></p>
						<?php elseif ( '' !== trim( get_the_content() ) ) : ?>
							<div class="wpis-prose mt-6 max-w-xl text-lg text-cream/80 [&_p]:text-cream/80">
								<?php the_content(); ?>
							</div>
						<?php endif; ?>

						<ul class="mt-10 grid max-w-xl gap-4 sm:grid-cols-2">
							<?php
							$wpis_quick = array(
								__( 'Estimation gratuite', 'hello-immosync' ),
								__( 'Sans engagement', 'hello-immosync' ),
								__( 'Confidentialité garantie', 'hello-immosync' ),
								__( 'Réalisée par des experts', 'hello-immosync' ),
							);
							foreach ( $wpis_quick as $wpis_point ) :
								?>
								<li class="flex items-center gap-3 font-body text-sm text-cream/90">
									<span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-brand/20 text-brand">
										<?php echo wpis_icon( 'check', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</span>
									<?php echo esc_html( $wpis_point ); ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<!-- Colonne droite : formulaire wpis -->
					<div id="estimation" class="wpis-form-shell rounded-[var(--radius-card)] bg-cream p-7 text-charcoal shadow-2xl sm:p-9 lg:sticky lg:top-28">
						<p class="wpis-eyebrow mb-2"><?php esc_html_e( 'Votre estimation', 'hello-immosync' ); ?></p>
						<h2 class="font-display text-2xl text-ink"><?php esc_html_e( 'Décrivez votre bien', 'hello-immosync' ); ?></h2>
						<p class="mt-2 font-body text-sm text-stone"><?php esc_html_e( 'Réponse personnalisée, sans aucun engagement de votre part.', 'hello-immosync' ); ?></p>
						<div class="mt-6">
							<?php
							if ( shortcode_exists( 'wpis-form-evaluation' ) ) {
								echo do_shortcode( '[wpis-form-evaluation style="off"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} else {
								printf(
									'<a class="wpis-btn w-full" href="mailto:%1$s?subject=%2$s">%3$s</a>',
									esc_attr( get_option( 'admin_email' ) ),
									esc_attr( rawurlencode( __( 'Demande d’estimation', 'hello-immosync' ) ) ),
									esc_html__( 'Demander une estimation', 'hello-immosync' )
								);
							}
							?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</section>

	<!-- Arguments commerciaux : pourquoi faire confiance à La Maison Claire -->
	<section class="wpis-section">
		<div class="wpis-container">
			<div class="mx-auto max-w-2xl text-center">
				<p class="wpis-eyebrow mb-3"><?php esc_html_e( 'Pourquoi nous confier votre bien', 'hello-immosync' ); ?></p>
				<h2 class="wpis-title">
					<?php
					printf(
						/* translators: %s: nom de l'agence. */
						esc_html__( 'L’exigence %s', 'hello-immosync' ),
						esc_html( get_bloginfo( 'name' ) )
					);
					?>
				</h2>
				<p class="wpis-prose mx-auto mt-6 max-w-xl">
					<?php esc_html_e( 'Estimer, c’est déjà s’engager auprès de vous. Voici ce qui guide chacune de nos estimations.', 'hello-immosync' ); ?>
				</p>
			</div>

			<div class="mt-14 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
				<?php foreach ( $wpis_arguments as $wpis_arg ) : ?>
					<div class="flex flex-col rounded-[var(--radius-card)] border border-line bg-cream p-8">
						<span class="flex h-12 w-12 items-center justify-center rounded-full bg-sand text-brand">
							<?php echo wpis_icon( $wpis_arg['icon'], 'w-6 h-6' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</span>
						<h3 class="mt-6 font-display text-xl text-ink"><?php echo esc_html( $wpis_arg['title'] ); ?></h3>
						<p class="wpis-prose mt-3 text-sm"><?php echo esc_html( $wpis_arg['text'] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- Déroulé : comment ça marche -->
	<section class="wpis-section bg-sand">
		<div class="wpis-container">
			<div class="mx-auto max-w-2xl text-center">
				<p class="wpis-eyebrow mb-3"><?php esc_html_e( 'Simple et transparent', 'hello-immosync' ); ?></p>
				<h2 class="wpis-title"><?php esc_html_e( 'Comment ça se passe', 'hello-immosync' ); ?></h2>
			</div>

			<ol class="mt-14 grid gap-8 md:grid-cols-3">
				<?php foreach ( $wpis_steps as $wpis_index => $wpis_step ) : ?>
					<li class="relative rounded-[var(--radius-card)] bg-cream p-8">
						<span class="font-display text-5xl text-brand/30"><?php echo esc_html( sprintf( '%02d', $wpis_index + 1 ) ); ?></span>
						<h3 class="mt-4 font-display text-xl text-ink"><?php echo esc_html( $wpis_step['title'] ); ?></h3>
						<p class="wpis-prose mt-3 text-sm"><?php echo esc_html( $wpis_step['text'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>

			<div class="mt-12 text-center">
				<a href="#estimation" class="wpis-btn">
					<?php esc_html_e( 'Estimer mon bien', 'hello-immosync' ); ?>
					<?php echo wpis_icon( 'arrow', 'w-4 h-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</div>
		</div>
	</section>

	<?php
	// Réassurance finale : contact direct pour les hésitants.
	$wpis_contact = get_page_by_path( 'contact' );
	if ( $wpis_contact ) :
		?>
		<section class="wpis-section pt-0">
			<div class="wpis-container">
				<div class="flex flex-col items-center gap-6 rounded-[var(--radius-card)] border border-line bg-cream p-10 text-center md:flex-row md:justify-between md:text-left">
					<div class="max-w-xl">
						<h2 class="font-display text-2xl text-ink"><?php esc_html_e( 'Une question avant de vous lancer ?', 'hello-immosync' ); ?></h2>
						<p class="wpis-prose mt-2 text-sm"><?php esc_html_e( 'Nos conseillers sont à votre écoute pour vous guider, en toute simplicité.', 'hello-immosync' ); ?></p>
					</div>
					<a href="<?php echo esc_url( get_permalink( $wpis_contact ) ); ?>" class="wpis-btn-outline shrink-0">
						<?php esc_html_e( 'Nous contacter', 'hello-immosync' ); ?>
					</a>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
endwhile;

get_footer();
