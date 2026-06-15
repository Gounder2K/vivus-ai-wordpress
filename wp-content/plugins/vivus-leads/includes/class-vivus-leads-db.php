<?php
/**
 * Data-access layer for Vivus Leads.
 *
 * All queries use $wpdb->prepare() (or the safe insert/update helpers) so
 * user input is never concatenated into SQL.
 *
 * @package Vivus_Leads
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Thin repository around the custom leads table.
 */
class Vivus_Leads_DB {

	/**
	 * Allowed status values (whitelist used when filtering / updating).
	 *
	 * @return string[]
	 */
	public static function statuses() {
		return array( 'new', 'contacted', 'closed', 'spam' );
	}

	/**
	 * Insert a new lead.
	 *
	 * @param array $data Sanitised field values.
	 * @return int|false Inserted row id or false on failure.
	 */
	public static function insert( array $data ) {
		global $wpdb;
		$table = Vivus_Leads_Activator::table_name();

		// Store the IP in packed binary form (privacy-friendlier + compact).
		$ip_raw = isset( $data['ip_address'] ) ? @inet_pton( $data['ip_address'] ) : null;

		$inserted = $wpdb->insert(
			$table,
			array(
				'name'         => $data['name'],
				'email'        => $data['email'],
				'organisation' => isset( $data['organisation'] ) ? $data['organisation'] : '',
				'team_size'    => isset( $data['team_size'] ) ? $data['team_size'] : '',
				'message'      => $data['message'],
				'status'       => 'new',
				'ip_address'   => $ip_raw ? $ip_raw : null,
				'user_agent'   => isset( $data['user_agent'] ) ? $data['user_agent'] : '',
				'referrer'     => isset( $data['referrer'] ) ? $data['referrer'] : '',
				'created_at'   => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		return $inserted ? (int) $wpdb->insert_id : false;
	}

	/**
	 * Count submissions from an IP within the last N seconds.
	 * Used for lightweight rate limiting.
	 *
	 * @param string $ip      IP address (string form).
	 * @param int    $seconds Look-back window.
	 * @return int
	 */
	public static function recent_count_for_ip( $ip, $seconds = 60 ) {
		global $wpdb;
		$table  = Vivus_Leads_Activator::table_name();
		$packed = @inet_pton( $ip );
		if ( ! $packed ) {
			return 0;
		}

		$since = gmdate( 'Y-m-d H:i:s', time() - (int) $seconds );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is internal, values are prepared.
		$sql = $wpdb->prepare(
			"SELECT COUNT(*) FROM {$table} WHERE ip_address = %s AND created_at >= %s",
			$packed,
			$since
		);

		return (int) $wpdb->get_var( $sql );
	}

	/**
	 * Fetch a paginated, optionally filtered list of leads.
	 *
	 * @param array $args status, search, per_page, page, orderby, order.
	 * @return array{items: array, total: int}
	 */
	public static function get_results( array $args = array() ) {
		global $wpdb;
		$table = Vivus_Leads_Activator::table_name();

		$args = wp_parse_args(
			$args,
			array(
				'status'   => '',
				'search'   => '',
				'per_page' => 20,
				'page'     => 1,
				'orderby'  => 'created_at',
				'order'    => 'DESC',
			)
		);

		// Whitelist orderby / order to avoid SQL injection via sort params.
		$allowed_orderby = array( 'id', 'name', 'email', 'status', 'created_at' );
		$orderby         = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'created_at';
		$order           = ( 'ASC' === strtoupper( $args['order'] ) ) ? 'ASC' : 'DESC';

		$where  = array( '1=1' );
		$params = array();

		if ( $args['status'] && in_array( $args['status'], self::statuses(), true ) ) {
			$where[]  = 'status = %s';
			$params[] = $args['status'];
		}

		if ( '' !== $args['search'] ) {
			$like     = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$where[]  = '(name LIKE %s OR email LIKE %s OR organisation LIKE %s)';
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
		}

		$where_sql = implode( ' AND ', $where );

		$per_page = max( 1, (int) $args['per_page'] );
		$page     = max( 1, (int) $args['page'] );
		$offset   = ( $page - 1 ) * $per_page;

		// Total count (with filters applied).
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_sql}";
		$total     = $params
			? (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $params ) )
			: (int) $wpdb->get_var( $count_sql );

		// Page of results.
		$query_params = array_merge( $params, array( $per_page, $offset ) );
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- orderby/order whitelisted above.
		$list_sql = "SELECT * FROM {$table} WHERE {$where_sql} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";
		$items    = $wpdb->get_results( $wpdb->prepare( $list_sql, $query_params ) );

		return array(
			'items' => $items ? $items : array(),
			'total' => $total,
		);
	}

	/**
	 * Update a lead's status (whitelisted).
	 *
	 * @param int    $id     Lead id.
	 * @param string $status New status.
	 * @return bool
	 */
	public static function update_status( $id, $status ) {
		global $wpdb;
		if ( ! in_array( $status, self::statuses(), true ) ) {
			return false;
		}
		$table = Vivus_Leads_Activator::table_name();
		return (bool) $wpdb->update(
			$table,
			array( 'status' => $status ),
			array( 'id' => (int) $id ),
			array( '%s' ),
			array( '%d' )
		);
	}

	/**
	 * Delete a lead.
	 *
	 * @param int $id Lead id.
	 * @return bool
	 */
	public static function delete( $id ) {
		global $wpdb;
		$table = Vivus_Leads_Activator::table_name();
		return (bool) $wpdb->delete( $table, array( 'id' => (int) $id ), array( '%d' ) );
	}

	/**
	 * Convert a packed IP back to a readable string for display.
	 *
	 * @param mixed $packed Binary IP from the DB.
	 * @return string
	 */
	public static function format_ip( $packed ) {
		if ( empty( $packed ) ) {
			return '';
		}
		$ip = @inet_ntop( $packed );
		return $ip ? $ip : '';
	}
}
