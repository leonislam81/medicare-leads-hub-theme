(function () {
	'use strict';

	var faqLists = document.querySelectorAll('.booking-faq-list');

	if (!faqLists.length) {
		return;
	}

	Array.prototype.forEach.call(faqLists, function (list) {
		var items = list.querySelectorAll('.booking-faq-item');

		Array.prototype.forEach.call(items, function (item) {
			var trigger = item.querySelector('.booking-faq-item__trigger');

			if (!trigger) {
				return;
			}

			trigger.addEventListener('click', function () {
				var isOpen = item.classList.contains('is-open');

				Array.prototype.forEach.call(items, function (otherItem) {
					var otherTrigger = otherItem.querySelector('.booking-faq-item__trigger');

					otherItem.classList.remove('is-open');
					if (otherTrigger) {
						otherTrigger.setAttribute('aria-expanded', 'false');
						var otherIcon = otherTrigger.querySelector('.booking-faq-item__icon');
						if (otherIcon) {
							otherIcon.textContent = '+';
						}
					}
				});

				if (!isOpen) {
					item.classList.add('is-open');
					trigger.setAttribute('aria-expanded', 'true');
					var icon = trigger.querySelector('.booking-faq-item__icon');
					if (icon) {
						icon.textContent = '−';
					}
				}
			});
		});
	});
}());
