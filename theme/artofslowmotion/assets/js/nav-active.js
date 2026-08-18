( function () {
	'use strict';

	// De navigatie bestaat uit vaste custom-URL links, geen echte WP-pagina's
	// in het menu, dus WordPress kan zelf geen "current-menu-item" zetten.
	// Vergelijk daarom hier het pad van elke link met de huidige URL, zoals
	// de oude Astro-site dat client-side deed (Astro.url.pathname).
	var links = document.querySelectorAll( '.aos-header .wp-block-navigation-item__content' );
	var here = window.location.pathname.replace( /\/+$/, '' ) + '/';

	links.forEach( function ( link ) {
		var href = link.getAttribute( 'href' );
		if ( href && href.replace( /\/+$/, '' ) + '/' === here ) {
			link.classList.add( 'is-huidige-pagina' );
		}
	} );
} )();
