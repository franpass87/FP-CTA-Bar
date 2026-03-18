<?php

declare(strict_types=1);

namespace FP\CtaBar;

/**
 * Registrazione blocco Gutenberg per inserire la CTA Bar in un punto della pagina.
 */
final class Block {

    public static function register(): void {
        if (!function_exists('register_block_type')) {
            return;
        }
        register_block_type('fp/cta-bar', [
            'api_version'     => 2,
            'title'           => __('FP CTA Bar', 'fp-cta-bar'),
            'description'     => __('Inserisce la barra CTA configurata in Impostazioni.', 'fp-cta-bar'),
            'category'        => 'widgets',
            'icon'            => 'megaphone',
            'keywords'        => ['cta', 'bar', 'fp'],
            'attributes'      => [],
            'render_callback' => [self::class, 'render'],
        ]);
    }

    /**
     * Render callback del blocco: output della barra CTA (stesso dello shortcode).
     *
     * @param array $attributes Attributi blocco (vuoti)
     * @return string HTML della barra
     */
    public static function render(array $attributes = []): string {
        ob_start();
        Frontend::render_for_shortcode();
        return (string) ob_get_clean();
    }
}
