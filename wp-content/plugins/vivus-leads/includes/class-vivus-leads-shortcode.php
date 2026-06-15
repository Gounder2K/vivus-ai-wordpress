<?php
/**
 * [vivus_contact_form] shortcode.
 *
 * Renders the lead-capture form and enqueues its script (which posts to the
 * REST endpoint). The REST nonce + endpoint URL are passed via wp_localize_script.
 *
 * @package Vivus_Leads
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the contact form shortcode + its assets.
 */
class Vivus_Leads_Shortcode {

	/**
	 * Register hooks.
	 */
	public function register() {
		add_shortcode( 'vivus_contact_form', array( $this, 'render' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Register (but don't force-enqueue) the form script; the shortcode
	 * enqueues it on demand so other pages stay lean.
	 */
	public function register_assets() {
		wp_register_style(
			'vivus-leads-form',
			VIVUS_LEADS_URL . 'assets/css/form.css',
			array(),
			VIVUS_LEADS_VERSION
		);

		wp_register_script(
			'vivus-leads-form',
			VIVUS_LEADS_URL . 'assets/js/form.js',
			array( 'jquery' ),
			VIVUS_LEADS_VERSION,
			true
		);

		wp_localize_script(
			'vivus-leads-form',
			'VivusLeads',
			array(
				'endpoint' => esc_url_raw( rest_url( Vivus_Leads_Rest::NAMESPACE . Vivus_Leads_Rest::ROUTE ) ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
			)
		);
	}

	/**
	 * Render the form markup.
	 *
	 * @param array $atts Shortcode attributes (unused, reserved for future use).
	 * @return string
	 */
	public function render( $atts = array() ) {
		wp_enqueue_style( 'vivus-leads-form' );
		wp_enqueue_script( 'vivus-leads-form' );

		$sizes = array(
			''       => __( 'Team size (optional)', 'vivus-leads' ),
			'1-5'    => __( '1–5 clinicians', 'vivus-leads' ),
			'6-20'   => __( '6–20 clinicians', 'vivus-leads' ),
			'21-100' => __( '21–100 clinicians', 'vivus-leads' ),
			'100+'   => __( '100+ clinicians', 'vivus-leads' ),
		);

		ob_start();
		?>
		<form class="vivus-form" id="vivus-lead-form" novalidate>
			<div class="vivus-form__feedback" id="vivus-form-feedback" role="status" aria-live="polite"></div>

			<div class="row g-3">
				<div class="col-md-6">
					<label class="form-label" for="vivus-name"><?php esc_html_e( 'Name', 'vivus-leads' ); ?> <span aria-hidden="true">*</span></label>
					<input type="text" class="form-control" id="vivus-name" name="name" required maxlength="150" autocomplete="name" />
				</div>
				<div class="col-md-6">
					<label class="form-label" for="vivus-email"><?php esc_html_e( 'Work email', 'vivus-leads' ); ?> <span aria-hidden="true">*</span></label>
					<input type="email" class="form-control" id="vivus-email" name="email" required maxlength="190" autocomplete="email" />
				</div>
				<div class="col-md-6">
					<label class="form-label" for="vivus-org"><?php esc_html_e( 'Organisation', 'vivus-leads' ); ?></label>
					<input type="text" class="form-control" id="vivus-org" name="organisation" maxlength="190" autocomplete="organization" />
				</div>
				<div class="col-md-6">
					<label class="form-label" for="vivus-size"><?php esc_html_e( 'Team size', 'vivus-leads' ); ?></label>
					<select class="form-select" id="vivus-size" name="team_size">
						<?php foreach ( $sizes as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-12">
					<label class="form-label" for="vivus-message"><?php esc_html_e( 'How can we help?', 'vivus-leads' ); ?> <span aria-hidden="true">*</span></label>
					<textarea class="form-control" id="vivus-message" name="message" rows="4" required maxlength="5000"></textarea>
				</div>

				<!-- Honeypot: hidden from humans, catches naive bots. -->
				<div class="vivus-form__honeypot" aria-hidden="true">
					<label for="vivus-company-website"><?php esc_html_e( 'Leave this field empty', 'vivus-leads' ); ?></label>
					<input type="text" id="vivus-company-website" name="company_website" tabindex="-1" autocomplete="off" />
				</div>

				<div class="col-12 d-flex align-items-center gap-3">
					<button type="submit" class="btn btn-dark btn-lg vivus-btn" id="vivus-submit">
						<?php esc_html_e( 'Request a demo', 'vivus-leads' ); ?>
					</button>
					<span class="vivus-form__spinner spinner-border spinner-border-sm text-secondary" role="status" aria-hidden="true" style="display:none;"></span>
				</div>
				<p class="col-12 text-muted small mb-0">
					<?php esc_html_e( 'By submitting you agree to be contacted about Vivus AI. We never share your details.', 'vivus-leads' ); ?>
				</p>
			</div>
		</form>
		<?php
		return ob_get_clean();
	}
}
