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
	 * Galerie / lightbox de la fiche bien.
	 * S'appuie sur les éléments [data-wpis-gallery-item] (data-full = URL pleine taille).
	 */
	WPIS.gallery = function () {
		var items = document.querySelectorAll('[data-wpis-gallery-item]');
		if (!items.length) {
			return;
		}

		var overlay = document.createElement('div');
		overlay.className =
			'fixed inset-0 z-[200] hidden items-center justify-center bg-ink/95 p-4';
		overlay.setAttribute('data-wpis-lightbox', '');
		overlay.innerHTML =
			'<button type="button" class="absolute right-6 top-6 text-cream/70 hover:text-cream" data-wpis-lightbox-close aria-label="Fermer">&#10005;</button>' +
			'<button type="button" class="absolute left-4 top-1/2 -translate-y-1/2 px-4 text-3xl text-cream/70 hover:text-cream" data-wpis-lightbox-prev aria-label="Précédent">&#8249;</button>' +
			'<img alt="" class="max-h-[88vh] max-w-[92vw] object-contain" data-wpis-lightbox-img>' +
			'<button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 px-4 text-3xl text-cream/70 hover:text-cream" data-wpis-lightbox-next aria-label="Suivant">&#8250;</button>';
		document.body.appendChild(overlay);

		var img = overlay.querySelector('[data-wpis-lightbox-img]');
		var sources = Array.prototype.map.call(items, function (el) {
			return el.getAttribute('data-full') || el.querySelector('img').src;
		});
		var current = 0;

		function show(index) {
			current = (index + sources.length) % sources.length;
			img.src = sources[current];
		}
		function open(index) {
			show(index);
			overlay.classList.remove('hidden');
			overlay.classList.add('flex');
			document.body.style.overflow = 'hidden';
		}
		function close() {
			overlay.classList.add('hidden');
			overlay.classList.remove('flex');
			document.body.style.overflow = '';
		}

		items.forEach(function (el, i) {
			el.addEventListener('click', function (e) {
				e.preventDefault();
				open(i);
			});
		});
		overlay.querySelector('[data-wpis-lightbox-close]').addEventListener('click', close);
		overlay.querySelector('[data-wpis-lightbox-next]').addEventListener('click', function () {
			show(current + 1);
		});
		overlay.querySelector('[data-wpis-lightbox-prev]').addEventListener('click', function () {
			show(current - 1);
		});
		overlay.addEventListener('click', function (e) {
			if (e.target === overlay) {
				close();
			}
		});
		document.addEventListener('keydown', function (e) {
			if (overlay.classList.contains('hidden')) {
				return;
			}
			if (e.key === 'Escape') close();
			if (e.key === 'ArrowRight') show(current + 1);
			if (e.key === 'ArrowLeft') show(current - 1);
		});
	};

	document.addEventListener('DOMContentLoaded', function () {
		WPIS.mobileMenu();
		WPIS.gallery();
	});
})();
