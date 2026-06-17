<?php
/**
 * hello-immosync — thème parent immobilier ImmoSync.
 *
 * Point d'entrée : charge la configuration, les assets et les helpers WPIS.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

define( 'HELLO_IMMOSYNC_VERSION', '0.1.0' );
define( 'HELLO_IMMOSYNC_DIR', get_template_directory() );
define( 'HELLO_IMMOSYNC_URI', get_template_directory_uri() );

/**
 * Fichiers d'amorçage du thème.
 * L'ordre est important : setup et enqueue d'abord, puis les helpers réutilisés
 * partout dans les templates.
 */
$wpis_includes = array(
	'/inc/setup.php',           // Supports, menus, tailles d'images.
	'/inc/enqueue.php',         // CSS (Tailwind compilé), polices, JS.
	'/inc/helpers.php',         // Helpers de formatage génériques.
	'/inc/immosync-fields.php', // Accesseurs de champs WPIS.
	'/inc/epc.php',             // PEB/EPC : visuels officiels par région (Wallonie par défaut).
	'/inc/template-tags.php',   // Helpers de rendu (cartes, badges, formulaire de recherche).
	'/inc/search.php',          // Recherche/filtres server-side (WP_Query).
	'/inc/structured-data.php', // Données structurées JSON-LD (SEO).
	'/inc/acf-content.php',     // Contenu éditable : helpers ACF, page d'options, champs.
	'/inc/estate-sections.php', // Fiche bien modulable : registre + ordre des sections.
);

foreach ( $wpis_includes as $wpis_file ) {
	$path = HELLO_IMMOSYNC_DIR . $wpis_file;
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}
