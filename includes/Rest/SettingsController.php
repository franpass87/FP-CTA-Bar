<?php

declare(strict_types=1);

namespace FP\CtaBar\Rest;

use FP\CtaBar\Plugin;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * REST controller per lettura in sola lettura delle impostazioni CTA Bar.
 */
final class SettingsController {

    private const NAMESPACE = 'fp/ctabar/v1';

    public function register_routes(): void {
        register_rest_route(self::NAMESPACE, '/settings', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_settings'],
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
        ]);
    }

    /**
     * Restituisce le impostazioni salvate (solo lettura).
     *
     * @return WP_REST_Response
     */
    public function get_settings(WP_REST_Request $request) {
        $settings = get_option(Plugin::OPTION_KEY, []);
        $defaults = Plugin::get_defaults();
        $merged   = wp_parse_args(is_array($settings) ? $settings : [], $defaults);
        return rest_ensure_response($merged);
    }
}
