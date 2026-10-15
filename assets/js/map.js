/**
 * Carte OpenStreetMap interactive (module map_voyager).
 */
( function () {
	'use strict';

	if ( typeof window.L === 'undefined' || typeof window.WPISMapData === 'undefined' ) {
		return;
	}

	document.querySelectorAll( '[data-wpis-map]' ).forEach( function ( element ) {
		var lat = Number.parseFloat( element.dataset.lat );
		var lng = Number.parseFloat( element.dataset.lng );
		if ( ! Number.isFinite( lat ) || ! Number.isFinite( lng ) ) {
			return;
		}

		var map = window.L.map( element, { scrollWheelZoom: false } ).setView( [ lat, lng ], 15 );
		window.L.tileLayer( window.WPISMapData.tileUrl, {
			attribution: window.WPISMapData.attribution,
			maxZoom: 20,
		} ).addTo( map );
		window.L.marker( [ lat, lng ], {
			title: element.dataset.title || '',
		} ).addTo( map );
	} );
}() );
