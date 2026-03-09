# FP CTA Bar

Barra CTA fissa per WordPress con bottone personalizzabile, tracking marketing integrato e routing eventi verso FP Marketing Tracking Layer.

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/franpass87/FP-CTA-Bar)
[![License](https://img.shields.io/badge/license-Proprietary-red.svg)]()

---

## Per l'utente

### Cosa fa
FP CTA Bar aggiunge una barra fissa (in alto o in basso) con un bottone call-to-action personalizzabile. Ideale per promuovere un'offerta, un link di prenotazione o un numero di telefono su ogni pagina del sito.

### Configurazione
1. Vai su **Impostazioni → FP CTA Bar** nel pannello WordPress
2. Configura:
   - **Testo bottone** e **URL destinazione**
   - **Icona** del bottone (Font Awesome)
   - **Colori** sfondo e testo
   - **Posizione** (top / bottom)
   - **Tracking**: abilita l'invio eventi a GTM/Meta tramite FP Marketing Tracking Layer

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
Il plugin emette un `CustomEvent` JavaScript `fpCtaBarClick` al click sul bottone. L'evento viene intercettato da `fp-tracking.js` (FP Marketing Tracking Layer) che lo instrada verso GTM/Meta/GA4.

```javascript
// Evento emesso al click
document.dispatchEvent(new CustomEvent('fpCtaBarClick', {
    detail: { url: '...', label: '...' }
}));
```

### Filtri disponibili
| Filtro | Descrizione |
|--------|-------------|
| `fp_cta_bar_settings` | Modifica le impostazioni prima del rendering |
| `fp_cta_bar_html` | Modifica l'HTML della barra |

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
