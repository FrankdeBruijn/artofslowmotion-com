( function () {
	'use strict';

	var grid = document.getElementById( 'aos-portfolio-grid' );
	var lightbox = document.getElementById( 'aos-lightbox' );
	var closeBtn = document.getElementById( 'aos-lightbox-close' );
	var content = document.getElementById( 'aos-lightbox-content' );

	if ( ! grid || ! lightbox || ! content ) {
		return;
	}

	function openLightbox( id ) {
		content.innerHTML = '<iframe src="https://www.youtube.com/embed/' + id +
			'?autoplay=1&rel=0" frameborder="0" ' +
			'allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
		lightbox.classList.add( 'is-open' );
		lightbox.setAttribute( 'aria-hidden', 'false' );
		document.body.style.overflow = 'hidden';
	}

	function closeLightbox() {
		lightbox.classList.remove( 'is-open' );
		lightbox.setAttribute( 'aria-hidden', 'true' );
		content.innerHTML = '';
		document.body.style.overflow = '';
	}

	grid.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( 'button[data-youtube]' );
		if ( ! btn ) {
			return;
		}
		var id = btn.getAttribute( 'data-youtube' );
		if ( id ) {
			openLightbox( id );
		}
	} );

	closeBtn.addEventListener( 'click', closeLightbox );

	lightbox.addEventListener( 'click', function ( e ) {
		if ( e.target === lightbox ) {
			closeLightbox();
		}
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' === e.key ) {
			closeLightbox();
		}
	} );
} )();
