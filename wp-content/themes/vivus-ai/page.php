<?php
/**
 * Generic page template.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<article class="vivus-page">
	<header class="vivus-page__hero">
		<div class="container">
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
	</div>
</article>
<?php
get_footer();
