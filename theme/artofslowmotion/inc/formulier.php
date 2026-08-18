<?php
/**
 * De drie aanvraagformulieren (Diensten, Camera Gear, Contact).
 *
 * Vervangt de Netlify Forms (`data-netlify="true"`) van de oude Astro-site: die
 * dienst is hier niet beschikbaar. In plaats daarvan: native `wp_mail()` naar
 * frank@artofslowmotion.com, zelfde patroon als warmtescan.nu
 * (inc/formulier.php in dat thema). Spam wordt tegengehouden zonder captcha:
 * een verborgen veld dat een mens nooit invult, plus een minimale invultijd.
 *
 * Eén gedeelde handler voor alle drie, met het formuliertype in een verborgen
 * veld — de velden verschillen per type, de afhandeling (nonce, spamvang,
 * mailen, terugsturen) is voor alle drie gelijk.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const AOS_FORMULIER_EMAIL = 'frank@artofslowmotion.com';

/** Welke phantom-sets aan te vinken zijn op het contactformulier. */
function aos_camera_sets(): array {
	return array(
		'flex25k'        => 'Phantom Flex 2.5k set',
		'flex4k'          => 'Phantom Flex 4k set',
		'onderwaterhuis'  => 'Onderwaterhuis',
	);
}

add_action( 'template_redirect', function () {
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || ! isset( $_POST['aos_formulier_type'] ) ) {
		return;
	}

	$terug = remove_query_arg( array( 'verzonden', 'fout' ) );
	$type  = sanitize_key( $_POST['aos_formulier_type'] );

	if ( ! isset( $_POST['aos_nonce'] ) || ! wp_verify_nonce( $_POST['aos_nonce'], 'aos_formulier_' . $type ) ) {
		wp_safe_redirect( add_query_arg( 'fout', 'verlopen', $terug ) );
		exit;
	}

	// Vangveld plus minimale invultijd, zelfde spamvang als bij warmtescan.nu.
	$geopend = absint( $_POST['aos_geopend'] ?? 0 );
	if ( ! empty( $_POST['aos_website'] ) || ( time() - $geopend ) < 3 ) {
		wp_safe_redirect( add_query_arg( 'verzonden', '1', $terug ) );
		exit;
	}

	$regels    = array();
	$antwoord  = '';
	$onderwerp = '';

	switch ( $type ) {

		case 'diensten':
			$gegevens = array(
				'Voornaam'   => sanitize_text_field( $_POST['voornaam'] ?? '' ),
				'Achternaam' => sanitize_text_field( $_POST['achternaam'] ?? '' ),
				'Email'      => sanitize_email( $_POST['email'] ?? '' ),
				'Telefoon'   => sanitize_text_field( $_POST['telefoon'] ?? '' ),
			);
			$antwoord  = $gegevens['Email'];
			$onderwerp = sprintf( 'Aanvraag via Diensten: %s %s', $gegevens['Voornaam'], $gegevens['Achternaam'] );
			foreach ( $gegevens as $label => $waarde ) {
				if ( '' !== $waarde ) {
					$regels[] = "$label: $waarde";
				}
			}
			break;

		case 'cameragear':
			if ( ! is_email( $_POST['email'] ?? '' ) ) {
				wp_safe_redirect( add_query_arg( 'fout', 'email', $terug ) );
				exit;
			}
			$gegevens = array(
				'Voornaam'  => sanitize_text_field( $_POST['first_name'] ?? '' ),
				'Achternaam' => sanitize_text_field( $_POST['last_name'] ?? '' ),
				'Email'     => sanitize_email( $_POST['email'] ?? '' ),
				'Bericht'   => sanitize_textarea_field( $_POST['message'] ?? '' ),
			);
			$antwoord  = $gegevens['Email'];
			$onderwerp = sprintf( 'Aanvraag via Camera Gear: %s %s', $gegevens['Voornaam'], $gegevens['Achternaam'] );
			foreach ( $gegevens as $label => $waarde ) {
				if ( '' !== $waarde ) {
					$regels[] = "$label: $waarde";
				}
			}
			break;

		case 'contact':
			if ( ! is_email( $_POST['email'] ?? '' ) ) {
				wp_safe_redirect( add_query_arg( 'fout', 'email', $terug ) );
				exit;
			}
			$gekozen = array();
			foreach ( (array) ( $_POST['set'] ?? array() ) as $slug ) {
				$slug = sanitize_key( $slug );
				if ( isset( aos_camera_sets()[ $slug ] ) ) {
					$gekozen[] = aos_camera_sets()[ $slug ];
				}
			}
			$gegevens = array(
				'Voornaam'    => sanitize_text_field( $_POST['voornaam'] ?? '' ),
				'Achternaam'  => sanitize_text_field( $_POST['achternaam'] ?? '' ),
				'Email'       => sanitize_email( $_POST['email'] ?? '' ),
				'Onderwerp'   => sanitize_text_field( $_POST['onderwerp'] ?? '' ),
				'Datum shoot' => sanitize_text_field( $_POST['datum'] ?? '' ),
				'Camera set'  => implode( ', ', $gekozen ),
				'Bericht'     => sanitize_textarea_field( $_POST['bericht'] ?? '' ),
			);
			$antwoord  = $gegevens['Email'];
			$onderwerp = sprintf( 'Contactformulier: %s %s', $gegevens['Voornaam'], $gegevens['Achternaam'] );
			foreach ( $gegevens as $label => $waarde ) {
				if ( '' !== $waarde ) {
					$regels[] = "$label: $waarde";
				}
			}
			break;

		default:
			wp_safe_redirect( $terug );
			exit;
	}

	if ( '' === $antwoord || ! is_email( $antwoord ) ) {
		wp_safe_redirect( add_query_arg( 'fout', 'email', $terug ) );
		exit;
	}

	$body = "Nieuwe aanvraag via artofslowmotion.com\n\n" . implode( "\n", $regels );

	wp_mail(
		AOS_FORMULIER_EMAIL,
		$onderwerp,
		$body,
		array( 'Reply-To: ' . $antwoord )
	);

	wp_safe_redirect( add_query_arg( 'verzonden', '1', $terug ) );
	exit;
} );

