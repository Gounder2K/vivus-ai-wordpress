<?php
/**
 * Uninstall handler — runs when the plugin is deleted from the admin.
 *
 * Drops the custom table and removes plugin options so uninstalling leaves
 * the database clean.
 *
 * @package Vivus_Leads
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$table = $wpdb->prefix . 'vivus_leads';
// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- internal table name, no user input.
$wpdb->query( "DROP TABLE IF EXISTS {$table}" );

delete_option( 'vivus_leads_db_version' );
