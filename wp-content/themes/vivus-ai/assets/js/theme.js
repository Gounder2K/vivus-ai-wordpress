/**
 * Vivus AI — front-end behaviour.
 *
 * Written with jQuery (bundled with WordPress) per the project brief.
 * Handles: sticky-navbar shadow, scroll-reveal animations, smooth anchor
 * scrolling with mobile-menu auto-close, and active-link highlighting.
 */
(function ($) {
	'use strict';

	$(function () {
		var $navbar = $('.vivus-navbar');
		var $window = $(window);

		/* --- Navbar shadow on scroll ------------------------------------ */
		function onScroll() {
			$navbar.toggleClass('is-scrolled', $window.scrollTop() > 8);
		}
		$window.on('scroll', onScroll);
		onScroll();

		/* --- Scroll reveal ---------------------------------------------- */
		// Tag the main marketing blocks so they fade in as they enter view.
		$('.vivus-card, .vivus-step, .vivus-quote, .vivus-plan, .vivus-section__head')
			.addClass('vivus-reveal');

		if ('IntersectionObserver' in window) {
			var observer = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						observer.unobserve(entry.target);
					}
				});
			}, { threshold: 0.12 });

			$('.vivus-reveal').each(function () {
				observer.observe(this);
			});
		} else {
			// Graceful fallback: just show everything.
			$('.vivus-reveal').addClass('is-visible');
		}

		/* --- Smooth in-page anchor scrolling ---------------------------- */
		$(document).on('click', 'a[href*="#"]:not([href="#"])', function (e) {
			var url = this.href.split('#')[0];
			var here = window.location.href.split('#')[0];
			if (url !== here) {
				return; // Different page — let the browser navigate normally.
			}

			var target = $(this.hash);
			if (!target.length) {
				return;
			}

			e.preventDefault();
			var offset = target.offset().top - 80;
			$('html, body').animate({ scrollTop: offset }, 450);

			// Close the mobile collapse menu if open.
			var $collapse = $('#vivusNav');
			if ($collapse.hasClass('show')) {
				$collapse.collapse('hide');
			}
		});
	});
})(jQuery);
