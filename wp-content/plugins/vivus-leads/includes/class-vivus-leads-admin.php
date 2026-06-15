<?php
/**
 * Admin dashboard for Vivus Leads.
 *
 * Lists submissions with search, status filter and pagination, lets an
 * editor change status or delete a lead, and exports filtered results to CSV.
 * Every state-changing action is capability- and nonce-checked.
 *
 * @package Vivus_Leads
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings + list screen for captured leads.
 */
class Vivus_Leads_Admin {

	const CAPABILITY = 'manage_options';
	const SLUG       = 'vivus-leads';
	const PER_PAGE   = 20;

	/**
	 * Register hooks.
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'maybe_handle_actions' ) );
		add_action( 'admin_post_vivus_leads_export', array( $this, 'export_csv' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Load the admin stylesheet only on the plugin's screen.
	 *
	 * @param string $hook Current admin page hook suffix.
	 */
	public function enqueue_assets( $hook ) {
		if ( 'toplevel_page_' . self::SLUG !== $hook ) {
			return;
		}
		wp_enqueue_style(
			'vivus-leads-admin',
			VIVUS_LEADS_URL . 'assets/css/admin.css',
			array(),
			VIVUS_LEADS_VERSION
		);
	}

	/**
	 * Add the top-level admin menu.
	 */
	public function add_menu() {
		$hook = add_menu_page(
			__( 'Vivus Leads', 'vivus-leads' ),
			__( 'Vivus Leads', 'vivus-leads' ),
			self::CAPABILITY,
			self::SLUG,
			array( $this, 'render_page' ),
			'dashicons-email-alt',
			26
		);
		unset( $hook );
	}

