<?php
/**
 * Écran « Bientôt en ligne » (coming soon) — fonctionnalité générique du thème.
 *
 * Remplace tout le front par un écran d'attente plein écran tant que le
 * visiteur n'est pas connecté (droit `edit_posts`). Utile pour un pré-lancement
 * de site : l'équipe travaille sur le vrai site, le public voit l'écran.
 *
 * Désactivé par défaut (opt-in) : à activer depuis « Réglages du thème →
 * Bientôt en ligne ». Le contenu (image de fond, logo, textes, contact, bouton)
 * est éditable via ACF. Un thème enfant peut fournir des visuels par défaut via
 * les filtres `wpis_coming_soon_default_background_url` /
 * `wpis_coming_soon_default_logo_url`, sans coder de contenu dans le parent.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

/**
 * Résout une valeur de champ image ACF (tableau, ID ou URL) en URL exploitable.
 *
 * @param mixed $value Valeur brute du champ.
 * @return string URL ou chaîne vide.
 */
function wpis_coming_soon_image_url( $value ) {
	if ( is_array( $value ) ) {
		return isset( $value['url'] ) ? $value['url'] : '';
	}
	if ( is_numeric( $value ) ) {
		return (string) wp_get_attachment_image_url( (int) $value, 'full' );
	}
	return (string) $value;
}

/**
 * Détermine si l'écran « Bientôt en ligne » doit remplacer le front.
 *
 * @return bool
 */
function wpis_coming_soon_is_active() {
	// L'équipe connectée (admin, éditeurs…) voit toujours le vrai site.
	if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
		return false;
	}

	// Ne pas interférer avec l'admin, les API et les tâches techniques.
	if ( is_admin()
		|| wp_doing_ajax()
		|| wp_doing_cron()
		|| ( defined( 'REST_REQUEST' ) && REST_REQUEST )
		|| ( defined( 'WP_CLI' ) && WP_CLI )
		|| is_robots()
		|| is_favicon() ) {
		return false;
	}

	// Interrupteur (désactivé par défaut : opt-in explicite).
	$enabled = function_exists( 'get_field' ) ? get_field( 'wpis_cs_enabled', 'option' ) : false;

	/**
	 * Filtre l'état actif/inactif de l'écran « Bientôt en ligne ».
	 *
	 * @param bool $enabled Vrai si l'écran doit s'afficher.
	 */
	return (bool) apply_filters( 'wpis_coming_soon_active', (bool) $enabled );
}

/**
 * Remplace le rendu du front par l'écran « Bientôt en ligne ».
 */
function wpis_maybe_render_coming_soon() {
	if ( ! wpis_coming_soon_is_active() ) {
		return;
	}

	if ( ! headers_sent() ) {
		status_header( 503 );
		header( 'Retry-After: 86400' );
		nocache_headers();
	}

	wpis_render_coming_soon_page();
	exit;
}
add_action( 'template_redirect', 'wpis_maybe_render_coming_soon' );

/**
 * Sort le document HTML complet de l'écran « Bientôt en ligne ».
 */
