<?php
/**
 * Closing call-to-action band.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="vivus-cta">
	<div class="container">
		<div class="vivus-cta__inner text-center">
			<h2 class="vivus-cta__title"><?php esc_html_e( 'Give your clinicians their time back', 'vivus-ai' ); ?></h2>
			<p class="vivus-cta__lead"><?php esc_html_e( 'Join the practices using Vivus AI to turn questions into patient-ready answers — fast.', 'vivus-ai' ); ?></p>
			<div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-light btn-lg vivus-btn-light">
					<?php esc_html_e( 'Book a demo', 'vivus-ai' ); ?>
				</a>
				<a href="#demo" class="btn btn-outline-light btn-lg">
					<?php esc_html_e( 'Try it now', 'vivus-ai' ); ?>
				</a>
			</div>
		</div>
	</div>
</section>
