# WordPress-subsite

Opgezet 2026-08-19 op het bestaande multisite-netwerk van `orangestock.com` bij TransIP,
exact volgens het patroon van Rooye Plas en Warmtescan.nu. Achtergrond over dat netwerk:
`~/Projects/PROJECTEN/orangestock/CLAUDE.md`. Vervangt de huidige Wix-site
(artofslowmotion.com) — zie ook `~/Projects/PROJECTEN/wix-eruit/04-openstaand.md`.

## Wat er draait

| | |
|---|---|
| WordPress | https://orangestock.com/artofslowmotion/ |
| Beheer | https://orangestock.com/artofslowmotion/wp-admin/ |
| Vorm | Subsite (`blog_id` 6) van het multisite-netwerk op orangestock.com |
| Database | dezelfde als de hoofdsite (`oranih_kwhgho79`), tabelprefix `wp_6_` |
| Thema | `artofslowmotion`, child van Twenty Twenty-Five |
| Permalinks | Berichtnaam (`/%postname%/`) |
| Zoekmachines | geweerd (`blog_public = 0`) — staging, nog niet indexeren |
| Novamira | niet geïnstalleerd/geactiveerd voor deze subsite (niet nodig geweest tijdens bouw) |

Inloggen gaat met het netwerkaccount **`admin`**, zelfde als op de andere subsites.

## Waar de code staat

Thema in dit project, in git: `theme/artofslowmotion/`. De bestaande Astro-bestanden in
deze repo (`src/`, `astro.config.mjs`, `dist/`) zijn **niet aangeraakt** — die blijven
als referentie/archief staan.

Deployen met rsync naar de gedeelde themamap van het netwerk:

```bash
rsync -az --delete --exclude '.DS_Store' \
  theme/artofslowmotion/ orangestock:/data/sites/web/orangestockcom/www/wp-content/themes/artofslowmotion/
ssh orangestock 'cd /data/sites/web/orangestockcom/www && \
  wp theme activate artofslowmotion --url=https://orangestock.com/artofslowmotion/'
```

## Opbouw van het thema

| Pad | Wat |
|---|---|
| `theme.json` | Palet (wit/achtergrond/lijn/tekst/gedempt/actief-teal), Open Sans, groottes, ruimtes |
| `style.css` | Header/footer, portfolio-grid + lightbox, hero, gear-items, formulieren |
| `functions.php` | Editor-stijl, Google Fonts-enqueue, titel-tag-patroon, meta description, emoji uit |
| `inc/portfolio.php` | Shortcode `[aos_portfolio]` — de 12-video grid met lightbox (homepage) |
| `inc/formulier.php` | Shortcode `[aos_formulier type="diensten\|cameragear\|contact"]` — de 3 formulieren |
| `assets/portfolio/` | De 12 thumbnails, gedownload van de Wix-CDN (niet meer extern geladen) |
| `assets/img/logo.jpg` | Logo, gekopieerd uit `public/logo.jpg` |
| `assets/img/camera-gear-hero.jpg` | Tijdelijke hero-foto (drone-luchtfoto), zie open punten |
| `assets/js/portfolio.js` | Lightbox-gedrag (Vimeo-iframe openen/sluiten) |
| `assets/js/nav-active.js` | Markeert de actieve nav-link in teal (custom-URL links, geen WP-paginamenu) |
| `parts/header.html`, `parts/footer.html` | Template-parts |
| `templates/front-page.html` | Portfolio-grid via shortcode |
| `templates/page.html`, `index.html`, `404.html` | Standaardpagina's |

### Waarom shortcodes in plaats van vaste Gutenberg-blocks

Zowel de portfolio-grid als de drie formulieren hebben gedrag (JS-lightbox, spamvang,
`wp_mail()`) dat niet in kale core-blocks past. Zelfde aanpak als het
`[warmtescan_formulier]`-patroon in `warmtescan-nu/theme/warmtescan/inc/formulier.php`.

### Een bug onderweg, en de les erin

De eerste versie van de portfolio-shortcode gaf een kapotte grid: `<img>`-tags belandden
als sibling náást hun `<button>` in plaats van erin, met een zichtbaar schaakbordpatroon
van gevulde en lege cellen tot gevolg. Oorzaak: `wpautop` draait op de **hele**
pagina-inhoud *nadat* alle blocks (inclusief de shortcode) al zijn gerenderd, en herkent
`<button>`/`<svg>` niet als block-level — elke newline in de ruwe HTML werd omgezet in een
losse `<br>`, wat de nesting brak. Opgelost met een gedeelde helper `aos_compact_html()` in
`functions.php` die whitespace tussen tags weghaalt vóór een shortcode zijn output
teruggeeft. Toegepast op zowel `[aos_portfolio]` als `[aos_formulier]`. **Les voor een
volgend thema met shortcode-HTML die niet-block-level tags bevat: pas dit direct toe, dan
hoeft dit niet opnieuw ontdekt te worden.**

### Titel-tag en meta description

Via `pre_get_document_title` en een handmatige `<meta name="description">` op `wp_head`
(prioriteit 1), zodat ze overeenkomen met het patroon van de oude Astro-site:
`"{paginatitel} | Art of slowmotion"` en een vaste omschrijving
("Art of Slowmotion – Phantom Operator & High Speed Cinematografie") op elke pagina.

### Content van de pagina's

