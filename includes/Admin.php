<?php
declare(strict_types=1);

namespace FP\CtaBar;

class Admin {

    private static $instance = null;
    private $page_hook = '';
    private $stats_page_hook = '';

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
        add_action('admin_post_fp_cta_bar_reset_stats', [$this, 'handle_reset_stats']);
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
        if (!array_key_exists('panel_text_color', $data) || trim((string) ($data['panel_text_color'] ?? '')) === '') {
            $merged['panel_text_color'] = $merged['text_color'] ?? $defaults['panel_text_color'];
        }
        update_option(Plugin::OPTION_KEY, $merged);
        wp_send_json_success(['redirect' => admin_url('options-general.php?page=fp-cta-bar&imported=1')]);
    }

    /**
     * Registra la voce di menu admin (top-level + Impostazioni).
     */
    public function add_menu(): void {
        $page_title = __('FP CTA Bar', 'fp-cta-bar');
        $menu_title = __('FP CTA Bar', 'fp-cta-bar');
        $capability = 'manage_options';
        $menu_slug  = 'fp-cta-bar';
        $callback   = [$this, 'render_page'];

        // Menu top-level nella sidebar (visibile)
        $this->page_hook = add_menu_page(
            $page_title,
            $menu_title,
            $capability,
            $menu_slug,
            $callback,
            'dashicons-megaphone',
            30
        );

        // Anche sotto Impostazioni per retrocompatibilità
        add_options_page($page_title, $menu_title, $capability, $menu_slug, $callback);

        // Pagina statistiche sotto menu principale plugin.
        $this->stats_page_hook = add_submenu_page(
            $menu_slug,
            __('Statistiche Click', 'fp-cta-bar'),
            __('Statistiche', 'fp-cta-bar'),
            $capability,
            'fp-cta-bar-stats',
            [$this, 'render_stats_page']
        );
    }

    public function enqueue_assets($hook) {
        $allowed_hooks = [
            $this->page_hook,
            $this->stats_page_hook,
            'settings_page_fp-cta-bar',
        ];
        if (!in_array($hook, $allowed_hooks, true)) {
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
            'iconPresets' => IconSvg::presets_for_script(),
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

    /**
     * Render della pagina statistiche click.
     */
    public function render_stats_page(): void {
        include FP_CTA_BAR_DIR . 'includes/admin-templates/stats-page.php';
    }

    /**
     * Azzera le statistiche click del plugin.
     */
    public function handle_reset_stats(): void {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Permessi insufficienti.', 'fp-cta-bar'));
        }

        check_admin_referer('fp_cta_bar_reset_stats');
        ClickStats::get_instance()->reset_stats();

        $redirect = add_query_arg(
            [
                'page'        => 'fp-cta-bar-stats',
                'stats_reset' => '1',
            ],
            admin_url('admin.php')
        );

        wp_safe_redirect($redirect);
        exit;
    }
}
