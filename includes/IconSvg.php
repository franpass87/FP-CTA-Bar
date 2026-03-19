<?php

declare(strict_types=1);

namespace FP\CtaBar;

/**
 * Icone: SVG line-art (INNER), emoji, brand; selettori admin separati per bottone principale vs link.
 */
final class IconSvg {

    /**
     * @var array<string, string>|null
     */
    private static ?array $brands = null;

    /**
     * Definizioni emoji da `data/icon-emoji-presets.php` (char + label i18n).
     *
     * @var array<string, array<string, string>>|null
     */
    private static ?array $emoji_definitions = null;

    /**
     * @return array<string, array<string, string>>
     */
    private static function emoji_definitions(): array {
        if (self::$emoji_definitions === null) {
            $path = __DIR__ . '/data/icon-emoji-presets.php';
            $loaded = is_readable($path) ? require $path : [];
            self::$emoji_definitions = is_array($loaded) ? $loaded : [];
        }

        return self::$emoji_definitions;
    }

    /**
     * Mappa chiave preset → carattere emoji.
     *
     * @return array<string, string>
     */
    private static function emojis(): array {
        static $chars = null;
        if ($chars === null) {
            $chars = [];
            foreach (self::emoji_definitions() as $key => $row) {
                if (is_array($row) && isset($row['char'])) {
                    $chars[$key] = (string) $row['char'];
                }
            }
        }

        return $chars;
    }

    /**
     * Opzioni selettore **link** nel pannello: «Nessuna», WhatsApp (logo), emoji.
     *
     * @return array<string, string>
     */
    public static function settings_link_icon_options(): array {
        $head = ['' => __('Nessuna', 'fp-cta-bar')];
        $brandExtras = [];
        if (array_key_exists('fpctabar-whatsapp', self::brands())) {
            $brandExtras['fpctabar-whatsapp'] = __('WhatsApp (logo)', 'fp-cta-bar');
        }
        $emojiOpts = [];
        foreach (self::emoji_definitions() as $key => $row) {
            if (!is_array($row)) {
                continue;
            }
            $label = isset($row['label']) ? (string) $row['label'] : '';
            $emojiOpts[$key] = $label !== '' ? __($label, 'fp-cta-bar') : $key;
        }

        return array_merge($head, $brandExtras, $emojiOpts);
    }

    /**
     * @deprecated 1.8.0 Usare {@see self::settings_link_icon_options()}.
     *
     * @return array<string, string>
     */
    public static function settings_icon_options(): array {
        return self::settings_link_icon_options();
    }

    /**
     * True se la chiave è un preset emoji (carattere Unicode, reso col font di sistema).
     */
    public static function is_emoji_preset(string $iconKey): bool {
        $k = trim($iconKey);

        return $k !== '' && array_key_exists($k, self::emojis());
    }

    /**
     * Carattere emoji per la chiave, o stringa vuota.
     */
    public static function emoji_char(string $iconKey): string {
        $k = trim($iconKey);

        return self::emojis()[$k] ?? '';
    }

    /**
     * Preset brand (chiavi `fpctabar-*` tranne emoji) caricati da `data/icon-brand-svgs.php`.
     *
     * @return array<string, string>
     */
    private static function brands(): array {
        if (self::$brands === null) {
            $path = __DIR__ . '/data/icon-brand-svgs.php';
            $loaded = is_readable($path) ? require $path : [];
            self::$brands = is_array($loaded) ? $loaded : [];
        }

        return self::$brands;
    }

    /**
     * True se l’icona è un SVG brand con colori propri (non va forzato stroke/currentColor del tema).
     */
    public static function is_brand(string $iconKey): bool {
        $k = trim($iconKey);

        return $k !== '' && !self::is_emoji_preset($k) && array_key_exists($k, self::brands());
    }

