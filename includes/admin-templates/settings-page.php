<?php
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$settings = \FP\CtaBar\Plugin::get_settings();
$opt      = \FP\CtaBar\Plugin::OPTION_KEY;

$use_shortcode = !empty($settings['use_shortcode']);
$has_tracking  = !empty($settings['ga4_enabled']) || !empty($settings['gtm_enabled']) || !empty($settings['meta_enabled']);
?>
<div class="wrap fpctabar-admin-page">
    <!-- Page Header -->
    <div class="fpctabar-page-header">
        <div class="fpctabar-page-header-content">
            <h1><span class="dashicons dashicons-megaphone"></span> <?php esc_html_e('FP CTA Bar', 'fp-cta-bar'); ?></h1>
            <p><?php esc_html_e('Configura la barra CTA fissa con link dinamici ITA/ENG.', 'fp-cta-bar'); ?></p>
        </div>
        <span class="fpctabar-page-header-badge">v<?php echo esc_html(FP_CTA_BAR_VERSION); ?></span>
    </div>

    <!-- Status Bar -->
    <div class="fpctabar-status-bar">
        <span class="fpctabar-status-pill <?php echo $use_shortcode ? 'is-missing' : 'is-active'; ?>">
            <span class="dot"></span>
            <?php echo $use_shortcode ? esc_html__('Solo shortcode', 'fp-cta-bar') : esc_html__('Barra globale attiva', 'fp-cta-bar'); ?>
        </span>
        <span class="fpctabar-status-pill <?php echo $has_tracking ? 'is-active' : 'is-missing'; ?>">
            <span class="dot"></span>
            <?php echo $has_tracking ? esc_html__('Tracking attivo', 'fp-cta-bar') : esc_html__('Tracking disattivato', 'fp-cta-bar'); ?>
        </span>
    </div>

    <form method="post" action="options.php">
        <?php settings_fields('fp_cta_bar_group'); ?>

        <!-- Card: Display Mode -->
        <div class="fpctabar-card">
            <div class="fpctabar-card-header">
                <div class="fpctabar-card-header-left">
                    <span class="dashicons dashicons-layout"></span>
                    <h2><?php esc_html_e('Modalità di visualizzazione', 'fp-cta-bar'); ?></h2>
                </div>
            </div>
            <div class="fpctabar-card-body">
                <fieldset class="fpctabar-modes">
                    <?php
                    $modes = [
                        'full-width'   => __('Full-width (barra in basso)', 'fp-cta-bar'),
                        'button-left'  => __('Bottone in basso a sinistra', 'fp-cta-bar'),
                        'button-right' => __('Bottone in basso a destra', 'fp-cta-bar'),
                    ];
                    foreach ($modes as $value => $label) :
                    ?>
                        <label class="fpctabar-mode-option">
                            <input type="radio"
                                   name="<?php echo esc_attr($opt); ?>[display_mode]"
                                   value="<?php echo esc_attr($value); ?>"
                                   <?php checked($settings['display_mode'], $value); ?>>
                            <span class="fpctabar-mode-preview fpctabar-mode-preview--<?php echo esc_attr($value); ?>">
                                <span class="fpctabar-mode-icon"></span>
                            </span>
                            <span class="fpctabar-mode-label"><?php echo esc_html($label); ?></span>
                        </label>
                    <?php endforeach; ?>
                </fieldset>
            </div>
        </div>

        <!-- Card: Bottone principale -->
        <div class="fpctabar-card">
            <div class="fpctabar-card-header">
                <div class="fpctabar-card-header-left">
                    <span class="dashicons dashicons-edit"></span>
                    <h2><?php esc_html_e('Bottone / Barra principale', 'fp-cta-bar'); ?></h2>
                </div>
            </div>
            <div class="fpctabar-card-body">
                <div class="fpctabar-fields-grid">
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Testo ITA', 'fp-cta-bar'); ?></label>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[main_label_it]" value="<?php echo esc_attr($settings['main_label_it']); ?>" placeholder="PRENOTA">
                        <span class="fpctabar-hint"><?php esc_html_e('Testo del bottone in italiano.', 'fp-cta-bar'); ?></span>
                    </div>
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Testo ENG', 'fp-cta-bar'); ?></label>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[main_label_en]" value="<?php echo esc_attr($settings['main_label_en']); ?>" placeholder="BOOK NOW">
                        <span class="fpctabar-hint"><?php esc_html_e('Testo del bottone in inglese.', 'fp-cta-bar'); ?></span>
                    </div>
                    <div class="fpctabar-field" style="grid-column: 1 / -1;">
                        <label><?php esc_html_e('Icona principale', 'fp-cta-bar'); ?></label>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[main_icon]" value="<?php echo esc_attr($settings['main_icon'] ?? ''); ?>" placeholder="dashicons dashicons-calendar-alt oppure URL immagine">
                        <span class="fpctabar-hint"><?php esc_html_e('Classe CSS (es. dashicons dashicons-calendar-alt) oppure URL immagine. Vuoto = nessuna icona.', 'fp-cta-bar'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Colori -->
        <div class="fpctabar-card">
            <div class="fpctabar-card-header">
                <div class="fpctabar-card-header-left">
                    <span class="dashicons dashicons-art"></span>
                    <h2><?php esc_html_e('Colori', 'fp-cta-bar'); ?></h2>
                </div>
            </div>
            <div class="fpctabar-card-body">
                <div class="fpctabar-fields-grid">
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Sfondo barra/bottone', 'fp-cta-bar'); ?></label>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[bg_color]" value="<?php echo esc_attr($settings['bg_color']); ?>" class="fp-cta-bar-color">
                    </div>
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Testo', 'fp-cta-bar'); ?></label>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[text_color]" value="<?php echo esc_attr($settings['text_color']); ?>" class="fp-cta-bar-color">
                    </div>
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Bordo', 'fp-cta-bar'); ?></label>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[border_color]" value="<?php echo esc_attr($settings['border_color']); ?>" class="fp-cta-bar-color">
                    </div>
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Sfondo pannello link', 'fp-cta-bar'); ?></label>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[panel_bg_color]" value="<?php echo esc_attr($settings['panel_bg_color']); ?>" class="fp-cta-bar-color">
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Visibilità e comportamento -->
        <div class="fpctabar-card">
            <div class="fpctabar-card-header">
                <div class="fpctabar-card-header-left">
                    <span class="dashicons dashicons-visibility"></span>
                    <h2><?php esc_html_e('Visibilità e comportamento', 'fp-cta-bar'); ?></h2>
                </div>
            </div>
            <div class="fpctabar-card-body">
                <div class="fpctabar-fields-grid">
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Z-index', 'fp-cta-bar'); ?></label>
                        <input type="number" name="<?php echo esc_attr($opt); ?>[z_index]" value="<?php echo esc_attr($settings['z_index'] ?? 99999); ?>" min="1" max="2147483647">
                    </div>
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Dispositivo', 'fp-cta-bar'); ?></label>
                        <select name="<?php echo esc_attr($opt); ?>[device_visibility]">
                            <option value="all" <?php selected($settings['device_visibility'] ?? 'all', 'all'); ?>><?php esc_html_e('Tutti', 'fp-cta-bar'); ?></option>
                            <option value="mobile" <?php selected($settings['device_visibility'] ?? 'all', 'mobile'); ?>><?php esc_html_e('Solo mobile', 'fp-cta-bar'); ?></option>
                            <option value="desktop" <?php selected($settings['device_visibility'] ?? 'all', 'desktop'); ?>><?php esc_html_e('Solo desktop', 'fp-cta-bar'); ?></option>
                        </select>
                    </div>
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Dimensione font', 'fp-cta-bar'); ?></label>
                        <select name="<?php echo esc_attr($opt); ?>[font_size]">
                            <option value="small" <?php selected($settings['font_size'] ?? 'medium', 'small'); ?>><?php esc_html_e('Piccolo', 'fp-cta-bar'); ?></option>
                            <option value="medium" <?php selected($settings['font_size'] ?? 'medium', 'medium'); ?>><?php esc_html_e('Medio', 'fp-cta-bar'); ?></option>
                            <option value="large" <?php selected($settings['font_size'] ?? 'medium', 'large'); ?>><?php esc_html_e('Grande', 'fp-cta-bar'); ?></option>
                        </select>
                    </div>
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Border radius bottone (px)', 'fp-cta-bar'); ?></label>
                        <input type="number" name="<?php echo esc_attr($opt); ?>[button_radius]" value="<?php echo esc_attr($settings['button_radius'] ?? 4); ?>" min="0" max="24">
                    </div>
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Ritardo comparsa (sec)', 'fp-cta-bar'); ?></label>
                        <input type="number" name="<?php echo esc_attr($opt); ?>[delay_seconds]" value="<?php echo esc_attr($settings['delay_seconds'] ?? 0); ?>" min="0" max="10">
                        <span class="fpctabar-hint"><?php esc_html_e('0 = immediato', 'fp-cta-bar'); ?></span>
                    </div>
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Mostra dopo scroll (%)', 'fp-cta-bar'); ?></label>
                        <input type="number" name="<?php echo esc_attr($opt); ?>[show_after_scroll_percent]" value="<?php echo esc_attr($settings['show_after_scroll_percent'] ?? 0); ?>" min="0" max="100">
                        <span class="fpctabar-hint"><?php esc_html_e('0 = disabilitato', 'fp-cta-bar'); ?></span>
                    </div>
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Nascondi dopo chiusura (ore)', 'fp-cta-bar'); ?></label>
                        <input type="number" name="<?php echo esc_attr($opt); ?>[hide_after_dismiss_hours]" value="<?php echo esc_attr($settings['hide_after_dismiss_hours'] ?? 0); ?>" min="0" max="168">
                        <span class="fpctabar-hint"><?php esc_html_e('0 = disabilitato, max 168', 'fp-cta-bar'); ?></span>
                    </div>
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Animazione', 'fp-cta-bar'); ?></label>
                        <select name="<?php echo esc_attr($opt); ?>[animation]">
                            <option value="none" <?php selected($settings['animation'] ?? 'slide', 'none'); ?>><?php esc_html_e('Nessuna', 'fp-cta-bar'); ?></option>
                            <option value="slide" <?php selected($settings['animation'] ?? 'slide', 'slide'); ?>><?php esc_html_e('Slide', 'fp-cta-bar'); ?></option>
                            <option value="fade" <?php selected($settings['animation'] ?? 'slide', 'fade'); ?>><?php esc_html_e('Fade', 'fp-cta-bar'); ?></option>
                            <option value="bounce" <?php selected($settings['animation'] ?? 'slide', 'bounce'); ?>><?php esc_html_e('Bounce', 'fp-cta-bar'); ?></option>
                        </select>
                    </div>
                </div>

                <p class="description" style="margin-top: 16px; margin-bottom: 12px;"><?php esc_html_e('Mostra su', 'fp-cta-bar'); ?></p>
                <div style="display: flex; flex-wrap: wrap; gap: 12px 24px;">
                    <?php
                    $vis     = $settings['visibility'] ?? ['home', 'single', 'page', 'archive'];
                    $vis_opts = [
                        'home'    => __('Homepage', 'fp-cta-bar'),
                        'single'  => __('Singoli post', 'fp-cta-bar'),
                        'page'    => __('Pagine', 'fp-cta-bar'),
                        'archive' => __('Archivi (categorie, tag)', 'fp-cta-bar'),
                        'search'  => __('Risultati di ricerca', 'fp-cta-bar'),
                        '404'     => __('Pagina 404', 'fp-cta-bar'),
                    ];
                    foreach ($vis_opts as $v => $l) :
                    ?>
                        <label style="display: flex; align-items: center; gap: 6px;">
                            <input type="checkbox" name="<?php echo esc_attr($opt); ?>[visibility][]" value="<?php echo esc_attr($v); ?>" <?php checked(in_array($v, $vis, true)); ?>>
                            <?php echo esc_html($l); ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top: 24px;">
                    <div class="fpctabar-toggle-row">
                        <div class="fpctabar-toggle-info">
                            <strong><?php esc_html_e('Chiudi al click sul link', 'fp-cta-bar'); ?></strong>
                            <span><?php esc_html_e('Chiudi il pannello quando si clicca un link', 'fp-cta-bar'); ?></span>
                        </div>
                        <label class="fpctabar-toggle">
                            <input type="checkbox" name="<?php echo esc_attr($opt); ?>[close_on_link_click]" value="1" <?php checked(!empty($settings['close_on_link_click'])); ?>>
                            <span class="fpctabar-toggle-slider"></span>
                        </label>
                    </div>
                    <div class="fpctabar-toggle-row">
                        <div class="fpctabar-toggle-info">
                            <strong><?php esc_html_e('Pannello aperto di default', 'fp-cta-bar'); ?></strong>
                            <span><?php esc_html_e('Mostra il pannello link aperto all\'avvio', 'fp-cta-bar'); ?></span>
                        </div>
                        <label class="fpctabar-toggle">
                            <input type="checkbox" name="<?php echo esc_attr($opt); ?>[panel_open_by_default]" value="1" <?php checked(!empty($settings['panel_open_by_default'])); ?>>
                            <span class="fpctabar-toggle-slider"></span>
                        </label>
                    </div>
                    <div class="fpctabar-toggle-row">
                        <div class="fpctabar-toggle-info">
                            <strong><?php esc_html_e('Solo shortcode', 'fp-cta-bar'); ?></strong>
                            <span><?php esc_html_e('Mostra la barra solo dove inserisci [fp_cta_bar]', 'fp-cta-bar'); ?></span>
                        </div>
                        <label class="fpctabar-toggle">
                            <input type="checkbox" name="<?php echo esc_attr($opt); ?>[use_shortcode]" value="1" <?php checked(!empty($settings['use_shortcode'])); ?>>
                            <span class="fpctabar-toggle-slider"></span>
                        </label>
                    </div>
                    <div class="fpctabar-toggle-row">
                        <div class="fpctabar-toggle-info">
                            <strong><?php esc_html_e('Cookie consent richiesto', 'fp-cta-bar'); ?></strong>
                            <span><?php esc_html_e('Nascondi finché l\'utente non ha accettato i cookie. Supporta Cookie Law Info, fp_cta_bar_can_show.', 'fp-cta-bar'); ?></span>
                        </div>
                        <label class="fpctabar-toggle">
                            <input type="checkbox" name="<?php echo esc_attr($opt); ?>[cookie_consent_required]" value="1" <?php checked(!empty($settings['cookie_consent_required'])); ?>>
                            <span class="fpctabar-toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Tracking -->
        <div class="fpctabar-card">
            <div class="fpctabar-card-header">
                <div class="fpctabar-card-header-left">
                    <span class="dashicons dashicons-chart-bar"></span>
                    <h2><?php esc_html_e('Tracking', 'fp-cta-bar'); ?></h2>
                </div>
                <span class="fpctabar-badge <?php echo $has_tracking ? 'fpctabar-badge-success' : 'fpctabar-badge-neutral'; ?>">
                    <?php echo $has_tracking ? '&#10003; ' . esc_html__('Attivo', 'fp-cta-bar') : esc_html__('Disattivato', 'fp-cta-bar'); ?>
                </span>
            </div>
            <div class="fpctabar-card-body">
                <div class="fpctabar-fields-grid" style="margin-bottom: 20px;">
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Google Analytics 4', 'fp-cta-bar'); ?></label>
                        <div class="fpctabar-toggle-row" style="padding: 0; border: none;">
                            <div class="fpctabar-toggle-info">
                                <strong><?php esc_html_e('Invia eventi GA4', 'fp-cta-bar'); ?></strong>
                                <span><?php esc_html_e('Richiede gtag', 'fp-cta-bar'); ?></span>
                            </div>
                            <label class="fpctabar-toggle">
                                <input type="checkbox" name="<?php echo esc_attr($opt); ?>[ga4_enabled]" value="1" <?php checked(!empty($settings['ga4_enabled'])); ?>>
                                <span class="fpctabar-toggle-slider"></span>
                            </label>
                        </div>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[ga4_event_name]" value="<?php echo esc_attr($settings['ga4_event_name'] ?? 'cta_bar_click'); ?>" placeholder="<?php esc_attr_e('Nome evento', 'fp-cta-bar'); ?>" style="margin-top: 8px;">
                    </div>
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Google Tag Manager', 'fp-cta-bar'); ?></label>
                        <div class="fpctabar-toggle-row" style="padding: 0; border: none;">
                            <div class="fpctabar-toggle-info">
                                <strong><?php esc_html_e('Push su dataLayer', 'fp-cta-bar'); ?></strong>
                                <span><?php esc_html_e('event + cta_bar_action, cta_bar_label', 'fp-cta-bar'); ?></span>
                            </div>
                            <label class="fpctabar-toggle">
                                <input type="checkbox" name="<?php echo esc_attr($opt); ?>[gtm_enabled]" value="1" <?php checked(!empty($settings['gtm_enabled'])); ?>>
                                <span class="fpctabar-toggle-slider"></span>
                            </label>
                        </div>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[gtm_event_name]" value="<?php echo esc_attr($settings['gtm_event_name'] ?? 'cta_bar_click'); ?>" placeholder="<?php esc_attr_e('Nome evento', 'fp-cta-bar'); ?>" style="margin-top: 8px;">
                    </div>
                    <div class="fpctabar-field">
                        <label><?php esc_html_e('Meta (Facebook Pixel)', 'fp-cta-bar'); ?></label>
                        <div class="fpctabar-toggle-row" style="padding: 0; border: none;">
                            <div class="fpctabar-toggle-info">
                                <strong><?php esc_html_e('trackCustom', 'fp-cta-bar'); ?></strong>
                                <span><?php esc_html_e('Richiede fbq', 'fp-cta-bar'); ?></span>
                            </div>
                            <label class="fpctabar-toggle">
                                <input type="checkbox" name="<?php echo esc_attr($opt); ?>[meta_enabled]" value="1" <?php checked(!empty($settings['meta_enabled'])); ?>>
                                <span class="fpctabar-toggle-slider"></span>
                            </label>
                        </div>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[meta_event_name]" value="<?php echo esc_attr($settings['meta_event_name'] ?? 'cta_bar_click'); ?>" placeholder="<?php esc_attr_e('Nome evento', 'fp-cta-bar'); ?>" style="margin-top: 8px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Link -->
        <div class="fpctabar-card">
            <div class="fpctabar-card-header">
                <div class="fpctabar-card-header-left">
                    <span class="dashicons dashicons-admin-links"></span>
                    <h2><?php esc_html_e('Link', 'fp-cta-bar'); ?></h2>
                </div>
                <?php $links_count = !empty($settings['links']) ? count($settings['links']) : 0; ?>
                <span class="fpctabar-badge fpctabar-badge-<?php echo $links_count > 0 ? 'success' : 'neutral'; ?>">
                    <?php echo (int) $links_count; ?> <?php esc_html_e('link', 'fp-cta-bar'); ?>
                </span>
            </div>
            <div class="fpctabar-card-body">
                <div id="fp-cta-bar-links">
                    <?php if (!empty($settings['links'])) : ?>
                        <?php foreach ($settings['links'] as $i => $link) : ?>
                            <div class="fpctabar-link-row fp-cta-bar-link-row" data-index="<?php echo (int) $i; ?>">
                                <div class="fpctabar-link-handle fp-cta-bar-link-handle" title="<?php esc_attr_e('Trascina per riordinare', 'fp-cta-bar'); ?>">&#9776;</div>
                                <div class="fpctabar-link-fields fp-cta-bar-link-fields">
                                    <div class="fpctabar-link-field fpctabar-link-field--icon fp-cta-bar-link-field fp-cta-bar-link-field--icon">
                                        <label><?php esc_html_e('Icona', 'fp-cta-bar'); ?></label>
                                        <input type="text" name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][icon]" value="<?php echo esc_attr($link['icon'] ?? ''); ?>" placeholder="dashicons dashicons-...">
                                    </div>
                                    <div class="fpctabar-link-field fp-cta-bar-link-field">
                                        <label><?php esc_html_e('Label ITA', 'fp-cta-bar'); ?></label>
                                        <input type="text" name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][label_it]" value="<?php echo esc_attr($link['label_it']); ?>">
                                    </div>
                                    <div class="fpctabar-link-field fp-cta-bar-link-field">
                                        <label><?php esc_html_e('Label ENG', 'fp-cta-bar'); ?></label>
                                        <input type="text" name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][label_en]" value="<?php echo esc_attr($link['label_en']); ?>">
                                    </div>
                                    <div class="fpctabar-link-field fp-cta-bar-link-field">
                                        <label><?php esc_html_e('URL ITA', 'fp-cta-bar'); ?></label>
                                        <input type="url" name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][url_it]" value="<?php echo esc_attr($link['url_it']); ?>">
                                    </div>
                                    <div class="fpctabar-link-field fp-cta-bar-link-field">
                                        <label><?php esc_html_e('URL ENG', 'fp-cta-bar'); ?></label>
                                        <input type="url" name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][url_en]" value="<?php echo esc_attr($link['url_en']); ?>">
                                    </div>
                                    <div class="fpctabar-link-field fpctabar-link-field--small fp-cta-bar-link-field fp-cta-bar-link-field--small">
                                        <label><?php esc_html_e('Target', 'fp-cta-bar'); ?></label>
                                        <select name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][target]">
                                            <option value="_blank" <?php selected($link['target'], '_blank'); ?>>_blank</option>
                                            <option value="_self" <?php selected($link['target'], '_self'); ?>>_self</option>
                                        </select>
                                    </div>
                                    <div class="fpctabar-link-field fpctabar-link-field--tracking fp-cta-bar-link-field fp-cta-bar-link-field--tracking">
                                        <label style="display:flex;align-items:center;gap:6px;font-weight:600;color:var(--fpdms-info);">
                                            <input type="checkbox" name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][track]" value="1" <?php checked(!empty($link['track'])); ?>>
                                            &#128202; <?php esc_html_e('Traccia click', 'fp-cta-bar'); ?>
                                        </label>
                                        <div class="fp-cta-bar-track-fields" style="<?php echo empty($link['track']) ? 'display:none;' : ''; ?>margin-top:6px;">
                                            <input type="text" name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][track_label]" value="<?php echo esc_attr($link['track_label'] ?? ''); ?>" placeholder="<?php esc_attr_e('Label evento (es. Prenota Tavolo)', 'fp-cta-bar'); ?>" style="margin-bottom:4px;">
                                            <input type="text" name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][track_category]" value="<?php echo esc_attr($link['track_category'] ?? ''); ?>" placeholder="<?php esc_attr_e('Categoria (es. prenotazione, contatto)', 'fp-cta-bar'); ?>">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="fpctabar-btn fpctabar-btn-secondary fp-cta-bar-link-duplicate" title="<?php esc_attr_e('Duplica', 'fp-cta-bar'); ?>"><?php esc_html_e('Duplica', 'fp-cta-bar'); ?></button>
                                <button type="button" class="fpctabar-link-remove fp-cta-bar-link-remove" title="<?php esc_attr_e('Rimuovi', 'fp-cta-bar'); ?>">&times;</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <p style="margin-top: 12px;">
                    <button type="button" class="fpctabar-btn fpctabar-btn-secondary" id="fp-cta-bar-add-link">
                        + <?php esc_html_e('Aggiungi link', 'fp-cta-bar'); ?>
                    </button>
                </p>

                <!-- Hidden template for JS -->
                <script type="text/html" id="tmpl-fp-cta-bar-link-row">
                    <div class="fpctabar-link-row fp-cta-bar-link-row" data-index="{{INDEX}}">
                        <div class="fpctabar-link-handle fp-cta-bar-link-handle" title="<?php esc_attr_e('Trascina per riordinare', 'fp-cta-bar'); ?>">&#9776;</div>
                        <div class="fpctabar-link-fields fp-cta-bar-link-fields">
                            <div class="fpctabar-link-field fpctabar-link-field--icon fp-cta-bar-link-field fp-cta-bar-link-field--icon">
                                <label><?php esc_html_e('Icona', 'fp-cta-bar'); ?></label>
                                <input type="text" name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][icon]" value="" placeholder="dashicons dashicons-...">
                            </div>
                            <div class="fpctabar-link-field fp-cta-bar-link-field">
                                <label><?php esc_html_e('Label ITA', 'fp-cta-bar'); ?></label>
                                <input type="text" name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][label_it]" value="">
                            </div>
                            <div class="fpctabar-link-field fp-cta-bar-link-field">
                                <label><?php esc_html_e('Label ENG', 'fp-cta-bar'); ?></label>
                                <input type="text" name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][label_en]" value="">
                            </div>
                            <div class="fpctabar-link-field fp-cta-bar-link-field">
                                <label><?php esc_html_e('URL ITA', 'fp-cta-bar'); ?></label>
                                <input type="url" name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][url_it]" value="">
                            </div>
                            <div class="fpctabar-link-field fp-cta-bar-link-field">
                                <label><?php esc_html_e('URL ENG', 'fp-cta-bar'); ?></label>
                                <input type="url" name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][url_en]" value="">
                            </div>
                            <div class="fpctabar-link-field fpctabar-link-field--small fp-cta-bar-link-field fp-cta-bar-link-field--small">
                                <label><?php esc_html_e('Target', 'fp-cta-bar'); ?></label>
                                <select name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][target]">
                                    <option value="_blank">_blank</option>
                                    <option value="_self">_self</option>
                                </select>
                            </div>
                            <div class="fpctabar-link-field fpctabar-link-field--tracking fp-cta-bar-link-field fp-cta-bar-link-field--tracking">
                                <label style="display:flex;align-items:center;gap:6px;font-weight:600;color:var(--fpdms-info);">
                                    <input type="checkbox" name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][track]" value="1" class="fp-cta-bar-track-toggle">
                                    &#128202; <?php esc_html_e('Traccia click', 'fp-cta-bar'); ?>
                                </label>
                                <div class="fp-cta-bar-track-fields" style="display:none;margin-top:6px;">
                                    <input type="text" name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][track_label]" value="" placeholder="<?php esc_attr_e('Label evento (es. Prenota Tavolo)', 'fp-cta-bar'); ?>" style="margin-bottom:4px;">
                                    <input type="text" name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][track_category]" value="" placeholder="<?php esc_attr_e('Categoria (es. prenotazione, contatto)', 'fp-cta-bar'); ?>">
                                </div>
                            </div>
                        </div>
                        <button type="button" class="fpctabar-btn fpctabar-btn-secondary fp-cta-bar-link-duplicate" title="<?php esc_attr_e('Duplica', 'fp-cta-bar'); ?>"><?php esc_html_e('Duplica', 'fp-cta-bar'); ?></button>
                        <button type="button" class="fpctabar-link-remove fp-cta-bar-link-remove" title="<?php esc_attr_e('Rimuovi', 'fp-cta-bar'); ?>">&times;</button>
                    </div>
                </script>
            </div>
        </div>

        <!-- Card: Import/Export -->
        <div class="fpctabar-card">
            <div class="fpctabar-card-header">
                <div class="fpctabar-card-header-left">
                    <span class="dashicons dashicons-database-export"></span>
                    <h2><?php esc_html_e('Importa / Esporta', 'fp-cta-bar'); ?></h2>
                </div>
            </div>
            <div class="fpctabar-card-body">
                <a href="<?php echo esc_url(add_query_arg(['action' => 'fp_cta_bar_export', 'nonce' => wp_create_nonce('fp_cta_bar_export')], admin_url('admin-ajax.php'))); ?>" class="fpctabar-btn fpctabar-btn-secondary" id="fp-cta-bar-export" target="_blank">
                    <span class="dashicons dashicons-download"></span> <?php esc_html_e('Esporta JSON', 'fp-cta-bar'); ?>
                </a>
                <span style="margin-left: 12px; display: inline-flex; align-items: center; gap: 8px;">
                    <input type="file" id="fp-cta-bar-import-file" accept=".json">
                    <button type="button" class="fpctabar-btn fpctabar-btn-secondary" id="fp-cta-bar-import"><?php esc_html_e('Importa JSON', 'fp-cta-bar'); ?></button>
                </span>
            </div>
        </div>

        <!-- Card: CSS personalizzato -->
        <div class="fpctabar-card">
            <div class="fpctabar-card-header">
                <div class="fpctabar-card-header-left">
                    <span class="dashicons dashicons-editor-code"></span>
                    <h2><?php esc_html_e('CSS personalizzato', 'fp-cta-bar'); ?></h2>
                </div>
            </div>
            <div class="fpctabar-card-body">
                <div class="fpctabar-field">
                    <label><?php esc_html_e('CSS', 'fp-cta-bar'); ?></label>
                    <textarea name="<?php echo esc_attr($opt); ?>[custom_css]" rows="8" class="large-text code"><?php echo esc_textarea($settings['custom_css'] ?? ''); ?></textarea>
                    <span class="fpctabar-hint"><?php esc_html_e('CSS aggiuntivo per personalizzare la barra. Usa selettori come #fpctabar o .fpctabar', 'fp-cta-bar'); ?></span>
                </div>
            </div>
        </div>

        <!-- Card: Anteprima -->
        <div class="fpctabar-card">
            <div class="fpctabar-card-header">
                <div class="fpctabar-card-header-left">
                    <span class="dashicons dashicons-visibility"></span>
                    <h2><?php esc_html_e('Anteprima', 'fp-cta-bar'); ?></h2>
                </div>
            </div>
            <div class="fpctabar-card-body">
                <div id="fp-cta-bar-preview" class="fpctabar-preview-wrap">
                    <div class="fpctabar fpctabar--preview fpctabar--fullwidth" id="fp-cta-bar-preview-box">
                        <div class="fpctabar__bar">
                            <span class="fpctabar__label" id="fp-cta-bar-preview-label"><?php echo esc_html($settings['main_label_it'] ?? 'PRENOTA'); ?></span>
                            <span class="fpctabar__arrow">&#9650;</span>
                        </div>
                        <div class="fpctabar__panel fpctabar--open">
                            <a href="#"><?php echo esc_html($settings['links'][0]['label_it'] ?? 'LINK'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <p style="margin-top: 24px;">
            <button type="submit" class="fpctabar-btn fpctabar-btn-primary">
                <span class="dashicons dashicons-saved"></span> <?php esc_html_e('Salva modifiche', 'fp-cta-bar'); ?>
            </button>
        </p>
    </form>
</div>
<script>
(function($){
    $(document).on('change', '.fp-cta-bar-link-field--tracking input[type="checkbox"]', function(){
        $(this).closest('.fp-cta-bar-link-field--tracking').find('.fp-cta-bar-track-fields').toggle(this.checked);
    });
})(jQuery);
</script>
