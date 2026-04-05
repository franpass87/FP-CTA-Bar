# FP CTA Bar

Barra CTA fissa per WordPress con bottone personalizzabile, tracking marketing integrato e routing eventi verso FP Marketing Tracking Layer.

[![Version](https://img.shields.io/badge/version-1.9.5-blue.svg)](https://github.com/franpass87/FP-CTA-Bar)
[![License](https://img.shields.io/badge/license-Proprietary-red.svg)]()

---

## Per l'utente

### Cosa fa
FP CTA Bar aggiunge una barra fissa (in alto o in basso) con un bottone call-to-action personalizzabile. Ideale per promuovere un'offerta, un link di prenotazione o un numero di telefono su ogni pagina del sito.

### Configurazione
1. Vai su **FP CTA Bar** (menu laterale) o **Impostazioni → FP CTA Bar** nel pannello WordPress
2. Configura:
   - **Testo bottone** e **URL destinazione**
   - **Icona bottone principale**: preset **SVG** esplicativi (CTA, prenotazione, contatti…); **icone dei link**: **emoji** + logo WhatsApp (+ URL dove supportato)
   - **Grandezza bottone** (Compatto/Medio/Grande)
   - Opzione **solo icona** (senza testo) e stile **circolare** per modalità bottone
   - **Colori** sfondo e testo
   - **Posizione** (top / bottom)
   - **Tracking**: abilita l'invio eventi a GTM/Meta tramite FP Marketing Tracking Layer
   - **Statistiche click**: pagina admin dedicata con totale click, ultimi 7 giorni e top link tracciati

### Requisiti
- WordPress 6.0+
- PHP 8.0+
- FP Marketing Tracking Layer (opzionale, per tracking)

---

## Per lo sviluppatore

### Struttura
```
FP-CTA-Bar/
├── fp-cta-bar.php          # File principale, header WP
├── src/
│   ├── Admin/
│   │   ├── Settings.php    # Pagina impostazioni admin
│   │   └── Frontend.php    # Rendering barra frontend
│   └── Tracking/           # Integrazione tracking
├── assets/
│   ├── css/
│   └── js/
└── vendor/                 # Autoload PSR-4
```

### Tracking
Il plugin emette un `CustomEvent` JavaScript `fpCtaBarClick` all’apertura della barra/bottone e, **per ogni link con «Traccia click» attivo**, al click sul link. `fp-tracking.js` (FP Marketing Tracking Layer) intercetta l’evento e fa `dataLayer.push` con **nome evento** uguale al campo *Nome evento* in admin (priorità: GTM, poi GA4; default `cta_bar_click`) e parametri **`cta_label`**, **`cta_action`**, **`cta_url`**, **`cta_category`** (derivati da «Label evento» / «Categoria» del link, o dal testo del link se la label è vuota).

**GA4:** i parametri personalizzati **non** compaiono nei report finché non crei **dimensioni personalizzate** in Amministratore → Definizioni personalizzate (es. scope evento, parametro `cta_label`). In **GTM** usa variabili Data Layer omonime e mappale nel tag *Google Analytics: evento GA4*.

```javascript
document.dispatchEvent(new CustomEvent('fpCtaBarClick', {
    detail: { eventName: 'cta_bar_click', label: '...', action: '...', url: '...', category: '...' }
}));
```

### Icone (frontend)
Le icone scelte dal selettore admin (preset) sono renderizzate come **SVG line-art** (stile moderno, `currentColor` per rispettare i colori barra/pannello). Le classi Dashicons personalizzate non in elenco restano su font Dashicons. I file immagine (URL) non cambiano.

### Filtri disponibili
| Filtro | Descrizione |
|--------|-------------|
| `fp_cta_bar_settings` | Modifica le impostazioni prima del rendering |
| `fp_cta_bar_html` | Modifica l'HTML della barra |
| `fp_cta_bar_hide_on_fp_experience_pages` | Default `true`: nasconde la barra su pagine FP Experiences (prenotazione). Restituire `false` per mostrarla comunque. |

### Installazione sviluppo
```bash
cd FP-CTA-Bar
composer install
```

---

## Changelog
Vedi [CHANGELOG.md](CHANGELOG.md)
---

## Autore

**Francesco Passeri**
- Sito: [francescopasseri.com](https://francescopasseri.com)
- Email: [info@francescopasseri.com](mailto:info@francescopasseri.com)
- GitHub: [github.com/franpass87](https://github.com/franpass87)
