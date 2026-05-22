<?php

declare(strict_types=1);

use FP\CtaBar\Services\Settings\SettingsRegistry;

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('fp_ctabar_settings_is_available')) {
    function fp_ctabar_settings_is_available(): bool
    {
        return defined('FP_CTA_BAR_VERSION')
            && version_compare((string) FP_CTA_BAR_VERSION, SettingsRegistry::MIN_PLUGIN_VERSION, '>=')
            && class_exists(\FP\CtaBar\Settings::class);
    }
}

if (!function_exists('fp_ctabar_get_settings_builder_catalog')) {
    function fp_ctabar_get_settings_builder_catalog(): array
    {
        return SettingsRegistry::get_builder_catalog();
    }
}

if (!function_exists('fp_ctabar_apply_settings')) {
    function fp_ctabar_apply_settings(array $items, bool $dry_run = true): array
    {
        return SettingsRegistry::apply_settings($items, $dry_run);
    }
}
