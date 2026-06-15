/**
 * Vivus AI — mocked clinical chat demo (jQuery).
 *
 * Fully client-side: scripted, keyword-matched responses with a simulated
 * "typing" delay. No network calls, no API key — safe to run anywhere.
 * Responses are illustrative only and clearly labelled as a demo in the UI.
 */
(function ($) {
	'use strict';

	// Keyword → scripted reply. First match wins; otherwise the default.
	var SCRIPT = [
		{
			keywords: ['diabetes', 'type 2', 'hba1c', 'metformin'],
			reply:
				'Type 2 diabetes — concise overview:\n\n' +
				'• First-line: lifestyle changes + metformin (titrate to tolerance).\n' +
				'• Add an SGLT2 inhibitor or GLP-1 receptor agonist where there is established cardiovascular or renal disease.\n' +
				'• Review HbA1c every 3–6 months; individualise targets (often ~53 mmol/mol / 7%).\n\n' +
				'Would you like me to draft a patient-friendly summary or a review note?'
		},
		{
			keywords: ['referral', 'cardiology', 'letter', 'refer'],
			reply:
				'Here is a referral letter skeleton you can edit:\n\n' +
				'Dear Cardiology team,\n' +
				'I am referring [Patient name, DOB] for assessment of [presenting complaint]. ' +
				'Relevant history: [...]. Examination: [...]. Investigations to date: [...]. ' +
				'I would value your opinion on [specific question].\n\n' +
				'Want me to fill this in from a set of notes?'
		},
		{
			keywords: ['back pain', 'red flag', 'lower back', 'sciatica'],
			reply:
				'Red flags for acute lower back pain that warrant urgent review:\n\n' +
				'• Saddle anaesthesia, bladder/bowel dysfunction (cauda equina).\n' +
				'• Progressive neurological deficit.\n' +
				'• Fever, IV drug use, or immunosuppression (infection).\n' +
				'• History of cancer, unexplained weight loss.\n' +
				'• Significant trauma, or age <20 / >50 with new pain.\n\n' +
				'Shall I turn this into a triage checklist?'
		},
		{
			keywords: ['hypertension', 'blood pressure', 'bp'],
			reply:
				'Hypertension — quick guide:\n\n' +
				'• Confirm with ambulatory or home readings before diagnosis.\n' +
				'• Lifestyle first; consider an ACE inhibitor/ARB or calcium-channel blocker depending on age and ethnicity.\n' +
				'• Recheck within 4 weeks of starting or changing therapy.\n\n' +
				'I can draft a monitoring plan if that helps.'
		}
	];

	var DEFAULT_REPLY =
		'Thanks — in the full product I’d give you a structured, guideline-aware answer here, ' +
		'with the option to save it to a patient record or export it as a note. ' +
		'This preview is scripted, so try one of the suggested prompts above to see a sample response.';

	function pickReply(text) {
		var lower = text.toLowerCase();
		for (var i = 0; i < SCRIPT.length; i++) {
			var entry = SCRIPT[i];
			for (var k = 0; k < entry.keywords.length; k++) {
				if (lower.indexOf(entry.keywords[k]) !== -1) {
					return entry.reply;
				}
			}
		}
		return DEFAULT_REPLY;
	}

	$(function () {
		var $log = $('#vivus-chat-log');
		var $form = $('#vivus-chat-form');
		var $input = $('#vivus-chat-input');
		var $prompts = $('#vivus-chat-prompts');

		if (!$log.length || !$form.length) {
			return; // Demo section not on this page.
		}

		var busy = false;

		function scrollToBottom() {
			$log.stop().animate({ scrollTop: $log[0].scrollHeight }, 250);
		}

		// Append a message bubble. Uses .text() so user input is never
		// injected as HTML (defensive even in a demo).
		function addMessage(role, text) {
			var $msg = $('<div/>')
				.addClass('vivus-chat__msg')
				.addClass(role === 'user' ? 'vivus-chat__msg--user' : 'vivus-chat__msg--ai')
				.text(text);
			$log.append($msg);
			scrollToBottom();
			return $msg;
		}

		function showTyping() {
			var $typing = $(
				'<div class="vivus-chat__msg vivus-chat__msg--ai vivus-chat__msg--typing">' +
					'<span></span><span></span><span></span>' +
				'</div>'
			);
			$log.append($typing);
			scrollToBottom();
			return $typing;
		}

		function respondTo(text) {
			busy = true;
			var $typing = showTyping();
			var reply = pickReply(text);
			// Delay scales a little with reply length to feel natural.
			var delay = Math.min(1600, 600 + reply.length * 4);

			window.setTimeout(function () {
				$typing.remove();
				addMessage('ai', reply);
				busy = false;
			}, delay);
		}

		function send(text) {
			text = $.trim(text);
			if (!text || busy) {
				return;
			}
			addMessage('user', text);
			$input.val('');
			respondTo(text);
		}

		$form.on('submit', function (e) {
			e.preventDefault();
			send($input.val());
		});

		$prompts.on('click', '.vivus-chip', function () {
			send($(this).data('prompt'));
		});
	});
})(jQuery);
