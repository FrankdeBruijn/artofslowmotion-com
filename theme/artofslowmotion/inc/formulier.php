<?php
/**
 * Shortcode [aos_formulier], die vroeger de drie aanvraagformulieren
 * (Diensten, Camera Gear, Contact) toonde.
 *
 * Per 2026-10 zijn de formulieren weg: het mailpakket van artofslowmotion.com
 * is opgezegd, en Frank wil geen formulieren meer. De shortcode blijft bestaan
 * omdat hij in de pagina-inhoud (database) staat; hij toont nu een
 * telefoonverwijzing, zodat die pagina's niet aangepast hoeven te worden.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode( 'aos_formulier', function ( $atts ) {
	$atts = shortcode_atts( array( 'type' => 'contact' ), $atts );
	$tekst = 'cameragear' === sanitize_key( $atts['type'] )
		? 'Interested? Call <a href="tel:+31612415418">+31 6 12415418</a>.'
		: 'Interesse? Bel <a href="tel:+31612415418">06 12415418</a>.';

	return '<p class="aos-bellen">' . $tekst . '</p>';
} );
