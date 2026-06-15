<?php
/**
 * Fallback template (blog / archive listing).
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="container vivus-page__body">
	<div class="row justify-content-center">
		<div class="col-lg-8">
			<?php if ( have_posts() ) : ?>
				<header class="mb-4">
					<h1 class="vivus-page__title"><?php echo esc_html( get_the_archive_title() ? wp_strip_all_tags( get_the_archive_title() ) : __( 'Latest posts', 'vivus-ai' ) ); ?></h1>
				</header>

				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'vivus-post mb-5' ); ?>>
						<h2 class="h4"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="text-muted small"><?php echo esc_html( get_the_date() ); ?></p>
						<div class="vivus-post__excerpt"><?php the_excerpt(); ?></div>
						<a class="vivus-readmore" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'vivus-ai' ); ?> &rarr;</a>
					</article>
					<?php
				endwhile;

				the_posts_pagination(
					array(
						'mid_size'  => 1,
						'prev_text' => __( '&larr; Newer', 'vivus-ai' ),
						'next_text' => __( 'Older &rarr;', 'vivus-ai' ),
					)
				);
			else :
				?>
				<p><?php esc_html_e( 'Nothing here yet.', 'vivus-ai' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php
get_footer();