    /**
     * Contenuto interno SVG (elementi path/line/circle/rect…) per chiave identica a quella salvata in opzioni.
     *
     * @var array<string, string>
     */
    private const INNER = [
        'dashicons dashicons-calendar'     => '<rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>',
        'dashicons dashicons-calendar-alt' => '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/>',
        'dashicons dashicons-phone'        => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>',
        'dashicons dashicons-smartphone'   => '<rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/>',
        'dashicons dashicons-email'        => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
        'dashicons dashicons-location'     => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'dashicons dashicons-location-alt' => '<path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/>',
        'dashicons dashicons-admin-site'   => '<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/>',
        'dashicons dashicons-admin-home'   => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        'dashicons dashicons-admin-users'  => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'dashicons dashicons-tickets-alt'  => '<path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/>',
        'dashicons dashicons-cart'         => '<circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>',
        'dashicons dashicons-products'     => '<path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>',
        'dashicons dashicons-store'        => '<path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7"/>',
        'dashicons dashicons-money-alt'    => '<line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
        'dashicons dashicons-megaphone'    => '<path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>',
        'dashicons dashicons-info'         => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>',
        'dashicons dashicons-warning'      => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
        'dashicons dashicons-yes-alt'      => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>',
        'dashicons dashicons-plus-alt'     => '<circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/>',
        'dashicons dashicons-external'     => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/>',
        'dashicons dashicons-share'        => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/>',
        'dashicons dashicons-star-filled'  => '<polygon fill="none" points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
        'dashicons dashicons-heart'        => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5a5.5 5.5 0 0 0-2.5-4.5h0"/>',
        'dashicons dashicons-clock'        => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'dashicons dashicons-marker'       => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'dashicons dashicons-camera'       => '<path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"/><circle cx="12" cy="13" r="3"/>',
        'dashicons dashicons-format-image' => '<rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>',
        'dashicons dashicons-video-alt3'   => '<path d="m16 13 5.223 3.482a.5.5 0 0 0 .777-.416V7.87a.5.5 0 0 0-.752-.432L16 10.5"/><rect x="2" y="6" width="14" height="12" rx="2"/>',
        'dashicons dashicons-microphone'   => '<path d="m12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="23"/><line x1="8" x2="16" y1="23" y2="23"/>',
        'dashicons dashicons-format-chat'  => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
        'dashicons dashicons-format-status' => '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>',
        'dashicons dashicons-beer'         => '<path d="M8 22h8"/><path d="M7 10h10v9a4 4 0 0 1-4 4H11a4 4 0 0 1-4-4v-9Z"/><path d="M12 15v-3.5"/><path d="M8 2h8l1 6H7l1-6Z"/>',
    ];

    /**
     * Catalogo icone per il **bottone principale**: solo SVG line-art, etichette esplicative (ordine griglia admin).
     *
     * @return array<string, string>
     */
    private static function main_icon_catalog(): array {
        return [
            'dashicons dashicons-megaphone'     => __('Principale · Annuncio / CTA', 'fp-cta-bar'),
            'dashicons dashicons-calendar'     => __('Prenota · Calendario', 'fp-cta-bar'),
            'dashicons dashicons-calendar-alt'  => __('Prenota · Calendario dettaglio', 'fp-cta-bar'),
            'dashicons dashicons-phone'         => __('Contatti · Telefono', 'fp-cta-bar'),
            'dashicons dashicons-smartphone'    => __('Contatti · Smartphone', 'fp-cta-bar'),
            'dashicons dashicons-email'         => __('Contatti · Email', 'fp-cta-bar'),
            'dashicons dashicons-format-chat'   => __('Contatti · Chat / messaggi', 'fp-cta-bar'),
            'dashicons dashicons-location'      => __('Dove siamo · Pin', 'fp-cta-bar'),
            'dashicons dashicons-location-alt'  => __('Dove siamo · Pin alternativo', 'fp-cta-bar'),
            'dashicons dashicons-marker'        => __('Mappa · Segnaposto', 'fp-cta-bar'),
            'dashicons dashicons-admin-site'    => __('Web · Sito / globo', 'fp-cta-bar'),
            'dashicons dashicons-admin-home'    => __('Navigazione · Home', 'fp-cta-bar'),
            'dashicons dashicons-external'      => __('Azione · Apri link esterno', 'fp-cta-bar'),
            'dashicons dashicons-share'         => __('Social · Condividi', 'fp-cta-bar'),
            'dashicons dashicons-cart'          => __('Vendita · Carrello', 'fp-cta-bar'),
            'dashicons dashicons-store'         => __('Vendita · Negozio', 'fp-cta-bar'),
            'dashicons dashicons-products'      => __('Vendita · Catalogo', 'fp-cta-bar'),
            'dashicons dashicons-tickets-alt'   => __('Eventi · Biglietti', 'fp-cta-bar'),
            'dashicons dashicons-money-alt'     => __('Vendita · Prezzi / pagamento', 'fp-cta-bar'),
            'dashicons dashicons-admin-users'   => __('Chi siamo · Team / persone', 'fp-cta-bar'),
            'dashicons dashicons-star-filled'   => __('Evidenza · Stella', 'fp-cta-bar'),
            'dashicons dashicons-heart'         => __('Evidenza · Cuore', 'fp-cta-bar'),
            'dashicons dashicons-clock'         => __('Servizio · Orari', 'fp-cta-bar'),
            'dashicons dashicons-info'          => __('Aiuto · Informazioni', 'fp-cta-bar'),
            'dashicons dashicons-warning'       => __('Importante · Avviso', 'fp-cta-bar'),
            'dashicons dashicons-yes-alt'       => __('Stato · OK / conferma', 'fp-cta-bar'),
            'dashicons dashicons-plus-alt'      => __('Esplora · Altro / più', 'fp-cta-bar'),
            'dashicons dashicons-camera'        => __('Media · Fotocamera', 'fp-cta-bar'),
            'dashicons dashicons-format-image'  => __('Media · Galleria', 'fp-cta-bar'),
            'dashicons dashicons-video-alt3'    => __('Media · Video', 'fp-cta-bar'),
            'dashicons dashicons-microphone'    => __('Media · Voce / podcast', 'fp-cta-bar'),
            'dashicons dashicons-format-status' => __('Novità · Aggiornamento', 'fp-cta-bar'),
            'dashicons dashicons-beer'          => __('Locale · Ristorante / drink', 'fp-cta-bar'),
        ];
    }

