<?php
/**
 * Plugin Name:       Vivus Leads
 * Plugin URI:        https://github.com/prateek/vivus-ai-wordpress
 * Description:       Custom lead / demo-request capture for the Vivus AI site. Stores submissions in a dedicated MySQL table, exposes a secure REST endpoint, a [vivus_contact_form] shortcode, and an admin dashboard with CSV export.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Prateek Gounder
 * License:           GPL-2.0-or-later
 * Text Domain:       vivus-leads
 *
 * @package Vivus_Leads
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'VIVUS_LEADS_VERSION', '1.0.0' );
define( 'VIVUS_LEADS_DB_VERSION', '1.0.0' );
define( 'VIVUS_LEADS_FILE', __FILE__ );
define( 'VIVUS_LEADS_DIR', plugin_dir_path( __FILE__ ) );
define( 'VIVUS_LEADS_URL', plugin_dir_url( __FILE__ ) );
define( 'VIVUS_LEADS_TABLE', 'vivus_leads' ); // Prefixed at runtime via $wpdb->prefix.

require_once VIVUS_LEADS_DIR . 'includes/class-vivus-leads-activator.php';
require_once VIVUS_LEADS_DIR . 'includes/class-vivus-leads-db.php';
require_once VIVUS_LEADS_DIR . 'includes/class-vivus-leads-rest.php';
require_once VIVUS_LEADS_DIR . 'includes/class-vivus-leads-shortcode.php';
require_once VIVUS_LEADS_DIR . 'includes/class-vivus-leads-admin.php';

/**
 * Activation: create/upgrade the database table.
 */
function vivus_leads_activate() {
	Vivus_Leads_Activator::activate();
}
register_activation_hook( __FILE__, 'vivus_leads_activate' );

/**
 * Boot the plugin once all plugins are loaded.
 */
function vivus_leads_bootstrap() {
	// Run a lightweight DB upgrade check on load (handles updates without re-activation).
	Vivus_Leads_Activator::maybe_upgrade();

	( new Vivus_Leads_Rest() )->register();
	( new Vivus_Leads_Shortcode() )->register();

	if ( is_admin() ) {
		( new Vivus_Leads_Admin() )->register();
	}
}
add_action( 'plugins_loaded', 'vivus_leads_bootstrap' );
