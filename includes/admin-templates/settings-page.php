<?php
if (!defined('ABSPATH')) exit;

$settings = \FP\CtaBar\Plugin::get_settings();
$opt      = \FP\CtaBar\Plugin::OPTION_KEY;
?>
<div class="wrap fp-cta-bar-admin">
    <h1><?php esc_html_e('FP CTA Bar', 'fp-cta-bar'); ?></h1>

    <form method="post" action="options.php">
        <?php settings_fields('fp_cta_bar_group'); ?>

        <!-- Display Mode -->
        <h2><?php esc_html_e('Modalità di visualizzazione', 'fp-cta-bar'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e('Layout', 'fp-cta-bar'); ?></th>
                <td>
                    <fieldset class="fp-cta-bar-modes">
                        <?php
                        $modes = [
                            'full-width'   => __('Full-width (barra in basso)', 'fp-cta-bar'),
                            'button-left'  => __('Bottone in basso a sinistra', 'fp-cta-bar'),
                            'button-right' => __('Bottone in basso a destra', 'fp-cta-bar'),
                        ];
                        foreach ($modes as $value => $label) : ?>
                            <label class="fp-cta-bar-mode-option">
                                <input type="radio"
                                       name="<?php echo esc_attr($opt); ?>[display_mode]"
                                       value="<?php echo esc_attr($value); ?>"
                                       <?php checked($settings['display_mode'], $value); ?>>
                                <span class="fp-cta-bar-mode-preview fp-cta-bar-mode-preview--<?php echo esc_attr($value); ?>">
                                    <span class="fp-cta-bar-mode-icon"></span>
                                </span>
                                <span class="fp-cta-bar-mode-label"><?php echo esc_html($label); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </fieldset>
                </td>
            </tr>
        </table>

        <!-- Labels -->
        <h2><?php esc_html_e('Bottone / Barra principale (PRENOTA)', 'fp-cta-bar'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e('Testo ITA', 'fp-cta-bar'); ?></th>
                <td>
                    <input type="text"
                           name="<?php echo esc_attr($opt); ?>[main_label_it]"
                           value="<?php echo esc_attr($settings['main_label_it']); ?>"
                           class="regular-text"
                           placeholder="PRENOTA">
                    <p class="description"><?php esc_html_e('Personalizza il testo del bottone in italiano.', 'fp-cta-bar'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Testo ENG', 'fp-cta-bar'); ?></th>
                <td>
                    <input type="text"
                           name="<?php echo esc_attr($opt); ?>[main_label_en]"
                           value="<?php echo esc_attr($settings['main_label_en']); ?>"
                           class="regular-text"
                           placeholder="BOOK NOW">
                    <p class="description"><?php esc_html_e('Personalizza il testo del bottone in inglese.', 'fp-cta-bar'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Icona principale', 'fp-cta-bar'); ?></th>
                <td>
                    <input type="text"
                           name="<?php echo esc_attr($opt); ?>[main_icon]"
                           value="<?php echo esc_attr($settings['main_icon'] ?? ''); ?>"
                           class="regular-text"
                           placeholder="dashicons dashicons-calendar-alt oppure URL immagine">
                    <p class="description"><?php esc_html_e('Classe CSS (es. dashicons dashicons-calendar-alt) oppure URL immagine. Vuoto = nessuna icona.', 'fp-cta-bar'); ?></p>
                </td>
            </tr>
        </table>

        <!-- Colors -->
        <h2><?php esc_html_e('Colori', 'fp-cta-bar'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e('Sfondo barra/bottone', 'fp-cta-bar'); ?></th>
                <td><input type="text" name="<?php echo esc_attr($opt); ?>[bg_color]" value="<?php echo esc_attr($settings['bg_color']); ?>" class="fp-cta-bar-color"></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Testo', 'fp-cta-bar'); ?></th>
                <td><input type="text" name="<?php echo esc_attr($opt); ?>[text_color]" value="<?php echo esc_attr($settings['text_color']); ?>" class="fp-cta-bar-color"></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Bordo', 'fp-cta-bar'); ?></th>
                <td><input type="text" name="<?php echo esc_attr($opt); ?>[border_color]" value="<?php echo esc_attr($settings['border_color']); ?>" class="fp-cta-bar-color"></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Sfondo pannello link', 'fp-cta-bar'); ?></th>
                <td><input type="text" name="<?php echo esc_attr($opt); ?>[panel_bg_color]" value="<?php echo esc_attr($settings['panel_bg_color']); ?>" class="fp-cta-bar-color"></td>
            </tr>
        </table>

        <!-- Visibilità e comportamento -->
        <h2><?php esc_html_e('Visibilità e comportamento', 'fp-cta-bar'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e('Z-index', 'fp-cta-bar'); ?></th>
                <td><input type="number" name="<?php echo esc_attr($opt); ?>[z_index]" value="<?php echo esc_attr($settings['z_index'] ?? 99999); ?>" min="1" max="2147483647" class="small-text"></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Mostra su', 'fp-cta-bar'); ?></th>
                <td>
                    <?php
                    $vis = $settings['visibility'] ?? ['home', 'single', 'page', 'archive'];
                    $vis_opts = [
                        'home'    => __('Homepage', 'fp-cta-bar'),
                        'single'  => __('Singoli post', 'fp-cta-bar'),
                        'page'    => __('Pagine', 'fp-cta-bar'),
                        'archive' => __('Archivi (categorie, tag)', 'fp-cta-bar'),
                        'search'  => __('Risultati di ricerca', 'fp-cta-bar'),
                        '404'     => __('Pagina 404', 'fp-cta-bar'),
                    ];
                    foreach ($vis_opts as $v => $l) : ?>
                        <label style="display:inline-block;margin-right:15px;">
                            <input type="checkbox" name="<?php echo esc_attr($opt); ?>[visibility][]" value="<?php echo esc_attr($v); ?>" <?php checked(in_array($v, $vis, true)); ?>>
                            <?php echo esc_html($l); ?>
                        </label>
                    <?php endforeach; ?>
                    <p class="description"><?php esc_html_e('Seleziona le pagine dove mostrare la barra.', 'fp-cta-bar'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Dispositivo', 'fp-cta-bar'); ?></th>
                <td>
                    <select name="<?php echo esc_attr($opt); ?>[device_visibility]">
                        <option value="all" <?php selected($settings['device_visibility'] ?? 'all', 'all'); ?>><?php esc_html_e('Tutti', 'fp-cta-bar'); ?></option>
                        <option value="mobile" <?php selected($settings['device_visibility'] ?? 'all', 'mobile'); ?>><?php esc_html_e('Solo mobile', 'fp-cta-bar'); ?></option>
                        <option value="desktop" <?php selected($settings['device_visibility'] ?? 'all', 'desktop'); ?>><?php esc_html_e('Solo desktop', 'fp-cta-bar'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Dimensione font', 'fp-cta-bar'); ?></th>
                <td>
                    <select name="<?php echo esc_attr($opt); ?>[font_size]">
                        <option value="small" <?php selected($settings['font_size'] ?? 'medium', 'small'); ?>><?php esc_html_e('Piccolo', 'fp-cta-bar'); ?></option>
                        <option value="medium" <?php selected($settings['font_size'] ?? 'medium', 'medium'); ?>><?php esc_html_e('Medio', 'fp-cta-bar'); ?></option>
                        <option value="large" <?php selected($settings['font_size'] ?? 'medium', 'large'); ?>><?php esc_html_e('Grande', 'fp-cta-bar'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Border radius bottone (px)', 'fp-cta-bar'); ?></th>
                <td><input type="number" name="<?php echo esc_attr($opt); ?>[button_radius]" value="<?php echo esc_attr($settings['button_radius'] ?? 4); ?>" min="0" max="24" class="small-text"></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Chiudi al click sul link', 'fp-cta-bar'); ?></th>
                <td><label><input type="checkbox" name="<?php echo esc_attr($opt); ?>[close_on_link_click]" value="1" <?php checked(!empty($settings['close_on_link_click'])); ?>> <?php esc_html_e('Chiudi il pannello quando si clicca un link', 'fp-cta-bar'); ?></label></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Ritardo comparsa (sec)', 'fp-cta-bar'); ?></th>
                <td><input type="number" name="<?php echo esc_attr($opt); ?>[delay_seconds]" value="<?php echo esc_attr($settings['delay_seconds'] ?? 0); ?>" min="0" max="10" class="small-text"> (0 = immediato)</td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Mostra dopo scroll (%)', 'fp-cta-bar'); ?></th>
                <td><input type="number" name="<?php echo esc_attr($opt); ?>[show_after_scroll_percent]" value="<?php echo esc_attr($settings['show_after_scroll_percent'] ?? 0); ?>" min="0" max="100" class="small-text"> (0 = disabilitato)</td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Pannello aperto di default', 'fp-cta-bar'); ?></th>
                <td><label><input type="checkbox" name="<?php echo esc_attr($opt); ?>[panel_open_by_default]" value="1" <?php checked(!empty($settings['panel_open_by_default'])); ?>> <?php esc_html_e('Mostra il pannello link aperto all\'avvio', 'fp-cta-bar'); ?></label></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Nascondi dopo chiusura (ore)', 'fp-cta-bar'); ?></th>
                <td><input type="number" name="<?php echo esc_attr($opt); ?>[hide_after_dismiss_hours]" value="<?php echo esc_attr($settings['hide_after_dismiss_hours'] ?? 0); ?>" min="0" max="168" class="small-text"> (0 = disabilitato, max 168)</td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Animazione', 'fp-cta-bar'); ?></th>
                <td>
                    <select name="<?php echo esc_attr($opt); ?>[animation]">
                        <option value="none" <?php selected($settings['animation'] ?? 'slide', 'none'); ?>><?php esc_html_e('Nessuna', 'fp-cta-bar'); ?></option>
                        <option value="slide" <?php selected($settings['animation'] ?? 'slide', 'slide'); ?>><?php esc_html_e('Slide', 'fp-cta-bar'); ?></option>
                        <option value="fade" <?php selected($settings['animation'] ?? 'slide', 'fade'); ?>><?php esc_html_e('Fade', 'fp-cta-bar'); ?></option>
                        <option value="bounce" <?php selected($settings['animation'] ?? 'slide', 'bounce'); ?>><?php esc_html_e('Bounce', 'fp-cta-bar'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Solo shortcode', 'fp-cta-bar'); ?></th>
                <td><label><input type="checkbox" name="<?php echo esc_attr($opt); ?>[use_shortcode]" value="1" <?php checked(!empty($settings['use_shortcode'])); ?>> <?php esc_html_e('Mostra la barra solo dove inserisci [fp_cta_bar]', 'fp-cta-bar'); ?></label></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Cookie consent richiesto', 'fp-cta-bar'); ?></th>
                <td><label><input type="checkbox" name="<?php echo esc_attr($opt); ?>[cookie_consent_required]" value="1" <?php checked(!empty($settings['cookie_consent_required'])); ?>> <?php esc_html_e('Nascondi finché l\'utente non ha accettato i cookie', 'fp-cta-bar'); ?></label><p class="description"><?php esc_html_e('Supporta Cookie Law Info, filtri custom via fp_cta_bar_can_show', 'fp-cta-bar'); ?></p></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Tracking', 'fp-cta-bar'); ?></th>
                <td>
                    <p><strong><?php esc_html_e('Google Analytics 4', 'fp-cta-bar'); ?></strong></p>
                    <label><input type="checkbox" name="<?php echo esc_attr($opt); ?>[ga4_enabled]" value="1" <?php checked(!empty($settings['ga4_enabled'])); ?>> <?php esc_html_e('Invia eventi GA4 (richiede gtag)', 'fp-cta-bar'); ?></label>
                    <p class="description">
                        <?php esc_html_e('Nome evento:', 'fp-cta-bar'); ?>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[ga4_event_name]" value="<?php echo esc_attr($settings['ga4_event_name'] ?? 'cta_bar_click'); ?>" class="small-text">
                    </p>
                    <p><strong><?php esc_html_e('Google Tag Manager', 'fp-cta-bar'); ?></strong></p>
                    <label><input type="checkbox" name="<?php echo esc_attr($opt); ?>[gtm_enabled]" value="1" <?php checked(!empty($settings['gtm_enabled'])); ?>> <?php esc_html_e('Push su dataLayer (GTM)', 'fp-cta-bar'); ?></label>
                    <p class="description">
                        <?php esc_html_e('Nome evento:', 'fp-cta-bar'); ?>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[gtm_event_name]" value="<?php echo esc_attr($settings['gtm_event_name'] ?? 'cta_bar_click'); ?>" class="small-text">
                        (event + cta_bar_action, cta_bar_label)
                    </p>
                    <p><strong><?php esc_html_e('Meta (Facebook Pixel)', 'fp-cta-bar'); ?></strong></p>
                    <label><input type="checkbox" name="<?php echo esc_attr($opt); ?>[meta_enabled]" value="1" <?php checked(!empty($settings['meta_enabled'])); ?>> <?php esc_html_e('trackCustom (richiede fbq)', 'fp-cta-bar'); ?></label>
                    <p class="description">
                        <?php esc_html_e('Nome evento:', 'fp-cta-bar'); ?>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[meta_event_name]" value="<?php echo esc_attr($settings['meta_event_name'] ?? 'cta_bar_click'); ?>" class="small-text">
                    </p>
                </td>
            </tr>
        </table>

        <!-- Dynamic Links Repeater -->
        <h2><?php esc_html_e('Link', 'fp-cta-bar'); ?></h2>

        <div id="fp-cta-bar-links">
            <?php if (!empty($settings['links'])) : ?>
                <?php foreach ($settings['links'] as $i => $link) : ?>
                    <div class="fp-cta-bar-link-row" data-index="<?php echo (int) $i; ?>">
                        <div class="fp-cta-bar-link-handle" title="<?php esc_attr_e('Trascina per riordinare', 'fp-cta-bar'); ?>">&#9776;</div>
                        <div class="fp-cta-bar-link-fields">
                            <div class="fp-cta-bar-link-field fp-cta-bar-link-field--icon">
                                <label><?php esc_html_e('Icona', 'fp-cta-bar'); ?></label>
                                <input type="text" name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][icon]" value="<?php echo esc_attr($link['icon'] ?? ''); ?>" placeholder="dashicons dashicons-...">
                            </div>
                            <div class="fp-cta-bar-link-field">
                                <label><?php esc_html_e('Label ITA', 'fp-cta-bar'); ?></label>
                                <input type="text" name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][label_it]" value="<?php echo esc_attr($link['label_it']); ?>">
                            </div>
                            <div class="fp-cta-bar-link-field">
                                <label><?php esc_html_e('Label ENG', 'fp-cta-bar'); ?></label>
                                <input type="text" name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][label_en]" value="<?php echo esc_attr($link['label_en']); ?>">
                            </div>
                            <div class="fp-cta-bar-link-field">
                                <label><?php esc_html_e('URL ITA', 'fp-cta-bar'); ?></label>
                                <input type="url" name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][url_it]" value="<?php echo esc_attr($link['url_it']); ?>">
                            </div>
                            <div class="fp-cta-bar-link-field">
                                <label><?php esc_html_e('URL ENG', 'fp-cta-bar'); ?></label>
                                <input type="url" name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][url_en]" value="<?php echo esc_attr($link['url_en']); ?>">
                            </div>
                            <div class="fp-cta-bar-link-field fp-cta-bar-link-field--small">
                                <label><?php esc_html_e('Target', 'fp-cta-bar'); ?></label>
                                <select name="<?php echo esc_attr($opt); ?>[links][<?php echo (int) $i; ?>][target]">
                                    <option value="_blank" <?php selected($link['target'], '_blank'); ?>>_blank</option>
                                    <option value="_self" <?php selected($link['target'], '_self'); ?>>_self</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" class="button fp-cta-bar-link-duplicate" title="<?php esc_attr_e('Duplica', 'fp-cta-bar'); ?>"><?php esc_html_e('Duplica', 'fp-cta-bar'); ?></button>
                        <button type="button" class="button fp-cta-bar-link-remove" title="<?php esc_attr_e('Rimuovi', 'fp-cta-bar'); ?>">&times;</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <p>
            <button type="button" class="button button-secondary" id="fp-cta-bar-add-link">
                + <?php esc_html_e('Aggiungi link', 'fp-cta-bar'); ?>
            </button>
        </p>

        <!-- Hidden template for JS -->
        <script type="text/html" id="tmpl-fp-cta-bar-link-row">
            <div class="fp-cta-bar-link-row" data-index="{{INDEX}}">
                <div class="fp-cta-bar-link-handle" title="<?php esc_attr_e('Trascina per riordinare', 'fp-cta-bar'); ?>">&#9776;</div>
                <div class="fp-cta-bar-link-fields">
                    <div class="fp-cta-bar-link-field fp-cta-bar-link-field--icon">
                        <label><?php esc_html_e('Icona', 'fp-cta-bar'); ?></label>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][icon]" value="" placeholder="dashicons dashicons-...">
                    </div>
                    <div class="fp-cta-bar-link-field">
                        <label><?php esc_html_e('Label ITA', 'fp-cta-bar'); ?></label>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][label_it]" value="">
                    </div>
                    <div class="fp-cta-bar-link-field">
                        <label><?php esc_html_e('Label ENG', 'fp-cta-bar'); ?></label>
                        <input type="text" name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][label_en]" value="">
                    </div>
                    <div class="fp-cta-bar-link-field">
                        <label><?php esc_html_e('URL ITA', 'fp-cta-bar'); ?></label>
                        <input type="url" name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][url_it]" value="">
                    </div>
                    <div class="fp-cta-bar-link-field">
                        <label><?php esc_html_e('URL ENG', 'fp-cta-bar'); ?></label>
                        <input type="url" name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][url_en]" value="">
                    </div>
                    <div class="fp-cta-bar-link-field fp-cta-bar-link-field--small">
                        <label><?php esc_html_e('Target', 'fp-cta-bar'); ?></label>
                        <select name="<?php echo esc_attr($opt); ?>[links][{{INDEX}}][target]">
                            <option value="_blank">_blank</option>
                            <option value="_self">_self</option>
                        </select>
                    </div>
                </div>
                <button type="button" class="button fp-cta-bar-link-duplicate" title="<?php esc_attr_e('Duplica', 'fp-cta-bar'); ?>"><?php esc_html_e('Duplica', 'fp-cta-bar'); ?></button>
                <button type="button" class="button fp-cta-bar-link-remove" title="<?php esc_attr_e('Rimuovi', 'fp-cta-bar'); ?>">&times;</button>
            </div>
        </script>

        <!-- Import/Export -->
        <h2><?php esc_html_e('Importa / Esporta', 'fp-cta-bar'); ?></h2>
        <p>
            <a href="<?php echo esc_url(add_query_arg(['action' => 'fp_cta_bar_export', 'nonce' => wp_create_nonce('fp_cta_bar_export')], admin_url('admin-ajax.php'))); ?>" class="button" id="fp-cta-bar-export" target="_blank"><?php esc_html_e('Esporta JSON', 'fp-cta-bar'); ?></a>
            <span class="fp-cta-bar-import-wrap" style="margin-left:10px;">
                <input type="file" id="fp-cta-bar-import-file" accept=".json">
                <button type="button" class="button" id="fp-cta-bar-import"><?php esc_html_e('Importa JSON', 'fp-cta-bar'); ?></button>
            </span>
        </p>

        <!-- Custom CSS -->
        <h2><?php esc_html_e('CSS personalizzato', 'fp-cta-bar'); ?></h2>
        <table class="form-table">
            <tr>
                <th scope="row"><?php esc_html_e('CSS', 'fp-cta-bar'); ?></th>
                <td>
                    <textarea name="<?php echo esc_attr($opt); ?>[custom_css]" rows="8" class="large-text code"><?php echo esc_textarea($settings['custom_css'] ?? ''); ?></textarea>
                    <p class="description"><?php esc_html_e('CSS aggiuntivo per personalizzare la barra. Usa selettori come #fpctabar o .fpctabar', 'fp-cta-bar'); ?></p>
                </td>
            </tr>
        </table>

        <!-- Preview -->
        <h2><?php esc_html_e('Anteprima', 'fp-cta-bar'); ?></h2>
        <div id="fp-cta-bar-preview" class="fp-cta-bar-preview-wrap">
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

        <?php submit_button(); ?>
    </form>
</div>
