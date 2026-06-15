<?php
/**
 * Interactive (mocked) chat demo.
 *
 * The conversation is driven entirely client-side by assets/js/chat-demo.js
 * using scripted responses, so it works offline with no API key required.
 *
 * @package Vivus_AI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$prompts = array(
	__( 'Summarise the latest type 2 diabetes guidance', 'vivus-ai' ),
	__( 'Draft a referral letter for a cardiology review', 'vivus-ai' ),
	__( 'What are red flags for acute lower back pain?', 'vivus-ai' ),
);
?>
<section class="vivus-section" id="demo">
	<div class="container">
		<div class="vivus-section__head text-center mx-auto">
			<span class="vivus-eyebrow"><?php esc_html_e( 'Live demo', 'vivus-ai' ); ?></span>
			<h2 class="vivus-section__title"><?php esc_html_e( 'Try Vivus AI right here', 'vivus-ai' ); ?></h2>
			<p class="vivus-section__lead"><?php esc_html_e( 'This is a scripted preview of the clinical assistant. Pick a prompt or type your own.', 'vivus-ai' ); ?></p>
		</div>

		<div class="vivus-chat mx-auto" id="vivus-chat">
			<div class="vivus-chat__header">
				<?php vivus_ai_logo( array( 'height' => 26 ) ); ?>
				<span class="vivus-chat__status"><span class="vivus-dot"></span><?php esc_html_e( 'Demo mode', 'vivus-ai' ); ?></span>
			</div>

			<div class="vivus-chat__log" id="vivus-chat-log" aria-live="polite">
				<div class="vivus-chat__msg vivus-chat__msg--ai">
					<?php esc_html_e( 'Hi, I’m Vivus AI. Ask me a clinical question, or pick one of the suggestions below.', 'vivus-ai' ); ?>
				</div>
			</div>

			<div class="vivus-chat__prompts" id="vivus-chat-prompts">
				<?php foreach ( $prompts as $prompt ) : ?>
					<button type="button" class="vivus-chip" data-prompt="<?php echo esc_attr( $prompt ); ?>">
						<?php echo esc_html( $prompt ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<form class="vivus-chat__form" id="vivus-chat-form" autocomplete="off">
				<input type="text" class="form-control vivus-chat__input" id="vivus-chat-input"
					placeholder="<?php esc_attr_e( 'Type a message…', 'vivus-ai' ); ?>"
					aria-label="<?php esc_attr_e( 'Message Vivus AI', 'vivus-ai' ); ?>" maxlength="500" />
				<button type="submit" class="btn btn-dark vivus-chat__send" aria-label="<?php esc_attr_e( 'Send', 'vivus-ai' ); ?>">
					<i class="bi bi-arrow-up" aria-hidden="true"></i>
				</button>
			</form>
		</div>
		<p class="text-center vivus-demo-note mt-3">
			<i class="bi bi-info-circle me-1" aria-hidden="true"></i><?php esc_html_e( 'Responses are illustrative and for demonstration only.', 'vivus-ai' ); ?>
		</p>
	</div>
</section>
