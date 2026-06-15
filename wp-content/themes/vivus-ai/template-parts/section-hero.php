<?php
/**
 * Hero section.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow  = vivus_ai_opt( 'vivus_hero_eyebrow', __( 'For clinics, hospitals & private practice', 'vivus-ai' ) );
$title    = vivus_ai_opt( 'vivus_hero_title', __( 'The AI assistant built for clinical work.', 'vivus-ai' ) );
$subtitle = vivus_ai_opt( 'vivus_hero_subtitle', __( 'Vivus AI gives your team guideline-aware answers, patient-ready documents and a private model you control — all in one calm, fast workspace.', 'vivus-ai' ) );
?>
<section class="vivus-hero" id="top">
	<div class="vivus-hero__glow" aria-hidden="true"></div>
	<div class="container">
		<div class="row align-items-center g-5">
			<div class="col-lg-6">
				<span class="vivus-eyebrow"><i class="bi bi-stars me-1" aria-hidden="true"></i><?php echo esc_html( $eyebrow ); ?></span>
				<h1 class="vivus-hero__title"><?php echo esc_html( $title ); ?></h1>
				<p class="vivus-hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>

				<div class="d-flex flex-wrap gap-3 mt-4">
					<a href="#demo" class="btn btn-dark btn-lg vivus-btn">
						<i class="bi bi-play-circle me-2" aria-hidden="true"></i><?php esc_html_e( 'Try the live demo', 'vivus-ai' ); ?>
					</a>
					<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn-outline-dark btn-lg vivus-btn-ghost">
						<?php esc_html_e( 'Book a walkthrough', 'vivus-ai' ); ?>
					</a>
				</div>

				<ul class="vivus-hero__points list-unstyled mt-4">
					<li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><?php esc_html_e( 'Runs on your own private models', 'vivus-ai' ); ?></li>
					<li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><?php esc_html_e( 'No setup — works in the browser', 'vivus-ai' ); ?></li>
					<li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><?php esc_html_e( 'Built with clinicians, for clinicians', 'vivus-ai' ); ?></li>
				</ul>
			</div>

			<div class="col-lg-6">
				<!-- Decorative product mock built in pure HTML/CSS to mirror the real chat UI. -->
				<div class="vivus-mock" aria-hidden="true">
					<div class="vivus-mock__bar">
						<span class="vivus-mock__dot"></span>
						<span class="vivus-mock__dot"></span>
						<span class="vivus-mock__dot"></span>
						<span class="vivus-mock__title">Vivus AI — Consultation</span>
					</div>
					<div class="vivus-mock__body">
						<div class="vivus-mock__msg vivus-mock__msg--user">
							Summarise the latest guidance on managing type 2 diabetes in adults.
						</div>
						<div class="vivus-mock__msg vivus-mock__msg--ai">
							<strong>First-line:</strong> lifestyle + metformin. Consider an SGLT2 inhibitor or GLP-1 agonist where there is established cardiovascular disease…
							<span class="vivus-mock__cursor"></span>
						</div>
						<div class="vivus-mock__chips">
							<span>Export note</span><span>Save to patient</span><span>Add to template</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
