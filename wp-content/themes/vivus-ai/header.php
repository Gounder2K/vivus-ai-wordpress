<?php
/**
 * Site header and opening markup.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="icon" href="<?php echo esc_url( VIVUS_AI_URI . '/assets/img/favicon.svg' ); ?>" type="image/svg+xml" />
	<link rel="apple-touch-icon" href="<?php echo esc_url( VIVUS_AI_URI . '/assets/img/apple-icon.png' ); ?>" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="visually-hidden-focusable skip-link" href="#main-content"><?php esc_html_e( 'Skip to content', 'vivus-ai' ); ?></a>

<header class="site-header" id="site-header">
	<nav class="navbar navbar-expand-lg fixed-top vivus-navbar" aria-label="<?php esc_attr_e( 'Primary navigation', 'vivus-ai' ); ?>">
		<div class="container">
			<?php vivus_ai_logo( array( 'class' => 'navbar-brand p-0 m-0' ) ); ?>

			<button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#vivusNav"
				aria-controls="vivusNav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'vivus-ai' ); ?>">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="vivusNav">
				<?php vivus_ai_primary_menu(); ?>

				<div class="d-flex align-items-center gap-2 ms-lg-3 mt-3 mt-lg-0">
					<a class="btn btn-outline-dark btn-sm vivus-btn-ghost" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">
						<?php esc_html_e( 'Sign in', 'vivus-ai' ); ?>
					</a>
					<a class="btn btn-dark btn-sm vivus-btn" href="#demo">
						<?php esc_html_e( 'Try the demo', 'vivus-ai' ); ?>
					</a>
				</div>
			</div>
		</div>
	</nav>
</header>

<main id="main-content" class="site-main">
