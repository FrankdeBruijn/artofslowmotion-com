<?php
/**
 * Art of Slowmotion-thema.
 *
 * Child-thema van Twenty Twenty-Five. Bewust klein gehouden: het meeste zit
 * in theme.json. Hier staat de portfolio-shortcode, de drie formulieren en
 * wat theme.json niet dekt.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whitespace tussen tags weghalen uit shortcode-output. Onze shortcodes
 * bouwen markup op met <button>/<img>/<svg>/<form>-structuren; `wpautop` —
 * dat op de hele pagina-inhoud draait ná het renderen van blokken — herkent
 * zulke tags niet als block-level en zet elke newline om in een losse
 * <br>. Bij geneste elementen (bv. <img> in <button>) breekt dat de nesting
 * (de <img> belandt als sibling náást de <button> in plaats van erin).
 * Zonder newlines in de output heeft wpautop niets om op te breken.
 */
function aos_compact_html( string $html ): string {
	return preg_replace( '/>\s+</', '><', trim( $html ) );
}

require_once __DIR__ . '/inc/formulier.php';
require_once __DIR__ . '/inc/portfolio.php';

/**
 * Thema-stijl ook in de blokeditor, zodat wat Frank daar ziet klopt met de site.
 */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
} );

add_action( 'wp_enqueue_scripts', function () {
	// Open Sans van Google Fonts, zoals op de huidige site (geen self-hosted
	// kopie — bewust dezelfde bron als de Astro-versie).
	wp_enqueue_style(
		'aos-open-sans',
		'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap',
		array(),
		null
	);

	// Versie op basis van de bestandsdatum: een browser haalt de stijl na elke
	// wijziging opnieuw op, in plaats van een oude versie uit de cache.
	$pad = get_stylesheet_directory() . '/style.css';

	wp_enqueue_style(
		'artofslowmotion',
		get_stylesheet_uri(),
		array( 'aos-open-sans' ),
		file_exists( $pad ) ? (string) filemtime( $pad ) : wp_get_theme()->get( 'Version' )
	);

	// De portfolio-lightbox, alleen geregistreerd — ingeladen door de
	// shortcode zelf via wp_enqueue_script(), niet op elke pagina.
	$js_pad = get_stylesheet_directory() . '/assets/js/portfolio.js';
	wp_register_script(
		'aos-portfolio',
		get_theme_file_uri( 'assets/js/portfolio.js' ),
		array(),
		file_exists( $js_pad ) ? (string) filemtime( $js_pad ) : wp_get_theme()->get( 'Version' ),
		true
	);

	// De actieve navigatie-link markeren: wél op elke pagina, want de nav
	// staat in de header op elke pagina.
	$nav_js_pad = get_stylesheet_directory() . '/assets/js/nav-active.js';
	wp_enqueue_script(
		'aos-nav-active',
		get_theme_file_uri( 'assets/js/nav-active.js' ),
		array(),
		file_exists( $nav_js_pad ) ? (string) filemtime( $nav_js_pad ) : wp_get_theme()->get( 'Version' ),
		true
	);
} );

/**
 * Emoji-scripts van WordPress uitzetten. Ze laden op elke pagina een script
 * dat deze site niet gebruikt.
 */
add_action( 'init', function () {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
} );

/**
 * Titel-tag patroon van de oude Astro-site: "{paginatitel} | Art of
 * slowmotion" in plaats van het WordPress-standaardformaat.
 */
add_filter( 'pre_get_document_title', function () {
	if ( is_front_page() ) {
		return 'Portfolio | Art of slowmotion';
	}
	return single_post_title( '', false ) . ' | Art of slowmotion';
} );

/**
 * Vaste meta description, zelfde tekst als op elke pagina van de oude site
 * (die gaf ook geen pagina-specifieke omschrijving mee).
 */
add_action( 'wp_head', function () {
	echo '<meta name="description" content="Art of Slowmotion – Phantom Operator &amp; High Speed Cinematografie">' . "\n";
}, 1 );
