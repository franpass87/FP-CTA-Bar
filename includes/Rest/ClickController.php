<?php

declare(strict_types=1);

namespace FP\CtaBar\Rest;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * REST controller per registrare il click su un link della CTA bar (action fp_cta_bar_clicked).
 */
final class ClickController {

    private const NAMESPACE = 'fp/ctabar/v1';
    private const MAX_URL_LENGTH = 2048;
    private const MAX_LABEL_LENGTH = 500;

    public function register_routes(): void {
        register_rest_route(self::NAMESPACE, '/click', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'handle_click'],
            'permission_callback' => [$this, 'check_permission'],
            'args'                => [
                'url'   => [
                    'required'          => true,
                    'type'              => 'string',
                    'sanitize_callback' => 'esc_url_raw',
                    'validate_callback' => function ($value) {
                        return is_string($value) && strlen($value) <= self::MAX_URL_LENGTH;
                    },
                ],
                'label' => [
                    'required'          => false,
                    'type'              => 'string',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function ($value) {
                        return is_string($value) && strlen($value) <= self::MAX_LABEL_LENGTH;
                    },
                ],
                'lang'  => [
                    'required'          => false,
                    'type'              => 'string',
                    'default'           => '',
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function ($value) {
                        return is_string($value) && strlen($value) <= 10;
                    },
                ],
                'nonce' => [
                    'required'          => true,
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ],
        ]);
    }

    /**
     * Verifica il nonce per richieste da frontend (utenti non necessariamente loggati).
     */
    public function check_permission(WP_REST_Request $request): bool {
        $nonce = $request->get_param('nonce');
        return is_string($nonce) && wp_verify_nonce($nonce, 'fp_cta_bar_click');
    }

    /**
     * Gestisce POST /click: sanitizza e invoca do_action('fp_cta_bar_clicked', $url, $label, $lang).
     *
     * @return WP_REST_Response
     */
    public function handle_click(WP_REST_Request $request) {
        $url   = $request->get_param('url');
        $label = $request->get_param('label') ?? '';
        $lang  = $request->get_param('lang') ?? '';

        $url = is_string($url) ? esc_url_raw($url) : '';
        if ($url === '') {
            return new WP_REST_Response(null, 400);
        }
        $label = is_string($label) ? sanitize_text_field($label) : '';
        $lang  = is_string($lang) ? sanitize_text_field($lang) : '';

        do_action('fp_cta_bar_clicked', $url, $label, $lang);

        return new WP_REST_Response(null, 204);
    }
}