    /**
     * Opzioni selettore **icona principale** (barra/bottone): solo SVG vettoriali esplicativi, no emoji.
     *
     * @return array<string, string>
     */
    public static function settings_main_icon_options(): array {
        $out = ['' => __('Nessuna', 'fp-cta-bar')];
        foreach (self::main_icon_catalog() as $key => $label) {
            if (isset(self::INNER[$key])) {
                $out[$key] = $label;
            }
        }

        return $out;
    }

    /**
     * SVG completo per la chiave preset, o stringa vuota.
     */
    public static function inline(string $iconKey): string {
        $k = trim($iconKey);
        if ($k === '') {
            return '';
        }
        if (array_key_exists($k, self::emojis())) {
            return '';
        }
        $brands = self::brands();
        if (isset($brands[$k])) {
            return $brands[$k];
        }
        if (!isset(self::INNER[$k])) {
            return '';
        }

        return self::wrap(self::INNER[$k]);
    }

    public static function has_preset(string $iconKey): bool {
        $k = trim($iconKey);

        return isset(self::INNER[$k]) || array_key_exists($k, self::brands()) || array_key_exists($k, self::emojis());
    }

    /**
     * Stampa icona per griglia admin (SVG preset o Dashicons fallback).
     */
    public static function echo_admin_icon(string $iconClass): void {
        $iconClass = trim($iconClass);
        if ($iconClass === '') {
            echo '<span class="dashicons dashicons-minus fpctabar-admin-icon-fallback" aria-hidden="true"></span>';
            return;
        }
        if (self::is_emoji_preset($iconClass)) {
            echo '<span class="fpctabar-admin-icon-emoji" aria-hidden="true">' . esc_html(self::emoji_char($iconClass)) . '</span>';
            return;
        }
        $svg = self::inline($iconClass);
        if ($svg !== '') {
            $brandClass = self::is_brand($iconClass) ? ' fpctabar-admin-icon-svg--brand' : '';
            echo '<span class="fpctabar-admin-icon-svg' . $brandClass . '" aria-hidden="true">' . $svg . '</span>';
            return;
        }
        echo '<span class="' . esc_attr($iconClass) . ' fpctabar-admin-icon-fallback" aria-hidden="true"></span>';
    }

    /**
     * Mappa chiave preset → SVG inline per wp_localize_script (sync trigger in admin JS).
     *
     * @return array<string, string>
     */
    public static function presets_for_script(): array {
        $out = [];
        foreach (array_keys(self::INNER) as $k) {
            $out[$k] = self::inline($k);
        }
        foreach (array_keys(self::brands()) as $k) {
            $out[$k] = self::inline($k);
        }
        foreach (self::emojis() as $k => $ch) {
            $out[$k] = $ch;
        }

        return $out;
    }

    private static function wrap(string $inner): string {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" width="24" height="24" focusable="false">' . $inner . '</svg>';
    }
}
