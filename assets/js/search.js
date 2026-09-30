/**
 * Recherche de biens — facettes + recherche instantanée.
 *
 * Enrichit les formulaires [data-wpis-search] (template-parts/global/search-bar.php),
 * qui fonctionnent seuls en GET classique :
 *
 * 1. Facettes : à chaque sélection, recalcule (à partir du dataset embarqué
 *    WPISSearch.estates) quelles valeurs des AUTRES champs mènent encore à au
 *    moins un bien, et désactive les options impossibles — pas de recherche à
 *    « 0 résultat ». Faceting disjonctif : les options d'un champ sont calculées
 *    avec les filtres des autres champs seulement. Un choix devenu impossible
 *    est relâché automatiquement.
 *
 * 2. Sur l'archive, chaque changement relance la recherche (sans le bouton) :
 *    - module « search_ajax » actif : résultats rechargés via la route REST
 *      wpis/v1/estates, URL mise à jour (history.pushState), retour arrière géré ;
 *    - sinon : soumission classique (rechargement de la page).
 */
( function () {
	'use strict';

	var config = window.WPISSearch;
	if ( ! config ) {
		return;
	}

	var estates = Array.isArray( config.estates ) ? config.estates : [];

	document.querySelectorAll( '[data-wpis-search]' ).forEach( function ( form ) {
		initForm( form );
	} );

	function initForm( form ) {
		var radios = Array.prototype.slice.call( form.querySelectorAll( 'input[name="wpis_purpose"]' ) );
		var selects = [
			{ key: 'category', el: form.querySelector( '[name="wpis_category"]' ), kind: 'eq' },
			{ key: 'city', el: form.querySelector( '[name="wpis_city"]' ), kind: 'eq' },
			{ key: 'bedrooms', el: form.querySelector( '[name="wpis_bedrooms"]' ), kind: 'min' }
		].filter( function ( f ) {
			return f.el;
		} );
		var priceEl = form.querySelector( '[name="wpis_price_max"]' );

		/* ---------------------------------------------------------------
		 * Facettes
		 * ------------------------------------------------------------ */

		// Lit l'état courant du formulaire.
		function readFilters() {
			var checked = radios.filter( function ( r ) {
				return r.checked;
			} )[ 0 ];
			var f = {
				purpose: checked ? checked.value : '',
				price: priceEl && priceEl.value !== '' ? parseInt( priceEl.value, 10 ) : ''
			};
			selects.forEach( function ( sel ) {
				var v = sel.el.value;
				f[ sel.key ] = 'min' === sel.kind && v !== '' ? parseInt( v, 10 ) : v;
			} );
			return f;
		}

		// Un bien satisfait-il un jeu de filtres ?
		function matches( estate, f ) {
			if ( f.purpose && estate.purpose !== f.purpose ) {
				return false;
			}
			if ( f.category && estate.category !== f.category ) {
				return false;
			}
			if ( f.city && estate.city !== f.city ) {
				return false;
			}
			if ( f.bedrooms && estate.bedrooms < f.bedrooms ) {
				return false;
			}
			if ( f.price && estate.price > 0 && estate.price > f.price ) {
				return false;
			}
			return true;
		}

		// Existe-t-il un bien pour ces filtres, en remplaçant la clé par la valeur candidate ?
		function reachable( baseFilters, key, value ) {
			var test = {};
			for ( var k in baseFilters ) {
				if ( Object.prototype.hasOwnProperty.call( baseFilters, k ) ) {
					test[ k ] = baseFilters[ k ];
				}
			}
			test[ key ] = value;
			return estates.some( function ( e ) {
				return matches( e, test );
			} );
		}

		function recompute() {
			if ( ! estates.length ) {
				return;
			}
			var f = readFilters();

			// Onglets « Opération » (« Toutes » = valeur vide, toujours active).
			radios.forEach( function ( r ) {
				var ok = '' === r.value || reachable( f, 'purpose', r.value );
				r.disabled = ! ok;
				var tab = r.closest( '.wpis-searchform-tab' );
				if ( tab ) {
					tab.classList.toggle( 'is-disabled', ! ok );
				}
			} );

			// Selects (l'option vide reste toujours active).
			selects.forEach( function ( sel ) {
				Array.prototype.forEach.call( sel.el.options, function ( opt ) {
					if ( '' === opt.value ) {
						opt.disabled = false;
						return;
					}
					var value = 'min' === sel.kind ? parseInt( opt.value, 10 ) : opt.value;
					opt.disabled = ! reachable( f, sel.key, value );
				} );
			} );

			// Garde-fou : un choix courant devenu impossible est relâché.
			var changed = false;
			radios.forEach( function ( r ) {
				if ( r.checked && r.disabled ) {
					var all = radios.filter( function ( x ) {
						return '' === x.value;
					} )[ 0 ];
					if ( all ) {
						all.checked = true;
						changed = true;
					}
				}
			} );
			selects.forEach( function ( sel ) {
				var opt = sel.el.options[ sel.el.selectedIndex ];
				if ( opt && opt.disabled ) {
					sel.el.value = '';
					changed = true;
				}
			} );
			if ( changed ) {
				recompute(); // Converge : relâcher un choix ne fait qu'élargir.
			}
		}

		radios.forEach( function ( r ) {
			r.addEventListener( 'change', recompute );
		} );
		selects.forEach( function ( sel ) {
			sel.el.addEventListener( 'change', recompute );
		} );
		if ( priceEl ) {
			priceEl.addEventListener( 'input', recompute );
		}

		/* ---------------------------------------------------------------
		 * Archive : relance automatique (AJAX ou rechargement)
		 * ------------------------------------------------------------ */

		if ( config.archive ) {
			// Enregistré APRÈS recompute sur le même évènement « change » : le
			// garde-fou relâche d'abord un choix impossible, puis on soumet
			// l'état corrigé. Le prix ne soumet qu'au « change » (blur / Entrée).
			var instant = config.ajax && window.fetch && window.history && window.URLSearchParams
				? initInstant( form, recompute )
				: null;

			var autoSubmit = function () {
				if ( instant ) {
					instant();
				} else if ( form.requestSubmit ) {
					form.requestSubmit();
				} else {
					form.submit();
				}
			};
			radios.forEach( function ( r ) {
				r.addEventListener( 'change', autoSubmit );
			} );
			selects.forEach( function ( sel ) {
				sel.el.addEventListener( 'change', autoSubmit );
			} );
			if ( priceEl ) {
				priceEl.addEventListener( 'change', autoSubmit );
			}

			if ( ! instant ) {
				// .is-loading posé seulement si la soumission aboutit (l'évènement
				// « submit » n'a pas lieu si la validation HTML5 échoue).
				form.addEventListener( 'submit', function () {
					form.classList.add( 'is-loading' );
				} );
			}
		}

		recompute(); // État initial (tient compte des filtres déjà appliqués en GET).
	}

	/* -------------------------------------------------------------------
	 * Recherche instantanée (module search_ajax)
	 * ---------------------------------------------------------------- */

	/**
	 * Branche la recherche instantanée sur un formulaire de l'archive.
	 *
	 * @return {Function} Relance la recherche avec l'état courant du formulaire.
	 */
	function initInstant( form, recompute ) {
		var controller = null;

		// Paramètres de recherche d'une URL d'archive (filtres + page).
		function paramsFromUrl( href ) {
			var url = new URL( href, window.location.href );
			var params = new URLSearchParams();
			url.searchParams.forEach( function ( value, key ) {
				if ( 0 === key.indexOf( 'wpis_' ) || 'paged' === key ) {
					params.set( key, value );
				}
			} );
			var page = url.pathname.match( /\/page\/(\d+)\/?$/ );
			if ( page ) {
				params.set( 'paged', page[ 1 ] );
			}
			return params;
		}

		// Paramètres de recherche du formulaire (champs vides omis, page 1).
		function paramsFromForm() {
			var params = new URLSearchParams();
			new FormData( form ).forEach( function ( value, key ) {
				if ( '' !== value ) {
					params.set( key, value );
				}
			} );
			// Le tri n'est pas un champ du formulaire : on conserve le tri courant.
			var sort = new URL( window.location.href ).searchParams.get( 'wpis_sort' );
			if ( sort ) {
				params.set( 'wpis_sort', sort );
			}
			return params;
		}

		// Recale les champs du formulaire sur une URL (retour arrière).
		function syncForm( params ) {
			form.querySelectorAll( 'input[name="wpis_purpose"]' ).forEach( function ( r ) {
				r.checked = r.value === ( params.get( 'wpis_purpose' ) || '' );
			} );
			[ 'wpis_category', 'wpis_city', 'wpis_bedrooms', 'wpis_price_max' ].forEach( function ( name ) {
				var el = form.querySelector( '[name="' + name + '"]' );
				if ( el ) {
					el.value = params.get( name ) || '';
				}
			} );
			recompute();
		}

		function load( params, push ) {
			var results = document.querySelector( '[data-wpis-results]' );
			if ( ! results ) {
				form.submit();
				return;
			}

			if ( controller ) {
				controller.abort();
			}
			controller = window.AbortController ? new AbortController() : null;

			form.classList.add( 'is-loading' );
			results.classList.add( 'is-loading' );
			results.setAttribute( 'aria-busy', 'true' );

			var sep = config.endpoint.indexOf( '?' ) === -1 ? '?' : '&';
			fetch( config.endpoint + sep + params.toString(), {
				credentials: 'same-origin',
				headers: { Accept: 'application/json' },
				signal: controller ? controller.signal : undefined
			} )
				.then( function ( response ) {
					if ( ! response.ok ) {
						throw new Error( response.status );
					}
					return response.json();
				} )
				.then( function ( data ) {
					var wrapper = document.createElement( 'div' );
					wrapper.innerHTML = data.html;
					var fresh = wrapper.querySelector( '[data-wpis-results]' );
					if ( ! fresh ) {
						throw new Error( 'markup' );
					}
					results.replaceWith( fresh );
					if ( push && data.url ) {
						window.history.pushState( { wpisSearch: true }, '', data.url );
					}
					// Changement de page : on remonte en haut des résultats.
					if ( params.get( 'paged' ) && fresh.getBoundingClientRect().top < 0 ) {
						fresh.scrollIntoView( { behavior: 'smooth', block: 'start' } );
					}
					form.classList.remove( 'is-loading' );
				} )
				.catch( function ( error ) {
					if ( error && 'AbortError' === error.name ) {
						return;
					}
					// Repli : navigation classique, le serveur sait faire.
					window.location.href = form.action + ( form.action.indexOf( '?' ) === -1 ? '?' : '&' ) + params.toString();
				} );
		}

		function submitForm() {
			if ( form.checkValidity && ! form.checkValidity() ) {
				form.reportValidity && form.reportValidity();
				return;
			}
			load( paramsFromForm(), true );
		}

		form.addEventListener( 'submit', function ( event ) {
			event.preventDefault();
			submitForm();
		} );

		// Liens de tri / pagination du bloc de résultats (délégation : le bloc est remplacé).
		document.addEventListener( 'click', function ( event ) {
			var link = event.target.closest( 'a[data-wpis-results-link]' );
			if ( ! link || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || 0 !== event.button ) {
				return;
			}
			event.preventDefault();
			load( paramsFromUrl( link.href ), true );
		} );

		// L'entrée d'historique initiale est marquée : un retour arrière jusqu'à
		// elle recharge aussi les résultats. Les autres popstate (ancres) sont ignorés.
		window.history.replaceState( { wpisSearch: true }, '' );
		window.addEventListener( 'popstate', function ( event ) {
			if ( ! event.state || ! event.state.wpisSearch ) {
				return;
			}
			var params = paramsFromUrl( window.location.href );
			syncForm( params );
			load( params, false );
		} );

		return submitForm;
	}
} )();
