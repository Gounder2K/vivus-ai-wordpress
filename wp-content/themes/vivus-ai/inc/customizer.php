<?php
/**
 * Theme Customizer settings.
 *
 * Exposes the marketing copy and contact details that change most often so a
 * site editor can update them without touching code.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper to read a Customizer value with a default.
 *
 * @param string $key     Setting key.
 * @param string $default Default value.
 * @return string
 */
function vivus_ai_opt( $key, $default = '' ) {
	return get_theme_mod( $key, $default );
}

/**
 * Register Customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function vivus_ai_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'vivus_hero',
		array(
			'title'    => __( 'Vivus AI — Hero', 'vivus-ai' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'vivus_hero_eyebrow' => array(
			'label'   => __( 'Hero eyebrow', 'vivus-ai' ),
			'default' => __( 'For clinics, hospitals & private practice', 'vivus-ai' ),
			'type'    => 'text',
		),
		'vivus_hero_title'   => array(
			'label'   => __( 'Hero title', 'vivus-ai' ),
			'default' => __( 'The AI assistant built for clinical work.', 'vivus-ai' ),
			'type'    => 'text',
		),
		'vivus_hero_subtitle' => array(
			'label'   => __( 'Hero subtitle', 'vivus-ai' ),
			'default' => __( 'Vivus AI gives your team guideline-aware answers, patient-ready documents and a private model you control — all in one calm, fast workspace.', 'vivus-ai' ),
			'type'    => 'textarea',
		),
		'vivus_contact_email' => array(
			'label'   => __( 'Contact email', 'vivus-ai' ),
			'default' => 'hello@vivus.ai',
			'type'    => 'text',
		),
	);

	foreach ( $fields as $id => $args ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => ( 'textarea' === $args['type'] ) ? 'sanitize_textarea_field' : 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $args['label'],
				'section' => 'vivus_hero',
				'type'    => $args['type'],
			)
		);
	}
}
add_action( 'customize_register', 'vivus_ai_customize_register' );
