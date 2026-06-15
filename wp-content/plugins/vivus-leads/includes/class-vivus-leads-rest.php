<?php
/**
 * REST endpoint for submitting leads.
 *
 * Security layers applied here:
 *  - WordPress REST nonce (X-WP-Nonce) to mitigate CSRF.
 *  - Strict sanitisation + validation of every field.
 *  - Honeypot field to deter naive bots.
 *  - Per-IP rate limiting (transient + DB count).
 *
 * @package Vivus_Leads
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and handles the leads REST route.
 */
class Vivus_Leads_Rest {

	const NAMESPACE = 'vivus/v1';
	const ROUTE     = '/leads';

	/**
	 * Hook route registration.
	 */
	public function register() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the POST /vivus/v1/leads route.
	 */
	public function register_routes() {
		register_rest_route(
			self::NAMESPACE,
			self::ROUTE,
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'handle' ),
				'permission_callback' => '__return_true', // Public form; protected by nonce + validation below.
				'args'                => array(
					'name'    => array( 'required' => true ),
					'email'   => array( 'required' => true ),
					'message' => array( 'required' => true ),
				),
			)
		);
	}

	/**
	 * Handle a submission.
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response
	 */
	public function handle( WP_REST_Request $request ) {
		// 1) CSRF: verify the REST nonce supplied by the form.
		$nonce = $request->get_header( 'X-WP-Nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return $this->error( 'invalid_nonce', __( 'Your session expired. Please refresh the page and try again.', 'vivus-leads' ), 403 );
		}

		// 2) Honeypot: a hidden field that humans never fill in.
		if ( '' !== trim( (string) $request->get_param( 'company_website' ) ) ) {
			// Pretend success so bots don't learn they were caught.
			return $this->success( __( 'Thanks! We’ll be in touch shortly.', 'vivus-leads' ) );
		}

		// 3) Sanitise inputs.
		$name         = sanitize_text_field( (string) $request->get_param( 'name' ) );
		$email        = sanitize_email( (string) $request->get_param( 'email' ) );
		$organisation = sanitize_text_field( (string) $request->get_param( 'organisation' ) );
		$team_size    = sanitize_text_field( (string) $request->get_param( 'team_size' ) );
		$message      = sanitize_textarea_field( (string) $request->get_param( 'message' ) );

		// 4) Validate.
		$errors = array();
		if ( '' === $name || mb_strlen( $name ) > 150 ) {
			$errors['name'] = __( 'Please enter your name.', 'vivus-leads' );
		}
		if ( '' === $email || ! is_email( $email ) ) {
			$errors['email'] = __( 'Please enter a valid email address.', 'vivus-leads' );
		}
		if ( '' === $message || mb_strlen( $message ) < 5 ) {
			$errors['message'] = __( 'Please tell us a little about what you need.', 'vivus-leads' );
		}
		if ( mb_strlen( $message ) > 5000 ) {
			$errors['message'] = __( 'That message is a little too long.', 'vivus-leads' );
		}
		$allowed_sizes = array( '', '1-5', '6-20', '21-100', '100+' );
		if ( ! in_array( $team_size, $allowed_sizes, true ) ) {
			$team_size = '';
		}

		if ( $errors ) {
			return $this->error( 'validation_failed', __( 'Please check the highlighted fields.', 'vivus-leads' ), 422, $errors );
		}

		// 5) Rate limit per IP (transient = fast path, DB = backstop).
		$ip = $this->client_ip();
		if ( $ip ) {
			$bucket = 'vivus_leads_rl_' . md5( $ip );
			$hits   = (int) get_transient( $bucket );
			if ( $hits >= 5 || Vivus_Leads_DB::recent_count_for_ip( $ip, 300 ) >= 5 ) {
				return $this->error( 'rate_limited', __( 'Too many submissions. Please try again in a few minutes.', 'vivus-leads' ), 429 );
			}
			set_transient( $bucket, $hits + 1, 5 * MINUTE_IN_SECONDS );
		}

		// 6) Persist.
		$id = Vivus_Leads_DB::insert(
			array(
				'name'         => $name,
				'email'        => $email,
				'organisation' => $organisation,
				'team_size'    => $team_size,
				'message'      => $message,
				'ip_address'   => $ip,
				'user_agent'   => substr( sanitize_text_field( (string) $request->get_header( 'user_agent' ) ), 0, 255 ),
				'referrer'     => substr( esc_url_raw( (string) $request->get_header( 'referer' ) ), 0, 255 ),
			)
		);

		if ( ! $id ) {
			return $this->error( 'db_error', __( 'Something went wrong saving your message. Please try again.', 'vivus-leads' ), 500 );
		}

		// 7) Notify the site admin (best-effort).
		$this->notify_admin( $id, $name, $email, $organisation, $message );

		/**
		 * Fires after a lead is stored. Lets other code hook in (CRM, Slack…).
		 *
		 * @param int   $id   New lead id.
		 * @param array $data Lead data.
		 */
		do_action( 'vivus_leads_created', $id, compact( 'name', 'email', 'organisation', 'team_size', 'message' ) );

		return $this->success( __( 'Thanks! We’ll be in touch within one business day.', 'vivus-leads' ), array( 'id' => $id ) );
	}

	/**
	 * Email the admin about a new lead.
	 */
	private function notify_admin( $id, $name, $email, $organisation, $message ) {
		$to      = get_option( 'admin_email' );
		$subject = sprintf( '[%s] New demo request from %s', wp_specialchars_decode( get_bloginfo( 'name' ) ), $name );
		$body    = sprintf(
			"A new lead was submitted (#%d):\n\nName: %s\nEmail: %s\nOrganisation: %s\n\nMessage:\n%s\n",
			$id,
			$name,
			$email,
			$organisation ? $organisation : '—',
			$message
		);
		wp_mail( $to, $subject, $body );
	}

	/**
	 * Best-effort client IP detection.
	 *
	 * @return string
	 */
	private function client_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
	}

	/**
	 * Build a success response.
	 */
	private function success( $message, $extra = array() ) {
		return new WP_REST_Response( array_merge( array( 'success' => true, 'message' => $message ), $extra ), 200 );
	}

	/**
	 * Build an error response.
	 */
	private function error( $code, $message, $status, $fields = array() ) {
		return new WP_REST_Response(
			array(
				'success' => false,
				'code'    => $code,
				'message' => $message,
				'fields'  => $fields,
			),
			$status
		);
	}
}