De drie inhoudspagina's (Diensten, Camera Gear, Contact) zijn aangemaakt via
`wp post create` met kant-en-klare Gutenberg-blockmarkup als `post_content`, woordelijk
overgenomen uit de Astro-bronbestanden. Geen los contentplan-bestand nodig; de content
staat nu in de WordPress-database van deze subsite (en is dus ook via de Site-editor
bewerkbaar).

## Getest (2026-08-19)

| Test | Uitkomst |
|---|---|
| `/artofslowmotion/` | 200 |
| `/artofslowmotion/diensten/` | 200 |
| `/artofslowmotion/camera-gear/` | 200 |
| `/artofslowmotion/contact/` | 200 |
| `/artofslowmotion/wp-json/` | 200 |
| PHP-fouten/warnings op alle vier pagina's | geen |
| Portfolio-grid: 12 items, juiste nesting | Playwright-screenshot bevestigd (desktop 3 kolommen, mobiel 1 kolom) |
| Lightbox opent bij klik | ja (`is-open`-class gezet); Vimeo-iframe zelf geeft 403, zie open punt 1 |
| Hamburgermenu <768px | opent, alle 4 links leesbaar en klikbaar |
| Actieve nav-link in teal | werkt op alle 4 pagina's (custom-URL links via `nav-active.js`) |
| Formulier "Diensten": submit → `wp_mail()` → redirect | 302 naar `?verzonden=1`, succesmelding rendert, geen serverfout |
| Titel-tag | `Portfolio \| Art of slowmotion`, `Diensten \| Art of slowmotion`, enz. |
| Meta description | aanwezig, vaste tekst zoals gespecificeerd |
| `blog_public` | `0` (niet geïndexeerd) |

Niet los getest: daadwerkelijke e-mailaflevering (geen logtoegang op dit gedeelde
pakket) — `wp_mail()` gaf geen fout, wat het enige signaal is dat op dit hostingpakket
beschikbaar is. Zelfde beperking als bij de andere subsites.

## Terugdraaien

```bash
ssh orangestock 'cd /data/sites/web/orangestockcom/www && wp site delete 6 --yes'
```

Raakt geen andere subsite (frankdebruijn.com/site 1, rooyeplas/site 3, warmtescan/site 5).

## Openstaande punten

1. **Vimeo-embed-privacy nog niet aangepast.** De 12 portfolio-video's zijn co-credited
   klantwerk met Jorrit Stollman (vimeo.com/jorritstollman); de embeds geven momenteel een
   403 omdat het Vimeo-abonnement is afgeschaald en domeinbeperkt embedden is vervallen.
   De grid gebruikt de juiste video-ID's en gaat vanzelf werken zodra Frank (met eigen
   toestemming, zo nodig via Jorrits account) de embed-privacy per video openzet voor dit
   domein. Buiten scope van dit thema-werk.
2. **Camera Gear-hero is nog de tijdelijke drone-luchtfoto**, niet de kliffen/water-foto
   van de live Wix-site — al een bekend open punt in de Astro-bronversie, hier gewoon
   overgenomen.
3. ~~Domein artofslowmotion.com nog niet gekoppeld.~~ **Inmiddels wél gekoppeld** (geverifieerd
   2026-08-27, buiten deze sessie om gedaan — deze notitie liep achter). Nameservers staan op
   Cloudflare, `https://artofslowmotion.com/` serveert nu rechtstreeks deze WordPress-subsite
   (title-tag, `aos-portfolio`-markup en `wp-json`-link kloppen), en
   `https://orangestock.com/artofslowmotion/` geeft inmiddels 404 — de siteurl/home staan dus
   op het eigen domein. `robots.txt` staat op `Allow: /`, dus `blog_public` lijkt ook al naar
   `1` gezet (zie ook de tabel hierboven, die nog `0` toont — verifiëren met
   `wp option get blog_public --url=https://artofslowmotion.com`). Nog wel na te lopen vóór
   Wix wordt opgezegd: media/formulier-inzendingen in het Wix-account exporteren (zie
   `wix-eruit/04-openstaand.md`) — dat kan alleen Frank, via het Wix-paneel.
4. Novamira staat niet actief op deze subsite. Als een volgende sessie er via de MCP-server
   aan verder moet, eerst de plugin activeren en netwerkbreed beschikbaar maken (zie
   orangestock/CLAUDE.md, sectie Novamira), en na de bouwfase weer deactiveren.
5. De mobiele hamburgeruitklap wijkt qua uitlijning nét af tussen de vier links (geen
   pixel-perfecte linkerkant) — functioneel prima (alle links leesbaar en klikbaar), maar
   niet verder gepolijst dan dat.

## Netwerkoverzicht (bijgewerkt)

| blog_id | URL | Thema | Geïndexeerd | Waar staat de code |
|---|---|---|---|---|
| 1 | `orangestock.com/` | `frankdebruijn` | nee | `PROJECTEN/orangestock/theme/frankdebruijn/` |
| 3 | `orangestock.com/rooyeplas/` | `rooyeplas` | nee | `rooye-plas/theme/rooyeplas/` |
| 4 | `orangestock.com/frank/` | `frankdebruijn` | nee | `PROJECTEN/orangestock/theme/frankdebruijn/` |
| 5 | `orangestock.com/warmtescan/` | `warmtescan` | nee | `warmtescan-nu/theme/warmtescan/` |
| 6 | `orangestock.com/artofslowmotion/` | `artofslowmotion` | nee | dit project, `theme/artofslowmotion/` |
