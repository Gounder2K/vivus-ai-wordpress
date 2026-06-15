<?php
/**
 * Testimonials.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$quotes = array(
	array(
		'quote' => __( 'It shaved fifteen minutes off every complex consult. The notes are clean enough to drop straight into the record.', 'vivus-ai' ),
		'name'  => __( 'Dr. Amara Okafor', 'vivus-ai' ),
		'role'  => __( 'GP, Northside Clinic', 'vivus-ai' ),
	),
	array(
		'quote' => __( 'Finally an AI tool our compliance team was happy with — it runs on our own infrastructure.', 'vivus-ai' ),
		'name'  => __( 'James Whitlock', 'vivus-ai' ),
		'role'  => __( 'IT Director, Meridian Health', 'vivus-ai' ),
	),
	array(
		'quote' => __( 'The patient-aware workspaces are the killer feature. Context is always exactly where I left it.', 'vivus-ai' ),
		'name'  => __( 'Dr. Priya Nair', 'vivus-ai' ),
		'role'  => __( 'Consultant, Care Collective', 'vivus-ai' ),
	),
);
?>
<section class="vivus-section vivus-section--muted" id="testimonials">
	<div class="container">
		<div class="vivus-section__head text-center mx-auto">
			<span class="vivus-eyebrow"><?php esc_html_e( 'Testimonials', 'vivus-ai' ); ?></span>
			<h2 class="vivus-section__title"><?php esc_html_e( 'Clinicians who got their time back', 'vivus-ai' ); ?></h2>
		</div>

		<div class="row g-4 mt-2">
			<?php foreach ( $quotes as $q ) : ?>
				<div class="col-md-4">
					<figure class="vivus-quote h-100">
						<i class="bi bi-quote vivus-quote__mark" aria-hidden="true"></i>
						<blockquote class="vivus-quote__text"><?php echo esc_html( $q['quote'] ); ?></blockquote>
						<figcaption class="vivus-quote__cite">
							<span class="vivus-quote__name"><?php echo esc_html( $q['name'] ); ?></span>
							<span class="vivus-quote__role"><?php echo esc_html( $q['role'] ); ?></span>
						</figcaption>
					</figure>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
