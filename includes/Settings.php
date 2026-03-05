<?php

namespace FP\CtaBar;

class Settings {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_settings() {
        register_setting(
            'fp_cta_bar_group',
            Plugin::OPTION_KEY,
            [
                'type'              => 'array',
                'sanitize_callback' => [$this, 'sanitize'],
            ]
        );
    }

    public function sanitize($input) {
        $defaults = Plugin::get_defaults();

        if (!is_array($input)) {
            return get_option(Plugin::OPTION_KEY, $defaults);
        }

        $clean = [];
        $allowed_modes = ['full-width', 'button-left', 'button-right'];
        $clean['display_mode'] = (isset($input['display_mode']) && in_array($input['display_mode'], $allowed_modes, true))
            ? $input['display_mode']
            : $defaults['display_mode'];

        $clean['main_label_it']  = isset($input['main_label_it']) ? sanitize_text_field($input['main_label_it']) : $defaults['main_label_it'];
        $clean['main_label_en']  = isset($input['main_label_en']) ? sanitize_text_field($input['main_label_en']) : $defaults['main_label_en'];
        $clean['main_icon']      = isset($input['main_icon']) ? $this->sanitize_icon($input['main_icon']) : '';
        $clean['bg_color']       = $this->sanitize_hex($input['bg_color'] ?? $defaults['bg_color']);
        $clean['text_color']     = $this->sanitize_hex($input['text_color'] ?? $defaults['text_color']);
        $clean['border_color']   = $this->sanitize_hex($input['border_color'] ?? $defaults['border_color']);
        $clean['panel_bg_color'] = $this->sanitize_hex($input['panel_bg_color'] ?? $defaults['panel_bg_color']);

        $z = isset($input['z_index']) ? absint($input['z_index']) : 99999;
        $clean['z_index'] = ($z >= 1 && $z <= 2147483647) ? $z : 99999;

        $allowed_devices = ['all', 'mobile', 'desktop'];
        $clean['device_visibility'] = (isset($input['device_visibility']) && in_array($input['device_visibility'], $allowed_devices, true))
            ? $input['device_visibility']
            : $defaults['device_visibility'];

        $allowed_font_sizes = ['small', 'medium', 'large'];
        $clean['font_size'] = (isset($input['font_size']) && in_array($input['font_size'], $allowed_font_sizes, true))
            ? $input['font_size']
            : $defaults['font_size'];

        $radius = isset($input['button_radius']) ? absint($input['button_radius']) : 4;
        $clean['button_radius'] = ($radius >= 0 && $radius <= 24) ? $radius : 4;

        $clean['close_on_link_click'] = !empty($input['close_on_link_click']);

        $allowed_visibility = ['home', 'single', 'page', 'archive', 'search', '404'];
        $clean['visibility'] = [];
        if (!empty($input['visibility']) && is_array($input['visibility'])) {
            foreach ($input['visibility'] as $v) {
                if (in_array($v, $allowed_visibility, true)) {
                    $clean['visibility'][] = $v;
                }
            }
        }
        if (empty($clean['visibility'])) {
            $clean['visibility'] = $defaults['visibility'];
        }

        $delay = isset($input['delay_seconds']) ? absint($input['delay_seconds']) : 0;
        $clean['delay_seconds'] = ($delay >= 0 && $delay <= 10) ? $delay : 0;

        $scroll = isset($input['show_after_scroll_percent']) ? absint($input['show_after_scroll_percent']) : 0;
        $clean['show_after_scroll_percent'] = ($scroll >= 0 && $scroll <= 100) ? $scroll : 0;

        $clean['panel_open_by_default'] = !empty($input['panel_open_by_default']);

        $dismiss = isset($input['hide_after_dismiss_hours']) ? absint($input['hide_after_dismiss_hours']) : 0;
        $clean['hide_after_dismiss_hours'] = ($dismiss >= 0 && $dismiss <= 168) ? $dismiss : 0;

        $allowed_animations = ['none', 'slide', 'fade', 'bounce'];
        $clean['animation'] = (isset($input['animation']) && in_array($input['animation'], $allowed_animations, true))
            ? $input['animation']
            : $defaults['animation'];

        $clean['ga4_enabled'] = !empty($input['ga4_enabled']);
        $clean['ga4_event_name'] = isset($input['ga4_event_name']) ? sanitize_key($input['ga4_event_name']) : $defaults['ga4_event_name'];
        if (empty($clean['ga4_event_name'])) {
            $clean['ga4_event_name'] = 'cta_bar_click';
        }

        $clean['gtm_enabled'] = !empty($input['gtm_enabled']);
        $clean['gtm_event_name'] = isset($input['gtm_event_name']) ? sanitize_key($input['gtm_event_name']) : $defaults['gtm_event_name'];
        if (empty($clean['gtm_event_name'])) {
            $clean['gtm_event_name'] = 'cta_bar_click';
        }

        $clean['meta_enabled'] = !empty($input['meta_enabled']);
        $clean['meta_event_name'] = isset($input['meta_event_name']) ? sanitize_key($input['meta_event_name']) : $defaults['meta_event_name'];
        if (empty($clean['meta_event_name'])) {
            $clean['meta_event_name'] = 'cta_bar_click';
        }

        $clean['use_shortcode'] = !empty($input['use_shortcode']);
        $clean['cookie_consent_required'] = !empty($input['cookie_consent_required']);

        $clean['custom_css'] = isset($input['custom_css']) ? wp_strip_all_tags($input['custom_css']) : '';

        $clean['links'] = [];
        if (!empty($input['links']) && is_array($input['links'])) {
            foreach ($input['links'] as $link) {
                $url_it = isset($link['url_it']) ? esc_url_raw($link['url_it']) : '';
                $url_en = isset($link['url_en']) ? esc_url_raw($link['url_en']) : '';

                if (empty($url_it) && empty($url_en)) {
                    continue;
                }

                $clean['links'][] = [
                    'label_it' => isset($link['label_it']) ? sanitize_text_field($link['label_it']) : '',
                    'label_en' => isset($link['label_en']) ? sanitize_text_field($link['label_en']) : '',
                    'url_it'   => $url_it,
                    'url_en'   => $url_en,
                    'target'   => (isset($link['target']) && $link['target'] === '_self') ? '_self' : '_blank',
                    'icon'     => isset($link['icon']) ? $this->sanitize_icon($link['icon']) : '',
                ];
            }
        }

        return $clean;
    }

    private function sanitize_icon($val) {
        $val = isset($val) ? trim((string) $val) : '';
        if ($val === '') {
            return '';
        }
        if (preg_match('#^(https?:|/|data:)#i', $val)) {
            return esc_url_raw($val) ?: '';
        }
        return preg_replace('/[^a-zA-Z0-9 _\-.]/', '', $val);
    }

    private function sanitize_hex($color) {
        $color = is_string($color) ? trim($color) : '';
        if ($color !== '' && preg_match('/^#[a-fA-F0-9]{6}$/', $color)) {
            return $color;
        }
        return '#000000';
    }
}
