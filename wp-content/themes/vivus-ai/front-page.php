<?php
/**
 * The front page (marketing home).
 *
 * Composed from focused template parts so each section stays small and
 * the next developer can rearrange the page without scrolling a monolith.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

get_template_part( 'template-parts/section', 'hero' );
get_template_part( 'template-parts/section', 'logos' );
get_template_part( 'template-parts/section', 'features' );
get_template_part( 'template-parts/section', 'howitworks' );
get_template_part( 'template-parts/section', 'demo' );
get_template_part( 'template-parts/section', 'testimonials' );
get_template_part( 'template-parts/section', 'pricing' );
get_template_part( 'template-parts/section', 'cta' );

get_footer();
