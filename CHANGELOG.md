# Changelog

All notable changes to FP CTA Bar will be documented in this file.

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
