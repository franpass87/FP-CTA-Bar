<?php

namespace FP\CtaBar;

class Plugin {

    private static $instance = null;

    const OPTION_KEY = 'fp_cta_bar_settings';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        if (is_admin()) {
            Admin::get_instance();
            Settings::get_instance();
        }

        if (!is_admin()) {
            add_shortcode('fp_cta_bar', [Shortcode::class, 'render']);
            Frontend::get_instance();
        }
    }

    private function __clone() {}
    public function __wakeup() {}

    public static function get_defaults() {
        return [
            'display_mode'           => 'full-width',
            'main_label_it'          => 'PRENOTA',
            'main_label_en'          => 'BOOK NOW',
            'main_icon'              => '',
            'bg_color'               => '#000000',
            'text_color'             => '#ffffff',
            'border_color'           => '#ffffff',
            'panel_bg_color'         => '#111111',
            'links'                  => [],
            'z_index'                => 99999,
            'visibility'             => ['home', 'single', 'page', 'archive'],
            'device_visibility'      => 'all',
            'font_size'              => 'medium',
            'button_radius'          => 4,
            'close_on_link_click'    => true,
            'delay_seconds'          => 0,
            'show_after_scroll_percent' => 0,
            'panel_open_by_default'  => false,
            'hide_after_dismiss_hours'   => 0,
            'animation'              => 'slide',
            'ga4_enabled'            => false,
            'ga4_event_name'         => 'cta_bar_click',
            'gtm_enabled'            => false,
            'gtm_event_name'         => 'cta_bar_click',
            'meta_enabled'           => false,
            'meta_event_name'        => 'cta_bar_click',
            'use_shortcode'          => false,
            'cookie_consent_required' => false,
            'custom_css'             => '',
        ];
    }

    public static function get_settings() {
        $saved = get_option(self::OPTION_KEY, []);
        return wp_parse_args($saved, self::get_defaults());
    }

    public static function activate() {
        if (false === get_option(self::OPTION_KEY)) {
            update_option(self::OPTION_KEY, self::get_defaults());
        }
    }

    public static function deactivate() {
        // Nessun cleanup necessario per ora
    }
}