function wpis_render_coming_soon_page() {
	$option = function_exists( 'wpis_theme_option' )
		? 'wpis_theme_option'
		: static function ( $name, $default = '' ) {
			$val = function_exists( 'get_field' ) ? get_field( $name, 'option' ) : '';
			return ( '' === $val || null === $val || false === $val ) ? $default : $val;
		};

	$eyebrow      = call_user_func( $option, 'wpis_cs_eyebrow', __( 'Bientôt en ligne', 'hello-immosync' ) );
	$heading      = call_user_func( $option, 'wpis_cs_heading', get_bloginfo( 'name' ) );
	$text         = call_user_func( $option, 'wpis_cs_text', __( 'Notre nouvel espace ouvre très prochainement. En attendant, notre équipe reste à votre écoute.', 'hello-immosync' ) );
	$contact_name = call_user_func( $option, 'wpis_cs_contact_name', '' );
	$phone        = call_user_func( $option, 'wpis_cs_phone', '' );
	$email        = call_user_func( $option, 'wpis_cs_email', get_bloginfo( 'admin_email' ) );
	$cta_url      = call_user_func( $option, 'wpis_cs_cta_url', '' );
	$cta_label    = call_user_func( $option, 'wpis_cs_cta_label', __( 'Découvrir nos biens', 'hello-immosync' ) );

	// Image de fond : option ACF sinon visuel par défaut fourni par l'enfant.
	$bg_url = wpis_coming_soon_image_url( call_user_func( $option, 'wpis_cs_background', '' ) );
	if ( ! $bg_url ) {
		$bg_url = apply_filters( 'wpis_coming_soon_default_background_url', '' );
	}

	// Logo : option ACF sinon visuel par défaut fourni par l'enfant.
	$logo_url = wpis_coming_soon_image_url( call_user_func( $option, 'wpis_cs_logo', '' ) );
	if ( ! $logo_url ) {
		$logo_url = apply_filters( 'wpis_coming_soon_default_logo_url', '' );
	}

	$phone_href = preg_replace( '/[^0-9+]/', '', (string) $phone );
	$fonts_url  = apply_filters( 'wpis_fonts_url', 'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500&family=Inter:wght@300;400;500&display=swap' );

	?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex, nofollow">
	<title><?php echo esc_html( $heading . ' — ' . $eyebrow ); ?></title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="stylesheet" href="<?php echo esc_url( $fonts_url ); ?>">
	<style>
		:root {
			--cs-ink: var(--color-ink, #19150f);
			--cs-cream: var(--color-cream, #fdfbf7);
			--cs-brand: var(--color-brand, #b6925a);
			--cs-line: rgba(253, 251, 247, 0.18);
			--cs-font-display: var(--font-display, "Fraunces", Georgia, serif);
			--cs-font-body: var(--font-body, "Inter", system-ui, sans-serif);
		}
		* { box-sizing: border-box; margin: 0; padding: 0; }
		html, body { height: 100%; }
		body {
			font-family: var(--cs-font-body);
			color: var(--cs-cream);
			background-color: var(--cs-ink);
			<?php if ( $bg_url ) : ?>
			background-image: linear-gradient(rgba(25, 21, 15, 0.55), rgba(25, 21, 15, 0.78)), url('<?php echo esc_url( $bg_url ); ?>');
			<?php else : ?>
			background-image: radial-gradient(120% 120% at 50% 0%, #2c2820 0%, #19150f 70%);
			<?php endif; ?>
			background-size: cover;
			background-position: center;
			background-attachment: fixed;
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 6vh 24px;
			text-align: center;
			-webkit-font-smoothing: antialiased;
		}
		.cs { max-width: 640px; display: flex; flex-direction: column; align-items: center; gap: 28px; }
		.cs__logo-img { width: clamp(150px, 34vw, 210px); height: auto; display: block; }
		.cs__logo-text { font-family: var(--cs-font-display); font-size: clamp(2rem, 5vw, 3rem); font-weight: 500; letter-spacing: -0.02em; color: var(--cs-cream); }
		.cs__eyebrow { font-size: 0.72rem; font-weight: 500; letter-spacing: 0.28em; text-transform: uppercase; color: var(--cs-brand); }
		.cs__text { font-size: clamp(1rem, 2.2vw, 1.15rem); font-weight: 300; line-height: 1.7; color: rgba(253, 251, 247, 0.86); max-width: 46ch; }
		.cs__cta { display: inline-flex; align-items: center; gap: 10px; padding: 15px 30px; background: var(--cs-brand); color: var(--cs-ink); font-size: 0.9rem; font-weight: 500; letter-spacing: 0.02em; text-decoration: none; border-radius: 2px; transition: background-color 0.25s ease, transform 0.25s ease; }
		.cs__cta:hover { background: #c9a86e; transform: translateY(-1px); }
		.cs__cta svg { width: 18px; height: 18px; }
		.cs__contact { display: flex; flex-direction: column; align-items: center; gap: 14px; margin-top: 6px; padding-top: 26px; border-top: 1px solid var(--cs-line); width: 100%; }
		.cs__contact-name { font-family: var(--cs-font-display); font-size: 1.15rem; font-weight: 500; letter-spacing: 0.01em; color: var(--cs-cream); }
		.cs__contact-links { display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 14px 28px; }
		.cs__contact a { display: inline-flex; align-items: center; gap: 9px; color: rgba(253, 251, 247, 0.9); text-decoration: none; font-size: 0.95rem; transition: color 0.2s ease; }
		.cs__contact a:hover { color: var(--cs-brand); }
		.cs__contact svg { width: 17px; height: 17px; color: var(--cs-brand); flex: none; }
	</style>
</head>
<body>
	<main class="cs">
		<div class="cs__logo">
			<?php if ( $logo_url ) : ?>
				<img class="cs__logo-img" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $heading ); ?>">
			<?php else : ?>
				<span class="cs__logo-text"><?php echo esc_html( $heading ); ?></span>
			<?php endif; ?>
		</div>

		<?php if ( $eyebrow ) : ?>
			<p class="cs__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>

		<?php if ( $text ) : ?>
			<p class="cs__text"><?php echo nl2br( esc_html( $text ) ); ?></p>
		<?php endif; ?>

		<?php if ( $cta_url ) : ?>
			<a class="cs__cta" href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="noopener">
				<?php echo esc_html( $cta_label ); ?>
				<?php echo function_exists( 'wpis_icon' ) ? wpis_icon( 'arrow' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		<?php endif; ?>

		<?php if ( $contact_name || $phone || $email ) : ?>
			<div class="cs__contact">
				<?php if ( $contact_name ) : ?>
					<p class="cs__contact-name"><?php echo esc_html( $contact_name ); ?></p>
				<?php endif; ?>
				<div class="cs__contact-links">
					<?php if ( $phone ) : ?>
						<a href="tel:<?php echo esc_attr( $phone_href ); ?>">
							<?php echo function_exists( 'wpis_icon' ) ? wpis_icon( 'phone' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( $phone ); ?></span>
						</a>
					<?php endif; ?>
					<?php if ( $email ) : ?>
						<a href="mailto:<?php echo esc_attr( $email ); ?>">
							<?php echo function_exists( 'wpis_icon' ) ? wpis_icon( 'mail' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo esc_html( $email ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</main>
</body>
</html>
	<?php
}

/* -------------------------------------------------------------------------
 * Réglages ACF : sous-page « Bientôt en ligne » + groupe de champs
 * ---------------------------------------------------------------------- */

add_action( 'acf/init', 'wpis_register_coming_soon_page' );
/**
 * Sous-page d'options « Bientôt en ligne » sous « Réglages du thème ».
 */
function wpis_register_coming_soon_page() {
	if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
		return;
	}

	acf_add_options_sub_page(
		array(
			'page_title'  => __( 'Bientôt en ligne', 'hello-immosync' ),
			'menu_title'  => __( 'Bientôt en ligne', 'hello-immosync' ),
			'menu_slug'   => 'wpis-coming-soon',
			'parent_slug' => 'wpis-theme-settings',
		)
	);
}

add_action( 'acf/init', 'wpis_register_coming_soon_fields' );
/**
 * Groupe de champs de l'écran « Bientôt en ligne ».
 */
function wpis_register_coming_soon_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_wpis_coming_soon',
			'title'    => __( 'Bientôt en ligne (Coming soon)', 'hello-immosync' ),
			'fields'   => array(
				array(
					'key'           => 'field_wpis_cs_enabled',
					'label'         => __( 'Activer la page « Bientôt en ligne »', 'hello-immosync' ),
					'name'          => 'wpis_cs_enabled',
					'type'          => 'true_false',
					'instructions'  => __( 'Affichée à tous les visiteurs non connectés. L’équipe connectée voit le site normal.', 'hello-immosync' ),
					'ui'            => 1,
					'default_value' => 0,
				),
				array(
					'key'           => 'field_wpis_cs_background',
					'label'         => __( 'Image de fond', 'hello-immosync' ),
					'name'          => 'wpis_cs_background',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'library'       => 'all',
				),
				array(
					'key'           => 'field_wpis_cs_logo',
					'label'         => __( 'Logo', 'hello-immosync' ),
					'name'          => 'wpis_cs_logo',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'library'       => 'all',
					'instructions'  => __( 'Version claire pour fond sombre. Sinon, visuel par défaut du thème enfant, ou le nom du site.', 'hello-immosync' ),
				),
				array(
					'key'         => 'field_wpis_cs_eyebrow',
					'label'       => __( 'Surtitre', 'hello-immosync' ),
					'name'        => 'wpis_cs_eyebrow',
					'type'        => 'text',
					'placeholder' => __( 'Bientôt en ligne', 'hello-immosync' ),
				),
				array(
					'key'          => 'field_wpis_cs_heading',
					'label'        => __( 'Titre (si pas de logo)', 'hello-immosync' ),
					'name'         => 'wpis_cs_heading',
					'type'         => 'text',
					'instructions' => __( 'Utilisé comme texte si aucun logo n’est défini.', 'hello-immosync' ),
				),
				array(
					'key'   => 'field_wpis_cs_text',
					'label' => __( 'Texte', 'hello-immosync' ),
					'name'  => 'wpis_cs_text',
					'type'  => 'textarea',
					'rows'  => 3,
				),
				array(
					'key'   => 'field_wpis_cs_contact_name',
					'label' => __( 'Nom du contact', 'hello-immosync' ),
					'name'  => 'wpis_cs_contact_name',
					'type'  => 'text',
				),
				array(
					'key'         => 'field_wpis_cs_phone',
					'label'       => __( 'Téléphone', 'hello-immosync' ),
					'name'        => 'wpis_cs_phone',
					'type'        => 'text',
				),
				array(
					'key'   => 'field_wpis_cs_email',
					'label' => __( 'E-mail de contact', 'hello-immosync' ),
					'name'  => 'wpis_cs_email',
					'type'  => 'email',
				),
				array(
					'key'          => 'field_wpis_cs_cta_url',
					'label'        => __( 'Lien du bouton', 'hello-immosync' ),
					'name'         => 'wpis_cs_cta_url',
					'type'         => 'url',
					'instructions' => __( 'Le bouton est masqué si ce champ est vide.', 'hello-immosync' ),
				),
				array(
					'key'         => 'field_wpis_cs_cta_label',
					'label'       => __( 'Libellé du bouton', 'hello-immosync' ),
					'name'        => 'wpis_cs_cta_label',
					'type'        => 'text',
					'placeholder' => __( 'Découvrir nos biens', 'hello-immosync' ),
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'wpis-coming-soon',
					),
				),
			),
		)
	);
}
