<?php
/**
 * Trust bar / "trusted by" strip.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$logos = array( 'Northside Clinic', 'Meridian Health', 'Care Collective', 'St. Lucia GP', 'Vantage Medical' );
?>
<section class="vivus-trust">
	<div class="container">
		<p class="vivus-trust__label text-center text-uppercase"><?php esc_html_e( 'Trusted by forward-thinking care teams', 'vivus-ai' ); ?></p>
		<div class="d-flex flex-wrap justify-content-center align-items-center gap-4 gap-md-5">
			<?php foreach ( $logos as $logo ) : ?>
				<span class="vivus-trust__logo"><?php echo esc_html( $logo ); ?></span>
			<?php endforeach; ?>
		</div>
	</div>
</section>
