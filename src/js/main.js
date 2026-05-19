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

	function init() {
		initHeaderMenu();
		initHeroReveal();
		initStats();
		initRunLine();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
