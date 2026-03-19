# Changelog

All notable changes to FP CTA Bar will be documented in this file.

## [1.8.3] - 2026-03-19
### Fixed
- Admin: anteprima CTA (full-width e modalità bottone) contenuta nel box «Anteprima» invece di agganciarsi al footer del viewport (`position: fixed` del frontend sovrascritto nel wrapper `.fpctabar-preview-wrap`).

## [1.8.2] - 2025-03-19
### Fixed
- Admin: `h1` come primo elemento nel `.wrap` (screen reader) e titolo visibile nel banner come `h2`, così le notice iniettate da terze parti con `$( '.wrap h1' ).after( … )` non finiscono più **dentro** il banner viola.

## [1.8.1] - 2026-03-19
### Fixed
- Admin: rimosso `display:flex` / `order` su `#wpbody-content` che portava le **notice WordPress sotto** il contenuto del plugin (effetto “dentro” l’header viola); flusso DOM naturale + `margin-top` sul `.wrap` per menu top-level e sotto Impostazioni

### Changed (documentazione)
- Allineata la regola in `fp-admin-ui-design-system.mdc` (workspace): niente flex+order errato sulle notice

## [1.8.0] - 2026-03-19
### Added
- `IconSvg::settings_main_icon_options()` — catalogo **SVG line-art** per il **bottone principale** con etichette esplicative (categorie: Principale, Prenota, Contatti, Vendita, Media, ecc.)

### Changed
- Selettore **icona principale** (admin): solo icone vettoriali dedicate, **non** emoji / WhatsApp
- Selettore **icone dei link**: invariato (emoji + WhatsApp); metodo rinominato `settings_link_icon_options()` (sostituisce `settings_icon_options()`)
- Griglia admin icona principale a 4 colonne per etichette più lunghe (classe `fp-cta-bar-icon-grid--main`)

### Note
- Valori `main_icon` già salvati come emoji/URL restano validi sul sito; in admin possono comparire come «Icona personalizzata…» finché non scegli un preset dalla nuova lista

## [1.7.1] - 2026-03-19
### Added
- Selettore admin: voce **WhatsApp (logo)** (`fpctabar-whatsapp`, SVG verde da `icon-brand-svgs.php`) subito dopo «Nessuna»

## [1.7.0] - 2026-03-19
### Added
- Oltre **60 nuove emoji** nel preset; file `icon-emoji-presets.php` con struttura `char` + `label` (msgid i18n)
- `IconSvg::settings_icon_options()` per la griglia admin solo-emoji

### Changed
- Selettore icone in admin: **solo «Nessuna» + emoji** (niente più line-art né brand in elenco)
- Hint icona principale aggiornato
- Admin: griglia icone `max-height` 320px (scroll con elenco più lungo)

### Note
- Chiavi **dashicons** / **fpctabar-*** (brand SVG) già salvate restano supportate in frontend e in anteprima admin (`iconPresets` + `IconSvg`)

## [1.6.0] - 2026-03-19
### Added
- Preset **emoji Unicode** (`fpctabar-emoji-*`, file `includes/data/icon-emoji-presets.php`): icone colorate native di OS/browser, senza CDN; `IconSvg::is_emoji_preset()`, `emoji_char()`; rendering frontend `fpctabar__icon--emoji`; admin/JS allineati

### Changed
- Selettore admin: meno duplicati (rimossi calendario/mappa “alt” e i **logo brand SVG** dalla lista); le chiavi brand già salvate restano supportate e continuano a rendersi da `icon-brand-svgs.php`
- Etichette preset line-art suffisso “(linea)” per distinguerle dalle emoji

## [1.5.0] - 2026-03-19
### Added
- Preset **icone brand a colori** (WhatsApp, Telegram, Messenger, Facebook, Instagram, Threads, X, LinkedIn, YouTube, TikTok, Pinterest, Spotify, Snapchat, Google, pin Maps, Booking.com, Airbnb, Tripadvisor) — file `includes/data/icon-brand-svgs.php`, chiavi `fpctabar-*`
- `IconSvg::is_brand()`, classe frontend `fpctabar__icon--brand` e admin `fpctabar-admin-icon-svg--brand` per non forzare `stroke`/`currentColor` del tema sui SVG colorati
- Admin JS: trigger icona avvolge gli SVG preset in `<span class="fpctabar-admin-icon-svg">` come il PHP (coerenza con icone brand)

## [1.4.2] - 2026-03-19
### Fixed
- Icone **SVG** su **mobile** (soprattutto Safari / temi tipo Salient): il testo del bottone rispettava `--fpctabar-text` ma l’icona restava scura — molti temi applicano `button { color: … !important }` solo sotto breakpoint stretti; aggiunti `color` / `-webkit-text-fill-color` su `#fpctabar .fpctabar__btn` e **stroke** esplicito `var(--fpctabar-text)` sugli `<svg>` della barra/bottone; stesso schema per SVG nel pannello con `--fpctabar-panel-text`; rinforzo in `@media (max-width: 600px)`

