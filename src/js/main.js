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
			if (link && !link.classList.contains('site-header__button--mobile')) {
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

	function initRecentWorkCompare(compareEl) {
		var section = compareEl.closest('[data-recent-work]');
		var media = compareEl.querySelector('.recent-work-section__compare-media');
		var handle = compareEl.querySelector('[data-recent-work-handle]');
		var beforeImage = compareEl.querySelector('.recent-work-section__compare-image--before');

		if (!media || !handle) {
			return;
		}

		var min = 10;
		var max = 90;
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
				if (event.target.closest('[data-recent-work-handle]')) {
					event.stopPropagation();
				}
			},
			{ passive: false }
		);

		syncBeforeWidth();
		window.addEventListener('resize', syncBeforeWidth);
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

	function initRecentWorkLightbox(section) {
		var lightbox = section.querySelector('[data-recent-work-lightbox]');

		if (!lightbox || lightbox.dataset.lightboxInit === 'true') {
			return;
		}

		lightbox.dataset.lightboxInit = 'true';

		var cards = Array.prototype.slice.call(section.querySelectorAll('[data-recent-work-card]'));
		var imageEl = lightbox.querySelector('.recent-work-lightbox__image');
		var prevBtn = lightbox.querySelector('[data-recent-work-lightbox-prev]');
		var nextBtn = lightbox.querySelector('[data-recent-work-lightbox-next]');
		var closeTriggers = lightbox.querySelectorAll('[data-recent-work-lightbox-close]');
		var slides = [];
		var currentIndex = 0;
		var lastFocused = null;

		cards.forEach(function (card) {
			var afterImage = card.getAttribute('data-after-image');

			if (!afterImage) {
				return;
			}

			var textEl = card.querySelector('.recent-work-section__card-text');

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
				if (event.target.closest('[data-recent-work-handle]')) {
					return;
				}

				var slideIndex = parseInt(card.getAttribute('data-slide-index'), 10);

				if (Number.isNaN(slideIndex)) {
					slideIndex = cardIndex;
				}

				openLightbox(slideIndex);
			});

			card.addEventListener('keydown', function (event) {
				if (event.target.closest('[data-recent-work-handle]')) {
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
				event.target.closest('[data-recent-work-lightbox-prev]') ||
				event.target.closest('[data-recent-work-lightbox-next]')
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
			});

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
		initOurServices();
		initCategoryList();
		initWhatWeDo();
		initPortfolioBaner();
		initOurStory();
		initTeam();
		initWhereWeWork();
		initRecentWork();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
