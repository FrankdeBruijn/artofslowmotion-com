<?php
/**
 * Portfolio-grid met lightbox op de homepage.
 *
 * Twaalf video's, co-credited met Jorrit Stollman (vimeo.com/jorritstollman).
 * De thumbnails staan lokaal in het thema (assets/portfolio/), niet meer op de
 * Wix-CDN. Stond eerst op Vimeo-embeds, maar die gaven een blijvende 403
 * (Vimeo-abonnement afgeschaald, domeinbeperkt embedden vervallen) — omgezet
 * naar YouTube (@GEWOONFILM), 2026-08-27. `youtube` is de video-ID op dat
 * kanaal; `null` betekent: nog niet op YouTube gevonden (staat dus nog niet
 * geüpload of onder een andere titel). Zulke items tonen "binnenkort" i.p.v.
 * een lightbox — zie aos_compact_html() hieronder.
 *
 * Bevestigd via zoeken op @GEWOONFILM (2026-08-27): Whiskey, Lamar Whipping
 * Cream en Sligro ZIN staan er letterlijk zo. "The Art of Chocolate" is
 * waarschijnlijk dezelfde productie als "The Alchemy of Chocolate" op het
 * kanaal (enige chocolade-video, andere titel) — nog even bevestigen.
 *
 * Update 2026-08-27 (later): de overige 7 afgewerkte exports teruggevonden op
 * Data 4, map "Vimeo Films download/", en als unlisted geüpload naar
 * @GEWOONFILM. "Welcher Joghurt 2" blijft null — daarvan bestaat maar één
 * afgewerkt bestand, geen tweede versie gevonden.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** De twaalf portfolio-video's: YouTube-ID (of null), lokaal thumbnail-bestand, titel. */
function aos_portfolio_videos(): array {
	return array(
		array( 'youtube' => '1Nw0clLl7ks', 'thumb' => 'portfolio-01.jpg', 'title' => 'Hoffmann Getranke – High Speed Operator' ),
		array( 'youtube' => 'wBb6DcccFDY', 'thumb' => 'portfolio-02.jpg', 'title' => 'Welcher Joghurt by Jorrit Stollman' ),
		array( 'youtube' => '8T_gzc82zOg', 'thumb' => 'portfolio-03.jpg', 'title' => 'Guylian by Jorrit Stollman' ),
		array( 'youtube' => 'z_Jwzyd0bAs', 'thumb' => 'portfolio-04.jpg', 'title' => 'The Magician – Joyce' ),
		array( 'youtube' => 'dhHDw4B3dro', 'thumb' => 'portfolio-05.jpg', 'title' => 'Quaker CruesLi by Jorrit Stollman' ),
		array( 'youtube' => 'kfxFOH8jktc', 'thumb' => 'portfolio-06.jpg', 'title' => 'Whiskey by Jorrit Stollman' ),
		array( 'youtube' => '7sNKoxblpSE', 'thumb' => 'portfolio-07.jpg', 'title' => 'The Art of Chocolate by Jorrit Stollman' ),
		array( 'youtube' => 'ehRJ8wbT9YE', 'thumb' => 'portfolio-08.jpg', 'title' => 'Lamar CookingCream by Jorrit Stollman' ),
		array( 'youtube' => '-3x8iEpNkQA', 'thumb' => 'portfolio-09.jpg', 'title' => 'SHAKLEE by Jorrit Stollman' ),
		array( 'youtube' => null, 'thumb' => 'portfolio-10.jpg', 'title' => 'Welcher Joghurt 2 by Jorrit Stollman' ),
		array( 'youtube' => '-32vaHXH6Ww', 'thumb' => 'portfolio-11.jpg', 'title' => 'Lamar WhippingCream by Jorrit Stollman' ),
		array( 'youtube' => 'Pzgb5IYAguk', 'thumb' => 'portfolio-12.jpg', 'title' => 'Sligro ZIN by Jorrit Stollman' ),
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
			<?php $heeft_video = ! empty( $video['youtube'] ); ?>
			<button
				class="aos-portfolio-item<?php echo $heeft_video ? '' : ' aos-portfolio-item--todo'; ?>"
				type="button"
				<?php echo $heeft_video ? 'data-youtube="' . esc_attr( $video['youtube'] ) . '"' : 'disabled aria-disabled="true"'; ?>
				aria-label="<?php echo esc_attr( $video['title'] ); ?><?php echo $heeft_video ? '' : ' (binnenkort beschikbaar)'; ?>"
			>
				<img
					src="<?php echo esc_url( get_theme_file_uri( 'assets/portfolio/' . $video['thumb'] ) ); ?>"
					alt="<?php echo esc_attr( $video['title'] ); ?>"
					loading="<?php echo $i < 6 ? 'eager' : 'lazy'; ?>"
				>
				<?php if ( $heeft_video ) : ?>
					<span class="aos-portfolio-overlay">
						<svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<circle cx="30" cy="30" r="30" fill="rgba(0,0,0,0.5)"/>
							<polygon points="24,18 46,30 24,42" fill="white"/>
						</svg>
					</span>
				<?php else : ?>
					<span class="aos-portfolio-overlay aos-portfolio-overlay--todo">binnenkort</span>
				<?php endif; ?>
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
