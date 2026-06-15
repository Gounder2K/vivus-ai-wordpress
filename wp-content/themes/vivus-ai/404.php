<?php
/**
 * 404 template.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="vivus-section text-center">
	<div class="container">
		<span class="vivus-eyebrow"><?php esc_html_e( 'Error 404', 'vivus-ai' ); ?></span>
		<h1 class="vivus-section__title"><?php esc_html_e( 'We couldn’t find that page', 'vivus-ai' ); ?></h1>
		<p class="vivus-section__lead mx-auto"><?php esc_html_e( 'The page may have moved. Let’s get you back on track.', 'vivus-ai' ); ?></p>
		<a class="btn btn-dark btn-lg vivus-btn mt-3" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php esc_html_e( 'Back to home', 'vivus-ai' ); ?>
		</a>
	</div>
</section>
<?php
get_footer();
