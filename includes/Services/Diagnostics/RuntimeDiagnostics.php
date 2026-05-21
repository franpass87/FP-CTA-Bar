<?php
/**
 * Runtime diagnostics FP CTA Bar (read-only, Bridge-safe).
 *
 * @package FP\CtaBar\Services\Diagnostics
 */

declare(strict_types=1);

namespace FP\CtaBar\Services\Diagnostics;

use FP\CtaBar\ClickStats;
use FP\CtaBar\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Snapshot operativo per FP Remote Bridge (`ctabar_runtime`).
 */
final class RuntimeDiagnostics
{
    public const SECTION_DISPLAY_CONTEXT = 'display_context';
    public const SECTION_LINKS_SUMMARY = 'links_summary';
    public const SECTION_CLICK_STATS = 'click_stats';
    public const SECTION_SETTINGS = 'settings';
    public const SECTION_INTEGRATIONS = 'integrations';
    public const SECTION_REST_HEALTH = 'rest_health';
    public const SECTION_CRON = 'cron';
    public const SECTION_PROBLEMS = 'problems';

    public const ALL_SECTIONS = [
        self::SECTION_DISPLAY_CONTEXT,
        self::SECTION_LINKS_SUMMARY,
        self::SECTION_CLICK_STATS,
        self::SECTION_SETTINGS,
        self::SECTION_INTEGRATIONS,
        self::SECTION_REST_HEALTH,
        self::SECTION_CRON,
        self::SECTION_PROBLEMS,
    ];

