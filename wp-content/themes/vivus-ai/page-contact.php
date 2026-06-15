<?php
/**
 * Contact page (slug: contact).
 *
 * Renders the lead-capture form provided by the Vivus Leads plugin via the
 * [vivus_contact_form] shortcode, alongside contact details.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$email = vivus_ai_opt( 'vivus_contact_email', 'hello@vivus.ai' );
?>
<article class="vivus-page">
	<header class="vivus-page__hero">
		<div class="container">
			<span class="vivus-eyebrow"><?php esc_html_e( 'Contact', 'vivus-ai' ); ?></span>
			<h1 class="vivus-page__title"><?php the_title(); ?></h1>
			<p class="vivus-page__lead"><?php esc_html_e( 'Tell us about your team and we’ll set up a tailored walkthrough.', 'vivus-ai' ); ?></p>
		</div>
	</header>

	<div class="container vivus-page__body">
		<div class="row g-5">
			<div class="col-lg-7">
				<?php
				// Page body content (optional) followed by the form shortcode.
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;

				if ( shortcode_exists( 'vivus_contact_form' ) ) {
					echo do_shortcode( '[vivus_contact_form]' );
				} else {
					echo '<div class="alert alert-warning">' . esc_html__( 'The Vivus Leads plugin is not active, so the contact form is unavailable.', 'vivus-ai' ) . '</div>';
				}
				?>
			</div>

			<div class="col-lg-5">
				<aside class="vivus-contact-aside">
					<h2 class="h5"><?php esc_html_e( 'Prefer email?', 'vivus-ai' ); ?></h2>
					<p>
						<a href="mailto:<?php echo esc_attr( $email ); ?>" class="vivus-contact-aside__link">
							<i class="bi bi-envelope me-2" aria-hidden="true"></i><?php echo esc_html( $email ); ?>
						</a>
					</p>
					<hr class="my-4" />
					<h2 class="h5"><?php esc_html_e( 'What to expect', 'vivus-ai' ); ?></h2>
					<ul class="vivus-contact-aside__list list-unstyled">
						<li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><?php esc_html_e( 'A reply within one business day', 'vivus-ai' ); ?></li>
						<li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><?php esc_html_e( 'A 30-minute tailored walkthrough', 'vivus-ai' ); ?></li>
						<li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><?php esc_html_e( 'No pressure, no spam', 'vivus-ai' ); ?></li>
					</ul>
				</aside>
			</div>
		</div>
	</div>
</article>
<?php
get_footer();
