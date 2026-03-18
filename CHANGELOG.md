# Changelog

All notable changes to FP CTA Bar will be documented in this file.

## [1.2.0] - 2026-03-18
### Added
- Filtro `fp_cta_bar_lang` per sovrascrivere la lingua (ITA/ENG); integrazione opzionale con FP-Multilanguage (`fp_ml_current_language`).
- Action `fp_cta_bar_clicked` (PHP) invocata al click su un link della barra; endpoint REST `POST fp/ctabar/v1/click` con nonce.
- Anteprima lingua in admin: select ITA/ENG per vedere label e primo link nella lingua scelta.
- Filtro `fp_cta_bar_visibility_context`: contesto (is_front_page, post_type, taxonomy, ecc.) per mostrare/nascondere la barra da altro codice.
- Accessibilità: focus trap nel pannello aperto (Tab/Shift+Tab), `aria-label` sui link.
- Schedulazione temporale: "Mostra dal" / "Mostra al" (date) per campagne.
- Visibilità avanzata: whitelist post type e term ID (solo su singoli post/pagine).
- Blocco Gutenberg `fp/cta-bar` per inserire la barra in un punto della pagina.
- REST API: `GET fp/ctabar/v1/settings` (solo lettura, `manage_options`).

### Changed
- `should_show()`: controlli per schedule_start/end, filtro contesto, post type e term visibility sui singular.

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
