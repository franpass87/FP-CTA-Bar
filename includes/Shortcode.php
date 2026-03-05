<?php

namespace FP\CtaBar;

class Shortcode {

    public static function render($atts) {
        ob_start();
        Frontend::render_for_shortcode();
        return ob_get_clean();
    }
}