    /**
     * @param array<int, string>   $sections
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public static function build(array $sections = [], array $options = []): array
    {
        $requested = $sections === [] ? self::ALL_SECTIONS : array_values(array_intersect($sections, self::ALL_SECTIONS));
        if ($requested === []) {
            $requested = self::ALL_SECTIONS;
        }

        $topLimit = isset($options['click_top_limit']) ? max(1, min(20, (int) $options['click_top_limit'])) : 10;
        $byDayDays = isset($options['click_by_day_days']) ? max(7, min(90, (int) $options['click_by_day_days'])) : 14;

        $payload = [
            'plugin_active' => true,
            'plugin_version' => defined('FP_CTA_BAR_VERSION') ? (string) FP_CTA_BAR_VERSION : '',
            'available_sections' => self::ALL_SECTIONS,
            'requested_sections' => $requested,
            'generated_at_gmt' => gmdate('Y-m-d H:i:s'),
        ];

        foreach ($requested as $section) {
            switch ($section) {
                case self::SECTION_DISPLAY_CONTEXT:
                    $payload['display_context'] = self::build_display_context();
                    break;
                case self::SECTION_LINKS_SUMMARY:
                    $payload['links_summary'] = self::build_links_summary();
                    break;
                case self::SECTION_CLICK_STATS:
                    $payload['click_stats'] = self::build_click_stats($topLimit, $byDayDays);
                    break;
                case self::SECTION_SETTINGS:
                    $payload['settings'] = self::build_settings();
                    break;
                case self::SECTION_INTEGRATIONS:
                    $payload['integrations'] = self::build_integrations();
                    break;
                case self::SECTION_REST_HEALTH:
                    $payload['rest_health'] = self::build_rest_health();
                    break;
                case self::SECTION_CRON:
                    $payload['cron'] = [
                        'scheduled_hooks' => [],
                        'note' => 'FP CTA Bar non registra cron WordPress.',
                    ];
                    break;
                case self::SECTION_PROBLEMS:
                    $payload['problems'] = self::collect_problems($payload);
                    break;
            }
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>
     */
    private static function build_display_context(): array
    {
        $settings = Plugin::get_settings();
        $links = isset($settings['links']) && is_array($settings['links']) ? $settings['links'] : [];
        $today = gmdate('Y-m-d');
        $start = trim((string) ($settings['schedule_start'] ?? ''));
        $end = trim((string) ($settings['schedule_end'] ?? ''));
        $scheduleActive = true;
        if ($start !== '' && $today < $start) {
            $scheduleActive = false;
        }
        if ($end !== '' && $today > $end) {
            $scheduleActive = false;
        }

        return [
            'use_shortcode' => !empty($settings['use_shortcode']),
            'auto_render_enabled' => empty($settings['use_shortcode']) && $links !== [],
            'display_mode' => (string) ($settings['display_mode'] ?? 'full-width'),
            'visibility' => is_array($settings['visibility'] ?? null) ? array_values($settings['visibility']) : [],
            'device_visibility' => (string) ($settings['device_visibility'] ?? 'all'),
            'schedule_start' => $start,
            'schedule_end' => $end,
            'schedule_active_today' => $scheduleActive,
            'cookie_consent_required' => !empty($settings['cookie_consent_required']),
            'panel_open_by_default' => !empty($settings['panel_open_by_default']),
            'hide_on_fp_experience_pages' => (bool) apply_filters('fp_cta_bar_hide_on_fp_experience_pages', true),
            'link_count' => count($links),
            'shortcode_registered' => shortcode_exists('fp_cta_bar'),
            'block_registered' => class_exists('WP_Block_Type_Registry', false)
                && \WP_Block_Type_Registry::get_instance()->is_registered('fp/cta-bar'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function build_links_summary(): array
    {
        $settings = Plugin::get_settings();
        $links = isset($settings['links']) && is_array($settings['links']) ? $settings['links'] : [];

        $withTrack = 0;
        $emptyUrl = 0;
        $withUrlEn = 0;
        $withIcon = 0;

        foreach ($links as $link) {
            if (!is_array($link)) {
                continue;
            }
            $urlIt = trim((string) ($link['url_it'] ?? $link['url'] ?? ''));
            $urlEn = trim((string) ($link['url_en'] ?? ''));
            if ($urlIt === '' && $urlEn === '') {
                ++$emptyUrl;
            }
            if ($urlEn !== '') {
                ++$withUrlEn;
            }
            if (!empty($link['track'])) {
                ++$withTrack;
            }
            if (!empty($link['icon'])) {
                ++$withIcon;
            }
        }

        return [
            'link_count' => count($links),
            'empty_url_count' => $emptyUrl,
            'with_url_en' => $withUrlEn,
            'with_track' => $withTrack,
            'with_icon' => $withIcon,
            'main_label_it_set' => trim((string) ($settings['main_label_it'] ?? '')) !== '',
            'main_label_en_set' => trim((string) ($settings['main_label_en'] ?? '')) !== '',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function build_click_stats(int $topLimit, int $byDayDays): array
    {
        $stats = ClickStats::get_instance()->get_stats();
        $byDay = isset($stats['by_day']) && is_array($stats['by_day']) ? $stats['by_day'] : [];
        krsort($byDay);
        $recentDays = [];
        $i = 0;
        foreach ($byDay as $day => $count) {
            if ($i >= $byDayDays) {
                break;
            }
            $recentDays[(string) $day] = (int) $count;
            ++$i;
        }

        $top = [];
        foreach (ClickStats::get_instance()->get_rows($topLimit) as $row) {
            $top[] = [
                'url_mask' => self::mask_click_url((string) ($row['url'] ?? '')),
                'label' => mb_substr((string) ($row['label'] ?? ''), 0, 80),
                'lang' => (string) ($row['lang'] ?? ''),
                'clicks' => (int) ($row['clicks'] ?? 0),
            ];
        }

        $rows = isset($stats['rows']) && is_array($stats['rows']) ? $stats['rows'] : [];

        return [
            'total_clicks' => (int) ($stats['total_clicks'] ?? 0),
            'rows_count' => count($rows),
            'rows_cap' => 300,
            'by_day_recent' => $recentDays,
            'updated_at' => (string) ($stats['updated_at'] ?? ''),
            'today_clicks' => ClickStats::get_instance()->get_today_clicks(),
            'clicks_last_7_days' => ClickStats::get_instance()->get_clicks_last_days(7),
            'top_rows' => $top,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function build_settings(): array
    {
        $settings = Plugin::get_settings();
        $customCss = (string) ($settings['custom_css'] ?? '');

        return [
            'display_mode' => (string) ($settings['display_mode'] ?? ''),
            'bg_color' => (string) ($settings['bg_color'] ?? ''),
            'text_color' => (string) ($settings['text_color'] ?? ''),
            'panel_bg_color' => (string) ($settings['panel_bg_color'] ?? ''),
            'z_index' => (int) ($settings['z_index'] ?? 0),
            'animation' => (string) ($settings['animation'] ?? ''),
            'delay_seconds' => (int) ($settings['delay_seconds'] ?? 0),
            'show_after_scroll_percent' => (int) ($settings['show_after_scroll_percent'] ?? 0),
            'hide_after_dismiss_hours' => (int) ($settings['hide_after_dismiss_hours'] ?? 0),
            'ga4_enabled' => !empty($settings['ga4_enabled']),
            'ga4_event_name' => (string) ($settings['ga4_event_name'] ?? ''),
            'gtm_enabled' => !empty($settings['gtm_enabled']),
            'gtm_event_name' => (string) ($settings['gtm_event_name'] ?? ''),
            'meta_enabled' => !empty($settings['meta_enabled']),
            'meta_event_name' => (string) ($settings['meta_event_name'] ?? ''),
            'custom_css_bytes' => strlen($customCss),
            'post_type_visibility_count' => is_array($settings['post_type_visibility'] ?? null)
                ? count($settings['post_type_visibility'])
                : 0,
            'term_visibility_count' => is_array($settings['term_visibility'] ?? null)
                ? count($settings['term_visibility'])
                : 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function build_integrations(): array
    {
        return [
            'fp_multilanguage_active' => defined('FP_ML_VERSION'),
            'fp_multilanguage_version' => defined('FP_ML_VERSION') ? (string) FP_ML_VERSION : null,
            'wpml_active' => defined('ICL_SITEPRESS_VERSION'),
            'polylang_active' => function_exists('pll_current_language'),
            'fp_tracking_active' => class_exists('FPTracking\\Core\\Plugin', false),
            'fp_experience_cpt' => post_type_exists('fp_experience'),
            'filters_registered' => [
                'fp_cta_bar_can_show' => has_filter('fp_cta_bar_can_show'),
                'fp_cta_bar_visibility_context' => has_filter('fp_cta_bar_visibility_context'),
                'fp_cta_bar_hide_on_fp_experience_pages' => has_filter('fp_cta_bar_hide_on_fp_experience_pages'),
                'fp_cta_bar_lang' => has_filter('fp_cta_bar_lang'),
            ],
            'frontend_event' => 'fpCtaBarClick',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function build_rest_health(): array
    {
        $namespace = 'fp/ctabar/v1';

        return [
            'namespace_registered' => self::rest_namespace_registered($namespace),
            'routes' => [
                $namespace . '/click',
                $namespace . '/settings',
            ],
            'click_permission' => 'public_with_nonce_fp_cta_bar_click',
            'settings_permission' => 'manage_options',
        ];
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<int, array<string, string>>
     */
    private static function collect_problems(array $payload): array
    {
        $problems = [];

        $links = isset($payload['links_summary']) && is_array($payload['links_summary']) ? $payload['links_summary'] : [];
        if ((int) ($links['link_count'] ?? 0) === 0) {
            $problems[] = [
                'code' => 'zero_links',
                'severity' => 'high',
                'message' => 'Nessun link configurato nella CTA Bar.',
            ];
        } elseif ((int) ($links['empty_url_count'] ?? 0) > 0) {
            $problems[] = [
                'code' => 'links_missing_url',
                'severity' => 'high',
                'message' => 'Uno o più link senza URL IT/EN.',
            ];
        }

        $display = isset($payload['display_context']) && is_array($payload['display_context']) ? $payload['display_context'] : [];
        if (!empty($display['schedule_start']) || !empty($display['schedule_end'])) {
            if (empty($display['schedule_active_today'])) {
                $problems[] = [
                    'code' => 'schedule_inactive',
                    'severity' => 'medium',
                    'message' => 'Programmazione attiva ma fuori intervallo date odierno.',
                ];
            }
        }
        if (!empty($display['use_shortcode']) && empty($display['auto_render_enabled'])) {
            $problems[] = [
                'code' => 'shortcode_mode',
                'severity' => 'low',
                'message' => 'Modalità shortcode/blocco: la barra non si auto-inietta nel tema.',
            ];
        }

        $settings = isset($payload['settings']) && is_array($payload['settings']) ? $payload['settings'] : [];
        $trackingOn = !empty($settings['ga4_enabled']) || !empty($settings['gtm_enabled']) || !empty($settings['meta_enabled']);
        if ($trackingOn && (int) ($links['with_track'] ?? 0) === 0 && (int) ($links['link_count'] ?? 0) > 0) {
            $problems[] = [
                'code' => 'tracking_on_no_link_track_flags',
                'severity' => 'low',
                'message' => 'Tracking globale abilitato ma nessun link con flag track.',
            ];
        }

        $stats = isset($payload['click_stats']) && is_array($payload['click_stats']) ? $payload['click_stats'] : [];
        if ($trackingOn && (int) ($stats['total_clicks'] ?? 0) === 0) {
            $problems[] = [
                'code' => 'tracking_on_zero_clicks',
                'severity' => 'low',
                'message' => 'Tracking abilitato ma statistiche click ancora vuote.',
            ];
        }

        $rest = isset($payload['rest_health']) && is_array($payload['rest_health']) ? $payload['rest_health'] : [];
        if (empty($rest['namespace_registered'])) {
            $problems[] = [
                'code' => 'rest_namespace_missing',
                'severity' => 'high',
                'message' => 'Namespace REST fp/ctabar/v1 non registrato.',
            ];
        }

        $visibility = is_array($display['visibility'] ?? null) ? $display['visibility'] : [];
        if ($visibility === []) {
            $problems[] = [
                'code' => 'visibility_empty',
                'severity' => 'medium',
                'message' => 'Visibilità pagine vuota — la barra non verrà mostrata in auto-render.',
            ];
        }

        return $problems;
    }

    private static function mask_click_url(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '[empty]';
        }

        $parsed = wp_parse_url($url);
        if (!is_array($parsed)) {
            return '[invalid]';
        }

        $host = isset($parsed['host']) ? (string) $parsed['host'] : '';
        $path = isset($parsed['path']) ? (string) $parsed['path'] : '/';

        return $host !== '' ? $host . $path : $path;
    }

    private static function rest_namespace_registered(string $namespace): bool
    {
        if (!function_exists('rest_get_server')) {
            return false;
        }

        $server = rest_get_server();
        if (!$server instanceof \WP_REST_Server) {
            return false;
        }

        foreach (array_keys($server->get_routes()) as $route) {
            if (strpos((string) $route, '/' . $namespace) === 0) {
                return true;
            }
        }

        return false;
    }
}
