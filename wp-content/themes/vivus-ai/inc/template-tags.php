<?php
/**
 * Reusable template helpers for the Vivus AI theme.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'vivus_ai_logo' ) ) :
	/**
	 * Render the Vivus AI wordmark.
	 *
	 * Uses a custom logo if one is set in the Customizer, otherwise falls back
	 * to the bundled brand mark + text wordmark ("Vivus AI" with AI in green).
	 *
	 * @param array $args Optional. height (px) and whether to link home.
	 */
	function vivus_ai_logo( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'height' => 34,
				'link'   => true,
				'class'  => '',
			)
		);

		$home  = esc_url( home_url( '/' ) );
		$name  = get_bloginfo( 'name' );
		$class = 'vivus-logo d-inline-flex align-items-center gap-2 ' . sanitize_html_class( $args['class'] );

		echo '<a href="' . $home . '" class="' . esc_attr( $class ) . '" rel="home" aria-label="' . esc_attr( $name ) . '">';

		if ( has_custom_logo() ) {
			$logo_id  = get_theme_mod( 'custom_logo' );
			$logo_src = wp_get_attachment_image_src( $logo_id, 'full' );
			if ( $logo_src ) {
				printf(
					'<img src="%1$s" alt="%2$s" height="%3$d" class="vivus-logo__img" />',
					esc_url( $logo_src[0] ),
					esc_attr( $name ),
					absint( $args['height'] )
				);
			}
		} else {
			printf(
				'<img src="%1$s" alt="%2$s" height="%3$d" width="%3$d" class="vivus-logo__img rounded-3" />',
				esc_url( VIVUS_AI_URI . '/assets/img/vivus-logo.png' ),
				esc_attr( $name ),
				absint( $args['height'] )
			);
			echo '<span class="vivus-logo__text">Vivus <span class="vivus-logo__accent">AI</span></span>';
		}

		echo '</a>';
	}
endif;

if ( ! function_exists( 'vivus_ai_primary_menu' ) ) :
	/**
	 * Output the primary navigation, falling back to a sensible default
	 * when no menu has been assigned yet (useful on a fresh install).
	 */
	function vivus_ai_primary_menu() {
		if ( has_nav_menu( 'primary' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1',
					'depth'          => 2,
					'fallback_cb'    => 'vivus_ai_default_menu',
					'walker'         => new Vivus_AI_Nav_Walker(),
				)
			);
		} else {
			vivus_ai_default_menu();
		}
	}
endif;

if ( ! function_exists( 'vivus_ai_default_menu' ) ) :
	/**
	 * Hard-coded fallback menu so the header never looks empty.
	 */
	function vivus_ai_default_menu() {
		$items = array(
			'#features'      => __( 'Features', 'vivus-ai' ),
			'#how-it-works'  => __( 'How it works', 'vivus-ai' ),
			'#demo'          => __( 'Demo', 'vivus-ai' ),
			'#pricing'       => __( 'Pricing', 'vivus-ai' ),
			'/about/'        => __( 'About', 'vivus-ai' ),
			'/contact/'      => __( 'Contact', 'vivus-ai' ),
		);
		echo '<ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center gap-lg-1">';
		foreach ( $items as $href => $label ) {
			$url = ( '#' === substr( $href, 0, 1 ) ) ? home_url( '/' ) . $href : home_url( $href );
			printf(
				'<li class="nav-item"><a class="nav-link" href="%s">%s</a></li>',
				esc_url( $url ),
				esc_html( $label )
			);
		}
		echo '</ul>';
	}
endif;

if ( ! class_exists( 'Vivus_AI_Nav_Walker' ) ) :
	/**
	 * Minimal Bootstrap 5 nav walker so admin-defined menus get the right
	 * .nav-item / .nav-link classes without a heavyweight dependency.
	 */
	class Vivus_AI_Nav_Walker extends Walker_Nav_Menu {

		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
			$is_active = in_array( 'current-menu-item', $classes, true );

			$li_class   = 'nav-item';
			$link_class = 'nav-link' . ( $is_active ? ' active' : '' );

			$atts  = ' class="' . esc_attr( $link_class ) . '"';
			$atts .= $is_active ? ' aria-current="page"' : '';
			$atts .= ' href="' . esc_url( $item->url ) . '"';

			$output .= '<li class="' . esc_attr( $li_class ) . '">';
			$output .= '<a' . $atts . '>' . esc_html( $item->title ) . '</a>';
		}

		public function end_el( &$output, $item, $depth = 0, $args = null ) {
			$output .= '</li>';
		}
	}
endif;

