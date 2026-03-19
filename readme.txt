=== FP CTA Bar ===

Contributors: franpass87
Tags: cta, call to action, bar, megaphone, tracking, multilingual
Requires at least: 6.0
Tested up to: 6.4
Stable tag: 1.3.2
Requires PHP: 8.0
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
Aggiungi almeno un link con URL e etichetta nella sezione "Link". Senza link la barra non viene mai mostrata. Verifica anche: "Solo shortcode" disattivato per la barra globale; "Mostra su" include Home se vuoi vederla in homepage; schedulazione (date inizio/fine); dispositivo (Tutti/mobile/desktop).

= Come tracciare i click? =
Abilita GA4, GTM e/o Meta nella card Tracking e, per ogni link, attiva "Traccia click". Gli eventi vengono inviati tramite FP Marketing Tracking Layer (CustomEvent `fpCtaBarClick`).

== Changelog ==

= 1.3.2 = (2026-03-19)
* Changed: Stile frontend più pulito (font di sistema, pannello con ombre e link allineati meglio)
* Changed: Etichette link avvolte in span per testo su più righe senza disallineare le icone

= 1.3.1 = (2026-03-19)
* Fixed: Inizializzazione frontend spostata su hook `wp` per valutare correttamente la visibilita' (homepage/contesto) e mostrare la CTA quando configurata
* Fixed: Aggiunto fallback render su `wp_body_open` (con guard anti-duplicazione) oltre a `wp_footer` per maggiore compatibilita' con i temi
* Changed: Allineato requisito PHP minimo a 8.0

= 1.3.0 = (2026-03-19)
* Added: Impostazione "Grandezza bottone" (Compatto/Medio/Grande)
* Changed: Scala il padding del bottone e la dimensione del bottone circolare in modalità solo icona

= 1.2.3 = (2026-03-19)
* Fixed: Picker icone admin con griglie sempre aperte (layout disordinato) ora corretto

= 1.2.2 = (2026-03-19)
* Fixed: Supporto link solo icona (URL + icona) anche senza etichetta
* Fixed: Conteggio link validi aggiornato (URL + etichetta oppure URL + icona)

= 1.2.1 = (2026-03-19)
* Added: Nuova icona "Vino" nel picker icone visuale

= 1.2.0 = (2026-03-19)
* Changed: Picker icone visuale in admin con griglia di icone cliccabili (icona principale + icone link)

= 1.1.0 = (2026-03-19)
* Added: Opzione "Mostra solo icona" per bottone/barra principale (testo non obbligatorio)
* Added: Opzione "Bottone circolare" in modalità bottone quando è attiva la modalità solo icona
* Changed: Icona principale selezionabile da menu con anteprima

= 1.0.7 = (2026-03-19)
* Changed: Aggiunte molte nuove icone Dashicons nel selettore "Icona" dei link

= 1.0.6 = (2026-03-19)
* Changed: Selettore icona nei link (con anteprima) senza inserire classi manuali
* Changed: Layout campi link in griglia, con disposizione più ordinata e leggibile

= 1.0.5 = (2026-03-19)
* Changed: Notice e status bar quando nessun link configurato — guida l'utente ad aggiungere link

= 1.0.4 = (2026-03-19)
* Fixed: vendor incluso nel repo — in produzione (fp-git-updater) il plugin non caricava senza vendor

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