	/**
	 * Handle status-change and delete actions (GET links with nonces).
	 */
	public function maybe_handle_actions() {
		if ( ! isset( $_GET['page'] ) || self::SLUG !== $_GET['page'] ) {
			return;
		}
		if ( ! current_user_can( self::CAPABILITY ) ) {
			return;
		}

		// Update status.
		if ( isset( $_GET['vivus_action'], $_GET['lead'], $_GET['_wpnonce'] ) && 'set_status' === $_GET['vivus_action'] ) {
			$id     = absint( $_GET['lead'] );
			$status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
			if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'vivus_set_status_' . $id ) ) {
				Vivus_Leads_DB::update_status( $id, $status );
				$this->redirect_with_notice( 'updated' );
			}
		}

		// Delete.
		if ( isset( $_GET['vivus_action'], $_GET['lead'], $_GET['_wpnonce'] ) && 'delete' === $_GET['vivus_action'] ) {
			$id = absint( $_GET['lead'] );
			if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'vivus_delete_' . $id ) ) {
				Vivus_Leads_DB::delete( $id );
				$this->redirect_with_notice( 'deleted' );
			}
		}
	}

	/**
	 * Redirect back to the list with a notice flag, preserving filters.
	 *
	 * @param string $notice Notice key.
	 */
	private function redirect_with_notice( $notice ) {
		$url = add_query_arg(
			array(
				'page'         => self::SLUG,
				'vivus_notice' => $notice,
				'status'       => isset( $_GET['status_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['status_filter'] ) ) : '',
			),
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Read + normalise list-table query args from the request.
	 *
	 * @return array
	 */
	private function current_query_args() {
		$paged  = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
		$status = isset( $_GET['status_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['status_filter'] ) ) : '';
		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

		return array(
			'status'   => $status,
			'search'   => $search,
			'per_page' => self::PER_PAGE,
			'page'     => $paged,
		);
	}

	/**
	 * Render the admin list screen.
	 */
	public function render_page() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You do not have permission to view this page.', 'vivus-leads' ) );
		}

		$args    = $this->current_query_args();
		$results = Vivus_Leads_DB::get_results( $args );
		$items   = $results['items'];
		$total   = $results['total'];
		$pages   = (int) ceil( $total / self::PER_PAGE );

		$export_url = wp_nonce_url(
			add_query_arg(
				array(
					'action'        => 'vivus_leads_export',
					'status_filter' => $args['status'],
					's'             => $args['search'],
				),
				admin_url( 'admin-post.php' )
			),
			'vivus_leads_export'
		);
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Vivus Leads', 'vivus-leads' ); ?></h1>
			<a href="<?php echo esc_url( $export_url ); ?>" class="page-title-action"><?php esc_html_e( 'Export CSV', 'vivus-leads' ); ?></a>
			<hr class="wp-header-end" />

			<?php $this->render_notice(); ?>

			<form method="get">
				<input type="hidden" name="page" value="<?php echo esc_attr( self::SLUG ); ?>" />
				<ul class="subsubsub">
					<?php $this->render_status_links( $args['status'], $total ); ?>
				</ul>
				<p class="search-box">
					<label class="screen-reader-text" for="vivus-search"><?php esc_html_e( 'Search leads', 'vivus-leads' ); ?></label>
					<input type="search" id="vivus-search" name="s" value="<?php echo esc_attr( $args['search'] ); ?>" placeholder="<?php esc_attr_e( 'Search name, email, org…', 'vivus-leads' ); ?>" />
					<input type="hidden" name="status_filter" value="<?php echo esc_attr( $args['status'] ); ?>" />
					<input type="submit" class="button" value="<?php esc_attr_e( 'Search', 'vivus-leads' ); ?>" />
				</p>

				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'Name', 'vivus-leads' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Email', 'vivus-leads' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Organisation', 'vivus-leads' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Team', 'vivus-leads' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Message', 'vivus-leads' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Status', 'vivus-leads' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Received', 'vivus-leads' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Actions', 'vivus-leads' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $items ) ) : ?>
							<tr><td colspan="8"><?php esc_html_e( 'No leads found.', 'vivus-leads' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $items as $lead ) : ?>
								<?php $this->render_row( $lead, $args['status'] ); ?>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>

				<?php $this->render_pagination( $pages, $args['page'], $total ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render a single table row.
	 *
	 * @param object $lead          Lead row.
	 * @param string $current_status Active status filter (for redirect back).
	 */
	private function render_row( $lead, $current_status ) {
		$statuses    = Vivus_Leads_DB::statuses();
		$delete_url  = wp_nonce_url(
			add_query_arg(
				array(
					'page'          => self::SLUG,
					'vivus_action'  => 'delete',
					'lead'          => (int) $lead->id,
					'status_filter' => $current_status,
				),
				admin_url( 'admin.php' )
			),
			'vivus_delete_' . (int) $lead->id
		);
		?>
		<tr>
			<td><strong><?php echo esc_html( $lead->name ); ?></strong></td>
			<td><a href="mailto:<?php echo esc_attr( $lead->email ); ?>"><?php echo esc_html( $lead->email ); ?></a></td>
			<td><?php echo esc_html( $lead->organisation ? $lead->organisation : '—' ); ?></td>
			<td><?php echo esc_html( $lead->team_size ? $lead->team_size : '—' ); ?></td>
			<td><?php echo esc_html( wp_trim_words( $lead->message, 18, '…' ) ); ?></td>
			<td>
				<span class="vivus-status vivus-status--<?php echo esc_attr( $lead->status ); ?>">
					<?php echo esc_html( ucfirst( $lead->status ) ); ?>
				</span>
			</td>
			<td><?php echo esc_html( mysql2date( 'M j, Y H:i', $lead->created_at ) ); ?></td>
			<td>
				<div class="vivus-row-actions">
					<?php foreach ( $statuses as $status ) : ?>
						<?php if ( $status === $lead->status ) { continue; } ?>
						<?php
						$url = wp_nonce_url(
							add_query_arg(
								array(
									'page'          => self::SLUG,
									'vivus_action'  => 'set_status',
									'lead'          => (int) $lead->id,
									'status'        => $status,
									'status_filter' => $current_status,
								),
								admin_url( 'admin.php' )
							),
							'vivus_set_status_' . (int) $lead->id
						);
						?>
						<a href="<?php echo esc_url( $url ); ?>" class="button button-small"><?php echo esc_html( ucfirst( $status ) ); ?></a>
					<?php endforeach; ?>
					<a href="<?php echo esc_url( $delete_url ); ?>" class="button button-small button-link-delete"
						onclick="return confirm('<?php echo esc_js( __( 'Delete this lead?', 'vivus-leads' ) ); ?>');">
						<?php esc_html_e( 'Delete', 'vivus-leads' ); ?>
					</a>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render the status filter links.
	 *
	 * @param string $active Active status.
	 * @param int    $total  Total leads (unfiltered count for "All").
	 */
	private function render_status_links( $active, $total ) {
		unset( $total );
		$links   = array( '' => __( 'All', 'vivus-leads' ) );
		foreach ( Vivus_Leads_DB::statuses() as $status ) {
			$links[ $status ] = ucfirst( $status );
		}

		$out = array();
		foreach ( $links as $key => $label ) {
			$url   = add_query_arg(
				array(
					'page'          => self::SLUG,
					'status_filter' => $key,
				),
				admin_url( 'admin.php' )
			);
			$class = ( $active === $key ) ? ' class="current"' : '';
			$out[] = sprintf( '<li><a href="%s"%s>%s</a></li>', esc_url( $url ), $class, esc_html( $label ) );
		}
		echo wp_kses_post( implode( ' | ', $out ) );
	}

	/**
	 * Render pagination controls.
	 *
	 * @param int $pages Total pages.
	 * @param int $page  Current page.
	 * @param int $total Total rows.
	 */
	private function render_pagination( $pages, $page, $total ) {
		if ( $pages < 2 ) {
			return;
		}
		echo '<div class="tablenav"><div class="tablenav-pages">';
		printf( '<span class="displaying-num">%s</span> ', esc_html( sprintf( _n( '%d item', '%d items', $total, 'vivus-leads' ), $total ) ) );
		echo wp_kses_post(
			paginate_links(
				array(
					'base'      => add_query_arg( 'paged', '%#%' ),
					'format'    => '',
					'current'   => $page,
					'total'     => $pages,
					'prev_text' => '&laquo;',
					'next_text' => '&raquo;',
				)
			)
		);
		echo '</div></div>';
	}

	/**
	 * Render an admin notice after an action.
	 */
	private function render_notice() {
		if ( empty( $_GET['vivus_notice'] ) ) {
			return;
		}
		$notice   = sanitize_text_field( wp_unslash( $_GET['vivus_notice'] ) );
		$messages = array(
			'updated' => __( 'Lead status updated.', 'vivus-leads' ),
			'deleted' => __( 'Lead deleted.', 'vivus-leads' ),
		);
		if ( isset( $messages[ $notice ] ) ) {
			printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( $messages[ $notice ] ) );
		}
	}

	/**
	 * Stream filtered leads as a CSV download.
	 */
	public function export_csv() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'Permission denied.', 'vivus-leads' ) );
		}
		check_admin_referer( 'vivus_leads_export' );

		$args             = $this->current_query_args();
		$args['per_page'] = 10000; // Export cap.
		$args['page']     = 1;
		$results          = Vivus_Leads_DB::get_results( $args );

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=vivus-leads-' . gmdate( 'Y-m-d' ) . '.csv' );

		$out = fopen( 'php://output', 'w' );
		fputcsv( $out, array( 'ID', 'Name', 'Email', 'Organisation', 'Team size', 'Message', 'Status', 'IP', 'Received' ) );
		foreach ( $results['items'] as $lead ) {
			fputcsv(
				$out,
				array(
					$lead->id,
					$lead->name,
					$lead->email,
					$lead->organisation,
					$lead->team_size,
					$lead->message,
					$lead->status,
					Vivus_Leads_DB::format_ip( $lead->ip_address ),
					$lead->created_at,
				)
			);
		}
		fclose( $out );
		exit;
	}
}
