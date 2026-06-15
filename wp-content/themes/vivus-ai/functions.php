<?php
/**
 * Vivus AI theme bootstrap.
 *
 * Sets up theme supports, registers navigation menus, enqueues front-end
 * assets (Bootstrap 5.3 + jQuery + theme code) and wires up small helpers.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

if ( ! defined( 'VIVUS_AI_VERSION' ) ) {
	define( 'VIVUS_AI_VERSION', '1.0.0' );
}
if ( ! defined( 'VIVUS_AI_DIR' ) ) {
	define( 'VIVUS_AI_DIR', get_template_directory() );
}
if ( ! defined( 'VIVUS_AI_URI' ) ) {
	define( 'VIVUS_AI_URI', get_template_directory_uri() );
}

/**
 * Core theme supports and menu registration.
 */
function vivus_ai_setup() {
	load_theme_textdomain( 'vivus-ai', VIVUS_AI_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 48,
			'width'       => 48,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'vivus-ai' ),
			'footer'  => __( 'Footer Menu', 'vivus-ai' ),
		)
	);
}
add_action( 'after_setup_theme', 'vivus_ai_setup' );

/**
 * Set the content width in pixels, based on the theme's design.
 */
function vivus_ai_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'vivus_ai_content_width', 1140 );
}
add_action( 'after_setup_theme', 'vivus_ai_content_width', 0 );

/**
 * Enqueue styles and scripts.
 *
 * Bootstrap 5.3 and jQuery are required by the brief. jQuery ships with
 * WordPress core, so we declare it as a dependency rather than re-bundling it.
 */
function vivus_ai_assets() {
	// --- Styles -------------------------------------------------------------
	wp_enqueue_style(
		'bootstrap',
		'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
		array(),
		'5.3.3'
	);

	wp_enqueue_style(
		'bootstrap-icons',
		'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
		array(),
		'1.11.3'
	);

	wp_enqueue_style(
		'vivus-ai-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
		array(),
		VIVUS_AI_VERSION
	);

	wp_enqueue_style(
		'vivus-ai',
		VIVUS_AI_URI . '/assets/css/theme.css',
		array( 'bootstrap' ),
		VIVUS_AI_VERSION
	);

	// The required style.css (theme header) — kept last so it can override.
	wp_enqueue_style( 'vivus-ai-base', get_stylesheet_uri(), array( 'vivus-ai' ), VIVUS_AI_VERSION );

	// --- Scripts ------------------------------------------------------------
	wp_enqueue_script(
		'bootstrap',
		'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
		array(),
		'5.3.3',
		true
	);

	// Main theme behaviour — depends on jQuery (bundled with WordPress).
	wp_enqueue_script(
		'vivus-ai',
		VIVUS_AI_URI . '/assets/js/theme.js',
		array( 'jquery', 'bootstrap' ),
		VIVUS_AI_VERSION,
		true
	);

	// Mocked clinical chat demo (jQuery driven, scripted responses).
	wp_enqueue_script(
		'vivus-ai-chat-demo',
		VIVUS_AI_URI . '/assets/js/chat-demo.js',
		array( 'jquery' ),
		VIVUS_AI_VERSION,
		true
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'vivus_ai_assets' );

/**
 * Add async/defer-friendly attributes are intentionally avoided to keep
 * Bootstrap's bundle order deterministic. Instead we add a small preconnect
 * for the font + CDN hosts to improve load performance.
 */
function vivus_ai_resource_hints( $hints, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$hints[] = array(
			'href'        => 'https://cdn.jsdelivr.net',
			'crossorigin',
		);
		$hints[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $hints;
}
add_filter( 'wp_resource_hints', 'vivus_ai_resource_hints', 10, 2 );

/**
 * Register the marketing widget areas (footer columns).
 */
function vivus_ai_widgets_init() {
	for ( $i = 1; $i <= 3; $i++ ) {
		register_sidebar(
			array(
				'name'          => sprintf( __( 'Footer Column %d', 'vivus-ai' ), $i ),
				'id'            => 'footer-' . $i,
				'description'   => __( 'Appears in the site footer.', 'vivus-ai' ),
				'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h6 class="footer-widget__title text-uppercase">',
				'after_title'   => '</h6>',
			)
		);
	}
}
add_action( 'widgets_init', 'vivus_ai_widgets_init' );

/**
 * Lightweight SEO: output a sane meta description on the front page and
 * singular content when Yoast/RankMath are not present.
 */
function vivus_ai_meta_description() {
	// Defer to dedicated SEO plugins if active.
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) ) {
		return;
	}

	$description = get_bloginfo( 'description', 'display' );

	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post && ! empty( $post->post_excerpt ) ) {
			$description = $post->post_excerpt;
		} elseif ( $post && ! empty( $post->post_content ) ) {
			$description = wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '…' );
		}
	}

	$description = trim( $description );
	if ( '' === $description ) {
		return;
	}

	printf(
		'<meta name="description" content="%s" />' . "\n",
		esc_attr( wp_strip_all_tags( $description ) )
	);
	printf(
		'<meta property="og:description" content="%s" />' . "\n",
		esc_attr( wp_strip_all_tags( $description ) )
	);
	printf(
		'<meta property="og:title" content="%s" />' . "\n",
		esc_attr( wp_get_document_title() )
	);
	echo '<meta property="og:type" content="website" />' . "\n";
}
add_action( 'wp_head', 'vivus_ai_meta_description', 1 );

/**
 * Body classes used by the stylesheet for page-specific layout tweaks.
 */
function vivus_ai_body_classes( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'vivus-front';
	}
	return $classes;
}
add_filter( 'body_class', 'vivus_ai_body_classes' );

// Template helpers (logo, menus, section data).
require_once VIVUS_AI_DIR . '/inc/template-tags.php';
require_once VIVUS_AI_DIR . '/inc/customizer.php';
