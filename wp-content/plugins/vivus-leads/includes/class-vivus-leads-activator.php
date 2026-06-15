<?php
/**
 * Database schema management for Vivus Leads.
 *
 * @package Vivus_Leads
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates and upgrades the custom leads table.
 */
class Vivus_Leads_Activator {

	/**
	 * Fully-qualified (prefixed) table name.
	 *
	 * @return string
	 */
	public static function table_name() {
		global $wpdb;
		return $wpdb->prefix . VIVUS_LEADS_TABLE;
	}

	/**
	 * Create the table on activation.
	 *
	 * Schema notes:
	 *  - InnoDB + utf8mb4 for full Unicode (names, accents, emoji).
	 *  - Indexes on email, status and created_at because those are the
	 *    columns the admin list filters/sorts by — keeping queries fast
	 *    as the table grows.
	 */
	public static function activate() {
		global $wpdb;

		$table           = self::table_name();
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			name VARCHAR(150) NOT NULL,
			email VARCHAR(190) NOT NULL,
			organisation VARCHAR(190) NOT NULL DEFAULT '',
			team_size VARCHAR(40) NOT NULL DEFAULT '',
			message TEXT NOT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'new',
			ip_address VARBINARY(16) NULL,
			user_agent VARCHAR(255) NOT NULL DEFAULT '',
			referrer VARCHAR(255) NOT NULL DEFAULT '',
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY idx_email (email),
			KEY idx_status (status),
			KEY idx_created_at (created_at)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( 'vivus_leads_db_version', VIVUS_LEADS_DB_VERSION );
	}

	/**
	 * Re-run dbDelta if the stored DB version is behind the code version.
	 * This lets schema changes ship without forcing a manual re-activation.
	 */
	public static function maybe_upgrade() {
		$installed = get_option( 'vivus_leads_db_version' );
		if ( VIVUS_LEADS_DB_VERSION !== $installed ) {
			self::activate();
		}
	}
}
