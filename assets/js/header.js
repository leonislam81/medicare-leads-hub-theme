(function () {
	'use strict';

	var header = document.querySelector('.site-header');
	var toggle = document.querySelector('.menu-toggle');

	if (!header || !toggle) {
		return;
	}

	function setMenuState(open) {
		header.classList.toggle('menu-open', open);
		toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
	}

	toggle.addEventListener('click', function () {
		setMenuState(!header.classList.contains('menu-open'));
	});

	var navLinks = header.querySelectorAll('.site-navigation a');
	Array.prototype.forEach.call(navLinks, function (link) {
		link.addEventListener('click', function () {
			setMenuState(false);
		});
	});

	document.addEventListener('keydown', function (event) {
		if (event.key === 'Escape' && header.classList.contains('menu-open')) {
			setMenuState(false);
			toggle.focus();
		}
	});

	window.addEventListener('resize', function () {
		if (window.innerWidth > 760 && header.classList.contains('menu-open')) {
			setMenuState(false);
		}
	});
}());

(function () {
	'use strict';

	var servicesSections = document.querySelectorAll('.services-section');

	if (!servicesSections.length) {
		return;
	}

	Array.prototype.forEach.call(servicesSections, function (servicesSection) {
		var firstMedia = servicesSection.querySelector('.service-card__media');

		if (!firstMedia) {
			return;
		}

		var frame = 0;

		function updateBackgroundStop() {
			if (frame) {
				window.cancelAnimationFrame(frame);
			}

			frame = window.requestAnimationFrame(function () {
				var sectionRect = servicesSection.getBoundingClientRect();
				var mediaRect = firstMedia.getBoundingClientRect();
				servicesSection.style.setProperty(
					'--mlh-services-color-stop',
					Math.ceil(mediaRect.bottom - sectionRect.top) + 'px'
				);
				frame = 0;
			});
		}

		updateBackgroundStop();
		window.addEventListener('load', updateBackgroundStop, { once: true });
		window.addEventListener('resize', updateBackgroundStop);

		if (window.ResizeObserver) {
			var observer = new ResizeObserver(updateBackgroundStop);
			observer.observe(servicesSection);
			observer.observe(firstMedia);
		}
	});
}());
