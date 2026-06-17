<?php
/**
 * Page d'accueil premium.
 *
 * @package HelloImmoSync
 */

defined( 'ABSPATH' ) || exit;

get_header();

get_template_part( 'template-parts/global/page-hero', null, array( 'variant' => 'home' ) );
get_template_part( 'template-parts/home/featured' );
get_template_part( 'template-parts/home/find-your-place' );
get_template_part( 'template-parts/home/lifestyle' );
get_template_part( 'template-parts/home/offmarket' );
get_template_part( 'template-parts/home/about' );
get_template_part( 'template-parts/home/cta-seller' );

get_footer();
