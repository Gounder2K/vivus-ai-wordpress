<?php
/**
 * About page (slug: about).
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$values = array(
	array(
		'icon'  => 'bi-shield-check',
		'title' => __( 'Safety first', 'vivus-ai' ),
		'text'  => __( 'Clinical decision support should be transparent, grounded and easy to verify. We build for trust, not hype.', 'vivus-ai' ),
	),
	array(
		'icon'  => 'bi-lock',
		'title' => __( 'Privacy by default', 'vivus-ai' ),
		'text'  => __( 'Your data is yours. Vivus AI is designed to run against models you control, on infrastructure you trust.', 'vivus-ai' ),
	),
	array(
		'icon'  => 'bi-heart-pulse',
		'title' => __( 'Built with clinicians', 'vivus-ai' ),
		'text'  => __( 'Every feature starts with a real workflow problem from the people who use it every day.', 'vivus-ai' ),
	),
);
?>
<article class="vivus-page">
	<header class="vivus-page__hero">
		<div class="container">
			<span class="vivus-eyebrow"><?php esc_html_e( 'About', 'vivus-ai' ); ?></span>
			<h1 class="vivus-page__title"><?php the_title(); ?></h1>
		</div>
	</header>

	<div class="container vivus-page__body">
		<div class="row justify-content-center">
			<div class="col-lg-8">
				<?php
				while ( have_posts() ) :
					the_post();
					the_content();
				endwhile;
				?>
			</div>
		</div>

		<div class="row g-4 mt-4">
			<?php foreach ( $values as $value ) : ?>
				<div class="col-md-4">
					<article class="vivus-card h-100">
						<span class="vivus-card__icon">
							<i class="bi <?php echo esc_attr( $value['icon'] ); ?>" aria-hidden="true"></i>
						</span>
						<h3 class="vivus-card__title"><?php echo esc_html( $value['title'] ); ?></h3>
						<p class="vivus-card__text"><?php echo esc_html( $value['text'] ); ?></p>
					</article>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</article>
<?php
get_footer();