## [1.4.1] - 2026-03-19
### Fixed
- Admin: le **card** usavano `overflow: hidden`, tagliando il dropdown icone (`position: absolute`)
- Admin: **z-index** basso (`50`) faceva finire la griglia sotto righe successive / metabox; picker aperto ora `z-index: 100100` e riga/field genitore `100090` (classi `is-open`, `fpctabar-link-row--icon-open`, `fpctabar-field--icon-open` via JS)

### Changed
- Admin: griglia preset **6 colonne**, gap/padding ridotti, celle senza `min-height` eccessivo; etichette con clamp a 2 righe
- Admin: nel repeater **link**, dropdown icone **largo min. 260px / max 400px** (non limitato alla colonna stretta)
- Admin: JS unificato su selettore `.fpctabar-icon-picker` per close/refresh

## [1.4.0] - 2026-03-19
### Added
- Classe `IconSvg`: mappa ogni preset del selettore (stessa chiave `dashicons dashicons-*` salvata in DB) verso **SVG inline** line-art (path ispirati a [Lucide](https://lucide.dev), MIT)
- Frontend: `icon_html()` usa gli SVG per i preset; classi Dashicons sconosciute o custom restano su `<span>` + font Dashicons come prima
- Admin: griglia e trigger icone mostrano gli stessi SVG; `wp_localize_script` espone `fpCtaBar.iconPresets` per aggiornare il trigger dopo la scelta (JS)

### Changed
- `uses_dashicons()`: non carica più lo stylesheet Dashicons se tutte le icone in uso sono preset mappati su SVG
- CSS frontend: tile morbido per icona nei link del pannello, link con padding/angoli leggermente più curati; regole colore anche per `.fpctabar__icon--svg`
- CSS admin: dimensioni allineate per trigger/griglia SVG

## [1.3.9] - 2026-03-19
### Fixed
- **Border radius**: molti temi applicano `border-radius` (spesso con `!important`) ai `button` e annullavano il valore impostato; ora `#fpctabar .fpctabar__btn` applica `var(--fpctabar-btn-radius)` con `!important`, con eccezione esplicita per il bottone tondo (icon-circle → `999px`)
- **Full-width**: barra principale e pannello usano il raggio sui vertici superiori (prima ignorato)
- **Pannello floating (button left/right)**: rimosso `max(16px, …)` che rendeva identici i raggi ≤16px; raggio = valore admin
- **Sanitize**: `button_radius` ammette 0–48px (prima max 24)

### Changed
- Admin: hint sotto il campo border radius; input `max="48"`

## [1.3.8] - 2026-03-19
### Fixed
- Dashicons su barra/bottone principale e nel pannello link: il glifo è su `::before` e molti temi impostano `.dashicons:before` (anche con `!important`) rendendo l’icona scura a dispetto del colore testo scelto in admin. Aggiunte regole ad alta specificità (`#fpctabar …`) che applicano `var(--fpctabar-text)` / `var(--fpctabar-panel-text)` anche al pseudo-elemento

## [1.3.7] - 2026-03-19
### Changed
- Modalità bottone (fixed): tipografia senza `uppercase` forzato e tracking più naturale; ombre a due strati più morbide; bordo più leggero; raggio di default 14px; hover senza `filter` (solo sollevamento + ombra); `:active` più compatto
- Barra full-width: etichetta come sopra (nessun uppercase forzato); bordo superiore e ombra verso l’alto più soft; hover `brightness` solo con `@media (hover: hover) and (pointer: fine)`

## [1.3.6] - 2026-03-19
### Fixed
- Modalità bottone su mobile (Safari/iOS): `filter` sul wrapper `position: fixed` poteva nascondere o tagliare il bottone; ombra spostata su `.fpctabar__btn` con `box-shadow`
- Posizionamento mobile: `env(safe-area-inset-*)` su wrapper bottone e body quando serve spazio per il pannello

### Changed
- Barra full-width: `padding-bottom` con safe-area per contenuti sopra la home indicator
- Hover bottone con `transform`/`brightness` solo dove `@media (hover: hover) and (pointer: fine)` (evita effetti strani al touch)

### Added
- Admin: testo di aiuto sotto «Dispositivo» (Tutti / Solo mobile / Solo desktop)

## [1.3.5] - 2026-03-19
### Changed
- Frontend: più spazio nel pannello link (larghezza min/max, padding, gap icona–testo) per ridurre testi tagliati o troppo stretti
- Frontend: etichette link con `overflow-wrap`, interlinea maggiore e freccia `›` non comprimibile (`flex-shrink: 0`)
- Frontend: pannello full-width con padding orizzontale fluido e link fino a ~36rem; barra principale con padding laterale e label che può andare a capo

## [1.3.4] - 2026-03-19
### Changed
- Frontend: pannello floating con angoli più morbidi, ombre stratificate e leggero highlight interno
- Frontend: link del menu senza righe “tagliate”; hover con bordo interno sottile e freccia `›` animata (solo modalità bottone)
- Frontend: bottone principale con ombra più naturale, stato `:active` e transizioni con curve easing
- Frontend: barra full-width con hover leggero (`brightness`) e ombre sul pannello aperto
- Frontend: rispetto `prefers-reduced-motion` su hover che usano `transform`

## [1.3.3] - 2026-03-19
### Added
- Nuovo colore **Testo e icone nel pannello link** (`panel_text_color`), separato dal colore barra/bottone principale (`text_color`): puoi avere testo nero nel menu aperto e icona/etichetta chiare sul bottone

### Changed
- Admin: etichetta "Testo" rinominata in "Testo e icona barra / bottone" con hint esplicativo
- Migrazione automatica: alle installazioni esistenti viene copiato il vecchio `text_color` in `panel_text_color` così il comportamento resta identico finché non modifichi i colori

## [1.3.2] - 2026-03-19
### Changed
- Frontend: tipografia con stack di sistema (niente Playfair non caricata → fallback serif brutto)
- Frontend: pannello link con ombre morbide, bordi e separatori derivati dal colore testo (`color-mix`), padding e larghezza minima migliori
- Frontend: allineamento icone + etichette su più righe (wrapper `.fpctabar__link-label`, flex nel pannello)
- Frontend: bottone floating con hover più curato (ombra, leggero sollevamento) e `drop-shadow` sul contenitore

## [1.3.1] - 2026-03-19
### Fixed
- Inizializzazione frontend spostata su hook `wp` per valutare correttamente la visibilità (homepage/contesto) ed evitare falsi negativi
- Aggiunto fallback di rendering su `wp_body_open` (con guard anti-duplicazione) oltre a `wp_footer` per compatibilità con temi che gestiscono diversamente il footer

### Changed
- Requisito minimo PHP aggiornato a 8.0

## [1.3.0] - 2026-03-19
### Added
- Nuova impostazione "Grandezza bottone" (Compatto / Medio / Grande)

### Changed
- Frontend: la dimensione scelta scala padding del bottone e dimensione del bottone circolare in modalità solo icona

## [1.2.3] - 2026-03-19
### Fixed
- Admin UI: risolto bug del picker icone che mostrava le griglie sempre aperte (layout "a cascata" sulle righe Link)

## [1.2.2] - 2026-03-19
### Fixed
- Frontend: supporto link "solo icona" (URL + icona) anche senza etichetta, evitando che la CTA venga nascosta
- Admin: conteggio/link validi aggiornato (URL + etichetta oppure URL + icona)

## [1.2.1] - 2026-03-19
### Added
- Nuova icona "Vino" nel picker icone visuale (selezione rapida da griglia)

## [1.2.0] - 2026-03-19
### Changed
- Picker icone visuale in admin: scelta tramite griglia di icone cliccabili (non solo testo), per icona principale e icone link
- Migliorata UX del selettore con pulsante preview, stato attivo e dropdown icone

## [1.1.0] - 2026-03-19
### Added
- Nuova opzione "Mostra solo icona" per il bottone/barra principale (testo non obbligatorio)
- Nuova opzione "Bottone circolare" in modalità bottone (left/right) quando è attiva la modalità solo icona

### Changed
- Icona principale ora selezionabile da menu con anteprima, senza dover scrivere classi manuali

## [1.0.7] - 2026-03-19
### Changed
- Aggiunte molte nuove icone Dashicons nel selettore "Icona" dei link (telefono, smartphone, casa, pagamento, warning, chat, video e altre)

## [1.0.6] - 2026-03-19
### Changed
- Selettore icona nei link: ora scegli l'icona da menu (con anteprima) senza inserire classi manuali
- Layout campi link riorganizzato in griglia per allineare meglio icona, label, URL, target e tracking

## [1.0.5] - 2026-03-19
### Changed
- Notice admin quando nessun link configurato: chiarisce che la barra non appare senza almeno un link (URL + etichetta)
- Status bar: pill "Barra visibile" / "Aggiungi link per attivare" e conteggio link validi

## [1.0.4] - 2026-03-19
### Fixed
- vendor/ incluso nel repo: in produzione (fp-git-updater) il plugin non aveva vendor e terminava senza caricare Admin/menu

## [1.0.3] - 2026-03-19
### Fixed
- Menu admin non visibile: aggiunta voce top-level "FP CTA Bar" nella sidebar (icona megaphone). Rimane anche sotto Impostazioni.

## [1.0.2] - 2026-03-14
### Changed
- Pagina admin adattata al design system FP (header gradiente, card, toggle, badge, bottoni)
- Token CSS e componenti unificati con FP Digital Marketing Suite
- Status bar con pill (barra attiva, tracking)
- Griglia campi al posto di form-table, submit_button sostituito con fpctabar-btn

## [1.0.1] - 2026-03-09
### Changed
- Aggiornamenti settings e README

## [1.0.0] - 2026-03-08
### Added
- Initial release
- Barra CTA fissa con bottone personalizzabile e link
- Icone bottone configurabili
- Personalizzazione testo e colori
- Integrazione tracking GTM/Meta tramite CustomEvent `fpCtaBarClick`
- Pannello admin con UI tracking per link
- Routing eventi verso `fp-tracking.js` (FP Marketing Tracking Layer)
