<?php
/**
 * Pricing plans.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$plans = vivus_ai_plans();
?>
<section class="vivus-section" id="pricing">
	<div class="container">
		<div class="vivus-section__head text-center mx-auto">
			<span class="vivus-eyebrow"><?php esc_html_e( 'Pricing', 'vivus-ai' ); ?></span>
			<h2 class="vivus-section__title"><?php esc_html_e( 'Simple pricing that scales with your team', 'vivus-ai' ); ?></h2>
		</div>

		<div class="row g-4 mt-2 justify-content-center">
			<?php foreach ( $plans as $plan ) : ?>
				<div class="col-md-6 col-lg-4">
					<div class="vivus-plan h-100 <?php echo $plan['featured'] ? 'vivus-plan--featured' : ''; ?>">
						<?php if ( $plan['featured'] ) : ?>
							<span class="vivus-plan__badge"><?php esc_html_e( 'Most popular', 'vivus-ai' ); ?></span>
						<?php endif; ?>
						<h3 class="vivus-plan__name"><?php echo esc_html( $plan['name'] ); ?></h3>
						<div class="vivus-plan__price">
							<span class="vivus-plan__amount"><?php echo esc_html( $plan['price'] ); ?></span>
							<span class="vivus-plan__period"><?php echo esc_html( $plan['period'] ); ?></span>
						</div>
						<ul class="vivus-plan__features list-unstyled">
							<?php foreach ( $plan['features'] as $feature ) : ?>
								<li><i class="bi bi-check-lg" aria-hidden="true"></i><?php echo esc_html( $feature ); ?></li>
							<?php endforeach; ?>
						</ul>
						<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
							class="btn <?php echo $plan['featured'] ? 'btn-dark vivus-btn' : 'btn-outline-dark vivus-btn-ghost'; ?> w-100">
							<?php echo esc_html( $plan['cta'] ); ?>
						</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
