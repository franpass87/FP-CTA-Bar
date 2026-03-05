<?php

namespace FP\CtaBar;

class Admin {

    private static $instance = null;
    private $page_hook = '';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_fp_cta_bar_export', [$this, 'ajax_export']);
        add_action('wp_ajax_fp_cta_bar_import', [$this, 'ajax_import']);
    }

    public function ajax_export() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }
        check_ajax_referer('fp_cta_bar_export', 'nonce');
        $settings = Plugin::get_settings();
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="fp-cta-bar-settings-' . date('Y-m-d') . '.json"');
        echo wp_json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function ajax_import() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized'], 403);
        }
        check_ajax_referer('fp_cta_bar_import', 'nonce');
        if (empty($_FILES['file']['tmp_name'])) {
            wp_send_json_error(['message' => 'No file uploaded']);
        }
        $json = file_get_contents($_FILES['file']['tmp_name']);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            wp_send_json_error(['message' => 'Invalid JSON']);
        }
        $defaults = Plugin::get_defaults();
        $merged = wp_parse_args($data, $defaults);
        update_option(Plugin::OPTION_KEY, $merged);
        wp_send_json_success(['redirect' => admin_url('options-general.php?page=fp-cta-bar&imported=1')]);
    }

    public function add_menu() {
        $this->page_hook = add_options_page(
            __('FP CTA Bar', 'fp-cta-bar'),
            __('FP CTA Bar', 'fp-cta-bar'),
            'manage_options',
            'fp-cta-bar',
            [$this, 'render_page']
        );
    }

    public function enqueue_assets($hook) {
        if ($hook !== $this->page_hook) {
            return;
        }

        wp_enqueue_style('wp-color-picker');

        wp_enqueue_style(
            'fp-cta-bar-admin',
            FP_CTA_BAR_URL . 'assets/css/admin.css',
            [],
            FP_CTA_BAR_VERSION
        );

        wp_enqueue_script(
            'fp-cta-bar-admin',
            FP_CTA_BAR_URL . 'assets/js/admin.js',
            ['jquery', 'wp-color-picker', 'jquery-ui-sortable'],
            FP_CTA_BAR_VERSION,
            true
        );

        wp_enqueue_style(
            'fp-cta-bar-front',
            FP_CTA_BAR_URL . 'assets/css/frontend.css',
            [],
            FP_CTA_BAR_VERSION
        );

        wp_localize_script('fp-cta-bar-admin', 'fpCtaBar', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'exportUrl' => add_query_arg(['action' => 'fp_cta_bar_export', 'nonce' => wp_create_nonce('fp_cta_bar_export')], admin_url('admin-ajax.php')),
            'importNonce' => wp_create_nonce('fp_cta_bar_import'),
            'i18n' => [
                'confirmRemove' => __('Rimuovere questo link?', 'fp-cta-bar'),
                'importSuccess' => __('Importazione completata.', 'fp-cta-bar'),
                'importError' => __('Errore durante l\'importazione.', 'fp-cta-bar'),
            ],
        ]);
    }

    public function render_page() {
        include FP_CTA_BAR_DIR . 'includes/admin-templates/settings-page.php';
    }
}
