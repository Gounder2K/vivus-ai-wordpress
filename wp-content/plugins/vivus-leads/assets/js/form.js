/**
 * Vivus Leads — contact form submission (jQuery + REST).
 *
 * Posts the form to the plugin's REST endpoint with the WordPress REST nonce,
 * shows inline validation/feedback, and never reloads the page.
 */
(function ($) {
	'use strict';

	$(function () {
		var $form = $('#vivus-lead-form');
		if (!$form.length || typeof VivusLeads === 'undefined') {
			return;
		}

		var $feedback = $('#vivus-form-feedback');
		var $submit = $('#vivus-submit');
		var $spinner = $form.find('.vivus-form__spinner');

		function setFeedback(type, message) {
			$feedback
				.removeClass('is-success is-error')
				.addClass(type === 'success' ? 'is-success' : 'is-error')
				.text(message);
		}

		function clearFieldErrors() {
			$form.find('.is-invalid').removeClass('is-invalid');
		}

		function markFieldErrors(fields) {
			if (!fields) {
				return;
			}
			$.each(fields, function (name) {
				$form.find('[name="' + name + '"]').addClass('is-invalid');
			});
		}

		$form.on('submit', function (e) {
			e.preventDefault();
			clearFieldErrors();
			$feedback.removeClass('is-success is-error').hide();

			$submit.prop('disabled', true);
			$spinner.show();

			$.ajax({
				url: VivusLeads.endpoint,
				method: 'POST',
				dataType: 'json',
				// Send as form-encoded so PHP reads each field via get_param().
				data: $form.serialize(),
				headers: { 'X-WP-Nonce': VivusLeads.nonce }
			})
				.done(function (res) {
					if (res && res.success) {
						setFeedback('success', res.message);
						$form[0].reset();
					} else {
						setFeedback('error', (res && res.message) || 'Something went wrong.');
						markFieldErrors(res && res.fields);
					}
				})
				.fail(function (xhr) {
					var res = xhr.responseJSON;
					setFeedback('error', (res && res.message) || 'Network error. Please try again.');
					markFieldErrors(res && res.fields);
				})
				.always(function () {
					$submit.prop('disabled', false);
					$spinner.hide();
					$feedback.show();
					$('html, body').animate(
						{ scrollTop: $feedback.offset().top - 120 },
						300
					);
				});
		});
	});
})(jQuery);
