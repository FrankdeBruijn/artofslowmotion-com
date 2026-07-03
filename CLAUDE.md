# artofslowmotion.com

Portfolio- en bedrijfswebsite van Art of Slowmotion — Frank de Bruijn, Phantom operator.

## Stack
- Astro (static site)
- Vanilla CSS (geen framework)
- Open Sans via Google Fonts
- Gehost op Cloudflare Pages — elke push naar `main` deployt automatisch

## Lokaal ontwikkelen
```bash
npm run dev      # start dev server op http://localhost:4321
npm run build    # build naar dist/
```

## Deployment
- GitHub: https://github.com/FrankdeBruijn/artofslowmotion-com
- Cloudflare Pages: https://artofslowmotion-com.pages.dev
- Live domein (nog te koppelen): https://www.artofslowmotion.com

## Huidige situatie
artofslowmotion.com staat nog op Wix (www.artofslowmotion.com).
Domein staat nog bij TransIP — verhuizen naar Cloudflare zodra de site klaar is.
Pas koppelen als de site goedgekeurd is.

## Design
Nagebouwd naar de live Wix-versie. Lichte achtergrond, Open Sans font, teal (#00a4b8) actieve nav-link.
Logo: /public/logo.jpg (gedownload van Wix CDN)

## Pagina's
- `/` — Portfolio (3-koloms Vimeo thumbnail grid + lightbox)
- `/diensten` — Diensten + contactformulier
- `/camera-gear` — Camera Gear (hero foto + gear-lijst + contactformulier)
- `/contact` — Contactpagina met specifiek formulier

## Contactgegevens (voor in de site)
- Telefoon: +31 (0)6 12 41 54 18
- E-mail: frank@artofslowmotion.com
- Social: Vimeo, Instagram (@frankdebruijnfilmmaker), Facebook, LinkedIn

## Openstaande punten
1. **5 ontbrekende Vimeo IDs** (portfolio items 7-12): klikken opent vimeo.com/frankdebruijn.
   Gevonden IDs: 769337956, 769338059, 522660418, 769337923, 769326455, 522660498.
   Voeg de ontbrekende IDs toe in `src/pages/index.astro` zodra je ze hebt.

2. **Camera Gear hero-afbeelding**: huidige foto is een drone-luchtopname (stand-in).
   Live Wix-site gebruikt een kliffen/water-foto. Download en sla op als `public/camera-gear-hero.jpg`.

3. **Afbeeldingen Wix CDN**: thumbnails en hero laden van `static.wixstatic.com`.
   Vervang door lokale bestanden in `public/` voordat Wix-account opgezegd wordt.

4. **Formulieren**: gebruiken `data-netlify="true"` voor Netlify Forms.
   Bij Cloudflare Pages: gebruik een externe dienst (Formspree, Netlify, etc.) of een Worker.
