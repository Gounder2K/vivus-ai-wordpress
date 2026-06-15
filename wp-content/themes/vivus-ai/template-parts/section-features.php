<?php
/**
 * Features grid.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$features = vivus_ai_features();
?>
<section class="vivus-section" id="features">
	<div class="container">
		<div class="vivus-section__head text-center mx-auto">
			<span class="vivus-eyebrow"><?php esc_html_e( 'Features', 'vivus-ai' ); ?></span>
			<h2 class="vivus-section__title"><?php esc_html_e( 'Everything a clinical team needs, nothing it doesn’t', 'vivus-ai' ); ?></h2>
			<p class="vivus-section__lead"><?php esc_html_e( 'Vivus AI focuses on the moments that actually save time in a busy practice.', 'vivus-ai' ); ?></p>
		</div>

		<div class="row g-4 mt-2">
			<?php foreach ( $features as $feature ) : ?>
				<div class="col-md-6 col-lg-4">
					<article class="vivus-card h-100">
						<span class="vivus-card__icon">
							<i class="bi <?php echo esc_attr( $feature['icon'] ); ?>" aria-hidden="true"></i>
						</span>
						<h3 class="vivus-card__title"><?php echo esc_html( $feature['title'] ); ?></h3>
						<p class="vivus-card__text"><?php echo esc_html( $feature['text'] ); ?></p>
					</article>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
