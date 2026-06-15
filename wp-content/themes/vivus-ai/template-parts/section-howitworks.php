<?php
/**
 * "How it works" steps.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$steps = vivus_ai_steps();
?>
<section class="vivus-section vivus-section--muted" id="how-it-works">
	<div class="container">
		<div class="vivus-section__head text-center mx-auto">
			<span class="vivus-eyebrow"><?php esc_html_e( 'How it works', 'vivus-ai' ); ?></span>
			<h2 class="vivus-section__title"><?php esc_html_e( 'From question to patient-ready note in three steps', 'vivus-ai' ); ?></h2>
		</div>

		<div class="row g-4 mt-2">
			<?php foreach ( $steps as $step ) : ?>
				<div class="col-md-4">
					<div class="vivus-step h-100">
						<span class="vivus-step__num"><?php echo esc_html( $step['num'] ); ?></span>
						<h3 class="vivus-step__title"><?php echo esc_html( $step['title'] ); ?></h3>
						<p class="vivus-step__text"><?php echo esc_html( $step['text'] ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
