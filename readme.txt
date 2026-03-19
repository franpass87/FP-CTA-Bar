=== FP CTA Bar ===

Contributors: franpass87
Tags: cta, call to action, bar, megaphone, tracking, multilingual
Requires at least: 6.0
Tested up to: 6.4
Stable tag: 1.0.3
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Barra CTA fissa con bottone personalizzabile, link dinamici ITA/ENG, tracking e blocco Gutenberg.

== Description ==

FP CTA Bar aggiunge una barra fissa (full-width o bottone in basso a sinistra/destra) con un bottone call-to-action personalizzabile. Ideale per promuovere un'offerta, un link di prenotazione o un numero di telefono su ogni pagina del sito.

**Funzionalità principali:**
* Testo e link in italiano e inglese (rilevamento lingua automatico o via filtro/FP-Multilanguage)
* Tre modalità: barra full-width, bottone in basso a sinistra, bottone in basso a destra
* Link multipli con icone, target e tracking per singolo link
* Schedulazione: mostra la barra solo tra due date (campagne)
* Visibilità avanzata: solo su certi post type e/o term ID
* Integrazione tracking: CustomEvent `fpCtaBarClick` per GTM/Meta/GA4 (FP Marketing Tracking Layer)
* Action PHP `fp_cta_bar_clicked` e REST API (click + lettura impostazioni)
* Blocco Gutenberg `fp/cta-bar` e shortcode `[fp_cta_bar]`
* Accessibilità: focus trap, aria-label, supporto screen reader

= Requisiti =
* WordPress 6.0+
* PHP 7.4+
* FP Marketing Tracking Layer (opzionale, per invio eventi a GTM/Meta)

== Installation ==

1. Carica la cartella del plugin in `wp-content/plugins/` (o installa via fp-git-updater).
2. Assicurati che nella cartella del plugin sia presente `vendor/` (esegui `composer install --no-dev` se necessario).
3. Attiva il plugin da **Plugin** nel menu WordPress.
4. Vai su **FP CTA Bar** (menu laterale) o **Impostazioni → FP CTA Bar** per configurare testo, link, colori e visibilità.

== Frequently Asked Questions ==

= La barra non si vede =
Verifica che "Solo shortcode" sia disattivato se vuoi la barra globale, oppure inserisci lo shortcode `[fp_cta_bar]` o il blocco "FP CTA Bar" dove desideri. Controlla anche "Mostra su" e la schedulazione (date inizio/fine).

= Come tracciare i click? =
Abilita GA4, GTM e/o Meta nella card Tracking e, per ogni link, attiva "Traccia click". Gli eventi vengono inviati tramite FP Marketing Tracking Layer (CustomEvent `fpCtaBarClick`).

== Changelog ==

= 1.0.3 = (2026-03-19)
* Fixed: Menu admin non visibile — aggiunta voce top-level "FP CTA Bar" nella sidebar

= 1.2.0 = (2026-03-18)
* Added: Filtro `fp_cta_bar_lang` e integrazione FP-Multilanguage
* Added: Action `fp_cta_bar_clicked` e endpoint REST POST `/fp/ctabar/v1/click`
* Added: Anteprima lingua ITA/ENG in admin
* Added: Filtro `fp_cta_bar_visibility_context`
* Added: Focus trap e aria-label (a11y)
* Added: Schedulazione date (Mostra dal / Mostra al)
* Added: Visibilità avanzata (post type e term ID)
* Added: Blocco Gutenberg `fp/cta-bar`
* Added: REST GET `/fp/ctabar/v1/settings`
* Changed: Logica visibilità in `should_show()` (schedule, contesto, post type, term)

= 1.0.2 = (2026-03-14)
* Changed: Pagina admin con design system FP (card, toggle, badge)

= 1.0.1 = (2026-03-09)
* Changed: Aggiornamenti settings e README

= 1.0.0 = (2026-03-08)
* Added: Release iniziale, barra CTA, link multipli, tracking, shortcode

== Upgrade Notice ==

= 1.2.0 =
Nuovi filtri e action, REST API, blocco Gutenberg, schedulazione date e visibilità avanzata (post type/term). Requisito PHP 7.4+.