/**
 * Meldingen (gelukt/fout) boven een formulier, gedeeld door alle drie.
 */
function aos_formulier_meldingen(): string {
	if ( isset( $_GET['verzonden'] ) ) {
		return '<div class="aos-melding aos-melding--goed"><p><strong>Bedankt, je bericht is verstuurd.</strong> '
			. 'Ik neem zo snel mogelijk contact met je op.</p></div>';
	}

	$fouten = array(
		'email'    => 'Dat e-mailadres klopt niet. Controleer het even.',
		'verlopen' => 'Het formulier stond te lang open. Verstuur het opnieuw.',
	);

	if ( isset( $_GET['fout'] ) && isset( $fouten[ $_GET['fout'] ] ) ) {
		return sprintf(
			'<div class="aos-melding aos-melding--fout"><p>%s</p></div>',
			esc_html( $fouten[ $_GET['fout'] ] )
		);
	}

	return '';
}

/**
 * Shortcode [aos_formulier type="diensten|cameragear|contact"].
 */
add_shortcode( 'aos_formulier', function ( $atts ) {
	$atts = shortcode_atts( array( 'type' => 'contact' ), $atts );
	$type = sanitize_key( $atts['type'] );

	ob_start();
	echo aos_formulier_meldingen();
	?>
	<form class="aos-formulier" method="post">
		<?php wp_nonce_field( 'aos_formulier_' . $type, 'aos_nonce' ); ?>
		<input type="hidden" name="aos_formulier_type" value="<?php echo esc_attr( $type ); ?>">
		<input type="hidden" name="aos_geopend" value="<?php echo esc_attr( time() ); ?>">

		<p class="aos-vangveld" aria-hidden="true">
			<label for="aos-website-<?php echo esc_attr( $type ); ?>">Laat dit veld leeg</label>
			<input type="text" id="aos-website-<?php echo esc_attr( $type ); ?>" name="aos_website" tabindex="-1" autocomplete="off">
		</p>

		<?php if ( 'diensten' === $type ) : ?>

			<div class="aos-rij">
				<p>
					<label for="voornaam">Voornaam</label>
					<input type="text" id="voornaam" name="voornaam" autocomplete="given-name">
				</p>
				<p>
					<label for="achternaam">Achternaam</label>
					<input type="text" id="achternaam" name="achternaam" autocomplete="family-name">
				</p>
			</div>
			<p>
				<label for="email">Email</label>
				<input type="email" id="email" name="email" autocomplete="email">
			</p>
			<p>
				<label for="telefoon">Telefoon</label>
				<input type="tel" id="telefoon" name="telefoon" autocomplete="tel">
			</p>
			<p>
				<button type="submit" class="wp-element-button">verstuur</button>
			</p>

		<?php elseif ( 'cameragear' === $type ) : ?>

			<div class="aos-rij">
				<p>
					<label for="first_name">First Name</label>
					<input type="text" id="first_name" name="first_name" autocomplete="given-name">
				</p>
				<p>
					<label for="last_name">Last Name</label>
					<input type="text" id="last_name" name="last_name" autocomplete="family-name">
				</p>
			</div>
			<p>
				<label for="email">Email <span aria-hidden="true">*</span></label>
				<input type="email" id="email" name="email" required autocomplete="email">
			</p>
			<p>
				<label for="message">Message</label>
				<textarea id="message" name="message" rows="4"></textarea>
			</p>
			<p>
				<button type="submit" class="wp-element-button">Send</button>
			</p>

		<?php else : ?>

			<div class="aos-rij">
				<p>
					<label for="voornaam">Voornaam</label>
					<input type="text" id="voornaam" name="voornaam" autocomplete="given-name">
				</p>
				<p>
					<label for="achternaam">Achternaam</label>
					<input type="text" id="achternaam" name="achternaam" autocomplete="family-name">
				</p>
			</div>
			<p>
				<label for="email">Email <span aria-hidden="true">*</span></label>
				<input type="email" id="email" name="email" required autocomplete="email">
			</p>
			<p>
				<label for="onderwerp">Onderwerp</label>
				<input type="text" id="onderwerp" name="onderwerp">
			</p>
			<p>
				<label for="datum">Optie datum voor high speed shoot</label>
				<input type="date" id="datum" name="datum">
			</p>
			<fieldset>
				<legend>Phantom camera set:</legend>
				<?php foreach ( aos_camera_sets() as $slug => $label ) : ?>
					<label class="aos-keuze">
						<input type="checkbox" name="set[]" value="<?php echo esc_attr( $slug ); ?>" <?php checked( 'flex25k', $slug ); ?>>
						<?php echo esc_html( $label ); ?>
					</label>
				<?php endforeach; ?>
			</fieldset>
			<p>
				<label for="bericht">Laat een bericht achter ...</label>
				<textarea id="bericht" name="bericht" rows="5"></textarea>
			</p>
			<p>
				<button type="submit" class="wp-element-button">Verstuur</button>
			</p>

		<?php endif; ?>
	</form>
	<?php
	return aos_compact_html( ob_get_clean() );
} );
