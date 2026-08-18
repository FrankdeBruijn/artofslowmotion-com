<?php
/**
 * Portfolio-grid met lightbox op de homepage.
 *
 * Twaalf video's, co-credited met Jorrit Stollman (vimeo.com/jorritstollman).
 * De thumbnails staan lokaal in het thema (assets/portfolio/), niet meer op de
 * Wix-CDN. De Vimeo-embeds zelf geven op dit moment een 404 omdat het
 * Vimeo-abonnement is afgeschaald en domeinbeperkt embedden is vervallen — dat
 * is een handmatige actie bij Vimeo, buiten de scope van dit thema. De markup
 * hieronder gebruikt gewoon de juiste video-ID's; ze gaan vanzelf werken zodra
 * de embed-privacy per video is aangepast.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** De twaalf portfolio-video's: Vimeo-ID, lokaal thumbnail-bestand, titel. */
function aos_portfolio_videos(): array {
	return array(
		array( 'id' => '769326455', 'thumb' => 'portfolio-01.jpg', 'title' => 'Hoffmann Getranke – High Speed Operator' ),
		array( 'id' => '769338059', 'thumb' => 'portfolio-02.jpg', 'title' => 'Welcher Joghurt by Jorrit Stollman' ),
		array( 'id' => '769331652', 'thumb' => 'portfolio-03.jpg', 'title' => 'Guylian by Jorrit Stollman' ),
		array( 'id' => '522667927', 'thumb' => 'portfolio-04.jpg', 'title' => 'The Magician – Joyce' ),
		array( 'id' => '769337956', 'thumb' => 'portfolio-05.jpg', 'title' => 'Quaker CruesLi by Jorrit Stollman' ),
		array( 'id' => '522660498', 'thumb' => 'portfolio-06.jpg', 'title' => 'Whiskey by Jorrit Stollman' ),
		array( 'id' => '769337923', 'thumb' => 'portfolio-07.jpg', 'title' => 'The Art of Chocolate by Jorrit Stollman' ),
		array( 'id' => '522660418', 'thumb' => 'portfolio-08.jpg', 'title' => 'Lamar CookingCream by Jorrit Stollman' ),
		array( 'id' => '522663534', 'thumb' => 'portfolio-09.jpg', 'title' => 'SHAKLEE by Jorrit Stollman' ),
		array( 'id' => '769333929', 'thumb' => 'portfolio-10.jpg', 'title' => 'Welcher Joghurt 2 by Jorrit Stollman' ),
		array( 'id' => '522660321', 'thumb' => 'portfolio-11.jpg', 'title' => 'Lamar WhippingCream by Jorrit Stollman' ),
		array( 'id' => '522660251', 'thumb' => 'portfolio-12.jpg', 'title' => 'Sligro ZIN by Jorrit Stollman' ),
	);
}

add_shortcode( 'aos_portfolio', function () {
	wp_enqueue_script( 'aos-portfolio' );

	ob_start();
	?>
	<div class="aos-portfolio-titel">
		<h1>phantom operator</h1>
	</div>

	<div class="aos-portfolio-grid" id="aos-portfolio-grid">
		<?php foreach ( aos_portfolio_videos() as $i => $video ) : ?>
			<button
				class="aos-portfolio-item"
				type="button"
				data-id="<?php echo esc_attr( $video['id'] ); ?>"
				aria-label="<?php echo esc_attr( $video['title'] ); ?>"
			>
				<img
					src="<?php echo esc_url( get_theme_file_uri( 'assets/portfolio/' . $video['thumb'] ) ); ?>"
					alt="<?php echo esc_attr( $video['title'] ); ?>"
					loading="<?php echo $i < 6 ? 'eager' : 'lazy'; ?>"
				>
				<span class="aos-portfolio-overlay">
					<svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<circle cx="30" cy="30" r="30" fill="rgba(0,0,0,0.5)"/>
						<polygon points="24,18 46,30 24,42" fill="white"/>
					</svg>
				</span>
			</button>
		<?php endforeach; ?>
	</div>

	<div class="aos-lightbox" id="aos-lightbox" aria-modal="true" role="dialog" aria-hidden="true">
		<button class="aos-lightbox-close" id="aos-lightbox-close" aria-label="Sluiten" type="button">&#x2715;</button>
		<div class="aos-lightbox-content" id="aos-lightbox-content"></div>
	</div>
	<?php
	return aos_compact_html( ob_get_clean() );
} );
