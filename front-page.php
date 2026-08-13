<?php
/**
 * Page d'accueil premium.
 *
 * L'ordre et la visibilité des blocs sont réglables depuis « Réglages du thème →
 * Page d'accueil » : voir inc/home-sections.php pour le registre.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

get_header();

wpis_render_home_sections();

get_footer();
