/**
 * hello-immosync — interactions front-end.
 * Namespace global : WPIS.
 */
(function () {
	'use strict';

	var WPIS = (window.WPIS = window.WPIS || {});

	/**
	 * Menu mobile.
	 */
	WPIS.mobileMenu = function () {
		var toggle = document.querySelector('[data-wpis-menu-toggle]');
		var menu = document.querySelector('[data-wpis-mobile-menu]');
		if (!toggle || !menu) {
			return;
		}
		toggle.addEventListener('click', function () {
			var isOpen = !menu.classList.contains('hidden');
			menu.classList.toggle('hidden');
			toggle.setAttribute('aria-expanded', String(!isOpen));
		});
	};

	/**
	 * Galerie de la fiche bien : mosaïque + modal plein écran (Swiper).
	 * Chaque bloc [data-wpis-gallery] contient ses déclencheurs
	 * [data-wpis-gallery-open] (data-index) et sa modal [data-wpis-gallery-modal].
	 * Swiper est initialisé à la première ouverture (lazy) pour un mesurage correct.
	 */
	WPIS.gallery = function () {
		if (typeof window.Swiper === 'undefined') {
			return;
		}

		var roots = document.querySelectorAll('[data-wpis-gallery]');
		Array.prototype.forEach.call(roots, function (root) {
			var modal = root.querySelector('[data-wpis-gallery-modal]');
			var swiperEl = root.querySelector('[data-wpis-swiper]');
			var triggers = root.querySelectorAll('[data-wpis-gallery-open]');
			if (!modal || !swiperEl || !triggers.length) {
				return;
			}

			var swiper = null;

			function open(index) {
				modal.classList.remove('hidden');
				document.body.style.overflow = 'hidden';

				if (!swiper) {
					swiper = new window.Swiper(swiperEl, {
						loop: true,
						slidesPerView: 1,
						spaceBetween: 24,
						keyboard: { enabled: true },
						navigation: {
							nextEl: swiperEl.querySelector('.swiper-button-next'),
							prevEl: swiperEl.querySelector('.swiper-button-prev')
						},
						pagination: {
							el: swiperEl.querySelector('.swiper-pagination'),
							type: 'fraction'
						}
					});
				}
				swiper.update();
				swiper.slideToLoop(index, 0);
			}

			function close() {
				modal.classList.add('hidden');
				document.body.style.overflow = '';
			}

			Array.prototype.forEach.call(triggers, function (el) {
				el.addEventListener('click', function (e) {
					e.preventDefault();
					open(parseInt(el.getAttribute('data-index'), 10) || 0);
				});
			});

			var closeBtn = modal.querySelector('[data-wpis-gallery-close]');
			if (closeBtn) {
				closeBtn.addEventListener('click', close);
			}
			modal.addEventListener('click', function (e) {
				// Clic en dehors de l'image (zone sombre / slide) = fermeture.
				if (e.target === modal || e.target.classList.contains('swiper-slide')) {
					close();
				}
			});
			document.addEventListener('keydown', function (e) {
				if (!modal.classList.contains('hidden') && e.key === 'Escape') {
					close();
				}
			});
		});
	};

	/**
	 * Lightbox d'embed (vidéo / visite 360°) de la fiche bien.
	 * Les déclencheurs [data-wpis-embed-open="ID"] clonent le gabarit
	 * <template data-wpis-embed-tpl="ID"> dans la scène de la modal partagée
	 * [data-wpis-embed-modal]. L'iframe n'est chargée qu'à l'ouverture (template
	 * inerte) et déchargée à la fermeture (arrêt de la lecture).
	 */
	WPIS.embed = function () {
		var modal = document.querySelector('[data-wpis-embed-modal]');
		var openers = document.querySelectorAll('[data-wpis-embed-open]');
		if (!modal || !openers.length) {
			return;
		}
		var stage = modal.querySelector('[data-wpis-embed-stage]');
		if (!stage) {
			return;
		}

		function open(id) {
			var tpl = document.querySelector('[data-wpis-embed-tpl="' + id + '"]');
			if (!tpl || !tpl.content) {
				return;
			}
			stage.innerHTML = '';
			stage.appendChild(tpl.content.cloneNode(true));
			modal.classList.remove('hidden');
			document.body.style.overflow = 'hidden';
		}

		function close() {
			modal.classList.add('hidden');
			stage.innerHTML = ''; // Décharge l'iframe → stoppe la lecture.
			document.body.style.overflow = '';
		}

		Array.prototype.forEach.call(openers, function (el) {
			el.addEventListener('click', function (e) {
				e.preventDefault();
				open(el.getAttribute('data-wpis-embed-open'));
			});
		});

		var closeBtn = modal.querySelector('[data-wpis-embed-close]');
		if (closeBtn) {
			closeBtn.addEventListener('click', close);
		}
		modal.addEventListener('click', function (e) {
			// Clic sur le fond sombre / la scène (hors iframe) = fermeture.
			if (e.target === modal || e.target.hasAttribute('data-wpis-embed-stage')) {
				close();
			}
		});
		document.addEventListener('keydown', function (e) {
			if (!modal.classList.contains('hidden') && e.key === 'Escape') {
				close();
			}
		});
	};

	/**
	 * Titres de section dans les formulaires ImmoSync.
	 * Insère un titre (.wpis-form-section-title) avant certains groupes de champs.
	 * La configuration (cibles + libellés traduits) est fournie par PHP via
	 * window.WPISFormData.sections (wp_localize_script) — multilingue.
	 */
	WPIS.formSections = function () {
		var data = window.WPISFormData;
		if (!data || !Array.isArray(data.sections) || !data.sections.length) {
			return;
		}
		var forms = document.querySelectorAll('.wpis-form');
		Array.prototype.forEach.call(forms, function (form) {
			data.sections.forEach(function (section) {
				if (!section.target || !section.title) {
					return;
				}
				var target = form.querySelector(section.target);
				if (!target) {
					return;
				}
				var prev = target.previousElementSibling;
				if (prev && prev.classList.contains('wpis-form-section-title')) {
					return; // Déjà inséré.
				}
				var title = document.createElement('div');
				title.className = 'wpis-form-section-title';
				title.textContent = section.title;
				target.parentNode.insertBefore(title, target);
			});
		});
	};

	/**
	 * Partage d'un bien (module « share », template-parts/estate/share.php).
	 * Les liens WhatsApp / Facebook fonctionnent seuls ; on ajoute la copie du
	 * lien (presse-papiers, repli execCommand) et la feuille de partage native
	 * du téléphone quand navigator.share existe.
	 */
	WPIS.share = function () {
		var roots = document.querySelectorAll('[data-wpis-share]');
		Array.prototype.forEach.call(roots, function (root) {
			var url = root.getAttribute('data-url');
			var title = root.getAttribute('data-title');
			var status = root.querySelector('[data-wpis-share-status]');
			var timer = null;

			function announce(key) {
				if (!status) {
					return;
				}
				status.textContent = status.getAttribute('data-' + key) || '';
				root.classList.add('is-announcing');
				window.clearTimeout(timer);
				timer = window.setTimeout(function () {
					status.textContent = '';
					root.classList.remove('is-announcing');
				}, 2500);
			}

			function fallbackCopy() {
				var field = document.createElement('textarea');
				field.value = url;
				field.setAttribute('readonly', '');
				field.style.position = 'fixed';
				field.style.opacity = '0';
				document.body.appendChild(field);
				field.select();
				var ok = false;
				try {
					ok = document.execCommand('copy');
				} catch (e) {
					ok = false;
				}
				document.body.removeChild(field);
				return ok;
			}

			var copy = root.querySelector('[data-wpis-share-copy]');
			if (copy) {
				copy.addEventListener('click', function () {
					if (navigator.clipboard && window.isSecureContext) {
						navigator.clipboard.writeText(url).then(
							function () {
								announce('copied');
							},
							function () {
								announce(fallbackCopy() ? 'copied' : 'failed');
							}
						);
					} else {
						announce(fallbackCopy() ? 'copied' : 'failed');
					}
				});
			}

			var native = root.querySelector('[data-wpis-share-native]');
			if (native && navigator.share && window.matchMedia('(pointer: coarse)').matches) {
				native.hidden = false;
				native.addEventListener('click', function () {
					navigator.share({ title: title, url: url }).catch(function () {});
				});
			}
		});
	};

	document.addEventListener('DOMContentLoaded', function () {
		WPIS.mobileMenu();
		WPIS.gallery();
		WPIS.embed();
		WPIS.formSections();
		WPIS.share();
	});
})();
