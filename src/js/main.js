(function () {
	'use strict';

	function initHeaderMenu() {
		var header = document.querySelector('.site-header');
		var burger = document.querySelector('.site-header__burger');
		var mobilePanel = document.getElementById('site-header-mobile');

		if (!header || !burger || !mobilePanel) {
			return;
		}

		function setMenuOpen(isOpen) {
			header.classList.toggle('is-menu-open', isOpen);
			document.body.classList.toggle('is-menu-open', isOpen);
			document.body.classList.toggle('is-mobile-menu-open', isOpen);
			burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			mobilePanel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
		}

		burger.addEventListener('click', function () {
			setMenuOpen(!header.classList.contains('is-menu-open'));
		});

		document.addEventListener('keyup', function (event) {
			if (event.key === 'Escape' && header.classList.contains('is-menu-open')) {
				setMenuOpen(false);
			}
		});

		mobilePanel.addEventListener('click', function (event) {
			var link = event.target.closest('a');
			if (link && !link.classList.contains('site-header__button--mobile')) {
				setMenuOpen(false);
			}
		});
	}

	function getCountDecimals(value) {
		var stringValue = String(value);
		var dotIndex = stringValue.indexOf('.');

		if (dotIndex === -1) {
			return 0;
		}

		return stringValue.length - dotIndex - 1;
	}

	function animateCounter(element) {
		var rawValue = element.getAttribute('data-count');

		if (rawValue === null || rawValue === '') {
			return;
		}

		var target = parseFloat(rawValue);

		if (Number.isNaN(target)) {
			return;
		}

		var decimals = getCountDecimals(rawValue);
		var duration = 2000;
		var startTime = null;

		function formatValue(progress) {
			var current = target * progress;

			if (decimals === 0) {
				return String(Math.trunc(current));
			}

			return current.toFixed(decimals);
		}

		function tick(timestamp) {
			if (!startTime) {
				startTime = timestamp;
			}

			var elapsed = timestamp - startTime;
			var progress = Math.min(elapsed / duration, 1);
			var eased = 1 - Math.pow(1 - progress, 3);

			element.textContent = formatValue(eased);

			if (progress < 1) {
				window.requestAnimationFrame(tick);
			} else {
				element.textContent = decimals === 0 ? String(target) : target.toFixed(decimals);
			}
		}

		window.requestAnimationFrame(tick);
	}

	function initStats() {
		var sections = document.querySelectorAll('.stats-section');

		if (!sections.length) {
			return;
		}

		function revealSection(section) {
			section.classList.add('is-visible');

			section.querySelectorAll('.stats-section__number[data-count]').forEach(function (counter) {
				if (counter.dataset.animated === 'true') {
					return;
				}

				counter.dataset.animated = 'true';
				animateCounter(counter);
			});
		}

		if (!('IntersectionObserver' in window)) {
			sections.forEach(revealSection);
			return;
		}

		var observer = new IntersectionObserver(
			function (entries, obs) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					revealSection(entry.target);
					obs.unobserve(entry.target);
				});
			},
			{
				threshold: 0.2,
				rootMargin: '0px 0px -5% 0px',
			}
		);

		sections.forEach(function (section) {
			observer.observe(section);
		});
	}

	function initRunLine() {
		var sections = document.querySelectorAll('.run-line-section');

		if (!sections.length) {
			return;
		}

		function buildTrack(section) {
			var viewport = section.querySelector('.run-line-section__viewport');
			var track = section.querySelector('.run-line-section__track');
			var firstGroup = section.querySelector('.run-line-section__group');

			if (!viewport || !track || !firstGroup) {
				return;
			}

			var minWidth = Math.max(viewport.offsetWidth, window.innerWidth);

			while (track.scrollWidth < minWidth) {
				track.appendChild(firstGroup.cloneNode(true));
			}

			track.innerHTML = track.innerHTML + track.innerHTML;

			var halfWidth = track.scrollWidth / 2;
			var duration = halfWidth / 80;

			track.style.setProperty('--run-line-duration', duration + 's');
		}

		function revealSection(section) {
			buildTrack(section);
			section.classList.add('is-visible');
		}

		if (!('IntersectionObserver' in window)) {
			sections.forEach(revealSection);
			return;
		}

		var observer = new IntersectionObserver(
			function (entries, obs) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					revealSection(entry.target);
					obs.unobserve(entry.target);
				});
			},
			{
				threshold: 0.15,
				rootMargin: '0px 0px -5% 0px',
			}
		);

		sections.forEach(function (section) {
			observer.observe(section);
		});

		window.addEventListener('resize', function () {
			sections.forEach(function (section) {
				if (!section.classList.contains('is-visible')) {
					return;
				}

				var track = section.querySelector('.run-line-section__track');
				var groups = section.querySelectorAll('.run-line-section__group');

				if (!track || groups.length < 2) {
					return;
				}

				var halfWidth = track.scrollWidth / 2;
				track.style.setProperty('--run-line-duration', halfWidth / 80 + 's');
			});
		});
	}

	function initWhyChoose() {
		var sections = document.querySelectorAll('.why-choose-section');

		if (!sections.length) {
			return;
		}

		if (!('IntersectionObserver' in window)) {
			sections.forEach(function (section) {
				section.classList.add('is-visible');
			});
			return;
		}

		var observer = new IntersectionObserver(
			function (entries, obs) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					entry.target.classList.add('is-visible');
					obs.unobserve(entry.target);
				});
			},
			{
				threshold: 0.15,
				rootMargin: '0px 0px -5% 0px',
			}
		);

		sections.forEach(function (section) {
			observer.observe(section);
		});
	}

	function initReviews() {
		var sections = document.querySelectorAll('.reviews-section');

		if (!sections.length) {
			return;
		}

		function revealSection(section) {
			section.classList.add('is-visible');

			var counter = section.querySelector('.reviews-section__rating[data-count]');

			if (counter && counter.dataset.animated !== 'true') {
				counter.dataset.animated = 'true';
				animateCounter(counter);
			}

			var sliderEl = section.querySelector('.reviews-section__slider');

			if (!sliderEl || sliderEl.dataset.swiperInit === 'true') {
				return;
			}

			if (typeof window.Swiper === 'undefined') {
				return;
			}

			sliderEl.dataset.swiperInit = 'true';

			new window.Swiper(sliderEl, {
				slidesPerView: 1,
				slidesPerGroup: 1,
				spaceBetween: 20,
				loop: true,
				speed: 800,
				watchOverflow: true,
				navigation: {
					nextEl: section.querySelector('.reviews-section__nav--next'),
					prevEl: section.querySelector('.reviews-section__nav--prev'),
				},
				breakpoints: {
					551: {
						slidesPerView: 2,
						slidesPerGroup: 1,
						spaceBetween: 20,
					},
					901: {
						slidesPerView: 3,
						slidesPerGroup: 1,
						spaceBetween: 20,
					},
				},
			});
		}

		if (!('IntersectionObserver' in window)) {
			sections.forEach(revealSection);
			return;
		}

		var observer = new IntersectionObserver(
			function (entries, obs) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					revealSection(entry.target);
					obs.unobserve(entry.target);
				});
			},
			{
				threshold: 0.15,
				rootMargin: '0px 0px -5% 0px',
			}
		);

		sections.forEach(function (section) {
			observer.observe(section);
		});
	}

	function initHeroReveal() {
		var heroes = document.querySelectorAll('.hero-section');

		if (!heroes.length) {
			return;
		}

		if (!('IntersectionObserver' in window)) {
			heroes.forEach(function (hero) {
				hero.classList.add('is-visible');
			});
			return;
		}

		var observer = new IntersectionObserver(
			function (entries, obs) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					entry.target.classList.add('is-visible');
					obs.unobserve(entry.target);
				});
			},
			{
				threshold: 0.15,
				rootMargin: '0px 0px -5% 0px',
			}
		);

		heroes.forEach(function (hero) {
			observer.observe(hero);
		});
	}

	function enhanceContactSubmitButtons(root) {
		var scope = root || document;
		var inputs = scope.querySelectorAll(
			'.contact-section input[type="submit"].rhino-cf7-form__submit'
		);

		inputs.forEach(function (input) {
			if (input.getAttribute('data-rhino-submit-enhanced') === '1') {
				return;
			}

			var button = document.createElement('button');
			var attr;
			var i;

			button.type = 'submit';
			button.className = input.className;

			for (i = 0; i < input.attributes.length; i++) {
				attr = input.attributes[i];

				if (attr.name === 'type' || attr.name === 'value') {
					continue;
				}

				button.setAttribute(attr.name, attr.value);
			}

			button.innerHTML =
				'<span class="rhino-cf7-form__submit-text">' +
				(input.value || 'SEND REQUEST') +
				'</span><span class="rhino-cf7-form__submit-icon" aria-hidden="true"></span>';

			input.setAttribute('data-rhino-submit-enhanced', '1');
			input.parentNode.replaceChild(button, input);
		});
	}

	function initContact() {
		var sections = document.querySelectorAll('.contact-section');

		if (!sections.length) {
			return;
		}

		var modal =
			document.getElementById('contact-success-modal') ||
			document.querySelector('.contact-section__modal');

		function openModal() {
			if (!modal) {
				return;
			}

			modal.classList.add('is-open');
			modal.setAttribute('aria-hidden', 'false');
			document.body.classList.add('is-contact-modal-open');
		}

		function closeModal() {
			if (!modal) {
				return;
			}

			modal.classList.remove('is-open');
			modal.setAttribute('aria-hidden', 'true');
			document.body.classList.remove('is-contact-modal-open');
		}

		if (modal) {
			if (modal.parentNode !== document.body) {
				document.body.appendChild(modal);
			}

			if (modal.getAttribute('data-rhino-modal-init') !== '1') {
				modal.setAttribute('data-rhino-modal-init', '1');

				modal.querySelectorAll('[data-contact-modal-close]').forEach(function (trigger) {
					trigger.addEventListener('click', closeModal);
				});

				document.addEventListener('keydown', function (event) {
					if (event.key === 'Escape' && modal.classList.contains('is-open')) {
						closeModal();
					}
				});
			}
		}

		function bindContactForms(root) {
			if (!root) {
				return;
			}

			var forms = [];

			if (root.nodeName === 'FORM' && root.classList.contains('wpcf7-form')) {
				forms = [root];
			} else if (root.querySelectorAll) {
				forms = Array.prototype.slice.call(root.querySelectorAll('.wpcf7-form'));
			}

			forms.forEach(function (form) {
				if (!form.closest('.contact-section') && !form.closest('[data-rhino-contact-form]')) {
					return;
				}

				if (form.getAttribute('data-rhino-form-bound') === '1') {
					return;
				}

				form.setAttribute('data-rhino-form-bound', '1');

				form.addEventListener('wpcf7submit', function (event) {
					if (event.detail && event.detail.status === 'mail_sent') {
						openModal();
					}
				});

				form.addEventListener('wpcf7mailsent', openModal);

				if (window.jQuery) {
					window.jQuery(form).on('wpcf7mailsent', openModal);
				}
			});
		}

		sections.forEach(function (section) {
			enhanceContactSubmitButtons(section);
			bindContactForms(section);
		});

		function onCf7DomReady(event) {
			var target = event.target || (event.detail && event.detail.target);

			if (!target) {
				return;
			}

			enhanceContactSubmitButtons(target);
			bindContactForms(target);
		}

		document.addEventListener('wpcf7domready', onCf7DomReady);

		if (window.jQuery) {
			window.jQuery(document).on('wpcf7domready', onCf7DomReady);
		}

		function revealSection(section) {
			section.classList.add('is-visible');
		}

		if ('IntersectionObserver' in window) {
			var revealObserver = new IntersectionObserver(
				function (entries, obs) {
					entries.forEach(function (entry) {
						if (!entry.isIntersecting) {
							return;
						}

						revealSection(entry.target);
						obs.unobserve(entry.target);
					});
				},
				{
					threshold: 0.12,
					rootMargin: '0px 0px -5% 0px',
				}
			);

			sections.forEach(function (section) {
				revealObserver.observe(section);
			});
		} else {
			sections.forEach(revealSection);
		}
	}

	function initProcess() {
		var sections = document.querySelectorAll('.process-section');

		if (!sections.length) {
			return;
		}

		if (!('IntersectionObserver' in window)) {
			sections.forEach(function (section) {
				section.classList.add('is-visible');
			});
			return;
		}

		var observer = new IntersectionObserver(
			function (entries, obs) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					entry.target.classList.add('is-visible');
					obs.unobserve(entry.target);
				});
			},
			{
				threshold: 0.15,
				rootMargin: '0px 0px -5% 0px',
			}
		);

		sections.forEach(function (section) {
			observer.observe(section);
		});
	}

	function init() {
		initHeaderMenu();
		initHeroReveal();
		initStats();
		initRunLine();
		initWhyChoose();
		initReviews();
		initProcess();
		initContact();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