if ( ! function_exists( 'vivus_ai_features' ) ) :
	/**
	 * Marketing feature cards. Centralised so they can be reused/edited once.
	 *
	 * @return array<int, array{icon:string,title:string,text:string}>
	 */
	function vivus_ai_features() {
		return array(
			array(
				'icon'  => 'bi-chat-square-text',
				'title' => __( 'Clinical chat assistant', 'vivus-ai' ),
				'text'  => __( 'Ask questions in plain language and get structured, guideline-aware answers your clinicians can act on.', 'vivus-ai' ),
			),
			array(
				'icon'  => 'bi-file-earmark-medical',
				'title' => __( 'Documents that write themselves', 'vivus-ai' ),
				'text'  => __( 'Turn a consultation into a clean, exportable note or report in seconds — no copy-paste gymnastics.', 'vivus-ai' ),
			),
			array(
				'icon'  => 'bi-people',
				'title' => __( 'Patient-aware context', 'vivus-ai' ),
				'text'  => __( 'Organise conversations by patient so the assistant always has the right context at hand.', 'vivus-ai' ),
			),
			array(
				'icon'  => 'bi-shield-lock',
				'title' => __( 'Private by design', 'vivus-ai' ),
				'text'  => __( 'Runs against your own models. Your data stays in your control — not in a third-party training set.', 'vivus-ai' ),
			),
			array(
				'icon'  => 'bi-lightning-charge',
				'title' => __( 'Built for the workflow', 'vivus-ai' ),
				'text'  => __( 'Templates, folders and saved consultations keep busy teams moving instead of fighting the tool.', 'vivus-ai' ),
			),
			array(
				'icon'  => 'bi-graph-up-arrow',
				'title' => __( 'Insight for admins', 'vivus-ai' ),
				'text'  => __( 'Usage dashboards show how the assistant is adopted across your practice, with an audit trail.', 'vivus-ai' ),
			),
		);
	}
endif;

if ( ! function_exists( 'vivus_ai_steps' ) ) :
	/**
	 * "How it works" steps.
	 */
	function vivus_ai_steps() {
		return array(
			array(
				'num'   => '01',
				'title' => __( 'Ask', 'vivus-ai' ),
				'text'  => __( 'Type a clinical question or paste your consultation notes into the assistant.', 'vivus-ai' ),
			),
			array(
				'num'   => '02',
				'title' => __( 'Review', 'vivus-ai' ),
				'text'  => __( 'Vivus AI returns a structured, source-grounded response you can read and refine.', 'vivus-ai' ),
			),
			array(
				'num'   => '03',
				'title' => __( 'Export', 'vivus-ai' ),
				'text'  => __( 'Save it to the patient record or export a polished report — ready to share.', 'vivus-ai' ),
			),
		);
	}
endif;

if ( ! function_exists( 'vivus_ai_plans' ) ) :
	/**
	 * Pricing plans.
	 */
	function vivus_ai_plans() {
		return array(
			array(
				'name'     => __( 'Clinician', 'vivus-ai' ),
				'price'    => '$0',
				'period'   => __( 'free while in beta', 'vivus-ai' ),
				'features' => array(
					__( 'Unlimited clinical chat', 'vivus-ai' ),
					__( 'Document export (PDF & DOCX)', 'vivus-ai' ),
					__( 'Up to 50 saved consultations', 'vivus-ai' ),
				),
				'cta'      => __( 'Start free', 'vivus-ai' ),
				'featured' => false,
			),
			array(
				'name'     => __( 'Practice', 'vivus-ai' ),
				'price'    => '$29',
				'period'   => __( 'per seat / month', 'vivus-ai' ),
				'features' => array(
					__( 'Everything in Clinician', 'vivus-ai' ),
					__( 'Patient-aware workspaces', 'vivus-ai' ),
					__( 'Shared templates & folders', 'vivus-ai' ),
					__( 'Priority support', 'vivus-ai' ),
				),
				'cta'      => __( 'Book a demo', 'vivus-ai' ),
				'featured' => true,
			),
			array(
				'name'     => __( 'Enterprise', 'vivus-ai' ),
				'price'    => __( 'Custom', 'vivus-ai' ),
				'period'   => __( 'self-hosted models', 'vivus-ai' ),
				'features' => array(
					__( 'Everything in Practice', 'vivus-ai' ),
					__( 'Self-hosted / on-prem models', 'vivus-ai' ),
					__( 'SSO & admin controls', 'vivus-ai' ),
					__( 'Audit logs & analytics', 'vivus-ai' ),
				),
				'cta'      => __( 'Talk to sales', 'vivus-ai' ),
				'featured' => false,
			),
		);
	}
endif;
