(function () {
	'use strict';

	function initHeaderMenu() {
		var header = document.querySelector('.site-header');
		var burger = document.querySelector('.site-header__burger');
		var mobilePanel = document.getElementById('site-header-mobile');

		if (!header || !burger || !mobilePanel) {
			return;
		}

		function resetMobileSubmenus() {
			mobilePanel.querySelectorAll('.menu-item-has-children.is-submenu-open').forEach(function (item) {
				item.classList.remove('is-submenu-open');

				var toggle = item.querySelector('.site-header__menu-toggle');
				var submenu = item.querySelector('.site-header__submenu');

				if (toggle) {
					toggle.setAttribute('aria-expanded', 'false');
				}

				if (submenu) {
					submenu.style.maxHeight = '0';
				}
			});
		}

		function setMenuOpen(isOpen) {
			if (!isOpen) {
				resetMobileSubmenus();
			}

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
			if (event.target.closest('.site-header__menu-toggle')) {
				return;
			}

			var link = event.target.closest('a');

			if (link) {
				setMenuOpen(false);
			}
		});

		mobilePanel.querySelectorAll('.site-header__menu-toggle').forEach(function (toggle) {
			toggle.addEventListener('click', function (event) {
				event.preventDefault();
				event.stopPropagation();

				var item = toggle.closest('.menu-item-has-children');
				var submenu = item ? item.querySelector('.site-header__submenu') : null;

				if (!item || !submenu) {
					return;
				}

				var isOpen = item.classList.contains('is-submenu-open');

				resetMobileSubmenus();

				if (!isOpen) {
					item.classList.add('is-submenu-open');
					toggle.setAttribute('aria-expanded', 'true');
					submenu.style.maxHeight = submenu.scrollHeight + 'px';
				}
			});
		});

		var desktopMenuItems = document.querySelectorAll('.site-header__nav .menu-item-has-children');
		var dropdownCloseDelay = 500;

		desktopMenuItems.forEach(function (item) {
			var closeTimer = null;

			function openDropdown() {
				clearTimeout(closeTimer);
				item.classList.add('is-dropdown-open');
			}

			function scheduleClose() {
				clearTimeout(closeTimer);
				closeTimer = setTimeout(function () {
					item.classList.remove('is-dropdown-open');
				}, dropdownCloseDelay);
			}

			item.addEventListener('mouseenter', openDropdown);
			item.addEventListener('mouseleave', scheduleClose);
			item.addEventListener('focusin', openDropdown);
			item.addEventListener('focusout', function (event) {
				if (!item.contains(event.relatedTarget)) {
					scheduleClose();
				}
			});
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

	function initTeam() {
		var sections = document.querySelectorAll('[data-team]');

		if (!sections.length) {
			return;
		}

		function revealSection(section) {
			section.classList.add('is-visible');

			var sliderEl = section.querySelector('.team-section__slider');

			if (!sliderEl || sliderEl.dataset.swiperInit === 'true') {
				return;
			}

			if (typeof window.Swiper === 'undefined') {
				return;
			}

			sliderEl.dataset.swiperInit = 'true';

			new window.Swiper(sliderEl, {
				slidesPerView: 'auto',
				slidesPerGroup: 1,
				spaceBetween: 50,
				speed: 700,
				watchOverflow: true,
				navigation: {
					nextEl: section.querySelector('.team-section__nav--next'),
					prevEl: section.querySelector('.team-section__nav--prev'),
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
				threshold: 0.12,
				rootMargin: '0px 0px -5% 0px',
			}
		);

		sections.forEach(function (section) {
			observer.observe(section);
		});
	}

	function heroIsInInitialView(hero) {
		var rect = hero.getBoundingClientRect();

		return rect.top < window.innerHeight * 0.92 && rect.bottom > 0;
	}

	function showHero(hero) {
		hero.classList.add('is-visible');
	}

	function initHeroReveal() {
		var heroes = document.querySelectorAll('.hero-section');

		if (!heroes.length) {
			return;
		}

		heroes.forEach(function (hero) {
			if (heroIsInInitialView(hero)) {
				hero.classList.add('hero-section--play-on-load');
				hero.classList.remove('hero-section--await-reveal');
				return;
			}

			hero.classList.remove('hero-section--play-on-load');
		});

		if (!('IntersectionObserver' in window)) {
			heroes.forEach(function (hero) {
				hero.classList.add('hero-section--play-on-load');
				hero.classList.remove('hero-section--await-reveal');
			});
			return;
		}

		var observer = new IntersectionObserver(
			function (entries, obs) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					showHero(entry.target);
					obs.unobserve(entry.target);
				});
			},
			{
				threshold: 0.12,
				rootMargin: '0px',
			}
		);

		heroes.forEach(function (hero) {
			if (hero.classList.contains('hero-section--play-on-load')) {
				return;
			}

			hero.classList.add('hero-section--await-reveal');
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

	function initCategoryList() {
		var sections = document.querySelectorAll('[data-category-list]');

		if (!sections.length) {
			return;
		}

		var stackMQ = window.matchMedia('(min-width: 1025px)');
		var header  = document.querySelector('.site-header');

		function getHeaderH() {
			return header ? header.offsetHeight : 0;
		}

		function markVisible(item) {
			if (!item.classList.contains('is-item-visible')) {
				item.classList.add('is-item-visible');
			}
		}

		// в”Ђв”Ђ rolling-window sticky stacking в”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђ
		//
		//  On each scroll frame the "active" index is recalculated.
		//  Then every item's top is assigned based on delta = activeIdx - i:
		//
		//   delta  0  (active)   в†’  headerH + 2*peek
		//   delta  1  (1-back)   в†’  headerH + 1*peek
		//   delta  2  (2-back)   в†’  headerH
		//   delta в‰Ґ 3 (old)      в†’  в€’itemHeight  (hidden above viewport)
		//   delta < 0 (future)   в†’  headerH + 2*peek  (not sticky yet)
		//
		//  Natural tops are cached once (with sticky removed) so the active
		//  index can be found cheaply on every frame.

		function setupSection(section) {
			if (section._clCleanup) {
				section._clCleanup();
				delete section._clCleanup;
			}

			var items = Array.prototype.slice.call(section.querySelectorAll('[data-category-list-item]'));
			var isMobile = !stackMQ.matches;
				var maxPeeks  = isMobile ? 1 : 2;
			var peek      = isMobile
					? parseInt(section.getAttribute('data-stack-peek-mobile'), 10) || 80
					: parseInt(section.getAttribute('data-stack-peek'), 10) || 150;

				if (items.length <= 1) {
				section.classList.remove('category-list-section--stack-enabled');
				section.classList.remove('category-list-section--no-transition');
				items.forEach(function (item) {
					item.style.top    = '';
					item.style.zIndex = '';
				});
				return;
			}

			section.classList.add('category-list-section--stack-enabled');
			section.classList.add('category-list-section--no-transition');

			// Temporarily remove sticky to read natural document positions
			items.forEach(function (item) {
				item.style.position = 'relative';
				item.style.top      = '0px';
			});
			void section.offsetHeight;

			var headerH = getHeaderH();
			var N       = items.length;
			var rafId   = null;

			var naturalTops = items.map(function (item) {
					return item.getBoundingClientRect().top + window.pageYOffset;
				});
				var itemHeights = items.map(function (item) {
					return item.offsetHeight;
				});
				// Restore sticky
				items.forEach(function (item) {
					item.style.position = '';
				});

			function departure(j, scrollY) {
				if (j >= N) { return 0; }
				var vp     = naturalTops[j] - scrollY;
				var arrVp  = headerH + maxPeeks * peek;
				var trigVp = arrVp + peek;
				return Math.max(0, Math.min(1, (trigVp - vp) / peek));
			}

			function updateTops() {
				var scrollY = window.pageYOffset;

				items.forEach(function (item, i) {
					var stickyStart = naturalTops[i] - headerH - maxPeeks * peek;
					var newTop;

					if (scrollY < stickyStart) {
						// Not yet in stacking zone: set top = natural viewport position so
						// sticky fires at exactly the natural position — no visual displacement.
						newTop = naturalTops[i] - scrollY;
					} else {
						var dep1 = departure(i + 1, scrollY);
						var dep2 = maxPeeks > 1 ? departure(i + 2, scrollY) : 0;
						newTop = Math.max(
							headerH,
							Math.min(headerH + maxPeeks * peek, headerH + (maxPeeks - dep1 - dep2) * peek)
						);
					}

					item.style.top    = newTop + 'px';
					item.style.zIndex = i + 1;
				});
			}

			updateTops();

			requestAnimationFrame(function () {
				requestAnimationFrame(function () {
					section.classList.remove('category-list-section--no-transition');
				});
			});

			function onScroll() {
				if (rafId) { return; }
				rafId = requestAnimationFrame(function () {
					rafId = null;
					updateTops();
				});
			}

			window.addEventListener('scroll', onScroll, { passive: true });

			section._clCleanup = function () {
				window.removeEventListener('scroll', onScroll);
				if (rafId) {
					cancelAnimationFrame(rafId);
					rafId = null;
				}
			};
		}

		// в”Ђв”Ђ reveal animation via IntersectionObserver в”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђ

		sections.forEach(function (section) {
			var items = section.querySelectorAll('[data-category-list-item]');

			if ('IntersectionObserver' in window) {
				var obs = new IntersectionObserver(
					function (entries, o) {
						entries.forEach(function (e) {
							if (e.isIntersecting) {
								markVisible(e.target);
								o.unobserve(e.target);
							}
						});
					},
					{ rootMargin: '0px 0px -60px 0px', threshold: 0 }
				);
				items.forEach(function (item) { obs.observe(item); });
			} else {
				items.forEach(markVisible);
			}

			setupSection(section);
		});

		// в”Ђв”Ђ recalculate on resize / breakpoint change в”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђв”Ђ

		var resizeTimer;

		function onResize() {
			window.clearTimeout(resizeTimer);
			resizeTimer = window.setTimeout(function () {
				sections.forEach(setupSection);
			}, 200);
		}

		window.addEventListener('resize', onResize, { passive: true });
		window.addEventListener('load', function () {
			sections.forEach(setupSection);
		}, { passive: true });

		if (typeof stackMQ.addEventListener === 'function') {
			stackMQ.addEventListener('change', onResize);
		} else if (typeof stackMQ.addListener === 'function') {
			stackMQ.addListener(onResize);
		}
	}

	function initOurServices() {
		var sections = document.querySelectorAll('.our-services-section');

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
				threshold: 0.12,
				rootMargin: '0px 0px -5% 0px',
			}
		);

		sections.forEach(function (section) {
			observer.observe(section);
		});
	}

	function setupRecentWorkPause(section) {
		if (section._recentWorkSetPaused) {
			return;
		}

		section._recentWorkPauseState = {};

		section._recentWorkSetPaused = function (key, paused) {
			section._recentWorkPauseState[key] = !!paused;
		};

		section._recentWorkIsPaused = function () {
			var state = section._recentWorkPauseState;
			var key;

			for (key in state) {
				if (state[key]) {
					return true;
				}
			}

			return section.classList.contains('is-lightbox-open');
		};
	}

	function initRecentCompare(compareEl, config) {
		config = config || {};
		var section = compareEl.closest(config.sectionSelector || '[data-recent-work]');
		var media = compareEl.querySelector(
			config.mediaSelector || '.recent-work-section__compare-media'
		);
		var handle = compareEl.querySelector(
			config.handleSelector || '[data-recent-work-handle]'
		);
		var beforeImage = compareEl.querySelector(
			config.beforeSelector || '.recent-work-section__compare-image--before'
		);

		if (!media || !handle) {
			return;
		}

		var min = 0;
		var max = 100;
		var isDragging = false;

		function syncBeforeWidth() {
			if (!beforeImage) {
				return;
			}

			beforeImage.style.width = media.offsetWidth + 'px';
		}

		function setPosition(percent) {
			var value = Math.min(max, Math.max(min, percent));
			compareEl.style.setProperty('--compare-position', value + '%');
		}

		function positionFromClientX(clientX) {
			var rect = media.getBoundingClientRect();

			if (!rect.width) {
				return;
			}

			setPosition(((clientX - rect.left) / rect.width) * 100);
		}

		function onPointerMove(event) {
			if (!isDragging) {
				return;
			}

			var point = event.touches ? event.touches[0] : event;

			if (!point) {
				return;
			}

			positionFromClientX(point.clientX);
		}

		function stopDragging() {
			isDragging = false;

			if (section && section._recentWorkSetPaused) {
				section._recentWorkSetPaused('compare', false);
			}

			document.removeEventListener('mousemove', onPointerMove);
			document.removeEventListener('mouseup', stopDragging);
			document.removeEventListener('touchmove', onPointerMove);
			document.removeEventListener('touchend', stopDragging);
		}

		function startDragging(event) {
			isDragging = true;

			if (section && section._recentWorkSetPaused) {
				section._recentWorkSetPaused('compare', true);
			}

			event.preventDefault();
			event.stopPropagation();

			var point = event.touches ? event.touches[0] : event;

			if (point) {
				positionFromClientX(point.clientX);
			}

			document.addEventListener('mousemove', onPointerMove);
			document.addEventListener('mouseup', stopDragging);
			document.addEventListener('touchmove', onPointerMove, { passive: false });
			document.addEventListener('touchend', stopDragging);
		}

		handle.addEventListener('click', function (event) {
			event.stopPropagation();
		});

		handle.addEventListener('mousedown', startDragging);
		handle.addEventListener('touchstart', startDragging, { passive: false });

		media.addEventListener(
			'touchstart',
			function (event) {
				if (event.target.closest(config.handleSelector || '[data-recent-work-handle]')) {
					event.stopPropagation();
				}
			},
			{ passive: false }
		);

		syncBeforeWidth();
		window.addEventListener('resize', syncBeforeWidth);
		compareEl._recentCompareSyncBeforeWidth = syncBeforeWidth;
	}

	function initRecentWorkCompare(compareEl) {
		initRecentCompare(compareEl);
	}

	function getRecentWorkSlideMetrics(swiper) {
		var slide = swiper.slides[0];
		var width = slide ? slide.offsetWidth : 0;
		var gap = swiper.params.spaceBetween || 0;
		var perView = 1;

		if (width > 0 && swiper.width > 0) {
			perView = (swiper.width + gap) / (width + gap);
		}

		return {
			width: width,
			gap: gap,
			perView: perView,
		};
	}

	function getRecentWorkSlidesPerView(swiper, slideCount) {
		var metrics = getRecentWorkSlideMetrics(swiper);

		return Math.min(slideCount, Math.max(1, metrics.perView));
	}

	function getRecentWorkMaxIndex(swiper, slideCount) {
		if (swiper.snapGrid && swiper.snapGrid.length > 0) {
			return Math.max(0, swiper.snapGrid.length - 1);
		}

		var perView = getRecentWorkSlidesPerView(swiper, slideCount);

		return Math.max(0, Math.ceil(slideCount - perView));
	}

	function bindRecentWorkPagination(section, swiper, track, fillEl, slideCount) {
		if (!track || !swiper || slideCount < 1) {
			return;
		}

		var isDragging = false;

		function slideIndexFromClientX(clientX) {
			var rect = track.getBoundingClientRect();
			var maxIndex = getRecentWorkMaxIndex(swiper, slideCount);

			if (!rect.width) {
				return 0;
			}

			var ratio = (clientX - rect.left) / rect.width;

			return Math.max(0, Math.min(maxIndex, Math.round(ratio * maxIndex)));
		}

		function updateProgressUI(index) {
			if (!fillEl) {
				return;
			}

			var perView = getRecentWorkSlidesPerView(swiper, slideCount);
			var maxIndex = getRecentWorkMaxIndex(swiper, slideCount);
			var segment = (perView / slideCount) * 100;
			var progressRatio = maxIndex > 0 ? index / maxIndex : 0;
			var offset = progressRatio * (100 - segment);

			fillEl.style.width = segment + '%';
			fillEl.style.transform = 'none';
			fillEl.style.left = offset + '%';
			track.setAttribute('aria-valuenow', String(index + 1));
			track.setAttribute(
				'aria-valuemax',
				String(Math.max(1, maxIndex + 1))
			);
		}

		function goToIndex(index) {
			var maxIndex = getRecentWorkMaxIndex(swiper, slideCount);
			var target = Math.max(0, Math.min(maxIndex, index));

			swiper.slideTo(target);
			updateProgressUI(target);
		}

		function onTrackPointer(clientX) {
			goToIndex(slideIndexFromClientX(clientX));
		}

		function stopTrackDrag() {
			isDragging = false;
			document.removeEventListener('mousemove', onTrackMove);
			document.removeEventListener('mouseup', stopTrackDrag);
			document.removeEventListener('touchmove', onTrackTouchMove);
			document.removeEventListener('touchend', stopTrackDrag);
		}

		function onTrackMove(event) {
			if (!isDragging) {
				return;
			}

			onTrackPointer(event.clientX);
		}

		function onTrackTouchMove(event) {
			if (!isDragging || !event.touches.length) {
				return;
			}

			event.preventDefault();
			onTrackPointer(event.touches[0].clientX);
		}

		function startTrackDrag(event) {
			isDragging = true;
			event.preventDefault();

			var point = event.touches ? event.touches[0] : event;

			if (point) {
				onTrackPointer(point.clientX);
			}

			document.addEventListener('mousemove', onTrackMove);
			document.addEventListener('mouseup', stopTrackDrag);
			document.addEventListener('touchmove', onTrackTouchMove, { passive: false });
			document.addEventListener('touchend', stopTrackDrag);
		}

		track.addEventListener('mousedown', startTrackDrag);
		track.addEventListener('touchstart', startTrackDrag, { passive: false });

		track.addEventListener('keydown', function (event) {
			var index = swiper.activeIndex;
			var maxIndex = getRecentWorkMaxIndex(swiper, slideCount);

			if (event.key === 'ArrowRight') {
				event.preventDefault();
				goToIndex(Math.min(maxIndex, index + 1));
			} else if (event.key === 'ArrowLeft') {
				event.preventDefault();
				goToIndex(Math.max(0, index - 1));
			}
		});

		swiper.on('slideChange', function () {
			updateProgressUI(swiper.activeIndex);
		});

		swiper.on('breakpoint', function () {
			var maxIndex = getRecentWorkMaxIndex(swiper, slideCount);

			if (swiper.activeIndex > maxIndex) {
				swiper.slideTo(maxIndex);
			}

			updateProgressUI(swiper.activeIndex);
		});

		swiper.on('resize', function () {
			updateProgressUI(swiper.activeIndex);
		});

		updateProgressUI(swiper.activeIndex);
	}

	function initRecentGalleryLightbox(section, config) {
		config = config || {};
		var lightbox = section.querySelector(
			config.lightboxSelector || '[data-recent-work-lightbox]'
		);

		if (!lightbox || lightbox.dataset.lightboxInit === 'true') {
			return;
		}

		lightbox.dataset.lightboxInit = 'true';

		if (lightbox.parentNode !== document.body) {
			document.body.appendChild(lightbox);
		}

		var cards = Array.prototype.slice.call(
			section.querySelectorAll(config.cardSelector || '[data-recent-work-card]')
		);
		var imageEl = lightbox.querySelector('.recent-work-lightbox__image');
		var prevBtn = lightbox.querySelector(
			config.prevSelector || '[data-recent-work-lightbox-prev]'
		);
		var nextBtn = lightbox.querySelector(
			config.nextSelector || '[data-recent-work-lightbox-next]'
		);
		var closeTriggers = lightbox.querySelectorAll(
			config.closeSelector || '[data-recent-work-lightbox-close]'
		);
		var cardTextSelector =
			config.cardTextSelector || '.recent-work-section__card-text';
		var handleSelector = config.handleSelector || '[data-recent-work-handle]';
		var slides = [];
		var currentIndex = 0;
		var lastFocused = null;

		cards.forEach(function (card) {
			var afterImage = card.getAttribute('data-after-image');

			if (!afterImage) {
				return;
			}

			var textEl = card.querySelector(cardTextSelector);

			slides.push({
				after: afterImage,
				alt: textEl ? textEl.textContent.trim() : '',
			});
		});

		if (!slides.length || !imageEl) {
			return;
		}

		function updateNavState() {
			var disabled = slides.length <= 1;

			if (prevBtn) {
				prevBtn.disabled = disabled;
			}

			if (nextBtn) {
				nextBtn.disabled = disabled;
			}
		}

		function showSlide(index) {
			currentIndex = (index + slides.length) % slides.length;
			imageEl.src = slides[currentIndex].after;
			imageEl.alt = slides[currentIndex].alt;
			updateNavState();
		}

		function openLightbox(index) {
			lastFocused = document.activeElement;
			showSlide(index);
			section.classList.add('is-lightbox-open');
			lightbox.classList.add('is-open');
			lightbox.setAttribute('aria-hidden', 'false');
			document.body.classList.add('is-recent-work-lightbox-open');

			if (prevBtn) {
				prevBtn.focus();
			}
		}

		function closeLightbox() {
			section.classList.remove('is-lightbox-open');
			lightbox.classList.remove('is-open');
			lightbox.setAttribute('aria-hidden', 'true');
			document.body.classList.remove('is-recent-work-lightbox-open');

			if (lastFocused && typeof lastFocused.focus === 'function') {
				lastFocused.focus();
			}
		}

		cards.forEach(function (card, cardIndex) {
			card.addEventListener('click', function (event) {
				if (event.target.closest(handleSelector)) {
					return;
				}

				var slideIndex = parseInt(card.getAttribute('data-slide-index'), 10);

				if (Number.isNaN(slideIndex)) {
					slideIndex = cardIndex;
				}

				openLightbox(slideIndex);
			});

			card.addEventListener('keydown', function (event) {
				if (event.target.closest(handleSelector)) {
					return;
				}

				if (event.key === 'Enter' || event.key === ' ') {
					event.preventDefault();

					var slideIndex = parseInt(card.getAttribute('data-slide-index'), 10);

					if (Number.isNaN(slideIndex)) {
						slideIndex = cardIndex;
					}

					openLightbox(slideIndex);
				}
			});
		});

		if (prevBtn) {
			prevBtn.addEventListener('click', function () {
				showSlide(currentIndex - 1);
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function () {
				showSlide(currentIndex + 1);
			});
		}

		closeTriggers.forEach(function (trigger) {
			trigger.addEventListener('click', closeLightbox);
		});

		lightbox.addEventListener('click', function (event) {
			if (!lightbox.classList.contains('is-open')) {
				return;
			}

			if (
				event.target.closest('.recent-work-lightbox__image') ||
				event.target.closest(config.prevSelector || '[data-recent-work-lightbox-prev]') ||
				event.target.closest(config.nextSelector || '[data-recent-work-lightbox-next]')
			) {
				return;
			}

			closeLightbox();
		});

		if (prevBtn) {
			prevBtn.addEventListener('click', function (event) {
				event.stopPropagation();
			});
		}

		if (nextBtn) {
			nextBtn.addEventListener('click', function (event) {
				event.stopPropagation();
			});
		}

		document.addEventListener('keydown', function (event) {
			if (!lightbox.classList.contains('is-open')) {
				return;
			}

			if (event.key === 'Escape') {
				closeLightbox();
			} else if (event.key === 'ArrowLeft') {
				event.preventDefault();
				showSlide(currentIndex - 1);
			} else if (event.key === 'ArrowRight') {
				event.preventDefault();
				showSlide(currentIndex + 1);
			}
		});
	}

	function initRecentWorkLightbox(section) {
		initRecentGalleryLightbox(section);
	}

	function initWhatWeDo() {
		var sections = document.querySelectorAll('[data-what-we-do]');

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

	function initWhereWeWork() {
		var sections = document.querySelectorAll('[data-where-we-work]');

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
				threshold: 0.12,
				rootMargin: '0px 0px -5% 0px',
			}
		);

		sections.forEach(function (section) {
			observer.observe(section);
		});
	}

	function initOurStory() {
		var sections = document.querySelectorAll('[data-our-story]');

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

	function initPortfolioBaner() {
		var sections = document.querySelectorAll('[data-portfolio-baner]');

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

	function initRecentWork() {
		var sections = document.querySelectorAll('[data-recent-work]');

		if (!sections.length) {
			return;
		}

		function initSlider(section) {
			var sliderEl = section.querySelector('.recent-work-section__slider');
			var progressFill = section.querySelector('[data-recent-work-progress]');
			var progressTrack = section.querySelector('[data-recent-work-pagination]');
			var slideCount = parseInt(section.getAttribute('data-slide-count'), 10) || 0;

			if (!sliderEl || sliderEl.dataset.swiperInit === 'true') {
				return;
			}

			if (typeof window.Swiper === 'undefined') {
				return;
			}

			sliderEl.dataset.swiperInit = 'true';

			setupRecentWorkPause(section);

			var direction = 1;
			var autoplayTimer = null;
			var sliderArea = section.querySelector('.recent-work-section__slider-area');

			var mobileMediaQuery = window.matchMedia(
				'(max-width: ' + (1024) + 'px)'
			);

			function syncRecentWorkMobileSlideWidths() {
				if (mobileMediaQuery.matches) {
					var slideWidth = sliderEl.clientWidth;

					if (slideWidth) {
						swiper.slides.forEach(function (slide) {
							slide.style.width = slideWidth + 'px';
						});
					}
				} else {
					swiper.slides.forEach(function (slide) {
						slide.style.width = '';
					});
				}

				swiper.update();

				section.querySelectorAll('[data-recent-work-compare]').forEach(function (compareEl) {
					if (typeof compareEl._recentCompareSyncBeforeWidth === 'function') {
						compareEl._recentCompareSyncBeforeWidth();
					}
				});
			}

			var swiper = new window.Swiper(sliderEl, {
				slidesPerView: 'auto',
				slidesPerGroup: 1,
				spaceBetween: 51,
				speed: 700,
				watchOverflow: true,
				allowTouchMove: false,
				simulateTouch: false,
				touchRatio: 0,
				shortSwipes: false,
				longSwipes: false,
				noSwiping: true,
				preventClicks: false,
				preventClicksPropagation: false,
				roundLengths: true,
				breakpoints: {
					0: {
						slidesPerView: 1,
						spaceBetween: 0,
					},
					1025: {
						slidesPerView: 'auto',
						spaceBetween: 51,
					},
				},
			});

			swiper.on('resize', syncRecentWorkMobileSlideWidths);
			swiper.on('init', syncRecentWorkMobileSlideWidths);
			window.requestAnimationFrame(syncRecentWorkMobileSlideWidths);
			window.setTimeout(syncRecentWorkMobileSlideWidths, 150);

			if (typeof mobileMediaQuery.addEventListener === 'function') {
				mobileMediaQuery.addEventListener('change', syncRecentWorkMobileSlideWidths);
			}

			bindRecentWorkPagination(section, swiper, progressTrack, progressFill, slideCount);

			if (sliderArea) {
				sliderArea.addEventListener('mouseenter', function () {
					section._recentWorkSetPaused('hover', true);
				});

				sliderArea.addEventListener('mouseleave', function () {
					section._recentWorkSetPaused('hover', false);
				});

				sliderArea.addEventListener('focusin', function () {
					section._recentWorkSetPaused('hover', true);
				});

				sliderArea.addEventListener('focusout', function (event) {
					if (!sliderArea.contains(event.relatedTarget)) {
						section._recentWorkSetPaused('hover', false);
					}
				});
			}

			function autoplayTick() {
				var maxIndex = getRecentWorkMaxIndex(swiper, slideCount);

				if (section._recentWorkIsPaused() || maxIndex < 1) {
					return;
				}

				if (direction > 0) {
					if (swiper.activeIndex >= maxIndex) {
						direction = -1;
						swiper.slidePrev();
					} else {
						swiper.slideNext();
					}
				} else if (swiper.activeIndex <= 0) {
					direction = 1;
					swiper.slideNext();
				} else {
					swiper.slidePrev();
				}
			}

			autoplayTimer = window.setInterval(autoplayTick, 4000);

			section._recentWorkAutoplayTimer = autoplayTimer;
		}

		function revealSection(section) {
			section.classList.add('is-visible');

			setupRecentWorkPause(section);

			section.querySelectorAll('[data-recent-work-compare]').forEach(initRecentWorkCompare);
			initSlider(section);
			initRecentWorkLightbox(section);
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
				threshold: 0.12,
				rootMargin: '0px 0px -5% 0px',
			}
		);

		sections.forEach(function (section) {
			observer.observe(section);
		});
	}

	var sharedReviewCityCache = '';

	function getSharedReviewServiceAreaCities(config) {
		if (config && config.serviceAreaCities && config.serviceAreaCities.length) {
			return config.serviceAreaCities;
		}

		return ['Raleigh', 'Durham', 'Cary', 'Chapel Hill', 'Apex'];
	}

	function getSharedReviewDefaultServiceCity(config) {
		if (config && config.defaultServiceAreaCity) {
			return config.defaultServiceAreaCity;
		}

		return 'Raleigh';
	}

	function resolveSharedReviewCity(city, config) {
		var normalizedCity = typeof city === 'string' ? city.trim() : '';
		var serviceAreaCities = getSharedReviewServiceAreaCities(config);
		var defaultCity = getSharedReviewDefaultServiceCity(config);
		var cityLower;
		var i;
		var serviceCity;
		var serviceLower;
		var regionMap = {
			wake: 'Raleigh',
			'wake county': 'Raleigh',
			durham: 'Durham',
			orange: 'Chapel Hill',
			'orange county': 'Chapel Hill',
			chatham: 'Apex',
			johnston: 'Raleigh',
		};
		var regionKey;

		if (!normalizedCity) {
			return defaultCity;
		}

		cityLower = normalizedCity.toLowerCase();

		for (i = 0; i < serviceAreaCities.length; i++) {
			serviceCity = serviceAreaCities[i];
			serviceLower = serviceCity.toLowerCase();

			if (cityLower === serviceLower || cityLower.indexOf(serviceLower) !== -1) {
				return serviceCity;
			}
		}

		for (regionKey in regionMap) {
			if (Object.prototype.hasOwnProperty.call(regionMap, regionKey) && cityLower.indexOf(regionKey) !== -1) {
				return regionMap[regionKey];
			}
		}

		return normalizedCity;
	}

	function fetchJsonWithTimeout(url, ms) {
		if (typeof AbortController !== 'undefined') {
			var controller = new AbortController();
			var timeoutId = setTimeout(function () {
				controller.abort();
			}, ms);

			return fetch(url, { signal: controller.signal }).finally(function () {
				clearTimeout(timeoutId);
			});
		}

		return fetch(url);
	}

	function fetchSharedReviewGeo(url, normalize, ms) {
		return fetchJsonWithTimeout(url, ms || 3500)
			.then(function (response) {
				if (!response.ok) {
					return '';
				}

				return response.json();
			})
			.then(normalize)
			.catch(function () {
				return '';
			});
	}

	function detectSharedReviewCityFromIp(config) {
		if (sharedReviewCityCache) {
			return Promise.resolve(sharedReviewCityCache);
		}

		try {
			var storedCity = sessionStorage.getItem('rhino_shared_review_city');

			if (storedCity) {
				sharedReviewCityCache = storedCity;
				return Promise.resolve(storedCity);
			}
		} catch (storageError) {
			// Ignore private mode / blocked storage.
		}

		return Promise.all([
			fetchSharedReviewGeo('https://ipwho.is/', function (data) {
				if (!data || data.success === false) {
					return '';
				}

				return data.city || data.region || '';
			}),
			fetchSharedReviewGeo('https://get.geojs.io/v1/ip/geo.json', function (data) {
				if (!data) {
					return '';
				}

				return data.city || data.region || '';
			}),
		]).then(function (results) {
			var city = '';

			for (var i = 0; i < results.length; i++) {
				if (results[i]) {
					city = results[i];
					break;
				}
			}

			city = resolveSharedReviewCity(city, config);

			if (city) {
				sharedReviewCityCache = city;

				try {
					sessionStorage.setItem('rhino_shared_review_city', city);
				} catch (storageError) {
					// Ignore private mode / blocked storage.
				}
			}

			return city;
		});
	}

	function ensureSharedReviewCity(cityInput, config) {
		if (cityInput && cityInput.value.trim()) {
			return Promise.resolve(resolveSharedReviewCity(cityInput.value.trim(), config));
		}

		var detection = detectSharedReviewCityFromIp(config).then(function (city) {
			city = resolveSharedReviewCity(city, config);

			if (city && cityInput) {
				cityInput.value = city;
			}

			return city || getSharedReviewDefaultServiceCity(config);
		});

		return Promise.race([
			detection,
			new Promise(function (resolve) {
				setTimeout(function () {
					var fallbackCity = cityInput && cityInput.value ? cityInput.value.trim() : '';

					resolve(resolveSharedReviewCity(fallbackCity, config));
				}, 2500);
			}),
		]);
	}

	function initSharedReview() {
		var sections = document.querySelectorAll('[data-shared-review]');

		if (!sections.length) {
			return;
		}

		var config = window.rhinoSharedReview || {};
		var modal =
			document.getElementById('shared-review-success-modal') ||
			document.querySelector('[id="shared-review-success-modal"]');

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

				modal.querySelectorAll('[data-shared-review-modal-close]').forEach(function (trigger) {
					trigger.addEventListener('click', closeModal);
				});

				document.addEventListener('keydown', function (event) {
					if (event.key === 'Escape' && modal.classList.contains('is-open')) {
						closeModal();
					}
				});
			}
		}

		function clearFormErrors(form) {
			form.querySelectorAll('.wpcf7-not-valid').forEach(function (field) {
				field.classList.remove('wpcf7-not-valid');
			});

			form.querySelectorAll('.wpcf7-not-valid-tip').forEach(function (tip) {
				tip.remove();
			});

			var ratingWrap = form.querySelector('[data-shared-review-rating]');

			if (ratingWrap) {
				ratingWrap.classList.remove('is-invalid');
			}
		}

		function setFieldError(field, message) {
			if (!field) {
				return;
			}

			field.classList.add('wpcf7-not-valid');

			var wrap = field.closest('.rhino-cf7-form__field');

			if (!wrap) {
				return;
			}

			var tip = document.createElement('span');

			tip.className = 'wpcf7-not-valid-tip';
			tip.setAttribute('role', 'alert');
			tip.textContent = message;
			wrap.appendChild(tip);
		}

		function setRatingError(ratingWrap, message) {
			if (!ratingWrap) {
				return;
			}

			var ratingInput = ratingWrap.querySelector('input[name="rating"]');

			if (ratingInput) {
				ratingInput.classList.add('wpcf7-not-valid');
			}

			ratingWrap.classList.add('is-invalid');

			var tip = document.createElement('span');

			tip.className = 'wpcf7-not-valid-tip';
			tip.setAttribute('role', 'alert');
			tip.textContent = message;
			ratingWrap.appendChild(tip);
		}

		function isValidEmail(value) {
			if (typeof value !== 'string' || !value.trim()) {
				return false;
			}

			if (typeof HTMLInputElement !== 'undefined') {
				var probe = document.createElement('input');

				probe.type = 'email';
				probe.required = true;
				probe.value = value.trim();

				return probe.checkValidity();
			}

			return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim());
		}

		function setStarState(ratingWrap, value, previewValue) {
			var emptyUrl = ratingWrap.getAttribute('data-star-empty');
			var filledUrl = ratingWrap.getAttribute('data-star-filled');
			var activeValue = typeof previewValue === 'number' ? previewValue : value;

			ratingWrap.querySelectorAll('[data-star-value]').forEach(function (button) {
				var starValue = parseInt(button.getAttribute('data-star-value'), 10);
				var icon = button.querySelector('.shared-review-section__star-icon');
				var isFilled = starValue <= activeValue && activeValue > 0;
				var isSelected = starValue <= value && value > 0;

				button.classList.toggle('is-preview', typeof previewValue === 'number' && previewValue > 0);
				button.classList.toggle('is-filled', isSelected);

				if (!icon) {
					return;
				}

				icon.src = isFilled ? filledUrl : emptyUrl;
			});
		}

		function bindRating(ratingWrap) {
			if (!ratingWrap || ratingWrap.getAttribute('data-rating-bound') === '1') {
				return;
			}

			ratingWrap.setAttribute('data-rating-bound', '1');

			var ratingInput = ratingWrap.querySelector('input[name="rating"]');
			var currentValue = 0;

			function getValue() {
				return parseInt(ratingInput && ratingInput.value ? ratingInput.value : '0', 10) || 0;
			}

			ratingWrap.querySelectorAll('[data-star-value]').forEach(function (button) {
				button.addEventListener('mouseenter', function () {
					var previewValue = parseInt(button.getAttribute('data-star-value'), 10);
					setStarState(ratingWrap, getValue(), previewValue);
				});

				button.addEventListener('focus', function () {
					var previewValue = parseInt(button.getAttribute('data-star-value'), 10);
					setStarState(ratingWrap, getValue(), previewValue);
				});

				button.addEventListener('click', function () {
					currentValue = parseInt(button.getAttribute('data-star-value'), 10);

					if (ratingInput) {
						ratingInput.value = String(currentValue);
					}

					setStarState(ratingWrap, currentValue);
				});
			});

			ratingWrap.addEventListener('mouseleave', function () {
				setStarState(ratingWrap, getValue());
			});

			ratingWrap.addEventListener('focusout', function (event) {
				if (!ratingWrap.contains(event.relatedTarget)) {
					setStarState(ratingWrap, getValue());
				}
			});
		}

		function bindForm(form) {
			if (!form || form.getAttribute('data-shared-review-bound') === '1') {
				return;
			}

			form.setAttribute('data-shared-review-bound', '1');

			var ratingWrap = form.querySelector('[data-shared-review-rating]');
			var cityInput = form.querySelector('[data-shared-review-city]');
			var nonceInput = form.querySelector('[data-shared-review-nonce]');
			var submitButton = form.querySelector('[type="submit"]');
			var submitText = submitButton ? submitButton.querySelector('.shared-review-section__submit-text') : null;
			var defaultSubmitLabel = submitText ? submitText.textContent : '';

			if (nonceInput && config.nonce) {
				nonceInput.value = config.nonce;
			}

			if (cityInput) {
				detectSharedReviewCityFromIp(config).then(function (city) {
					if (city) {
						cityInput.value = city;
					}
				});
			}

			bindRating(ratingWrap);

			form.querySelectorAll('.rhino-cf7-form__control').forEach(function (field) {
				field.addEventListener('input', function () {
					field.classList.remove('wpcf7-not-valid');

					var wrap = field.closest('.rhino-cf7-form__field');

					if (wrap) {
						var tip = wrap.querySelector('.wpcf7-not-valid-tip');

						if (tip) {
							tip.remove();
						}
					}
				});
			});

			if (ratingWrap) {
				ratingWrap.querySelectorAll('[data-star-value]').forEach(function (button) {
					button.addEventListener('click', function () {
						ratingWrap.classList.remove('is-invalid');

						var ratingInput = ratingWrap.querySelector('input[name="rating"]');

						if (ratingInput) {
							ratingInput.classList.remove('wpcf7-not-valid');
						}

						var tip = ratingWrap.querySelector('.wpcf7-not-valid-tip');

						if (tip) {
							tip.remove();
						}
					});
				});
			}

			form.addEventListener('submit', function (event) {
				event.preventDefault();

				var fullName = form.querySelector('[name="full_name"]');
				var email = form.querySelector('[name="email"]');
				var message = form.querySelector('[name="message"]');
				var rating = form.querySelector('[name="rating"]');
				var i18n = config.i18n || {};
				var hasErrors = false;

				clearFormErrors(form);

				if (!fullName || !fullName.value.trim()) {
					setFieldError(fullName, i18n.requiredField || 'This field is required.');
					hasErrors = true;
				}

				if (!email || !email.value.trim()) {
					setFieldError(email, i18n.requiredField || 'This field is required.');
					hasErrors = true;
				} else if (!isValidEmail(email.value)) {
					setFieldError(email, i18n.invalidEmail || 'Please enter a valid email address.');
					hasErrors = true;
				}

				if (!message || !message.value.trim()) {
					setFieldError(message, i18n.requiredField || 'This field is required.');
					hasErrors = true;
				}

				if (!rating || !rating.value) {
					setRatingError(
						ratingWrap,
						i18n.invalidRating || 'Please select a star rating.'
					);
					hasErrors = true;
				}

				if (hasErrors) {
					var firstInvalid = form.querySelector('.wpcf7-not-valid, .shared-review-section__rating.is-invalid');

					if (firstInvalid && typeof firstInvalid.focus === 'function') {
						firstInvalid.focus();
					}

					return;
				}

				if (!config.ajaxUrl || !config.action) {
					setFieldError(message, i18n.error || 'Something went wrong. Please try again.');
					return;
				}

				if (submitButton) {
					submitButton.disabled = true;
				}

				if (submitText) {
					submitText.textContent = i18n.sending || 'Sending…';
				}

				ensureSharedReviewCity(cityInput, config)
					.then(function (city) {
						var formData = new FormData(form);

						formData.set('action', config.action);

						if (config.nonce) {
							formData.set('nonce', config.nonce);
						}

						if (city) {
							formData.set('city', city);
						}

						return fetch(config.ajaxUrl, {
							method: 'POST',
							body: formData,
							credentials: 'same-origin',
						});
					})
					.then(function (response) {
						return response.json();
					})
					.then(function (data) {
						if (data && data.success) {
							form.reset();
							clearFormErrors(form);

							if (rating) {
								rating.value = '';
							}

							if (ratingWrap) {
								setStarState(ratingWrap, 0);
							}

							if (nonceInput && config.nonce) {
								nonceInput.value = config.nonce;
							}

							if (cityInput && sharedReviewCityCache) {
								cityInput.value = sharedReviewCityCache;
							}

							openModal();
							return;
						}

						var errorMessage =
							(data && data.data && data.data.message) ||
							i18n.error ||
							'Something went wrong. Please try again.';

						setFieldError(message, errorMessage);
					})
					.catch(function () {
						setFieldError(message, i18n.error || 'Something went wrong. Please try again.');
					})
					.finally(function () {
						if (submitButton) {
							submitButton.disabled = false;
						}

						if (submitText) {
							submitText.textContent = defaultSubmitLabel;
						}
					});
			});
		}

		sections.forEach(function (section) {
			var form = section.querySelector('[data-shared-review-form]');

			bindForm(form);
		});

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
				threshold: 0.12,
				rootMargin: '0px 0px -5% 0px',
			}
		);

		sections.forEach(function (section) {
			observer.observe(section);
		});
	}

	function initCategoryReview() {
		var sections = document.querySelectorAll('[data-category-review]');

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
				threshold: 0.12,
				rootMargin: '0px 0px -5% 0px',
			}
		);

		sections.forEach(function (section) {
			observer.observe(section);
		});
	}

	function initRecentCategoryWork() {
		var sections = document.querySelectorAll('[data-recent-category-work]');

		if (!sections.length) {
			return;
		}

		var compareConfig = {
			sectionSelector: '[data-recent-category-work]',
			mediaSelector: '.recent-category-work-section__compare-media',
			handleSelector: '[data-recent-category-work-handle]',
			beforeSelector: '.recent-category-work-section__compare-image--before',
		};

		var lightboxConfig = {
			lightboxSelector: '[data-recent-category-work-lightbox]',
			cardSelector: '[data-recent-category-work-card]',
			prevSelector: '[data-recent-category-work-lightbox-prev]',
			nextSelector: '[data-recent-category-work-lightbox-next]',
			closeSelector: '[data-recent-category-work-lightbox-close]',
			cardTextSelector: '.recent-category-work-section__card-text',
			handleSelector: '[data-recent-category-work-handle]',
		};

		var desktopMediaQuery = window.matchMedia('(min-width: 1025px)');

		function syncCategoryCompareWidths(section) {
			section.querySelectorAll('[data-recent-category-work-compare]').forEach(function (compareEl) {
				if (typeof compareEl._recentCompareSyncBeforeWidth === 'function') {
					compareEl._recentCompareSyncBeforeWidth();
				}
			});
		}

		function syncCategoryMobileSlideWidths(section) {
			var mobileSliderEl = section.querySelector('[data-category-work-slider="mobile"]');
			var mobileSwiper = section._categoryWorkSwiperMobile;

			if (
				!mobileSliderEl ||
				!mobileSwiper ||
				desktopMediaQuery.matches
			) {
				return;
			}

			var slideWidth = mobileSliderEl.clientWidth;

			if (!slideWidth) {
				return;
			}

			mobileSwiper.slides.forEach(function (slide) {
				slide.style.width = slideWidth + 'px';
			});

			mobileSwiper.update();
		}

		function getActiveCategoryWorkSwiper(section) {
			if (desktopMediaQuery.matches) {
				return section._categoryWorkSwiperDesktop || null;
			}

			return section._categoryWorkSwiperMobile || null;
		}

		function createCategoryWorkSwiper(section, sliderEl, mode) {
			var isDesktop = mode === 'desktop';
			var slideCount = isDesktop
				? parseInt(section.getAttribute('data-page-count'), 10) || 0
				: parseInt(section.getAttribute('data-slide-count'), 10) || 0;

			return new window.Swiper(sliderEl, {
				slidesPerView: 1,
				slidesPerGroup: 1,
				spaceBetween: 0,
				speed: 700,
				watchOverflow: false,
				allowTouchMove: false,
				simulateTouch: false,
				touchRatio: 0,
				shortSwipes: false,
				longSwipes: false,
				noSwiping: true,
				preventClicks: false,
				preventClicksPropagation: false,
				roundLengths: !isDesktop,
			});
		}

		function initSlider(section) {
			var desktopSliderEl = section.querySelector('[data-category-work-slider="desktop"]');
			var mobileSliderEl = section.querySelector('[data-category-work-slider="mobile"]');
			var progressFill = section.querySelector('[data-recent-category-work-progress]');
			var progressTrack = section.querySelector('[data-recent-category-work-pagination]');
			var slideCount = parseInt(section.getAttribute('data-slide-count'), 10) || 0;
			var prevNav = section.querySelector('.recent-category-work-section__nav--prev');
			var nextNav = section.querySelector('.recent-category-work-section__nav--next');
			var sliderArea = section.querySelector('.recent-category-work-section__slider-area');

			if (typeof window.Swiper === 'undefined') {
				return;
			}

			if (
				(!desktopSliderEl || desktopSliderEl.dataset.swiperInit === 'true') &&
				(!mobileSliderEl || mobileSliderEl.dataset.swiperInit === 'true')
			) {
				return;
			}

			setupRecentWorkPause(section);

			if (desktopSliderEl && desktopSliderEl.dataset.swiperInit !== 'true') {
				desktopSliderEl.dataset.swiperInit = 'true';
				section._categoryWorkSwiperDesktop = createCategoryWorkSwiper(
					section,
					desktopSliderEl,
					'desktop'
				);
			}

			if (mobileSliderEl && mobileSliderEl.dataset.swiperInit !== 'true') {
				mobileSliderEl.dataset.swiperInit = 'true';
				section._categoryWorkSwiperMobile = createCategoryWorkSwiper(
					section,
					mobileSliderEl,
					'mobile'
				);

				if (progressTrack && progressTrack.dataset.paginationBound !== 'true') {
					progressTrack.dataset.paginationBound = 'true';
					bindRecentWorkPagination(
						section,
						section._categoryWorkSwiperMobile,
						progressTrack,
						progressFill,
						slideCount
					);
				}
			}

			function refreshCategorySwiper() {
				syncCategoryMobileSlideWidths(section);

				var swiper = getActiveCategoryWorkSwiper(section);

				if (!swiper) {
					return;
				}

				swiper.update();
				updateCategoryNavState();
				syncCategoryCompareWidths(section);
			}

			function updateCategoryNavState() {
				var swiper = getActiveCategoryWorkSwiper(section);

				if (!swiper) {
					return;
				}

				var atStart = swiper.isBeginning;
				var atEnd = swiper.isEnd;

				if (prevNav) {
					prevNav.classList.toggle('swiper-button-disabled', atStart);
					prevNav.setAttribute('aria-disabled', atStart ? 'true' : 'false');
				}

				if (nextNav) {
					nextNav.classList.toggle('swiper-button-disabled', atEnd);
					nextNav.setAttribute('aria-disabled', atEnd ? 'true' : 'false');
				}
			}

			function onNavPrev(event) {
				event.preventDefault();
				event.stopPropagation();

				var swiper = getActiveCategoryWorkSwiper(section);

				if (!swiper || swiper.isBeginning) {
					return;
				}

				swiper.slidePrev();
			}

			function onNavNext(event) {
				event.preventDefault();
				event.stopPropagation();

				var swiper = getActiveCategoryWorkSwiper(section);

				if (!swiper || swiper.isEnd) {
					return;
				}

				swiper.slideNext();
			}

			if (prevNav && !prevNav.dataset.categoryNavBound) {
				prevNav.dataset.categoryNavBound = 'true';
				prevNav.addEventListener('click', onNavPrev);
			}

			if (nextNav && !nextNav.dataset.categoryNavBound) {
				nextNav.dataset.categoryNavBound = 'true';
				nextNav.addEventListener('click', onNavNext);
			}

			[section._categoryWorkSwiperDesktop, section._categoryWorkSwiperMobile].forEach(function (swiper) {
				if (!swiper) {
					return;
				}

				swiper.on('slideChange', updateCategoryNavState);
				swiper.on('resize', refreshCategorySwiper);
				swiper.on('init', refreshCategorySwiper);
			});

			if (typeof desktopMediaQuery.addEventListener === 'function') {
				desktopMediaQuery.addEventListener('change', refreshCategorySwiper);
			}

			window.requestAnimationFrame(refreshCategorySwiper);
			window.setTimeout(refreshCategorySwiper, 150);

			if (sliderArea) {
				sliderArea.addEventListener('mouseenter', function () {
					section._recentWorkSetPaused('hover', true);
				});

				sliderArea.addEventListener('mouseleave', function () {
					section._recentWorkSetPaused('hover', false);
				});

				sliderArea.addEventListener('focusin', function () {
					section._recentWorkSetPaused('hover', true);
				});

				sliderArea.addEventListener('focusout', function (event) {
					if (!sliderArea.contains(event.relatedTarget)) {
						section._recentWorkSetPaused('hover', false);
					}
				});
			}
		}

		function revealSection(section) {
			section.classList.add('is-visible');

			setupRecentWorkPause(section);

			initSlider(section);

			window.requestAnimationFrame(function () {
				if (section._categoryWorkSwiperDesktop) {
					section._categoryWorkSwiperDesktop.update();
				}

				if (section._categoryWorkSwiperMobile) {
					section._categoryWorkSwiperMobile.update();
				}
			});

			section.querySelectorAll('[data-recent-category-work-compare]').forEach(function (compareEl) {
				initRecentCompare(compareEl, compareConfig);
			});

			initRecentGalleryLightbox(section, lightboxConfig);
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
				threshold: 0.12,
				rootMargin: '0px 0px -5% 0px',
			}
		);

		sections.forEach(function (section) {
			observer.observe(section);
		});
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

	function getFixedHeaderOffset() {
		var header = document.querySelector('.site-header');

		return header ? header.offsetHeight + 16 : 80;
	}

	function scrollToAnchorTarget(target, behavior) {
		if (!target) {
			return;
		}

		var prefersReduced =
			typeof window.matchMedia === 'function' &&
			window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var scrollBehavior = behavior || (prefersReduced ? 'auto' : 'smooth');
		var top =
			target.getBoundingClientRect().top +
			window.pageYOffset -
			getFixedHeaderOffset();

		window.scrollTo({
			top: Math.max(0, top),
			behavior: scrollBehavior,
		});
	}

	function initSmoothAnchors() {
		document.addEventListener('click', function (event) {
			var link = event.target.closest('a[href*="#"]');

			if (!link) {
				return;
			}

			var url;

			try {
				url = new URL(link.href, window.location.href);
			} catch (parseError) {
				return;
			}

			if (!url.hash || url.hash === '#') {
				return;
			}

			var target = document.querySelector(url.hash);

			if (!target) {
				return;
			}

			var isSamePage =
				url.pathname === window.location.pathname &&
				url.search === window.location.search;

			if (!isSamePage) {
				return;
			}

			event.preventDefault();
			history.pushState(null, '', url.hash);
			scrollToAnchorTarget(target);
		});

		if (!window.location.hash) {
			return;
		}

		var initialTarget = document.querySelector(window.location.hash);

		if (!initialTarget) {
			return;
		}

		window.requestAnimationFrame(function () {
			scrollToAnchorTarget(initialTarget, 'auto');
			window.requestAnimationFrame(function () {
				scrollToAnchorTarget(initialTarget);
			});
		});
	}

	function init() {
		initSmoothAnchors();
		initHeaderMenu();
		initHeroReveal();
		initStats();
		initRunLine();
		initWhyChoose();
		initReviews();
		initProcess();
		initContact();
		initOurServices();
		initCategoryList();
		initWhatWeDo();
		initPortfolioBaner();
		initOurStory();
		initTeam();
		initWhereWeWork();
		initRecentWork();
		initRecentCategoryWork();
		initCategoryReview();
		initSharedReview();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
