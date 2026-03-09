<?php
/**
 * Plugin Name: FP CTA Bar
 * Plugin URI: https://francescopasseri.com
 * Description: Barra/bottone CTA configurabile (full-width, bottom-left, bottom-right) con link dinamici ITA/ENG
 * Version: 1.0.0
 * GitHub Plugin URI: franpass87/FP-CTA-Bar
 * Author: Francesco Passeri
 * Author URI: https://francescopasseri.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fp-cta-bar
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('FP_CTA_BAR_VERSION', '1.0.0');
define('FP_CTA_BAR_DIR', dirname(__FILE__) . '/');
define('FP_CTA_BAR_URL', plugin_dir_url(__FILE__));
define('FP_CTA_BAR_FILE', __FILE__);
define('FP_CTA_BAR_BASENAME', plugin_basename(__FILE__));

$autoload_file = FP_CTA_BAR_DIR . 'vendor/autoload.php';

if (file_exists($autoload_file)) {
    require_once $autoload_file;
} else {
    add_action('admin_notices', function () {
        if (!current_user_can('activate_plugins')) {
            return;
        }
        echo '<div class="notice notice-error"><p>';
        echo '<strong>FP CTA Bar:</strong> ';
        echo 'vendor/ non trovato. Esegui <code>composer install --no-dev</code> nella cartella del plugin.';
        echo '</p></div>';
    });
    return;
}

use FP\CtaBar\Plugin;

register_activation_hook(__FILE__, function () {
    if (class_exists('\FP\CtaBar\Plugin')) {
        Plugin::activate();
    }
});

register_deactivation_hook(__FILE__, function () {
    if (class_exists('\FP\CtaBar\Plugin')) {
        Plugin::deactivate();
    }
});

add_action('plugins_loaded', function () {
    load_plugin_textdomain('fp-cta-bar', false, dirname(FP_CTA_BAR_BASENAME) . '/languages');
    Plugin::get_instance();
}, 10);
