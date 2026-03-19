<?php
declare(strict_types=1);

namespace FP\CtaBar;

class Frontend {

    private static $instance = null;
    private $settings = [];
    private bool $bootstrapped = false;
    private bool $rendered = false;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->settings = Plugin::get_settings();
        add_action('wp', [$this, 'maybe_bootstrap']);
    }

    /**
     * Attiva il rendering automatico della barra quando il contesto WP e' disponibile.
     *
     * @return void
     */
    public function maybe_bootstrap(): void {
        if ($this->bootstrapped) {
            return;
        }
        $this->bootstrapped = true;

        if (empty($this->settings['links'])) {
            return;
        }

        if (!empty($this->settings['use_shortcode'])) {
            return;
        }

        if (!$this->should_show()) {
            return;
        }

        if ($this->cookie_consent_blocked()) {
            return;
        }

        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_body_open', [$this, 'render_once']);
        add_action('wp_footer', [$this, 'render_once']);
    }

    /**
     * Evita rendering duplicato se wp_body_open e wp_footer sono entrambi presenti.
     *
     * @return void
     */
    public function render_once(): void {
        if ($this->rendered) {
            return;
        }
        $this->rendered = true;
        $this->render();
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

    /**
     * Determina se la barra deve essere mostrata in base a visibilità e contesto.
     *
     * @return bool
     */
    private function should_show() {
        $today = gmdate('Y-m-d');
        $start = trim($this->settings['schedule_start'] ?? '');
        $end   = trim($this->settings['schedule_end'] ?? '');
        if ($start !== '' && $today < $start) {
            return false;
        }
        if ($end !== '' && $today > $end) {
            return false;
        }

        $context = [
            'is_front_page' => is_front_page(),
            'is_singular'   => is_singular(),
            'post_type'     => is_singular() ? get_post_type() : null,
            'is_archive'    => is_archive(),
            'is_search'     => is_search(),
            'is_404'        => is_404(),
        ];
        if (is_archive()) {
            $context['taxonomy'] = get_queried_object() instanceof \WP_Term ? get_queried_object()->taxonomy : null;
            $context['term_id']  = get_queried_object() instanceof \WP_Term ? get_queried_object()->term_id : null;
        }
        $show = apply_filters('fp_cta_bar_visibility_context', null, $context);
        if ($show === true) {
            return true;
        }
        if ($show === false) {
            return false;
        }

        $visibility = $this->settings['visibility'] ?? ['home', 'single', 'page', 'archive'];
        if (empty($visibility)) {
            return true;
        }

        if ((is_front_page() || is_home()) && in_array('home', $visibility, true)) {
            return true;
        }
        if (in_array('home', $visibility, true) && $this->is_home_request()) {
            return true;
        }
        if (is_singular('post') && in_array('single', $visibility, true)) {
            return $this->passes_advanced_visibility_singular();
        }
        if (is_singular('page') && in_array('page', $visibility, true)) {
            return $this->passes_advanced_visibility_singular();
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

    /**
     * Fallback per homepage in contesti tema dove i conditional tag non riflettono la root URL.
     *
     * @return bool
     */
    private function is_home_request(): bool {
        $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        if ($uri === '') {
            return false;
        }

        $path = (string) parse_url($uri, PHP_URL_PATH);
        if ($path === '' || $path === '/') {
            return true;
        }

        // Alcuni setup locale espongono querystring sulla root (es. '/?foo=bar').
        return str_starts_with($uri, '/?');
    }

    /**
     * Verifica visibilità avanzata su singoli: post type e/o term in whitelist.
     *
     * @return bool
     */
    private function passes_advanced_visibility_singular(): bool {
        $post_types = $this->settings['post_type_visibility'] ?? [];
        if (is_array($post_types) && !empty($post_types)) {
            if (!in_array(get_post_type(), $post_types, true)) {
                return false;
            }
        }
        $term_ids = $this->settings['term_visibility'] ?? [];
        if (is_array($term_ids) && !empty($term_ids)) {
            $post_id = get_the_ID();
            if (!$post_id) {
                return false;
            }
            $taxonomies = get_object_taxonomies(get_post_type(), 'names');
            $found = false;
            foreach ($taxonomies as $tax) {
                $terms = wp_get_object_terms($post_id, $tax);
                if (is_array($terms) && !is_wp_error($terms)) {
                    foreach ($terms as $t) {
                        if (in_array((int) $t->term_id, $term_ids, true)) {
                            $found = true;
                            break 2;
                        }
                    }
                }
            }
            if (!$found) {
                return false;
            }
        }
        return true;
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

        $lang = $this->detect_lang();
        $click_endpoint = rest_url('fp/ctabar/v1/click');
        $click_nonce    = wp_create_nonce('fp_cta_bar_click');
        // Tracking delegated to FP-Marketing-Tracking-Layer via fpCtaBarClick DOM event
        wp_localize_script('fp-cta-bar-front', 'fpCtaBarTrack', [
            'eventName'     => $this->settings['gtm_event_name'] ?? $this->settings['ga4_event_name'] ?? 'cta_bar_click',
            'useFpLayer'    => true,
            'clickEndpoint' => $click_endpoint,
            'clickNonce'    => $click_nonce,
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

    /**
     * Determina la lingua corrente per label e link (ITA/ENG).
     * Utilizzabile via filtro fp_cta_bar_lang o FP-Multilanguage se attivo.
     *
     * @return string Codice lingua a 2 caratteri (es. 'it', 'en')
     */
    private function detect_lang() {
        $lang = apply_filters('fp_cta_bar_lang', null);
        if (is_string($lang) && strlen($lang) === 2) {
            return strtolower($lang);
        }
        if (defined('FP_ML_VERSION') && function_exists('apply_filters')) {
            $ml_lang = apply_filters('fp_ml_current_language', '');
            if (is_string($ml_lang) && strlen($ml_lang) >= 2) {
                return strtolower(substr($ml_lang, 0, 2));
            }
        }
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
        $icon_only = !empty($s['main_icon_only']) && $main_icon !== '';
        $icon_circle = !empty($s['main_icon_circle']);

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
        $button_sizes = [
            'small'  => ['pad_y' => '10px', 'pad_x' => '20px', 'icon_pad' => '9px', 'circle' => '46px'],
            'medium' => ['pad_y' => '14px', 'pad_x' => '28px', 'icon_pad' => '12px', 'circle' => '56px'],
            'large'  => ['pad_y' => '18px', 'pad_x' => '34px', 'icon_pad' => '14px', 'circle' => '66px'],
        ];
        $bs = $button_sizes[$s['button_size'] ?? 'medium'] ?? $button_sizes['medium'];

        $css_vars = sprintf(
            '--fpctabar-bg:%s;--fpctabar-text:%s;--fpctabar-border:%s;--fpctabar-panel-bg:%s;--fpctabar-z:%d;--fpctabar-font-size-label:%s;--fpctabar-font-size-link:%s;--fpctabar-btn-radius:%dpx;--fpctabar-btn-pad-y:%s;--fpctabar-btn-pad-x:%s;--fpctabar-btn-icon-only-pad:%s;--fpctabar-btn-circle-size:%s;',
            esc_attr($s['bg_color']),
            esc_attr($s['text_color']),
            esc_attr($s['border_color']),
            esc_attr($s['panel_bg_color']),
            (int) ($s['z_index'] ?? 99999),
            esc_attr($fs['label']),
            esc_attr($fs['link']),
            (int) ($s['button_radius'] ?? 4),
            esc_attr($bs['pad_y']),
            esc_attr($bs['pad_x']),
            esc_attr($bs['icon_pad']),
            esc_attr($bs['circle'])
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
            'data-lang="%s" data-delay="%d" data-scroll-percent="%d" data-panel-open="%s" data-dismiss-hours="%d" data-animation="%s" data-aria-open="%s" data-aria-closed="%s"',
            esc_attr($lang),
            (int) ($s['delay_seconds'] ?? 0),
            (int) ($s['show_after_scroll_percent'] ?? 0),
            !empty($s['panel_open_by_default']) ? '1' : '0',
            (int) ($s['hide_after_dismiss_hours'] ?? 0),
            esc_attr($s['animation'] ?? 'slide'),
            esc_attr(__('Pannello aperto', 'fp-cta-bar')),
            esc_attr(__('Pannello chiuso', 'fp-cta-bar'))
        );

        if ($mode === 'full-width') {
            $this->render_fullwidth($main_label, $main_icon, $links_html, $css_vars, $device_class, $close_on_click, $data_attrs, $icon_only);
        } else {
            $position = ($mode === 'button-left') ? 'left' : 'right';
            $this->render_button($main_label, $main_icon, $links_html, $css_vars, $position, $device_class, $close_on_click, $data_attrs, $icon_only, $icon_circle);
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
            $has_icon = trim((string) ($link['icon'] ?? '')) !== '';
            $has_label = trim((string) $label) !== '';

            if (empty($url) || (!$has_label && !$has_icon)) {
                continue;
            }

            // Tracking data attributes (only if tracking is enabled for this link)
            $track_attrs = '';
            if (!empty($link['track'])) {
                $track_label    = !empty($link['track_label']) ? $link['track_label'] : ($has_label ? $label : $url);
                $track_category = !empty($link['track_category']) ? $link['track_category'] : '';
                $track_attrs = sprintf(
                    ' data-fp-track="1" data-fp-track-label="%s" data-fp-track-category="%s"',
                    esc_attr($track_label),
                    esc_attr($track_category)
                );
            }

            $aria_label = $has_label
                ? sprintf(/* translators: link purpose for screen readers */ __('Apri link: %s', 'fp-cta-bar'), $label)
                : __('Apri link CTA', 'fp-cta-bar');
            $inner = $icon . ($has_label
                ? '<span class="fpctabar__link-label">' . esc_html($label) . '</span>'
                : '<span class="fpctabar__sr-only">' . esc_html__('Apri link', 'fp-cta-bar') . '</span>');
            $html .= sprintf(
                '<a href="%s" target="%s" rel="%s" aria-label="%s"%s>%s</a>',
                esc_url($url),
                esc_attr($target),
                esc_attr($rel),
                esc_attr($aria_label),
                $track_attrs,
                $inner
            );
        }
        return $html;
    }

    private function render_fullwidth($label, $main_icon, $links_html, $css_vars, $device_class = '', $close_on_link_click = true, $data_attrs = '', bool $icon_only = false) {
        $icon_html = $this->icon_html($main_icon);
        $label = trim((string) $label);
        $icon_only_class = $icon_only ? ' fpctabar--icon-only' : '';
        $sr_label = $label !== '' ? $label : __('Apri menu CTA', 'fp-cta-bar');
        ?>
        <div id="fpctabar" class="fpctabar fpctabar--fullwidth<?php echo esc_attr($device_class . $icon_only_class); ?>" style="<?php echo $css_vars; ?>" data-mode="fullwidth" data-close-on-link-click="<?php echo $close_on_link_click ? '1' : '0'; ?>" <?php echo $data_attrs; ?>>
            <span class="fpctabar__sr-only" aria-live="polite" aria-atomic="true" id="fpctabar-announcer"></span>
            <div class="fpctabar__bar" role="button" tabindex="0" aria-expanded="false" aria-controls="fpctabar-panel" aria-label="<?php echo esc_attr($sr_label); ?>">
                <?php echo $icon_html; ?>
                <?php if (!$icon_only) : ?>
                    <span class="fpctabar__label"><?php echo esc_html($label); ?></span>
                <?php endif; ?>
                <span class="fpctabar__arrow">&#9650;</span>
            </div>
            <div id="fpctabar-panel" class="fpctabar__panel" aria-hidden="true">
                <?php echo $links_html; ?>
            </div>
        </div>
        <?php
    }

    private function render_button($label, $main_icon, $links_html, $css_vars, $position, $device_class = '', $close_on_link_click = true, $data_attrs = '', bool $icon_only = false, bool $icon_circle = false) {
        $icon_html = $this->icon_html($main_icon);
        $label = trim((string) $label);
        $icon_only_class = $icon_only ? ' fpctabar--icon-only' : '';
        $icon_circle_class = ($icon_only && $icon_circle) ? ' fpctabar--icon-circle' : '';
        $sr_label = $label !== '' ? $label : __('Apri menu CTA', 'fp-cta-bar');
        ?>
        <div id="fpctabar" class="fpctabar fpctabar--button fpctabar--<?php echo esc_attr($position); ?><?php echo esc_attr($device_class . $icon_only_class . $icon_circle_class); ?>" style="<?php echo $css_vars; ?>" data-mode="button" data-close-on-link-click="<?php echo $close_on_link_click ? '1' : '0'; ?>" <?php echo $data_attrs; ?>>
            <span class="fpctabar__sr-only" aria-live="polite" aria-atomic="true" id="fpctabar-announcer"></span>
            <button class="fpctabar__btn" aria-expanded="false" aria-controls="fpctabar-panel" aria-label="<?php echo esc_attr($sr_label); ?>">
                <?php echo $icon_html; ?>
                <?php if (!$icon_only) : ?>
                    <span class="fpctabar__label"><?php echo esc_html($label); ?></span>
                <?php endif; ?>
            </button>
            <div id="fpctabar-panel" class="fpctabar__panel" aria-hidden="true">
                <?php echo $links_html; ?>
            </div>
        </div>
        <?php
    }
}
