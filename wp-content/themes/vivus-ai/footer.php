<?php
/**
 * Site footer and closing markup.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vivus_email = vivus_ai_opt( 'vivus_contact_email', 'hello@vivus.ai' );
?>
</main><!-- #main-content -->

<footer class="site-footer">
	<div class="container">
		<div class="row gy-4">
			<div class="col-lg-4">
				<?php vivus_ai_logo( array( 'class' => 'mb-3', 'height' => 32 ) ); ?>
				<p class="footer-tagline">
					<?php esc_html_e( 'AI assistant for clinical guidance and medical workflow support.', 'vivus-ai' ); ?>
				</p>
				<a class="footer-email" href="mailto:<?php echo esc_attr( $vivus_email ); ?>">
					<i class="bi bi-envelope me-1" aria-hidden="true"></i><?php echo esc_html( $vivus_email ); ?>
				</a>
			</div>

			<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
				<?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
					<div class="col-6 col-lg-2 offset-lg-<?php echo 1 === $i ? '1' : '0'; ?>">
						<?php dynamic_sidebar( 'footer-' . $i ); ?>
					</div>
				<?php endif; ?>
			<?php endfor; ?>

			<?php if ( ! is_active_sidebar( 'footer-1' ) ) : ?>
				<!-- Default footer columns shown until widgets are configured. -->
				<div class="col-6 col-lg-2 offset-lg-1">
					<h6 class="footer-widget__title text-uppercase"><?php esc_html_e( 'Product', 'vivus-ai' ); ?></h6>
					<ul class="footer-menu list-unstyled">
						<li><a href="<?php echo esc_url( home_url( '/#features' ) ); ?>"><?php esc_html_e( 'Features', 'vivus-ai' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/#pricing' ) ); ?>"><?php esc_html_e( 'Pricing', 'vivus-ai' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/#demo' ) ); ?>"><?php esc_html_e( 'Demo', 'vivus-ai' ); ?></a></li>
					</ul>
				</div>
				<div class="col-6 col-lg-2">
					<h6 class="footer-widget__title text-uppercase"><?php esc_html_e( 'Company', 'vivus-ai' ); ?></h6>
					<ul class="footer-menu list-unstyled">
						<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'vivus-ai' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'vivus-ai' ); ?></a></li>
					</ul>
				</div>
				<div class="col-6 col-lg-2">
					<h6 class="footer-widget__title text-uppercase"><?php esc_html_e( 'Legal', 'vivus-ai' ); ?></h6>
					<ul class="footer-menu list-unstyled">
						<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Privacy', 'vivus-ai' ); ?></a></li>
						<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Terms', 'vivus-ai' ); ?></a></li>
					</ul>
				</div>
			<?php endif; ?>
		</div>

		<div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-5 pt-4">
			<p class="mb-2 mb-md-0">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'vivus-ai' ); ?>
			</p>
			<p class="footer-disclaimer mb-0">
				<?php esc_html_e( 'For clinical decision support only — not a substitute for professional judgement.', 'vivus-ai' ); ?>
			</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
