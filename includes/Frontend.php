<?php

namespace FP\CtaBar;

class Frontend {

    private static $instance = null;
    private $settings = [];

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->settings = Plugin::get_settings();

        if (empty($this->settings['links'])) {
            return;
        }

        $use_shortcode = !empty($this->settings['use_shortcode']);
        if ($use_shortcode) {
            return;
        }

        if (!$this->should_show()) {
            return;
        }

        if ($this->cookie_consent_blocked()) {
            return;
        }

        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_footer', [$this, 'render']);
    }

    private function cookie_consent_blocked() {
        if (empty($this->settings['cookie_consent_required'])) {
            return false;
        }
        $can_show = apply_filters('fp_cta_bar_can_show', null);
        if ($can_show === true) {
            return false;
        }
        if ($can_show === false) {
            return true;
        }
        if (!empty($_COOKIE['cookie_accepted']) || !empty($_COOKIE['CookieConsent']) || (isset($_COOKIE['cookielawinfo-checkbox-necessary']) && $_COOKIE['cookielawinfo-checkbox-necessary'] === 'yes')) {
            return false;
        }
        return true;
    }

    public static function render_for_shortcode() {
        $instance = self::get_instance();
        if (empty($instance->settings['use_shortcode'])) {
            return;
        }
        if (empty($instance->settings['links'])) {
            return;
        }
        if ($instance->cookie_consent_blocked()) {
            return;
        }
        $instance->enqueue_assets();
        $instance->render();
    }

    private function should_show() {
        $visibility = $this->settings['visibility'] ?? ['home', 'single', 'page', 'archive'];
        if (empty($visibility)) {
            return true;
        }

        if (is_front_page() && in_array('home', $visibility, true)) {
            return true;
        }
        if (is_singular('post') && in_array('single', $visibility, true)) {
            return true;
        }
        if (is_singular('page') && in_array('page', $visibility, true)) {
            return true;
        }
        if (is_archive() && in_array('archive', $visibility, true)) {
            return true;
        }
        if (is_search() && in_array('search', $visibility, true)) {
            return true;
        }
        if (is_404() && in_array('404', $visibility, true)) {
            return true;
        }

        return false;
    }

    public function enqueue_assets() {
        if ($this->uses_dashicons()) {
            wp_enqueue_style('dashicons');
        }

        wp_enqueue_style(
            'fp-cta-bar-front',
            FP_CTA_BAR_URL . 'assets/css/frontend.css',
            [],
            FP_CTA_BAR_VERSION
        );

        wp_enqueue_script(
            'fp-cta-bar-front',
            FP_CTA_BAR_URL . 'assets/js/frontend.js',
            [],
            FP_CTA_BAR_VERSION,
            true
        );

        // Tracking delegated to FP-Marketing-Tracking-Layer via fpCtaBarClick DOM event
        wp_localize_script('fp-cta-bar-front', 'fpCtaBarTrack', [
            'eventName' => $this->settings['gtm_event_name'] ?? $this->settings['ga4_event_name'] ?? 'cta_bar_click',
            'useFpLayer' => true,
        ]);
    }

    private function uses_dashicons() {
        $main = $this->settings['main_icon'] ?? '';
        if (stripos($main, 'dashicons') !== false) {
            return true;
        }
        foreach ($this->settings['links'] ?? [] as $link) {
            if (!empty($link['icon']) && stripos($link['icon'], 'dashicons') !== false) {
                return true;
            }
        }
        return false;
    }

    private function detect_lang() {
        if (defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE) {
            return substr(ICL_LANGUAGE_CODE, 0, 2);
        }
        if (function_exists('pll_current_language') && pll_current_language()) {
            return substr(pll_current_language(), 0, 2);
        }
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        if (preg_match('#/en(?:/|$)#', $uri)) {
            return 'en';
        }
        $locale = get_locale();
        return $locale ? substr($locale, 0, 2) : 'it';
    }

    public function render() {
        $s    = $this->settings;
        $lang = $this->detect_lang();
        $mode = $s['display_mode'];

        $main_label = ($lang === 'en') ? $s['main_label_en'] : $s['main_label_it'];
        $main_icon = trim($s['main_icon'] ?? '');

        $links_html = $this->build_links_html($lang);
        if (empty($links_html)) {
            return;
        }

        $font_sizes = [
            'small'  => ['label' => '14px', 'link' => '13px'],
            'medium' => ['label' => '18px', 'link' => '15px'],
            'large'  => ['label' => '22px', 'link' => '17px'],
        ];
        $fs = $font_sizes[$s['font_size'] ?? 'medium'] ?? $font_sizes['medium'];

        $css_vars = sprintf(
            '--fpctabar-bg:%s;--fpctabar-text:%s;--fpctabar-border:%s;--fpctabar-panel-bg:%s;--fpctabar-z:%d;--fpctabar-font-size-label:%s;--fpctabar-font-size-link:%s;--fpctabar-btn-radius:%dpx;',
            esc_attr($s['bg_color']),
            esc_attr($s['text_color']),
            esc_attr($s['border_color']),
            esc_attr($s['panel_bg_color']),
            (int) ($s['z_index'] ?? 99999),
            esc_attr($fs['label']),
            esc_attr($fs['link']),
            (int) ($s['button_radius'] ?? 4)
        );

        $device_class = '';
        $dv = $s['device_visibility'] ?? 'all';
        if ($dv === 'mobile') {
            $device_class = ' fpctabar--mobile-only';
        } elseif ($dv === 'desktop') {
            $device_class = ' fpctabar--desktop-only';
        }

        $close_on_click = !empty($s['close_on_link_click']);
        $data_attrs = sprintf(
            'data-delay="%d" data-scroll-percent="%d" data-panel-open="%s" data-dismiss-hours="%d" data-animation="%s" data-aria-open="%s" data-aria-closed="%s"',
            (int) ($s['delay_seconds'] ?? 0),
            (int) ($s['show_after_scroll_percent'] ?? 0),
            !empty($s['panel_open_by_default']) ? '1' : '0',
            (int) ($s['hide_after_dismiss_hours'] ?? 0),
            esc_attr($s['animation'] ?? 'slide'),
            esc_attr(__('Pannello aperto', 'fp-cta-bar')),
            esc_attr(__('Pannello chiuso', 'fp-cta-bar'))
        );

        if ($mode === 'full-width') {
            $this->render_fullwidth($main_label, $main_icon, $links_html, $css_vars, $device_class, $close_on_click, $data_attrs);
        } else {
            $position = ($mode === 'button-left') ? 'left' : 'right';
            $this->render_button($main_label, $main_icon, $links_html, $css_vars, $position, $device_class, $close_on_click, $data_attrs);
        }

        $custom_css = trim($s['custom_css'] ?? '');
        if ($custom_css !== '') {
            printf('<style id="fpctabar-custom">%s</style>', esc_html($custom_css));
        }
    }

    private function icon_html($icon) {
        $icon = trim((string) ($icon ?? ''));
        if ($icon === '') {
            return '';
        }
        if (preg_match('#^(https?:|/|data:)#i', $icon)) {
            return '<span class="fpctabar__icon fpctabar__icon--img"><img src="' . esc_url($icon) . '" alt="" aria-hidden="true"></span>';
        }
        return '<span class="fpctabar__icon fpctabar__icon--class ' . esc_attr($icon) . '" aria-hidden="true"></span>';
    }

    private function build_links_html($lang) {
        $html = '';
        foreach ($this->settings['links'] as $link) {
            $url   = ($lang === 'en') ? ($link['url_en'] ?? $link['url_it'] ?? '') : ($link['url_it'] ?? $link['url_en'] ?? '');
            $label = ($lang === 'en') ? ($link['label_en'] ?? $link['label_it'] ?? '') : ($link['label_it'] ?? $link['label_en'] ?? '');
            $target = (isset($link['target']) && $link['target'] === '_self') ? '_self' : '_blank';
            $rel = ($target === '_blank') ? 'noopener noreferrer' : '';
            $icon = $this->icon_html($link['icon'] ?? '');

            if (empty($url) || empty($label)) {
                continue;
            }

            // Tracking data attributes (only if tracking is enabled for this link)
            $track_attrs = '';
            if (!empty($link['track'])) {
                $track_label    = !empty($link['track_label']) ? $link['track_label'] : $label;
                $track_category = !empty($link['track_category']) ? $link['track_category'] : '';
                $track_attrs = sprintf(
                    ' data-fp-track="1" data-fp-track-label="%s" data-fp-track-category="%s"',
                    esc_attr($track_label),
                    esc_attr($track_category)
                );
            }

            $inner = $icon . esc_html($label);
            $html .= sprintf(
                '<a href="%s" target="%s" rel="%s"%s>%s</a>',
                esc_url($url),
                esc_attr($target),
                esc_attr($rel),
                $track_attrs,
                $inner
            );
        }
        return $html;
    }

    private function render_fullwidth($label, $main_icon, $links_html, $css_vars, $device_class = '', $close_on_link_click = true, $data_attrs = '') {
        $icon_html = $this->icon_html($main_icon);
        ?>
        <div id="fpctabar" class="fpctabar fpctabar--fullwidth<?php echo esc_attr($device_class); ?>" style="<?php echo $css_vars; ?>" data-mode="fullwidth" data-close-on-link-click="<?php echo $close_on_link_click ? '1' : '0'; ?>" <?php echo $data_attrs; ?>>
            <span class="fpctabar__sr-only" aria-live="polite" aria-atomic="true" id="fpctabar-announcer"></span>
            <div class="fpctabar__bar" role="button" tabindex="0" aria-expanded="false" aria-controls="fpctabar-panel">
                <?php echo $icon_html; ?>
                <span class="fpctabar__label"><?php echo esc_html($label); ?></span>
                <span class="fpctabar__arrow">&#9650;</span>
            </div>
            <div id="fpctabar-panel" class="fpctabar__panel" aria-hidden="true">
                <?php echo $links_html; ?>
            </div>
        </div>
        <?php
    }

    private function render_button($label, $main_icon, $links_html, $css_vars, $position, $device_class = '', $close_on_link_click = true, $data_attrs = '') {
        $icon_html = $this->icon_html($main_icon);
        ?>
        <div id="fpctabar" class="fpctabar fpctabar--button fpctabar--<?php echo esc_attr($position); ?><?php echo esc_attr($device_class); ?>" style="<?php echo $css_vars; ?>" data-mode="button" data-close-on-link-click="<?php echo $close_on_link_click ? '1' : '0'; ?>" <?php echo $data_attrs; ?>>
            <span class="fpctabar__sr-only" aria-live="polite" aria-atomic="true" id="fpctabar-announcer"></span>
            <button class="fpctabar__btn" aria-expanded="false" aria-controls="fpctabar-panel">
                <?php echo $icon_html; ?>
                <span class="fpctabar__label"><?php echo esc_html($label); ?></span>
            </button>
            <div id="fpctabar-panel" class="fpctabar__panel" aria-hidden="true">
                <?php echo $links_html; ?>
            </div>
        </div>
        <?php
    }
}
