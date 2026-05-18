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
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
