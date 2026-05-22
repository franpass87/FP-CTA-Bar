<?php

declare(strict_types=1);

namespace FP\CtaBar\Services\Settings;

use FP\CtaBar\Plugin;
use FP\CtaBar\Settings;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Impostazioni FP CTA Bar per automazioni Bridge/MCP.
 *
 * @since 1.10.1
 */
final class SettingsRegistry
{
    public const PARENT_OPTION = Plugin::OPTION_KEY;

    public const MIN_PLUGIN_VERSION = '1.10.1';

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function get_field_catalog(): array
    {
        $catalog = [
            'display_mode' => self::field('display', 'enum', 'low', 'Modalità display', '', 'full-width', ['full-width', 'button-left', 'button-right']),
            'main_label_it' => self::field('labels', 'string', 'low', 'Etichetta IT', ''),
            'main_label_en' => self::field('labels', 'string', 'low', 'Etichetta EN', ''),
            'bg_color' => self::field('colors', 'hex', 'low', 'Colore sfondo', ''),
            'text_color' => self::field('colors', 'hex', 'low', 'Colore testo', ''),
            'panel_text_color' => self::field('colors', 'hex', 'low', 'Colore testo pannello', ''),
            'panel_bg_color' => self::field('colors', 'hex', 'low', 'Sfondo pannello', ''),
            'border_color' => self::field('colors', 'hex', 'low', 'Bordo', ''),
            'z_index' => self::field('layout', 'int', 'low', 'z-index', '', 99999),
            'device_visibility' => self::field('visibility', 'enum', 'low', 'Dispositivi', '', 'all', ['all', 'mobile', 'desktop']),
            'font_size' => self::field('layout', 'enum', 'low', 'Font size', '', 'medium', ['small', 'medium', 'large']),
            'button_size' => self::field('layout', 'enum', 'low', 'Button size', '', 'medium', ['small', 'medium', 'large']),
            'button_radius' => self::field('layout', 'int', 'low', 'Border radius px', '', 4),
            'delay_seconds' => self::field('behavior', 'int', 'low', 'Ritardo secondi', '', 0),
            'show_after_scroll_percent' => self::field('behavior', 'int', 'low', 'Scroll %', '', 0),
            'animation' => self::field('behavior', 'enum', 'low', 'Animazione', '', 'slide', ['none', 'slide', 'fade', 'bounce']),
            'use_shortcode' => self::field('behavior', 'bool', 'medium', 'Solo shortcode', ''),
            'ga4_enabled' => self::field('tracking', 'bool', 'low', 'GA4', ''),
            'gtm_enabled' => self::field('tracking', 'bool', 'low', 'GTM', ''),
            'meta_enabled' => self::field('tracking', 'bool', 'low', 'Meta Pixel', ''),
            'links' => self::field('links', 'array', 'high', 'Array link IT/EN (sostituisce intero links[])', 'Usare con cautela: passare array completo.'),
        ];

        return apply_filters('fp_ctabar_remote_settings_registry', $catalog);
    }

    /**
     * @return array<string, mixed>
     */
    public static function get_builder_catalog(): array
    {
        $list = [];
        foreach (self::get_field_catalog() as $key => $meta) {
            $list[] = array_merge(['key' => $key], $meta);
        }

        return [
            'fp_cta_bar_version' => defined('FP_CTA_BAR_VERSION') ? FP_CTA_BAR_VERSION : '',
            'parent_option' => self::PARENT_OPTION,
            'settings' => $list,
            'example_items' => [
                ['key' => 'main_label_it', 'value' => 'PRENOTA'],
                ['key' => 'main_label_en', 'value' => 'BOOK NOW'],
            ],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return array<string, mixed>
     */
    public static function apply_settings(array $items, bool $dry_run = true): array
    {
        $catalog = self::get_field_catalog();
        $results = [];
        $errors = 0;
        $working = Plugin::get_settings();

        foreach ($items as $idx => $row) {
            if (!is_array($row) || !isset($row['key'])) {
                ++$errors;
                continue;
            }
            $key = trim((string) $row['key']);
            if ($key === '' || !isset($catalog[$key])) {
                ++$errors;
                $results[] = ['ok' => false, 'key' => $key, 'status' => 'unknown_key'];
                continue;
            }
            if (!array_key_exists('value', $row)) {
                ++$errors;
                continue;
            }
            $working[$key] = $row['value'];
            $results[] = ['ok' => true, 'key' => $key, 'status' => 'staged'];
        }

        if ($errors > 0) {
            return ['success' => false, 'dry_run' => $dry_run, 'results' => $results, 'summary' => ['errors' => $errors]];
        }

        $sanitizer = Settings::get_instance();
        $clean = $sanitizer->sanitize($working);

        if ($dry_run) {
            return [
                'success' => true,
                'dry_run' => true,
                'results' => $results,
                'preview_keys' => array_keys($working),
            ];
        }

        update_option(self::PARENT_OPTION, $clean, false);
        do_action('fp_ctabar_settings_applied_remote', $clean);

        return [
            'success' => true,
            'dry_run' => false,
            'results' => $results,
            'summary' => ['applied' => count($results)],
        ];
    }

    /**
     * @param array<int, string>|null $allowed
     * @return array<string, mixed>
     */
    private static function field(
        string $area,
        string $type,
        string $risk,
        string $label,
        string $description,
        mixed $default = null,
        ?array $allowed = null
    ): array {
        $f = [
            'area' => $area,
            'type' => $type,
            'risk' => $risk,
            'label' => $label,
            'description' => $description,
            'default' => $default,
        ];
        if ($allowed !== null) {
            $f['allowed'] = $allowed;
        }

        return $f;
    }
}
